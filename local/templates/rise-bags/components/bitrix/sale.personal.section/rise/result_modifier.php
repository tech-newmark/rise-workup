<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$availablePages = array();

if ($arParams['SHOW_ORDER_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arResult['PATH_TO_ORDERS'],
    "name" => Loc::getMessage("SPS_ORDER_PAGE_NAME"),
    "icon" => 'personal-section-offer-icon'
  );
}

if ($arParams['SHOW_ACCOUNT_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arResult['PATH_TO_ACCOUNT'],
    "name" => Loc::getMessage("SPS_ACCOUNT_PAGE_NAME"),
    "icon" => 'personal-section-purse-icon'
  );
}

if ($arParams['SHOW_PRIVATE_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arResult['PATH_TO_PRIVATE'],
    "name" => Loc::getMessage("SPS_PERSONAL_PAGE_NAME"),
    "icon" => 'icon-user'
  );
}

// if ($arParams['SHOW_ORDER_PAGE'] === 'Y') {

//   $delimeter = ($arParams['SEF_MODE'] === 'Y') ? "?" : "&";
//   $availablePages[] = array(
//     "path" => $arResult['PATH_TO_ORDERS'] . $delimeter . "filter_history=Y",
//     "name" => Loc::getMessage("SPS_ORDER_PAGE_HISTORY"),
//     "icon" => 'personal-section-history-icon'
//   );
// }

if ($arParams['SHOW_PROFILE_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arResult['PATH_TO_PROFILE'],
    "name" => Loc::getMessage("SPS_PROFILE_PAGE_NAME"),
    "icon" => 'personal-section-profile-icon'
  );
}

if ($arParams['SHOW_BASKET_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arParams['PATH_TO_BASKET'],
    "name" => Loc::getMessage("SPS_BASKET_PAGE_NAME"),
    "icon" => 'icon-cart'
  );
}

$availablePages[] = array(
  "path" => "/personal/favourite/",
  "name" => "Избранные товары",
  "icon" => 'icon-heart'
);

if ($arParams['SHOW_SUBSCRIBE_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arResult['PATH_TO_SUBSCRIBE'],
    "name" => Loc::getMessage("SPS_SUBSCRIBE_PAGE_NAME"),
    "icon" => 'personal-section-envelope-icon'
  );
}

if ($arParams['SHOW_CONTACT_PAGE'] === 'Y') {
  $availablePages[] = array(
    "path" => $arParams['PATH_TO_CONTACT'],
    "name" => Loc::getMessage("SPS_CONTACT_PAGE_NAME"),
    "icon" => 'personal-section-phone-icon'
  );
}

if (!empty($arParams['~CUSTOM_PAGES'])) {
  $customPagesList = CUtil::JsObjectToPhp($arParams['~CUSTOM_PAGES']);
  if (!empty($customPagesList) && is_array($customPagesList)) {
    foreach ($customPagesList as $page) {
      $icon = (string)($page[2] ?? '');
      $availablePages[] = [
        'path' => $page[0],
        'name' => $page[1],
        'icon' => $icon !== '' ? 'icon-user' : ''
      ];
      unset($icon);
    }
  }
  unset($customPagesList);
}

$arResult["AVAILABLE_PAGES"] = $availablePages;
