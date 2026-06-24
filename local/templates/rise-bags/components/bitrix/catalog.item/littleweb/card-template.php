<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array|null $price
 * @var float|int|null $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $displayTitle
 * @var string $detailPageUrl
 * @var string $buttonSizeClass
 * @var string $discountPositionClass
 * @var string $labelPositionClass
 * @var CatalogSectionComponent $component
 */
?>

<div class="product-item">
	<?/* product-item-header  */ ?>
	<? if ($itemHasDetailUrl): ?>
		<a class="product-item-header" href="<?= $detailPageUrl ?>" title="<?= $imgTitle ?>" data-entity="image-wrapper" data-detail-link="Y">
		<? else: ?>
			<div class="product-item-header" data-entity="image-wrapper">
			<? endif; ?>

			<div class="swiper product-item-slider">
				<div class="swiper-wrapper" id="<?= $itemIds['PICT_SLIDER'] ?>">
					<? if ($showSlider): ?>
						<? foreach ($morePhoto as $index => $slide): ?>
							<div class="swiper-slide" <?= ($index === 0 ? ' id="' . $itemIds['PICT'] . '"' : '') ?>>
								<img src="<?= $slide['SRC'] ?>" alt="<?= $item['NAME'] ?>">
							</div>
						<? endforeach; ?>
					<? else: ?>

						<? if (!empty($morePhoto[0]['SRC'])): ?>
							<div class="swiper-slide" id="<?= $itemIds['PICT'] ?>">
								<img src="<?= $item["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $item['NAME'] ?>">
							</div>
						<? endif; ?>
					<? endif; ?>
				</div>
				<div class="swiper-pagination" aria-label="Пагинация"></div>
			</div>

			<!-- метки(хит и тд) и скидка -->
			<? if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($price) && $price['PERCENT'] > 0): ?>
				<span class="product-label product-label--discount" id="<?= $itemIds['DSC_PERC'] ?>">
					<?= -$price['PERCENT'] ?>%
				</span>
			<? endif; ?>
			<? if ($item['LABEL'] && !empty($item['LABEL_ARRAY_VALUE']) || $arParams['SHOW_MAX_QUANTITY'] !== 'N'): ?>
				<div class="product-label-container" id="<?= $itemIds['STICKER_ID'] ?>">
					<? if ($item['LABEL'] && !empty($item['LABEL_ARRAY_VALUE'])): ?>
						<? foreach ($item['LABEL_ARRAY_VALUE'] as $code => $value): ?>
							<span class="product-label product-label--<?= strtolower($code) ?>" title="Новинка">
								<?= $value ?>
							</span>
						<? endforeach; ?>
					<? endif; ?>
				</div>
			<? endif; ?>

			<div class="product-item-sidebar">
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
				<? if ($displayCompare && (!$haveOffers || $arParams['PRODUCT_DISPLAY_MODE'] === 'Y')): ?>
					<label
						id="<?= $itemIds['COMPARE_LINK'] ?>"
						class="compare"
						aria-label="<?= $isCompared ? 'Удалить товар из сравнения' : 'Добавить товар в сравнение' ?>"
						data-compare-toggle
						data-product-id="<?= $compareProductId ?>"
						data-compare-iblock-id="<?= (int)$item['IBLOCK_ID'] ?>"
						data-compare-name="<?= htmlspecialcharsbx($arParams['COMPARE_NAME'] ?: 'CATALOG_COMPARE_LIST') ?>"
						data-compare-add-url="<?= htmlspecialcharsbx($compareAddUrl) ?>"
						data-compare-delete-url="<?= htmlspecialcharsbx($compareDeleteUrl) ?>"
					>
						<input type="checkbox" data-entity="compare-checkbox" <?= $isCompared ? 'checked' : '' ?>>
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

			<? if ($itemHasDetailUrl): ?>
		</a>
	<? else: ?>
</div>
<? endif; ?>

<?/* product-item-view-content  */ ?>

<div class="product-item-body">
	<? if ($itemHasDetailUrl): ?>
		<a class="product-item-title" href="<?= $detailPageUrl ?>" title="<?= $displayTitle ?>" data-entity="name-link" data-detail-link="Y">
		<? endif; ?>
		<span class="product-item-title" title="<?= $displayTitle ?>" data-entity="name"><?= $displayTitle ?></span>
		<? if ($itemHasDetailUrl): ?>
		</a>
	<? endif; ?>

	<? //Вывод доступного количества товара(настраивается в параметрах и в самом товаре)
	?>
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

	<? if (!empty($price) && $actualItem['CAN_BUY']): ?>
		<div class="product-item-price-container" data-entity="price-block">
			<? if ($arParams['SHOW_OLD_PRICE'] === 'Y' && !empty($price)): ?>
				<span class="product-item-price product-item-price--old" id="<?= $itemIds['PRICE_OLD'] ?>"
					<?= ($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] ? 'style="display: none;"' : '') ?>>
					<?= $price['PRINT_RATIO_BASE_PRICE'] ?>
				</span>
			<? endif; ?>
			<span class="product-item-price product-item-price--current" id="<?= $itemIds['PRICE'] ?>">
				<? if (!empty($price)): ?>
					<?
					if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers):
						echo Loc::getMessage(
							'CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
							array(
								'#PRICE#' => $price['PRINT_RATIO_PRICE'],
								'#VALUE#' => $measureRatio,
								'#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
							)
						);
					else:
						echo $price['PRINT_RATIO_PRICE'];
					endif;
					?>
				<? endif; ?>
			</span>
		</div>
	<? endif; ?>

	<? if (!$actualItem["CAN_BUY"]): ?>
		<div class="product-item-subscribe-block">
			<span class="product-item-subscribe-text" id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>">
				<?= $arParams['MESS_NOT_AVAILABLE'] ?>
			</span>

			<? if ($showSubscribe):
				$APPLICATION->IncludeComponent(
					'bitrix:catalog.product.subscribe',
					'littleweb',
					array(
						'PRODUCT_ID' => $item['ID'],
						'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
						'BUTTON_CLASS' => 'product-item-subscribe-btn',
						'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
						'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
					),
					$component,
					array('HIDE_ICONS' => 'N')
				);
			endif; ?>
		</div>
	<? endif; ?>

	<? if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $haveOffers && !empty($item['OFFERS_PROP'])): ?>
		<div class="product-item-sku-props-container" id="<?= $itemIds['PROP_DIV'] ?>">
			<? foreach ($arParams['SKU_PROPS'] as $skuProperty):
				$propertyId = $skuProperty['ID'];
				$skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
				if (!isset($item['SKU_TREE_VALUES'][$propertyId]))
					continue;
			?>
				<div class="sku-prop-block" data-entity="sku-block">
					<div class="sku-prop-container" data-entity="sku-line-block">
						<span class="sku-prop-name"><?= $skuProperty['NAME'] ?>:</span>
						<ul class="sku-prop-list">

							<? foreach ($skuProperty['VALUES'] as $value):
								if (!isset($item['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
									continue;

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
				</div>
			<? endforeach; ?>
		</div>

		<? foreach ($arParams['SKU_PROPS'] as $skuProperty): ?>
			<? if (!isset($item['OFFERS_PROP'][$skuProperty['CODE']]))
				continue;

			$skuProps[] = array(
				'ID' => $skuProperty['ID'],
				'SHOW_MODE' => $skuProperty['SHOW_MODE'],
				'VALUES' => $skuProperty['VALUES'],
				'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
			); ?>
		<? endforeach;
		unset($skuProperty, $value); ?>

		<? if ($item['OFFERS_PROPS_DISPLAY']): ?>
			<? foreach ($item['JS_OFFERS'] as $keyOffer => $jsOffer):
				$strProps = '';

				if (!empty($jsOffer['DISPLAY_PROPERTIES'])):
					foreach ($jsOffer['DISPLAY_PROPERTIES'] as $displayProperty):
						$strProps .= '<div class="prop-list-item"><span class="prop-list-item-name">' . $displayProperty['NAME'] . '</span><span class="prop-list-item-value">'
							. (is_array($displayProperty['VALUE'])
								? implode(' / ', $displayProperty['VALUE'])
								: $displayProperty['VALUE'])
							. '</span></div>';
					endforeach;
				endif;

				$item['JS_OFFERS'][$keyOffer]['DISPLAY_PROPERTIES'] = $strProps; ?>
			<? endforeach;
			unset($jsOffer, $strProps); ?>
		<? endif; ?>
	<? endif; ?>
</div>

<?/* product-item-hover-content */ ?>
<div class="product-item-footer">
	<? if ($actualItem['CAN_BUY']): ?>
		<? if (!$haveOffers): ?>
			<? if (!empty($item['DISPLAY_PROPERTIES'])): ?>
				<div class="product-item-props" data-entity="props-block">
					<small class="product-item-props-title">Характеристики товара:</small>
					<ul class="prop-list">
						<? foreach ($item['DISPLAY_PROPERTIES'] as $code => $displayProperty): ?>
							<li class="prop-list-item">
								<span class="prop-list-item-name"><?= $displayProperty['NAME'] ?></span>
								<span class="prop-list-item-value">
									<?= (is_array($displayProperty['DISPLAY_VALUE'])
										? implode(' / ', $displayProperty['DISPLAY_VALUE'])
										: $displayProperty['DISPLAY_VALUE']) ?>
								</span>
							</li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>

			<? if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !empty($item['PRODUCT_PROPERTIES'])): ?>
				<div id="<?= $itemIds['BASKET_PROP_DIV'] ?>">
					<? if (!empty($item['PRODUCT_PROPERTIES_FILL'])): ?>
						<? foreach ($item['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo): ?>
							<input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propID ?>]"
								value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
						<?
							unset($item['PRODUCT_PROPERTIES'][$propID]);
						endforeach; ?>
					<? endif; ?>

					<?/* if (!empty($item['PRODUCT_PROPERTIES'])): ?>

						<table>
							<? foreach ($item['PRODUCT_PROPERTIES'] as $propID => $propInfo): ?>
								<tr>
									<td><?= $item['PROPERTIES'][$propID]['NAME'] ?></td>
									<td>
										<?
										if (
											$item['PROPERTIES'][$propID]['PROPERTY_TYPE'] === 'L'
											&& $item['PROPERTIES'][$propID]['LIST_TYPE'] === 'C'
										):
											foreach ($propInfo['VALUES'] as $valueID => $value):
										?>
												<label>
													<? $checked = $valueID === $propInfo['SELECTED'] ? 'checked' : ''; ?>
													<input type="radio" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propID ?>]"
														value="<?= $valueID ?>" <?= $checked ?>>
													<?= $value ?>
												</label>
												<br />
											<?
											endforeach;
										else:
											?>
											<select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propID ?>]">
												<?
												foreach ($propInfo['VALUES'] as $valueID => $value):
													$selected = $valueID === $propInfo['SELECTED'] ? 'selected' : '';
												?>
													<option value="<?= $valueID ?>" <?= $selected ?>>
														<?= $value ?>
													</option>
												<?
												endforeach;
												?>
											</select>
										<?
										endif;
										?>
									</td>
								</tr>
							<?
							endforeach;
							?>
						</table>
					<? endif; */ ?>
				</div>
			<? endif; ?>

		<? else: ?>
			<?
			$showProductProps = !empty($item['DISPLAY_PROPERTIES']);
			$showOfferProps = $arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $item['OFFERS_PROPS_DISPLAY'];

			if ($showProductProps || $showOfferProps): ?>
				<div class="product-item-props" data-entity="props-block">
					<small class=" product-item-props-title">Характеристики товара:</small>
					<ul class="prop-list">
						<? if ($showProductProps): ?>
							<? foreach ($item['DISPLAY_PROPERTIES'] as $code => $displayProperty): ?>
								<li class="prop-list-item">
									<span class="prop-list-item-name"><?= $displayProperty['NAME'] ?></span>
									<span class="prop-list-item-value">
										<?= (is_array($displayProperty['DISPLAY_VALUE'])
											? implode(' / ', $displayProperty['DISPLAY_VALUE'])
											: $displayProperty['DISPLAY_VALUE']) ?>
									</span>
								</li>
							<? endforeach; ?>
						<? endif; ?>
					</ul>
					<? if ($showOfferProps && $item['JS_OFFERS']): ?>
						<div class="prop-list prop-list--sku" id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>"></div>
					<? endif; ?>
				</div>
			<? endif; ?>
		<? endif; ?>
	<? endif; ?>

	<? $showQuantityBlock = (
		!$haveOffers && $actualItem['CAN_BUY'] && $arParams['USE_PRODUCT_QUANTITY'] && $arParams['PRODUCT_DISPLAY_MODE'] === 'Y'
	) || (
		$haveOffers && $arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $arParams['USE_PRODUCT_QUANTITY']
	);

	if ($showQuantityBlock):
	?>
		<div class="counter-block">
			<div class="counter counter--sm" data-entity="quantity-block">
				<button type="button" class="counter-btn counter-btn--dec" id="<?= $itemIds['QUANTITY_DOWN'] ?>">
					<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#icon-minus"></use>
					</svg>
				</button>
				<input type="number" value="1" disabled="disabled" data-value="1" id="<?= $itemIds['QUANTITY'] ?>" type="number"
					name="<?= $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>"
					value="<?= $measureRatio ?>">
				<button type="button" class="counter-btn counter-btn--inc" id="<?= $itemIds['QUANTITY_UP'] ?>">
					<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#icon-plus"></use>
					</svg>
				</button>
			</div>

			<span class="product-item-amount-description-container">
				<small id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
					<?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
				</small>
				<small id="<?= $itemIds['PRICE_TOTAL'] ?>"></small>
			</span>

		</div>
	<? endif; ?>


	<? if (!$haveOffers): ?>
		<? if ($actualItem['CAN_BUY']): ?>
			<? // расширенный режим показа карточки товара
			if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y'):
			?>
				<div class="product-item-button-container" id="<?= $itemIds['BASKET_ACTIONS'] ?>">
					<button type="button" class="main-btn outlined" id="<?= $itemIds['BUY_LINK'] ?>">
						<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>
					</button>
					<button type="button" class="main-btn" data-1clickbuy-id="<?= $item["ID"] ?>">
						<span>Купить в 1 клик</span>
					</button>
				</div>
			<? else: ?>
				<div class="product-item-button-container">
					<a class="main-btn outlined" href="<?= $detailPageUrl ?>" data-detail-link="Y">
						<?= $arParams['MESS_BTN_DETAIL'] ?>
					</a>
					<button type="button" class="main-btn" data-1clickbuy-id="<?= $item["ID"] ?>">
						<span>Купить в 1 клик</span>
					</button>
				</div>
			<? endif; ?>

		<? endif; ?>
	<? else: ?>
		<? // расширенный режим показа карточки товара
		if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y'):
		?>
			<div class="product-item-button-container" <? if ($actualItem['CAN_BUY']): ?> id="<?= $itemIds['BASKET_ACTIONS'] ?>" <? endif; ?>>
				<? if ($actualItem['CAN_BUY']): ?>
					<button type="button" class="main-btn outlined" id="<?= $itemIds['BUY_LINK'] ?>">
						<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>
					</button>

					<button type="button" class="main-btn" data-1clickbuy-id="<?= $item["ID"] ?>">
						<span>Купить в 1 клик</span>
					</button>
				<? endif; ?>
			</div>
		<? else: ?>
			<div class="product-item-button-container">
				<a class="main-btn outlined" href="<?= $detailPageUrl ?>" data-detail-link="Y">
					<?= $arParams['MESS_BTN_DETAIL'] ?>
				</a>
				<button type="button" class="main-btn" data-1clickbuy-id="<?= $item["ID"] ?>">
					<span>Купить в 1 клик</span>
				</button>
			</div>
		<? endif; ?>
	<? endif; ?>
</div>
</div>
