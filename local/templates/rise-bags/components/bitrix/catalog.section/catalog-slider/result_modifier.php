<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$uniqueImagesById = static function (array $images): array {
	$result = [];

	foreach ($images as $image) {
		$imageId = (int)($image['ID'] ?? 0);

		if ($imageId > 0 && !isset($result[$imageId])) {
			$result[$imageId] = $image;
		}
	}

	return array_values($result);
};

$getImagesFromProperty = static function (array $properties, string $propertyCode): array {
	$images = [];
	$imageIds = $properties[$propertyCode]['VALUE'] ?? [];

	if (!is_array($imageIds)) {
		$imageIds = [$imageIds];
	}

	foreach ($imageIds as $imageId) {
		$image = CFile::GetFileArray($imageId);

		if ($image) {
			$images[] = $image;
		}
	}

	return $images;
};

$getProductImages = static function (array $item, string $propertyCode) use ($getImagesFromProperty): array {
	$images = [];

	if (!empty($item['PREVIEW_PICTURE'])) {
		$images[] = $item['PREVIEW_PICTURE'];
	}

	return array_merge(
		$images,
		$getImagesFromProperty($item['PROPERTIES'] ?? [], $propertyCode)
	);
};

$getOfferImages = static function (array $offer, string $propertyCode, array $product) use ($getImagesFromProperty): array {
	$images = [];
	$previewPicture = !empty($offer['PREVIEW_PICTURE'])
		? $offer['PREVIEW_PICTURE']
		: ($product['PREVIEW_PICTURE'] ?? null);

	if ($previewPicture) {
		$images[] = $previewPicture;
	}

	return array_merge(
		$images,
		$getImagesFromProperty($offer['PROPERTIES'] ?? [], $propertyCode)
	);
};

$setProductImages = static function (array &$item, array $images) use ($uniqueImagesById, $arParams): void {
	$images = $uniqueImagesById($images);
	$item['MORE_PHOTO'] = $images;
	$item['MORE_PHOTO_COUNT'] = count($images);
	$item['SHOW_SLIDER'] = $arParams['SHOW_SLIDER'] === 'Y' && count($images) > 1;
};

foreach ($arResult['ITEMS'] as &$item) {
	if (empty($item['OFFERS']) || $arParams['PRODUCT_DISPLAY_MODE'] === 'N') {
		$productImages = $getProductImages($item, (string)$arParams['ADD_PICT_PROP']);
		$setProductImages($item, $productImages);

		foreach ($item['OFFERS'] ?? [] as $offerIndex => $offer) {
			$item['OFFERS'][$offerIndex]['MORE_PHOTO'] = $item['MORE_PHOTO'];
			$item['OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = $item['MORE_PHOTO_COUNT'];

			if (isset($item['JS_OFFERS'][$offerIndex])) {
				$item['JS_OFFERS'][$offerIndex]['MORE_PHOTO'] = $item['MORE_PHOTO'];
				$item['JS_OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = $item['MORE_PHOTO_COUNT'];
			}
		}

		continue;
	}

	foreach ($item['OFFERS'] as $offerIndex => $offer) {
		$offerImages = $uniqueImagesById(
			$getOfferImages($offer, (string)$arParams['OFFER_ADD_PICT_PROP'], $item)
		);

		$item['OFFERS'][$offerIndex]['MORE_PHOTO'] = $offerImages;
		$item['OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = count($offerImages);

		if (isset($item['JS_OFFERS'][$offerIndex])) {
			$item['JS_OFFERS'][$offerIndex]['MORE_PHOTO'] = $offerImages;
			$item['JS_OFFERS'][$offerIndex]['MORE_PHOTO_COUNT'] = count($offerImages);
		}
	}

	$selectedOfferIndex = (int)($item['OFFERS_SELECTED'] ?? 0);
	$selectedOffer = $item['OFFERS'][$selectedOfferIndex] ?? reset($item['OFFERS']);
	$setProductImages($item, is_array($selectedOffer) ? ($selectedOffer['MORE_PHOTO'] ?? []) : []);
}
unset($item);
