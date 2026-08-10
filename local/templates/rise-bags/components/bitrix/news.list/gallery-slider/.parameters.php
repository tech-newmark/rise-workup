<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	"SECTION_GALLERY" => array(
		"PARENT" => "VISUAL",
		"NAME" => "Выводить галерею как секцию",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SECTION_TITLE" => array(
		"PARENT" => "VISUAL",
		"NAME" => "Заголовок секции",
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
	"SECTION_DESCRIPTION" => array(
		"PARENT" => "VISUAL",
		"NAME" => "Описание секции",
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
	"SHOW_FOOTER" =>  array(
		"PARENT" => "VISUAL",
		"NAME" => "Показывать подпись изображения",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),

	"USE_FANCY" =>  array(
		"PARENT" => "VISUAL",
		"NAME" => "Открывать изображения в Fancybox",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
);
