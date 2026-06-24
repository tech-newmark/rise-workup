<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

if (!$arResult["NavShowAlways"]) {
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
?>
<? if (!$arResult["NavShowAll"]): ?>
	<div class="pagination">

		<? if ($arResult["NavPageNomer"] > 1): ?>
			<? if ($arResult["bSavePage"]): ?>
				<a
					href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"
					class="pagination__btn pagination__btn--prev">
					<svg width="18" height="18">
						<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/sprite.svg#icon-arrow"></use>
					</svg>
				</a>
			<? else: ?>
				<? if ($arResult["NavPageNomer"] > 2): ?>
					<a
						href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"
						class="pagination__btn pagination__btn--prev">
						<svg width="18" height="18">
							<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/sprite.svg#icon-arrow"></use>
						</svg>
					</a>
				<? else: ?>
					<a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>" class="pagination__btn pagination__btn--prev">
						<svg width="18" height="18">
							<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/sprite.svg#icon-arrow"></use>
						</svg>
					</a>
				<? endif ?>
			<? endif ?>
		<? else: ?>
			<span class="pagination__btn pagination__btn--prev pagination__btn--disabled">
				<svg width="18" height="18">
					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/sprite.svg#icon-arrow"></use>
				</svg>
			</span>
		<? endif ?>

		<? while ($arResult["nStartPage"] <= $arResult["nEndPage"]): ?>
			<? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]): ?>
				<span class="pagination__btn pagination__btn--selected"><?= $arResult["nStartPage"] ?></span>
			<? elseif ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false): ?>
				<a class="pagination__btn" href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"><?= $arResult["nStartPage"] ?></a>
			<? else: ?>
				<a class="pagination__btn" href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a>
			<? endif ?>
			<? $arResult["nStartPage"]++ ?>
		<? endwhile ?>

		<? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]): ?>
			<a
				href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"
				class="pagination__btn pagination__btn--next">
				<svg width="18" height="18">
					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/sprite.svg#icon-arrow"></use>
				</svg>
			</a>
		<? else: ?>
			<span class="pagination__btn pagination__btn--next pagination__btn--disabled">
				<svg width="18" height="18">
					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/sprite.svg#icon-arrow"></use>
				</svg>
			</span>
		<? endif ?>

		<? if ($arResult["bShowAll"]): ?>
			<noindex>
				<? if (!$arResult["NavShowAll"]): ?>
					<a class="pagination-show-all-btn" href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>SHOWALL_<?= $arResult["NavNum"] ?>=1" rel="nofollow">
						Показать все
					</a>
				<? endif ?>
			</noindex>
		<? endif ?>
	</div>
<? endif ?>