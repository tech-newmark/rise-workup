<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
  "bitrix:menu.sections",
  "",
  array(
    "CACHE_TIME" => "36000000",
    "CACHE_TYPE" => "A",
    "DEPTH_LEVEL" => "3",
    "DETAIL_PAGE_URL" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#",
    "IBLOCK_ID" => "2",
    "IBLOCK_TYPE" => "catalog",
    "IS_SEF" => "Y",
    "SECTION_PAGE_URL" => "#SECTION_CODE_PATH#/",
    "SEF_BASE_URL" => "/catalog/"
  )
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
