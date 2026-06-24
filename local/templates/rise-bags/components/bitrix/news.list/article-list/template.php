<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult["ITEMS"]): ?>
	<section class="section articles">
		<div class="container">
			<div class="articles__header">
				<h1><?= $arResult['NAME'] ?></h1>
				<? if ($arResult['DESCRIPTION']): ?>
					<p><?= $arResult['DESCRIPTION'] ?></p>
				<? endif; ?>
			</div>

			<div class="articles__list">
				<? foreach ($arResult["ITEMS"] as $index => $arItemParams): ?>
					<?
					$this->AddEditAction($arItemParams['ID'], $arItemParams['EDIT_LINK'], CIBlock::GetArrayByID($arItemParams["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItemParams['ID'], $arItemParams['DELETE_LINK'], CIBlock::GetArrayByID($arItemParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<div class="articles-item" id="<?= $this->GetEditAreaId($arItemParams['ID']); ?>">
						<? $APPLICATION->IncludeComponent(
							"custom:cards",
							"article-card",
							$arItemParams + [
								"FILTER_NAME" => "arrFilter",
								"SHOW_DATE_ACTIVE_FROM" => $arParams["SHOW_DATE_ACTIVE_FROM"],
								"SHOW_DATE_ACTIVE_TO" => $arParams["SHOW_DATE_ACTIVE_TO"],
							],
							$component,
							array("HIDE_ICONS" => $index > 0 ?? "Y")
						); ?>
					</div>
				<? endforeach; ?>
			</div>
			<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
				<?= $arResult["NAV_STRING"] ?>
			<? endif; ?>
		</div>
	</section>
<? endif; ?>