<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if (!empty($arResult)): ?>
	<div class="catalog-menu">

		<a href="/catalog/" class="main-btn catalog-menu-opener">
			<svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
				<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#catalog-icon"></use>
			</svg>
			<span>Каталог</span>
		</a>

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
	</div>
<? endif ?>

<?/*
			$menuItems = [];
			foreach ($arResult as $item) {
				$depthLevel = (int)$item["DEPTH_LEVEL"];
				if ($depthLevel === 1 || $depthLevel >= 4) {
					continue;
				}
				$item["RENDER_LEVEL"] = $depthLevel - 1;
				$menuItems[] = $item;
			}

			$itemCount = count($menuItems);
			for ($i = 0; $i < $itemCount; $i++):
				$arItem = $menuItems[$i];
				$currentLevel = (int)$arItem["RENDER_LEVEL"];
				$nextLevel = isset($menuItems[$i + 1]) ? (int)$menuItems[$i + 1]["RENDER_LEVEL"] : 0;
			?>

				<? if ($arItem["IS_PARENT"]): ?>

					<li>
						<a href="<?= $arItem["LINK"] ?>" class="parent"><?= $arItem["TEXT"] ?></a>
						<ul class="sublist">
						<? else: ?>
							<li>
								<a href="<?= $arItem["LINK"] ?>" class="<?= $arItem["SELECTED"] ? 'root-item-selected' : 'root-item' ?>"><?= $arItem["TEXT"] ?></a>
								</li>
							<? endif ?>

				<? if ($nextLevel < $currentLevel): ?>
					<?= str_repeat("</ul></li>", ($currentLevel - $nextLevel)); ?>
				<? endif ?>

			<? endfor */ ?>