<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
  die();
}

$arParams['PRODUCT_DATA'] = [];
$productId = (int)($arParams['OFFER_ID'] ?? 0);

if ($productId <= 0) {
  return;
}

$requiredModules = ['iblock', 'catalog', 'sale', 'highloadblock'];
foreach ($requiredModules as $module) {
  if (!\Bitrix\Main\Loader::includeModule($module)) {
    return;
  }
}

$getElementById = static function (int $id, array $select = ['ID', 'NAME', 'IBLOCK_ID']): ?array {
  $element = CIBlockElement::GetList([], ['ID' => $id], false, false, $select)->Fetch();

  return is_array($element) ? $element : null;
};

$getDirectoryData = static function (string $tableName, string $xmlId): ?array {
  static $hlCache = [];

  if ($tableName === '' || $xmlId === '') {
    return null;
  }

  if (!isset($hlCache[$tableName])) {
    $hlCache[$tableName] = \Bitrix\Highloadblock\HighloadBlockTable::getList([
      'filter' => ['=TABLE_NAME' => $tableName],
    ])->fetch() ?: null;
  }

  if (!$hlCache[$tableName]) {
    return null;
  }

  $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlCache[$tableName]);
  $entityDataClass = $entity->getDataClass();

  $result = $entityDataClass::getList([
    'select' => ['*'],
    'filter' => ['=UF_XML_ID' => $xmlId],
  ])->fetch();

  if (!$result) {
    return null;
  }

  return [
    'ID' => $result['ID'],
    'NAME' => $result['UF_NAME'],
    'XML_ID' => $result['UF_XML_ID'],
    'SORT' => $result['UF_SORT'],
    'FILE_ID' => $result['UF_FILE'],
    'FILE_PATH' => $result['UF_FILE'] ? CFile::GetPath($result['UF_FILE']) : null,
    'DESCRIPTION' => $result['UF_DESCRIPTION'] ?? '',
    'FULL_DATA' => $result,
  ];
};

$getPropertyName = static function (int $iblockId, string $propertyCode): string {
  static $propertyNames = [];

  if (!isset($propertyNames[$iblockId][$propertyCode])) {
    $dbProperty = CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => $propertyCode]);
    $propertyNames[$iblockId][$propertyCode] = ($dbProperty->Fetch()['NAME'] ?? $propertyCode);
  }

  return $propertyNames[$iblockId][$propertyCode];
};

$processProperty = static function (array $prop, int $iblockId) use ($getDirectoryData, $getPropertyName): ?array {
  if ($prop['VALUE'] === '' || $prop['VALUE'] === null || $prop['VALUE'] === false) {
    return null;
  }

  $processedProp = [
    'CODE' => $prop['CODE'],
    'NAME' => $getPropertyName($iblockId, $prop['CODE']),
    'VALUE' => $prop['VALUE'],
    'RAW_VALUE' => $prop['VALUE'],
    'PROPERTY_TYPE' => $prop['PROPERTY_TYPE'],
    'USER_TYPE' => $prop['USER_TYPE'],
    'MULTIPLE' => $prop['MULTIPLE'],
  ];

  if ($prop['PROPERTY_TYPE'] === 'F') {
    $processedProp['VALUE'] = CFile::GetPath($prop['VALUE']);
    $processedProp['FILE_ARRAY'] = CFile::GetFileArray($prop['VALUE']);
  }

  if ($prop['USER_TYPE'] === 'directory') {
    $settings = is_array($prop['USER_TYPE_SETTINGS']) ? $prop['USER_TYPE_SETTINGS'] : [];
    $tableName = (string)($settings['TABLE_NAME'] ?? '');

    if ($tableName !== '') {
      $directoryData = $getDirectoryData($tableName, (string)$prop['VALUE']);
      if ($directoryData) {
        $processedProp['DIRECTORY_DATA'] = $directoryData;
        $processedProp['VALUE'] = $directoryData['NAME'];
        $processedProp['DIRECTORY_IMAGE'] = $directoryData['FILE_PATH'];
      }
    }
  }

  return $processedProp;
};

$getAllPropertiesWithNames = static function (int $elementId, int $iblockId) use ($getPropertyName, $processProperty): array {
  $properties = [];
  $dbProps = CIBlockElement::GetProperty($iblockId, $elementId, ['sort' => 'asc'], []);

  while ($prop = $dbProps->Fetch()) {
    $processedProp = $processProperty($prop, $iblockId);
    if (!$processedProp) {
      continue;
    }

    $code = $prop['CODE'];
    if ($prop['MULTIPLE'] === 'Y') {
      if (!isset($properties[$code])) {
        $properties[$code] = [
          'CODE' => $code,
          'NAME' => $getPropertyName($iblockId, $code),
          'MULTIPLE' => 'Y',
          'VALUES' => [],
        ];
      }
      $properties[$code]['VALUES'][] = $processedProp;
      continue;
    }

    $properties[$code] = $processedProp;
  }

  return $properties;
};

$extractMorePhoto = static function (array $properties): string {
  if (empty($properties['MORE_PHOTO'])) {
    return '';
  }

  $morePhoto = $properties['MORE_PHOTO'];
  if (($morePhoto['MULTIPLE'] ?? 'N') === 'Y' && !empty($morePhoto['VALUES'][0]['VALUE'])) {
    return (string)$morePhoto['VALUES'][0]['VALUE'];
  }

  return (string)($morePhoto['VALUE'] ?? '');
};

$getPriceData = static function (int $elementId): array {
  $price = CCatalogProduct::GetOptimalPrice($elementId, 1, [], 'N');
  if (!$price || empty($price['RESULT_PRICE'])) {
    return [
      'PRICE' => 0,
      'CURRENCY' => 'RUB',
      'FORMATTED' => '',
    ];
  }

  $resultPrice = $price['RESULT_PRICE'];
  $value = (float)($resultPrice['DISCOUNT_PRICE'] ?? $resultPrice['BASE_PRICE'] ?? 0);
  $currency = (string)($resultPrice['CURRENCY'] ?? 'RUB');

  return [
    'PRICE' => $value,
    'CURRENCY' => $currency,
    'FORMATTED' => CurrencyFormat($value, $currency),
  ];
};

$productInfo = CCatalogSKU::GetProductInfo($productId);
$isOffer = !empty($productInfo);
$elementId = $productId;
$parentId = null;

if ($isOffer) {
  $parentId = (int)$productInfo['ID'];
} else {
  $iblockId = (int)CIBlockElement::GetIBlockByID($productId);
  if ($iblockId > 0) {
    $skuInfo = CCatalogSKU::GetInfoByProductIBlock($iblockId);
    if ($skuInfo) {
      $offers = CCatalogSKU::getOffersList($productId, $skuInfo['PRODUCT_IBLOCK_ID']);
      if (!empty($offers[$productId])) {
        $firstOffer = reset($offers[$productId]);
        $elementId = (int)$firstOffer['ID'];
        $parentId = $productId;
        $isOffer = true;
      }
    }
  }
}

$parentElement = $parentId ? $getElementById($parentId, ['ID', 'NAME', 'IBLOCK_ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE']) : null;
$parentProperties = $parentElement ? $getAllPropertiesWithNames($parentId, (int)$parentElement['IBLOCK_ID']) : [];

$element = $getElementById($elementId, ['ID', 'NAME', 'IBLOCK_ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'CODE']);
if (!$element) {
  return;
}

$offerProperties = $getAllPropertiesWithNames($elementId, (int)$element['IBLOCK_ID']);
$allProperties = array_merge($parentProperties, $offerProperties);

$productTitle = (string)($element['NAME'] ?? '');
if ($productTitle === '' && $parentElement) {
  $productTitle = (string)($parentElement['NAME'] ?? '');
}

$priceData = $getPriceData($elementId);
$productImage = '';

if ((int)$element['PREVIEW_PICTURE'] > 0) {
  $productImage = (string)CFile::GetPath($element['PREVIEW_PICTURE']);
}
if ($productImage === '' && (int)$element['DETAIL_PICTURE'] > 0) {
  $productImage = (string)CFile::GetPath($element['DETAIL_PICTURE']);
}
if ($productImage === '') {
  $productImage = $extractMorePhoto($offerProperties);
}
if ($productImage === '' && $parentElement) {
  if ((int)$parentElement['PREVIEW_PICTURE'] > 0) {
    $productImage = (string)CFile::GetPath($parentElement['PREVIEW_PICTURE']);
  } elseif ((int)$parentElement['DETAIL_PICTURE'] > 0) {
    $productImage = (string)CFile::GetPath($parentElement['DETAIL_PICTURE']);
  }
}

$propertiesToFill = ["ARTNUMBER", "COLOR_REF"];

// Фильтруем свойства, оставляя только те, CODE которых есть в $needPropsArray
$filteredProperties = array_filter(
  $allProperties,
  function ($prop) use ($propertiesToFill) {
    return in_array($prop["CODE"], $propertiesToFill);
  }
);

$arParams['PRODUCT_DATA'] = [
  'PRODUCT_ID' => $elementId,
  'PRODUCT_PARENT_ID' => $parentId,
  'IS_OFFER' => $isOffer,
  'PRODUCT_TITLE' => $productTitle,
  'PRODUCT_PRICE' => $priceData['PRICE'],
  'PRODUCT_PRICE_FORMATTED' => $priceData['FORMATTED'],
  'PRODUCT_CURRENCY' => $priceData['CURRENCY'],
  'PRODUCT_IMG' => $productImage,
  'PRODUCT_IBLOCK_ID' => $element['IBLOCK_ID'],
  'PROPERTIES' => $filteredProperties,
  'PRODUCT_URL' => (string)($arParams['SEF_FOLDER'] ?? '/') . (($element['CODE'] ?: $elementId) . '/'),
];

if (!$isOffer && $parentId === null) {
  $skuInfo = CCatalogSKU::GetInfoByProductIBlock((int)$element['IBLOCK_ID']);
  if ($skuInfo) {
    $offersList = CCatalogSKU::getOffersList($elementId, $skuInfo['PRODUCT_IBLOCK_ID']);
    if (!empty($offersList[$elementId])) {
      $arParams['PRODUCT_DATA']['OFFERS'] = [];

      foreach ($offersList[$elementId] as $offer) {
        $offerId = (int)$offer['ID'];
        $offerProps = $getAllPropertiesWithNames($offerId, (int)$offer['IBLOCK_ID']);
        $offerProps = array_merge($parentProperties, $offerProps);

        $offerPriceData = $getPriceData($offerId);
        $arParams['PRODUCT_DATA']['OFFERS'][] = [
          'ID' => $offerId,
          'NAME' => $offer['NAME'],
          'PRICE' => $offerPriceData['PRICE'],
          'PRICE_FORMATTED' => $offerPriceData['FORMATTED'],
          'PRICE_CURRENCY' => $offerPriceData['CURRENCY'],
          'IMAGE' => $extractMorePhoto($offerProps),
          'PROPERTIES' => $offerProps,
        ];
      }
    }
  }
}
