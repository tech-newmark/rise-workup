<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<section class="section contacts">
	<div class="container">
		<h1>Наши контакты</h1>
		<div class="grid">
			<div class="grid__item grid__item--info">
				<? $APPLICATION->IncludeFile(
					SITE_DIR . "include/contacts/desc.php",
					array(),
					array(
						"MODE" => "html",
						"NAME" => "описание",
						"TEMPLATE" => "include_area.php",
					)
				); ?>
				<div class="contacts__list">
					<div class="contacts__list-item">
						<div class="contacts__list-item-header">
							<svg width="32" height="32" viewBox="0 0 32 32" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-pin"></use>
							</svg>
							<b>Адрес</b>
						</div>
						<div class="contacts__list-item-content">
							<? $APPLICATION->IncludeFile(
								SITE_DIR . "include/contacts/address.php",
								array(),
								array(
									"MODE" => "html",
									"NAME" => "адрес",
									"TEMPLATE" => "include_area.php",
								)
							); ?>
						</div>
					</div>
					<div class="contacts__list-item">
						<div class="contacts__list-item-header">
							<svg width="32" height="32" viewBox="0 0 32 32" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-phone"></use>
							</svg>
							<b>Телефон</b>
						</div>
						<div class="contacts__list-item-content">
							<? $APPLICATION->IncludeFile(
								SITE_DIR . "include/contacts/phones.php",
								array(),
								array(
									"MODE" => "html",
									"NAME" => "телефон",
									"TEMPLATE" => "include_area.php",
								)
							); ?>
						</div>
					</div>
					<div class="contacts__list-item">
						<div class="contacts__list-item-header">
							<svg width="32" height="32" viewBox="0 0 32 32" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-date"></use>
							</svg>
							<b>Время работы</b>
						</div>
						<div class="contacts__list-item-content">
							<? $APPLICATION->IncludeFile(
								SITE_DIR . "include/contacts/schedule.php",
								array(),
								array(
									"MODE" => "html",
									"NAME" => "время работы",
									"TEMPLATE" => "include_area.php",
								)
							); ?>
						</div>
					</div>
					<div class="contacts__list-item">
						<div class="contacts__list-item-header">
							<svg width="32" height="32" viewBox="0 0 32 32" role="img" aria-hidden="true" focusable="false">
								<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-mail"></use>
							</svg>
							<b>Email</b>
						</div>
						<div class="contacts__list-item-content">
							<? $APPLICATION->IncludeFile(
								SITE_DIR . "include/contacts/email.php",
								array(),
								array(
									"MODE" => "html",
									"NAME" => "email",
									"TEMPLATE" => "include_area.php",
								)
							); ?>
						</div>
					</div>
					<div class="contacts__list-item">
						<div class="contacts__list-item-header">
							<b>Мы в соцсетях</b>
						</div>

						<? $APPLICATION->IncludeComponent(
							"bitrix:news.list",
							"social-list",
							[
								"ACTIVE_DATE_FORMAT" => "d.m.Y",
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
								"FIELD_CODE" => [
									0 => "",
									1 => "",
								],
								"FILTER_NAME" => "",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"IBLOCK_ID" => "8",
								"IBLOCK_TYPE" => "site_content",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
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
								"PROPERTY_CODE" => [
									0 => "",
									1 => "ICON",
									2 => "",
								],
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
								"COMPONENT_TEMPLATE" => "social-list",
								"THEME_SELECT" => "1"
							],
							false
						); ?>

					</div>
				</div>
				<div class="contacts__buttons">
					<button class="main-btn outlined" type="button">
						<span>Запросить прайс</span>
					</button>
					<button class="main-btn" type="button">
						<span>Заказать звонок</span>
					</button>

				</div>
			</div>
			<div class="grid__item grid__item--map">
				<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A8ee7ebca82150d2a24a665ba7846167dae4cc3770c533b389d977e7f7f392690&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>
			</div>
		</div>
	</div>
</section>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>