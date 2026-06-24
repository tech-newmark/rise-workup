<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$groupedMenu = [];

if (!CModule::IncludeModule("iblock")) {
  $arResult = $groupedMenu;
  return;
}

// Получаем все активные разделы инфоблока #2
$sectionsById = [];
$rsSections = CIBlockSection::GetList(
  ["LEFT_MARGIN" => "ASC"],
  ["IBLOCK_ID" => 2, "ACTIVE" => "Y"],
  false,
  ["ID", "CODE", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "PICTURE", "DETAIL_PICTURE"]
);

while ($section = $rsSections->Fetch()) {
  $imageId = (int)$section["PICTURE"] > 0 ? (int)$section["PICTURE"] : (int)$section["DETAIL_PICTURE"];
  $section["IMAGE_SRC"] = $imageId > 0 ? (string)CFile::GetPath($imageId) : "";
  $sectionsById[(int)$section["ID"]] = $section;
}

// Индексируем пункты меню по SECTION_ID и CODE
$menuItemsBySectionId = [];
$menuItemsByCode = [];

foreach ($arResult as $item) {
  if (!empty($item["PARAMS"]["SECTION_ID"])) {
    $menuItemsBySectionId[(int)$item["PARAMS"]["SECTION_ID"]] = $item;
  }
  $linkPath = trim((string)$item["LINK"], "/");
  if ($linkPath !== "") {
    $parts = explode("/", $linkPath);
    $code = (string)end($parts);
    if ($code !== "") {
      $menuItemsByCode[$code] = $item;
    }
  }
}

$findMenuItem = static function (array $section) use ($menuItemsBySectionId, $menuItemsByCode): ?array {
  $id = (int)$section["ID"];
  if (isset($menuItemsBySectionId[$id])) {
    return $menuItemsBySectionId[$id];
  }
  if (!empty($section["CODE"]) && isset($menuItemsByCode[$section["CODE"]])) {
    return $menuItemsByCode[$section["CODE"]];
  }
  return null;
};

// Группируем: разделы 1-го уровня инфоблока → PARENT, их подразделы → CHILD
foreach ($sectionsById as $parentId => $parentSection) {
  if ((int)$parentSection["DEPTH_LEVEL"] !== 1) {
    continue;
  }

  $parentMenuItem = $findMenuItem($parentSection);
  if ($parentMenuItem === null) {
    continue;
  }

  $parentMenuItem["IMAGE_SRC"] = $parentSection["IMAGE_SRC"];

  $children = [];
  foreach ($sectionsById as $childSection) {
    if (
      (int)$childSection["DEPTH_LEVEL"] !== 2 ||
      (int)$childSection["IBLOCK_SECTION_ID"] !== $parentId
    ) {
      continue;
    }

    $childMenuItem = $findMenuItem($childSection);
    if ($childMenuItem === null) {
      continue;
    }

    $childMenuItem["IMAGE_SRC"] = $childSection["IMAGE_SRC"];
    $children[] = $childMenuItem;
  }

  $groupedMenu[] = [
    "PARENT" => $parentMenuItem,
    "CHILD"  => $children,
  ];
}

$arResult = $groupedMenu;
