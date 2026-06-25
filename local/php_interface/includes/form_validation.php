<?php

use Bitrix\Main\Loader;

AddEventHandler('form', 'onBeforeResultAdd', 'riseValidateEmailFormFields');

if (!function_exists('riseValidateEmailFormFields')) {
	function riseValidateEmailFormFields($webFormId, &$arFields, &$arrValues)
	{
		if (!Loader::includeModule('form')) {
			return;
		}

		$emailAnswers = riseGetEmailFormAnswers((int)$webFormId);

		foreach ($emailAnswers as $answer) {
			$value = riseGetFormAnswerValue($arrValues, $answer);

			if ($value === '') {
				continue;
			}

			if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				global $APPLICATION;
				$APPLICATION->ThrowException('Введите корректный email');
				return;
			}

			if (riseIsBlockedComEmail($value)) {
				global $APPLICATION;
				$APPLICATION->ThrowException('Почта в зоне .com не принимается');
				return;
			}
		}
	}
}

if (!function_exists('riseGetEmailFormAnswers')) {
	function riseGetEmailFormAnswers(int $webFormId): array
	{
		static $cache = [];

		if (isset($cache[$webFormId])) {
			return $cache[$webFormId];
		}

		$result = [];
		$fieldsIterator = CFormField::GetList($webFormId, 'N', 's_sort', 'asc', ['ACTIVE' => 'Y']);

		while ($field = $fieldsIterator->Fetch()) {
			$answersIterator = CFormAnswer::GetList((int)$field['ID'], 's_sort', 'asc', ['ACTIVE' => 'Y']);

			while ($answer = $answersIterator->Fetch()) {
				$fieldParam = (string)($answer['FIELD_PARAM'] ?? '');
				$fieldType = (string)($answer['FIELD_TYPE'] ?? '');

				if (
					stripos($fieldParam, 'data-type="email"') === false
					&& stripos($fieldParam, "data-type='email'") === false
					&& strcasecmp($fieldType, 'email') !== 0
				) {
					continue;
				}

				$result[] = [
					'ID' => (int)$answer['ID'],
					'FIELD_TYPE' => $fieldType,
				];
			}
		}

		$cache[$webFormId] = $result;

		return $result;
	}
}

if (!function_exists('riseIsBlockedComEmail')) {
	function riseIsBlockedComEmail(string $value): bool
	{
		return (bool)preg_match('/@[^@\s]+\.com$/i', trim($value));
	}
}

if (!function_exists('riseIsEmailFormQuestion')) {
	function riseIsEmailFormQuestion(array $arQuestion): bool
	{
		$answer = $arQuestion['STRUCTURE'][0] ?? [];
		$fieldParam = (string)($answer['FIELD_PARAM'] ?? '');
		$fieldType = (string)($answer['FIELD_TYPE'] ?? '');

		return stripos($fieldParam, 'data-type="email"') !== false
			|| stripos($fieldParam, "data-type='email'") !== false
			|| strcasecmp($fieldType, 'email') === 0;
	}
}

if (!function_exists('riseFormQuestionHasBlockedEmailValue')) {
	function riseFormQuestionHasBlockedEmailValue(array $arQuestion, array $arrValues): bool
	{
		if (!riseIsEmailFormQuestion($arQuestion)) {
			return false;
		}

		$answer = $arQuestion['STRUCTURE'][0] ?? [];
		$value = riseGetFormAnswerValue($arrValues, [
			'ID' => (int)($answer['ID'] ?? 0),
			'FIELD_TYPE' => (string)($answer['FIELD_TYPE'] ?? ''),
		]);

		return $value !== '' && riseIsBlockedComEmail($value);
	}
}

if (!function_exists('riseGetFormAnswerValue')) {
	function riseGetFormAnswerValue(array $arrValues, array $answer): string
	{
		$answerId = (int)$answer['ID'];
		$fieldType = (string)$answer['FIELD_TYPE'];
		$valueKeys = [
			'form_' . $fieldType . '_' . $answerId,
			'form_text_' . $answerId,
			'form_email_' . $answerId,
		];

		foreach ($valueKeys as $valueKey) {
			if (!array_key_exists($valueKey, $arrValues)) {
				continue;
			}

			$value = $arrValues[$valueKey];

			if (is_array($value)) {
				$value = reset($value);
			}

			return trim((string)$value);
		}

		return '';
	}
}
