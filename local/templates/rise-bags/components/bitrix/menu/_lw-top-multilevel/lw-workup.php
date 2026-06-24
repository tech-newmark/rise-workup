<!-- DEPTH_LEVEL -->
<!-- IS_PARENT -->
<!-- идут друг за другом по порядку -->

<div class="main-nav__wrapper">
  <div class="main-nav__header">
    <a class="main-logo" href="/" aria-label="Юстиком. Коллегия адвокатов">
      <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/logo.svg" alt="Лого" width="150" height="50">
    </a>

    <button class="main-nav-closer">
        <svg width="40" height="40" role="img" aria-hidden="true" focusable="false">
          <use xlink:href="<?=SITE_TEMPLATE_PATH?>/assets/spritemap.svg#sprite-close"></use>
        </svg>
    </button>
  </div>

  <ul class="main-nav__list">

  <?
    $previousLevel = 0;
    foreach($arResult as $arItem):?>

     

      <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
         <!-- Закрываю уровень вложенности -->
        <?=str_repeat("</ul></div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
        <!-- Закрыл уровень вложенности -->
      <?endif?>

      <?if ($arItem["IS_PARENT"]):?>

        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
          <!-- !! РОДИТЕЛЬ 1ГО УРОВНЯ !! -->
          <li class="main-nav__list-item <?=($arItem["PARAMS"]["TOP_MULTILEVEL"] ? 'multilevel-menu' : 'has-inner')?>"> 
            <a href="<?=$arItem["LINK"]?>">
              <span><?=$arItem["TEXT"]?></span>
              <svg tabindex="0" class="main-nav-item-expander" width="24" height="24">
                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/assets/spritemap.svg#sprite-icon-chevron-down"></use>
              </svg>
            </a>

            <?
              if($arItem["PARAMS"]["TOP_MULTILEVEL"]):
                $multilevel = true;
            ?>
              <div class="top-multilevel-menu">
                <ul class="top-multilevel-menu__list">
            <?else:
              $multilevel = false;  
            ?>
              <div class="main-nav__inner-list-wrapper" style="display:none;">
                <ul class="main-nav__inner-list">
            <?endif;?>
        <?else:?>
          <!-- !! ВНУТРЕННИЙ РОДИТЕЛЬ !! -->

          <?if($multilevel):
              $section = $arItem["IS_PARENT"] && $arItem["DEPTH_LEVEL"] == 3 ? true : false;
          ?>
            <li class="<?=($section ? 'top-multilevel-menu__section-list-item' : 'top-multilevel-menu__list-item')?>">
              <a href="<?=$arItem["LINK"]?>">
                <span><?=$arItem["TEXT"]?></span>
                <?if(!$section):?>
                <svg tabindex="0" class="main-nav-item-expander" width="24" height="24">
                  <use xlink:href="<?=SITE_TEMPLATE_PATH?>/assets/spritemap.svg#sprite-arrow-right"></use>
                </svg>
                <?endif;?>
              </a>
              <div class="<?=($arItem["DEPTH_LEVEL"] == 3 ? 'top-multilevel-menu__inner-list-wrapper' : 'top-multilevel-menu__side')?>">
                <ul class="<?=($arItem["IS_PARENT"] && $arItem["DEPTH_LEVEL"] == 2 && $previousLevel == 2 ? 'top-multilevel-menu__section-list' : 'top-multilevel-menu__inner-list')?>">
          <?else:?>
            <li class="main-nav__inner-list-item">
              <a href="<?=$arItem["LINK"]?>">
                <?=$arItem["TEXT"]?>
              </a>
              <div class="child-parent-wrapper">
                <ul class="child-parent__inner-list">
          <?endif?>
        <?endif?>

      <?else:?>

        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
          <!-- !! ПЕРВЫЙ ПУНКТ МЕНЮ, НЕ РОДИТЕЛЬ !! -->
          <li class="main-nav__list-item">
            <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
          </li>
        <?else:?>
          <!-- !! ФИНАЛЬНЫЙ ЭЛЕМЕНТ !! -->
          <?if($multilevel):?>
            <?if($arItem["DEPTH_LEVEL"] == 2):?>
              <li class="top-multilevel-menu__list-item">
                <a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a>
                <div class="top-multilevel-menu__side"></div>
              </li>
            <?else:?>
              <li class="<?=!$arItem["IS_PARENT"] && $arItem["DEPTH_LEVEL"] < 4 && $section ? 'top-multilevel-menu__section-list-item' : 'top-multilevel-menu__inner-list-item'?>">
                <a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a>
              </li>
            <?endif;?>
          <?else:?>
            <li class="main-nav__inner-list-item"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
          <?endif;?>

        <?endif?>
      <?endif?>

      <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
    <?endforeach?>

    <!-- Закрываю основной список -->
    <?if ($previousLevel > 1):?>
      <?=str_repeat("</ul></div></li>", ($previousLevel-1) );?>
    <?endif?>
    <!-- Закрываю основной список -->
  </ul>

  <div class="main-nav__footer">
    <?$APPLICATION->IncludeComponent(
      "bitrix:news.list", 
      "lw-social", 
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
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_DATE" => "N",
        "DISPLAY_NAME" => "N",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array(
          0 => "",
          1 => "",
        ),
        "FILTER_NAME" => "",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "19",
        "IBLOCK_TYPE" => "site_content",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "INCLUDE_SUBSECTIONS" => "N",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "3",
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
        "PROPERTY_CODE" => array(
          0 => "SOCIAL_LINK",
          1 => "SOCIAL_ICON",
          2 => "",
        ),
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
        "COMPONENT_TEMPLATE" => ".default"
      ),
      false
    );?>

    <div class="main-nav__contacts">
      <?
        $APPLICATION->IncludeFile(
          SITE_DIR."include/company/phones.php",
          Array(),
          Array("MODE"=>"html", "SHOW_BORDER" => false)
        );
      ?>
    </div>
  </div>
</div>