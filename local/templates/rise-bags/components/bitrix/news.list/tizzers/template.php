<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$i = 1;
$title = trim((string)($arParams["CUSTOM_TITLE"] ?? ''));

if ($arResult["ITEMS"]): ?>
  <section class="section">
    <div class="container">
      <? if (($arParams["SHOW_TITLE"] ?? '') == "Y" || $title !== ''): ?>
        <h2><?= !empty($title) ? $title : $arResult["NAME"] ?></h2>
      <? endif; ?>
      <div class="tizzers-container">
        <div class="tizzers <?= ($arParams["BIG_TIZZERS"] ?? '') == "Y" ? "tizzers--big" : "" ?>">

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
