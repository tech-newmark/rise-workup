<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
</main>

<?/* $APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	".default", 
	[
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"LIST_URL" => "",
		"SEF_MODE" => "N",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "Y",
		"WEB_FORM_ID" => "1",
		"COMPONENT_TEMPLATE" => ".default",
		"VARIABLE_ALIASES" => [
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		]
	],
	false
); */ ?>

<?/* $APPLICATION->IncludeComponent(
	"bitrix:subscribe.edit", 
	"rise", 
	[
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"ALLOW_ANONYMOUS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"SET_TITLE" => "N",
		"SHOW_AUTH_LINKS" => "N",
		"SHOW_HIDDEN" => "N",
		"COMPONENT_TEMPLATE" => "rise",
		"TITLE" => "Подпишитесь на рассылку и получите скидку 10% на товары в розницу",
		"DESCRIPTION" => "Добро пожаловать в сообщество Rise Bags! Ваш промокод на скидку 10% внутри",
		"ANSWER" => "Спасибо! Вы успешно подписались на рассылку!"
	],
	false
); */ ?>

<footer class="footer">
  <div class="container">
    <div class="grid">
      <div class="grid__item grid__item--company">
        <a href="/" class="footer__logo" aria-label="На главную страницу">
          <img src="<?= SITE_TEMPLATE_PATH ?>/_dist/images/logo.svg" alt="" width="240" height="180">
        </a>

        <div class="social">
          <? $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "social-list",
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
              "COMPONENT_TEMPLATE" => "social-list",
              "DETAIL_URL" => "",
              "DISPLAY_BOTTOM_PAGER" => "Y",
              "DISPLAY_DATE" => "Y",
              "DISPLAY_NAME" => "Y",
              "DISPLAY_PICTURE" => "Y",
              "DISPLAY_PREVIEW_TEXT" => "Y",
              "DISPLAY_TOP_PAGER" => "N",
              "FIELD_CODE" => [0 => "", 1 => "",],
              "FILTER_NAME" => "",
              "HIDE_LINK_WHEN_NO_DETAIL" => "N",
              "IBLOCK_ID" => "8",
              "IBLOCK_TYPE" => "site_content",
              "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
              "INCLUDE_SUBSECTIONS" => "N",
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
              "PROPERTY_CODE" => [0 => "", 1 => "ICON_DARK", 2 => "ICON_LIGHT", 3 => "",],
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
              "STRICT_SECTION_CHECK" => "N",
              "THEME_SELECT" => "2"
            )
          ); ?>
        </div>

        <button class="main-btn" data-form-id="3">Запросить прайс</button>
        <button class="main-btn outlined" data-form-id="1">Заказать звонок</button>
      </div>

      <div class="grid__item grid__item--menu">
        <? $APPLICATION->IncludeComponent(
          "bitrix:menu",
          "bottom-menu",
          [
            "TITLE" => "Каталог",
            "COLUMN_VIEW" => "Y",
            "ALLOW_MULTI_SELECT" => "N",
            "CHILD_MENU_TYPE" => "bottom.left",
            "DELAY" => "N",
            "MAX_LEVEL" => "1",
            "MENU_CACHE_GET_VARS" => [],
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_TYPE" => "N",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_THEME" => "site",
            "ROOT_MENU_TYPE" => "bottom.left",
            "USE_EXT" => "Y",
            "COMPONENT_TEMPLATE" => "bottom-menu"
          ],
          false
        ); ?>
      </div>

      <div class="grid__item grid__item--menu">
        <? $APPLICATION->IncludeComponent(
          "bitrix:menu",
          "bottom-menu",
          [
            "TITLE" => "Информация",
            "COLUMN_VIEW" => "N",
            "ALLOW_MULTI_SELECT" => "N",
            "CHILD_MENU_TYPE" => "left",
            "DELAY" => "N",
            "MAX_LEVEL" => "4",
            "MENU_CACHE_GET_VARS" => [],
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_TYPE" => "N",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_THEME" => "site",
            "ROOT_MENU_TYPE" => "bottom",
            "USE_EXT" => "Y",
            "COMPONENT_TEMPLATE" => "bottom-menu"
          ],
          false
        ); ?>
      </div>

      <div class="grid__item grid__item--contacts">
        <div class="contact-block">
          <div class="contact-block__section">
            <div class="contact-block__section-content">
              <? $APPLICATION->IncludeFile(
                SITE_DIR . "include/contacts/address.php",
                array(),
                array(
                  "MODE" => "html",
                  "NAME" => "Адрес",
                  "TEMPLATE" => "include_area.php",
                )
              ); ?>
            </div>
          </div>
          <div class="contact-block__section">
            <div class="contact-block__section-content">
              <? $APPLICATION->IncludeFile(
                SITE_DIR . "include/contacts/phones.php",
                array(),
                array(
                  "MODE" => "html",
                  "NAME" => "Телефон",
                  "TEMPLATE" => "include_area.php",
                )
              ); ?>
            </div>
          </div>
          <div class="contact-block__section">
            <div class="contact-block__section-content">
              <? $APPLICATION->IncludeFile(
                SITE_DIR . "include/contacts/email.php",
                array(),
                array(
                  "MODE" => "html",
                  "NAME" => "E-mail",
                  "TEMPLATE" => "include_area.php",
                )
              ); ?>
            </div>
          </div>
          <div class="contact-block__section">
            <div class="contact-block__section-content">
              <? $APPLICATION->IncludeFile(
                SITE_DIR . "include/contacts/schedule.php",
                array(),
                array(
                  "MODE" => "html",
                  "NAME" => "Время работы",
                  "TEMPLATE" => "include_area.php",
                )
              ); ?>
            </div>
          </div>
          <!-- <div class="contact-block__section">
            <div class="contact-block__section-content">
              <img src="<?= SITE_TEMPLATE_PATH ?>/_dist/yandex-rate.png" alt="Рейтинг организации в Яндекс" width="210" height="70">
            </div>
          </div> -->
        </div>
      </div>

    </div>
  </div>
  <div class="footer__bottom-line">
    <div class="container">
      <? $APPLICATION->IncludeFile(
        SITE_DIR . "include/policy-footer.php",
        array(),
        array(
          "MODE" => "html",
          "NAME" => "Текст",
          "TEMPLATE" => "include_area.php",
        )
      ); ?>
    </div>
  </div>
</footer>

<? $APPLICATION->IncludeComponent(
  'bitrix:main.userconsent.request',
  'cookie',
  array(
    'ID' => 1,
    'IS_CHECKED' => 'N',
    'IS_LOADED' => 'Y',
    'AUTO_SAVE' => 'Y',
    'INPUT_NAME' => 'COOKIE_CONSENT',
    'REPLACE' => array()
  )
); ?>

</body>

</html>