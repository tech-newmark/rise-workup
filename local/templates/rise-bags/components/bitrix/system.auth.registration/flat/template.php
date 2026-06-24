<?php

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2024 Bitrix
 */

use Bitrix\Main\Web\Json;

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if ($arResult["SHOW_SMS_FIELD"] == true) {
	CJSCore::Init('phone_auth');
}
?>

<section class="section bx-authform registration">
	<div class="container">
		<h2 class="title"><?= GetMessage("AUTH_REGISTER") ?></h2>

		<? if (!empty($arParams["~AUTH_RESULT"]["MESSAGE"]) && $arParams["~AUTH_RESULT"]["TYPE"] != "OK"): ?>
			<div class="alert alert-danger alert-line">
				<?= $arParams["~AUTH_RESULT"]["MESSAGE"] ?>
			</div>
		<? endif ?>

		<? if ($arResult["SHOW_EMAIL_SENT_CONFIRMATION"]): ?>
			<div class="alert alert-success alert-line"><?= GetMessage("AUTH_EMAIL_SENT") ?></div>
		<? endif ?>

		<? if (!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"] && $arResult["USE_EMAIL_CONFIRMATION"] === "Y"): ?>
			<div class="alert alert-warning"><?= GetMessage("AUTH_EMAIL_WILL_BE_SENT") ?></div>
		<? endif ?>

		<? if ($arResult["SHOW_SMS_FIELD"] == true): ?>
			<form method="post" class="bx-authform__form" action="<?= $arResult["AUTH_URL"] ?>" name="regform">
				<input type="hidden" name="SIGNED_DATA" value="<?= htmlspecialcharsbx($arResult["SIGNED_DATA"]) ?>" />

				<div class="main-input-wrapper">
					<label for="bx-register-sms-code"><span class="bx-authform-starrequired">*</span><?= GetMessage("main_register_sms_code") ?></label>
					<input class="main-input" type="text" id="bx-register-sms-code" name="SMS_CODE" maxlength="255" value="<?= htmlspecialcharsbx($arResult["SMS_CODE"] ?? '') ?>" autocomplete="off" />
				</div>

				<div class="bx-authform__form-footer">
					<input type="submit" class="main-btn" name="code_submit_button" value="<?= GetMessage("main_register_sms_send") ?>" />
				</div>
			</form>

			<script>
				new BX.PhoneAuth({
					containerId: 'bx_register_resend',
					errorContainerId: 'bx_register_error',
					interval: <?= $arResult["PHONE_CODE_RESEND_INTERVAL"] ?>,
					data: <?= Json::encode([
									'signedData' => $arResult["SIGNED_DATA"],
								]) ?>,
					onError: function(response) {
						var errorNode = BX('bx_register_error');
						errorNode.innerHTML = '';
						for (var i = 0; i < response.errors.length; i++) {
							errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br />';
						}
						errorNode.style.display = '';
					}
				});
			</script>

			<div id="bx_register_error" style="display:none" class="alert alert-danger alert-line"></div>
			<div id="bx_register_resend"></div>

		<? elseif (!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"]): ?>

			<form method="post" class="bx-authform__form" action="<?= $arResult["AUTH_URL"] ?>" name="bform" enctype="multipart/form-data">
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="REGISTRATION" />
				<?= bitrix_sessid_post(); ?>

				<div class="main-input-wrapper">
					<label for="bx-register-user-name"><?= GetMessage("AUTH_NAME") ?></label>
					<input class="main-input" type="text" id="bx-register-user-name" name="USER_NAME" maxlength="255" value="<?= $arResult["USER_NAME"] ?>" />
				</div>

				<div class="main-input-wrapper">
					<label for="bx-register-user-last-name"><?= GetMessage("AUTH_LAST_NAME") ?></label>
					<input class="main-input" type="text" id="bx-register-user-last-name" name="USER_LAST_NAME" maxlength="255" value="<?= $arResult["USER_LAST_NAME"] ?>" />
				</div>

				<div class="main-input-wrapper">
					<label for="bx-register-user-login"><?= GetMessage("AUTH_LOGIN_MIN") ?><span class="bx-authform-starrequired">*</span></label>
					<input class="main-input" type="text" id="bx-register-user-login" name="USER_LOGIN" maxlength="255" value="<?= $arResult["USER_LOGIN"] ?>" />
				</div>

				<div class="main-input-wrapper">
					<label for="bx-register-user-password"><?= GetMessage("AUTH_PASSWORD_REQ") ?><span class="bx-authform-starrequired">*</span></label>
					<? if ($arResult["SECURE_AUTH"]): ?>
						<div class="alert alert-warning alert-line">
							<?= GetMessage("AUTH_SECURE_NOTE") ?>
						</div>
					<? endif ?>
					<input class="main-input" type="password" id="bx-register-user-password" name="USER_PASSWORD" maxlength="255" value="<?= $arResult["USER_PASSWORD"] ?>" autocomplete="off" />
				</div>

				<div class="main-input-wrapper">
					<label for="bx-register-user-confirm-password"><?= GetMessage("AUTH_CONFIRM") ?><span class="bx-authform-starrequired">*</span></label>
					<? if ($arResult["SECURE_AUTH"]): ?>
						<div class="alert alert-warning alert-line">
							<?= GetMessage("AUTH_SECURE_NOTE") ?>
						</div>
					<? endif ?>
					<input class="main-input" type="password" id="bx-register-user-confirm-password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?= $arResult["USER_CONFIRM_PASSWORD"] ?>" autocomplete="off" />
				</div>

				<? if (!empty($arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"])): ?>
					<div class="alert alert-warning alert-line">
						*<?= $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]; ?>
					</div>
				<? endif ?>

				<? if ($arResult["EMAIL_REGISTRATION"]): ?>
					<div class="main-input-wrapper">
						<label for="bx-register-user-email">
							<? if ($arResult["EMAIL_REQUIRED"]): ?><? endif ?>
							<?= GetMessage("AUTH_EMAIL") ?><span class="bx-authform-starrequired">*</span>
						</label>
						<input class="main-input" type="text" id="bx-register-user-email" name="USER_EMAIL" maxlength="255" value="<?= $arResult["USER_EMAIL"] ?>" />
					</div>
				<? endif ?>

				<? if ($arResult["PHONE_REGISTRATION"]): ?>
					<div class="main-input-wrapper">
						<label for="bx-register-user-phone">
							<? if ($arResult["PHONE_REQUIRED"]): ?><span class="bx-authform-starrequired">*</span><? endif ?>
							<?= GetMessage("main_register_phone_number") ?>
						</label>
						<input class="main-input" type="text" id="bx-register-user-phone" name="USER_PHONE_NUMBER" maxlength="255" value="<?= $arResult["USER_PHONE_NUMBER"] ?>" />
					</div>
				<? endif ?>

				<? if ($arResult["USER_PROPERTIES"]["SHOW"] == "Y"): ?>
					<? foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField): ?>
						<div class="main-input-wrapper">
							<label>
								<? if ($arUserField["MANDATORY"] == "Y"): ?><span class="bx-authform-starrequired">*</span><? endif ?>
								<?= $arUserField["EDIT_FORM_LABEL"] ?>
							</label>
							<?
							$APPLICATION->IncludeComponent(
								"bitrix:system.field.edit",
								$arUserField["USER_TYPE"]["USER_TYPE_ID"],
								array(
									"bVarsFromForm" => $arResult["bVarsFromForm"],
									"arUserField" => $arUserField,
									"form_name" => "bform"
								),
								null,
								array("HIDE_ICONS" => "Y")
							);
							?>
						</div>
					<? endforeach; ?>
				<? endif; ?>

				<? if ($arResult["USE_CAPTCHA"] == "Y"): ?>
					<div class="captcha-block">
						<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />
						<label>
							<span><span class="bx-authform-starrequired">*</span><?= GetMessage("CAPTCHA_REGF_PROMT") ?></span>
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
							<input placeholder="<?= GetMessage("CAPTCHA_REGF_PROMT") ?>" class="main-input" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
						</label>
					</div>
				<? endif ?>


				<? $APPLICATION->IncludeComponent(
					"bitrix:main.userconsent.request",
					"",
					array(
						"ID" => COption::getOptionString("main", "new_user_agreement", ""),
						"IS_CHECKED" => "Y",
						"AUTO_SAVE" => "N",
						"IS_LOADED" => "Y",
						"ORIGINATOR_ID" => $arResult["AGREEMENT_ORIGINATOR_ID"],
						"ORIGIN_ID" => $arResult["AGREEMENT_ORIGIN_ID"],
						"INPUT_NAME" => $arResult["AGREEMENT_INPUT_NAME"],
						"REPLACE" => array(
							"button_caption" => GetMessage("AUTH_REGISTER"),
							"fields" => array(
								rtrim(GetMessage("AUTH_NAME"), ":"),
								rtrim(GetMessage("AUTH_LAST_NAME"), ":"),
								rtrim(GetMessage("AUTH_LOGIN_MIN"), ":"),
								rtrim(GetMessage("AUTH_PASSWORD_REQ"), ":"),
								rtrim(GetMessage("AUTH_EMAIL"), ":"),
							)
						),
					)
				); ?>

				<div class="alert alert-danger alert-line">
					*<?= GetMessage("AUTH_REQ") ?>
				</div>

				<div class="bx-authform__form-footer">
					<input type="submit" class="main-btn" name="Register" value="<?= GetMessage("AUTH_REGISTER") ?>" />
				</div>
			</form>

			<div class="bx-authform__footer">
				<a class="main-link underlined" href="<?= $arResult["AUTH_AUTH_URL"] ?>"><?= GetMessage("AUTH_AUTH") ?></a>
			</div>

			<script>
				try {
					document.bform.USER_NAME.focus();
				} catch (e) {}
			</script>

		<? endif ?>
	</div>
</section>