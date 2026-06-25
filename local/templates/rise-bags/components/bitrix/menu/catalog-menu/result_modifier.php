<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$groupedMenu = [];

if (!CModule::IncludeModule("iblock")) {
  $arResult = $groupedMenu;
  return;
}

$catalogBaseUrl = "/catalog/";

$isHiddenInMenu = static function ($value): bool {
  if (is_array($value)) {
    foreach ($value as $item) {
      if ($item !== null && $item !== '' && $item !== '0' && $item !== 0 && $item !== false) {
        return true;
      }
    }

    return false;
  }

  $value = strtoupper(trim((string)$value));

  return $value !== '' && $value !== '0' && $value !== 'N' && $value !== 'FALSE';
};

// Catalog header menu must not depend on the current page's left menu files.
$sectionsById = [];
$rsSections = CIBlockSection::GetList(
  ["LEFT_MARGIN" => "ASC"],
  [
    "IBLOCK_ID" => 2,
    "ACTIVE" => "Y",
    "GLOBAL_ACTIVE" => "Y",
    "<=DEPTH_LEVEL" => 2,
  ],
  false,
  ["ID", "NAME", "CODE", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "PICTURE", "DETAIL_PICTURE", "UF_*"]
);

while ($section = $rsSections->GetNext(false, false)) {
  if ($isHiddenInMenu($section["UF_HIDE_IN_MENU"] ?? null)) {
    continue;
  }

  $imageId = (int)$section["PICTURE"] > 0 ? (int)$section["PICTURE"] : (int)$section["DETAIL_PICTURE"];
  $section["IMAGE_SRC"] = $imageId > 0 ? (string)CFile::GetPath($imageId) : "";
  $sectionsById[(int)$section["ID"]] = $section;
}

$getSectionCodePath = static function (int $sectionId) use (&$getSectionCodePath, $sectionsById): string {
  if (!isset($sectionsById[$sectionId])) {
    return "";
  }

  $section = $sectionsById[$sectionId];
  $code = trim((string)$section["CODE"], "/");
  $parentId = (int)$section["IBLOCK_SECTION_ID"];

  if ($parentId <= 0) {
    return $code;
  }

  $parentPath = $getSectionCodePath($parentId);
  if ($parentPath === "") {
    return $code;
  }

  return $parentPath . "/" . $code;
};

$makeMenuItem = static function (array $section) use ($catalogBaseUrl, $getSectionCodePath): array {
  $id = (int)$section["ID"];
  $codePath = $getSectionCodePath($id);
  $link = $codePath !== "" ? rtrim($catalogBaseUrl, "/") . "/" . $codePath . "/" : $catalogBaseUrl;

  return [
    "TEXT" => htmlspecialcharsbx((string)$section["NAME"]),
    "LINK" => $link,
    "SELECTED" => false,
    "PERMISSION" => "X",
    "ADDITIONAL_LINKS" => [],
    "ITEM_TYPE" => "D",
    "ITEM_INDEX" => 0,
    "PARAMS" => [
      "FROM_IBLOCK" => true,
      "SECTION_ID" => $id,
      "DEPTH_LEVEL" => (int)$section["DEPTH_LEVEL"],
    ],
    "CHAIN" => [],
    "DEPTH_LEVEL" => (int)$section["DEPTH_LEVEL"],
    "IS_PARENT" => false,
    "IMAGE_SRC" => $section["IMAGE_SRC"],
  ];
};

// Группируем: разделы 1-го уровня инфоблока -> PARENT, их подразделы -> CHILD
foreach ($sectionsById as $parentId => $parentSection) {
  if ((int)$parentSection["DEPTH_LEVEL"] !== 1) {
    continue;
  }

  $parentMenuItem = $makeMenuItem($parentSection);
  $children = [];

  foreach ($sectionsById as $childSection) {
    if (
      (int)$childSection["DEPTH_LEVEL"] !== 2 ||
      (int)$childSection["IBLOCK_SECTION_ID"] !== $parentId
    ) {
      continue;
    }

    $children[] = $makeMenuItem($childSection);
  }

  $parentMenuItem["IS_PARENT"] = !empty($children);
  $parentMenuItem["PARAMS"]["IS_PARENT"] = !empty($children);

  $groupedMenu[] = [
    "PARENT" => $parentMenuItem,
    "CHILD" => $children,
  ];
}

$arResult = $groupedMenu;
