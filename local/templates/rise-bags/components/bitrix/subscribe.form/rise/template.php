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


<section class="section subscribe" id="subscribe-form">
	<div class="container">
		<div class="subscribe__content">

			<span class="subscribe__title"><?= $arParams["TITLE"] ? $arParams["TITLE"] : GetMessage("SUBSCRIBE_TITLE") ?></span>
			<span class="subscribe__text"><?= $arParams["DESCRIPTION"] ? $arParams["DESCRIPTION"] : GetMessage("SUBSCRIBE_DESCRIPTION") ?></span>

			<? $frame = $this->createFrame('subscribe-form', false)->begin(); ?>
			<form action="<?= $arResult['FORM_ACTION'] ?>">
				<? if ($arParams['HIDE_FIELDS'] !== 'Y'): ?>
					<? foreach ($arResult['RUBRICS'] as $itemValue): ?>
						<label for="sf_RUB_ID_<?= $itemValue['ID'] ?>">
							<input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?= $itemValue['ID'] ?>" value="<?= $itemValue['ID'] ?>" <?= $itemValue['CHECKED'] ? 'checked' : '' ?> /> <?= $itemValue['NAME'] ?>
						</label>
					<? endforeach; ?>
				<? else: ?>
					<? foreach ($arResult['RUBRICS'] as $itemValue): ?>
						<input type="hidden" name="sf_RUB_ID[]" value="<?= $itemValue['ID'] ?>" />
					<? endforeach; ?>
				<? endif; ?>

				<fieldset class="subscribe__fields">
					<label>
						<input type="text" class="main-input" placeholder="E-mail" name="sf_EMAIL" size="20" value="<?= $arResult['EMAIL'] ?>" title="<?= GetMessage('subscr_form_email_title') ?>" />
					</label>
					<input type="submit" class="main-btn" name="OK" value="<?= GetMessage('subscr_form_button') ?>" />
				</fieldset>
			</form>
			<? $frame->beginStub(); ?>

			<form action="<?= $arResult['FORM_ACTION'] ?>">
				<? if ($arParams['HIDE_FIELDS'] !== 'Y'): ?>
					<? foreach ($arResult['RUBRICS'] as $itemValue): ?>
						<label for="sf_RUB_ID_<?= $itemValue['ID'] ?>">
							<input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?= $itemValue['ID'] ?>" value="<?= $itemValue['ID'] ?>" <?= $itemValue['CHECKED'] ? 'checked' : '' ?> /> <?= $itemValue['NAME'] ?>
						</label>
					<? endforeach; ?>
				<? else: ?>
					<? foreach ($arResult['RUBRICS'] as $itemValue): ?>
						<input type="hidden" name="sf_RUB_ID[]" value="<?= $itemValue['ID'] ?>" />
					<? endforeach; ?>
				<? endif; ?>

				<fieldset class="subscribe__fields">
					<label>
						<input type="text" class="main-input" placeholder="E-mail" name="sf_EMAIL" size="20" value="<?= $arResult['EMAIL'] ?>" title="<?= GetMessage('subscr_form_email_title') ?>" />
					</label>
					<input type="submit" class="main-btn" name="OK" value="<?= GetMessage('subscr_form_button') ?>" />
				</fieldset>
			</form>

			<? $frame->end(); ?>
		</div>
	</div>
</section>