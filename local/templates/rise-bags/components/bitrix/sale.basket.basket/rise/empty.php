<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>

<section class="section bx-basket-empty">
	<div class="container">

		<img src="<?= $templateFolder . '/images/empty_cart.svg' ?>" alt="Пустая корзина" width="320" height="320">

		<h2 class="title"><?= Loc::getMessage("SBB_EMPTY_BASKET_TITLE") ?></h2>
		<? if (!empty($arParams['EMPTY_BASKET_HINT_PATH'])): ?>
			<div class="alert alert-line">
				<?= Loc::getMessage(
					'SBB_EMPTY_BASKET_HINT',
					[
						'#A1#' => '<a href="' . $arParams['EMPTY_BASKET_HINT_PATH'] . '">',
						'#A2#' => '</a>',
					]
				) ?>
			</div>
		<? endif; ?>
	</div>
</section>