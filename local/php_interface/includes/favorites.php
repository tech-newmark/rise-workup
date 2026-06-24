<?php

use Bitrix\Main\Loader;
use Bitrix\Sale;

if (!function_exists('riseBagsGetFavoriteBasketItems')) {
  function riseBagsGetFavoriteBasketItems(): array
  {
    if (!Loader::includeModule('sale')) {
      return [];
    }

    $items = [];
    $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), SITE_ID);

    foreach ($basket as $basketItem) {
      if ($basketItem->getField('DELAY') === 'Y') {
        $items[] = $basketItem;
      }
    }

    return $items;
  }
}

if (!function_exists('riseBagsGetFavoriteProductIds')) {
  function riseBagsGetFavoriteProductIds(): array
  {
    $favoriteProductIds = [];

    foreach (riseBagsGetFavoriteBasketItems() as $basketItem) {
      $favoriteProductIds[] = (int)$basketItem->getProductId();
    }

    $favoriteProductIds = array_values(array_unique(array_filter($favoriteProductIds)));

    return $favoriteProductIds;
  }
}

if (!function_exists('riseBagsIsFavoriteProduct')) {
  function riseBagsIsFavoriteProduct(int $productId): bool
  {
    return in_array($productId, riseBagsGetFavoriteProductIds(), true);
  }
}

if (!function_exists('riseBagsGetFavoriteItemsViewData')) {
  function riseBagsGetFavoriteItemsViewData(): array
  {
    if (!Loader::includeModule('iblock')) {
      return [];
    }

    Loader::includeModule('currency');

    $result = [];

    foreach (riseBagsGetFavoriteBasketItems() as $basketItem) {
      $productId = (int)$basketItem->getProductId();
      $element = CIBlockElement::GetList(
        [],
        ['ID' => $productId],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
      )->GetNext();

      $pictureId = (int)($element['PREVIEW_PICTURE'] ?: $element['DETAIL_PICTURE']);
      $result[] = [
        'BASKET_ID' => (int)$basketItem->getId(),
        'PRODUCT_ID' => $productId,
        'NAME' => $basketItem->getField('NAME') ?: ($element['NAME'] ?? ''),
        'DETAIL_PAGE_URL' => $basketItem->getField('DETAIL_PAGE_URL') ?: ($element['DETAIL_PAGE_URL'] ?? ''),
        'PICTURE_SRC' => $pictureId > 0 ? CFile::GetPath($pictureId) : '',
        'PRICE' => CCurrencyLang::CurrencyFormat(
          (float)$basketItem->getPrice(),
          $basketItem->getCurrency(),
          true
        ),
      ];
    }

    return $result;
  }
}
