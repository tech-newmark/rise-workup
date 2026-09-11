<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var CBitrixComponentTemplate $this */
$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

// Local scope allows several uncached instances of this template in one request.
(static function (array &$items, array $params) {
    $files = [];
    $showSlider = ($params['SHOW_SLIDER'] ?? 'N') === 'Y';
    $simpleMode = ($params['PRODUCT_DISPLAY_MODE'] ?? 'N') === 'N';

    $getImage = static function ($value) use (&$files) {
        if (is_array($value)) {
            if (!empty($value['ID'])) {
                $files[(int)$value['ID']] = $value;
            }
            return $value;
        }
        $id = (int)$value;
        if ($id <= 0) {
            return [];
        }
        if (!array_key_exists($id, $files)) {
            $files[$id] = CFile::GetFileArray($id) ?: [];
        }
        return $files[$id];
    };

    $getSlider = static function (array $item, $propertyCode, $fallback = null) use ($getImage) {
        $images = [];
        $preview = $item['PREVIEW_PICTURE'] ?? null;
        $values = [$preview ?: $fallback];
        if ($propertyCode !== '') {
            $values = array_merge($values, (array)($item['PROPERTIES'][$propertyCode]['VALUE'] ?? []));
        }
        foreach ($values as $value) {
            $image = $getImage($value);
            if (!empty($image['ID'])) {
                $id = (int)$image['ID'];
                // Keep the first occurrence, including the preview image.
                if (!isset($images[$id])) {
                    $images[$id] = $image;
                }
            }
        }
        return array_values($images);
    };

    $setSlider = static function (array &$target, array $images) use ($showSlider) {
        $target['MORE_PHOTO'] = $images;
        $target['MORE_PHOTO_COUNT'] = count($images);
        $target['SHOW_SLIDER'] = $showSlider && count($images) > 1;
    };

    foreach ($items as &$item) {
        $productSlider = ($simpleMode || empty($item['OFFERS']))
            ? $getSlider($item, $params['ADD_PICT_PROP'] ?? '')
            : [];
        if (empty($item['OFFERS'])) {
            $setSlider($item, $productSlider);
            continue;
        }

        // JS_OFFERS can have a different order from OFFERS; match by product ID.
        $jsOfferIndexes = [];
        foreach ($item['JS_OFFERS'] ?? [] as $index => $jsOffer) {
            if (!empty($jsOffer['ID'])) {
                $jsOfferIndexes[(int)$jsOffer['ID']] = $index;
            }
        }
        foreach ($item['OFFERS'] as &$offer) {
            $images = $simpleMode
                ? $productSlider
                : $getSlider($offer, $params['OFFER_ADD_PICT_PROP'] ?? '', $item['PREVIEW_PICTURE'] ?? null);
            $setSlider($offer, $images);
            if (!$simpleMode) {
                $offer['PREVIEW_PICTURE_SECOND'] = [];
            }
            $jsIndex = $jsOfferIndexes[(int)$offer['ID']] ?? null;
            if ($jsIndex !== null) {
                $setSlider($item['JS_OFFERS'][$jsIndex], $images);
            }
        }
        unset($offer);

        $selectedOffer = $item['OFFERS'][$item['OFFERS_SELECTED'] ?? 0] ?? reset($item['OFFERS']);
        $setSlider($item, $simpleMode ? $productSlider : ($selectedOffer['MORE_PHOTO'] ?? []));
    }
    unset($item);
})($arResult['ITEMS'], $arParams);
