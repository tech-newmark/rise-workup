<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @var CMain $APPLICATION */
/** @var CUser $USER */
/** @var CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<? if ($arResult['ID'] == 0 && empty($_REQUEST['action']) || CSubscription::IsAuthorized($arResult['ID'])): ?>
	<section class="section subscribe" id="subscribe-form">
		<div class="container">
			<div class="subscribe__content">
				<span class="subscribe__title"><?= $arParams["TITLE"] ? $arParams["TITLE"] : GetMessage("SUBSCRIBE_TITLE") ?></span>
				<? if ($arResult['ID'] > 0): ?>
					<span class="subscribe__text"><?= $arParams["ANSWER"] ? $arParams["ANSWER"] : GetMessage("SUBSCRIBE_ANSWER") ?></span>
				<? else: ?>
					<span class="subscribe__text"><?= $arParams["DESCRIPTION"] ? $arParams["DESCRIPTION"] : GetMessage("SUBSCRIBE_DESCRIPTION") ?></span>
					<form action="<?= $arResult['FORM_ACTION'] ?>" method="post">
						<?= bitrix_sessid_post(); ?>
						<fieldset class="subscribe__fields">
							<label>
								<input class="main-input" type="text" name="EMAIL" value="<?= $arResult['SUBSCRIPTION']['EMAIL'] != '' ? $arResult['SUBSCRIPTION']['EMAIL'] : $arResult['REQUEST']['EMAIL']; ?>" />
							</label>

							<input class="main-btn" type="submit" name="Save" value="<?= ($arResult['ID'] > 0 ? GetMessage('subscr_upd') : GetMessage('subscr_add')) ?>" />
						</fieldset>

						<? foreach ($arResult['ERROR'] as $itemValue): ?>
							<div class="alert alert-danger "><small><?= $itemValue ?></small></div>
						<? endforeach; ?>

						<? foreach ($arResult['RUBRICS'] as $itemValue): ?>
							<label>
								<input type="hidden" name="RUB_ID[]" value="<?= $itemValue['ID'] ?>" checked />
							</label>
						<? endforeach; ?>

						<input type="hidden" name="FORMAT" value="html">
						<input type="hidden" name="PostAction" value="Add" />
						<input type="hidden" name="ID" value="<?= $arResult['SUBSCRIPTION']['ID']; ?>" />
						<? if ($_REQUEST['register'] == 'YES'): ?>
							<input type="hidden" name="register" value="YES" />
						<? endif; ?>
						<? if ($_REQUEST['authorize'] == 'YES'): ?>
							<input type="hidden" name="authorize" value="YES" />
						<? endif; ?>
					</form>
				<? endif; ?>
			</div>
		</div>
	</section>
<? endif; ?>