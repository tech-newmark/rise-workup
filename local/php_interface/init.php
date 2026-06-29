<?php

// use Bitrix\Main\Page\Asset;
use Bitrix\Main\EventManager;

// Загружаем манифест
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/ViteManifest.php';

// Создаём глобальный объект Vite
global $vite;
$vite = new ViteManifest('rise-bags');

// Подключаем все вспомогательные файлы
$includesPath = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/includes/';
require_once $includesPath . 'assets_init.php';
require_once $includesPath . 'core_init.php';
require_once $includesPath . 'debug.php';
require_once $includesPath . 'offer_names.php';
require_once $includesPath . 'favorites.php';
require_once $includesPath . 'compare.php';
require_once $includesPath . 'form_validation.php';


