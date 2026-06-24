<div class="main-nav__wrapper">

  <ul class="main-nav__list">

    <?
    $previousLevel = 0;
    foreach ($arResult as $arItem): ?>

      <? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel): ?>
        <?= str_repeat("</ul></div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"])); ?>
      <? endif ?>

      <? if ($arItem["IS_PARENT"]): ?>

        <? if ($arItem["DEPTH_LEVEL"] == 1): ?>
          <li class="main-nav__list-item <?= ($arItem["PARAMS"]["TOP_MULTILEVEL"] ? 'multilevel-menu' : 'has-inner') ?>">
            <a href="<?= $arItem["LINK"] ?>">
              <span><?= $arItem["TEXT"] ?></span>
              <svg tabindex="0" class="main-nav-item-expander" width="24" height="24">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/spritemap.svg#sprite-icon-chevron-down"></use>
              </svg>
            </a>

            <?
            if ($arItem["PARAMS"]["TOP_MULTILEVEL"]):
              $multilevel = true;
            ?>
              <div class="top-multilevel-menu">
                <ul class="top-multilevel-menu__list">
                <? else:
                $multilevel = false;
                ?>
                  <div class="main-nav__inner-list-wrapper">
                    <ul class="main-nav__inner-list">
                    <? endif; ?>
                  <? else: ?>
                    <? if ($multilevel):
                      $section = $arItem["IS_PARENT"] && $arItem["DEPTH_LEVEL"] == 3 ? true : false;
                    ?>
                      <li class="<?= ($section ? 'top-multilevel-menu__section-list-item' : 'top-multilevel-menu__list-item') ?>">
                        <a href="<?= $arItem["LINK"] ?>">
                          <span><?= $arItem["TEXT"] ?></span>
                          <? if (!$section): ?>
                            <svg tabindex="0" class="main-nav-item-expander" width="24" height="24">
                              <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/spritemap.svg#sprite-arrow-right"></use>
                            </svg>
                          <? endif; ?>
                        </a>
                        <div class="<?= ($arItem["DEPTH_LEVEL"] == 3 ? 'top-multilevel-menu__inner-list-wrapper' : 'top-multilevel-menu__side') ?>">

                          <ul class="<?= ($arItem["PARAMS"]["IS_PARENT"] && $arItem["PARAMS"]["DEPTH_LEVEL"] == 2 || $arItem["PARAMS"]["DEPTH_LEVEL"] == 3 && $previousLevel == 2 ? 'top-multilevel-menu__section-list' : 'top-multilevel-menu__inner-list') ?>">
                          <? else: ?>
                            <li class="main-nav__inner-list-item">
                              <a href="<?= $arItem["LINK"] ?>">
                                <?= $arItem["TEXT"] ?>
                              </a>
                              <div class="child-parent-wrapper">
                                <ul class="child-parent__inner-list">
                                <? endif ?>
                              <? endif ?>

                            <? else: ?>

                              <? if ($arItem["DEPTH_LEVEL"] == 1): ?>
                                <li class="main-nav__list-item">
                                  <a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
                                </li>
                              <? else: ?>
                                <? if ($multilevel): ?>
                                  <? if ($arItem["DEPTH_LEVEL"] == 2): ?>
                                    <li class="top-multilevel-menu__list-item">
                                      <a href="<?= $arItem["LINK"] ?>"><span><?= $arItem["TEXT"] ?></span></a>
                                      <div class="top-multilevel-menu__side"></div>
                                    </li>
                                  <? else: ?>
                                    <li class="<?= !$arItem["IS_PARENT"] && $arItem["DEPTH_LEVEL"] < 4 && $section ? 'top-multilevel-menu__section-list-item' : 'top-multilevel-menu__inner-list-item' ?>">
                                      <a href="<?= $arItem["LINK"] ?>"><span><?= $arItem["TEXT"] ?></span></a>
                                    </li>
                                  <? endif; ?>
                                <? else: ?>
                                  <li class="main-nav__inner-list-item"><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a></li>
                                <? endif; ?>
                              <? endif ?>
                            <? endif ?>

                            <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>
                          <? endforeach ?>

                          <? if ($previousLevel > 1): ?>
                            <?= str_repeat("</ul></div></li>", ($previousLevel - 1)); ?>
                          <? endif ?>
                                </ul>


                              </div>