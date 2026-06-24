<?
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$productId = (int)$_GET['product_id'];
$offerSelectedIndex = (int)$_GET['offer_id'] ?: 0;

// Включаем буферизацию
ob_start();

$APPLICATION->IncludeComponent(
  "bitrix:catalog.element",
  "quickview",
  [
    "AJAX_MODE" => "Y",
    "AJAX_OPTION_STYLE" => "Y",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_HISTORY" => "N",
    "ELEMENT_ID" => $_GET['product_id'],
    "OFFER_SELECTED" => $offerSelectedIndex,

    "ACTION_VARIABLE" => "action",
    "ADDITIONAL_FILTER_NAME" => "elementFilter",
    "ADD_DETAIL_TO_SLIDER" => "N",
    "ADD_ELEMENT_CHAIN" => "N",
    "ADD_PICT_PROP" => "-",
    "ADD_PROPERTIES_TO_BASKET" => "Y",
    "ADD_SECTIONS_CHAIN" => "Y",
    "ADD_TO_BASKET_ACTION" => [
      0 => "ADD",
    ],
    "ADD_TO_BASKET_ACTION_PRIMARY" => [],
    "BACKGROUND_IMAGE" => "-",
    "BASKET_URL" => "/personal/cart/",
    "BRAND_USE" => "N",
    "BROWSER_TITLE" => "-",
    "CACHE_GROUPS" => "N",
    "CACHE_TIME" => "36000000",
    "CACHE_TYPE" => "N",
    "CHECK_SECTION_ID_VARIABLE" => "N",
    "COMPATIBLE_MODE" => "N",
    "CONVERT_CURRENCY" => "N",
    "DETAIL_PICTURE_MODE" => [
      0 => "POPUP",
      1 => "MAGNIFIER",
    ],
    "DETAIL_URL" => "",
    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
    "DISPLAY_COMPARE" => "N",
    "DISPLAY_NAME" => "Y",
    "DISPLAY_PREVIEW_TEXT_MODE" => "E",
    "ELEMENT_CODE" => "",

    "GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
    "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
    "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "4",
    "GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
    "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
    "GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
    "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "4",
    "GIFTS_MESS_BTN_BUY" => "Выбрать",
    "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
    "GIFTS_SHOW_IMAGE" => "Y",
    "GIFTS_SHOW_NAME" => "Y",
    "GIFTS_SHOW_OLD_PRICE" => "Y",
    "HIDE_NOT_AVAILABLE_OFFERS" => "Y",
    "IBLOCK_ID" => "4",
    "IBLOCK_TYPE" => "1c_catalog",
    "IMAGE_RESOLUTION" => "16by9",
    "LABEL_PROP" => [],
    "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
    "LINK_IBLOCK_ID" => "",
    "LINK_IBLOCK_TYPE" => "",
    "LINK_PROPERTY_SID" => "",
    "MAIN_BLOCK_OFFERS_PROPERTY_CODE" => [
      0 => "RAZMER",
    ],
    "MAIN_BLOCK_PROPERTY_CODE" => [],
    "MESSAGE_404" => "",
    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
    "MESS_BTN_BUY" => "Купить",
    "MESS_BTN_SUBSCRIBE" => "Подписаться",
    "MESS_COMMENTS_TAB" => "Комментарии",
    "MESS_DESCRIPTION_TAB" => "Описание",
    "MESS_NOT_AVAILABLE" => "Нет в наличии",
    "MESS_NOT_AVAILABLE_SERVICE" => "Недоступно",
    "MESS_PRICE_RANGES_TITLE" => "Цены",
    "MESS_PROPERTIES_TAB" => "Характеристики",
    "META_DESCRIPTION" => "-",
    "META_KEYWORDS" => "-",
    "OFFERS_FIELD_CODE" => [
      0 => "",
      1 => "HIT",
      2 => "",
    ],
    "OFFERS_LIMIT" => "0",
    "OFFERS_SORT_FIELD" => "sort",
    "OFFERS_SORT_FIELD2" => "id",
    "OFFERS_SORT_ORDER" => "asc",
    "OFFERS_SORT_ORDER2" => "desc",
    "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",

    "PARTIAL_PRODUCT_PROPERTIES" => "Y",
    "PRICE_CODE" => [
      0 => "Розничная",
    ],
    "PRICE_VAT_INCLUDE" => "Y",
    "PRICE_VAT_SHOW_VALUE" => "N",
    "PRODUCT_ID_VARIABLE" => "id",
    "PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
    "PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
    "PRODUCT_PROPS_VARIABLE" => "prop",
    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
    "PRODUCT_SUBSCRIPTION" => "Y",
    "SECTION_CODE" => "",
    "SECTION_ID" => $_REQUEST["SECTION_ID"],
    "SECTION_ID_VARIABLE" => "SECTION_ID",
    "SECTION_URL" => "",
    "SEF_MODE" => "N",
    "SET_BROWSER_TITLE" => "Y",
    "SET_CANONICAL_URL" => "N",
    "SET_LAST_MODIFIED" => "N",
    "SET_META_DESCRIPTION" => "Y",
    "SET_META_KEYWORDS" => "Y",
    "SET_STATUS_404" => "N",
    "SET_TITLE" => "Y",
    "SET_VIEWED_IN_COMPONENT" => "N",
    "SHOW_404" => "N",
    "SHOW_CLOSE_POPUP" => "N",
    "SHOW_DEACTIVATED" => "N",
    "SHOW_DISCOUNT_PERCENT" => "Y",
    "SHOW_MAX_QUANTITY" => "N",
    "SHOW_OLD_PRICE" => "Y",
    "SHOW_PRICE_COUNT" => "1",
    "SHOW_SKU_DESCRIPTION" => "N",
    "SHOW_SLIDER" => "N",
    "STRICT_SECTION_CHECK" => "N",
    "TEMPLATE_THEME" => "blue",
    "USE_COMMENTS" => "N",
    "USE_ELEMENT_COUNTER" => "Y",
    "USE_ENHANCED_ECOMMERCE" => "N",
    "USE_GIFTS_DETAIL" => "Y",
    "USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
    "USE_MAIN_ELEMENT_SECTION" => "N",
    "USE_PRICE_COUNT" => "N",
    "USE_PRODUCT_QUANTITY" => "Y",
    "USE_RATIO_IN_RANGES" => "N",
    "USE_VOTE_RATING" => "N",
    "COMPONENT_TEMPLATE" => "kovry",
    "DISCOUNT_PERCENT_POSITION" => "bottom-right"
  ],
  false
);

$content = ob_get_clean();

// Для AJAX возвращаем чистый HTML
echo $content;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
