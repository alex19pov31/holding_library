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


$arResult["PERMISSION"] = CIBlock::GetPermission($arParams["IBLOCK_MOTION"]);
if($arResult["PERMISSION"] == "D"){
    ShowMessage(Array("MESSAGE" => "Доступ запрещен!", "TYPE" => "ERROR"));
    return;
}

$arResult["GRID_ID"] = "motion_grid";

    ob_start();
    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                   'INPUT_NAME' => "PROPERTY_RESPONSIBLE",
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
		array("id"=>"FIND", "name"=>"Найти", "type"=>"quick", "items"=>array("ID"=>"ID книги", "NAME"=>"Название книги"), "default"=>true),
        array("id"=>"PROPERTY_USERID", "name"=>"Сотрудник","type"=>"custom","value"=>$sVal1, "default"=>true),
        array("id"=>"PROPERTY_TXDATE", "name"=>"Дата получения", "type"=>"date", "default"=>true),
        array("id"=>"PROPERTY_RXDATE", "name"=>"Дата возврата", "type"=>"date", "default"=>true)
	);
// Список заголовков таблицы
$arResult["HEADERS"]=array(
		array("id"=>"ID", "name"=>"ID", "sort"=>"id", "editable"=>false),
        array("id"=>"NAME_BOOK", "name"=>"Название книги", "sort"=>"name", "default"=>true),
        array("id"=>"PROPERTY_USERID_VALUE", "name"=>"Сотрудник", "sort"=>"name", "default"=>true),
        array("id"=>"PROPERTY_TXDATE_VALUE", "name"=>"Дата получения", "sort"=>"name", "type"=>"date", "default"=>true),
        array("id"=>"PROPERTY_RXDATE_VALUE", "name"=>"Дата возврата", "sort"=>"name", "type"=>"date", "default"=>true)
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
            case 'PROPERTY_TARGET':
                $aFilter["PROPERTY_TARGET"] = "%".$value."%";
                break;
            case 'PROPERTY_TERRITORY':
                $aFilter["PROPERTY_TERRITORY"] = "%".$value."%";
                break;
            
            default:
                
                break;
        }
    }
}

$aFilter["IBLOCK_ID"] = $arParams["IBLOCK_MOTION"];
if($arParams["ID_MOTION"] > 0) $aFilter["PROPERTY_BOOKID"] = $arParams["ID_MOTION"];

/**
 * Фильтр для даты получения книги
 */
if($aFilter["PROPERTY_TXDATE_from"]&&$aFilter["PROPERTY_TXDATE_to"]){
   $aFilter[">=PROPERTY_TXDATE"] = ConvertDateTime($aFilter["PROPERTY_TXDATE_from"], "YYYY-MM-DD")." 00:00:00";
   $aFilter["<=PROPERTY_TXDATE"] = ConvertDateTime($aFilter["PROPERTY_TXDATE_to"], "YYYY-MM-DD")." 23:59:59";
} elseif ($aFilter["PROPERTY_TXDATE_from"]) {
   $aFilter[">=PROPERTY_TXDATE"] = ConvertDateTime($aFilter["PROPERTY_TXDATE_from"], "YYYY-MM-DD")." 00:00:00";
} elseif($aFilter["PROPERTY_TXDATE_to"]){
   $aFilter["<=PROPERTY_TXDATE"] = ConvertDateTime($aFilter["PROPERTY_TXDATE_to"], "YYYY-MM-DD")." 23:59:59";
}


/**
 * Фильтр для даты возврата книги
 */
if($aFilter["PROPERTY_RXDATE_from"]&&$aFilter["PROPERTY_RXDATE_to"]){
   $aFilter[">=PROPERTY_RXDATE"] = ConvertDateTime($aFilter["PROPERTY_RXDATE_from"], "YYYY-MM-DD")." 00:00:00";
   $aFilter["<=PROPERTY_RXDATE"] = ConvertDateTime($aFilter["PROPERTY_RXDATE_to"], "YYYY-MM-DD")." 23:59:59";
} elseif ($aFilter["PROPERTY_RXDATE_from"]) {
   $aFilter[">=PROPERTY_RXDATE"] = ConvertDateTime($aFilter["PROPERTY_RXDATE_from"], "YYYY-MM-DD")." 00:00:00";
} elseif($aFilter["PROPERTY_RXDATE_to"]) {
   $aFilter["<=PROPERTY_RXDATE"] = ConvertDateTime($aFilter["PROPERTY_RXDATE_to"], "YYYY-MM-DD")." 23:59:59";
}

/**
 * Чистим фильтр
 */
   unset($aFilter["PROPERTY_RXDATE_from"]);
   unset($aFilter["PROPERTY_RXDATE_to"]);
   unset($aFilter["PROPERTY_RXDATE_datesel"]);
   unset($aFilter["PROPERTY_TXDATE_from"]);
   unset($aFilter["PROPERTY_TXDATE_to"]);
   unset($aFilter["PROPERTY_TXDATE_datesel"]);



/*echo "<pre>";
print_r($aFilter);
echo "</pre>";*/

//сортировка
$arResult["SORT"] = $aSort["sort"];
$arResult["SORT_VARS"] = $aSort["vars"];

//это собственно выборка данных с учетом сортировки и фильтра, указанных пользователем
$aSortArg = each($aSort["sort"]);
$iblock_el = new CIBlockElement;
$arSelectFields = array(
    "NAME",
    "ID",
    "PROPERTY_bookId",
    "PROPERTY_userId",
    "PROPERTY_txDate",
    "PROPERTY_rxDate"
);
$db_res=$iblock_el->GetList(array("SORT"=>"ASC"), $aFilter,0,0,$arSelectFields);


//постраничка с учетом размера страницы
$db_res->NavStart($aNav["nPageSize"]);

//в этом цикле построчно заполняем данные для грида
$aRows = array();
while($aRes = $db_res->GetNext())
{
    $rsUser = CUser::GetByID($aRes["PROPERTY_USERID_VALUE"]); 
    $arUser = $rsUser->Fetch();
    $aRes["PROPERTY_USERID_VALUE"] = $arUser["NAME"]." ".$arUser["LAST_NAME"];

    $rsBook = CIBlockElement::GetByID($aRes["PROPERTY_BOOKID_VALUE"]);
    $arBook = $rsBook->Fetch();
    $aRes["NAME_BOOK"] = $arBook["NAME"];
       
	//в этой переменной - поля, требующие нестандартного отображения (не просто значение)
	$aCols = array(
            "ID" => '<a href="'.$APPLICATION->GetCurPage().'?ID='.$aRes["ID"].'">'.$aRes["ID"].'</a>',
            "NAME_BOOK" => '<a href="'.$arParams["BOOK_URL"].'?ID='.$arBook["ID"].'">'.$aRes["NAME_BOOK"].'</a>',
	);

	//это определения для меню действий над строкой
	$aActions = Array(
            array("ICONCLASS"=>"view", "TEXT"=>"Просмотреть", "ONCLICK"=>"jsUtils.Redirect(arguments, '".$arParams["DETAIL_URL"]."?ID=".$aRes["ID"]."')", "DEFAULT"=>true),
	);

	//запомнили данные. "data" - вся выборка,  "editable" - можно редактировать строку или нет
	$aRows[] = array("data"=>$aRes, "actions"=>$aActions, "columns"=>$aCols, "editable"=>($aRes["ID"]==11? false:true));
}

//наши накопленные данные
$arResult["ROWS"] = $aRows;

$this->IncludeComponentTemplate();
?>
