<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>


<section class="section service-list">
  <div class="container">
    <? if ($arParams["PAGE_TYPE"] === "HUB"): ?>
      <h1><?= $arResult["NAME"] ?></h1>
    <? endif; ?>
    <h2><?= ($arParams["CUSTOM_TITLE"] ?? '') ?: "Выберите продукцию" ?></h2>
    <p class="base-text"><?= ($arParams["CUSTOM_DESC"] ?? '') ?: " Перейдите в нужный раздел, чтобы ознакомиться с ассортиментом." ?></p>
    <? if ($arResult["ITEMS"]): ?>
      <div class="swiper service-slider">
        <div class="swiper-wrapper">
          <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="swiper-slide">
              <article class="service-card" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">

                <? if ($arItem["PREVIEW_PICTURE"]["SRC"]): ?>
                  <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["PREVIEW_PICTURE"]["DESCRIPTION"] ?: $arItem["NAME"] ?>" width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>" height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>">
                <? endif; ?>
                <h3 class="service-card__title"><?= $arItem["NAME"] ?></h3>
                <? if ($arItem["PREVIEW_TEXT"]): ?>
                  <p class="service-card__desc"><?= $arItem["PREVIEW_TEXT"] ?></p>
                <? endif; ?>
                <a class="main-btn" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">Подробнее</a>
              </article>
            </div>
          <? endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    <? endif; ?>
  </div>
</section>