<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */
$curDir = !empty($arParams['CURRENT_PAGE_DIR']) ? $arParams['CURRENT_PAGE_DIR'] : $APPLICATION->GetCurDir();
$isCatalogSection = preg_match('#^/catalog/(?:[^/]+/)+$#', $curDir) === 1;

$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
$favoriteCount = 0;
$compareCount = 0;

if (!$compositeStub) {
	if (function_exists('riseBagsGetFavoriteProductIds')) {
		$favoriteCount = count(riseBagsGetFavoriteProductIds());
	} elseif (!empty($arResult['CATEGORIES']['DELAY'])) {
		$favoriteCount = count($arResult['CATEGORIES']['DELAY']);
	}

	if (function_exists('riseBagsGetCompareCount')) {
		$compareCount = riseBagsGetCompareCount();
	}
}
?>

<div class="bx-basket-line-block">
	<? if (!$compositeStub && $arParams['SHOW_AUTHOR'] == 'Y'): ?>
		<? if ($USER->IsAuthorized()): ?>

			<div class="bx-basket-line-block-section">
				<a href="?logout=yes&<?= bitrix_sessid_get() ?>">
					<div class="bx-basket-line-block-section-icon">
						<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
							<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-login' ?> "></use>
						</svg>
					</div>

					<span><?= GetMessage('TSB1_LOGOUT') ?></span>
				</a>
			</div>

			<? if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'): ?>
				<div class="bx-basket-line-block-section">
					<a href="<?= $arParams['PATH_TO_PERSONAL'] ?>">
						<div class="bx-basket-line-block-section-icon">
							<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-user' ?> "></use>
							</svg>
						</div>
						<span><?= GetMessage('TSB1_PERSONAL') ?>
					</a></span>
				</div>
			<? endif; ?>

			<? if ($arParams['PATH_TO_PROFILE'] != ''): ?>
				<div class="bx-basket-line-block-section">
					<a href="<?= $arParams['PATH_TO_PROFILE'] ?>">
						<div class="bx-basket-line-block-section-icon">
							<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-profile' ?> "></use>
							</svg>
						</div>
						<span><?= GetMessage('TSB1_PROFILE') ?></span>
					</a>
				</div>
			<? endif; ?>

			<? else:
			$arParamsToDelete = array(
				"login",
				"login_form",
				"logout",
				"register",
				"forgot_password",
				"change_password",
				"confirm_registration",
				"confirm_code",
				"confirm_user_id",
				"logout_butt",
				"auth_service_id",
				"clear_cache",
				"backurl",
			);

			$currentUrl = urlencode($APPLICATION->GetCurPageParam("", $arParamsToDelete));
			if ($arParams['AJAX'] == 'N'): ?>
				<script>
					<?= $cartId ?>.currentUrl = '<?= $currentUrl ?>';
				</script>
			<? else:
				$currentUrl = '#CURRENT_URL#';
			endif;

			$pathToAuthorize = $arParams['PATH_TO_AUTHORIZE'];
			$pathToAuthorize .= (mb_stripos($pathToAuthorize, '?') === false ? '?' : '&');
			$pathToAuthorize .= 'login=yes&backurl=' . $currentUrl;
			?>

			<div class="bx-basket-line-block-section bx-basket-line-block-section--auth">
				<a href="<?= $pathToAuthorize ?>">
					<div class="bx-basket-line-block-section-icon">
						<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
							<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-login' ?> "></use>
						</svg>
					</div>

					<span><?= GetMessage('TSB1_LOGIN') ?></span>
				</a>

				<? if ($arParams['SHOW_REGISTRATION'] === 'Y'):
					$pathToRegister = $arParams['PATH_TO_REGISTER'];
					$pathToRegister .= (mb_stripos($pathToRegister, '?') === false ? '?' : '&');
					$pathToRegister .= 'register=yes&backurl=' . $currentUrl;
				?>
					<a href="<?= $pathToRegister ?>">
						<div class="bx-basket-line-block-section-icon">
							<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-reg' ?> "></use>
							</svg>
						</div>
						<span><?= GetMessage('TSB1_REGISTER') ?></span>
					</a>
				<? endif; ?>
			</div>
		<? endif ?>
	<? endif ?>

	<div class="bx-basket-line-block-section">
		<a href="/catalog/compare/">
				<div class="bx-basket-line-block-section-icon">
					<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-compare' ?> "></use>
					</svg>
					<span
						class="bx-basket-line-block-section-label"
						data-compare-counter
						<?= $compareCount <= 0 ? 'style="display: none;"' : '' ?>><?= $compareCount ?></span>
				</div>
				<span>Сравнение</span>
			</a>
	</div>

	<div class="bx-basket-line-block-section">
		<a href="/personal/favourite/">
			<div class="bx-basket-line-block-section-icon">
				<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
					<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-heart' ?> "></use>
				</svg>
				<span
					class="bx-basket-line-block-section-label"
					data-favorite-counter
					<?= $favoriteCount <= 0 ? 'style="display: none;"' : '' ?>><?= $favoriteCount ?></span>
			</div>
			<span>Избранное</span>
		</a>
	</div>

	<div class="bx-basket-line-block-section">
		<? if (!$arResult["DISABLE_USE_BASKET"]): ?>
			<a href="<?= $arParams['PATH_TO_BASKET'] ?>">
				<div class="bx-basket-line-block-section-icon">
					<svg width="24" height="20" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="<?= SITE_TEMPLATE_PATH . '/_dist/sprite.svg#icon-cart' ?> "></use>
					</svg>
					<? if (!$compositeStub): ?>
						<? if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')): ?>
							<span class="bx-basket-line-block-section-label"><?= $arResult['NUM_PRODUCTS'] ?></span>
						<? endif; ?>
					<? endif; ?>
				</div>
				<span><?= GetMessage('TSB1_CART') ?></span>
			</a>
		<? endif; ?>

		<? if (!$compositeStub): ?>
			<? if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')): ?>
				<? if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'): ?>
					<div class="bx-basket-line-tooltip">
						<small>
							<?= GetMessage('TSB1_TOTAL_PRICE') ?>
						</small>
						<strong><?= $arResult['TOTAL_PRICE'] ?></strong>
					</div>
				<? endif; ?>
			<? endif; ?>
		<? endif; ?>
	</div>

	<div class="bx-basket-line-block-section">
		<button class="search-title-opener" aria-label="Открыть поиск">
			<div class="bx-basket-line-block-section-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
					<use xlink:href="<?= SITE_TEMPLATE_PATH  . '/_dist/sprite.svg#icon-search' ?>"></use>
				</svg>
			</div>
			<span>Поиск</span>
		</button>
	</div>

	<? if ($isCatalogSection): ?>
		<div class="bx-basket-line-block-section">
			<button id="smartfilter_sticky_filter_opener" type="button" class="filter-opener" aria-label="Открыть фильтр">
				<div class="bx-basket-line-block-section-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
						<use xlink:href="<?= SITE_TEMPLATE_PATH  . '/_dist/sprite.svg#icon-filter' ?>"></use>
					</svg>
				</div>
				<span>Фильтр</span>
			</button>
		</div>
	<? endif; ?>
</div>

<!-- <script>
	(function() {
		var filterSelector = '#smartfilter_form, .bx-filter';
		var filterButtonSectionSelector = '.bx-basket-line-block-section--filter';

		function toggleFilterButtons() {
			var hasFilter = Boolean(document.querySelector(filterSelector));
			var sections = document.querySelectorAll(filterButtonSectionSelector);

			for (var i = 0; i < sections.length; i++) {
				sections[i].hidden = !hasFilter;
			}
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', toggleFilterButtons);
		} else {
			toggleFilterButtons();
		}
	})();
</script> -->
