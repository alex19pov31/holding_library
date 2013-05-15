<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")){
	echo GetMessage("IBLOCK_MODULE_MISSING");
}
if (!CModule::IncludeModule("crm"))
{
	ShowError(GetMessage("CRM_MODULE_NOT_INSTALLED"));
	return;
}

// $arParams - Массив параметров компонента
// $arResult - Массив значений для шаблона

$arResult["PERMISSION"] = CIBlock::GetPermission($arParams["IBLOCK_BOOKS"]);
if($arResult["PERMISSION"] == "D"){
    ShowMessage(Array("MESSAGE" => "Доступ запрещен!", "TYPE" => "ERROR"));
    return;
}


//здесь обработка POST
if($_REQUEST['apply']||$_REQUEST['saveAndView']){
    /**
     * NAME
     * TARGET
     * ACTIVE
     * FAMILY
     * TYPE
     * SOURCE
     * STATUS
     * ASSIGNED_BY_ID
     * TERRITORY
     * DATESTART
     * DATEFINISH 
     * TARGETCLIENT
     * COUNTOBJECT
     * PLANNEDRESPONSE
     * PLANNEDBUDJET
     * ACTUALBUDJET
     * SALESPROGRAMM
     * ACTUALSALES
     * CONTACT_ID
     * TEAMUSERS_ID
     */

     $el = new CIBlockElement;
     $PROP = array(
        "author" => $_REQUEST["AUTHOR"],
        "theme" => $_REQUEST["THEME"],
        "type" => $_REQUEST["TYPE"],
        "year" => $_REQUEST["YEAR"],
        "publisher" => $_REQUEST["PUBLISHER"],
        "lost" => $_REQUEST["LOST"],
        "available" => $_REQUEST["AVAILABLE"],
        "owner" => $_REQUEST["OWNER_BY_ID"]
     );
     
     $elementProps = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => $arParams["IBLOCK_BOOKS"],
        "PROPERTY_VALUES" => $PROP,
        "NAME" => $_REQUEST["NAME"],
        "ACTIVE" => $_REQUEST["ACTIVE"],
     );
     
     if($arParams["ID_ELEMENT"] > 0){
         if($elementProps["NAME"]){
            if($el->Update($arParams["ID_ELEMENT"], $elementProps)){
                ShowMessage(array("MESSAGE" => "Изменена запись с ID: ".$arParams["ID_ELEMENT"], "TYPE" => "OK"));
            } else {
                ShowMessage(array("MESSAGE" => $el->LAST_ERROR,"TYPE" => "ERROR"));
            }
          } else{
                ShowMessage(array("MESSAGE" => "Не введено название", "TYPE" => "ERROR")); 
          }
     } else{
        if($elementProps["NAME"]){
            if($RECORD_ID = $el->Add($elementProps)){
                ShowMessage(array("MESSAGE" => "Создана новая запись с ID: ".$RECORD_ID, "TYPE" => "OK"));
            } else {
                ShowMessage(array("MESSAGE" => $el->LAST_ERROR,"TYPE" => "ERROR"));
            }
        } else{
            ShowMessage(array("MESSAGE" => "Не введено название", "TYPE" => "ERROR")); 
        }  
     }
}




//получаем данные пользователя
if($arParams["ID_ELEMENT"]>0){
    $aFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_BOOKS"],
        "ID" => $arParams["ID_ELEMENT"]
    );
    $arSelectFields = array(
        "NAME",
        "ID",
        "DETAIL_TEXT",
        "PROPERTY_author",
        "PROPERTY_theme",
        "PROPERTY_type",
        "PROPERTY_year",
        "PROPERTY_publisher",
        "PROPERTY_lost",
        "PROPERTY_available",
        "PROPERTY_owner",
    );
	$db_res = CIBlockElement::GetList(array("SORT"=>"ASC"), $aFilter,0,0,$arSelectFields);
	$arResult["DATA"] = $db_res->Fetch();
    
    
    /*$db_res = CIBlockElement::GetList(array("SORT"=>"ASC"), $aFilter,0,0, array("PROPERTY_teamusers"));
    while($arRes = $db_res->Fetch()) $arResult["DATA"]["TEAMUSERS"][] = $arRes["PROPERTY_TEAMUSERS_VALUE"];
    
    $db_res = CIBlockElement::GetList(array("SORT"=>"ASC"), $aFilter,0,0, array("PROPERTY_teamcontacts"));
    while($arRes = $db_res->Fetch()) $arResult["DATA"]["TEAMCONTACTS"][] = $arRes["PROPERTY_TEAMCONTACTS_VALUE"];*/

    /*echo "<pre>";
    print_r($arResult["DATA"]);
    echo "</pre>";   */     
 
} else{
	$el = new CIBlockElement();
	$arResult["DATA"] = false;
}

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

//уникальный идентификатор формы
$arResult["FORM_ID"] = "book_form";

    ob_start();
    $GLOBALS['APPLICATION']->IncludeComponent('bitrix:crm.entity.selector',
        '',
        array(
            "ENTITY_TYPE" => "CONTACT",
            "INPUT_NAME" => "CONTACT_ID",
            'INPUT_VALUE' => isset($arResult["DATA"]["TEAMCONTACTS"]) ? $arResult["DATA"]["TEAMCONTACTS"] : '',
            'FORM_NAME' => $arResult["FORM_ID"] ,
            'MULTIPLE' => 'Y'
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    $sVal = ob_get_contents();
    ob_end_clean();

    ob_start();
    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                   'INPUT_NAME' => "OWNER_ID",
                   'INPUT_NAME_STRING' => "OWNER_STRING",
                   'INPUT_NAME_SUSPICIOUS' => "OWNER_NAME",
                   'TEXTAREA_MIN_HEIGHT' => 60,
                   'TEXTAREA_MAX_HEIGHT' => 80,
                   'INPUT_VALUE' => isset($arResult["DATA"]["TEAMUSERS"]) ? $arResult["DATA"]["OWNER"] : '',
                   //'INPUT_VALUE_STRING' => implode("\n", $arUsers),
                   'EXTERNAL' => 'A',
                   'MULTIPLE' => 'Y',
                   //'SOCNET_GROUP_ID' => ($arParams["TASK_TYPE"] == "group" ? $arParams["OWNER_ID"] : "")
         )
    );
    $sVal1 = ob_get_contents();
    ob_end_clean();
    
$rsUser = CUser::GetByID($arResult["DATA"]["PROPERTY_OWNER_VALUE"]);
$arUser = $rsUser->Fetch();
$arResult["OWNER"] = "<a href=\"/company/personal/user/".$arResult["DATA"]["PROPERTY_OWNER_VALUE"]."/\">".$arUser["NAME"]." ".$arUser["LAST_NAME"]."</a>";

$arResult["DATA"] = array(
    "NAME" => $arResult["DATA"]["NAME"],
    "THEME" => $arResult["DATA"]["PROPERTY_THEME_VALUE"],
    "AUTHOR" => $arResult["DATA"]["PROPERTY_AUTHOR_VALUE"],
    "TYPE" => $arResult["DATA"]["PROPERTY_TYPE_VALUE"],
    "YEAR" => $arResult["DATA"]["PROPERTY_YEAR_VALUE"],
    "PUBLISHER" => $arResult["DATA"]["PROPERTY_PUBLISHER_VALUE"],
    "OWNER" => $arResult["OWNER"],
    "LOST" => ($arResult["DATA"]["PROPERTY_LOST_VALUE"] != 'N' && isset($arResult["DATA"]["PROPERTY_LOST_VALUE"])) ? 'Да' : 'Нет',
    "AVAILABLE" => ($arResult["DATA"]["PROPERTY_AVAILABLE_VALUE"] != 'N' && isset($arResult["DATA"]["PROPERTY_AVAILABLE_VALUE"])) ? 'Да' : 'Нет',
    "DESCRIPTION" => $arResult["DATA"]["DETAIL_TEXT"]
);

// Вкладка основное
$arResult["FIELDS"]["tab_1"] = array(
    array(
        "id" => "NAME", 
        "name" => GetMessage("FIELD_NAME"),
        "type" => "label"
    ),
    array(
        "id" => "THEME",
        "name" => GetMessage("FIELD_THEME"),
        "type" => "label"
    ),  
    array(
        "id" => "AUTHOR",
        "name" => GetMessage("FIELD_AUTHOR"),
        "type" => "label"
    ),
    array(
        "id" => "TYPE",
        "name" => GetMessage("FIELD_TYPE"),
        "type" => "label"
    ),
    array(
        "id" => "YEAR",
        "name" => GetMessage("FIELD_YEAR"),
        "type" => "label"
    ),
    array(
        "id" => "PUBLISHER",
        "name" => GetMessage("FIELD_PUBLISHER"),
        "type" => "label",
    ),
    array(
        "id" => "OWNER",
        "name" => GetMessage("FIELD_OWNER"),
        "type" => "label"
    ),
    array(
        "id" => "LOST",
        "name" => GetMessage("FIELD_LOST"),
        "type" => "label"
    ),
    array(
        "id" => "AVAILABLE",
        "name" => GetMessage("FIELD_AVAILABLE"),
        "type" => "label"
    ),
    array(
        "id" => "DESCRIPTION",
        "name" => GetMessage("FIELD_DESCRIPTION"),
        "type" => "label"
    )

);

$this->IncludeComponentTemplate();
?>
