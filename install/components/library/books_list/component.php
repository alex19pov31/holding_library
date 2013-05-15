<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")){
	echo GetMessage("IBLOCK_MODULE_MISSING");
}
if(!CModule::IncludeModule("library")){
	echo GetMessage("MARKETING_MODULE_MISSING");
}

// $arParams - Массив параметров компонента
// $arResult - Массив значений для шаблона

$arResult["PERMISSION"] = CIBlock::GetPermission($arParams["IBLOCK_BOOKS"]);
if($arResult["PERMISSION"] == "D"){
    ShowMessage(Array("MESSAGE" => "Доступ запрещен!", "TYPE" => "ERROR"));
    return;
}

$arResult["GRID_ID"] = "books_grid";

if($_REQUEST["action"] == "delete" && $_REQUEST["ID"] > 0 && check_bitrix_sessid()){
    // Удаляем связанные перемещения
    $rsMotion = CIBlockElement::GetList(array("id"=>"ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_MOTION"], "PROPERTY_BOOKID" => $_REQUEST["ID"]));
    while ($arMotion = $rsMotion->Fetch()) {
        CIBlockElement::Delete($arMotion["ID"]);
    }
    // Удаляем книгу
    CIBlockElement::Delete($_REQUEST["ID"]);
}

// Получаем значения для полей типа "Список"
$PropertyRes = CIBlockPropertyEnum::GetList(
        array(),
        array(
                "IBLOCK_ID"=>$arParams["IBLOCK_BOOKS"], 
                "CODE"=>"type"
            )
        );
while($arProperty = $PropertyRes->Fetch()){
    $arResult["LIST"][$arProperty["PROPERTY_CODE"]][$arProperty["ID"]] = $arProperty["VALUE"];
}

    ob_start();
    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                   'INPUT_NAME' => "PROPERTY_OWNER",
                   //'INPUT_NAME_STRING' => "RESPONSIBLE_STRING",
                   //'INPUT_NAME_SUSPICIOUS' => "RESPONSIBLE_NAME",
                   //'TEXTAREA_MIN_HEIGHT' => 30,
                   //'TEXTAREA_MAX_HEIGHT' => 60,
                   //'INPUT_VALUE_STRING' => implode("\n", $arUsers),
                   'EXTERNAL' => 'A',
                   'MULTIPLE' => 'N'
                   //'SOCNET_GROUP_ID' => ($arParams["TASK_TYPE"] == "group" ? $arParams["OWNER_ID"] : "")
                    )
    );
    $sVal1 = ob_get_contents();
    ob_end_clean();


// Поля для фильтрации
$arResult["FILTER"]=array(
		array("id"=>"FIND", "name"=>"Найти", "type"=>"quick", "items"=>array("ID"=>"ID", "NAME"=>"Название"), "default"=>true),
		array("id"=>"PROPERTY_AUTHOR", "name"=>"Автор", "default"=>true),
		array("id"=>"PROPERTY_THEME", "name"=>"Тематика", "default"=>true),
		array("id"=>"PROPERTY_TYPE", "name"=>"Тип", "type"=>"list", "items"=>array(""=>"Все")+$arResult["LIST"]["type"], "default"=>true),
		array("id"=>"PROPERTY_YEAR", "name"=>"Год издания", "default"=>true),
		array("id"=>"PROPERTY_PUBLISHER", "name"=>"Издатель", "default"=>true),
        array("id"=>"PROPERTY_LOST", "name"=>"Утеряна", "type"=>"list", "items"=>array(""=>"Все", "N"=>"Нет", "Y"=>"Да"), "default"=>true),
        array("id"=>"PROPERTY_AVAILABLE", "name"=>"В наличии", "type"=>"list", "items"=>array(""=>"Все", "Y"=>"Да", "N"=>"Нет"), "default"=>true),
        array("id"=>"PROPERTY_OWNER", "name"=>"Текущий владелец","type"=>"custom","value"=>$sVal1, "default"=>true)
	);
    
// Список заголовков таблицы
$arResult["HEADERS"]=array(
		array("id"=>"ID", "name"=>"ID", "sort"=>"id", "default"=>true, "editable"=>false),
		array("id"=>"NAME", "name"=>"Название", "sort"=>"name", "default"=>true, "editable"=>array("size"=>20, "maxlength"=>255)),
        array("id"=>"PROPERTY_AUTHOR_VALUE", "name"=>"Автор", "sort"=>"author", "default"=>true, "editable"=>false),
        array("id"=>"PROPERTY_THEME_VALUE", "name"=>"Тематика", "sort"=>"theme", "default"=>true, "editable"=>false),
        array("id"=>"PROPERTY_TYPE_VALUE", "name"=>"Тип", "sort"=>"type", "default"=>true, "editable"=>false),
        array("id"=>"PROPERTY_YEAR_VALUE", "name"=>"Год издания", "sort"=>"year", "default"=>true, "editable"=>false),
        array("id"=>"PROPERTY_PUBLISHER_VALUE", "name"=>"Издатель", "sort"=>"publisher", "default"=>true, "editable"=>false)
	);

//инициализируем объект с настройками пользователя для нашего грида
$grid_options = new CGridOptions($arResult["GRID_ID"]);
//какую сортировку сохранил пользователь (передаем то, что по умолчанию)
$aSort = $grid_options->GetSorting(array("sort"=>array("id"=>"desc"), "vars"=>array("by"=>"by", "order"=>"order")));
//размер страницы в постраничке (передаем умолчания)
$aNav = $grid_options->GetNavParams(array("nPageSize"=>10));
//получим текущий фильтр (передаем описанный выше фильтр)
$aFilter = $grid_options->GetFilter($arResult["FILTER"]);

if(isset($aFilter["FIND"])){
	$aFilter[$aFilter["FIND_list"]]=$aFilter["FIND"];
	unset($aFilter["FIND"],$aFilter["FIND_list"]);
}



if(count($aFilter) > 0){
    foreach ($aFilter as $key => $value) {
        switch ($key) {
            case 'NAME':
                $aFilter["NAME"] = "%".$value."%";
                break;
            case 'PROPERTY_THEME':
                $aFilter["PROPERTY_THEME"] = "%".$value."%";
                break;
            case 'PROPERTY_AUTHOR':
                $aFilter["PROPERTY_AUTHOR"] = "%".$value."%";
                break;
            case 'PROPERTY_PUBLISHER':
                $aFilter["PROPERTY_PUBLISHER"] = "%".$value."%";
                break;
            
            default:
                
                break;
        }
    }
}

$aFilter["IBLOCK_ID"] = $arParams["IBLOCK_BOOKS"];


//сортировка
$arResult["SORT"] = $aSort["sort"];
$arResult["SORT_VARS"] = $aSort["vars"];

//это собственно выборка данных с учетом сортировки и фильтра, указанных пользователем
$aSortArg = each($aSort["sort"]);
$iblock_el = new CIBlockElement;
$arSelectFields = array(
    "NAME",
    "ID",
    "PROPERTY_author",
    "PROPERTY_theme",
    "PROPERTY_type",
    "PROPERTY_year",
    "PROPERTY_publisher",
    "PROPERTY_lost",
    "PROPERTY_available",
    "PROPERTY_owner"
);
$db_res=$iblock_el->GetList(array("SORT"=>"ASC"), $aFilter,0,0,$arSelectFields);


//постраничка с учетом размера страницы
$db_res->NavStart($aNav["nPageSize"]);

//в этом цикле построчно заполняем данные для грида
$aRows = array();
while($aRes = $db_res->GetNext())
{
       
	//в этой переменной - поля, требующие нестандартного отображения (не просто значение)
	$aCols = array(
            "ID" => '<a href="'.$arParams["DETAIL_URL"].'?ID='.$aRes["ID"].'">'.$aRes["ID"].'</a>',
            "NAME" => '<a href="'.$arParams["DETAIL_URL"].'?ID='.$aRes["ID"].'">'.$aRes["NAME"].'</a>'
	);

	//это определения для меню действий над строкой
	$aActions = Array(
            array("ICONCLASS"=>"view", "TEXT"=>"Просмотреть книгу", "ONCLICK"=>"jsUtils.Redirect(arguments, '".$arParams["DETAIL_URL"]."?ID=".$aRes["ID"]."')", "DEFAULT"=>true),
	);
    if($arResult["PERMISSION"] == "W" || $arResult["PERMISSION"] == "X"){
        $aActions[] = array("ICONCLASS"=>"edit", "TEXT"=>"Изменить", "ONCLICK"=>"jsUtils.Redirect(arguments, '".$arParams["EDIT_URL"]."?ID=".$aRes["ID"]."')");
        $aActions[] = array("SEPARATOR"=>true);
        if($aRes["PROPERTY_OWNER_VALUE"] > 0){
            $rsMotion = CIBlockElement::GetList(array("ID"=>"DESC"), array("IBLOCK_ID" => $arParams["IBLOCK_MOTION"], "PROPERTY_BOOKID" => $aRes["ID"]));
            $arMotion = $rsMotion->Fetch();
            $aActions[] = array("ICONCLASS"=>"checked", "TEXT"=>"Принять книгу", "ONCLICK"=>"jsUtils.Redirect(arguments, '".$arParams["ACTION_CLOSE_URL"]."?ID=".$arMotion["ID"]."')");
        } else
            $aActions[] = array("ICONCLASS"=>"add", "TEXT"=>"Выдать книгу", "ONCLICK"=>"jsUtils.Redirect(arguments, '".$arParams["ACTION_OPEN_URL"]."?ID=".$aRes["ID"]."')");
        $aActions[] = array("ICONCLASS"=>"delete", "TEXT"=>"Удалить", "ONCLICK"=>"if(confirm('Вы уверены, что хотите удалить данную запись?')) window.location='?action=delete&ID=".$aRes["ID"]."&".bitrix_sessid_get()."';");
    }
        //запомнили данные. "data" - вся выборка,  "editable" - можно редактировать строку или нет
        $aRows[] = array("data"=>$aRes, "actions"=>$aActions, "columns"=>$aCols, "editable"=>($aRes["ID"]==11? false:true));
}

//наши накопленные данные
$arResult["ROWS"] = $aRows;


$this->IncludeComponentTemplate();
?>
