<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

/** @var array $arCurrentValues */


$arTemplateParameters = array(
	"USE_DATE_FILTER" => array(
		"PARENT" => "VISUAL",
		"NAME" => "Использовать фильтр по году новости",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_DATE" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"USE_SHARE" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_USE_SHARE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	),
	"SHOW_DATE_ACTIVE_FROM" => array(
		"NAME" => "Показывать в карточке дату начала активности",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"SORT" => 100
	),
	"SHOW_DATE_ACTIVE_TO" => array(
		"NAME" => "Показывать в карточке дату окончания активности",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"SORT" => 100
	),
	"TITLE_IN_LINKED_ARTICLES" => array(
		"NAME" => "Заголовок над рекомендациями",
		"TYPE" => "STRING",
		"DEFAULT" => "Рекомендуем",
		"SORT" => 100
	),
	"DESC_IN_LINKED_ARTICLES" => array(
		"NAME" => "Описание над рекомендациями",
		"TYPE" => "STRING",
		"DEFAULT" => "Возможно вам так же будет интересно",
		"SORT" => 100
	),
	"BUTTON_NAME_IN_LINKED_ARTICLES" => array(
		"NAME" => "Текст на кнопке в рекомендациях",
		"TYPE" => "STRING",
		"DEFAULT" => "Смотреть все",
		"SORT" => 100
	),
);

if (($arCurrentValues['USE_SHARE'] ?? 'N') === 'Y') {
	$arTemplateParameters["SHARE_HIDE"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_HIDE"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
		"DEFAULT" => "N",
	);

	$arTemplateParameters["SHARE_TEMPLATE"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_TEMPLATE"),
		"DEFAULT" => "",
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"COLS" => 25,
		"REFRESH" => "Y",
	);

	$shareComponentTemplate = (trim((string)($arCurrentValues["SHARE_TEMPLATE"] ?? '')));
	if ($shareComponentTemplate === '') {
		$shareComponentTemplate = false;
	}

	include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/bitrix/main.share/util.php");

	$arHandlers = __bx_share_get_handlers($shareComponentTemplate);

	$arTemplateParameters["SHARE_HANDLERS"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_SYSTEM"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arHandlers["HANDLERS"],
		"DEFAULT" => $arHandlers["HANDLERS_DEFAULT"],
	);

	$arTemplateParameters["SHARE_SHORTEN_URL_LOGIN"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_SHORTEN_URL_LOGIN"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);

	$arTemplateParameters["SHARE_SHORTEN_URL_KEY"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_SHORTEN_URL_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}
