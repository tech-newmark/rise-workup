<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();



$arResult["SECTION_TITLES"] = [];

foreach ($arResult["SECTIONS"] as $arSection) {
  // debug($arSection);
  $arResult["SECTION_TITLES"][$arSection["ID"]] = [
    "ID" => $arSection["ID"],
    "NAME" => $arSection["NAME"],
    "CODE" => $arSection["CODE"],
    "SECTION_PAGE_URL" => $arSection["SECTION_PAGE_URL"]
  ];
}
