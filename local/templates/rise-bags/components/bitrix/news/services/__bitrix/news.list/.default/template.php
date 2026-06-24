<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
?>
<? if ($arResult["ITEMS"]): ?>
	<section class="section">
		<div class="container">
			<section>
				<h2>Выберите продукцию для оптовой закупки</h2>
				<p class="base-text">Перейдите в нужный раздел, чтобы ознакомиться с ассортиментом и оставить заявку на получение условий поставки.</p>
				<div class="news-list">

					<? foreach ($arResult["ITEMS"] as $arItem): ?>
						<?
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						?>

						<article class="service-card" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">

							<? if ($arItem["PREVIEW_PICTURE"]["SRC"]): ?>
								<img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["PREVIEW_PICTURE"]["DESCRIPTION"] ?: $arItem["NAME"] ?>">
							<? endif; ?>
							<h3><?= $arItem["NAME"] ?></h3>
							<? if ($arItem["PREVIEW_TEXT"]): ?>
								<p><?= $arItem["PREVIEW_TEXT"] ?></p>
							<? endif; ?>
							<a class="main-btn" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">Подробнее</a>
						</article>
					<? endforeach; ?>
			</section>


		</div>
		</div>
	</section>
<? endif; ?>