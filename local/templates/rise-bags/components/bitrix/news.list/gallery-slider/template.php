<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

// includeComponentAssets('news.list/gallery-slider');
?>
<? if ($arResult["ITEMS"]): ?>
	<div class="gallery">
		<div class="swiper gallery-slider <?= $arParams["CUSTOM_CLASS"] ?>">
			<div class="swiper-wrapper">
				<? foreach ($arResult["ITEMS"] as $arItem):
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>
					<div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
						<div class="gallery-item <?= ($arParams["USE_FANCY"]) ? "gallery-item--fancybox" : "" ?>">
							<img class="gallery-item__img" <?= ($arParams["USE_FANCY"]) ? 'data-fancybox="gallery-slider"' : "" ?> src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= (($arItem["PREVIEW_PICTURE"]["DESCRIPTION"]) ? ($arItem["PREVIEW_PICTURE"]["DESCRIPTION"]) : $arItem["NAME"]) ?>" width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>" height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>">
							<? if ($arParams["SHOW_FOOTER"] === "Y"): ?>
								<div class="gallery-item__footer">
									<? if ($arItem["PREVIEW_TEXT"]): ?>
										<span><?= $arItem["PREVIEW_TEXT"] ?></span>
									<? endif; ?>
									<? if ($arItem["DETAIL_TEXT"]): ?>
										<span><?= $arItem["DETAIL_TEXT"] ?></span>
									<? endif; ?>
								</div>
							<? endif; ?>


						</div>
					</div>
				<? endforeach; ?>
			</div>
			<div class="swiper-pagination"></div>
		</div>
	<? endif; ?>