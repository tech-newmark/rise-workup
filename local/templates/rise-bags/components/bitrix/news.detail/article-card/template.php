<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult): ?>
	<div class="article-card-container">
		<article class="article-card">
			<img src="<?= ($arResult['PREVIEW_PICTURE']['SRC']) ? $arResult['PREVIEW_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>" width="480" height="240">
			<div class="article-card__body">
				<? if ($arParams['SHOW_DATE_ACTIVE_FROM'] == "Y" && $arResult['DISPLAY_ACTIVE_FROM']): ?>
					<span><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></span>
				<? endif; ?>
				<? if ($arParams['SHOW_DATE_ACTIVE_TO'] == "Y" && $arResult['DATE_ACTIVE_TO']): ?>
					<span>Действует до <?= FormatDate("j F Y", MakeTimeStamp($arResult['DATE_ACTIVE_TO'], "DD.MM.YYYY")) ?></span>
				<? endif; ?>
				<h2><?= $arResult['NAME'] ?></h2>
				<? if (($arResult['PREVIEW_TEXT']) || $arResult['DETAIL_TEXT']): ?>
					<p><?= ($arResult['PREVIEW_TEXT']) ? $arResult['PREVIEW_TEXT'] : $arResult['DETAIL_TEXT'] ?></p>
				<? endif; ?>
				<a href="<?= $arResult['LIST_PAGE_URL'] . $arResult['DETAIL_PAGE_URL'] ?>">Подробнее
					<svg width="9" height="16" viewBox="0 0 9 16" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
					</svg>
				</a>
			</div>
		</article>
	</div>
<? endif; ?>