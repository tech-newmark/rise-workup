<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

?>

<? if ($arResult["ITEMS"]): ?>
	<section class="section reviews">
		<div class="container">
			<div class="reviews__header">
				<h1><?= $arResult['NAME'] ?></h1>
				<? if ($arResult['DESCRIPTION']): ?>
					<p><?= $arResult['DESCRIPTION'] ?></p>
				<? endif; ?>
			</div>

			<div class="reviews__list">
				<? foreach ($arResult["ITEMS"] as $index => $arItemParams): ?>
					<?
					$this->AddEditAction($arItemParams['ID'], $arItemParams['EDIT_LINK'], CIBlock::GetArrayByID($arItemParams["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItemParams['ID'], $arItemParams['DELETE_LINK'], CIBlock::GetArrayByID($arItemParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<div class="reviews__list-item" id="<?= $this->GetEditAreaId($arItemParams['ID']); ?>">
						<? $APPLICATION->IncludeComponent(
							"custom:cards",
							"review-card",
							$arItemParams + [
								"FILTER_NAME" => "tagFilter",
							],
							$component,
							array("HIDE_ICONS" => $index > 0 ?? "Y")
						); ?>
					</div>
				<? endforeach; ?>
				<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
					<?= $arResult["NAV_STRING"] ?>
				<? endif; ?>
			</div>
		</div>
	</section>
<? endif; ?>