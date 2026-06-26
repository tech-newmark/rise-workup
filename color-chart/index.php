<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Карта цветов");
?>
<section class="section">
	<div class="container">
		<!-- Сео блок -->
		<? $APPLICATION->IncludeFile(
			SITE_DIR . "include/color-chart-top.php",
			array(),
			array(
				"MODE" => "html",
				"NAME" => "Верхний блок текста",
				"TEMPLATE" => "include_area.php",
			)
		); ?>
		<!-- Сео блок -->
	</div>

	<!-- Галерея -->
	<? $APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"gallery-slider",
		array(
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"ADD_SECTIONS_CHAIN" => "N",
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
			"CUSTOM_VIEW" => "IMAGE",
			"DETAIL_URL" => "",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"FIELD_CODE" => array("", ""),
			"FILTER_NAME" => "",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"IBLOCK_ID" => "17",
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
			"SET_BROWSER_TITLE" => "N",
			"SET_LAST_MODIFIED" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_STATUS_404" => "N",
			"SET_TITLE" => "N",
			"SHOW_404" => "N",
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_BY2" => "SORT",
			"SORT_ORDER1" => "DESC",
			"SORT_ORDER2" => "ASC",
			"STRICT_SECTION_CHECK" => "N"
		)
	); ?>
	<!-- Галерея -->

	<div class="container">
		<!-- Сео блок -->
		<? $APPLICATION->IncludeFile(
			SITE_DIR . "include/color-chart-bottom.php",
			array(),
			array(
				"MODE" => "html",
				"NAME" => "Нижний блок текста",
				"TEMPLATE" => "include_area.php",
			)
		); ?>
		<!-- Сео блок -->
	</div>

	<!-- Форма -->
	<? $APPLICATION->IncludeComponent(
		"bitrix:form.result.new",
		"callback-form",
		array(
			"AJAX_MODE" => "Y",
			"CACHE_TIME" => "3600",
			"CACHE_TYPE" => "A",
			"CHAIN_ITEM_LINK" => "",
			"CHAIN_ITEM_TEXT" => "",
			"COMPONENT_TEMPLATE" => "callback-form",
			"EDIT_URL" => "result_edit.php",
			"IGNORE_CUSTOM_TEMPLATE" => "N",
			"LIST_URL" => "result_list.php",
			"NAME_TEMPLATE" => "",
			"RESULT_ID" => $_REQUEST["RESULT_ID"],
			"SEF_MODE" => "N",
			"SHOW_ADDITIONAL" => "N",
			"SHOW_ANSWER_VALUE" => "N",
			"SHOW_STATUS" => "Y",
			"SUCCESS_URL" => "",
			"USE_EXTENDED_ERRORS" => "Y",
			"VARIABLE_ALIASES" => ["WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID",],
			"WEB_FORM_ID" => "1"
		)
	); ?>
	<!-- Сео блок -->

</section>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>