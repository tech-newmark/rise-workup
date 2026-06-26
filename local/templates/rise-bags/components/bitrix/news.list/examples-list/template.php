<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

// includeComponentAssets('news.list/examples-list');
?>

<? if ($arResult["ITEMS"]): ?>
	<section class="section examples">
		<div class="container">
			<h2>Примеры наших работ</h2>
			<div class="swiper examples-slider">
				<div class="swiper-wrapper">
					<? foreach ($arResult["ITEMS"] as $arItem):
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
						<div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
							<div class="examples-item">
								<p class="examples-item__name"><?= $arItem["NAME"] ?></p>
								<img class="examples-item__img" data-fancybox="gallery-slider" src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= (($arItem["PREVIEW_PICTURE"]["DESCRIPTION"]) ? ($arItem["PREVIEW_PICTURE"]["DESCRIPTION"]) : $arItem["NAME"]) ?>" width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>" height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>">
								<button class="main-btn" type="button" data-form-id="4" data-product-sku="<?= $arItem["NAME"] ?>">Заказать</button>
							</div>
						</div>
					<? endforeach; ?>
				</div>
				<div class="swiper-pagination"></div>
			</div>
		</div>
	</section>
<? endif; ?>