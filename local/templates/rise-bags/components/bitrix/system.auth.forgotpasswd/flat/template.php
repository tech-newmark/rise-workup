<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */

//one css for all system.auth.* forms
// $APPLICATION->SetAdditionalCSS("/bitrix/css/main/system.auth/flat/style.css");
?>

<section class="section bx-authform">
	<div class="container">
		<h2 class="title"><?= GetMessage("AUTH_FORGOT_TITLE") ?></h2>

		<? if (!empty($arParams["~AUTH_RESULT"]["MESSAGE"]) && $arParams["~AUTH_RESULT"]["TYPE"] != "OK"): ?>
			<div class="alert alert-line alert-danger">
				<?= $arParams["~AUTH_RESULT"]["MESSAGE"] ?>
			</div>
		<? endif ?>

		<? if (!empty($arParams["~AUTH_RESULT"]["MESSAGE"]) && $arParams["~AUTH_RESULT"]["TYPE"] == "OK"): ?>
			<div class="alert alert-success">
				<?= $arParams["~AUTH_RESULT"]["MESSAGE"] ?>
			</div>
		<? endif ?>

		<form name="bform" class="bx-authform__form" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">
			<? if ($arResult["BACKURL"] <> ''): ?>
				<input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>" />
			<? endif ?>

			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="SEND_PWD">
			<input type="hidden" name="USER_EMAIL" />
			<?= bitrix_sessid_post(); ?>

			<div class="main-input-wrapper">
				<label for="bx-authform-user-login"><?= GetMessage("AUTH_LOGIN_EMAIL") ?></label>
				<input class="main-input" type="text" name="USER_LOGIN" id="bx-authform-user-login" maxlength="255" value="<?= $arResult["USER_LOGIN"] ?>" />
				<div class="alert alert-warning alert-line"><small>*<?= GetMessage("forgot_pass_email_note") ?></small></div>
			</div>

			<? if ($arResult["PHONE_REGISTRATION"]): ?>
				<div class="main-input-wrapper">
					<label for="bx-authform-user-phone"><?= GetMessage("forgot_pass_phone_number") ?></label>
					<input class="main-input" type="text" name="USER_PHONE_NUMBER" id="bx-authform-user-phone" maxlength="255" value="<?= $arResult["USER_PHONE_NUMBER"] ?>" />
					<div class="alert alert-warning alert-line"><small>*<?= GetMessage("forgot_pass_phone_number_note") ?></small></div>
				</div>
			<? endif ?>

			<? if ($arResult["USE_CAPTCHA"]): ?>
				<div class="captcha-block">
					<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />

					<label>
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
						<input placeholder="<?= GetMessage("system_auth_captcha") ?>" class="main-input" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
					</label>
				</div>
			<? endif ?>

			<div class="bx-authform__form-footer">
				<input type="submit" class="main-btn" name="send_account_info" value="<?= GetMessage("AUTH_SEND") ?>" />
			</div>
		</form>

		<div class="bx-authform__footer">
			<a class="main-link underlined" href="<?= $arResult["AUTH_AUTH_URL"] ?>"><?= GetMessage("AUTH_AUTH") ?></a>

			<? if ($arResult["NEW_USER_REGISTRATION"] == "Y"): ?>
				<small><?= GetMessage("AUTH_FIRST_ONE") ?></small>
				<a class="main-link underlined" href="<?= $arResult["AUTH_REGISTER_URL"] ?>" rel="nofollow"><?= GetMessage("AUTH_REGISTER") ?></a>
			<? endif ?>
		</div>
	</div>
</section>

<script>
	document.bform.onsubmit = function() {
		document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;
	};

	try {
		document.bform.USER_LOGIN.focus();
	} catch (e) {}
</script>