<?
define("HIDE_SIDEBAR", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");

$favoriteItems = function_exists('riseBagsGetFavoriteItemsViewData') ? riseBagsGetFavoriteItemsViewData() : [];
$favoriteProductIds = function_exists('riseBagsGetFavoriteProductIds') ? riseBagsGetFavoriteProductIds() : [];
$favoriteCatalogProductIds = [];

if (!empty($favoriteProductIds) && \Bitrix\Main\Loader::includeModule('catalog')) {
	foreach ($favoriteProductIds as $favoriteProductId) {
		$favoriteProductId = (int)$favoriteProductId;
		if ($favoriteProductId <= 0) {
			continue;
		}

		$skuInfo = CCatalogSku::GetProductInfo($favoriteProductId);
		$favoriteCatalogProductIds[] = !empty($skuInfo['ID']) ? (int)$skuInfo['ID'] : $favoriteProductId;
	}
}

$favoriteCatalogProductIds = array_values(array_unique(array_filter($favoriteCatalogProductIds)));
$favoriteCatalogFilterName = 'favoriteCatalogFilter';
global ${$favoriteCatalogFilterName};
${$favoriteCatalogFilterName} = [
	'=ID' => !empty($favoriteCatalogProductIds) ? $favoriteCatalogProductIds : 0,
];
?>

<section class="section">
	<div class="container">
		<h1 class="title">Избранное</h1>

		<div class="content" data-favorite-empty-message<?= !empty($favoriteItems) ? ' style="display: none;"' : '' ?>>
			<p>В избранном пока нет товаров.</p>
		</div>

		<? if (!empty($favoriteItems)): ?>
			<div data-favorite-content>
				<? $APPLICATION->IncludeComponent(
					"bitrix:catalog.section",
					"littleweb",
					[
						"IBLOCK_TYPE" => "catalog",
						"IBLOCK_ID" => "2",
						"FILTER_NAME" => $favoriteCatalogFilterName,
						"CACHE_TYPE" => "N",
						"CACHE_TIME" => "36000000",
						"CACHE_FILTER" => "N",
						"CACHE_GROUPS" => "Y",
						"SET_TITLE" => "N",
						"SET_BROWSER_TITLE" => "N",
						"SET_META_KEYWORDS" => "N",
						"SET_META_DESCRIPTION" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"SECTION_ID" => "",
						"SECTION_CODE" => "",
						"SECTION_USER_FIELDS" => [],
						"INCLUDE_SUBSECTIONS" => "Y",
						"SHOW_ALL_WO_SECTION" => "Y",
						"HIDE_NOT_AVAILABLE" => "Y",
						"HIDE_NOT_AVAILABLE_OFFERS" => "Y",
						"PAGE_ELEMENT_COUNT" => "24",
						"LINE_ELEMENT_COUNT" => "1",
						"ELEMENT_SORT_FIELD" => "sort",
						"ELEMENT_SORT_ORDER" => "asc",
						"ELEMENT_SORT_FIELD2" => "id",
						"ELEMENT_SORT_ORDER2" => "desc",
						"PROPERTY_CODE" => [
							"NEWPRODUCT",
							"SALELEADER",
							"SPECIALOFFER",
						],
						"OFFERS_FIELD_CODE" => [
							"NAME",
						],
						"OFFERS_PROPERTY_CODE" => [
							"SIZES_SHOES",
							"SIZES_CLOTHES",
							"COLOR_REF",
							"MORE_PHOTO",
							"ARTNUMBER",
						],
						"OFFERS_CART_PROPERTIES" => [
							"SIZES_SHOES",
							"SIZES_CLOTHES",
							"COLOR_REF",
						],
						"OFFERS_LIMIT" => "0",
						"OFFERS_SORT_FIELD" => "sort",
						"OFFERS_SORT_ORDER" => "desc",
						"OFFERS_SORT_FIELD2" => "id",
						"OFFERS_SORT_ORDER2" => "desc",
						"PRICE_CODE" => [
							"BASE",
							"WHOLESALE",
						],
						"USE_PRICE_COUNT" => "Y",
						"SHOW_PRICE_COUNT" => "1",
						"PRICE_VAT_INCLUDE" => "Y",
						"CONVERT_CURRENCY" => "N",
						"BASKET_URL" => "/personal/cart/",
						"ACTION_VARIABLE" => "action",
						"PRODUCT_ID_VARIABLE" => "id",
						"PRODUCT_QUANTITY_VARIABLE" => "quantity",
						"PRODUCT_PROPS_VARIABLE" => "prop",
						"USE_PRODUCT_QUANTITY" => "Y",
						"ADD_PROPERTIES_TO_BASKET" => "Y",
						"PARTIAL_PRODUCT_PROPERTIES" => "N",
						"PRODUCT_DISPLAY_MODE" => "Y",
						"ADD_TO_BASKET_ACTION" => "ADD",
						"SHOW_DISCOUNT_PERCENT" => "Y",
						"SHOW_OLD_PRICE" => "Y",
						"SHOW_MAX_QUANTITY" => "Y",
						"RELATIVE_QUANTITY_FACTOR" => "5",
						"MESS_SHOW_MAX_QUANTITY" => "В наличии",
						"MESS_RELATIVE_QUANTITY_MANY" => "много",
						"MESS_RELATIVE_QUANTITY_FEW" => "мало",
						"MESS_BTN_BUY" => "Купить",
						"MESS_BTN_ADD_TO_BASKET" => "В корзину",
						"MESS_BTN_COMPARE" => "Сравнение",
						"MESS_BTN_DETAIL" => "Подробнее",
						"MESS_BTN_SUBSCRIBE" => "Подписаться",
						"MESS_NOT_AVAILABLE" => "Нет в наличии",
						"MESS_NOT_AVAILABLE_SERVICE" => "Недоступно",
						"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
						"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false}]",
						"ADD_PICT_PROP" => "-",
						"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
						"OFFER_TREE_PROPS" => [
							"SIZES_SHOES",
							"SIZES_CLOTHES",
							"COLOR_REF",
						],
						"LABEL_PROP" => [
							"NEW",
							"POPULAR",
						],
						"LABEL_PROP_MOBILE" => "",
						"LABEL_PROP_POSITION" => "top-left",
						"DISCOUNT_PERCENT_POSITION" => "bottom-right",
						"DISPLAY_COMPARE" => "Y",
						"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
						"COMPARE_PATH" => "/catalog/compare/",
						"PRODUCT_SUBSCRIPTION" => "Y",
						"TEMPLATE_THEME" => "site",
						"DISPLAY_TOP_PAGER" => "N",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"PAGER_TEMPLATE" => "round",
						"PAGER_TITLE" => "Товары",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
						"PAGER_SHOW_ALL" => "N",
						"LAZY_LOAD" => "N",
						"LOAD_ON_SCROLL" => "N",
						"MESS_BTN_LAZY_LOAD" => "Показать ещё",
						"USE_ENHANCED_ECOMMERCE" => "N",
						"DATA_LAYER_NAME" => "",
						"BRAND_PROPERTY" => "",
						"SHOW_SLIDER" => "Y",
						"SLIDER_INTERVAL" => "3000",
						"SLIDER_PROGRESS" => "N",
						"SHOW_CLOSE_POPUP" => "Y",
						"ENLARGE_PRODUCT" => "PROP",
						"ENLARGE_PROP" => "NEW",
					],
					false
				); ?>
			</div>
		<? endif; ?>
	</div>
</section>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
