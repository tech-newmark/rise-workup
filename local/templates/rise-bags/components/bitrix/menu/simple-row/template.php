<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if ($arResult): ?>

	<ul class="simple-row-menu">
		<? foreach ($arResult as $arItem): ?>
			<li>
				<a <?= $arItem["SELECTED"] ? 'class="selected"' : '' ?> href="<?= $arItem["LINK"] ?>">
					<?= $arItem["TEXT"] ?>
				</a>
			</li>
		<? endforeach; ?>
	</ul>
<? endif; ?>