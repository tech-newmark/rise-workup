<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<div class="section article-page">
	<div class="container">
		<div class="article-detail-container">
			<article class="article-detail">
				<? if ($arResult['DETAIL_PICTURE']['SRC'] || $arResult['PREVIEW_PICTURE']['SRC']): ?>
					<img class="article-detail__img" src="<?= ($arResult['DETAIL_PICTURE']['SRC']) ? $arResult['DETAIL_PICTURE']['SRC'] : $arResult['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>" width="840" height="280">
				<? endif; ?>
				<div class="article-detail__header">
					<h1><?= $arResult['NAME'] ?></h1>
					<? if ($arParams['SHOW_DATE_ACTIVE_FROM'] == "Y" && $arResult['DISPLAY_ACTIVE_FROM']): ?>
						<time><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></time>
					<? endif; ?>
					<? if ($arParams['SHOW_DATE_ACTIVE_TO'] == "Y" && $arResult['DATE_ACTIVE_TO']): ?>
						<time>Действует до <?= FormatDate("j F Y", MakeTimeStamp($arResult['DATE_ACTIVE_TO'], "DD.MM.YYYY")) ?></time>
					<? endif; ?>
				</div>
				<? if ($arResult['DETAIL_TEXT']): ?>
					<div class="article-detail__body">
						<div class="content">
							<?= $arResult['DETAIL_TEXT'] ?>
						</div>
					</div>
				<? endif; ?>
			</article>
		</div>
		<? if (!empty($arResult['PROPERTIES']['LINKED_ARTICLES']['VALUE'])): ?>
			<?
			$GLOBALS['arLinkedFilter'] = array('ID' => $arResult['PROPERTIES']['LINKED_ARTICLES']['VALUE']);
			$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"linked-articles",
				array(
					"ACTIVE_DATE_FORMAT" => "j F Y",
					"ADD_SECTIONS_CHAIN" => "Y",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"AJAX_OPTION_HISTORY" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"CACHE_TIME" => "36000000",
					"CACHE_TYPE" => "A",
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"DISPLAY_DATE" => "Y",
					"DISPLAY_NAME" => "Y",
					"DISPLAY_PICTURE" => "Y",
					"DISPLAY_PREVIEW_TEXT" => "Y",
					"DISPLAY_TOP_PAGER" => "N",
					"FIELD_CODE" => array("", ""),
					"FILTER_NAME" => "arLinkedFilter",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"IBLOCK_ID" => $arResult['IBLOCK_ID'],
					"IBLOCK_TYPE" => "site_content",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"INCLUDE_SUBSECTIONS" => "Y",
					"MESSAGE_404" => "",
					"NEWS_COUNT" => "20",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => ".default",
					"PAGER_TITLE" => "Новости",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"PREVIEW_TRUNCATE_LEN" => "",
					"PROPERTY_CODE" => array("", ""),
					"SET_BROWSER_TITLE" => "Y",
					"SET_LAST_MODIFIED" => "N",
					"SET_META_DESCRIPTION" => "Y",
					"SET_META_KEYWORDS" => "Y",
					"SET_STATUS_404" => "N",
					"SET_TITLE" => "Y",
					"SHOW_404" => "N",
					"SORT_BY1" => "ACTIVE_FROM",
					"SORT_BY2" => "SORT",
					"SORT_ORDER1" => "DESC",
					"SORT_ORDER2" => "ASC",
					"STRICT_SECTION_CHECK" => "N",
					"TITLE_IN_LINKED_ARTICLES" => $arParams["TITLE_IN_LINKED_ARTICLES"],
					"DESC_IN_LINKED_ARTICLES" => $arParams["DESC_IN_LINKED_ARTICLES"],
					"BUTTON_NAME_IN_LINKED_ARTICLES" => $arParams["BUTTON_NAME_IN_LINKED_ARTICLES"],
					"SHOW_DATE_ACTIVE_FROM" => $arParams["SHOW_DATE_ACTIVE_FROM"],
					"SHOW_DATE_ACTIVE_TO" => $arParams["SHOW_DATE_ACTIVE_TO"],
				),
				$component
			);
			unset($GLOBALS['arLinkedFilter']);
			?>

		<? endif; ?>
	</div>
</div>