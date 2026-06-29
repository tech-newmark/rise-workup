<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $USER;
if (!$USER->IsAdmin()) {
  die('Access denied');
}

$result = rbRewriteTradeOfferNames([
  'DRY_RUN' => true,
]);

echo '<pre>';
print_r($result);
echo '</pre>';
