<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$i = 1;

if ($arResult["ITEMS"]): ?>
  <section class="section tizzers">
    <div class="container">
      <h2 class="<?= (($arParams["SHOW_TITLE"] ?? '') === "Y") ? "" : "visually-hidden" ?>">
        <?= ($arParams["CUSTOM_TITLE"] ?? '') ?: $arResult["NAME"] ?>
      </h2>
      <div class="tizzers-container">
        <div class="grid <?= $arParams["CUSTOM_GRID"] == "GRID-2" ? "grid--2-col" : "" ?>">

          <? foreach ($arResult["ITEMS"] as $arItem):
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            $iconPath = CFile::GetPath($arItem["PROPERTIES"]["ICON"]["VALUE"] ?? '');
            $arItemButton = $arItem["PROPERTIES"]["BUTTON"]["VALUE"]["SUB_VALUES"] ?? [];
            $arItemLink = $arItem["PROPERTIES"]["LINK"]["VALUE"]["SUB_VALUES"] ?? [];
          ?>
            <div class="tizzers__item <?= ($arParams["COLUMN_VIEW"] ?? '') == "Y" ? "tizzers__item--column" : "" ?> <?= ($arParams["CLEAR_BG"] ?? '') == "Y" ? "tizzers__item--clear" : "" ?>" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
              <div class=" tizzers__item-content-wrapper">
                <span class="tizzers__item-title"><?= $arItem["NAME"] ?></span>
                <p class="tizzers__item-text"><?= $arItem["PREVIEW_TEXT"] ?></p>
                <? if (($arItemButton["BUTTON_TITLE"]["VALUE"] ?? '') && ($arItemButton["FORM_ID"]["VALUE"] ?? '')): ?>
                  <button class="main-btn" data-form-id="<?= $arItemButton["FORM_ID"]["VALUE"] ?>"><?= $arItemButton["BUTTON_TITLE"]["VALUE"] ?></button>
                <? endif; ?>
                <? if (($arItemLink["LINK_TITLE"]["VALUE"] ?? '') && ($arItemLink["LINK_URL"]["VALUE"] ?? '')): ?>
                  <a class="main-btn" href="<?= $arItemLink["LINK_URL"]["VALUE"] ?>"><?= $arItemLink["LINK_TITLE"]["VALUE"] ?></a>
                <? endif; ?>
              </div>
              <? if (($arParams["SHOW_ITEM_NUMBER"] ?? '') == "Y"): ?>
                <span class="tizzers__item-number"><?= $i ?></span>
              <? elseif ($iconPath): ?>
                <img src="<?= $iconPath ?>" alt="Иконка" width="60" height="60">
              <? endif; ?>
            </div>
            <? $i++ ?>
          <? endforeach; ?>
        </div>
      </div>
    </div>
  </section>
<? endif; ?>