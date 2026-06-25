<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

// Передаю заголовок и описание раздела в section_horizontal.php
$this->setFrameMode(true);

$this->SetViewTarget("SECTION_HEADER");
?>

<h1 class="title"><?= $arResult["NAME"] ?></h1>

<? if ($arResult["UF_PREVIEW_DESCR"] && $arResult["UF_PREVIEW_DESCR"] !== ""): ?>
	<div class="content">
		<?= $arResult['~UF_PREVIEW_DESCR'] ?>
	</div>
<? endif; ?>


<? $this->EndViewTarget();
// Передаю теги раздела в section_horizontal.php
$this->setFrameMode(true);
$this->SetViewTarget("SECTION_TAGS");

$tagSections = [];
$currentSectionId = (int)(($arResult['ID'] ?? 0) ?: ($arParams['SECTION_ID'] ?? 0));
$currentSectionDepth = (int)($arResult['DEPTH_LEVEL'] ?? 0);

if ($currentSectionDepth === 1 && \Bitrix\Main\Loader::includeModule('iblock')) {
	$getTagSections = static function ($parentSectionId) use ($arParams): array {
		$filter = [
			'IBLOCK_ID' => (int)$arParams['IBLOCK_ID'],
			'ACTIVE' => 'Y',
			'GLOBAL_ACTIVE' => 'Y',
			'UF_IS_TAB' => 1,
		];

		$filter['SECTION_ID'] = $parentSectionId ? (int)$parentSectionId : false;

		$result = [];
		$sectionsIterator = CIBlockSection::GetList(
			['SORT' => 'ASC', 'NAME' => 'ASC'],
			$filter,
			false,
			['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'CODE', 'SECTION_PAGE_URL', 'UF_IS_TAB']
		);

		while ($section = $sectionsIterator->GetNext()) {
			if (!empty($arParams['SECTION_URL'])) {
				$codePath = [];
				$navChainIterator = CIBlockSection::GetNavChain(
					(int)$arParams['IBLOCK_ID'],
					(int)$section['ID'],
					['ID', 'CODE']
				);

				while ($navSection = $navChainIterator->GetNext()) {
					$codePath[] = $navSection['CODE'];
				}

				$section['SECTION_PAGE_URL'] = CComponentEngine::MakePathFromTemplate(
					$arParams['SECTION_URL'],
					[
						'SECTION_ID' => $section['ID'],
						'SECTION_CODE' => $section['CODE'],
						'SECTION_CODE_PATH' => implode('/', $codePath),
					]
				);
			}

			$result[] = $section;
		}

		return $result;
	};

	$tagSections = $getTagSections($currentSectionId);
}
?>

<? if ($tagSections): ?>
	<div class="catalog-tags">
		<button class="swiper-button-prev catalog-tags__button catalog-tags__button--prev" type="button" aria-label="Предыдущий тег">
			<svg width="13" height="13" viewBox="0 0 13 13" role="img" aria-hidden="true" focusable="false">
				<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
			</svg>
		</button>

		<div class="swiper catalog-tags__slider">
			<div class="swiper-wrapper">
				<? foreach ($tagSections as $tagSection): ?>
					<a class="swiper-slide main-btn outlined<?= (int)$tagSection['ID'] === $currentSectionId ? ' current' : '' ?>" href="<?= htmlspecialcharsbx($tagSection['SECTION_PAGE_URL']) ?>">
						<?= $tagSection['NAME'] ?>
					</a>
				<? endforeach; ?>
			</div>
		</div>
		<button class="swiper-button-next catalog-tags__button catalog-tags__button--next" type="button" aria-label="Следующий тег">
			<svg width="13" height="13" viewBox="0 0 13 13" role="img" aria-hidden="true" focusable="false">
				<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#arrow-sm"></use>
			</svg>
		</button>
	</div>
	<script>
		(function() {
			function initCatalogTagsFallback() {
				if (window.initCatalogTagsSliders && window.initCatalogTagsSliders()) {
					return true;
				}

				var sliders = document.querySelectorAll('.catalog-tags__slider');
				if (!sliders.length || typeof window.Swiper === 'undefined') {
					return false;
				}

				sliders.forEach(function(slider) {
					if (slider.dataset.catalogTagsSliderInited) {
						return;
					}

					slider.dataset.catalogTagsSliderInited = 'true';
					var tagsBlock = slider.closest('.catalog-tags');
					var btnPrev = tagsBlock ? tagsBlock.querySelector('.catalog-tags__button--prev') : null;
					var btnNext = tagsBlock ? tagsBlock.querySelector('.catalog-tags__button--next') : null;

					new window.Swiper(slider, {
						slidesPerView: 'auto',
						spaceBetween: 12,
						watchOverflow: true,
						navigation: {
							prevEl: btnPrev,
							nextEl: btnNext
						}
					});
				});

				return true;
			}

			if (initCatalogTagsFallback()) {
				return;
			}

			var attempts = 0;
			var interval = setInterval(function() {
				attempts += 1;

				if (initCatalogTagsFallback() || attempts >= 30) {
					clearInterval(interval);
				}
			}, 100);
		})();
	</script>
<? endif; ?>


<? $this->EndViewTarget();


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

$showPager = false;
$showLazyLoad = false;

if ($arParams['PAGE_ELEMENT_COUNT'] > 0 && $navParams['NavPageCount'] > 1) {
	$showPager = $arParams['DISPLAY_BOTTOM_PAGER'];
	$showLazyLoad = $arParams['LAZY_LOAD'] === 'Y' && $navParams['NavPageNomer'] != $navParams['NavPageCount'];
}

$templateLibrary = array('popup', 'ajax', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

unset($currencyList, $templateLibrary);

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

$arParams['MESS_BTN_LAZY_LOAD'] = $arParams['MESS_BTN_LAZY_LOAD'] ?: Loc::getMessage('CT_BCS_CATALOG_MESS_BTN_LAZY_LOAD');

$obName = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
$containerName = 'container-' . $navParams['NavNum'];
?>

<div class="catalog-section">

	<!-- items-container -->
	<? if (!empty($arResult['ITEMS'])):
		$generalParams = [
			'SHOW_SLIDER' => $arParams["SHOW_SLIDER"],
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
			'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
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
		<div class="catalog-section-grid" data-entity="<?= $containerName ?>">
			<? foreach ($arResult['ITEMS'] as $item):
				$uniqueId = $item['ID'] . '_' . md5($this->randString() . $component->getAction());
				$areaId = $this->GetEditAreaId($uniqueId);
				$this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
				$this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);

				$itemParameters = [
					'SKU_PROPS' => $arResult['SKU_PROPS'][$item['IBLOCK_ID']],
					'MESS_NOT_AVAILABLE' => ($arResult['MODULES']['catalog'] && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE
						? $arParams['~MESS_NOT_AVAILABLE_SERVICE']
						: $arParams['~MESS_NOT_AVAILABLE']
					),
				];
			?>
				<div class="catalog-section-grid-item" data-entity="items-row">
					<?
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
			<? endforeach; ?>
		</div>
	<? else:
		// load css for bigData/deferred load
		$APPLICATION->IncludeComponent(
			'bitrix:catalog.item',
			'littleweb',
			array(),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
	endif; ?>
	<!-- items-container -->
</div>

<? if ($showLazyLoad): ?>
	<div class="row bx-<?= $arParams['TEMPLATE_THEME'] ?>">
		<div class="btn btn-default btn-lg center-block" style="margin: 15px;"
			data-use="show-more-<?= $navParams['NavNum'] ?>">
			<?= $arParams['MESS_BTN_LAZY_LOAD'] ?>
		</div>
	</div>
<? endif; ?>

<? if ($showPager): ?>
	<div data-pagination-num="<?= $navParams['NavNum'] ?>">
		<!-- pagination-container -->
		<?= $arResult['NAV_STRING'] ?>
		<!-- pagination-container -->
	</div>
<? endif; ?>

<? if (!isset($arParams['HIDE_SECTION_DESCRIPTION']) || $arParams['HIDE_SECTION_DESCRIPTION'] !== 'Y'): ?>
	<? if (!empty(trim($arResult['DESCRIPTION']))): ?>
		<div class="content">
			<? if ($arResult["DESCRIPTION_TYPE"] === 'html') : ?>
				<?= $arResult['~DESCRIPTION'] ?>
			<? else: ?>
				<p class="text"><?= $arResult['~DESCRIPTION'] ?></p>
			<? endif; ?>
		</div>
	<? endif; ?>
<? endif; ?>

<?
$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'catalog.section');
$signedParams = $signer->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'catalog.section');
?>
<script>
	BX.message({
		BTN_MESSAGE_BASKET_REDIRECT: '<?= GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
		BASKET_URL: '<?= $arParams['BASKET_URL'] ?>',
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
	var <?= $obName ?> = new JCCatalogSectionComponent({
		siteId: '<?= CUtil::JSEscape($component->getSiteId()) ?>',
		componentPath: '<?= CUtil::JSEscape($componentPath) ?>',
		navParams: <?= CUtil::PhpToJSObject($navParams) ?>,
		deferredLoad: false,
		initiallyShowHeader: '<?= !empty($arResult['ITEMS']) ?>',
		bigData: <?= CUtil::PhpToJSObject($arResult['BIG_DATA']) ?>,
		lazyLoad: !!'<?= $showLazyLoad ?>',
		loadOnScroll: !!'<?= ($arParams['LOAD_ON_SCROLL'] === 'Y') ?>',
		template: '<?= CUtil::JSEscape($signedTemplate) ?>',
		ajaxId: '<?= CUtil::JSEscape($arParams['AJAX_ID'] ?? '') ?>',
		parameters: '<?= CUtil::JSEscape($signedParams) ?>',
		container: '<?= $containerName ?>'
	});
</script>
<!-- component-end -->