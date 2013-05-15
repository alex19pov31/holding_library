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
if($arResult["PERMISSION"] != "W" && $arResult["PERMISSION"] != "X"){
    ShowMessage(Array("MESSAGE" => "Доступ запрещен!", "TYPE" => "ERROR"));
    return;
}


//здесь обработка POST
if($_REQUEST['apply']||$_REQUEST['saveAndView']){
    /**
     * AUTHOR
     * THEME
     * TYPE
     * YEAR
     * PUBLISHER
     * LOST
     * AVAILABLE
     * OWNER
     * DESCRIPTION
     */

     $errMes = false;
     if(!$_REQUEST["NAME"]) $errMes[]="Не введено название!";
     
     $el = new CIBlockElement;
     $PROP = array(
        "author" => $_REQUEST["AUTHOR"],
        "theme" => $_REQUEST["THEME"],
        "type" => $_REQUEST["TYPE"],
        "year" => $_REQUEST["YEAR"],
        "publisher" => $_REQUEST["PUBLISHER"],
        "lost" => $_REQUEST["LOST"],
        "available" => "Y", //$_REQUEST["AVAILABLE"],
        "owner" => ""   //$_REQUEST["OWNER_BY_ID"]
     );
     
     $elementProps = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => $arParams["IBLOCK_BOOKS"],
        "PROPERTY_VALUES" => $PROP,
        "NAME" => $_REQUEST["NAME"],
        "DETAIL_TEXT" => $_REQUEST["DESCRIPTION"],
        //"ACTIVE" => $_REQUEST["ACTIVE"],
     );

     if(!$errMes){
         if($arParams["ID_ELEMENT"] > 0){
                if($el->Update($arParams["ID_ELEMENT"], $elementProps)){
                    ShowMessage(array("MESSAGE" => "Сохранено", "TYPE" => "OK"));
                } else {
                    ShowMessage(array("MESSAGE" => $el->LAST_ERROR,"TYPE" => "ERROR"));
                }
         } else{
                if($RECORD_ID = $el->Add($elementProps)){
                    ShowMessage(array("MESSAGE" => "Сохранено", "TYPE" => "OK"));
                } else {
                    ShowMessage(array("MESSAGE" => $el->LAST_ERROR,"TYPE" => "ERROR"));
                }
         }
         if($_REQUEST['saveAndView']) LocalRedirect($arParams["LIST_URL"]);
     } else {
         foreach ($errMes as $message) {
            ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE"=>$message));
         }         
     }
}




//получаем данные книги
if($arParams["ID_ELEMENT"]>0){
    $aFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_ACTION"],
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
    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                   'INPUT_NAME' => "OWNER_ID",
                   'INPUT_NAME_STRING' => "OWNER_STRING",
                   'INPUT_NAME_SUSPICIOUS' => "OWNER_NAME",
                   'TEXTAREA_MIN_HEIGHT' => 30,
                   'TEXTAREA_MAX_HEIGHT' => 60,
                   'INPUT_VALUE' => isset($arResult["DATA"]["OWNER"]) ? $arResult["DATA"]["OWNER"] : '',
                   //'INPUT_VALUE_STRING' => implode("\n", $arUsers),
                   'EXTERNAL' => 'A',
                   'MULTIPLE' => 'N',
                   //'SOCNET_GROUP_ID' => ($arParams["TASK_TYPE"] == "group" ? $arParams["OWNER_ID"] : "")
         )
    );
    $sVal1 = ob_get_contents();
    ob_end_clean();

    ob_start();
    $ar = array(
        'inputName' => 'DESCRIPTION',
        'inputId' => 'DESCRIPTION',
        'height' => '180',
        'content' => isset($arResult["DATA"]["DETAIL_TEXT"]) ? $arResult["DATA"]["DETAIL_TEXT"] : '',
        'bUseFileDialogs' => false,
        'bFloatingToolbar' => false,
        'bArisingToolbar' => false,
        'bResizable' => true,
        'bSaveOnBlur' => true,
        'toolbarConfig' => array(
            'Bold', 'Italic', 'Underline', 'Strike',
            'BackColor', 'ForeColor',
            'CreateLink', 'DeleteLink',
            'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent'
        )
    );  
    $LHE = new CLightHTMLEditor;
    $LHE->Show($ar);
    $sValEdit = ob_get_contents();
    ob_end_clean();


// Вкладка основное
$arResult["FIELDS"]["tab_1"] = array(
    /**
     * AUTHOR
     * THEME
     * TYPE
     * YEAR
     * PUBLISHER
     * LOST
     * AVAILABLE
     * OWNER
     * DESCRIPTION
     */
    array(
        "id" => "NAME",
        "required"=>true,   
        "name" => GetMessage("FIELD_NAME"),
        "value" => $arResult["DATA"]["NAME"]
    ),
    array(
        "id" => "THEME",
        "name" => GetMessage("FIELD_THEME"),
        "value" => $arResult["DATA"]["PROPERTY_THEME_VALUE"]
    ),  
	array(
		"id" => "AUTHOR",
		"name" => GetMessage("FIELD_AUTHOR"),
        "value" => $arResult["DATA"]["PROPERTY_AUTHOR_VALUE"]
	),
	array(
		"id" => "TYPE",
		"name" => GetMessage("FIELD_TYPE"),
		"items" => $arResult["LIST"]["type"],
		"type" => "list",
        "value" => $arResult["DATA"]["PROPERTY_TYPE_ENUM_ID"]
	),
	array(
		"id" => "YEAR",
		"name" => GetMessage("FIELD_YEAR"),
        "value" => $arResult["DATA"]["PROPERTY_YEAR_VALUE"]
	),
    array(
        "id" => "PUBLISHER",
        "name" => GetMessage("FIELD_PUBLISHER"),
        "value" => $arResult["DATA"]["PROPERTY_PUBLISHER_VALUE"]
    ),
    /*array(
        "id" => "OWNER",
        "name" => GetMessage("FIELD_OWNER"),
        "componentParams" => array(
                "NAME" => "action_edit_owner",
                "INPUT_NAME" => "OWNER_BY_ID",
                "SEARCH_INPUT_NAME" => "OWNER_BY_NAME",
        ),
        "type" => "intranet_user_search",
        "value" => $arResult["DATA"]["PROPERTY_OWNER_VALUE"] ? $arResult["DATA"]["PROPERTY_OWNER_VALUE"] : ""
    ),*/
    array(
        "id" => "LOST",
        "name" => GetMessage("FIELD_LOST"),
        "params" => array(),
        "type" => "vertical_checkbox",
        "title" => "Указывается утеряных для книг",
        "value" => ($arResult["DATA"]["PROPERTY_LOST_VALUE"] != 'N' && isset($arResult["DATA"]["PROPERTY_LOST_VALUE"])) ? 'Y' : 'N'
    ),
    /*array(
        "id" => "AVAILABLE",
        "name" => GetMessage("FIELD_AVAILABLE"),
        "params" => array(),
        "type" => "vertical_checkbox",
        "title" => "Указывается для книг которые есть в наличии",
        "value" => (isset($arResult["DATA"]["PROPERTY_AVAILABLE_VALUE"])) ? $arResult["DATA"]["PROPERTY_AVAILABLE_VALUE"] : 'Y'
    ),*/
    array(
        "id" => "DESCRIPTION",
        "name" => GetMessage("FIELD_DESCRIPTION"),
        "type" => "vertical_container", //textarea
        "value" => $sValEdit
    )
);


// Вкладка дополнительно
$arResult["FIELDS"]["tab_2"] = array(

);

$this->IncludeComponentTemplate();
?>
