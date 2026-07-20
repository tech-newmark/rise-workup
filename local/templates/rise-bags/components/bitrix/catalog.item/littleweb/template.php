<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogProductsViewedComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

if (isset($arResult['ITEM'])) {
	$item = $arResult['ITEM'];
	$areaId = $arResult['AREA_ID'];
	$itemIds = array(
		'ID' => $areaId,
		'PICT' => $areaId . '_pict',
		// 'SECOND_PICT' => $areaId . '_secondpict',
		'PICT_SLIDER' => $areaId . '_pict_slider',
		'STICKER_ID' => $areaId . '_sticker',
		'SECOND_STICKER_ID' => $areaId . '_secondsticker',
		'QUANTITY' => $areaId . '_quantity',
		'QUANTITY_DOWN' => $areaId . '_quant_down',
		'QUANTITY_UP' => $areaId . '_quant_up',
		'QUANTITY_MEASURE' => $areaId . '_quant_measure',
		'QUANTITY_LIMIT' => $areaId . '_quant_limit',
		'BUY_LINK' => $areaId . '_buy_link',
		'BASKET_ACTIONS' => $areaId . '_basket_actions',
		'NOT_AVAILABLE_MESS' => $areaId . '_not_avail',
		'SUBSCRIBE_LINK' => $areaId . '_subscribe',
		'COMPARE_LINK' => $areaId . '_compare_link',
		'PRICE' => $areaId . '_price',
		'PRICE_OLD' => $areaId . '_price_old',
		'PRICE_TOTAL' => $areaId . '_price_total',
		'DSC_PERC' => $areaId . '_dsc_perc',
		'SECOND_DSC_PERC' => $areaId . '_second_dsc_perc',
		'PROP_DIV' => $areaId . '_sku_tree',
		'PROP' => $areaId . '_prop_',
		'DISPLAY_PROP_DIV' => $areaId . '_sku_prop',
		'BASKET_PROP_DIV' => $areaId . '_basket_prop',
	);
	$obName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);
	$isBig = isset($arResult['BIG']) && $arResult['BIG'] === 'Y';

	$productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
		? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
		: $item['NAME'];

	$imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
		? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
		: $item['NAME'];

	$skuProps = array();

	$haveOffers = !empty($item['OFFERS']);
	if ($haveOffers) {
		$sortOffers = static function (array $offers, array $jsOffers, array $params): array {
			$getSortValue = static function (array $offer, string $field) {
				$field = trim($field);

				if ($field === '') {
					return null;
				}

				$upperField = mb_strtoupper($field);
				if (array_key_exists($upperField, $offer)) {
					return $offer[$upperField];
				}

				if (array_key_exists($field, $offer)) {
					return $offer[$field];
				}

				if (mb_substr($upperField, 0, 9) === 'PROPERTY_') {
					$propertyCode = mb_substr($upperField, 9);
					$property = $offer['PROPERTIES'][$propertyCode]
						?? $offer['DISPLAY_PROPERTIES'][$propertyCode]
						?? null;

					if (is_array($property)) {
						return $property['VALUE']
							?? $property['DISPLAY_VALUE']
							?? $property['VALUE_ENUM']
							?? $property['SORT']
							?? null;
					}
				}

				return null;
			};

			$normalizeValue = static function ($value) {
				if (is_array($value)) {
					$value = reset($value);
				}

				return $value;
			};

			$compareByField = static function (array $left, array $right, string $field, string $order) use ($getSortValue, $normalizeValue): int {
				$leftValue = $normalizeValue($getSortValue($left, $field));
				$rightValue = $normalizeValue($getSortValue($right, $field));

				if ($leftValue === $rightValue) {
					return 0;
				}

				if ($leftValue === null || $leftValue === '') {
					return 1;
				}

				if ($rightValue === null || $rightValue === '') {
					return -1;
				}

				if (is_numeric($leftValue) && is_numeric($rightValue)) {
					$result = (float)$leftValue <=> (float)$rightValue;
				} else {
					$result = strnatcasecmp((string)$leftValue, (string)$rightValue);
				}

				return mb_strtoupper($order) === 'DESC' ? -$result : $result;
			};

			$sortRules = [
				[
					(string)($params['OFFERS_SORT_FIELD'] ?? 'SORT'),
					(string)($params['OFFERS_SORT_ORDER'] ?? 'ASC'),
				],
				[
					(string)($params['OFFERS_SORT_FIELD2'] ?? 'ID'),
					(string)($params['OFFERS_SORT_ORDER2'] ?? 'DESC'),
				],
			];

			usort($offers, static function (array $left, array $right) use ($sortRules, $compareByField): int {
				foreach ($sortRules as $rule) {
					$result = $compareByField($left, $right, $rule[0], $rule[1]);

					if ($result !== 0) {
						return $result;
					}
				}

				return 0;
			});

			if (!empty($jsOffers)) {
				$jsOffersById = [];

				foreach ($jsOffers as $jsOffer) {
					$jsOffersById[(int)$jsOffer['ID']] = $jsOffer;
				}

				$sortedJsOffers = [];

				foreach ($offers as $offer) {
					$offerId = (int)$offer['ID'];

					if (isset($jsOffersById[$offerId])) {
						$sortedJsOffers[] = $jsOffersById[$offerId];
						unset($jsOffersById[$offerId]);
					}
				}

				$jsOffers = array_merge($sortedJsOffers, array_values($jsOffersById));
			}

			return [$offers, $jsOffers];
		};

		[$item['OFFERS'], $item['JS_OFFERS']] = $sortOffers(
			$item['OFFERS'],
			is_array($item['JS_OFFERS'] ?? null) ? $item['JS_OFFERS'] : [],
			$arParams
		);

		if (!empty($arParams['SKU_PROPS']) && is_array($arParams['SKU_PROPS'])) {
			foreach ($arParams['SKU_PROPS'] as &$skuProperty) {
				if (
					empty($skuProperty['ID'])
					|| empty($skuProperty['VALUES'])
					|| !is_array($skuProperty['VALUES'])
					|| !isset($item['OFFERS_PROP'][$skuProperty['CODE']])
				) {
					continue;
				}

				$propertyTreeKey = 'PROP_' . $skuProperty['ID'];
				$valueSortMap = [];

				foreach ($item['OFFERS'] as $offerIndex => $offer) {
					$valueId = (string)($offer['TREE'][$propertyTreeKey] ?? '');

					if ($valueId !== '' && !isset($valueSortMap[$valueId])) {
						$valueSortMap[$valueId] = $offerIndex;
					}
				}

				if (empty($valueSortMap)) {
					continue;
				}

				uasort($skuProperty['VALUES'], static function (array $left, array $right) use ($valueSortMap): int {
					$leftId = (string)($left['ID'] ?? '');
					$rightId = (string)($right['ID'] ?? '');
					$leftSort = $valueSortMap[$leftId] ?? PHP_INT_MAX;
					$rightSort = $valueSortMap[$rightId] ?? PHP_INT_MAX;

					if ($leftSort !== $rightSort) {
						return $leftSort <=> $rightSort;
					}

					$leftValueSort = (int)($left['SORT'] ?? 500);
					$rightValueSort = (int)($right['SORT'] ?? 500);

					if ($leftValueSort !== $rightValueSort) {
						return $leftValueSort <=> $rightValueSort;
					}

					return (int)($left['ID'] ?? 0) <=> (int)($right['ID'] ?? 0);
				});
			}
			unset($skuProperty);
		}

		$item['OFFERS_SELECTED'] = 0;

		$actualItem = isset($item['OFFERS'][$item['OFFERS_SELECTED']])
			? $item['OFFERS'][$item['OFFERS_SELECTED']]
			: reset($item['OFFERS']);
	} else {
		$actualItem = $item;
	}

	$displayTitle = $haveOffers && !empty($actualItem['NAME'])
		? $actualItem['NAME']
		: $productTitle;

	// ⚡ ИСПОЛЬЗУЕМ ГОТОВЫЕ ДАННЫЕ ИЗ result_modifier.php ⚡
	$morePhoto = $item['MORE_PHOTO'] ?? [];


	if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers) {
		$price = $item['ITEM_START_PRICE'];
		$minOffer = $item['OFFERS'][$item['ITEM_START_PRICE_SELECTED']];
		$measureRatio = $minOffer['ITEM_MEASURE_RATIOS'][$minOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
	} else {
		$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']] ?? null;
		$measureRatio = $price['MIN_QUANTITY'] ?? null;
	}

	$showSlider = $arParams["SHOW_SLIDER"] === "Y" && is_array($morePhoto) && count($morePhoto) > 1;
	$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($item['CATALOG_SUBSCRIBE'] === 'Y' || $haveOffers);
	$itemHasDetailUrl = isset($item['DETAIL_PAGE_URL']) && $item['DETAIL_PAGE_URL'] != '';
	$favoriteProductId = (int)$actualItem['ID'];
	$isFavorite = function_exists('riseBagsIsFavoriteProduct') && riseBagsIsFavoriteProduct($favoriteProductId);
	$displayCompare = in_array($arParams['DISPLAY_COMPARE'], ['Y', true, 1, '1'], true);
	$compareProductId = (int)($haveOffers && $arParams['PRODUCT_DISPLAY_MODE'] === 'Y' ? $actualItem['ID'] : $item['ID']);
	$compareProductIds = function_exists('riseBagsGetCompareItems') ? riseBagsGetCompareItems($arParams['COMPARE_NAME'] ?: 'CATALOG_COMPARE_LIST') : [];
	$isCompared = in_array($compareProductId, $compareProductIds, true);
	$compareAddUrl = !empty($arParams['~COMPARE_URL_TEMPLATE'])
		? str_replace('#ID#', $compareProductId, $arParams['~COMPARE_URL_TEMPLATE'])
		: '';
	$compareDeleteUrl = !empty($arParams['~COMPARE_DELETE_URL_TEMPLATE'])
		? str_replace('#ID#', $compareProductId, $arParams['~COMPARE_DELETE_URL_TEMPLATE'])
		: '';
	if ($displayCompare && $compareProductId > 0 && ($compareAddUrl === '' || $compareDeleteUrl === '')) {
		$compareActionVariable = $arParams['ACTION_VARIABLE'] ?: 'action';
		$compareProductIdVariable = $arParams['PRODUCT_ID_VARIABLE'] ?: 'id';
		$compareBaseUrl = $APPLICATION->GetCurPageParam(
			'',
			[$compareActionVariable, $compareProductIdVariable, 'ajax_action'],
			false
		);

		if ($compareAddUrl === '') {
			$compareAddUrl = CHTTP::urlAddParams(
				$compareBaseUrl,
				[
					$compareActionVariable => 'ADD_TO_COMPARE_LIST',
					$compareProductIdVariable => $compareProductId,
				],
				['encode' => true]
			);
		}

		if ($compareDeleteUrl === '') {
			$compareDeleteUrl = CHTTP::urlAddParams(
				$compareBaseUrl,
				[
					$compareActionVariable => 'DELETE_FROM_COMPARE_LIST',
					$compareProductIdVariable => $compareProductId,
				],
				['encode' => true]
			);
		}
	}
	$detailPageUrl = $item['DETAIL_PAGE_URL'];
	if ($itemHasDetailUrl && $haveOffers && !empty($actualItem['ID'])) {
		$detailPageUrl = CHTTP::urlAddParams(
			$item['DETAIL_PAGE_URL'],
			array('offer' => (int)$actualItem['ID']),
			array('encode' => true)
		);
	}
?>
	<div class="product-item-container<?= (isset($arResult['SCALABLE']) && $arResult['SCALABLE'] === 'Y' ? ' product-item-scalable-card' : '') ?>"
		id="<?= $areaId ?>" data-entity="item">
		<?php
		$documentRoot = Main\Application::getDocumentRoot();
		$file = new Main\IO\File($documentRoot . $templateFolder . '/card-template.php');
		if ($file->isExists()) {
			include($file->getPath());
		}

		if (!$haveOffers) {
			$jsParams = array(
				'PRODUCT_TYPE' => $item['PRODUCT']['TYPE'],
				'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
				'SHOW_ADD_BASKET_BTN' => false,
				'SHOW_BUY_BTN' => true,
				'SHOW_ABSENT' => true,
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
				'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
				'DISPLAY_COMPARE' => $displayCompare,
				'BIG_DATA' => $item['BIG_DATA'],
				'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
				'VIEW_MODE' => $arResult['TYPE'],
				'USE_SUBSCRIBE' => $showSubscribe,
				'PRODUCT' => array(
					'ID' => $item['ID'],
					'NAME' => $productTitle,
					'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
					// 'PICT' => $item['SECOND_PICT'] ? $item['PREVIEW_PICTURE_SECOND'] : $item['PREVIEW_PICTURE'],
					'PICT' => $item['PREVIEW_PICTURE'],
					'CAN_BUY' => $item['CAN_BUY'],
					'CHECK_QUANTITY' => $item['CHECK_QUANTITY'],
					'MAX_QUANTITY' => $item['CATALOG_QUANTITY'],
					'STEP_QUANTITY' => $item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
					'QUANTITY_FLOAT' => is_float($item['ITEM_MEASURE_RATIOS'][$item['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
					'ITEM_PRICE_MODE' => $item['ITEM_PRICE_MODE'],
					'ITEM_PRICES' => $item['ITEM_PRICES'],
					'ITEM_PRICE_SELECTED' => $item['ITEM_PRICE_SELECTED'],
					'ITEM_QUANTITY_RANGES' => $item['ITEM_QUANTITY_RANGES'],
					'ITEM_QUANTITY_RANGE_SELECTED' => $item['ITEM_QUANTITY_RANGE_SELECTED'],
					'ITEM_MEASURE_RATIOS' => $item['ITEM_MEASURE_RATIOS'],
					'ITEM_MEASURE_RATIO_SELECTED' => $item['ITEM_MEASURE_RATIO_SELECTED'],
					'MORE_PHOTO' => $item['MORE_PHOTO'],
					'MORE_PHOTO_COUNT' => $item['MORE_PHOTO_COUNT']
				),
				'BASKET' => array(
					'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
					'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'EMPTY_PROPS' => empty($item['PRODUCT_PROPERTIES']),
					'BASKET_URL' => $arParams['~BASKET_URL'],
					'ADD_URL_TEMPLATE' => $arParams['~ADD_URL_TEMPLATE'],
					'BUY_URL_TEMPLATE' => $arParams['~BUY_URL_TEMPLATE']
				),
				'VISUAL' => array(
					'ID' => $itemIds['ID'],
					// 'PICT_ID' => $item['SECOND_PICT'] ? $itemIds['SECOND_PICT'] : $itemIds['PICT'],
					'PICT_ID' => $itemIds['PICT'],
					'PICT_SLIDER_ID' => $itemIds['PICT_SLIDER'],
					'QUANTITY_ID' => $itemIds['QUANTITY'],
					'QUANTITY_UP_ID' => $itemIds['QUANTITY_UP'],
					'QUANTITY_DOWN_ID' => $itemIds['QUANTITY_DOWN'],
					'PRICE_ID' => $itemIds['PRICE'],
					'PRICE_OLD_ID' => $itemIds['PRICE_OLD'],
					'PRICE_TOTAL_ID' => $itemIds['PRICE_TOTAL'],
					'BUY_ID' => $itemIds['BUY_LINK'],
					'BASKET_PROP_DIV' => $itemIds['BASKET_PROP_DIV'],
					'BASKET_ACTIONS_ID' => $itemIds['BASKET_ACTIONS'],
					'NOT_AVAILABLE_MESS' => $itemIds['NOT_AVAILABLE_MESS'],
					'COMPARE_LINK_ID' => $itemIds['COMPARE_LINK'],
					'SUBSCRIBE_ID' => $itemIds['SUBSCRIBE_LINK']
				)
			);
		} else {
			$jsParams = array(
				'PRODUCT_TYPE' => $item['PRODUCT']['TYPE'],
				'SHOW_QUANTITY' => false,
				'SHOW_ADD_BASKET_BTN' => false,
				'SHOW_BUY_BTN' => true,
				'SHOW_ABSENT' => true,
				'SHOW_SKU_PROPS' => false,
				// 'SECOND_PICT' => $item['SECOND_PICT'],
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
				'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
				'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
				'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
				'DISPLAY_COMPARE' => $displayCompare,
				'BIG_DATA' => $item['BIG_DATA'],
				'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
				'VIEW_MODE' => $arResult['TYPE'],
				'USE_SUBSCRIBE' => $showSubscribe,
				'DEFAULT_PICTURE' => array(
					'PICTURE' => $item['PRODUCT_PREVIEW'],
				),
				'VISUAL' => array(
					'ID' => $itemIds['ID'],
					'PICT_ID' => $itemIds['PICT'],
					// 'SECOND_PICT_ID' => $itemIds['SECOND_PICT'],
					'PICT_SLIDER_ID' => $itemIds['PICT_SLIDER'],
					'QUANTITY_ID' => $itemIds['QUANTITY'],
					'QUANTITY_UP_ID' => $itemIds['QUANTITY_UP'],
					'QUANTITY_DOWN_ID' => $itemIds['QUANTITY_DOWN'],
					'QUANTITY_MEASURE' => $itemIds['QUANTITY_MEASURE'],
					'QUANTITY_LIMIT' => $itemIds['QUANTITY_LIMIT'],
					'PRICE_ID' => $itemIds['PRICE'],
					'PRICE_OLD_ID' => $itemIds['PRICE_OLD'],
					'PRICE_TOTAL_ID' => $itemIds['PRICE_TOTAL'],
					'TREE_ID' => $itemIds['PROP_DIV'],
					'TREE_ITEM_ID' => $itemIds['PROP'],
					'BUY_ID' => $itemIds['BUY_LINK'],
					'DSC_PERC' => $itemIds['DSC_PERC'],
					'SECOND_DSC_PERC' => $itemIds['SECOND_DSC_PERC'],
					'DISPLAY_PROP_DIV' => $itemIds['DISPLAY_PROP_DIV'],
					'BASKET_ACTIONS_ID' => $itemIds['BASKET_ACTIONS'],
					'NOT_AVAILABLE_MESS' => $itemIds['NOT_AVAILABLE_MESS'],
					'COMPARE_LINK_ID' => $itemIds['COMPARE_LINK'],
					'SUBSCRIBE_ID' => $itemIds['SUBSCRIBE_LINK']
				),
				'BASKET' => array(
					'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'SKU_PROPS' => $item['OFFERS_PROP_CODES'],
					'BASKET_URL' => $arParams['~BASKET_URL'],
					'ADD_URL_TEMPLATE' => $arParams['~ADD_URL_TEMPLATE'],
					'BUY_URL_TEMPLATE' => $arParams['~BUY_URL_TEMPLATE']
				),
				'PRODUCT' => array(
					'ID' => $item['ID'],
					'NAME' => $productTitle,
					'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
					'MORE_PHOTO' => $item['MORE_PHOTO'],
					'MORE_PHOTO_COUNT' => $item['MORE_PHOTO_COUNT']
				),
				'OFFERS' => array(),
				'OFFER_SELECTED' => 0,
				'TREE_PROPS' => array()
			);

			// debug($item['JS_OFFERS']);
			if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && !empty($item['OFFERS_PROP'])) {
				$jsParams['SHOW_QUANTITY'] = $arParams['USE_PRODUCT_QUANTITY'];
				$jsParams['SHOW_SKU_PROPS'] = $item['OFFERS_PROPS_DISPLAY'];
				$jsParams['OFFERS'] = $item['JS_OFFERS']; // тут передаю данные ТП в скрипт !!
				$jsParams['OFFER_SELECTED'] = $item['OFFERS_SELECTED'];
				$jsParams['TREE_PROPS'] = $skuProps;
			}
		}

		if ($displayCompare) {
			$jsParams['COMPARE'] = array(
				'COMPARE_URL_TEMPLATE' => $compareAddUrl,
				'COMPARE_DELETE_URL_TEMPLATE' => $compareDeleteUrl,
				'COMPARE_PATH' => $arParams['COMPARE_PATH']
			);
		}

		if ($item['BIG_DATA']) {
			$jsParams['PRODUCT']['RCM_ID'] = $item['RCM_ID'];
		}

		$jsParams['PRODUCT_DISPLAY_MODE'] = $arParams['PRODUCT_DISPLAY_MODE'];
		$jsParams['USE_ENHANCED_ECOMMERCE'] = $arParams['USE_ENHANCED_ECOMMERCE'];
		$jsParams['DATA_LAYER_NAME'] = $arParams['DATA_LAYER_NAME'];
		$jsParams['BRAND_PROPERTY'] = !empty($item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
			? $item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
			: null;
		$jsParams['SHOW_SLIDER'] = $arParams['SHOW_SLIDER'];

		$templateData = array(
			'JS_OBJ' => $obName,
			'ITEM' => array(
				'ID' => $item['ID'],
				'IBLOCK_ID' => $item['IBLOCK_ID'],
			),
		);
		if ($haveOffers) {
			$templateData['ITEM']['OFFERS_SELECTED'] = $item['OFFERS_SELECTED'];
			$templateData['ITEM']['JS_OFFERS'] = $item['JS_OFFERS'];
		}
		?>
		<script>
			var <?= $obName ?> = new JCCatalogItem(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
		</script>
	</div>
<?php
	unset($item, $actualItem, $minOffer, $itemIds, $jsParams, $displayTitle, $detailPageUrl);
}
