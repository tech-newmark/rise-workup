<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$stars = 5;
?>

<? if ($arResult["ITEMS"]): ?>
	<ul class="company-ratings">
		<? foreach ($arResult["ITEMS"] as $arItem):
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
			<? $starsFilled = (!empty($arItem["PROPERTIES"]["STARS_FILLED"]["VALUE"])) ? $arItem["PROPERTIES"]["STARS_FILLED"]["VALUE"] : 5; ?>
			<li id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
				<a class="company-rating" href="<?= $arItem["CODE"] ?>" target="_blank" rel="nofollow noreferrer noopener">
					<img class="company-rating__logo" src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="" width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>" height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>">
					<div class="company-rating__content">
						<span class="company-rating__name">Рейтинг в <?= $arItem["NAME"] ?></span>
						<span class="company-rating__value"><?= $arItem["PREVIEW_TEXT"] ?></span>
						<div class="company-rating__stars">
							<? for ($i = 1; $i <= $stars; $i++): ?>
								<svg class="company-rating__star <?= ($i <= $starsFilled) ? 'company-rating__star--filled' : '' ?>" width="14" height="14" viewBox="0 0 40 28" role="img" aria-hidden="true" focusable="false">
									<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-star"></use>
								</svg>
							<? endfor; ?>
						</div>
						<span class="company-rating__text">по отзывам клиентов</span>
					</div>
				</a>
			</li>
		<? endforeach; ?>
	</ul>
<? endif; ?>
<?/* debug($arResult) */ ?>