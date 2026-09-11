<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
includeComponentAssets([
	'catalog.section/catalog-filtered',
	'catalog.item/littleweb',
]);
/**
 * @var array $arParams
 * @var array $templateData
 * @var string $templateFolder
 * @var CatalogSectionComponent $component
 */

global $APPLICATION;

if (!empty($templateData['TEMPLATE_LIBRARY'])) {
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES'])) {
		$loadCurrency = \Bitrix\Main\Loader::includeModule('currency');
	}

	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);

	if ($loadCurrency) {
?>
		<script>
			BX.Currency.setCurrencies(<?= $templateData['CURRENCIES'] ?>);
		</script>
<?php
	}
}

//	lazy load and big data json answers
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
if ($request->isAjaxRequest() && ($request->get('action') === 'showMore' || $request->get('action') === 'deferredLoad')) {
	$content = ob_get_contents();
	ob_end_clean();

	$itemsParts = explode('<!-- items-container -->', (string)$content, 3);
	$itemsContainer = $itemsParts[1] ?? '';
	$paginationContainer = '';
	if (!empty($templateData['USE_PAGINATION_CONTAINER'])) {
		$paginationParts = explode('<!-- pagination-container -->', (string)$content, 3);
		$paginationContainer = $paginationParts[1] ?? '';
	}
	$epilogueParts = explode('<!-- component-end -->', (string)$content, 2);
	$epilogue = $epilogueParts[1] ?? '';

	if (isset($arParams['AJAX_MODE']) && $arParams['AJAX_MODE'] === 'Y') {
		$component->prepareLinks($paginationContainer);
	}

	$component::sendJsonAnswer(array(
		'items' => $itemsContainer,
		'pagination' => $paginationContainer,
		'epilogue' => $epilogue,
	));
}
