<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$arResult = $arParams;
?>

<? if ($arResult): ?>
    <div class="review-card-container">
        <article class="review-card">
            <? if ($arResult["PROPERTIES"]["RATING"]["VALUE"]): ?>
                <div class="review-card__rating">
                    <? for ($i = 0; $i < 5; $i++) : ?>
                        <svg class="<?= ($i < $arResult["PROPERTIES"]["RATING"]["VALUE"]) ? "active" : "" ?>" width="20" height="20" viewBox="0 0 20 20" role="img" aria-hidden="true" focusable="false">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#star"></use>
                        </svg>
                    <? endfor; ?>
                </div>
            <? endif; ?>
            <? if ($arResult["~PREVIEW_TEXT"]): ?>
                <div class="review-card__text"><?= $arResult["~PREVIEW_TEXT"] ?></div>
            <? endif; ?>
            <button class="clear-btn" type="button">Читать полностью
                <svg width="20" height="20" viewBox="0 0 20 20" role="img" aria-hidden="true" focusable="false">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
                </svg></button>
            <div class="review-card__author">
                <? if ($arResult["PREVIEW_PICTURE"]["SRC"]): ?>
                    <img src="<?= $arResult["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arResult["NAME"] ?>">
                <? endif; ?>
                <p class="review-card__author-name"><?= $arResult["NAME"] ?></p>
                <? if ($arResult["PROPERTIES"]["JOB_TITLE"]["VALUE"] || $arResult["PROPERTIES"]["COMPANY"]["VALUE"]): ?>
                    <p class="review-card__author-job"><?= $arResult["PROPERTIES"]["JOB_TITLE"]["VALUE"] ?>, <?= $arResult["PROPERTIES"]["COMPANY"]["VALUE"] ?></p>
                <? endif; ?>
            </div>
        </article>
    </div>
<? endif; ?>