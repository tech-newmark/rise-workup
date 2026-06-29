<?
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

// 1. Получаем ID из GET
$formId = $_GET['form_id'] ?? null;

if (!$formId) {
  http_response_code(400);
  echo 'Ошибка: не указан ID формы или формы с таким ID не существует!';
  die();
}

$APPLICATION->IncludeComponent(
  "bitrix:form.result.new",
  "ajax-popup",
  array(
    "AJAX_MODE" => "Y", // включаем AJAX-режим
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_ADDITIONAL" => "",
    "CACHE_TYPE" => "N",
    "CHAIN_ITEM_LINK" => "",
    "CHAIN_ITEM_TEXT" => "",
    "EDIT_URL" => "",
    "IGNORE_CUSTOM_TEMPLATE" => "N",
    "LIST_URL" => "",
    "SEF_MODE" => "N",
    "SUCCESS_URL" => "",
    "USE_EXTENDED_ERRORS" => "Y",
    "VARIABLE_ALIASES" => array("RESULT_ID" => "RESULT_ID", "WEB_FORM_ID" => "WEB_FORM_ID"),
    "WEB_FORM_ID" => $formId,
    "SKU_ID_FIELD_VALUE" => $_GET['sku_id'] ?? null
  )
);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
