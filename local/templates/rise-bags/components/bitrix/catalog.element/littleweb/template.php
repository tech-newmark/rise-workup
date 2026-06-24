<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

$templateLibrary = array('popup', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$haveOffers = !empty($arResult['OFFERS']);

$templateData = [
	// 'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'ITEM' => [
		'ID' => $arResult['ID'],
		'IBLOCK_ID' => $arResult['IBLOCK_ID'],
	],
];
if ($haveOffers) {
	$templateData['ITEM']['OFFERS_SELECTED'] = $arResult['OFFERS_SELECTED'];
	$templateData['ITEM']['JS_OFFERS'] = $arResult['JS_OFFERS'];
}
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
	'ID' => $mainId,
	'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
	'STICKER_ID' => $mainId . '_sticker',
	'BIG_SLIDER_ID' => $mainId . '_big_slider',
	'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
	'PICT' => $mainId . '_pict',
	'PICT_SLIDER' => $mainId . '_pict_slider',
	'SLIDER_CONT_ID' => $mainId . '_slider_cont',
	'OLD_PRICE_ID' => $mainId . '_old_price',
	'PRICE_ID' => $mainId . '_price',
	'DESCRIPTION_ID' => $mainId . '_description',
	'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
	'PRICE_TOTAL' => $mainId . '_price_total',
	'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
	'QUANTITY_ID' => $mainId . '_quantity',
	'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
	'QUANTITY_UP_ID' => $mainId . '_quant_up',
	'QUANTITY_MEASURE' => $mainId . '_quant_measure',
	'QUANTITY_LIMIT' => $mainId . '_quant_limit',
	'BUY_LINK' => $mainId . '_buy_link',
	'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
	'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
	'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
	'COMPARE_LINK' => $mainId . '_compare_link',
	'TREE_ID' => $mainId . '_skudiv',
	'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
	'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
	'OFFER_GROUP' => $mainId . '_set_group_',
	'BASKET_PROP_DIV' => $mainId . '_basket_prop',
	'SUBSCRIBE_LINK' => $mainId . '_subscribe',
	'TABS_ID' => $mainId . '_tabs',
	'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
	'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
	'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

if ($haveOffers) {
	$actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
	$showSliderControls = false;

	foreach ($arResult['OFFERS'] as $offer) {
		if ($offer['MORE_PHOTO_COUNT'] > 1) {
			$showSliderControls = true;
			break;
		}
	}
} else {
	$actualItem = $arResult;
	$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$displayName = $haveOffers && !empty($actualItem['NAME'])
	? $actualItem['NAME']
	: $name;

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;
$favoriteProductId = (int)$actualItem['ID'];
$isFavorite = function_exists('riseBagsIsFavoriteProduct') && riseBagsIsFavoriteProduct($favoriteProductId);

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y') {
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer) {
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '') {
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
} else {
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE) {
	$arParams['~MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE');

	$arParams['MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE');
} else {
	$arParams['~MESS_NOT_AVAILABLE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');

	$arParams['MESS_NOT_AVAILABLE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

?>

<div class="bx-catalog-element" id="<?= $itemIds['ID'] ?>" itemscope itemtype="http://schema.org/Product">
	<div class="grid">
		<div class="grid-item grid-item--gallery">

			<!-- discount -->
			<? if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y'): ?>
				<? if ($haveOffers): ?>
					<span class="product-label product-label--discount" id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>" style="display: none;">
					</span>
				<? else: ?>
					<? if ($price['DISCOUNT'] > 0): ?>
						<span class="product-item-label-ring" id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>" title="<?= -$price['PERCENT'] ?>%">
							<span><?= -$price['PERCENT'] ?>%</span>
						</span>
					<? endif; ?>
				<? endif; ?>
			<? endif; ?>
			<!-- discount -->

			<!-- sidebar -->
			<div class="bx-catalog-element__sidebar">
				<button
					class="favourite-add-btn<?= $isFavorite ? ' active' : '' ?>"
					type="button"
					aria-label="<?= $isFavorite ? 'Удалить товар из избранного' : 'Добавить товар в избранное' ?>"
					aria-pressed="<?= $isFavorite ? 'true' : 'false' ?>"
					data-favorite-toggle
					data-product-id="<?= $favoriteProductId ?>"
				>
					<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
						<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-heart'></use>
					</svg>
				</button>
				<? if (!empty($price) && $actualItem['CAN_BUY'] && $arParams['DISPLAY_COMPARE']): ?>
					<label class="compare" id="<?= $itemIds['COMPARE_LINK'] ?>">
						<input type="checkbox" data-entity="compare-checkbox">
						<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
							<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-compare'></use>
						</svg>
					</label>
				<? endif; ?>
				<button class="fast-view-btn" type="button" aria-label="Быстрый просмотр">
					<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
						<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-info'></use>
					</svg>
				</button>
				<button class="oneclickbuy-btn" type="button" aria-label="Информация о доставке">
					<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
						<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-cube'></use>
					</svg>
				</button>
			</div>
			<!-- sidebar -->

			<!-- labels -->
			<? if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE'])): ?>
				<div class="product-label-container" id="<?= $itemIds['STICKER_ID'] ?>">
					<? foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value): ?>
						<span class="product-label product-label--<?= strtolower($code) ?>" title="<?= $value ?>"><?= $value ?></span>
					<? endforeach; ?>
				</div>
			<? endif; ?>
			<!-- labels -->

			<!-- slider -->
			<div class="bx-catalog-element-slider-container" id="<?= $itemIds['BIG_SLIDER_ID'] ?>">
				<div class="swiper bx-catalog-element-slider" data-entity="images-slider-block">
					<div class="swiper-wrapper" id="<?= $itemIds['PICT_SLIDER'] ?>" data-entity="images-container">
						<? if (!empty($actualItem['MORE_PHOTO'])): ?>
							<? foreach ($actualItem['MORE_PHOTO'] as $index => $slide): ?>
								<div class="swiper-slide<?= ($index === 0 ? ' active' : '') ?>" <?= ($index === 0 ? ' id="' . $itemIds['PICT'] . '"' : '') ?> data-entity="image" data-id="<?= $slide['ID'] ?>">
									<img data-fancybox="bx-catalog-element-gallery" src="<?= $slide['SRC'] ?>" alt="<?= $alt ?>" title="<?= $title ?>" <?= ($index === 0 ? ' itemprop="image"' : '') ?>>
								</div>
							<? endforeach; ?>
						<? endif; ?>
					</div>
					<div class="swiper-pagination" aria-label="Пагинация"></div>
				</div>

				<?/*НАБОРЫ / НЕ ВЕРСТАЛИСЬ*/ ?>
				<? if ($haveOffers): ?>
					<? debug($arResult['OFFER_GROUP']) ?>
					<? if ($arResult['OFFER_GROUP']): ?>
						<? foreach ($arResult['OFFER_GROUP_VALUES'] as $offerId): ?>
							<span id="<?= $itemIds['OFFER_GROUP'] . $offerId ?>" style="display: none;">
								<?php
								$APPLICATION->IncludeComponent(
									'bitrix:catalog.set.constructor',
									'.default',
									array(
										'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
										'IBLOCK_ID' => $arResult['OFFERS_IBLOCK'],
										'ELEMENT_ID' => $offerId,
										'PRICE_CODE' => $arParams['PRICE_CODE'],
										'BASKET_URL' => $arParams['BASKET_URL'],
										'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
										'CACHE_TYPE' => $arParams['CACHE_TYPE'],
										'CACHE_TIME' => $arParams['CACHE_TIME'],
										'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
										// 'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
										'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
										'CURRENCY_ID' => $arParams['CURRENCY_ID']
									),
									$component,
									array('HIDE_ICONS' => 'Y')
								);
								?>
							</span>
						<? endforeach ?>
					<? endif; ?>
				<? else: ?>
					<? if ($arResult['MODULES']['catalog'] && $arResult['OFFER_GROUP']):
						$APPLICATION->IncludeComponent(
							'bitrix:catalog.set.constructor',
							'.default',
							array(
								'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
								'IBLOCK_ID' => $arParams['IBLOCK_ID'],
								'ELEMENT_ID' => $arResult['ID'],
								'PRICE_CODE' => $arParams['PRICE_CODE'],
								'BASKET_URL' => $arParams['BASKET_URL'],
								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME'],
								'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
								'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
								'CURRENCY_ID' => $arParams['CURRENCY_ID']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
					endif; ?>
				<? endif; ?>
				<?/*НАБОРЫ / НЕ ВЕРСТАЛИСЬ*/ ?>

			</div>
			<!-- slider -->
		</div>

		<div class="grid-item grid-item--main">
			<? if ($arParams['DISPLAY_NAME'] === 'Y'): ?>

				<h1 class="title" data-entity="name"><?= $displayName ?></h1>
			<? endif; ?>

			<div class=" bx-catalog-element-buy-info">

				<!-- RATING -->
				<? if ($arParams['USE_VOTE_RATING'] === 'Y'): ?>
					<div class="bx-catalog-element-vote-container">
						<?
						$APPLICATION->IncludeComponent(
							'bitrix:iblock.vote',
							'stars',
							array(
								'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
								'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
								'IBLOCK_ID' => $arParams['IBLOCK_ID'],
								'ELEMENT_ID' => $arResult['ID'],
								'ELEMENT_CODE' => '',
								'MAX_VOTE' => '5',
								'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
								'SET_STATUS_404' => 'N',
								'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
								'CACHE_TYPE' => $arParams['CACHE_TYPE'],
								'CACHE_TIME' => $arParams['CACHE_TIME']
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
				<? endif; ?>

				<!-- quantity limit -->

				<? if ($arParams['SHOW_MAX_QUANTITY'] !== 'N'): ?>
					<? if ($haveOffers): ?>
						<? if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y'): ?>
							<span class="product-label product-label--quantity" id="<?= $itemIds['QUANTITY_LIMIT'] ?>" data-entity="quantity-limit-block">
								<?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>:&nbsp;<span class="product-item-quantity" data-entity="quantity-limit-value"></span>
							</span>
						<? endif; ?>
					<? else : ?>
						<? if (
							$measureRatio
							&& (float)$actualItem['CATALOG_QUANTITY'] > 0
							&& $actualItem['CATALOG_QUANTITY_TRACE'] === 'Y'
							&& $actualItem['CATALOG_CAN_BUY_ZERO'] === 'N'
						):
						?>
							<span class="product-label product-label--quantity" id="<?= $itemIds['QUANTITY_LIMIT'] ?>">
								<?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>:&nbsp;<span class="product-item-quantity">
									<?
									if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
										if ((float)$actualItem['CATALOG_QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']) {
											echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
										} else {
											echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
										}
									} else {
										echo $actualItem['CATALOG_QUANTITY'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'];
									}
									?>
								</span>
							</span>
						<? endif; ?>
					<? endif; ?>
				<? endif; ?>
				<? if ($showSubscribe): ?>
					<div class="product-item-subscribe-block">
						<span class="product-item-subscribe-text" id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>"><?= $arParams['MESS_NOT_AVAILABLE'] ?></span>
						<?php
						$APPLICATION->IncludeComponent(
							'bitrix:catalog.product.subscribe',
							'littleweb',
							array(
								'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
								'PRODUCT_ID' => $arResult['ID'],
								'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
								'BUTTON_CLASS' => 'product-item-subscribe-btn',
								'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
								'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
				<? endif; ?>
				<!-- quantity limit -->

				<!-- SKU -->
				<? if ($haveOffers && !empty($arResult['OFFERS_PROP'])): ?>
					<div class="sku-prop-block" id="<?= $itemIds['TREE_ID'] ?>">
						<? foreach ($arResult['SKU_PROPS'] as $skuProperty):
							if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
								continue;

							$propertyId = $skuProperty['ID'];
							$skuProps[] = array(
								'ID' => $propertyId,
								'SHOW_MODE' => $skuProperty['SHOW_MODE'],
								'VALUES' => $skuProperty['VALUES'],
								'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
							);
						?>

							<div class="sku-prop-container" data-entity="sku-line-block">
								<span class="sku-prop-name"><?= $skuProperty['NAME'] ?>:</span>
								<ul class="sku-prop-list">

									<? foreach ($skuProperty['VALUES'] as &$value):


										$value['NAME'] = htmlspecialcharsbx($value['NAME']);

										if ($skuProperty['SHOW_MODE'] === 'PICT'):
									?>
											<li class="sku-prop-list-item" title="<?= $value['NAME'] ?>" data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>" data-onevalue="<?= $value['ID'] ?>">
												<button type="button" class="sku-prop-list-item-value">
													<img src="<?= $value['PICT']['SRC'] ?>" alt="<?= $value['NAME'] ?>" width="40" height="40">
												</button>
											</li>
										<? else: ?>
											<li class="sku-prop-list-item" title="<?= $value['NAME'] ?>" data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>" data-onevalue="<?= $value['ID'] ?>">
												<button type="button" class="sku-prop-list-item-value">
													<span><?= $value['NAME'] ?></span>
												</button>
											</li>
										<? endif; ?>
									<? endforeach; ?>
								</ul>
							</div>

						<? endforeach; ?>
					</div>
				<? endif; ?>

				<!-- PRICE -->
				<div class="bx-catalog-element-price-container">
					<p class="bx-catalog-element-price-value">
						<span class="$itemIds['PRICE_ID'] bx-catalog-element-price--current heading--lg" id="<?= $itemIds['PRICE_ID'] ?>">
							<?= $price['PRINT_RATIO_PRICE'] ?>
						</span>

						<? if ($arParams['SHOW_OLD_PRICE'] === 'Y' && $showDiscount): ?>
							<span class="bx-catalog-element-price bx-catalog-element-price--old heading--sm" id="<?= $itemIds['OLD_PRICE_ID'] ?>">
								<?= ($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '') ?>
							</span>
						<? endif; ?>
					</p>
					<? if ($arParams['SHOW_OLD_PRICE'] === 'Y' && $showDiscount): ?>
						<p class="bx-catalog-element-price-label" id="<?= $itemIds['DISCOUNT_PRICE_ID'] ?>">
							<?= Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT'])); ?>
						</p>
					<? endif; ?>
				</div>
				<!-- PRICE RANGES -->

				<? if ($arParams['USE_PRICE_COUNT']):
					$showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
					$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
				?>
					<? if ($showRanges): ?>
						<div class="product-item-detail-info-container" data-entity="price-ranges-block">
							<div class="product-item-detail-info-container-title">
								<?= $arParams['MESS_PRICE_RANGES_TITLE'] ?>
								<span data-entity="price-ranges-ratio-header">
									(<?= (Loc::getMessage(
											'CT_BCE_CATALOG_RATIO_PRICE',
											array('#RATIO#' => ($useRatio ? $measureRatio : '1') . ' ' . $actualItem['ITEM_MEASURE']['TITLE'])
										)) ?>)
								</span>
							</div>
							<dl class="product-item-detail-properties" data-entity="price-ranges-body">

								<? foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range): ?>
									<? if ($range['HASH'] !== 'ZERO-INF'):
										$itemPrice = false;

										foreach ($arResult['ITEM_PRICES'] as $itemPrice) {
											if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
												break;
											}
										}

										if ($itemPrice):
									?>
											<dt>
												<?= Loc::getMessage(
													'CT_BCE_CATALOG_RANGE_FROM',
													array('#FROM#' => $range['SORT_FROM'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'])
												) . ' ';

												if (is_infinite($range['SORT_TO'])) {
													echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
												} else {
													echo Loc::getMessage(
														'CT_BCE_CATALOG_RANGE_TO',
														array('#TO#' => $range['SORT_TO'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'])
													);
												}
												?>
											</dt>
											<dd><?= ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) ?></dd>
										<? endif; ?>
									<? endif; ?>
								<? endforeach; ?>

							</dl>
						</div>
					<? endif; ?>
				<? endif;
				unset($showRanges, $useRatio, $itemPrice, $range);
				?>


				<!-- PROPS -->
				<? /*if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']): ?>
					<div class="product-item-props" data-entity="props-block">
						<? if ($arResult['SHOW_OFFERS_PROPS']): ?>
							<ul class="prop-list" id="<?= $itemIds['DISPLAY_MAIN_PROP_DIV'] ?>"></ul>
						<? endif; ?>
					</div>
				<? endif; */ ?>

				<? if ($arParams['USE_PRODUCT_QUANTITY'] && $actualItem['CAN_BUY']): ?>
					<div class="counter-block" data-entity="quantity-block">
						<div class="counter counter--sm">
							<button type="button" class="counter-btn counter-btn--dec" id="<?= $itemIds['QUANTITY_DOWN_ID'] ?>">
								<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
									<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#icon-minus"></use>
								</svg>
							</button>
							<input type="number" value="1" disabled="disabled" data-value="1" id="<?= $itemIds['QUANTITY_ID'] ?>" value="<?= $price['MIN_QUANTITY'] ?>">
							<button type="button" class="counter-btn counter-btn--inc" id="<?= $itemIds['QUANTITY_UP_ID'] ?>">
								<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
									<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#icon-plus"></use>
								</svg>
							</button>
						</div>

						<span class="bx-catalog-element-amount-description-container">
							<small id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
								<?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
							</small>
							<small id="<?= $itemIds['PRICE_TOTAL'] ?>"></small>
						</span>
					</div>
				<? endif; ?>

				<div class="bx-catalog-element-buttons-container" id="<?= $itemIds['BASKET_ACTIONS_ID'] ?>" data-entity="main-button-container">
					<div style="display: <?= ($actualItem['CAN_BUY'] ? '' : 'none') ?>;">
						<div class="bx-catalog-element-buttons">
							<? if ($showAddBtn): ?>
								<button class="main-btn outlined" id="<?= $itemIds['ADD_BASKET_LINK'] ?>">
									<span><?= $arParams['MESS_BTN_ADD_TO_BASKET'] ?></span>
								</button>
							<? endif; ?>

							<? if ($showBuyBtn): ?>
								<button class="main-btn outlined" id="<?= $itemIds['BUY_LINK'] ?>">
									<span><?= $arParams['MESS_BTN_BUY'] ?></span>
								</button>
							<? endif; ?>

							<button type="button" class="main-btn" data-1clickbuy-id="317">
								<span>Купить в 1 клик</span>
							</button>
						</div>
					</div>
				</div>

				<?/*
				<? if ($actualItem['CAN_BUY']): ?>
					<div class="bx-catalog-element-buttons-container" id="<?= $itemIds['BASKET_ACTIONS_ID'] ?>">
						<div class="bx-catalog-element-buttons">
							<? if ($showAddBtn): ?>
								<button class="main-btn outlined" id="<?= $itemIds['ADD_BASKET_LINK'] ?>">
									<span><?= $arParams['MESS_BTN_ADD_TO_BASKET'] ?></span>
								</button>
							<? endif; ?>

							<? if ($showBuyBtn): ?>
								<button class="main-btn outlined" id="<?= $itemIds['BUY_LINK'] ?>">
									<span><?= $arParams['MESS_BTN_BUY'] ?></span>
								</button>
							<? endif; ?>
							<!-- !!! подключить текущее ТП по ID !!! -->
							<button type="button" class="main-btn" data-1clickbuy-id="317">
								<span>Купить в 1 клик</span>
							</button>
						</div>
					</div>
				<? else: ?>
					<div class="product-item-subscribe-block">
						<span class="product-item-subscribe-text" id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>"><?= $arParams['MESS_NOT_AVAILABLE'] ?></span>

						<? if ($showSubscribe): ?>
							<? $APPLICATION->IncludeComponent(
								'bitrix:catalog.product.subscribe',
								'littleweb',
								array(
									'PRODUCT_ID' => $arResult['ID'],
									'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
									'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
									'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
									'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
								),
								$component,
								array('HIDE_ICONS' => 'Y')
							);
							?>
						<? endif; ?>
					</div>
				<? endif; ?>
				*/ ?>
			</div>

			<div class="bx-catalog-element-description">
				<? if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']): ?>
					<div class="bx-catalog-element-description-section ">
						<span class="bx-catalog-element-description-section-title"><?= $arParams['MESS_PROPERTIES_TAB'] ?></span>

						<? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
							<ul class="prop-list">
								<? foreach ($arResult['DISPLAY_PROPERTIES'] as $property): ?>
									<li class="prop-list-item">
										<span class="prop-list-item-name"><?= $property['NAME'] ?></span>
										<span class="prop-list-item-value">
											<?= (
												is_array($property['DISPLAY_VALUE'])
												? implode(' / ', $property['DISPLAY_VALUE'])
												: $property['DISPLAY_VALUE']
											) ?>
										</span>
									</li>
								<?
								endforeach;
								unset($property);
								?>
							</ul>
						<? endif; ?>

						<? if ($arResult['SHOW_OFFERS_PROPS']): ?>
							<ul class="prop-list" id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>"></ul>
						<? endif; ?>
					</div>
				<? endif ?>

				<? if ($showDescription): ?>
					<div class="bx-catalog-element-description-section">
						<span class="bx-catalog-element-description-section-title"><?= $arParams['MESS_DESCRIPTION_TAB'] ?></span>
						<div class="content" itemprop="description" id="<?= $itemIds['DESCRIPTION_ID'] ?>">
							<? if (
								$arResult['PREVIEW_TEXT'] != ''
								&& (
									$arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
									|| ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
								)
							): ?>
								<?= $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>' . $arResult['PREVIEW_TEXT'] . '</p>'; ?>
							<? endif; ?>

							<? if ($arResult['DETAIL_TEXT'] != ''): ?>
								<?= $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>' . $arResult['DETAIL_TEXT'] . '</p>'; ?>
							<? endif; ?>
						</div>
					</div>
				<? endif; ?>
			</div>
		</div>
	</div>

	<meta itemprop="name" content="<?= $name ?>" />
	<meta itemprop="category" content="<?= $arResult['CATEGORY_PATH'] ?>" />

	<? if ($haveOffers):
		foreach ($arResult['JS_OFFERS'] as $offer):
			$currentOffersList = array();

			if (!empty($offer['TREE']) && is_array($offer['TREE'])) {
				foreach ($offer['TREE'] as $propName => $skuId) {
					$propId = (int)mb_substr($propName, 5);

					foreach ($skuProps as $prop) {
						if ($prop['ID'] == $propId) {
							foreach ($prop['VALUES'] as $propId => $propValue) {
								if ($propId == $skuId) {
									$currentOffersList[] = $propValue['NAME'];
									break;
								}
							}
						}
					}
				}
			}

			$offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
	?>
			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="sku" content="<?= htmlspecialcharsbx(implode('/', $currentOffersList)) ?>" />
				<meta itemprop="price" content="<?= $offerPrice['RATIO_PRICE'] ?>" />
				<meta itemprop="priceCurrency" content="<?= $offerPrice['CURRENCY'] ?>" />
				<link itemprop="availability" href="http://schema.org/<?= ($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>" />
			</span>
		<? endforeach;

		unset($offerPrice, $currentOffersList);
	else:
		?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price" content="<?= $price['RATIO_PRICE'] ?>" />
			<meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>" />
			<link itemprop="availability" href="http://schema.org/<?= ($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>" />
		</span>
	<? endif; ?>
</div>

<? if ($haveOffers):
	$offerIds = array();
	$offerCodes = array();

	$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

	foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer) {
		$offerIds[] = (int)$jsOffer['ID'];
		$offerCodes[] = $jsOffer['CODE'];

		$fullOffer = $arResult['OFFERS'][$ind];
		$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

		$strAllProps = '';
		$strMainProps = '';
		$strPriceRangesRatio = '';
		$strPriceRanges = '';

		if ($arResult['SHOW_OFFERS_PROPS']) {
			if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
				foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property) {
					$current = '<li class="prop-list-item"><span class="prop-list-item-name">' . $property['NAME'] . '</span><span class="prop-list-item-value">' . (
						is_array($property['VALUE'])
						? implode(' / ', $property['VALUE'])
						: $property['VALUE']
					) . '</span></li>';
					$strAllProps .= $current;

					if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']])) {
						$strMainProps .= $current;
					}
				}

				unset($current);
			}
		}

		if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1) {
			$strPriceRangesRatio = '(' . Loc::getMessage(
				'CT_BCE_CATALOG_RATIO_PRICE',
				array('#RATIO#' => ($useRatio
					? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
					: '1'
				) . ' ' . $measureName)
			) . ')';

			foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range) {
				if ($range['HASH'] !== 'ZERO-INF') {
					$itemPrice = false;

					foreach ($jsOffer['ITEM_PRICES'] as $itemPrice) {
						if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
							break;
						}
					}

					if ($itemPrice) {
						$strPriceRanges .= '<dt>' . Loc::getMessage(
							'CT_BCE_CATALOG_RANGE_FROM',
							array('#FROM#' => $range['SORT_FROM'] . ' ' . $measureName)
						) . ' ';

						if (is_infinite($range['SORT_TO'])) {
							$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
						} else {
							$strPriceRanges .= Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_TO',
								array('#TO#' => $range['SORT_TO'] . ' ' . $measureName)
							);
						}

						$strPriceRanges .= '</dt><dd>' . ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) . '</dd>';
					}
				}
			}

			unset($range, $itemPrice);
		}

		$jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
		$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
		$jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
		$jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
	}

	$templateData['OFFER_IDS'] = $offerIds;
	$templateData['OFFER_CODES'] = $offerCodes;
	unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => true,
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			// 'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null,
			'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
			'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
		),
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'VISUAL' => $itemIds,
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'NAME' => $arResult['~NAME'],
			'CATEGORY' => $arResult['CATEGORY_PATH'],
			'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
			'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
			'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
			'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
		),
		'BASKET' => array(
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL' => $arParams['BASKET_URL'],
			'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		),
		'OFFERS' => $arResult['JS_OFFERS'],
		'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS' => $skuProps
	);
else:
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties):
?>
		<div id="<?= $itemIds['BASKET_PROP_DIV'] ?>" style="display: none;">
			<?
			if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])):
				foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo):
			?>
					<input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]" value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
				<?
					unset($arResult['PRODUCT_PROPERTIES'][$propId]);
				endforeach;
			endif;

			$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
			if (!$emptyProductProperties): ?>
				<table>
					<? foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo): ?>
						<tr>
							<td><?= $arResult['PROPERTIES'][$propId]['NAME'] ?></td>
							<td>
								<? if (
									$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
									&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
								):
									foreach ($propInfo['VALUES'] as $valueId => $value):
								?>
										<label>
											<input type="radio" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
												value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? 'checked' : '') ?>>
											<?= $value ?>
										</label>
										<br>
									<? endforeach;
								else:
									?>
									<select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]">
										<? foreach ($propInfo['VALUES'] as $valueId => $value): ?>
											<option value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? 'selected' : '') ?>>
												<?= $value ?>
											</option>
										<? endforeach; ?>
									</select>
								<? endif; ?>
							</td>
						</tr>
					<? endforeach ?>
				</table>
			<? endif; ?>
		</div>
<? endif;

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			// 'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null
		),
		'VISUAL' => $itemIds,
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'PICT' => reset($arResult['MORE_PHOTO']),
			'NAME' => $arResult['~NAME'],
			'SUBSCRIPTION' => true,
			'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
			'ITEM_PRICES' => $arResult['ITEM_PRICES'],
			'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
			'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
			'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
			'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
			'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
			'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER' => $arResult['MORE_PHOTO'],
			'CAN_BUY' => $arResult['CAN_BUY'],
			'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
			'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
			'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
			'CATEGORY' => $arResult['CATEGORY_PATH']
		),
		'BASKET' => array(
			'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS' => $emptyProductProperties,
			'BASKET_URL' => $arParams['BASKET_URL'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		)
	);
	unset($emptyProductProperties);
endif;

if ($arParams['DISPLAY_COMPARE']) {
	$jsParams['COMPARE'] = array(
		'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
		'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
		'COMPARE_PATH' => $arParams['COMPARE_PATH']
	);
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
	$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"];
?>
<script>
	BX.message({
		ECONOMY_INFO_MESSAGE: '<?= GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2') ?>',
		TITLE_ERROR: '<?= GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
		TITLE_BASKET_PROPS: '<?= GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
		BASKET_UNKNOWN_ERROR: '<?= GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		BTN_SEND_PROPS: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS') ?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
		BTN_MESSAGE_CLOSE: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP') ?>',
		TITLE_SUCCESSFUL: '<?= GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK') ?>',
		COMPARE_MESSAGE_OK: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK') ?>',
		COMPARE_UNKNOWN_ERROR: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
		COMPARE_TITLE: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE') ?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?= GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
		PRODUCT_GIFT_LABEL: '<?= GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL') ?>',
		PRICE_TOTAL_PREFIX: '<?= GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX') ?>',
		RELATIVE_QUANTITY_MANY: '<?= CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY']) ?>',
		RELATIVE_QUANTITY_FEW: '<?= CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW']) ?>',
		SITE_ID: '<?= CUtil::JSEscape($component->getSiteId()) ?>'
	});

	var <?= $obName ?> = new JCCatalogElement(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
</script>
<?php
unset($actualItem, $itemIds, $jsParams);
