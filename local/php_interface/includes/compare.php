<?php

use Bitrix\Iblock\SectionElementTable;
use Bitrix\Main\Loader;

if (!function_exists('riseBagsGetCompareItems')) {
  function riseBagsGetCompareItems(string $compareName = 'CATALOG_COMPARE_LIST'): array
  {
    $compareItems = [];
    $sessionCompare = $_SESSION[$compareName] ?? [];

    if (!is_array($sessionCompare)) {
      return [];
    }

    foreach ($sessionCompare as $iblockId => $compareData) {
      if (empty($compareData['ITEMS']) || !is_array($compareData['ITEMS'])) {
        continue;
      }

      foreach (array_keys($compareData['ITEMS']) as $productId) {
        $productId = (int)$productId;

        if ($productId > 0) {
          $compareItems[] = $productId;
        }
      }
    }

    return array_values(array_unique($compareItems));
  }
}

if (!function_exists('riseBagsGetCompareCount')) {
  function riseBagsGetCompareCount(string $compareName = 'CATALOG_COMPARE_LIST'): int
  {
    return count(riseBagsGetCompareItems($compareName));
  }
}

if (!function_exists('riseBagsDeleteCompareItem')) {
  function riseBagsDeleteCompareItem(int $productId, string $compareName = 'CATALOG_COMPARE_LIST'): bool
  {
    $deleted = false;

    if ($productId <= 0 || empty($_SESSION[$compareName]) || !is_array($_SESSION[$compareName])) {
      return false;
    }

    foreach ($_SESSION[$compareName] as $iblockId => &$compareData) {
      if (!isset($compareData['ITEMS'][$productId])) {
        continue;
      }

      unset($compareData['ITEMS'][$productId]);
      $deleted = true;
    }
    unset($compareData);

    return $deleted;
  }
}

if (!function_exists('riseBagsAddCompareItem')) {
  function riseBagsAddCompareItem(int $productId, int $iblockId, string $compareName = 'CATALOG_COMPARE_LIST'): bool
  {
    global $APPLICATION;

    if ($productId <= 0 || $iblockId <= 0 || !Loader::includeModule('iblock')) {
      return false;
    }

    if (!isset($_SESSION[$compareName][$iblockId]['ITEMS']) || !is_array($_SESSION[$compareName][$iblockId]['ITEMS'])) {
      $_SESSION[$compareName][$iblockId]['ITEMS'] = [];
    }

    if (isset($_SESSION[$compareName][$iblockId]['ITEMS'][$productId])) {
      return true;
    }

    $offers = CIBlockPriceTools::GetOffersIBlock($iblockId);
    $offersIblockId = $offers ? (int)$offers['OFFERS_IBLOCK_ID'] : 0;
    $select = [
      'ID',
      'IBLOCK_ID',
      'IBLOCK_SECTION_ID',
      'NAME',
      'DETAIL_PAGE_URL',
    ];
    $filter = [
      'ID' => $productId,
      'IBLOCK_LID' => SITE_ID,
      'IBLOCK_ACTIVE' => 'Y',
      'ACTIVE_DATE' => 'Y',
      'ACTIVE' => 'Y',
      'CHECK_PERMISSIONS' => 'Y',
      'MIN_PERMISSION' => 'R',
      'IBLOCK_ID' => $offersIblockId > 0 ? [$iblockId, $offersIblockId] : $iblockId,
    ];

    $elementIterator = CIBlockElement::GetList([], $filter, false, false, $select);
    $element = $elementIterator->GetNext();
    unset($elementIterator);

    if (empty($element)) {
      return false;
    }

    if ($offersIblockId > 0 && (int)$element['IBLOCK_ID'] === $offersIblockId) {
      $masterPropertyIterator = CIBlockElement::GetProperty(
        $element['IBLOCK_ID'],
        $element['ID'],
        [],
        ['ID' => $offers['OFFERS_PROPERTY_ID'], 'EMPTY' => 'N']
      );
      $masterProperty = $masterPropertyIterator->Fetch();
      unset($masterPropertyIterator);

      $masterId = (int)($masterProperty['VALUE'] ?? 0);
      $masterIblockId = (int)($masterProperty['LINK_IBLOCK_ID'] ?? 0);

      if ($masterId <= 0 || $masterIblockId <= 0) {
        return false;
      }

      $masterIterator = CIBlockElement::GetList(
        [],
        [
          'ID' => $masterId,
          'IBLOCK_ID' => $masterIblockId,
          'ACTIVE' => 'Y',
        ],
        false,
        false,
        $select
      );
      $master = $masterIterator->GetNext();
      unset($masterIterator);

      if (empty($master)) {
        return false;
      }

      $master['NAME'] = $element['NAME'];
      $element = $master;
    }

    $sectionsList = [];
    $sectionsIterator = SectionElementTable::getList([
      'select' => ['IBLOCK_SECTION_ID'],
      'filter' => [
        '=IBLOCK_ELEMENT_ID' => $element['ID'],
        '=ADDITIONAL_PROPERTY_ID' => null,
      ],
    ]);

    while ($section = $sectionsIterator->fetch()) {
      $sectionId = (int)$section['IBLOCK_SECTION_ID'];
      $sectionsList[$sectionId] = $sectionId;
    }
    unset($section, $sectionsIterator);

    $_SESSION[$compareName][$iblockId]['ITEMS'][$productId] = [
      'ID' => $element['ID'],
      '~ID' => $element['~ID'],
      'IBLOCK_ID' => $element['IBLOCK_ID'],
      '~IBLOCK_ID' => $element['~IBLOCK_ID'],
      'IBLOCK_SECTION_ID' => $element['IBLOCK_SECTION_ID'],
      '~IBLOCK_SECTION_ID' => $element['~IBLOCK_SECTION_ID'],
      'NAME' => $element['NAME'],
      '~NAME' => $element['~NAME'],
      'DETAIL_PAGE_URL' => $element['DETAIL_PAGE_URL'],
      '~DETAIL_PAGE_URL' => $element['~DETAIL_PAGE_URL'],
      'SECTIONS_LIST' => $sectionsList,
      'PARENT_ID' => $productId,
      'DELETE_URL' => htmlspecialcharsbx($APPLICATION->GetCurPageParam(
        'action=DELETE_FROM_COMPARE_LIST&id=' . $productId,
        ['action', 'id']
      )),
    ];

    return true;
  }
}
