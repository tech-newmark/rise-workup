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

?>

<section class="section bx-authform">
	<div class="container">
		<h2 class="title"><?= GetMessage("AUTH_PLEASE_AUTH") ?></h2>

		<? if (!empty($arParams["~AUTH_RESULT"]["MESSAGE"])): ?>
			<div class="alert alert-danger alert-line"><?= $arParams["~AUTH_RESULT"]["MESSAGE"] ?></div>
		<? endif  ?>

		<? if (!empty($arResult['ERROR_MESSAGE'])): ?>
			<div class="alert alert-danger alert-line"><?= $arResult['ERROR_MESSAGE'] ?></div>
		<? endif ?>

		<? if ($arResult["AUTH_SERVICES"]): ?>
			<div class="bx-authform__socserv-block">
				<?
				$APPLICATION->IncludeComponent(
					"bitrix:socserv.auth.form",
					"flat",
					array(
						"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
						"AUTH_URL" => $arResult["AUTH_URL"],
						"POST" => $arResult["POST"],
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);
				?>
			</div>
		<? endif ?>

		<form name="form_auth" class="bx-authform__form" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">

			<input type="hidden" name="AUTH_FORM" value="Y" />
			<input type="hidden" name="TYPE" value="AUTH" />
			<?= bitrix_sessid_post(); ?>

			<? if ($arResult["BACKURL"] <> ''): ?>
				<input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>" />
			<? endif ?>
			<? foreach ($arResult["POST"] as $key => $value): ?>
				<input type="hidden" name="<?= $key ?>" value="<?= $value ?>" />
			<? endforeach ?>

			<div class="main-input-wrapper">
				<label for="bx-authform-user-login"><?= GetMessage("AUTH_LOGIN") ?></label>
				<input class="main-input" type="text" name="USER_LOGIN" id="bx-authform-user-login" maxlength="255" value="<?= $arResult["LAST_LOGIN"] ?>" />
			</div>

			<div class="main-input-wrapper">
				<label for="bx-authform-user-pswd"><?= GetMessage("AUTH_PASSWORD") ?></label>
				<input class="main-input" type="password" name="USER_PASSWORD" id="bx-authform-user-pswd" maxlength="255" autocomplete="off" />
			</div>

			<? if ($arResult["CAPTCHA_CODE"]): ?>
				<div class="captcha-block">
					<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />

					<div class="bx-authform-formgroup-container dbg_captha">
						<div class="bx-authform-label-container">
							<?= GetMessage("AUTH_CAPTCHA_PROMT") ?>
						</div>
						<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" /></div>
						<div class="bx-authform-input-container">
							<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
						</div>
					</div>
				</div>
			<? endif; ?>

			<div class="bx-authform__form-footer">
				<? if ($arResult["STORE_PASSWORD"] == "Y"): ?>
					<label class="main-checkbox">
						<input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" />
						<span><?= GetMessage("AUTH_REMEMBER_ME") ?></span>
					</label>
				<? endif ?>

				<input type="submit" class="main-btn" name="Login" value="<?= GetMessage("AUTH_AUTHORIZE") ?>" />
			</div>
		</form>

		<? if ($arParams["NOT_SHOW_LINKS"] != "Y" || $arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y"): ?>
			<noindex>
				<div class="bx-authform__footer">
					<? if ($arParams["NOT_SHOW_LINKS"] != "Y"): ?>
						<a class="main-link underlined" href="<?= $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>" rel="nofollow"><?= GetMessage("AUTH_FORGOT_PASSWORD_2") ?></a>
					<? endif ?>

					<? if ($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y"): ?>
						<small><?= GetMessage("AUTH_FIRST_ONE") ?></small>
						<a class="main-link underlined" href="<?= $arResult["AUTH_REGISTER_URL"] ?>" rel="nofollow"><?= GetMessage("AUTH_REGISTER") ?></a>
					<? endif ?>
				</div>
			</noindex>
		<? endif ?>

	</div>
</section>

<script>
	<? if ($arResult["LAST_LOGIN"] <> ''): ?>
		try {
			document.form_auth.USER_PASSWORD.focus();
		} catch (e) {}
	<? else: ?>
		try {
			document.form_auth.USER_LOGIN.focus();
		} catch (e) {}
	<? endif ?>
</script>