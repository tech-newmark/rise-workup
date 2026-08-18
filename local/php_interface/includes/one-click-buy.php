<?php

use Bitrix\Main\Loader;

AddEventHandler('form', 'onAfterResultAdd', function ($WEB_FORM_ID, $RESULT_ID) {

  $formId = (int)$WEB_FORM_ID;
  $resultId = (int)$RESULT_ID;

  if ($formId !== 6 || $resultId <= 0) {
    return;
  }

  if (
    !Loader::includeModule('form')
    || !Loader::includeModule('iblock')
    || !Loader::includeModule('catalog')
    || !Loader::includeModule('highloadblock')
  ) {
    return;
  }

  $answers = [];

  if (!CFormResult::GetDataByID(
    $resultId,
    [],
    $arResult,
    $answers
  )) {
    return;
  }

  $elementId = 0;

  if (!empty($answers['OFFER_ID'])) {
    $elementAnswer = reset($answers['OFFER_ID']);
    $elementId = (int)$elementAnswer['USER_TEXT'];
  }

  if ($elementId <= 0) {
    return;
  }

  $productContextUrl = trim((string)(
    $_POST['product_context_url'] ?? ''
  ));

  $getActiveElement = static function (int $elementId, array $select): ?array {
    $element = CIBlockElement::GetList(
      [],
      [
        '=ID' => $elementId,
        '=ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
      ],
      false,
      false,
      $select
    )->GetNext();

    return is_array($element) ? $element : null;
  };

  $selectedElement = $getActiveElement(
    $elementId,
    ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'DETAIL_PAGE_URL']
  );

  if (!$selectedElement) {
    return;
  }

  $productInfo = CCatalogSKU::GetProductInfo($elementId);
  $isOffer = is_array($productInfo) && (int)($productInfo['ID'] ?? 0) > 0;

  $productElement = $isOffer
    ? $getActiveElement(
      (int)$productInfo['ID'],
      ['ID', 'CODE', 'DETAIL_PAGE_URL']
    )
    : $selectedElement;

  if (!$productElement) {
    return;
  }

  $productContextParts = parse_url($productContextUrl);
  $productContextPath = is_array($productContextParts)
    ? (string)($productContextParts['path'] ?? '')
    : '';

  $isValidProductContextUrl =
    $productContextUrl !== ''
    && !isset($productContextParts['scheme'])
    && !isset($productContextParts['host'])
    && str_starts_with($productContextPath, '/catalog/')
    && basename(trim($productContextPath, '/')) === (string)$productElement['CODE'];

  if (!$isValidProductContextUrl) {
    $productContextPath = '';
  }

  $productUrl = $productContextPath !== ''
    ? rtrim($productContextPath, '/') . '/'
    : trim((string)$productElement['DETAIL_PAGE_URL']);

  if ($isOffer) {
    $productUrl .= (
      str_contains($productUrl, '?')
      ? '&'
      : '?'
    ) . 'offer=' . $elementId;
  }

  $serverName = trim((string)SITE_SERVER_NAME);

  if ($serverName !== '') {
    $productUrl = 'https://' . $serverName . $productUrl;
  }

  $priceData = CCatalogProduct::GetOptimalPrice($elementId, 1, [], 'N');

  $resultPrice = $priceData['RESULT_PRICE'] ?? [];

  $productPrice = (float)(
    $resultPrice['DISCOUNT_PRICE']
    ?? $resultPrice['BASE_PRICE']
    ?? 0
  );

  $productTitle = trim((string)$selectedElement['NAME']);

  $getElementProperty = static function (string $propertyCode) use (
    $selectedElement,
    $elementId
  ): array {
    return CIBlockElement::GetProperty(
      (int)$selectedElement['IBLOCK_ID'],
      $elementId,
      [],
      ['CODE' => $propertyCode]
    )->Fetch() ?: [];
  };

  $productArticle = trim((string)(
    $getElementProperty('ARTNUMBER')['VALUE'] ?? ''
  ));

  $colorProperty = $getElementProperty('COLOR_REF');

  $colorData = $colorProperty
    ? CIBlockPropertyDirectory::GetExtendedValue(
      $colorProperty,
      ['VALUE' => $colorProperty['VALUE']]
    )
    : false;

  $productColor = trim((string)(
    $colorData['VALUE']
    ?? $colorProperty['VALUE']
    ?? ''
  ));

  $setFormResultField = static function (
    int $resultId,
    int $formId,
    string $fieldSid,
    string $value
  ): void {
    $field = CFormField::GetBySID($fieldSid, $formId)->Fetch();

    if (!$field) {
      return;
    }

    $answer = CFormAnswer::GetList(
      (int)$field['ID'],
      's_sort',
      'asc',
      ['ACTIVE' => 'Y']
    )->Fetch();

    if (!$answer) {
      return;
    }

    CFormResult::SetField(
      $resultId,
      $fieldSid,
      [
        (int)$answer['ID'] => $value,
      ]
    );
  };

  $formResultFields = [
    'PRODUCT_TITLE' => $productTitle,
    'PRODUCT_PRICE' => (string)$productPrice,
    'PRODUCT_ARTNUMBER' => $productArticle,
    'PRODUCT_URL' => $productUrl,
    'PRODUCT_COLOR' => $productColor,
  ];

  foreach ($formResultFields as $fieldSid => $value) {
    $setFormResultField(
      $resultId,
      $formId,
      $fieldSid,
      $value
    );
  }
});
