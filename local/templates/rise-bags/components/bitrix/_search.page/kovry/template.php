<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<section class="section search-page">
	<div class="container">
		<? if (count($arResult['SEARCH']) > 0): ?>
			<div class="search-page__sort">
				<? if ($arResult['REQUEST']['HOW'] == 'd'): ?>
					<a class="search-page__sort-item active" href="<?= $arResult['URL'] ?>&amp;how=r<?= $arResult['REQUEST']['FROM'] ? '&amp;from=' . $arResult['REQUEST']['FROM'] : '' ?><?= $arResult['REQUEST']['TO'] ? '&amp;to=' . $arResult['REQUEST']['TO'] : '' ?>">
						<?= GetMessage('SEARCH_SORT_BY_RANK') ?>
					</a>
					<span class="search-page__sort-item"><?= GetMessage('SEARCH_SORTED_BY_DATE') ?></span>
				<? else: ?>
					<span class="search-page__sort-item">
						<?= GetMessage('SEARCH_SORTED_BY_RANK') ?>
					</span>
					<a class="search-page__sort-item active" href="<?= $arResult['URL'] ?>&amp;how=d<?= $arResult['REQUEST']['FROM'] ? '&amp;from=' . $arResult['REQUEST']['FROM'] : '' ?><?= $arResult['REQUEST']['TO'] ? '&amp;to=' . $arResult['REQUEST']['TO'] : '' ?>">
						<?= GetMessage('SEARCH_SORT_BY_DATE') ?>
					</a>
				<? endif; ?>
			</div>
		<? endif; ?>

		<form action="" method="get">
			<? if ($arParams['USE_SUGGEST'] === 'Y'):
				if (mb_strlen($arResult['REQUEST']['~QUERY']) && is_object($arResult['NAV_RESULT'])) {
					$arResult['FILTER_MD5'] = $arResult['NAV_RESULT']->GetFilterMD5();
					$obSearchSuggest = new CSearchSuggest($arResult['FILTER_MD5'], $arResult['REQUEST']['~QUERY']);
					$obSearchSuggest->SetResultCount($arResult['NAV_RESULT']->NavRecordCount);
				}
			?>
				<? $APPLICATION->IncludeComponent(
					'bitrix:search.suggest.input',
					'',
					[
						'NAME' => 'q',
						'VALUE' => $arResult['REQUEST']['~QUERY'],
						'INPUT_SIZE' => 40,
						'DROPDOWN_SIZE' => 10,
						'FILTER_MD5' => $arResult['FILTER_MD5'],
					],
					$component,
					['HIDE_ICONS' => 'Y']
				); ?>
			<? else: ?>
				<div class="main-input-wrapper search-title__wrapper">
					<label class="">
						<input type="text" name="q" value="<?= $arResult['REQUEST']['QUERY'] ?>" size="40" placeholder="Поиск" />
					</label>
					<button class="" name="s" type="submit" aria-label="Поиск">
						<svg width="24" height="24" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false">
							<use xlink:href="/local/templates/kovry-online/assets/sprite.svg#icon-search"></use>
						</svg>
					</button>
				</div>
			<? endif; ?>
		</form>

		<? if (isset($arResult['REQUEST']['ORIGINAL_QUERY'])): ?>
			<p class="search-language-guess">
				<?= GetMessage('CT_BSP_KEYBOARD_WARNING', ['#query#' => '<a href="' . $arResult['ORIGINAL_QUERY_URL'] . '">' . $arResult['REQUEST']['ORIGINAL_QUERY'] . '</a>']) ?>
			</p>
		<? endif; ?>



		<? if ($arResult['REQUEST']['QUERY'] === false && $arResult['REQUEST']['TAGS'] === false): ?>
			<p><?= GetMessage('SEARCH_PREVIEW_TEXT') ?></p>
		<? elseif ($arResult['ERROR_CODE'] != 0): ?>
			<p><?= GetMessage('SEARCH_ERROR') ?></p>
		<? elseif (count($arResult['SEARCH']) > 0): ?>
			<?= ($arParams['DISPLAY_TOP_PAGER'] != 'N') ? $arResult['NAV_STRING'] : ''; ?>
			<ul class="search-page__list">
				<? foreach ($arResult['SEARCH'] as $arItem): ?>
					<li class="search-page__list-item">
						<a href="<?= $arItem['URL'] ?>"><?= $arItem['TITLE_FORMATED'] ?></a>

						<? if ($arItem['BODY_FORMATED'] !== $arItem['TITLE_FORMATED']): ?>
							<p><?= $arItem['BODY_FORMATED'] ?></p>
						<? endif; ?>

						<? if ($arItem['CHAIN_PATH']): ?>
							<small><?= GetMessage('SEARCH_PATH') ?>&nbsp;<?= $arItem['CHAIN_PATH'] ?></small>
						<? endif; ?>
					</li>
				<? endforeach; ?>
			</ul>
			<?= ($arParams['DISPLAY_BOTTOM_PAGER'] != 'N') ? $arResult['NAV_STRING'] : ''; ?>

		<? else: ?>
			<p class="search-nothing-to-found">
				<?= GetMessage('SEARCH_NOTHING_TO_FOUND'); ?>
			</p>
		<? endif; ?>
	</div>
</section>