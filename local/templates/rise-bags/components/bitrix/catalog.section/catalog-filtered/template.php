<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

$this->setFrameMode(true);

$isTabAjax = ($arParams['IS_TAB_AJAX'] ?? 'N') === 'Y';

if (!empty($arResult['NAV_RESULT'])) {
	$navParams = array(
		'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
		'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
		'NavNum' => $arResult['NAV_RESULT']->NavNum
	);
} else {
	$navParams = array(
		'NavPageCount' => 1,
		'NavPageNomer' => 1,
		'NavNum' => $this->randString()
	);
}

$showLazyLoad = false;

if ($arParams['PAGE_ELEMENT_COUNT'] > 0 && $navParams['NavPageCount'] > 1) {
	$showLazyLoad = $arParams['LAZY_LOAD'] === 'Y' && $navParams['NavPageNomer'] != $navParams['NavPageCount'];
}

$templateLibrary = array('popup', 'ajax', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = [
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'USE_PAGINATION_CONTAINER' => false,
];
unset($currencyList, $templateLibrary);

$positionClass = static function ($position) {
	$classes = [];
	foreach (explode('-', (string)$position) as $part) {
		if (in_array($part, ['left', 'center', 'right', 'bottom', 'middle', 'top'], true)) {
			$classes[] = 'product-item-label-' . $part;
		}
	}
	return implode(' ', $classes);
};
$labelPositionClass = $positionClass($arParams['LABEL_PROP_POSITION'] ?? '');
$discountPositionClass = ($arParams['SHOW_DISCOUNT_PERCENT'] ?? 'N') === 'Y'
	? $positionClass($arParams['DISCOUNT_PERCENT_POSITION'] ?? '')
	: '';
unset($positionClass);

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$arParams['~MESS_BTN_BUY'] = ($arParams['~MESS_BTN_BUY'] ?? '') ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_BUY');
$arParams['~MESS_BTN_DETAIL'] = ($arParams['~MESS_BTN_DETAIL'] ?? '') ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_DETAIL');
$arParams['~MESS_BTN_COMPARE'] = ($arParams['~MESS_BTN_COMPARE'] ?? '') ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_COMPARE');
$arParams['~MESS_BTN_SUBSCRIBE'] = ($arParams['~MESS_BTN_SUBSCRIBE'] ?? '') ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_SUBSCRIBE');
$arParams['~MESS_BTN_ADD_TO_BASKET'] = ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? '') ?: Loc::getMessage('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET');
$arParams['~MESS_NOT_AVAILABLE'] = ($arParams['~MESS_NOT_AVAILABLE'] ?? '') ?: Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE');
$arParams['~MESS_NOT_AVAILABLE_SERVICE'] = ($arParams['~MESS_NOT_AVAILABLE_SERVICE'] ?? '') ?: Loc::getMessage('CP_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE_SERVICE');
$arParams['~MESS_SHOW_MAX_QUANTITY'] = ($arParams['~MESS_SHOW_MAX_QUANTITY'] ?? '') ?: Loc::getMessage('CT_BCS_CATALOG_SHOW_MAX_QUANTITY');
$arParams['~MESS_RELATIVE_QUANTITY_MANY'] = ($arParams['~MESS_RELATIVE_QUANTITY_MANY'] ?? '') ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = ($arParams['MESS_RELATIVE_QUANTITY_MANY'] ?? '') ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['~MESS_RELATIVE_QUANTITY_FEW'] = ($arParams['~MESS_RELATIVE_QUANTITY_FEW'] ?? '') ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_FEW');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = ($arParams['MESS_RELATIVE_QUANTITY_FEW'] ?? '') ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_FEW');

$arParams['MESS_BTN_LAZY_LOAD'] = ($arParams['MESS_BTN_LAZY_LOAD'] ?? '') ?: Loc::getMessage('CT_BCS_CATALOG_MESS_BTN_LAZY_LOAD');

// DOM identity must not depend on the navigation counter of a separate AJAX request.
$containerName = 'catalog-filtered-' . $this->randString();
$tabs = [
	'popular' => Loc::getMessage('CATALOG_FILTERED_POPULAR'),
	'new' => Loc::getMessage('CATALOG_FILTERED_NEW'),
	'hit' => Loc::getMessage('CATALOG_FILTERED_HIT'),
];
?>

<?php if (!$isTabAjax): ?>
	<section class="section catalog-filtered" data-catalog-filtered
		data-ajax-url="/local/ajax/catalog-filtered-section.php"
		data-loading-message="<?= htmlspecialcharsbx(Loc::getMessage('CT_BCS_CATALOG_BTN_MESSAGE_LAZY_LOAD_WAITER')) ?>"
		data-error-message="<?= htmlspecialcharsbx(Loc::getMessage('CATALOG_FILTERED_ERROR')) ?>"
		data-retry-message="<?= htmlspecialcharsbx(Loc::getMessage('CATALOG_FILTERED_RETRY')) ?>">
		<h2 class="title"><?= htmlspecialcharsbx(Loc::getMessage('CATALOG_FILTERED_TITLE')) ?></h2>
		<div class="catalog-filtered__tabs" role="tablist" aria-label="<?= htmlspecialcharsbx(Loc::getMessage('CATALOG_FILTERED_TITLE')) ?>">
			<?php foreach ($tabs as $tab => $label): ?>
				<button type="button" class="main-btn<?= $tab === 'popular' ? '' : ' outlined' ?>"
					role="tab" data-catalog-tab="<?= $tab ?>"
					aria-selected="<?= $tab === 'popular' ? 'true' : 'false' ?>"
					tabindex="<?= $tab === 'popular' ? '0' : '-1' ?>">
					<?= htmlspecialcharsbx($label) ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div data-catalog-panel="popular" data-loaded="true" role="tabpanel" tabindex="0">
<?php endif; ?>

		<div data-catalog-list="<?= $containerName ?>">
		<!-- items-container -->
		<div class="catalog-filtered__grid" data-entity="<?= $containerName ?>">
			<?php if (!empty($arResult['ITEMS'])):
				$generalParams = [
					'SHOW_SLIDER' => $arParams["SHOW_SLIDER"],
					'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
					'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
					'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
					'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
					'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
					'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
					'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
					'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
					'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
					'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
					'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],
					'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
					'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
					'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
					'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
					'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'],
					'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
					'COMPARE_PATH' => $arParams['COMPARE_PATH'],
					'COMPARE_NAME' => $arParams['COMPARE_NAME'],
					'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
					'PRODUCT_BLOCKS_ORDER' => $arParams['PRODUCT_BLOCKS_ORDER'],
					'LABEL_POSITION_CLASS' => $labelPositionClass,
					'DISCOUNT_POSITION_CLASS' => $discountPositionClass,
					'~BASKET_URL' => $arParams['~BASKET_URL'],
					'~ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
					'~BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
					'~COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
					'~COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
					'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
					'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
					'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
					'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY'],
					'MESS_BTN_BUY' => $arParams['~MESS_BTN_BUY'],
					'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
					'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
					'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
					'MESS_BTN_ADD_TO_BASKET' => $arParams['~MESS_BTN_ADD_TO_BASKET'],
					// custom
					'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
					'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP']
				];
			?>
				<?php foreach ($arResult['ITEMS'] as $item):
					$uniqueId = $item['ID'] . '_' . md5($this->randString() . $component->getAction());
					$areaId = $this->GetEditAreaId($uniqueId);
					$this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
					$this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);

					$itemParameters = [
						'SKU_PROPS' => $arResult['SKU_PROPS'][$item['IBLOCK_ID']] ?? [],
						'MESS_NOT_AVAILABLE' => ($arResult['MODULES']['catalog'] && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE
							? $arParams['~MESS_NOT_AVAILABLE_SERVICE']
							: $arParams['~MESS_NOT_AVAILABLE']
						),
					];
				?>
					<div data-entity="items-row">
						<?php
						$APPLICATION->IncludeComponent(
							'bitrix:catalog.item',
							'littleweb',
							array(
								'RESULT' => array(
									'ITEM' => $item,
									'AREA_ID' => $areaId,
								),
								'PARAMS' => $generalParams + $itemParameters,
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
						?>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<p class="catalog-filtered__empty"><?= htmlspecialcharsbx(Loc::getMessage('CATALOG_FILTERED_EMPTY')) ?></p>
			<?php endif; ?>
		</div>
		<!-- items-container -->

		<?php if ($showLazyLoad): ?>
			<div class="row bx-<?= $arParams['TEMPLATE_THEME'] ?>">
				<button type="button" class="btn main-btn show-more-btn" data-catalog-show-more>
					<?= htmlspecialcharsbx($arParams['MESS_BTN_LAZY_LOAD']) ?>
				</button>
			</div>
		<?php endif; ?>

		<p class="catalog-filtered__error" data-catalog-list-error role="alert" hidden><?= htmlspecialcharsbx(Loc::getMessage('CATALOG_FILTERED_ERROR')) ?></p>
		</div>

		<?php if (!$isTabAjax): ?>
		</div>
		<div data-catalog-panel="new" role="tabpanel" tabindex="0" hidden></div>
		<div data-catalog-panel="hit" role="tabpanel" tabindex="0" hidden></div>
	</section>
<?php endif; ?>

<?php
$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'catalog.section');
$signedParams = $signer->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'catalog.section');
?>
<script>
	BX.message({
		BTN_MESSAGE_BASKET_REDIRECT: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
		BASKET_URL: '<?= CUtil::JSEscape($arParams['BASKET_URL']) ?>',
		ADD_TO_BASKET_OK: '<?= GetMessageJS('ADD_TO_BASKET_OK') ?>',
		TITLE_ERROR: '<?= GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR') ?>',
		TITLE_BASKET_PROPS: '<?= GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS') ?>',
		TITLE_SUCCESSFUL: '<?= GetMessageJS('ADD_TO_BASKET_OK') ?>',
		BASKET_UNKNOWN_ERROR: '<?= GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		BTN_MESSAGE_SEND_PROPS: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS') ?>',
		BTN_MESSAGE_CLOSE: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		BTN_MESSAGE_CLOSE_POPUP: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP') ?>',
		COMPARE_MESSAGE_OK: '<?= GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK') ?>',
		COMPARE_UNKNOWN_ERROR: '<?= GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
		COMPARE_TITLE: '<?= GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE') ?>',
		PRICE_TOTAL_PREFIX: '<?= GetMessageJS('CT_BCS_CATALOG_PRICE_TOTAL_PREFIX') ?>',
		RELATIVE_QUANTITY_MANY: '<?= CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY']) ?>',
		RELATIVE_QUANTITY_FEW: '<?= CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW']) ?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
		BTN_MESSAGE_LAZY_LOAD: '<?= CUtil::JSEscape($arParams['MESS_BTN_LAZY_LOAD']) ?>',
		BTN_MESSAGE_LAZY_LOAD_WAITER: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_LAZY_LOAD_WAITER') ?>',
		SITE_ID: '<?= CUtil::JSEscape($component->getSiteId()) ?>'
	});
	BX.ready(function () {
		new RiseCatalogFilteredSection({
			siteId: '<?= CUtil::JSEscape($component->getSiteId()) ?>',
			componentPath: '<?= CUtil::JSEscape($componentPath) ?>',
			navParams: <?= CUtil::PhpToJSObject($navParams) ?>,
			deferredLoad: false,
			initiallyShowHeader: '<?= !empty($arResult['ITEMS']) ?>',
			bigData: <?= CUtil::PhpToJSObject($arResult['BIG_DATA'] ?? []) ?>,
			lazyLoad: !!'<?= $showLazyLoad ?>',
			loadOnScroll: !!'<?= ($arParams['LOAD_ON_SCROLL'] === 'Y') ?>',
			template: '<?= CUtil::JSEscape($signedTemplate) ?>',
			ajaxId: '<?= CUtil::JSEscape($arParams['AJAX_ID'] ?? '') ?>',
			parameters: '<?= CUtil::JSEscape($signedParams) ?>',
			container: '<?= $containerName ?>'
		});
	});
</script>
<!-- component-end -->