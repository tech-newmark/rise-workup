<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var string  $templateFolder
 * @var array $templateData
 * @var CatalogSectionComponent $component
 */

global $APPLICATION;
includeComponentAssets('catalog.top/littleweb');

$APPLICATION->AddHeadScript($templateFolder . '/section/script.js');
// $APPLICATION->SetAdditionalCSS($templateFolder . '/section/style.css');

if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY'])) {
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
<?
	}
}
?>