<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

includeComponentAssets('catalog.item/littleweb');

use Bitrix\Main\Loader;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $templateData
 */

if (!empty($templateData['TEMPLATE_LIBRARY'])) {
	$loadCurrency = false;

	if (!empty($templateData['CURRENCIES'])) {
		$loadCurrency = Loader::includeModule('currency');
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

// check compared state
if ($arParams['DISPLAY_COMPARE']) {
	$compared = false;
	$comparedIds = array();
	$item = $templateData['ITEM'];

	if (!empty($_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS'])) {
		if (!empty($item['JS_OFFERS']) && is_array($item['JS_OFFERS'])) {
			foreach ($item['JS_OFFERS'] as $key => $offer) {
				if (array_key_exists($offer['ID'], $_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS'])) {
					if ($key == $item['OFFERS_SELECTED']) {
						$compared = true;
					}

					$comparedIds[] = $offer['ID'];
				}
			}
		} elseif (array_key_exists($item['ID'], $_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS'])) {
			$compared = true;
		}
	}

	if ($templateData['JS_OBJ']) {
?>
		<script>
			BX.ready(BX.defer(function() {
				if (!!window.<?= $templateData['JS_OBJ'] ?>) {
					window.<?= $templateData['JS_OBJ'] ?>.setCompared('<?= $compared ?>');
					<?php
					if (!empty($comparedIds)):
					?>
						window.<?= $templateData['JS_OBJ'] ?>.setCompareInfo(<?= CUtil::PhpToJSObject($comparedIds, false, true) ?>);
					<?php
					endif;
					?>
				}
			}));
		</script>
<?php
	}
}
