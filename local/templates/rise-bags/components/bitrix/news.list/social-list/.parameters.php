<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	"THEME_SELECT" => array(
		"NAME" => 'Выбор темы',
		"TYPE" => "LIST",
		"VALUES" => array(
			"1" => "Светлая",
			"2" => "Темная",
		),
		"DEFAULT" => "1",
		"PARENT" => "VISUAL",
		"SORT" => 100
	),
);
