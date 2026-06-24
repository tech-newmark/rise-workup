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

$itemCount = count($arResult);
$needReload = (isset($_REQUEST["compare_list_reload"]) && $_REQUEST["compare_list_reload"] == "Y");
$idCompareCount = 'compareList' . $this->randString();
$obCompare = 'ob' . $idCompareCount;

$isHidden = isset($arParams["POSITION_FIXED"]) ? $arParams["POSITION_FIXED"] : 'N';
$showComparedList = isset($arParams["SHOW_COMPARED_LIST"]) ? $arParams["SHOW_COMPARED_LIST"] : 'N';
?>

<div id="<?= $idCompareCount; ?>" class="bx-catalog-compare<?= $itemCount > 0 ? ' active' : '' ?>" <?= $isHidden === "N" ? 'style="display:none;"' : '' ?>>
	<?
	if ($needReload) {
		$APPLICATION->RestartBuffer();
	}
	$frame = $this->createFrame($idCompareCount)->begin('');
	if ($itemCount > 0):
	?>
		<div class="bx-catalog-compare-header">
			<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
				<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-compare'></use>
			</svg>
			<span>
				<?=
				GetMessage(
					'CP_BCCL_TPL_MESS_COMPARE_COUNT_MSGVER_2',
					[
						'#COUNT_NUMBER#' => '<span data-block="count">' . $itemCount . '</span>',
					]
				);
				?>
			</span>
		</div>

		<? if ($showComparedList === "Y"): ?>
			<ul class="bx-catalog-compare-list" data-block="item-list">
				<? foreach ($arResult as $arElement): ?>
					<li data-block="item-row" data-row-id="row<?= $arElement['PARENT_ID']; ?>">
						<a href="<?= $arElement["DETAIL_PAGE_URL"] ?>"><?= $arElement["NAME"] ?></a></td>
						<button type="button" data-id="<?= $arElement['PARENT_ID']; ?>" rel="nofollow"><?= GetMessage("CATALOG_DELETE") ?></button>
					</li>
				<? endforeach; ?>
			</ul>
		<? endif; ?>

		<a class="bx-catalog-compare-link" href="<?= $arParams["COMPARE_URL"]; ?>"><?= GetMessage('CP_BCCL_TPL_MESS_COMPARE_PAGE'); ?></a>


	<?
	endif;
	$frame->end();
	if ($needReload) {
		die();
	}

	$currentPath = CHTTP::urlDeleteParams(
		$APPLICATION->GetCurPageParam(),
		array(
			$arParams['PRODUCT_ID_VARIABLE'],
			$arParams['ACTION_VARIABLE'],
			'ajax_action'
		),
		array("delete_system_params" => true)
	);

	$jsParams = array(
		'VISUAL' => array(
			'ID' => $idCompareCount,
		),
		'AJAX' => array(
			'url' => $currentPath,
			'params' => array(
				'ajax_action' => 'Y'
			),
			'reload' => array(
				'compare_list_reload' => 'Y'
			),
			'templates' => array(
				'delete' => (mb_strpos($currentPath, '?') === false ? '?' : '&') . $arParams['ACTION_VARIABLE'] . '=DELETE_FROM_COMPARE_LIST&' . $arParams['PRODUCT_ID_VARIABLE'] . '='
			)
		)

	);
	?>
</div>

<script>
	var <?= $obCompare; ?> = new JCCatalogCompareList(<?= \Bitrix\Main\Web\Json::encode($jsParams) ?>)
</script>