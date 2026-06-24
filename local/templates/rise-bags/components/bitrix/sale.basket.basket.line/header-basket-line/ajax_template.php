<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->IncludeLangFile('template.php');

$cartId = $arParams['cartId'];

require(realpath(__DIR__) . '/top_template.php');

if ($arParams["SHOW_PRODUCTS"] == "Y" && ($arResult['NUM_PRODUCTS'] > 0 || !empty($arResult['CATEGORIES']['DELAY']))): ?>

	<div data-role="basket-item-list" class="bx-basket-line-list-wrapper">

		<div id="<?= $cartId ?>products" class="bx-basket-line-list">
			<? foreach ($arResult["CATEGORIES"] as $category => $items):
				if (empty($items)) continue;
			?>

				<div class="bx-basket-line-list-header">
					<span><?= GetMessage("TSB1_$category") ?></span>
				</div>

				<? foreach ($items as $item): ?>
					<div class="bx-basket-line-list-item">
						<button class="bx-basket-line-list-item-remove" onclick="<?= $cartId ?>.removeItemFromCart(<?= $item['ID'] ?>)" title="<?= GetMessage("TSB1_DELETE") ?>">
							<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#cross-icon' ?> "></use>
							</svg>
						</button>
						<div class="bx-basket-line-list-item-header">
							<? if ($arParams["SHOW_IMAGE"] == "Y" && $item["PICTURE_SRC"]): ?>
								<? if ($item["DETAIL_PAGE_URL"]): ?>
									<a href="<?= $item["DETAIL_PAGE_URL"] ?>"><img src="<?= $item["PICTURE_SRC"] ?>" alt="<?= $item["NAME"] ?>"></a>
								<? else: ?>
									<img src="<?= $item["PICTURE_SRC"] ?>" alt="<?= $item["NAME"] ?>" />
								<? endif ?>
							<? endif ?>
							<? if ($item["DETAIL_PAGE_URL"]): ?>
								<a class="bx-basket-line-list-item-title" href="<?= $item["DETAIL_PAGE_URL"] ?>"><?= $item["NAME"] ?></a>
							<? else: ?>
								<span class="bx-basket-line-list-item-title"><?= $item["NAME"] ?></span>
							<? endif ?>
						</div>

						<? if (true): ?>
							<div class="bx-basket-line-list-item-price-block">

								<? if ($arParams["SHOW_PRICE"] == "Y"): ?>
									<span class="bx-basket-line-list-item-price bx-basket-line-list-item-price--current"><?= $item["PRICE_FMT"] ?></span>

									<? if ($item["FULL_PRICE"] != $item["PRICE_FMT"]): ?>
										<div class="bx-basket-line-list-item-price bx-basket-line-list-item-price--old"><?= $item["FULL_PRICE"] ?></div>
									<? endif ?>
								<? endif ?>

								<? if ($arParams["SHOW_SUMMARY"] == "Y"): ?>
									<div class="bx-basket-line-list-item-price-summary">
										<strong><?= $item["QUANTITY"] ?></strong> <?= $item["MEASURE_NAME"] ?> <?= GetMessage("TSB1_SUM") ?> <strong><?= $item["SUM"] ?></strong>
									</div>
								<? endif ?>
							</div>
						<? endif ?>

					</div>
				<? endforeach ?>
			<? endforeach ?>
		</div>

		<? if ($arParams["PATH_TO_ORDER"] && $arResult["CATEGORIES"]["READY"]): ?>
			<a href="<?= $arParams["PATH_TO_ORDER"] ?>" class="main-btn"><?= GetMessage("TSB1_2ORDER") ?></a>
		<? endif ?>

		<? if ($arParams["POSITION_FIXED"] == "Y"): ?>
			<div id="<?= $cartId ?>status" class="bx-basket-line-list-action" onclick="<?= $cartId ?>.toggleOpenCloseCart()"><?= GetMessage("TSB1_COLLAPSE") ?></div>
		<? endif ?>

	</div>

	<script>
		BX.ready(function() {
			<?= $cartId ?>.fixCart();
		});
	</script>
<? endif; ?>