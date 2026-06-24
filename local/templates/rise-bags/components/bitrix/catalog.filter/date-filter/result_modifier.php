<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

$iblockId = 1;

$arSelect = ['ID', 'NAME', 'DATE_ACTIVE_FROM'];
$arFilter = ['IBLOCK_ID' => $iblockId];

$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

$arrTabs = [];

while ($ob = $res->GetNextElement()) {
  $arFields = $ob->GetFields();
  $arProps = $ob->GetProperties();

  $dateFrom = $arFields['DATE_ACTIVE_FROM'] ?? '';

  $year = strlen($dateFrom) >= 4 ? substr($dateFrom, -4) : $dateFrom;

  $arrTabs[] = [
    'YEAR' => $year,
    'VALUE_FROM' => '01.01.' . $year,
    'VALUE_TO' => '31.12.' . $year
  ];

  $filteredTabs = array_filter($arrTabs, function ($item) {
    return !empty($item['YEAR']) && $item['YEAR'] !== '';
  });

  $uniqueByYear = [];

  foreach ($filteredTabs as $item) {
    $year = $item['YEAR'];

    if (!isset($uniqueByYear[$year])) {
      $uniqueByYear[$year] = $item;
    }
  }

  $arrTabs = array_values($uniqueByYear);
}

$arResult["TABS"] = $arrTabs;
