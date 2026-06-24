<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;
use Bitrix\Iblock;

if (!Loader::includeModule('iblock'))
	return;

$boolCatalog = Loader::includeModule('catalog');
CBitrixComponent::includeComponentClass('bitrix:catalog.section');
CBitrixComponent::includeComponentClass('bitrix:catalog.top');
CBitrixComponent::includeComponentClass('bitrix:catalog.element');

$usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$arSKU = false;
$boolSKU = false;
if ($boolCatalog && $iblockExists) {
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

$defaultValue = array('-' => GetMessage('CP_BC_TPL_PROP_EMPTY'));

$arThemes = array();
if (ModuleManager::isModuleInstalled('bitrix.eshop')) {
	$arThemes['site'] = GetMessage('CPT_BC_TPL_THEME_SITE');
}

$arThemes['blue'] = GetMessage('CPT_BC_TPL_THEME_BLUE');
$arThemes['green'] = GetMessage('CPT_BC_TPL_THEME_GREEN');
$arThemes['red'] = GetMessage('CPT_BC_TPL_THEME_RED');
$arThemes['wood'] = GetMessage('CPT_BC_TPL_THEME_WOOD');
$arThemes['yellow'] = GetMessage('CPT_BC_TPL_THEME_YELLOW');
$arThemes['black'] = GetMessage('CP_BC_TPL_THEME_BLACK');

$documentRoot = Loader::getDocumentRoot();

$arTemplateParameters["INSTANT_RELOAD"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => GetMessage("CPT_BC_INSTANT_RELOAD"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"HIDDEN" => (!isset($arCurrentValues['USE_FILTER']) || 'N' == $arCurrentValues['USE_FILTER'] ? 'Y' : 'N')
);

$arTemplateParameters["FILTER_EXPANDED"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => "Показывать фильтр по умолчанию развернутым",
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"HIDDEN" => (!isset($arCurrentValues['USE_FILTER']) || 'N' == $arCurrentValues['USE_FILTER'] ? 'Y' : 'N')
);

$arTemplateParameters['SEARCH_PAGE_RESULT_COUNT'] = array(
	'PARENT' => 'SEARCH_SETTINGS',
	'NAME' => GetMessage("CP_BC_TPL_SEARCH_PAGE_RESULT_COUNT_MSGVER_1"),
	"TYPE" => "STRING",
	"DEFAULT" => "50",
);
$arTemplateParameters['SEARCH_RESTART'] = array(
	'PARENT' => 'SEARCH_SETTINGS',
	'NAME' => GetMessage("CP_BC_TPL_SEARCH_RESTART"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arTemplateParameters['SEARCH_NO_WORD_LOGIC'] = array(
	'PARENT' => 'SEARCH_SETTINGS',
	'NAME' => GetMessage("CP_BC_TPL_SEARCH_NO_WORD_LOGIC"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);
$arTemplateParameters['SEARCH_USE_LANGUAGE_GUESS'] = array(
	'PARENT' => 'SEARCH_SETTINGS',
	'NAME' => GetMessage("CP_BC_TPL_SEARCH_USE_LANGUAGE_GUESS"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);
$arTemplateParameters['SEARCH_CHECK_DATES'] = array(
	'PARENT' => 'SEARCH_SETTINGS',
	'NAME' => GetMessage("CP_BC_TPL_SEARCH_CHECK_DATES"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);
$arTemplateParameters['SEARCH_USE_SEARCH_RESULT_ORDER'] = array(
	'PARENT' => 'SEARCH_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_SEARCH_USE_SEARCH_RESULT_ORDER'),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

$arAllPropList = array();
$arListPropList = array();
$arHighloadPropList = array();
$arFilePropList = $defaultValue;

if ($iblockExists) {
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch()) {
		$strPropName = '[' . $arProp['ID'] . ']' . ('' != $arProp['CODE'] ? '[' . $arProp['CODE'] . ']' : '') . ' ' . $arProp['NAME'];
		if ('' == $arProp['CODE']) {
			$arProp['CODE'] = $arProp['ID'];
		}

		$arAllPropList[$arProp['CODE']] = $strPropName;

		if ('F' == $arProp['PROPERTY_TYPE']) {
			$arFilePropList[$arProp['CODE']] = $strPropName;
		}

		if ('L' == $arProp['PROPERTY_TYPE']) {
			$arListPropList[$arProp['CODE']] = $strPropName;
		}

		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp)) {
			$arHighloadPropList[$arProp['CODE']] = $strPropName;
		}
	}

	$showedProperties = [];
	if ($usePropertyFeatures) {
		if ($iblockExists) {
			$showedProperties = Iblock\Model\PropertyFeature::getListPageShowPropertyCodes(
				$arCurrentValues['IBLOCK_ID'],
				['CODE' => 'Y']
			);
			if ($showedProperties === null)
				$showedProperties = [];
		}
	} else {
		if (!empty($arCurrentValues['LIST_PROPERTY_CODE']) && is_array($arCurrentValues['LIST_PROPERTY_CODE'])) {
			$showedProperties = $arCurrentValues['LIST_PROPERTY_CODE'];
		}
	}
	if (!empty($showedProperties)) {
		$selected = array();

		foreach ($showedProperties as $code) {
			if (isset($arAllPropList[$code])) {
				$selected[$code] = $arAllPropList[$code];
			}
		}
	}
	unset($showedProperties);

	$arTemplateParameters['LINE_ELEMENT_COUNT'] = array(
		'HIDDEN' => 'Y',
	);

	$arTemplateParameters['TOP_LINE_ELEMENT_COUNT'] = array(
		'HIDDEN' => 'Y',
	);

	$arTemplateParameters['LIST_ENLARGE_PRODUCT'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_ENLARGE_PRODUCT'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'Y',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'STRICT' => GetMessage('CP_BC_TPL_ENLARGE_PRODUCT_STRICT'),
			'PROP' => GetMessage('CP_BC_TPL_ENLARGE_PRODUCT_PROP')
		)
	);

	if (isset($arCurrentValues['LIST_ENLARGE_PRODUCT']) && $arCurrentValues['LIST_ENLARGE_PRODUCT'] === 'PROP') {
		$arTemplateParameters['LIST_ENLARGE_PROP'] = array(
			'PARENT' => 'LIST_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_ENLARGE_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $defaultValue + $arListPropList
		);
	}
	$arTemplateParameters['LIST_SHOW_SLIDER'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_SLIDER'),
		'TYPE' => 'CHECKBOX',
		'MULTIPLE' => 'N',
		'REFRESH' => 'Y',
		'DEFAULT' => 'Y'
	);

	$arTemplateParameters['ADD_PICT_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_ADD_PICT_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arFilePropList
	);
	$arTemplateParameters['LABEL_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_LABEL_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'Y',
		'VALUES' => $arListPropList
	);

	if (!empty($arCurrentValues['LABEL_PROP'])) {
		if (!is_array($arCurrentValues['LABEL_PROP'])) {
			$arCurrentValues['LABEL_PROP'] = array($arCurrentValues['LABEL_PROP']);
		}

		$selected = array();
		foreach ($arCurrentValues['LABEL_PROP'] as $name) {
			if (isset($arListPropList[$name])) {
				$selected[$name] = $arListPropList[$name];
			}
		}
	}

	if ($boolSKU) {
		$arTemplateParameters['PRODUCT_DISPLAY_MODE'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_PRODUCT_DISPLAY_MODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'Y',
			'DEFAULT' => 'N',
			'VALUES' => array(
				'N' => GetMessage('CP_BC_TPL_DML_SIMPLE'),
				'Y' => GetMessage('CP_BC_TPL_DML_EXT')
			),
			'REFRESH' => 'Y'
		);
		$arAllOfferPropList = array();
		$arFileOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$arTreeOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch()) {
			if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
				continue;
			$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
			$strPropName = '[' . $arProp['ID'] . ']' . ('' != $arProp['CODE'] ? '[' . $arProp['CODE'] . ']' : '') . ' ' . $arProp['NAME'];
			if ('' == $arProp['CODE'])
				$arProp['CODE'] = $arProp['ID'];
			$arAllOfferPropList[$arProp['CODE']] = $strPropName;
			if ('F' == $arProp['PROPERTY_TYPE'])
				$arFileOfferPropList[$arProp['CODE']] = $strPropName;
			if ('N' != $arProp['MULTIPLE'])
				continue;
			if (
				'L' == $arProp['PROPERTY_TYPE']
				|| 'E' == $arProp['PROPERTY_TYPE']
				|| ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			)
				$arTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}

		if ($arCurrentValues['PRODUCT_DISPLAY_MODE'] === "Y") {
			$arTemplateParameters['OFFER_ADD_PICT_PROP'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_OFFER_ADD_PICT_PROP'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'N',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arFileOfferPropList
			);
		}

		if (!$usePropertyFeatures) {
			$arTemplateParameters['OFFER_TREE_PROPS'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_OFFER_TREE_PROPS'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'Y',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arTreeOfferPropList
			);
		}
	}

	$showedProperties = [];
	if ($usePropertyFeatures) {
		if ($iblockExists) {
			$showedProperties = Iblock\Model\PropertyFeature::getDetailPageShowProperties(
				$arCurrentValues['IBLOCK_ID'],
				['CODE' => 'Y']
			);
			if ($showedProperties === null)
				$showedProperties = [];
		}
	} else {
		if (!empty($arCurrentValues['DETAIL_PROPERTY_CODE']) && is_array($arCurrentValues['DETAIL_PROPERTY_CODE'])) {
			$showedProperties = $arCurrentValues['DETAIL_PROPERTY_CODE'];
		}
	}
	if (!empty($showedProperties)) {
		$selected = array();

		foreach ($showedProperties as $code) {
			if (isset($arAllPropList[$code])) {
				$selected[$code] = $arAllPropList[$code];
			}
		}

		$arTemplateParameters['DETAIL_MAIN_BLOCK_PROPERTY_CODE'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_MAIN_BLOCK_PROPERTY_CODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'SIZE' => (count($selected) > 5 ? 8 : 3),
			'VALUES' => $selected
		);
	}
	unset($showedProperties);
}

if ($boolSKU) {
	$showedProperties = [];
	if ($usePropertyFeatures) {
		$showedProperties = Iblock\Model\PropertyFeature::getDetailPageShowProperties(
			$arSKU['IBLOCK_ID'],
			['CODE' => 'Y']
		);
		if ($showedProperties === null)
			$showedProperties = [];
	} else {
		if (!empty($arCurrentValues['DETAIL_OFFERS_PROPERTY_CODE']) && is_array($arCurrentValues['DETAIL_OFFERS_PROPERTY_CODE'])) {
			$showedProperties = $arCurrentValues['DETAIL_OFFERS_PROPERTY_CODE'];
		}
	}
	if (!empty($showedProperties)) {
		$selected = array();

		foreach ($showedProperties as $code) {
			if (isset($arAllOfferPropList[$code])) {
				$selected[$code] = $arAllOfferPropList[$code];
			}
		}

		$arTemplateParameters['DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_MAIN_BLOCK_OFFERS_PROPERTY_CODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'SIZE' => (count($selected) > 5 ? 8 : 3),
			'VALUES' => $selected
		);
	}
	unset($showedProperties);
}

// $arTemplateParameters['DETAIL_USE_VOTE_RATING'] = array(
// 	'PARENT' => 'DETAIL_SETTINGS',
// 	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_VOTE_RATING'),
// 	'TYPE' => 'CHECKBOX',
// 	'DEFAULT' => 'N',
// 	'REFRESH' => 'Y'
// );

// if (isset($arCurrentValues['DETAIL_USE_VOTE_RATING']) && 'Y' == $arCurrentValues['DETAIL_USE_VOTE_RATING']) {
// 	$arTemplateParameters['DETAIL_VOTE_DISPLAY_AS_RATING'] = array(
// 		'PARENT' => 'DETAIL_SETTINGS',
// 		'NAME' => GetMessage('CP_BC_TPL_DETAIL_VOTE_DISPLAY_AS_RATING'),
// 		'TYPE' => 'LIST',
// 		'VALUES' => array(
// 			'rating' => GetMessage('CP_BC_TPL_DVDAR_RATING'),
// 			'vote_avg' => GetMessage('CP_BC_TPL_DVDAR_AVERAGE'),
// 		),
// 		'DEFAULT' => 'rating'
// 	);
// }

// $arTemplateParameters['DETAIL_USE_COMMENTS'] = array(
// 	'PARENT' => 'DETAIL_SETTINGS',
// 	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_COMMENTS'),
// 	'TYPE' => 'CHECKBOX',
// 	'DEFAULT' => 'N',
// 	'REFRESH' => 'Y'
// );

// if (isset($arCurrentValues['DETAIL_USE_COMMENTS']) && 'Y' == $arCurrentValues['DETAIL_USE_COMMENTS']) {
// 	if (ModuleManager::isModuleInstalled("blog")) {
// 		$arTemplateParameters['DETAIL_BLOG_USE'] = array(
// 			'PARENT' => 'DETAIL_SETTINGS',
// 			'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_USE'),
// 			'TYPE' => 'CHECKBOX',
// 			'DEFAULT' => 'N',
// 			'REFRESH' => 'Y'
// 		);
// 		if (isset($arCurrentValues['DETAIL_BLOG_USE']) && $arCurrentValues['DETAIL_BLOG_USE'] == 'Y') {
// 			$arTemplateParameters['DETAIL_BLOG_URL'] = array(
// 				'PARENT' => 'DETAIL_SETTINGS',
// 				'NAME' => GetMessage('CP_BC_DETAIL_TPL_BLOG_URL'),
// 				'TYPE' => 'STRING',
// 				'DEFAULT' => 'catalog_comments'
// 			);
// 			$arTemplateParameters['DETAIL_BLOG_EMAIL_NOTIFY'] = array(
// 				'PARENT' => 'DETAIL_SETTINGS',
// 				'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_EMAIL_NOTIFY'),
// 				'TYPE' => 'CHECKBOX',
// 				'DEFAULT' => 'N'
// 			);
// 		}
// 	}
// }

$arTemplateParameters['DETAIL_DISPLAY_NAME'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_NAME'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);

// $arTemplateParameters['DETAIL_DETAIL_PICTURE_MODE'] = array(
// 	'PARENT' => 'DETAIL_SETTINGS',
// 	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DETAIL_PICTURE_MODE'),
// 	'TYPE' => 'LIST',
// 	'MULTIPLE' => 'Y',
// 	'DEFAULT' => array('POPUP', 'MAGNIFIER'),
// 	'VALUES' => array(
// 		'POPUP' => GetMessage('DETAIL_DETAIL_PICTURE_MODE_POPUP'),
// 		'MAGNIFIER' => GetMessage('DETAIL_DETAIL_PICTURE_MODE_MAGNIFIER'),
// 	)
// );

$arTemplateParameters['DETAIL_ADD_DETAIL_TO_SLIDER'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_DETAIL_TO_SLIDER'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
);
$arTemplateParameters['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE'),
	'TYPE' => 'LIST',
	'VALUES' => array(
		'H' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_HIDE'),
		'E' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_EMPTY_DETAIL'),
		'S' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_SHOW')
	),
	'DEFAULT' => 'E'
);

if ($boolCatalog) {
	$arTemplateParameters['USE_COMMON_SETTINGS_BASKET_POPUP'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_USE_COMMON_SETTINGS_BASKET_POPUP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);
	$useCommonSettingsBasketPopup = (
		isset($arCurrentValues['USE_COMMON_SETTINGS_BASKET_POPUP'])
		&& $arCurrentValues['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y'
	);
	$addToBasketActions = array(
		'BUY' => GetMessage('ADD_TO_BASKET_ACTION_BUY'),
		'ADD' => GetMessage('ADD_TO_BASKET_ACTION_ADD')
	);
	$arTemplateParameters['COMMON_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_COMMON_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => ($useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['COMMON_SHOW_CLOSE_POPUP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_COMMON_SHOW_CLOSE_POPUP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['MESS_PRICE_RANGES_TITLE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_MESS_PRICE_RANGES_TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('CP_BC_TPL_MESS_PRICE_RANGES_TITLE_DEFAULT')
	);
	$arTemplateParameters['MESS_DESCRIPTION_TAB'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_MESS_DESCRIPTION_TAB'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('CP_BC_TPL_MESS_DESCRIPTION_TAB_DEFAULT')
	);
	$arTemplateParameters['MESS_PROPERTIES_TAB'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_MESS_PROPERTIES_TAB'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('CP_BC_TPL_MESS_PROPERTIES_TAB_DEFAULT')
	);
	$arTemplateParameters['MESS_COMMENTS_TAB'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_MESS_COMMENTS_TAB'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('CP_BC_TPL_MESS_COMMENTS_TAB_DEFAULT')
	);
	$arTemplateParameters['TOP_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_TOP_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['SECTION_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_SECTION_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['DETAIL_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'BUY',
		'REFRESH' => 'Y',
		'MULTIPLE' => 'Y',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);

	if (!$useCommonSettingsBasketPopup && !empty($arCurrentValues['DETAIL_ADD_TO_BASKET_ACTION'])) {
		$selected = array();

		if (!is_array($arCurrentValues['DETAIL_ADD_TO_BASKET_ACTION'])) {
			$arCurrentValues['DETAIL_ADD_TO_BASKET_ACTION'] = array($arCurrentValues['DETAIL_ADD_TO_BASKET_ACTION']);
		}

		foreach ($arCurrentValues['DETAIL_ADD_TO_BASKET_ACTION'] as $action) {
			if (isset($addToBasketActions[$action])) {
				$selected[$action] = $addToBasketActions[$action];
			}
		}

		$arTemplateParameters['DETAIL_ADD_TO_BASKET_ACTION_PRIMARY'] = array(
			'PARENT' => 'BASKET',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_TO_BASKET_ACTION_PRIMARY'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $selected,
			'DEFAULT' => 'BUY',
			'REFRESH' => 'N'
		);
		unset($selected);
	}

	$arTemplateParameters['PRODUCT_SUBSCRIPTION'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_PRODUCT_SUBSCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	);
	$arTemplateParameters['SHOW_DISCOUNT_PERCENT'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);

	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['SHOW_MAX_QUANTITY'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY'),
		'TYPE' => 'LIST',
		'REFRESH' => 'Y',
		'MULTIPLE' => 'N',
		'VALUES' => array(
			'N' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_N'),
			'Y' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_Y'),
			'M' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_M')
		),
		'DEFAULT' => array('N')
	);

	if (isset($arCurrentValues['SHOW_MAX_QUANTITY'])) {
		if ($arCurrentValues['SHOW_MAX_QUANTITY'] !== 'N') {
			$arTemplateParameters['MESS_SHOW_MAX_QUANTITY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_SHOW_MAX_QUANTITY'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_SHOW_MAX_QUANTITY_DEFAULT')
			);
		}

		if ($arCurrentValues['SHOW_MAX_QUANTITY'] === 'M') {
			$arTemplateParameters['RELATIVE_QUANTITY_FACTOR'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_RELATIVE_QUANTITY_FACTOR'),
				'TYPE' => 'STRING',
				'DEFAULT' => '5'
			);
			$arTemplateParameters['MESS_RELATIVE_QUANTITY_MANY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY_DEFAULT')
			);
			$arTemplateParameters['MESS_RELATIVE_QUANTITY_FEW'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW_DEFAULT')
			);
		}
	}
}

$arTemplateParameters['LAZY_LOAD'] = array(
	'PARENT' => 'PAGER_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_LAZY_LOAD'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'Y',
	'DEFAULT' => 'N'
);

$arTemplateParameters['DISPLAY_TOP_PAGER'] = array(
	'VALUE' => 'N',
	'HIDDEN' => 'Y',
);

$arTemplateParameters['DISPLAY_BOTTOM_PAGER'] = array(
	'PARENT' => 'PAGER_SETTINGS',
	'NAME' => 'Показывать пагинацию',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'SORT' => 0,
);

$arTemplateParameters['MESS_BTN_LAZY_LOAD'] = array(
	'PARENT' => 'PAGER_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_LAZY_LOAD'),
	'TYPE' => 'TEXT',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_LAZY_LOAD_DEFAULT'),
	'HIDDEN' => (
		(isset($arCurrentValues['LAZY_LOAD']) && $arCurrentValues['LAZY_LOAD'] === 'Y')
		|| (isset($arCurrentValues['DISPLAY_TOP_PAGER']) && $arCurrentValues['DISPLAY_TOP_PAGER'] === 'Y')
		|| (!isset($arCurrentValues['DISPLAY_BOTTOM_PAGER']) || $arCurrentValues['DISPLAY_BOTTOM_PAGER'] !== 'N')
		? 'N'
		: 'Y'
	)
);

$arTemplateParameters['LOAD_ON_SCROLL'] = array(
	'PARENT' => 'PAGER_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_LOAD_ON_SCROLL'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'HIDDEN' => (isset($arCurrentValues['LAZY_LOAD']) && $arCurrentValues['LAZY_LOAD'] === 'Y' ? 'N' : 'Y')
);

$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADD_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_ADD_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_COMPARE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE_DEFAULT')
);
$arTemplateParameters['MESS_BTN_DETAIL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_DETAIL'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_DETAIL_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE_DEFAULT')
);
$arTemplateParameters['MESS_NOT_AVAILABLE_SERVICE'] = [
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE_SERVICE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_NOT_AVAILABLE_SERVICE_DEFAULT'),
];

$arTemplateParameters['MESS_BTN_SUBSCRIBE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_SUBSCRIBE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_SUBSCRIBE_DEFAULT')
);

if (ModuleManager::isModuleInstalled("sale")) {
	$arTemplateParameters['USE_SALE_BESTSELLERS'] = array(
		'NAME' => GetMessage('CP_BC_TPL_USE_SALE_BESTSELLERS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	);

	if (\Bitrix\Main\Analytics\Catalog::isOn()) {
		$arTemplateParameters['USE_BIG_DATA'] = array(
			'PARENT' => 'BIG_DATA_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_USE_BIG_DATA'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y'
		);
		if (!isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] === 'Y') {
			$rcmTypeList = array(
				'personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL'),
				'bestsell' => GetMessage('CP_BC_TPL_RCM_BESTSELLERS'),
				'similar_sell' => GetMessage('CP_BC_TPL_RCM_SOLD_WITH'),
				'similar_view' => GetMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
				'similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR'),
				'any_similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
				'any_personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
				'any' => GetMessage('CP_BC_TPL_RCM_RAND')
			);
			$arTemplateParameters['BIG_DATA_RCM_TYPE'] = array(
				'PARENT' => 'BIG_DATA_SETTINGS',
				'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
				'TYPE' => 'LIST',
				'DEFAULT' => 'personal',
				'VALUES' => $rcmTypeList
			);
			unset($rcmTypeList);
		}
	}
}

// if (isset($arCurrentValues['SHOW_TOP_ELEMENTS']) && 'Y' == $arCurrentValues['SHOW_TOP_ELEMENTS']) {
// 	$arTemplateParameters['TOP_VIEW_MODE'] = array(
// 		'PARENT' => 'TOP_SETTINGS',
// 		'NAME' => GetMessage('CPT_BC_TPL_TOP_VIEW_MODE'),
// 		'TYPE' => 'LIST',
// 		'VALUES' => array(
// 			'BANNER' => GetMessage('CPT_BC_TPL_VIEW_MODE_BANNER'),
// 			'SLIDER' => GetMessage('CPT_BC_TPL_VIEW_MODE_SLIDER'),
// 			'SECTION' => GetMessage('CPT_BC_TPL_VIEW_MODE_SECTION')
// 		),
// 		'MULTIPLE' => 'N',
// 		'DEFAULT' => 'SECTION',
// 		'REFRESH' => 'Y'
// 	);

// 	if (isset($arCurrentValues['TOP_VIEW_MODE']) && ('SLIDER' == $arCurrentValues['TOP_VIEW_MODE'] || 'BANNER' == $arCurrentValues['TOP_VIEW_MODE'])) {
// 		$arTemplateParameters['TOP_ROTATE_TIMER'] = array(
// 			'PARENT' => 'TOP_SETTINGS',
// 			'NAME' => GetMessage('CPT_BC_TPL_TOP_ROTATE_TIMER'),
// 			'TYPE' => 'STRING',
// 			'DEFAULT' => '30'
// 		);
// 	}

// 	if (isset($arCurrentValues['TOP_VIEW_MODE']) && $arCurrentValues['TOP_VIEW_MODE'] === 'SECTION') {
// 		if (!empty($arCurrentValues['TOP_PROPERTY_CODE'])) {
// 			$selected = array();

// 			foreach ($arCurrentValues['TOP_PROPERTY_CODE'] as $code) {
// 				if (isset($arAllPropList[$code])) {
// 					$selected[$code] = $arAllPropList[$code];
// 				}
// 			}

// 			$arTemplateParameters['TOP_PROPERTY_CODE_MOBILE'] = array(
// 				'PARENT' => 'TOP_SETTINGS',
// 				'NAME' => GetMessage('CP_BC_TPL_PROPERTY_CODE_MOBILE'),
// 				'TYPE' => 'LIST',
// 				'MULTIPLE' => 'Y',
// 				'VALUES' => $selected
// 			);
// 		}

// 		$arTemplateParameters['TOP_ENLARGE_PRODUCT'] = array(
// 			'PARENT' => 'TOP_SETTINGS',
// 			'NAME' => GetMessage('CP_BC_TPL_ENLARGE_PRODUCT'),
// 			'TYPE' => 'LIST',
// 			'MULTIPLE' => 'N',
// 			'ADDITIONAL_VALUES' => 'N',
// 			'REFRESH' => 'Y',
// 			'DEFAULT' => 'N',
// 			'VALUES' => array(
// 				'STRICT' => GetMessage('CP_BC_TPL_ENLARGE_PRODUCT_STRICT'),
// 				'PROP' => GetMessage('CP_BC_TPL_ENLARGE_PRODUCT_PROP')
// 			)
// 		);

// 		if (isset($arCurrentValues['TOP_ENLARGE_PRODUCT']) && $arCurrentValues['TOP_ENLARGE_PRODUCT'] === 'PROP') {
// 			$arTemplateParameters['TOP_ENLARGE_PROP'] = array(
// 				'PARENT' => 'TOP_SETTINGS',
// 				'NAME' => GetMessage('CP_BC_TPL_ENLARGE_PROP'),
// 				'TYPE' => 'LIST',
// 				'MULTIPLE' => 'N',
// 				'ADDITIONAL_VALUES' => 'N',
// 				'REFRESH' => 'N',
// 				'DEFAULT' => '-',
// 				'VALUES' => $defaultValue + $arListPropList
// 			);
// 		}
// 		$arTemplateParameters['TOP_SHOW_SLIDER'] = array(
// 			'PARENT' => 'TOP_SETTINGS',
// 			'NAME' => GetMessage('CP_BC_TPL_SHOW_SLIDER'),
// 			'TYPE' => 'CHECKBOX',
// 			'MULTIPLE' => 'N',
// 			'REFRESH' => 'Y',
// 			'DEFAULT' => 'Y'
// 		);

// 		// if (!isset($arCurrentValues['TOP_SHOW_SLIDER']) || $arCurrentValues['TOP_SHOW_SLIDER'] === 'Y') {
// 		// 	$arTemplateParameters['TOP_SLIDER_INTERVAL'] = array(
// 		// 		'PARENT' => 'TOP_SETTINGS',
// 		// 		'NAME' => GetMessage('CP_BC_TPL_SLIDER_INTERVAL'),
// 		// 		'TYPE' => 'TEXT',
// 		// 		'MULTIPLE' => 'N',
// 		// 		'REFRESH' => 'N',
// 		// 		'DEFAULT' => '3000'
// 		// 	);
// 		// 	$arTemplateParameters['TOP_SLIDER_PROGRESS'] = array(
// 		// 		'PARENT' => 'TOP_SETTINGS',
// 		// 		'NAME' => GetMessage('CP_BC_TPL_SLIDER_PROGRESS'),
// 		// 		'TYPE' => 'CHECKBOX',
// 		// 		'MULTIPLE' => 'N',
// 		// 		'REFRESH' => 'N',
// 		// 		'DEFAULT' => 'N'
// 		// 	);
// 		// }
// 	}
// }

if (isset($arCurrentValues['USE_COMPARE']) && $arCurrentValues['USE_COMPARE'] == 'Y') {
	$arTemplateParameters['COMPARE_POSITION_FIXED'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => "Показывать виджет сравниваемых товаров поверх страницы",
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	);
	if (!isset($arCurrentValues['COMPARE_POSITION_FIXED']) || $arCurrentValues['COMPARE_POSITION_FIXED'] == 'Y') {
		$arTemplateParameters['SHOW_COMPARED_LIST'] = array(
			'PARENT' => 'COMPARE_SETTINGS',
			'NAME' => "Показывать в виджете список сравниваемых товаров",
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		);
	}
}

// if (isset($arCurrentValues['USE_COMPARE']) && $arCurrentValues['USE_COMPARE'] == 'Y') {
// 	$arTemplateParameters['COMPARE_POSITION_FIXED'] = array(
// 		'PARENT' => 'COMPARE_SETTINGS',
// 		'NAME' => GetMessage('CPT_BC_TPL_COMPARE_POSITION_FIXED'),
// 		'TYPE' => 'CHECKBOX',
// 		'DEFAULT' => 'Y',
// 		'REFRESH' => 'Y'
// 	);
// 	if (!isset($arCurrentValues['COMPARE_POSITION_FIXED']) || $arCurrentValues['COMPARE_POSITION_FIXED'] == 'Y') {
// 		$positionList = array(
// 			'top left' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_TOP_LEFT'),
// 			'top right' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_TOP_RIGHT'),
// 			'bottom left' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_BOTTOM_LEFT'),
// 			'bottom right' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_BOTTOM_RIGHT')
// 		);
// 		$arTemplateParameters['COMPARE_POSITION'] = array(
// 			'PARENT' => 'COMPARE_SETTINGS',
// 			'NAME' => GetMessage('CPT_BC_TPL_COMPARE_POSITION'),
// 			'TYPE' => 'LIST',
// 			'VALUES' => $positionList,
// 			'DEFAULT' => 'top left'
// 		);
// 		unset($positionList);
// 	}
// }

$arTemplateParameters['SIDEBAR_SECTION_SHOW'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CPT_SIDEBAR_SECTION_SHOW'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'SORT' => 800
);
// $arTemplateParameters['SIDEBAR_DETAIL_SHOW'] = array(
// 	'PARENT' => 'VISUAL',
// 	'NAME' => GetMessage('CPT_SIDEBAR_DETAIL_SHOW'),
// 	'TYPE' => 'CHECKBOX',
// 	'DEFAULT' => 'N',
// 	'SORT' => 800
// );
$arTemplateParameters['SIDEBAR_PATH'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CPT_SIDEBAR_PATH'),
	'TYPE' => 'STRING',
	'SORT' => 800
);

$arTemplateParameters['USE_ENHANCED_ECOMMERCE'] = array(
	'PARENT' => 'ANALYTICS_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_USE_ENHANCED_ECOMMERCE'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'Y',
	'DEFAULT' => 'N'
);

if (isset($arCurrentValues['USE_ENHANCED_ECOMMERCE']) && $arCurrentValues['USE_ENHANCED_ECOMMERCE'] === 'Y') {
	$arTemplateParameters['DATA_LAYER_NAME'] = array(
		'PARENT' => 'ANALYTICS_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_DATA_LAYER_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'dataLayer'
	);
	$arTemplateParameters['BRAND_PROPERTY'] = array(
		'PARENT' => 'ANALYTICS_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_BRAND_PROPERTY'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'DEFAULT' => '',
		'VALUES' => $defaultValue + $arAllPropList
	);
}

$arTemplateParameters['DETAIL_SHOW_POPULAR'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_SHOW_POPULAR'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);
$arTemplateParameters['DETAIL_SHOW_VIEWED'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_SHOW_VIEWED'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);

// hack to hide component parameters by templates
$arTemplateParameters['HIDE_USE_ALSO_BUY'] = array();

$arTemplateParameters['USE_GIFTS_DETAIL'] = array(
	'HIDDEN' => 'Y'
);

$arTemplateParameters['USE_GIFTS_SECTION'] = array(
	'HIDDEN' => 'Y'
);

$arTemplateParameters['USE_GIFTS_MAIN_PR_SECTION_LIST'] = array(
	'HIDDEN' => 'Y'
);
