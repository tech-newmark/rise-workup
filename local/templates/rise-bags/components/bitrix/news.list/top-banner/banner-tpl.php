<article class="banner" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
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