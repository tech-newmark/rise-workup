<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$arResult = $arParams;
?>

<? if ($arResult): ?>
    <div class="article-card-container">
        <article class="article-card">
            <a href="<?= $arResult['DETAIL_PAGE_URL'] ?>">
                <img src="<?= ($arResult['PREVIEW_PICTURE']['SRC']) ? $arResult['PREVIEW_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>" width="480" height="160">
            </a>
            <div class="article-card__body">
                <? if ($arParams['SHOW_DATE_ACTIVE_FROM'] == "Y" && $arResult['DISPLAY_ACTIVE_FROM']): ?>
                    <time><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></time>
                <? endif; ?>
                <? if ($arParams['SHOW_DATE_ACTIVE_TO'] == "Y" && $arResult['DATE_ACTIVE_TO']): ?>
                    <time>Действует до <?= FormatDate("j F Y", MakeTimeStamp($arResult['DATE_ACTIVE_TO'], "DD.MM.YYYY")) ?></time>
                <? endif; ?>
                <a href="<?= $arResult['DETAIL_PAGE_URL'] ?>">
                    <h2><?= $arResult['NAME'] ?></h2>
                </a>
                <? if ($arResult['~PREVIEW_TEXT'] || $arResult['~DETAIL_TEXT']): ?>
                    <div class="article-card__desc"><?= ($arResult['~PREVIEW_TEXT']) ?  $arResult['~PREVIEW_TEXT'] : $arResult['~DETAIL_TEXT'] ?></div>
                <? endif; ?>
                <a class="clear-btn" href="<?= $arResult['DETAIL_PAGE_URL'] ?>">Подробнее
                    <svg width="20" height="20" viewBox="0 0 20 20" role="img" aria-hidden="true" focusable="false">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
                    </svg>
                </a>
            </div>
        </article>
    </div>
<? endif; ?>