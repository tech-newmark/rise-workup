<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$curPage = $APPLICATION->getCurPage();

if ($curPage == '/') {
  $arItems = [];

  foreach ($arResult["ITEMS"] as $arItem) {
    if ($arItem["PROPERTIES"]["SHOW_ON_INDEX_PAGE"]["VALUE"] == "Y") {
      $arItems[] = $arItem;
    }
  }

  $arResult["ITEMS"] = $arItems;
}
