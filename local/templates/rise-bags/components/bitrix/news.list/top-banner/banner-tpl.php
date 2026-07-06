<article id="<?= $this->GetEditAreaId($arItem['ID']); ?>"
  class="banner 
  <?= $arItem["PROPERTIES"]["DARK_FONT_COLOR"]["VALUE"] === "Y" ? "banner--dark-font" : "banner--default-font" ?>
  <?= $arParams["BANNER_SIZE"] === "SMALL" ? "banner--small" : "banner--default-size" ?>"
  style="<?= !empty($arItem["PROPERTIES"]["BACKGROUND_COLOR"]["VALUE"])
            ? 'background-color: ' . $arItem["PROPERTIES"]["BACKGROUND_COLOR"]["VALUE_XML_ID"] . ';'
            : (!empty($arItem["DETAIL_PICTURE"]["SRC"])
              ? 'background-image: url(' . $arItem["DETAIL_PICTURE"]["SRC"] . ')'
              : 'background-image: url(' . $templateFolder . '/_src/images/banner-bg.jpg);') ?>">
  <div class="banner__content">
    <? if ($arItem["PROPERTIES"]["H1_TITLE"]["VALUE"] == "Y") : ?>
      <h1 class="heading heading--xl banner__title"><?= $arItem["~NAME"] ?></h1>
    <? else: ?>
      <h2 class="heading heading--xl banner__title"><?= $arItem["~NAME"] ?></h2>
    <? endif; ?>

    <? if ($arItem["DETAIL_TEXT"]): ?>
      <div class="content">
        <?= $arItem["DETAIL_TEXT"] ?>
      </div>
    <? endif; ?>

    <div class="banner__buttons">
      <? if (!empty($arItem["PROPERTIES"]["FORM_BUTTON"]["VALUE"]["SUB_VALUES"]["BUTTON_TITLE"]["VALUE"])): ?>
        <button class="main-btn" type="button" data-form-id="<?= $arItem["PROPERTIES"]["FORM_BUTTON"]["VALUE"]["SUB_VALUES"]["FORM_ID"]["VALUE"] ?>">
          <span>
            <?= $arItem["PROPERTIES"]["FORM_BUTTON"]["VALUE"]["SUB_VALUES"]["BUTTON_TITLE"]["VALUE"] ?>
          </span>
        </button>
      <? endif; ?>

      <? if (!empty($arItem["PROPERTIES"]["LINK"]["VALUE"]["SUB_VALUES"]["LINK_URL"]["VALUE"])): ?>
        <a class="main-btn main-btn--outlined" href="<?= $arItem["PROPERTIES"]["LINK"]["VALUE"]["SUB_VALUES"]["LINK_URL"]["VALUE"] ?>">
          <span>
            <?= $arItem["PROPERTIES"]["LINK"]["VALUE"]["SUB_VALUES"]["LINK_TITLE"]["VALUE"] ?>
          </span>
        </a>
      <? endif; ?>
    </div>
  </div>

  <? if ($arItem["PREVIEW_PICTURE"]["SRC"]): ?>
    <img class="banner__img"
      src="<?= ($arItem["PREVIEW_PICTURE"]["SRC"] ? $arItem["PREVIEW_PICTURE"]["SRC"] : '/img/tooth-banner-bg-paddings.png') ?>"
      alt="<?= $arItem["NAME"] ?>" width="450" height="450">
  <? endif; ?>

</article>