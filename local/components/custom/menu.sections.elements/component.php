<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!function_exists('createMenuArray')){
    function createMenuArray(&$res,&$menuItems,$arParent,$depthLevel){
        foreach($arParent as $item){
            
            $isParent = ($item['IS_SECTION']&&isset($menuItems[$item['ID']]));
            $res[] = array(
                htmlspecialchars($item['~NAME']),
                $item['LINK'],
                array(), //массив доп ссылок
                array(
                    'FROM_IBLOCK' => true,
                    'IS_PARENT' => $isParent,
                    'DEPTH_LEVEL' => $depthLevel,
                ),
            );
            if ($isParent){
                createMenuArray($res,$menuItems,$menuItems[$item['ID']],$depthLevel+1);
            }
        }
    }
}

if (!function_exists('makeMenuItemUrl')){
    function makeMenuItemUrl($template, $baseUrl, array $fields, $fallbackUrl){
        $template = trim((string)$template);
        if ($template === '') {
            return $fallbackUrl;
        }

        $url = CComponentEngine::MakePathFromTemplate($template, array(
            'ID' => $fields['ID'],
            'CODE' => $fields['CODE'],
            'ELEMENT_ID' => $fields['ID'],
            'ELEMENT_CODE' => $fields['CODE'],
            'SECTION_ID' => $fields['ID'],
            'SECTION_CODE' => $fields['CODE'],
        ));

        if ($url === '') {
            return $fallbackUrl;
        }

        if (preg_match('#^(?:[a-z]+:)?//#i', $url) || $url[0] === '/') {
            return $url;
        }

        return rtrim((string)$baseUrl, '/') . '/' . ltrim($url, '/');
    }
}

if(!isset($arParams['CACHE_TIME']))
	$arParams['CACHE_TIME'] = 36000000;

$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

$arParams['DEPTH_LEVEL'] = intval($arParams['DEPTH_LEVEL']);
if($arParams['DEPTH_LEVEL']<=0)
	$arParams['DEPTH_LEVEL']=1;

if($this->StartResultCache()) {
    CModule::IncludeModule('iblock');
    $arSectionId = array();
    $arFilter = array(
            'IBLOCK_ID'=>$arParams['IBLOCK_ID'],
            'GLOBAL_ACTIVE'=>'Y',
            'ACTIVE'=>'Y',
            '<=DEPTH_LEVEL' => $arParams['DEPTH_LEVEL'],
    );
    $arOrder = array(
            'SORT'=>'ASC',
    );
    $rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, array(
            'ID',
            'DEPTH_LEVEL',
            'NAME',
            'CODE',
            'SECTION_PAGE_URL',
            'IBLOCK_SECTION_ID',
    ));
    $menuItems = array();
    while($arSection = $rsSections->GetNext()){
        $arSection['IS_SECTION'] = 1;
        $arSection['LINK'] = makeMenuItemUrl(
            $arParams['SECTION_PAGE_URL'],
            $arParams['SEF_BASE_URL'],
            $arSection,
            $arSection['SECTION_PAGE_URL']
        );
        if ($arSection['IBLOCK_SECTION_ID']){
            $menuItems[$arSection['IBLOCK_SECTION_ID']][] = $arSection;
        } else {
            $menuItems['ROOT'][] = $arSection;
        }
        $arSectionId[] = $arSection['ID'];
    }
    //Получим элементы
    $arSelect = Array('ID', 'NAME', 'CODE', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID');
    $arFilter = Array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ACTIVE' => 'Y',
        array(
        'LOGIC' => 'OR',
            array('SECTION_ID' => $arSectionId),
            array('SECTION_ID' => false),
        ),
    );
    $arOrder = Array('SORT' => 'ASC');
    $res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);  
    while ($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
        $arFields['IS_SECTION'] = 0;
        $arFields['LINK'] = makeMenuItemUrl(
            $arParams['DETAIL_PAGE_URL'],
            $arParams['SEF_BASE_URL'],
            $arFields,
            $arFields['DETAIL_PAGE_URL']
        );
        if ($arFields['IBLOCK_SECTION_ID']){
            $menuItems[$arFields['IBLOCK_SECTION_ID']][] = $arFields;
        } else {
            $menuItems['ROOT'][] = $arFields;
        }
    }
    //Рекурсивно сформируем итоговый массив для меню
    $arResult = array();
    createMenuArray($arResult,$menuItems,$menuItems['ROOT'],1);
    $this->EndResultCache();
}
return $arResult;
?>
