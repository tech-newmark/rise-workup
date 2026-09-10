<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

// ============================================================================
// 1. НАСТРОЙКИ И ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ============================================================================

$showSlider = $arParams['SHOW_SLIDER'];           // Включен ли слайдер
$displayMode = $arParams['PRODUCT_DISPLAY_MODE']; // Режим отображения ТП (N - простой, Y - расширенный)

/**
 * Удаляет дубликаты картинок по ID
 * 
 * @param array $images Массив картинок
 * @return array Массив без дубликатов
 */
function uniqueImagesById($images)
{
  if (empty($images) || !is_array($images)) {
    return [];
  }

  $seenIds = [];
  $result = [];

  foreach ($images as $image) {
    if (!in_array($image['ID'], $seenIds)) {
      $seenIds[] = $image['ID'];
      $result[] = $image;
    }
  }

  return $result;
}

/**
 * Получает массив картинок для товара
 * 
 * @param array $item Данные товара
 * @param string $propCode Код свойства с дополнительными картинками
 * @return array Массив картинок для слайдера
 */
function getProductSlider($item, $propCode)
{
  $slider = [];

  // 1. Добавляем PREVIEW_PICTURE (первая картинка)
  if (!empty($item['PREVIEW_PICTURE'])) {
    $slider[] = $item['PREVIEW_PICTURE'];
  }

  // 2. Добавляем дополнительные картинки из свойства MORE_PHOTO
  if (!empty($propCode) && !empty($item['PROPERTIES'][$propCode]['VALUE'])) {
    foreach ($item['PROPERTIES'][$propCode]['VALUE'] as $imageId) {
      $image = CFile::GetFileArray($imageId);
      if (!empty($image)) {
        $slider[] = $image;
      }
    }
  }

  return $slider;
}

/**
 * Получает массив картинок для торгового предложения
 * 
 * @param array $offer Данные ТП
 * @param string $propCode Код свойства с дополнительными картинками
 * @param array $parentItem Данные родительского товара (для fallback)
 * @return array Массив картинок для слайдера
 */
function getOfferSlider($offer, $propCode, $parentItem = [])
{
  $slider = [];

  // 1. PREVIEW_PICTURE: своя или родительская
  $previewPicture = !empty($offer['PREVIEW_PICTURE'])
    ? $offer['PREVIEW_PICTURE']
    : (!empty($parentItem['PREVIEW_PICTURE']) ? $parentItem['PREVIEW_PICTURE'] : null);

  if (!empty($previewPicture)) {
    $slider[] = $previewPicture;
  }

  // 2. Дополнительные картинки из свойства ТП
  if (!empty($propCode) && !empty($offer['PROPERTIES'][$propCode]['VALUE'])) {
    foreach ($offer['PROPERTIES'][$propCode]['VALUE'] as $imageId) {
      $image = CFile::GetFileArray($imageId);
      if (!empty($image)) {
        $slider[] = $image;
      }
    }
  }

  return $slider;
}

// ============================================================================
// 2. ОСНОВНОЙ ЦИКЛ ОБРАБОТКИ ТОВАРОВ
// ============================================================================

foreach ($arResult['ITEMS'] as &$item) {

  // ========================================================================
  // 2.1 ОБЫЧНЫЙ ТОВАР (без торговых предложений)
  // ========================================================================
  if (empty($item['OFFERS'])) {
    // Получаем слайдер для товара
    $slider = getProductSlider($item, $arParams['ADD_PICT_PROP']);
    // Удаляем дубликаты
    $slider = uniqueImagesById($slider);
    // Сохраняем результат
    $item['MORE_PHOTO'] = $slider;
    $item['MORE_PHOTO_COUNT'] = count($slider);
    $item['SHOW_SLIDER'] = ($showSlider === 'Y' && count($slider) > 1);

    continue; // Переходим к следующему товару
  }

  // ========================================================================
  // 2.2 ТОВАР С ТОРГОВЫМИ ПРЕДЛОЖЕНИЯМИ
  // ========================================================================

  // 2.2.1 Простой режим (PRODUCT_DISPLAY_MODE = N) - используем картинки товара
  if ($displayMode === 'N') {
    $slider = getProductSlider($item, $arParams['ADD_PICT_PROP']);
    $slider = uniqueImagesById($slider);

    $item['MORE_PHOTO'] = $slider;
    $item['MORE_PHOTO_COUNT'] = count($slider);
    $item['SHOW_SLIDER'] = ($showSlider === 'Y' && count($slider) > 1);

    // Для всех ТП в простом режиме тоже используем картинки товара
    foreach ($item['OFFERS'] as $offerIndex => $offer) {
      $item['OFFERS'][$offerIndex]['MORE_PHOTO'] = $slider;
      $item['OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = count($slider);

      if (!empty($item['JS_OFFERS'][$offerIndex])) {
        $item['JS_OFFERS'][$offerIndex]['MORE_PHOTO'] = $slider;
        $item['JS_OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = count($slider);
      }
    }

    continue; // Переходим к следующему товару
  }

  // ========================================================================
  // 2.2.2 Расширенный режим (PRODUCT_DISPLAY_MODE = Y) - используем картинки ТП
  // ========================================================================

  // Обрабатываем каждое торговое предложение
  foreach ($item['OFFERS'] as $offerIndex => $offer) {
    // Получаем слайдер для текущего ТП
    $offerSlider = getOfferSlider($offer, $arParams['OFFER_ADD_PICT_PROP'], $item);

    // Удаляем дубликаты
    $offerSlider = uniqueImagesById($offerSlider);

    // Сохраняем в структуру ТП
    $item['OFFERS'][$offerIndex]['MORE_PHOTO'] = $offerSlider;
    $item['OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = count($offerSlider);

    // Сохраняем в JS_OFFERS (для динамических слайдеров)
    if (!empty($item['JS_OFFERS'][$offerIndex])) {
      $item['JS_OFFERS'][$offerIndex]['MORE_PHOTO'] = $offerSlider;
      $item['JS_OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = count($offerSlider);
    }

    // Очищаем ненужное поле (legacy)
    $item['OFFERS'][$offerIndex]['PREVIEW_PICTURE_SECOND'] = [];
  }

  // Для основного слайдера товара берем слайдер выбранного ТП
  $selectedOfferIndex = $item['OFFERS_SELECTED'] ?? 0;
  $selectedOffer = $item['OFFERS'][$selectedOfferIndex] ?? reset($item['OFFERS']);

  if (!empty($selectedOffer['MORE_PHOTO'])) {
    $item['MORE_PHOTO'] = $selectedOffer['MORE_PHOTO'];
    $item['MORE_PHOTO_COUNT'] = count($selectedOffer['MORE_PHOTO']);
    $item['SHOW_SLIDER'] = ($showSlider === 'Y' && $item['MORE_PHOTO_COUNT'] > 1);
  }
}

// Очищаем ссылку на последний элемент
unset($item);
