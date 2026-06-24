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

<? if ($arResult["TABS"]): ?>
	<div class="news-tabs-row">
		<form name="<?= $arResult["FILTER_NAME"] . "_form" ?>" action="<?= $arResult["FORM_ACTION"] ?>" method="get">
			<? foreach ($arResult["ITEMS"] as $arItem):
				if (array_key_exists("HIDDEN", $arItem)):
					echo $arItem["INPUT"];
				endif;
			endforeach; ?>

			<input type="hidden" name="arrFilter_DATE_ACTIVE_FROM_1" value="">
			<input type="hidden" name="arrFilter_DATE_ACTIVE_FROM_2" value="">

			<input class="main-btn" type="submit" name="set_filter" value="Все новости" />
		</form>
		<? foreach ($arResult["TABS"] as $arTab): ?>
			<form name="<?= $arResult["FILTER_NAME"] . "_form" ?>" action="<?= $arResult["FORM_ACTION"] ?>" method="get">
				<? foreach ($arResult["ITEMS"] as $arItem):
					if (array_key_exists("HIDDEN", $arItem)):
						echo $arItem["INPUT"];
					endif;
				endforeach; ?>

				<input type="hidden" name="arrFilter_DATE_ACTIVE_FROM_1" value="<?= $arTab["VALUE_FROM"] ?>">
				<input type="hidden" name="arrFilter_DATE_ACTIVE_FROM_2" value="<?= $arTab["VALUE_TO"] ?>">
				<input class="main-btn" type="submit" name="set_filter" value="<?= $arTab["YEAR"] ?>" />
			</form>
		<? endforeach; ?>
	</div>
<? endif; ?>