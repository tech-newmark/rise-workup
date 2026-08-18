<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$arResult = $arParams;
?>

<? if ($arResult): ?>
    <div class="article-card-container">
        <article class="article-card">
            <a class="article-card__link" href="<?= $arResult['DETAIL_PAGE_URL'] ?>">
                <img src="<?= ($arResult['PREVIEW_PICTURE']['SRC']) ? $arResult['PREVIEW_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>" width="480" height="160">
                <div class="article-card__body">
                    <h2><?= $arResult['NAME'] ?></h2>
                    <? if ($arParams['SHOW_DATE_ACTIVE_FROM'] == "Y" && $arResult['DISPLAY_ACTIVE_FROM']): ?>
                        <span class="article-card__time">Опубликовано: <time><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></time></span>
                    <? endif; ?>
                    <? if ($arParams['SHOW_DATE_ACTIVE_TO'] == "Y" && $arResult['DATE_ACTIVE_TO']): ?>
                        <span class="article-card__time">Действует до <time><?= FormatDate("j F Y", MakeTimeStamp($arResult['DATE_ACTIVE_TO'], "DD.MM.YYYY")) ?></time></span>
                    <? endif; ?>
                    <? if ($arResult['~PREVIEW_TEXT'] || $arResult['~DETAIL_TEXT']): ?>
                        <div class="content">
                            <?= ($arResult['~PREVIEW_TEXT']) ?>
                        </div>
                    <? endif; ?>
                    <span class="clear-btn">Подробнее
                        <svg width="20" height="20" viewBox="0 0 20 20" role="img" aria-hidden="true" focusable="false">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
                        </svg>
                    </span>
                </div>
            </a>
        </article>
    </div>
<? endif; ?>