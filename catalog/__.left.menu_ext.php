<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
  "custom:menu.sections.elements",
  "",
  array(
    "IS_SEF" => "Y",
    "SEF_BASE_URL" => "",
    "SECTION_PAGE_URL" => "#SECTION_CODE#/",
    "DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_CODE#",
    "IBLOCK_TYPE" => "catalog",
    "IBLOCK_ID" => "2",
    "DEPTH_LEVEL" => "2",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "36000000",
  ),
  false
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
