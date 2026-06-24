<?php

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$request = Application::getInstance()->getContext()->getRequest();
$compareName = (string)($request->get('compare_name') ?: 'CATALOG_COMPARE_LIST');
$action = (string)($request->get('action') ?: 'status');
$productId = (int)($request->getPost('product_id') ?: $request->get('product_id'));
$iblockId = (int)($request->getPost('iblock_id') ?: $request->get('iblock_id'));
$success = true;
$message = '';

try {
  if ($action === 'add') {
    $success = function_exists('riseBagsAddCompareItem') && riseBagsAddCompareItem($productId, $iblockId, $compareName);
  } elseif ($action === 'delete') {
    $success = function_exists('riseBagsDeleteCompareItem') && riseBagsDeleteCompareItem($productId, $compareName);
  } elseif ($action === 'toggle') {
    $ids = function_exists('riseBagsGetCompareItems') ? riseBagsGetCompareItems($compareName) : [];

    if (in_array($productId, $ids, true)) {
      $success = function_exists('riseBagsDeleteCompareItem') && riseBagsDeleteCompareItem($productId, $compareName);
    } else {
      $success = function_exists('riseBagsAddCompareItem') && riseBagsAddCompareItem($productId, $iblockId, $compareName);
    }
  }

  if (!$success && $action !== 'status') {
    $message = 'Не удалось обновить список сравнения';
  }
} catch (Throwable $exception) {
  $success = false;
  $message = $exception->getMessage();
}

$ids = function_exists('riseBagsGetCompareItems') ? riseBagsGetCompareItems($compareName) : [];

header('Content-Type: application/json; charset=UTF-8');
echo Json::encode([
  'success' => $success,
  'message' => $message,
  'ids' => $ids,
  'count' => count($ids),
  'isCompared' => $productId > 0 && in_array($productId, $ids, true),
]);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
