<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

/** @var array $arResult */

?>
<section class="section order-cancel">
	<div class="container">
		<a class="main-btn" href=" <?= $arResult["URL_TO_LIST"] ?>"><?= GetMessage("SALE_RECORDS_LIST") ?></a>
		<div class="bx_my_order_cancel">
			<?php
			if ($arResult["ERROR_MESSAGE"] == ''):
			?>
				<form method="post" action="<?= POST_FORM_ACTION_URI ?>">
					<input type="hidden" name="CANCEL" value="Y">
					<?= bitrix_sessid_post() ?>
					<input type="hidden" name="ID" value="<?= $arResult["ID"] ?>">
					<p>
						<?= GetMessage("SALE_CANCEL_ORDER1") ?>
						<a href="<?= $arResult["URL_TO_DETAIL"] ?>"><?= GetMessage("SALE_CANCEL_ORDER2") ?> #<?= $arResult["ACCOUNT_NUMBER"] ?></a>?
						<b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b>
					</p>
					<p>
						<?= GetMessage("SALE_CANCEL_ORDER4") ?>:
					</p>
					<div class="main-textarea-wrapper">
						<textarea name="REASON_CANCELED"></textarea>
					</div>
					<input class="main-btn outlined" type="submit" name="action" value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>">
				</form>
			<?php
			else:
				ShowError($arResult["ERROR_MESSAGE"]);
			endif;
			?>
		</div>
	</div>
</section>