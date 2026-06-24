<?php

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

if (!defined('RB_PRODUCT_IBLOCK_ID')) {
  define('RB_PRODUCT_IBLOCK_ID', 2);
}

if (!defined('RB_OFFER_IBLOCK_ID')) {
  define('RB_OFFER_IBLOCK_ID', 3);
}

if (!function_exists('rbLowercaseColorName')) {
  /**
   * Приводит название цвета к нижнему регистру с поддержкой кириллицы.
   */
  function rbLowercaseColorName(string $colorName): string
  {
    $colorName = trim($colorName);

    if ($colorName === '') {
      return '';
    }

    if (function_exists('mb_strtolower')) {
      return mb_strtolower($colorName, defined('LANG_CHARSET') ? LANG_CHARSET : 'UTF-8');
    }

    return strtolower($colorName);
  }
}

if (!function_exists('rbIsLocalhostServer')) {
  /**
   * Проверяет, что скрипт запущен на локальном сервере.
   */
  function rbIsLocalhostServer(): bool
  {
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host);
    $serverAddress = (string)($_SERVER['SERVER_ADDR'] ?? '');

    return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
      || in_array($serverAddress, ['127.0.0.1', '::1'], true);
  }
}

if (!function_exists('rbGetAdditionalColorPropertyId')) {
  /**
   * Возвращает ID свойства дополнительного цвета для текущего окружения.
   */
  function rbGetAdditionalColorPropertyId(): int
  {
    return rbIsLocalhostServer() ? 57 : 71;
  }
}

if (!function_exists('rbGetDirectoryValueName')) {
  /**
   * Возвращает человекочитаемое имя значения свойства типа "Справочник".
   */
  function rbGetDirectoryValueName(array $property, $value): string
  {
    static $hlBlocks = [];

    $value = trim((string)$value);
    $settings = is_array($property['USER_TYPE_SETTINGS'] ?? null) ? $property['USER_TYPE_SETTINGS'] : [];
    $tableName = (string)($settings['TABLE_NAME'] ?? '');

    if ($value === '' || $tableName === '' || !Loader::includeModule('highloadblock')) {
      return '';
    }

    if (!array_key_exists($tableName, $hlBlocks)) {
      $hlBlocks[$tableName] = HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $tableName],
      ])->fetch() ?: null;
    }

    if (!$hlBlocks[$tableName]) {
      return '';
    }

    $entity = HighloadBlockTable::compileEntity($hlBlocks[$tableName]);
    $entityDataClass = $entity->getDataClass();
    $directoryItem = $entityDataClass::getList([
      'select' => ['UF_NAME'],
      'filter' => ['=UF_XML_ID' => $value],
      'limit' => 1,
    ])->fetch();

    return trim((string)($directoryItem['UF_NAME'] ?? ''));
  }
}

if (!function_exists('rbGetOfferPropertyDirectoryName')) {
  /**
   * Получает название значения справочного свойства торгового предложения.
   */
  function rbGetOfferPropertyDirectoryName(int $offerId, int $offerIblockId, int $propertyId): string
  {
    $property = CIBlockElement::GetProperty(
      $offerIblockId,
      $offerId,
      ['sort' => 'asc'],
      ['ID' => $propertyId]
    )->Fetch();

    if (!$property || $property['VALUE'] === '' || $property['VALUE'] === null) {
      return '';
    }

    if (($property['USER_TYPE'] ?? '') === 'directory') {
      return rbGetDirectoryValueName($property, $property['VALUE']);
    }

    return trim((string)$property['VALUE']);
  }
}

if (!function_exists('rbGetOfferParentProductId')) {
  /**
   * Возвращает ID родительского товара для торгового предложения.
   */
  function rbGetOfferParentProductId(int $offerId, int $offerIblockId): int
  {
    $productInfo = CCatalogSKU::GetProductInfo($offerId);
    if (!empty($productInfo['ID'])) {
      return (int)$productInfo['ID'];
    }

    $skuInfo = CCatalogSKU::GetInfoByOfferIBlock($offerIblockId);
    $linkPropertyId = (int)($skuInfo['SKU_PROPERTY_ID'] ?? 0);
    if ($linkPropertyId > 0) {
      $linkProperty = CIBlockElement::GetProperty(
        $offerIblockId,
        $offerId,
        ['sort' => 'asc'],
        ['ID' => $linkPropertyId]
      )->Fetch();

      if (!empty($linkProperty['VALUE'])) {
        return (int)$linkProperty['VALUE'];
      }
    }

    $linkProperty = CIBlockElement::GetProperty(
      $offerIblockId,
      $offerId,
      ['sort' => 'asc'],
      ['CODE' => 'CML2_LINK']
    )->Fetch();

    return (int)($linkProperty['VALUE'] ?? 0);
  }
}

if (!function_exists('rbBuildTradeOfferName')) {
  /**
   * Формирует название ТП: "Название товара цвет основной/дополнительный".
   * Если цвета не заполнены, возвращает только название товара.
   */
  function rbBuildTradeOfferName(int $offerId): string
  {
    if ($offerId <= 0 || !Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
      return '';
    }

    $offerIblockId = (int)CIBlockElement::GetIBlockByID($offerId);
    $productId = rbGetOfferParentProductId($offerId, $offerIblockId);
    if ($productId <= 0) {
      return '';
    }

    $product = CIBlockElement::GetList(
      [],
      ['ID' => $productId],
      false,
      false,
      ['ID', 'NAME']
    )->Fetch();

    $productName = trim((string)($product['NAME'] ?? ''));
    $color = rbLowercaseColorName(rbGetOfferPropertyDirectoryName($offerId, $offerIblockId, 21));
    $additionalColor = rbLowercaseColorName(
      rbGetOfferPropertyDirectoryName($offerId, $offerIblockId, rbGetAdditionalColorPropertyId())
    );

    if ($productName === '') {
      return '';
    }

    $colors = array_filter([$color, $additionalColor], static function (string $colorName): bool {
      return $colorName !== '';
    });

    if (empty($colors)) {
      return $productName;
    }

    return $productName . ' цвет ' . implode('/', $colors);
  }
}

if (!function_exists('rbGetTradeOfferNameBuildError')) {
  /**
   * Возвращает причину, почему название ТП не удалось собрать.
   */
  function rbGetTradeOfferNameBuildError(int $offerId): string
  {
    if ($offerId <= 0) {
      return 'Invalid offer ID';
    }

    if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
      return 'Required modules iblock/catalog are not available';
    }

    $offerIblockId = (int)CIBlockElement::GetIBlockByID($offerId);
    if ($offerIblockId <= 0) {
      return 'Offer iblock is not found';
    }

    $productId = rbGetOfferParentProductId($offerId, $offerIblockId);
    if ($productId <= 0) {
      return 'Parent product is not found';
    }

    $product = CIBlockElement::GetList(
      [],
      ['ID' => $productId],
      false,
      false,
      ['ID', 'NAME']
    )->Fetch();

    if (!$product) {
      return 'Parent product element is not found';
    }

    if (trim((string)($product['NAME'] ?? '')) === '') {
      return 'Parent product name is empty';
    }

    return 'Can not build offer name';
  }
}

if (!function_exists('rbRewriteTradeOfferName')) {
  /**
   * Перезаписывает название одного торгового предложения.
   */
  function rbRewriteTradeOfferName(int $offerId, bool $dryRun = false): array
  {
    $result = [
      'ID' => $offerId,
      'OLD_NAME' => '',
      'NEW_NAME' => '',
      'UPDATED' => false,
      'SKIPPED' => false,
      'ERROR' => '',
    ];

    if ($offerId <= 0 || !Loader::includeModule('iblock')) {
      $result['SKIPPED'] = true;
      $result['ERROR'] = 'Invalid offer ID or iblock module is not available';
      return $result;
    }

    $offer = CIBlockElement::GetList([], ['ID' => $offerId], false, false, ['ID', 'NAME'])->Fetch();
    if (!$offer) {
      $result['SKIPPED'] = true;
      $result['ERROR'] = 'Offer not found';
      return $result;
    }

    $result['OLD_NAME'] = (string)$offer['NAME'];
    $result['NEW_NAME'] = rbBuildTradeOfferName($offerId);

    if ($result['NEW_NAME'] === '') {
      $result['SKIPPED'] = true;
      $result['ERROR'] = rbGetTradeOfferNameBuildError($offerId);
      return $result;
    }

    if ($result['OLD_NAME'] === $result['NEW_NAME'] || $dryRun) {
      $result['SKIPPED'] = true;
      return $result;
    }

    $element = new CIBlockElement();
    if (!$element->Update($offerId, ['NAME' => $result['NEW_NAME']])) {
      $result['ERROR'] = (string)$element->LAST_ERROR;
      return $result;
    }

    $result['UPDATED'] = true;
    return $result;
  }
}

if (!function_exists('rbRewriteTradeOfferNames')) {
  /**
   * Массово перезаписывает названия ТП.
   *
   * Пример:
   * rbRewriteTradeOfferNames([
   *   'OFFER_IBLOCK_ID' => 3,
   *   'LIMIT' => 100,
   *   'DRY_RUN' => true,
   * ]);
   */
  function rbRewriteTradeOfferNames(array $params = []): array
  {
    $result = [
      'TOTAL' => 0,
      'UPDATED' => 0,
      'SKIPPED' => 0,
      'ERRORS' => [],
      'ITEMS' => [],
    ];

    if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
      $result['ERRORS'][] = 'Required modules iblock/catalog are not available';
      return $result;
    }

    $offerIblockId = (int)($params['OFFER_IBLOCK_ID'] ?? RB_OFFER_IBLOCK_ID);
    $offerIblockIds = [];
    $limit = (int)($params['LIMIT'] ?? 0);
    $dryRun = (bool)($params['DRY_RUN'] ?? false);
    $filter = is_array($params['FILTER'] ?? null) ? $params['FILTER'] : [];

    if ($offerIblockId > 0) {
      $offerIblockIds[] = $offerIblockId;
    } else {
      $catalogs = CCatalog::GetList(
        [],
        ['>PRODUCT_IBLOCK_ID' => 0],
        false,
        false,
        ['IBLOCK_ID', 'PRODUCT_IBLOCK_ID']
      );

      while ($catalog = $catalogs->Fetch()) {
        $detectedOfferIblockId = (int)($catalog['IBLOCK_ID'] ?? 0);
        if ($detectedOfferIblockId > 0) {
          $offerIblockIds[] = $detectedOfferIblockId;
        }
      }
    }

    $offerIblockIds = array_values(array_unique($offerIblockIds));
    if (empty($offerIblockIds)) {
      $result['ERRORS'][] = 'Offer iblock is not defined';
      return $result;
    }

    $filter['IBLOCK_ID'] = count($offerIblockIds) === 1 ? $offerIblockIds[0] : $offerIblockIds;

    $nav = $limit > 0 ? ['nTopCount' => $limit] : false;
    $offers = CIBlockElement::GetList(
      ['ID' => 'ASC'],
      $filter,
      false,
      $nav,
      ['ID', 'IBLOCK_ID', 'NAME']
    );

    while ($offer = $offers->Fetch()) {
      $itemResult = rbRewriteTradeOfferName((int)$offer['ID'], $dryRun);

      $result['TOTAL']++;
      $result['ITEMS'][] = $itemResult;

      if ($itemResult['UPDATED']) {
        $result['UPDATED']++;
      }

      if ($itemResult['SKIPPED']) {
        $result['SKIPPED']++;
      }

      if ($itemResult['ERROR'] !== '') {
        $result['ERRORS'][$itemResult['ID']] = $itemResult['ERROR'];
      }
    }

    return $result;
  }
}
