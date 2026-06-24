<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

// global $vite;

// $componentName = 'sale.personal.section/rise';
// $cssPath = $vite->getComponentCss($componentName);
// $jsPath = $vite->getComponentJs($componentName);

// if ($cssPath) $this->addExternalCss($cssPath);
// if ($jsPath) $this->addExternalJs($jsPath);
includeComponentAssets('sale.personal.section/rise');

if ($arParams["MAIN_CHAIN_NAME"] !== '') {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams["MAIN_CHAIN_NAME"]), $arResult['SEF_FOLDER']);
}

$availablePages = $arResult["AVAILABLE_PAGES"];

if (empty($availablePages)): {
		ShowError(Loc::getMessage("SPS_ERROR_NOT_CHOSEN_ELEMENT"));
	}
else:
	// debug($arResult);
?>
	<section class="personal">
		<div class="container">
			<h2 class="title"><?= $APPLICATION->ShowTitle() ?></h2>
			<div class="grid">
				<? foreach ($availablePages as $blockElement): ?>
					<a class="personal__item" href="<?= htmlspecialcharsbx($blockElement['path']) ?>">
						<svg width='40' height='40' role='img' aria-hidden='true' focusable='false'>
							<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#<?= $blockElement['icon'] ?>'></use>
						</svg>
						<span> <?= htmlspecialcharsbx($blockElement['name']) ?></h2>
					</a>
				<? endforeach; ?>
			</div>
		</div>
	</section>

<? endif; ?>