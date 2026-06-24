<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

// includeComponentAssets('news.list/catalog-preview');

if ($arResult["ITEMS"]): ?>
  <section class="section">
    <div class="container">
      <div class="catalog-preview-container">
        <div class="catalog-preview">
          <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <div class="catalog-preview__item">
              <div class="catalog-preview__item-content">
                <a href="<?= $arItem["PROPERTIES"]["CATALOG_SECTION_LINK"]["VALUE"] ?>" class="catalog-preview__item-title"><?= $arItem["NAME"] ?></a>
                <div class="catalog-preview__item-sections">
                  <? foreach ($arItem["LINKED_SECTIONS"] as $section): ?>
                    <a href="<?= $section["URL"] ?>">
                      <?= $section["NAME"] ?>
                    </a>
                  <? endforeach; ?>
                </div>

                <a href="<?= $arItem["PROPERTIES"]["CATALOG_SECTION_LINK"]["VALUE"] ?>" class="main-link underlined catalog-preview__item-link">Перейти в раздел</a>
              </div>

              <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["NAME"] ?>" width="360" height="240">
            </div>
          <? endforeach; ?>
        </div>
      </div>
    </div>
  </section>
<? endif; ?>