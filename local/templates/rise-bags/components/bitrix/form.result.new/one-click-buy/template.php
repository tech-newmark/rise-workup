<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="popup-form oneclickbuy">

	<!-- <? debug($arParams["PRODUCT_DATA"]) ?> -->
	<? if ($arResult["FORM_NOTE"]): ?>
		<?= $arResult["FORM_NOTE"] ?>
	<? else: ?>
		<?= $arResult["FORM_HEADER"] ?>

		<div class="popup-form__header">
			<? if ($arResult["isFormTitle"]): ?>
				<span class="popup-form__header-title"><?= $arResult["FORM_TITLE"] ?></span>
			<? endif; ?>

			<? if ($arResult["isFormDescription"] && $arResult["isFormDescription"] == "Y"): ?>
				<span class="popup-form__header-text"><?= $arResult["FORM_DESCRIPTION"] ?></span>
			<? endif; ?>
		</div>

		<div class="oneclickbuy__grid">
			<div class="oneclickbuy__grid-item oneclickbuy__grid-item--product">

				<img
					src="<?= $arParams["PRODUCT_DATA"]["PRODUCT_IMG"] ?>"
					alt="<?= $arParams["PRODUCT_DATA"]["PRODUCT_TITLE"] ?>"
					width="280" height="280">

				<span class="oneclickbuy__product-title"><?= $arParams["PRODUCT_DATA"]["PRODUCT_TITLE"] ?></span>

				<div class="oneclickbuy__product-props">
					<? foreach ($arParams["PRODUCT_DATA"]["PROPERTIES"] as $prop): ?>
						<div class="oneclickbuy__product-props-item">
							<span><?= $prop["NAME"] ?></span>
							<? if ($prop["DIRECTORY_DATA"]): ?>
								<span><img src="<?= $prop["DIRECTORY_DATA"]["FILE_PATH"] ?>" alt="<?= $prop["VALUE"] ?>" width="60" height="60" /></span>
							<? else: ?>
								<span><?= $prop["VALUE"] ?></span>
							<? endif; ?>
						</div>
					<? endforeach; ?>

					<div class="oneclickbuy__product-props-item">
						<span>Стоимость:</span>
						<span>
							<?= $arParams["PRODUCT_DATA"]["PRODUCT_PRICE_FORMATTED"] ?>
						</span>
					</div>
				</div>
			</div>

			<div class="oneclickbuy__grid-item">
				<div class="popup-form__content">
					<? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion): ?>
						<? if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden'):
							echo $arQuestion["HTML_CODE"];
						else: ?>
							<? if ($arQuestion["STRUCTURE"][0]["FIELD_TYPE"] === "text" || $arQuestion["STRUCTURE"][0]["FIELD_TYPE"] === "email"):
								$isProductField = isset($arParams["PRODUCT_DATA"][$FIELD_SID]);
							?>
								<? if ($isProductField): ?>
									<input type="hidden"
										name="<?= 'form_' . $arQuestion["STRUCTURE"][0]["FIELD_TYPE"] . "_" . $arQuestion["STRUCTURE"][0]["ID"] ?>"
										value="<?= htmlspecialchars($arParams["PRODUCT_DATA"][$FIELD_SID]) ?>">
								<? else: ?>
									<div class="main-input-wrapper<?= ($arResult["FORM_ERRORS"][$FIELD_SID] ? ' invalid-fld' : '') ?>">
										<label>
											<?= $arQuestion["HTML_CODE"] ?>
										</label>
									</div>
								<? endif; ?>
							<? endif; ?>

							<? if ($arQuestion["STRUCTURE"][0]["FIELD_TYPE"] === "checkbox"): ?>
								<div class="main-switcher-wrapper<?= ($arResult["FORM_ERRORS"][$FIELD_SID] ? ' invalid-fld' : '') ?>">
									<label for="<?= $arQuestion["STRUCTURE"][0]["ID"] ?>">
										<span>
											<?= $arQuestion["CAPTION"] ?>&nbsp;<span class="required-mark"><?= ($arQuestion["REQUIRED"] == "Y" ? '*' : '') ?></span>
										</span>
										<input type="checkbox" id="<?= $arQuestion["STRUCTURE"][0]["ID"] ?>" value="<?= $arQuestion["STRUCTURE"][0]["ID"] ?>" name="<?= 'form_' . $arQuestion["STRUCTURE"][0]["FIELD_TYPE"] . '_' . $FIELD_SID . '[]' ?>">
									</label>
								</div>
							<? endif; ?>

							<? if ($arQuestion["STRUCTURE"][0]["FIELD_TYPE"] === "textarea"): ?>
								<div class="main-textarea-wrapper">
									<label>
										<?= $arQuestion["HTML_CODE"] ?>
									</label>
								</div>
							<? endif; ?>
						<? endif; ?>
					<? endforeach; ?>

					<? if ($arResult["isUseCaptcha"] == "Y"): ?>
						<div class="captcha-block">
							<input type="hidden" name="captcha_sid" value="<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>" />
							<label>
								<div class="captcha-block-wrapper<?= ($arResult["FORM_ERRORS"][$FIELD_SID] ? ' invalid-fld' : '') ?>">
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>" width="180" height="40" alt="Капча" />
									<input placeholder="Текст с картинки" type="text" name="captcha_word" size="30" maxlength="50" value="" />
								</div>
							</label>
						</div>
					<? endif; ?>

					<input class="main-btn" type="submit" name="web_form_submit" value="<?= $arResult["arForm"]["BUTTON"] ?>" />
					<small> <?= $arResult["REQUIRED_SIGN"]; ?> - <?= GetMessage("FORM_REQUIRED_FIELDS") ?> </small>
				</div>
			</div>
		</div>

		<?= $arResult["FORM_FOOTER"] ?>
	<? endif; ?>
</div>