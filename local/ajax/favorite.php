<?php

use Bitrix\Catalog\Product\Basket as CatalogBasket;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Sale;

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$response = [
  'success' => false,
  'message' => '',
  'isFavorite' => false,
  'count' => 0,
  'ids' => [],
];

try {
  $request = Application::getInstance()->getContext()->getRequest();

  if ($request->get('action') === 'status') {
    $ids = function_exists('riseBagsGetFavoriteProductIds') ? riseBagsGetFavoriteProductIds() : [];

    $response['success'] = true;
    $response['ids'] = $ids;
    $response['count'] = count($ids);

    header('Content-Type: application/json; charset=UTF-8');
    echo Json::encode($response);

    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    exit;
  }

  if (!check_bitrix_sessid()) {
    throw new RuntimeException('Ошибка проверки сессии');
  }

  $productId = (int)$request->getPost('product_id');
  if ($productId <= 0) {
    throw new RuntimeException('Не передан товар');
  }

  if (!Loader::includeModule('sale') || !Loader::includeModule('catalog')) {
    throw new RuntimeException('Не подключены модули sale/catalog');
  }

  $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), SITE_ID);
  $favoriteItems = [];
  $favoriteCount = 0;

  foreach ($basket as $basketItem) {
    if ($basketItem->getField('DELAY') !== 'Y') {
      continue;
    }

    $favoriteCount++;

    if ((int)$basketItem->getProductId() === $productId) {
      $favoriteItems[] = $basketItem;
    }
  }

  if (!empty($favoriteItems)) {
    foreach ($favoriteItems as $favoriteItem) {
      $deleteResult = $favoriteItem->delete();
      if (!$deleteResult->isSuccess()) {
        throw new RuntimeException(implode('; ', $deleteResult->getErrorMessages()));
      }
    }

    $saveResult = $basket->save();
    if (!$saveResult->isSuccess()) {
      throw new RuntimeException(implode('; ', $saveResult->getErrorMessages()));
    }

    $favoriteCount -= count($favoriteItems);
    $response['isFavorite'] = false;
  } else {
    $addResult = CatalogBasket::addProduct([
      'PRODUCT_ID' => $productId,
      'QUANTITY' => 1,
      'DELAY' => 'Y',
      'PROPS' => [
        [
          'NAME' => 'Избранное',
          'CODE' => 'FAVORITE',
          'VALUE' => 'Y',
        ],
      ],
    ]);

    if (!$addResult->isSuccess()) {
      throw new RuntimeException(implode('; ', $addResult->getErrorMessages()));
    }

    $favoriteCount++;
    $response['isFavorite'] = true;
  }

  $response['success'] = true;
  $response['productId'] = $productId;
  $response['count'] = max(0, $favoriteCount);
  $response['ids'] = function_exists('riseBagsGetFavoriteProductIds') ? riseBagsGetFavoriteProductIds() : [];
} catch (Throwable $exception) {
  $response['message'] = $exception->getMessage();
}

header('Content-Type: application/json; charset=UTF-8');
echo Json::encode($response);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
