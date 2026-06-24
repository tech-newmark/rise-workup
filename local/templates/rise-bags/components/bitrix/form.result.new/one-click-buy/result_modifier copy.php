<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if ($arParams["OFFER_ID"] > 0) {
  \Bitrix\Main\Loader::includeModule('iblock');
  \Bitrix\Main\Loader::includeModule('catalog');

  $offerId = (int)$arParams["OFFER_ID"];
  $arParams["PRODUCT_DATA"] = [];

  $element = CIBlockElement::GetList(
    [],
    ['ID' => $offerId],
    false,
    false,
    [
      'ID',
      'NAME',
      'IBLOCK_ID',
      'PROPERTY_MORE_PHOTO',
      'PROPERTY_RAZMER'
    ]
  )->Fetch();

  if ($element) {
    // Название ТП
    $productTitle = $element['NAME'];
    // Размер ТП
    $productSize = $element["PROPERTY_RAZMER_VALUE"];
    // Цена ТП
    $price = CCatalogProduct::GetOptimalPrice($offerId, 1, [], 'N');
    $productPrice = 0;
    $productPriceFormatted = '';
    if ($price && $price['RESULT_PRICE']) {
      $productPrice = $price['RESULT_PRICE']['DISCOUNT_PRICE'] ?? $price['RESULT_PRICE']['BASE_PRICE'];
      $productPriceFormatted = CurrencyFormat(
        $productPrice,
        $price['RESULT_PRICE']['CURRENCY'] ?? 'RUB'
      );
    }
    // Изображение ТП
    $productImage = '';
    if (!empty($element['PROPERTY_MORE_PHOTO_VALUE'])) {
      if (is_array($element['PROPERTY_MORE_PHOTO_VALUE'])) {
        $firstPhoto = reset($element['PROPERTY_MORE_PHOTO_VALUE']);
        $productImage = CFile::GetPath($firstPhoto);
      } else {
        $productImage = CFile::GetPath($element['PROPERTY_MORE_PHOTO_VALUE']);
      }
    }
    // Если нет у ТП, ищем у родительского товара
    else {
      $parentInfo = CCatalogSKU::GetProductInfo($offerId);
      if ($parentInfo) {
        $parentElement = CIBlockElement::GetList(
          [],
          ['ID' => $parentInfo['ID']],
          false,
          false,
          ['ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE']
        )->Fetch();

        if ($parentElement) {
          if ($parentElement['DETAIL_PICTURE'] > 0) {
            $productImage = CFile::GetPath($parentElement['DETAIL_PICTURE']);
          } elseif ($parentElement['PREVIEW_PICTURE'] > 0) {
            $productImage = CFile::GetPath($parentElement['PREVIEW_PICTURE']);
          }
        }
      }
    }

    $arParams["PRODUCT_DATA"] = [
      "PRODUCT_ID" => $offerId,
      "PRODUCT_TITLE" => $productTitle,
      "PRODUCT_PRICE" => $productPrice,
      "PRODUCT_SIZE" => $productSize,
      "PRODUCT_PRICE_FORMATTED" => $productPriceFormatted,
      "PRODUCT_IMG" => $productImage,
    ];
  }
}
