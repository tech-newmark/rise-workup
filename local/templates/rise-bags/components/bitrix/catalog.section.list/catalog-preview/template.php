<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult["SECTIONS"]):
?>
  <? foreach ($arResult["SECTIONS"] as $arItem): ?>
    <? if ($arItem["UF_SHOW_ON_INDEX"]): ?>
      <!-- <?= debug($arItem) ?> -->
      <?= $arItem["NAME"] ?>
    <? endif; ?>
  <? endforeach; ?>
<? endif; ?>