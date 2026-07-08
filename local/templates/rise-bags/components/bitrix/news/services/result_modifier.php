<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
$arParams['USE_SHARE'] = (string)($arParams['USE_SHARE'] ?? 'N');
$arParams['USE_SHARE'] = $arParams['USE_SHARE'] === 'Y' ? 'Y' : 'N';
$arParams['SHARE_HIDE'] = (string)($arParams['SHARE_HIDE'] ?? 'N');
$arParams['SHARE_HIDE'] = $arParams['SHARE_HIDE'] === 'Y' ? 'Y' : 'N';
$arParams['SHARE_TEMPLATE'] = (string)($arParams['SHARE_TEMPLATE'] ?? 'N');
$arParams['SHARE_HANDLERS'] ??= [];
$arParams['SHARE_HANDLERS'] = is_array($arParams['SHARE_HANDLERS']) ? $arParams['SHARE_HANDLERS'] : [];
$arParams['SHARE_SHORTEN_URL_LOGIN'] = (string)($arParams['SHARE_SHORTEN_URL_LOGIN'] ?? 'N');
$arParams['SHARE_SHORTEN_URL_KEY'] = (string)($arParams['SHARE_SHORTEN_URL_KEY'] ?? 'N');

// Получаем описание услуги и связанные с ней элементы
$sectionId = CIBlockFindTools::GetSectionID(
	$arResult["VARIABLES"]["SECTION_ID"] ?? 0,
	$arResult["VARIABLES"]["SECTION_CODE"] ?? "",
	[
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"GLOBAL_ACTIVE" => "Y",
	]
);

$arResult["CURRENT_SECTION"] = [];

if ($sectionId > 0) {
	$sectionResult = CIBlockSection::GetList(
		[],
		[
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ID" => $sectionId,
			"GLOBAL_ACTIVE" => "Y",
		],
		false,
		[
			"ID",
			"NAME",
			"DESCRIPTION",
			"DESCRIPTION_TYPE",
			"UF_BANNERS",
			"UF_TIZZERS",
			"UF_ADVANTAGES",
			"UF_STAGES",
			"UF_FAQ",
			"UF_TIZZERS_TITLE",
			"UF_ADVANTAGES_TITLE",
			"UF_STAGES_TITLE",
			"UF_COOPERATION",
			"UF_SECTION_TITLE",
			"UF_SECTION_DESC",
			"UF_TOP_CONTENT_BANNER",
			"UF_BOTTOM_CONTENT_BANNER",
		]
	);

	$arResult["CURRENT_SECTION"] = $sectionResult->Fetch() ?: [];
}
