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

	$sortOffers = static function (array $offers, array $jsOffers, array $params): array {
		$getSortValue = static function (array $offer, string $field) {
			$field = trim($field);

			if ($field === '') {
				return null;
			}

			$upperField = mb_strtoupper($field);
			if (array_key_exists($upperField, $offer)) {
				return $offer[$upperField];
			}

			if (array_key_exists($field, $offer)) {
				return $offer[$field];
			}

			if (mb_substr($upperField, 0, 9) === 'PROPERTY_') {
				$propertyCode = mb_substr($upperField, 9);
				$property = $offer['PROPERTIES'][$propertyCode]
					?? $offer['DISPLAY_PROPERTIES'][$propertyCode]
					?? null;

				if (is_array($property)) {
					return $property['VALUE']
						?? $property['DISPLAY_VALUE']
						?? $property['VALUE_ENUM']
						?? $property['SORT']
						?? null;
				}
			}

			return null;
		};

		$normalizeValue = static function ($value) {
			if (is_array($value)) {
				$value = reset($value);
			}

			return $value;
		};

		$compareByField = static function (array $left, array $right, string $field, string $order) use ($getSortValue, $normalizeValue): int {
			$leftValue = $normalizeValue($getSortValue($left, $field));
			$rightValue = $normalizeValue($getSortValue($right, $field));

			if ($leftValue === $rightValue) {
				return 0;
			}

			if ($leftValue === null || $leftValue === '') {
				return 1;
			}

			if ($rightValue === null || $rightValue === '') {
				return -1;
			}

			if (is_numeric($leftValue) && is_numeric($rightValue)) {
				$result = (float)$leftValue <=> (float)$rightValue;
			} else {
				$result = strnatcasecmp((string)$leftValue, (string)$rightValue);
			}

			return mb_strtoupper($order) === 'DESC' ? -$result : $result;
		};

		$sortRules = [
			[
				(string)($params['OFFERS_SORT_FIELD'] ?? 'SORT'),
				(string)($params['OFFERS_SORT_ORDER'] ?? 'ASC'),
			],
			[
				(string)($params['OFFERS_SORT_FIELD2'] ?? 'ID'),
				(string)($params['OFFERS_SORT_ORDER2'] ?? 'DESC'),
			],
		];

		usort($offers, static function (array $left, array $right) use ($sortRules, $compareByField): int {
			foreach ($sortRules as $rule) {
				$result = $compareByField($left, $right, $rule[0], $rule[1]);

				if ($result !== 0) {
					return $result;
				}
			}

			return 0;
		});

		if (!empty($jsOffers)) {
			$jsOffersById = [];

			foreach ($jsOffers as $jsOffer) {
				$jsOffersById[(int)$jsOffer['ID']] = $jsOffer;
			}

			$sortedJsOffers = [];

			foreach ($offers as $offer) {
				$offerId = (int)$offer['ID'];

				if (isset($jsOffersById[$offerId])) {
					$sortedJsOffers[] = $jsOffersById[$offerId];
					unset($jsOffersById[$offerId]);
				}
			}

			$jsOffers = array_merge($sortedJsOffers, array_values($jsOffersById));
		}

		return [$offers, $jsOffers];
	};

	[$arResult['OFFERS'], $arResult['JS_OFFERS']] = $sortOffers(
		$arResult['OFFERS'],
		is_array($arResult['JS_OFFERS'] ?? null) ? $arResult['JS_OFFERS'] : [],
		$arParams
	);

	if (!empty($arResult['SKU_PROPS']) && is_array($arResult['SKU_PROPS'])) {
		foreach ($arResult['SKU_PROPS'] as &$skuProperty) {
			if (
				empty($skuProperty['ID'])
				|| empty($skuProperty['VALUES'])
				|| !is_array($skuProperty['VALUES'])
				|| !isset($arResult['OFFERS_PROP'][$skuProperty['CODE']])
			) {
				continue;
			}

			$propertyTreeKey = 'PROP_' . $skuProperty['ID'];
			$valueSortMap = [];

			foreach ($arResult['OFFERS'] as $offerIndex => $offer) {
				$valueId = (string)($offer['TREE'][$propertyTreeKey] ?? '');

				if ($valueId !== '' && !isset($valueSortMap[$valueId])) {
					$valueSortMap[$valueId] = $offerIndex;
				}
			}

			if (empty($valueSortMap)) {
				continue;
			}

			uasort($skuProperty['VALUES'], static function (array $left, array $right) use ($valueSortMap): int {
				$leftId = (string)($left['ID'] ?? '');
				$rightId = (string)($right['ID'] ?? '');
				$leftSort = $valueSortMap[$leftId] ?? PHP_INT_MAX;
				$rightSort = $valueSortMap[$rightId] ?? PHP_INT_MAX;

				if ($leftSort !== $rightSort) {
					return $leftSort <=> $rightSort;
				}

				$leftValueSort = (int)($left['SORT'] ?? 500);
				$rightValueSort = (int)($right['SORT'] ?? 500);

				if ($leftValueSort !== $rightValueSort) {
					return $leftValueSort <=> $rightValueSort;
				}

				return (int)($left['ID'] ?? 0) <=> (int)($right['ID'] ?? 0);
			});
		}
		unset($skuProperty);
	}

	if ($offerId > 0) {
		foreach ($arResult['OFFERS'] as $offerIndex => $offer) {
			if ((int)$offer['ID'] === $offerId) {
				$arResult['OFFERS_SELECTED'] = $offerIndex;
				break;
			}
		}
	} else {
		$arResult['OFFERS_SELECTED'] = 0;
	}
}
