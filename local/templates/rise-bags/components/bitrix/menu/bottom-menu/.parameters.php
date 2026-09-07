<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arTemplateParameters = [
	'TITLE' => [
		'NAME' => 'Заголовок',
		'TYPE' => 'STRING',
		'DEFAULT' => '',
		'PARENT' => 'BASE',
	],
	'TITLE_LINK' => [
		'NAME' => 'Ссылка',
		'TYPE' => 'STRING',
		'DEFAULT' => '',
		'PARENT' => 'BASE',
	],
];
