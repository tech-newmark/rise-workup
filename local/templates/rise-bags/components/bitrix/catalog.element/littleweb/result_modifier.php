<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

if (!empty($arResult['OFFERS']) && is_array($arResult['OFFERS'])) {
	$request = Context::getCurrent()->getRequest();
	$offerId = (int)($request->getQuery('offer') ?: $request->getQuery('offer_id'));

	if ($offerId > 0) {
		foreach ($arResult['OFFERS'] as $offerIndex => $offer) {
			if ((int)$offer['ID'] === $offerId) {
				$arResult['OFFERS_SELECTED'] = $offerIndex;
				break;
			}
		}
	}
}
