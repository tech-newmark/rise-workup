<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
if (empty($arResult['CATEGORIES']) || !$arResult['CATEGORIES_ITEMS_EXISTS']) {
	return;
}
?>

<div class="search-result-container">
	<? foreach ($arResult['CATEGORIES'] as $category_id => $arCategory): ?>
		<? foreach ($arCategory['ITEMS'] as $i => $arItem): ?>
			<? if ($category_id === 'all'): ?>
				<div class="search-result-item search-result-item--all">
					<a href="<?= $arItem['URL'] ?>"><?= $arItem['NAME'] ?></a>
				</div>

			<? else: ?>
				<div class="search-result-item">
					<a href="<?= $arItem['URL'] ?>"><?= $arItem['NAME'] ?></a>

				</div>
			<? endif; ?>
		<? endforeach; ?>
	<? endforeach; ?>
</div>