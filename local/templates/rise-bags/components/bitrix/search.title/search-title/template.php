<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$INPUT_ID = trim($arParams['~INPUT_ID']);
if ($INPUT_ID == '') {
	$INPUT_ID = 'title-search-input';
}
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams['~CONTAINER_ID']);
if ($CONTAINER_ID == '') {
	$CONTAINER_ID = 'title-search';
}
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if ($arParams['SHOW_INPUT'] !== 'N'): ?>
	<div id="<?= $CONTAINER_ID ?>" class="search-title">
		<form action="<?= $arResult['FORM_ACTION'] ?>">
			<div class="main-input-wrapper search-title__wrapper">
				<label class="">
					<input id="<?= $INPUT_ID ?>" type="text" name="q" value="<?= htmlspecialcharsbx($_REQUEST['q'] ?? '') ?>" autocomplete="off" placeholder="Поиск" />
				</label>
				<button class="search-title__btn" name="s" type="submit" aria-label="Поиск">
					<svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-search"></use>
					</svg>
				</button>
			</div>
			<button type="button" class="search-title-closer" aria-label="Закрыть поиск">
				<svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#cross-icon"></use>
				</svg>
			</button>
		</form>
		<div id="search-results" class="search-title__expand-wrapper"></div>
	</div>
<? endif ?>

<script>
	BX.ready(function() {
		new JCTitleSearch({
			'AJAX_PAGE': '/search/',
			'CONTAINER_ID': '<?= $CONTAINER_ID ?>',
			'RESULT_CONTAINER_ID': 'search-results',
			'INPUT_ID': '<?= $INPUT_ID ?>',
			'MIN_QUERY_LEN': 2
		});
	});
</script>