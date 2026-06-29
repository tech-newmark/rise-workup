<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();



$arResult["SECTION_TITLES"] = [];

$isHiddenInMenu = static function ($value): bool {
  if (is_array($value)) {
    foreach ($value as $item) {
      if ($item !== null && $item !== '' && $item !== '0' && $item !== 0 && $item !== false) {
        return true;
      }
    }

    return false;
  }

  $value = strtoupper(trim((string)$value));

  return $value !== '' && $value !== '0' && $value !== 'N' && $value !== 'FALSE';
};

$hiddenSectionIds = [];

if (\Bitrix\Main\Loader::includeModule('iblock')) {
  $hiddenSectionsIterator = CIBlockSection::GetList(
    [],
    ['IBLOCK_ID' => (int)$arParams['IBLOCK_ID']],
    false,
    ['ID', 'UF_*']
  );

  while ($hiddenSection = $hiddenSectionsIterator->GetNext(false, false)) {
    if ($isHiddenInMenu($hiddenSection['UF_HIDE_IN_MENU'] ?? null)) {
      $hiddenSectionIds[(int)$hiddenSection['ID']] = true;
    }
  }
}

$arResult['SECTIONS'] = array_values(array_filter(
  $arResult['SECTIONS'],
  static function ($section) use ($hiddenSectionIds) {
    return empty($hiddenSectionIds[(int)$section['ID']]);
  }
));

$arResult['SECTIONS_COUNT'] = count($arResult['SECTIONS']);

foreach ($arResult["SECTIONS"] as $arSection) {
  // debug($arSection);
  $arResult["SECTION_TITLES"][$arSection["ID"]] = [
    "ID" => $arSection["ID"],
    "NAME" => $arSection["NAME"],
    "CODE" => $arSection["CODE"],
    "SECTION_PAGE_URL" => $arSection["SECTION_PAGE_URL"]
  ];
}
