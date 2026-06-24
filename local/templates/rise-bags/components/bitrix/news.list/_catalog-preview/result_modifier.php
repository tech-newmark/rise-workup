<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

foreach ($arResult["ITEMS"] as &$arItem):
  $sectionIds = $arItem["PROPERTIES"]["LINKED_CATALOG_SECTIONS"]["VALUE"];

  $arItem["LINKED_SECTIONS"] = [];

  if ($sectionIds):
    $rsSections = CIBlockSection::GetList(
      ["SORT" => "ASC"],
      ["ID" => $sectionIds, "ACTIVE" => "Y"],
      false,
      ["ID", "NAME", "SECTION_PAGE_URL"]
    );

    while ($section = $rsSections->GetNext()):
      $arItem["LINKED_SECTIONS"][] = [
        "ID" => $section["ID"],
        "NAME" => $section["NAME"],
        "URL" => $section["SECTION_PAGE_URL"],
      ];
    endwhile;
  endif;
endforeach;

unset($arItem);
