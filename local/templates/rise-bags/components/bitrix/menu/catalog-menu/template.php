<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="catalog-menu">

	<a href="/catalog/" class="main-btn catalog-menu-opener">
		<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
			<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#catalog-icon"></use>
		</svg>
		<span>Каталог</span>
	</a>

	<? if (!empty($arResult)): ?>
		<div class="catalog-menu-wrapper">

			<ul class="catalog-menu-sidelist">
				<? foreach ($arResult as $index => $item): ?>

					<li data-id="<?= $index ?>">

						<a href="<?= $item["PARENT"]["LINK"] ?>">
							<img src="<?= $item["PARENT"]["IMAGE_SRC"] ?>" alt="" width="40" height="40">
							<?= $item["PARENT"]["TEXT"] ?></a>
					</li>
				<? endforeach; ?>
			</ul>

			<div class="catalog-menu-main">
				<? foreach ($arResult as $index => $item): ?>
					<ul data-id="<?= $index ?>" class="catalog-menu-mainlist">

						<? foreach ($item["CHILD"] as $child): ?>
							<li>

								<a href="<?= $child["LINK"] ?>"><?= $child["TEXT"] ?></a>
							</li>
						<? endforeach; ?>

					</ul>
				<? endforeach; ?>
			</div>

		</div>
	<? endif ?>
</div>
