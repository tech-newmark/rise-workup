<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$this->setFrameMode(true);

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
?>

<? if ($arResult["SECTIONS_COUNT"] > 0): ?>
  <div class="catalog-section-list catalog-section-list--accordeon-view">
    <!-- <span class="catalog-section-list__title">Каталог</span> -->
    <nav class="accordeon">
      <?
      $intCurrentDepth = 1;
      $boolFirst = true;
      $arFirstLevelSections = [];

      // Группируем разделы по родителям
      foreach ($arResult['SECTIONS'] as &$arSection):
        if ($arSection['RELATIVE_DEPTH_LEVEL'] == 1):
          $arFirstLevelSections[] = $arSection;
        endif;
      endforeach;
      unset($arSection);

      // Выводим разделы первого уровня с аккордеоном
      foreach ($arFirstLevelSections as &$arSection):
        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
      ?>
        <div class="accordeon-item" id="<?= $this->GetEditAreaId($arSection['ID']); ?>">
          <div class="accordeon-header">
            <span><?= $arSection["NAME"]; ?></span>
            <svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
              <use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#chevron'></use>
            </svg>
          </div>
          <div class="accordeon-body">
            <div class="accordeon-body-content">
              <?
              // Ищем и выводим вложенные разделы
              foreach ($arResult['SECTIONS'] as &$arSubSection):
                if ($arSubSection['IBLOCK_SECTION_ID'] == $arSection['ID']):
                  $this->AddEditAction($arSubSection['ID'], $arSubSection['EDIT_LINK'], $strSectionEdit);
                  $this->AddDeleteAction($arSubSection['ID'], $arSubSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
              ?>
                  <a href="<?= $arSubSection["SECTION_PAGE_URL"]; ?>">
                    <?= $arSubSection["NAME"]; ?>
                    <? if ($arParams["COUNT_ELEMENTS"] && $arSubSection['ELEMENT_CNT'] !== null && $arSubSection['ELEMENT_CNT'] > 0): ?>
                      <span>(<?= $arSubSection["ELEMENT_CNT"]; ?>)</span>
                    <? endif; ?>
                  </a>
              <?
                endif;
              endforeach;
              unset($arSubSection);
              ?>
            </div>
          </div>
        </div>
      <?
      endforeach;
      unset($arSection);
      ?>
    </nav>

  </div>
<? endif; ?>