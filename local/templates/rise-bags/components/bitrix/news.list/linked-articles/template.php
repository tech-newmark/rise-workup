<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult["ITEMS"]): ?>
	<section class="linked-articles">
		<div class="linked-articles__header">
			<h2><?= $arParams["TITLE_IN_LINKED_ARTICLES"] ? $arParams["TITLE_IN_LINKED_ARTICLES"] : 'Рекомендуем' ?></h2>
			<? if ($arParams["DESC_IN_LINKED_ARTICLES"]) : ?>
				<span><?= $arParams["DESC_IN_LINKED_ARTICLES"] ?></span>
			<? endif; ?>
			<a class="main-btn" href="<?= $arResult['LIST_PAGE_URL'] ?>"><span><?= $arParams["BUTTON_NAME_IN_LINKED_ARTICLES"] ? $arParams["BUTTON_NAME_IN_LINKED_ARTICLES"] : 'Смотреть все' ?></span></a>
		</div>
		<div class="linked-articles__list">
			<? foreach ($arResult["ITEMS"] as $index => $arItemParams): ?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>
				<div class="linked-articles__item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
					<? $APPLICATION->IncludeComponent(
						"custom:cards",
						"article-card",
						$arItemParams + [
							"SHOW_DATE_ACTIVE_FROM" => $arParams["SHOW_DATE_ACTIVE_FROM"],
							"SHOW_DATE_ACTIVE_TO" => $arParams["SHOW_DATE_ACTIVE_TO"],
						],
						$component,
						array("HIDE_ICONS" => $index > 0 ?? "Y")
					); ?>
				</div>
			<? endforeach; ?>
		</div>
	</section>
<? endif; ?>