<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult["ITEMS"]): ?>
  <section class="section banners" aria-label="Специальные предложения">
    <div class="container">
      <div class="banner-container">
        <? if (count($arResult["ITEMS"]) > 1): ?>
          <div class="swiper banners-slider">
            <div class="swiper-wrapper">
            <? endif;  ?>
            <?
            foreach ($arResult["ITEMS"] as $arItem):
              $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
              $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
              <? if (count($arResult["ITEMS"]) > 1): ?>
                <div class="swiper-slide">
                <? endif;  ?>
                <? include $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/banner-tpl.php'; ?>
                <? if (count($arResult["ITEMS"]) > 1): ?>
                </div>
              <? endif;  ?>
            <? endforeach; ?>
            <? if (count($arResult["ITEMS"]) > 1): ?>
            </div>
            <div class="swiper-pagination"></div>
          </div>
      </div>
    <? endif;  ?>
    </div>
  </section>

<? endif; ?>