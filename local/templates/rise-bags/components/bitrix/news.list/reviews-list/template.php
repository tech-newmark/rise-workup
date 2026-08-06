<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult["ITEMS"]): ?>
	<ul class="reviews">
		<? foreach ($arResult["ITEMS"] as $arItem): ?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<li class="review" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
				<article class="review-card">
					<? if ($arItem["PROPERTIES"]["RATING"]["VALUE"]): ?>
						<div class="review-card__rating">
							<? for ($i = 1; $i <= 5; $i++) : ?>
								<svg class="<?= ($i <= $arItem["PROPERTIES"]["RATING"]["VALUE"]) ? "active" : "" ?>" width="20" height="20" viewBox="0 0 20 20" role="img" aria-hidden="true" focusable="false">
									<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-star"></use>
								</svg>
							<? endfor; ?>
						</div>
					<? endif; ?>
					<? if ($arItem["~PREVIEW_TEXT"]): ?>
						<div class="review-card__text"><?= $arItem["~PREVIEW_TEXT"] ?></div>
					<? endif; ?>
					<button class="clear-btn" type="button">Читать полностью
						<svg width="20" height="20" viewBox="0 0 40 38" role="img" aria-hidden="true" focusable="false">
							<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
						</svg></button>
					<div class="review-card__author">
						<? if ($arItem["PREVIEW_PICTURE"]["SRC"]): ?>
							<img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["NAME"] ?>" width="56" height="56">
						<? endif; ?>
						<p class="review-card__author-name"><?= $arItem["NAME"] ?></p>
						<? if ($arItem["PROPERTIES"]["JOB_TITLE"]["VALUE"] || $arItem["PROPERTIES"]["COMPANY"]["VALUE"]): ?>
							<p class="review-card__author-job"><?= $arItem["PROPERTIES"]["JOB_TITLE"]["VALUE"] ?>, <?= $arItem["PROPERTIES"]["COMPANY"]["VALUE"] ?></p>
						<? endif; ?>
					</div>
				</article>
			</li>
		<? endforeach; ?>
	</ul>
	<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
		<?= $arResult["NAV_STRING"] ?>
	<? endif; ?>
<? endif; ?>