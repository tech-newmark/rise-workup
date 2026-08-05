<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

// includeComponentAssets('news.list/gallery-slider');
?>
<section class="section thanks">
	<div class="container">
		<div class="grid">
			<div class="grid__item grid__item--yandex">
				<iframe style="width:100%;height:100%;border:1px solid #e6e6e6;border-radius:8px;box-sizing:border-box" src="https://yandex.ru/maps-reviews-widget/241677942487?comments"></iframe><a href="https://yandex.ru/maps/org/rays/241677942487/" target="_blank" style="box-sizing:border-box;text-decoration:none;font-family:YS Text,sans-serif;;position:absolute;bottom:8px;text-align:center;overflow:hidden;text-overflow:ellipsis;display:block;box-sizing:border-box">Больше отзывов на Яндекс Картах</a>
			</div>
			<div class="grid__item grid__item--gallery">
				<h3>Благодарности</h3>
				<!-- Галерея -->
				<? $APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"gallery-slider",
					array(
						"CUSTOM_CLASS" => "thanks",
						"SHOW_FOOTER" => "Y",
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
						"IBLOCK_ID" => "18",
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
					),
					$component
				); ?>
				<!-- Галерея -->
				<p class="heading--lg">
					<? $APPLICATION->IncludeFile(
						SITE_DIR . "include/reviews-slogan.php",
						array(),
						array(
							"MODE" => "html",
							"NAME" => "Слоган",
							"TEMPLATE" => "include_area.php",
						)
					); ?>
				</p>
				<p>
					<? $APPLICATION->IncludeFile(
						SITE_DIR . "include/reviews-desc.php",
						array(),
						array(
							"MODE" => "html",
							"NAME" => "Текст",
							"TEMPLATE" => "include_area.php",
						)
					); ?></p>
				<button class="main-btn" data-form-id="1">Оставить отзыв</button>
			</div>
		</div>
	</div>
</section>