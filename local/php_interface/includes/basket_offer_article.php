<?php

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Sale\BasketItemBase;

if (!function_exists('riseSyncBasketOfferArticle')) {
  function riseSyncBasketOfferArticle(Event $event): void
  {
    /** @var BasketItemBase|null $basketItem */
    $basketItem = $event->getParameter('ENTITY');
    $isNew = (bool)$event->getParameter('IS_NEW');
    $oldValues = (array)$event->getParameter('VALUES');

    if ($isNew || !($basketItem instanceof BasketItemBase)) {
      return;
    }

    if ((string)$basketItem->getField('MODULE') !== 'catalog') {
      return;
    }

    $basket = $basketItem->getBasket();

    // Не изменяем товары уже созданного заказа.
    if ($basket->getOrder()) {
      return;
    }

    $oldProductId = (int)($oldValues['PRODUCT_ID'] ?? 0);
    $newProductId = (int)$basketItem->getProductId();

    // Обрабатываем только фактическую смену товара/SKU.
    if (
      $oldProductId <= 0
      || $newProductId <= 0
      || $oldProductId === $newProductId
    ) {
      return;
    }

    if (!Loader::includeModule('iblock')) {
      return;
    }

    $element = CIBlockElement::GetByID($newProductId)->Fetch();

    if (!$element) {
      return;
    }

    $articleData = CIBlockElement::GetProperty(
      (int)$element['IBLOCK_ID'],
      $newProductId,
      [
        'sort' => 'asc',
        'id' => 'asc',
      ],
      [
        'CODE' => 'ARTNUMBER',
      ]
    )->Fetch();

    $articleValue = '';

    if (
      $articleData
      && isset($articleData['VALUE'])
      && is_scalar($articleData['VALUE'])
    ) {
      $articleValue = trim((string)$articleData['VALUE']);
    }

    $propertyCollection = $basketItem->getPropertyCollection();

    // VALUE обязателен для поиска, но сам поиск выполняется по CODE.
    $articleProperty = $propertyCollection->getPropertyItemByValue([
      'CODE' => 'ARTNUMBER',
      'VALUE' => '',
    ]);

    // У нового предложения нет артикула — удаляем старое значение.
    if ($articleValue === '') {
      if ($articleProperty) {
        $articleProperty->delete();
      }

      return;
    }

    if (!$articleProperty) {
      $articleProperty = $propertyCollection->createItem();
    }

    $articleProperty->setFields([
      'NAME' => (string)($articleData['NAME'] ?? 'Артикул'),
      'CODE' => 'ARTNUMBER',
      'VALUE' => $articleValue,
      'SORT' => (int)($articleData['SORT'] ?? 100),
    ]);
  }
}

EventManager::getInstance()->addEventHandler(
  'sale',
  'OnSaleBasketItemBeforeSaved',
  'riseSyncBasketOfferArticle'
);
