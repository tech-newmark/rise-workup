<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <link rel="shortcut icon" type="image/x-icon" href="<?= SITE_TEMPLATE_PATH ?>/favicon.ico" />

  <? $APPLICATION->ShowHead(); ?>
  <title><? $APPLICATION->ShowTitle() ?></title>

  <?
  includeGlobalAssets();
  initBitrixCore('popup');

  $curPage = $APPLICATION->GetCurPage();
  $favoriteProductIds = function_exists('riseBagsGetFavoriteProductIds') ? riseBagsGetFavoriteProductIds() : [];
  $compareProductIds = function_exists('riseBagsGetCompareItems') ? riseBagsGetCompareItems() : [];
  ?>
  <script>
    window.RiseBagsFavoriteIds = <?= CUtil::PhpToJSObject($favoriteProductIds) ?>;
    window.RiseBagsCompareIds = <?= CUtil::PhpToJSObject($compareProductIds) ?>;
  </script>

</head>

<body>
  <div id="panel"><? $APPLICATION->ShowPanel(); ?></div>

  <?
  $APPLICATION->IncludeComponent(
    "bitrix:eshop.banner",
    "",
    array()
  ); ?>

  <header class="header">
    <div class="container">
      <div class="header__top">
        <a href="/" class="header__logo" aria-label="На главную страницу">
          <img src="<?= SITE_TEMPLATE_PATH ?>/_dist/images/logo-colored.svg" alt="" width="204" height="106">
        </a>

        <div class="header__top-row">
          <? $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "simple-row",
            [
              "ROOT_MENU_TYPE" => "top_simple",
              "MENU_CACHE_TYPE" => "A",
              "MENU_CACHE_TIME" => "36000000",
              "MENU_CACHE_USE_GROUPS" => "Y",
              "MENU_THEME" => "site",
              "CACHE_SELECTED_ITEMS" => "N",
              "MENU_CACHE_GET_VARS" => [],
              "MAX_LEVEL" => "1",
              "CHILD_MENU_TYPE" => "",
              "USE_EXT" => "N",
              "DELAY" => "N",
              "ALLOW_MULTI_SELECT" => "N",
              "COMPONENT_TEMPLATE" => "simple-row"
            ],
            false
          ); ?>

          <div class="contact-block">
            <div class="contact-block__section">
              <svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
                <use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-phone'></use>
              </svg>

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

            <div class="contact-block__section">
              <svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
                <use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-mail'></use>
              </svg>
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

          <button class="main-btn outlined" data-form-id="2">Стать партнером</button>
        </div>

        <div class="header__top-row">

          <?
          $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "catalog-menu",
            [
              "ROOT_MENU_TYPE" => "left",
              "MENU_CACHE_TYPE" => "N",
              "MENU_CACHE_TIME" => "36000000",
              "MENU_CACHE_USE_GROUPS" => "Y",
              "MENU_CACHE_GET_VARS" => [],
              "MAX_LEVEL" => "3",
              "CHILD_MENU_TYPE" => "left",
              "USE_EXT" => "Y",
              "ALLOW_MULTI_SELECT" => "N",
              "COMPONENT_TEMPLATE" => "catalog-menu",
              "DELAY" => "N"
            ],
            false
          );
          ?>

          <?php
          if ($curPage != SITE_DIR . "index.php"):
            if (\Bitrix\Main\ModuleManager::isModuleInstalled('search')):
          ?>
              <? $APPLICATION->IncludeComponent(
                "bitrix:search.title",
                "search-title",
                [
                  "NUM_CATEGORIES" => "1",
                  "TOP_COUNT" => "5",
                  "CHECK_DATES" => "Y",
                  "SHOW_OTHERS" => "N",
                  "PAGE" => SITE_DIR . "catalog/",
                  "CATEGORY_0_TITLE" => "",
                  "CATEGORY_0" => [
                    0 => "iblock_catalog",
                  ],
                  "CATEGORY_0_iblock_catalog" => [
                    0 => "2",
                  ],
                  "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
                  "SHOW_INPUT" => "Y",
                  "INPUT_ID" => "title-search-input",
                  "CONTAINER_ID" => "title-search",
                  "PRICE_CODE" => [
                    0 => "BASE",
                  ],
                  "SHOW_PREVIEW" => "Y",
                  "PREVIEW_WIDTH" => "75",
                  "PREVIEW_HEIGHT" => "75",
                  "CONVERT_CURRENCY" => "Y",
                  "COMPONENT_TEMPLATE" => "search-title",
                  "ORDER" => "date",
                  "USE_LANGUAGE_GUESS" => "Y"
                ],
                false
              ); ?>
          <?php
            endif;
          endif;
          ?>

          <!-- <div class="btn-group">
          </div> -->
          <? $APPLICATION->IncludeComponent(
            "bitrix:sale.basket.basket.line",
            "header-basket-line",
            [
              "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
              "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
              "SHOW_PERSONAL_LINK" => "N",
              "SHOW_NUM_PRODUCTS" => "Y",
              "SHOW_TOTAL_PRICE" => "N",
              "SHOW_PRODUCTS" => "N",
              "POSITION_FIXED" => "N",
              "SHOW_AUTHOR" => "Y",
              "PATH_TO_REGISTER" => SITE_DIR . "login/",
              "PATH_TO_PROFILE" => SITE_DIR . "personal/private/",
              "COMPONENT_TEMPLATE" => "header-basket-line",
              "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
              "SHOW_EMPTY_VALUES" => "N",
              "PATH_TO_AUTHORIZE" => SITE_DIR . "auth/",
              "SHOW_REGISTRATION" => "N",
              "SHOW_DELAY" => "Y",
              "SHOW_NOTAVAIL" => "Y",
              "SHOW_IMAGE" => "Y",
              "SHOW_PRICE" => "Y",
              "SHOW_SUMMARY" => "Y",
              "POSITION_HORIZONTAL" => "right",
              "POSITION_VERTICAL" => "vcenter",
              "HIDE_ON_BASKET_PAGES" => "N",
              "MAX_IMAGE_SIZE" => "80"
            ],
            false
          ); ?>
          <button class="main-btn callback-btn" data-form-id="1">Заказать звонок</button>
          <button class="menu-opener">
            <svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
              <use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-burger'></use>
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div class="header__bottom">
      <div class="container">
        <? $APPLICATION->IncludeComponent(
          "bitrix:menu",
          "horizontal_multilevel",
          [
            "ROOT_MENU_TYPE" => "top",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "36000000",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_THEME" => "site",
            "CACHE_SELECTED_ITEMS" => "N",
            "MENU_CACHE_GET_VARS" => [],
            "MAX_LEVEL" => "2",
            "CHILD_MENU_TYPE" => "left",
            "USE_EXT" => "Y",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N",
            "COMPONENT_TEMPLATE" => "horizontal_multilevel"
          ],
          false
        ); ?>
      </div>
    </div>

    <div class="header__mobile">
      <div class="container">
        <? $APPLICATION->IncludeComponent(
          "bitrix:catalog.section.list",
          "site-multilevel-menu",
          [
            "ADDITIONAL_COUNT_ELEMENTS_FILTER" => "additionalCountFilter",
            "ADD_SECTIONS_CHAIN" => "N",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "COMPONENT_TEMPLATE" => "site-multilevel-menu",
            "COUNT_ELEMENTS" => "Y",
            "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
            "FILTER_NAME" => "sectionsFilter",
            "HIDE_SECTIONS_WITH_ZERO_COUNT_ELEMENTS" => "N",
            "IBLOCK_ID" => "2",
            "IBLOCK_TYPE" => "catalog",
            "SECTION_CODE" => "",
            "SECTION_FIELDS" => [
              0 => "CODE",
              1 => "IBLOCK_CODE",
              2 => "",
            ],
            "SECTION_ID" => $_REQUEST["SECTION_ID"],
            "SECTION_URL" => "/catalog/#SECTION_CODE_PATH#/",
            "SECTION_USER_FIELDS" => [
              0 => "UF_HIDE_IN_MENU",
            ],
            "SHOW_PARENT_NAME" => "Y",
            "TOP_DEPTH" => "4",
            "VIEW_MODE" => "LIST"
          ],
          false
        ); ?>

        <? $APPLICATION->IncludeComponent(
          "bitrix:menu",
          "mobile-multilevel",
          [
            "ALLOW_MULTI_SELECT" => "N",
            "CHILD_MENU_TYPE" => "left",
            "DELAY" => "N",
            "MAX_LEVEL" => "2",
            "MENU_CACHE_GET_VARS" => [],
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_TYPE" => "N",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "ROOT_MENU_TYPE" => "top",
            "USE_EXT" => "Y",
            "COMPONENT_TEMPLATE" => "top-menu-multilevel"
          ],
          false
        ); ?>
      </div>
      <div class="container">
        <!-- <div class="header__mobile-buttons">
          <button class="main-btn callback-btn" data-form-id="1">Заказать звонок</button>
          <button class="main-btn outlined" data-form-id="1">Стать партнером</button>
          <button class="main-btn callback-btn" data-form-id="1">Запросить прайс</button>
        </div> -->
        <div class="contact-block">
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

        </div>

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
              "THEME_SELECT" => "1"
            )
          ); ?>
        </div>


      </div>
    </div>
  </header>

  <main id="workarea" class="workarea">
    <?

    if ($curPage != '/' && !defined("ERROR_404")) {
      $APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        "lw-breadcrumb",
        [
          "PATH" => "",
          "SITE_ID" => "s1",
          "START_FROM" => "0",
          "COMPONENT_TEMPLATE" => "lw-breadcrumb"
        ],
        false
      );
    }
    ?>
