<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if (!empty($arResult)): ?>
	<nav class="mobile-menu">

		<ul class="mobile-menu__list">
			<?
			$previousLevel = 0;
			foreach ($arResult as $arItem): ?>

				<? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel): ?>
					<?= str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"])); ?>
				<? endif ?>

				<? if ($arItem["IS_PARENT"]): ?>
					<? if ($arItem["DEPTH_LEVEL"] == 1): ?>

						<li>
							<a href="<?= $arItem["LINK"] ?>" class="mobile-menu__link<? ($arItem["SELECTED"] ? ' selected' : '') ?>">

								<?= $arItem["TEXT"] ?>
							</a>

							<ul>
								<li class="mobile-menu__mobile-back"><span class="mobile-menu__link">Назад</span></li>
								<li class="mobile-menu__mobile-title"><a href="<?= $arItem["LINK"] ?>" class="mobile-menu__link"><?= $arItem["TEXT"] ?></a></li>
							<? else: ?>

								<li>
									<a href="<?= $arItem["LINK"] ?>" class="mobile-menu__link" <? ($arItem["SELECTED"] ? 'class="selected"' : null) ?>>

										<?= $arItem["TEXT"] ?>
									</a>

									<ul>
									<? endif ?>

								<? else: ?>

									<? if ($arItem["PERMISSION"] > "D"): ?>

										<? if ($arItem["DEPTH_LEVEL"] == 1): ?>
											<li><a href="<?= $arItem["LINK"] ?>" class="mobile-menu__link<? ($arItem["SELECTED"] ? ' selected' : '') ?>">
													<? if ($arItem["PARAMS"]["SALE_ICON"] == true): ?>
														<img src="/images/sale-icon.svg" alt="" width="24" height="24">
													<? endif; ?>
													<?= $arItem["TEXT"] ?></a></li>
										<? else: ?>
											<li>
												<a href="<?= $arItem["LINK"] ?>" class="mobile-menu__link<? ($arItem["SELECTED"] ? ' selected' : '') ?>"><?= $arItem["TEXT"] ?></a>
											</li>
										<? endif ?>

									<? else: ?>

										<? if ($arItem["DEPTH_LEVEL"] == 1): ?>
											<li><span class="mobile-menu__link<? ($arItem["SELECTED"] ? ' selected' : '') ?>"><?= $arItem["TEXT"] ?></span></li>
										<? else: ?>
											<li><span class="mobile-menu__link denied<? ($arItem["SELECTED"] ? ' selected' : '') ?>"><?= $arItem["TEXT"] ?></span></li>
										<? endif ?>

									<? endif ?>

								<? endif ?>

								<? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>

							<? endforeach ?>

							<? if ($previousLevel > 1): //close last item tags
							?>
								<?= str_repeat("</ul></li>", ($previousLevel - 1)); ?>
							<? endif ?>

									</ul>
	</nav>
<? endif ?>