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


$arResult["PERMISSION"] = CIBlock::GetPermission($arParams["IBLOCK_MOTION"]);
if($arResult["PERMISSION"] != "W" && $arResult["PERMISSION"] != "X"){
    ShowMessage(Array("MESSAGE" => "Доступ запрещен!", "TYPE" => "ERROR"));
    return;
}

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
 $rsBook = CIBlockElement::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_BOOKS"], "ID" => $arParams["ID_BOOK"]), 0, 0, $arSelectFields);
 $arBook = $rsBook->Fetch();




//здесь обработка POST
if($_REQUEST['apply']||$_REQUEST['saveAndView']){
    /**
     * BOOKID
     * USERID
     * TXDATE
     * RXDATE
     * COMMENT
     */

     $errMes = false;
     if(!$_REQUEST["USER_BY_ID"]) $errMes[]="Не выбран сотрудник!";
     
     if(!$errMes){
         /**
          * Открываем новое перемещение
          */
         $el = new CIBlockElement;
         $PROP = array(
            "bookId" => $arParams["ID_BOOK"],
            "userId" => $_REQUEST["USER_BY_ID"],
            "txDate" => $_REQUEST["TXDATE"],
            //"rxDate" => $_REQUEST["RXDATE"],
            //"comment" => $_REQUEST["DESCRIPTION"]
         );
         
         $elementProps = array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $arParams["IBLOCK_MOTION"],
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $arBook["NAME"]." ".$_REQUEST["TXDATE"],
            "DETAIL_TEXT" => $_REQUEST["DESCRIPTION"],
            "ACTIVE" => "Y",
         );
    
        $el->Add($elementProps);    
        
        /**
         * Делаем книгу недоступной и назначаем владельца
         */
         $PROP = array(
            "available" => "N",
            "owner" => $_REQUEST["USER_BY_ID"],
            "theme" => $arBook["PROPERTY_THEME_VALUE"],
            "type" => $arBook["PROPERTY_TYPE_ENUM_ID"],
            "year" => $arBook["PROPERTY_YEAR_VALUE"],
            "publisher" => $arBook["PROPERTY_PUBLISHER_VALUE"],
            "author" => $arBook["PROPERTY_AUTHOR_VALUE"],
            "lost" => $arBook["PROPERTY_LOST_VALUE"]
         );
         
         $elementProps = array(
            "IBLOCK_ID" => $arParams["IBLOCK_BOOKS"],
            "PROPERTY_VALUES" => $PROP,     
         );
         
         $el->Update($arParams["ID_BOOK"], $elementProps);
         
         // Перенаправляем на список книг
         LocalRedirect($arParams["LIST_URL"]);
     } else{
         foreach ($errMes as $message) {
            ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE"=>$message));
         }
     }
}




//получаем данные книги
/*if($arParams["ID_ELEMENT"]>0){
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
}*/

//уникальный идентификатор формы
$arResult["FORM_ID"] = "motion_form";

   /* ob_start();
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
    ob_end_clean();*/

    ob_start();
    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                   'INPUT_NAME' => "OWNER_ID",
                   'INPUT_NAME_STRING' => "OWNER_STRING",
                   'INPUT_NAME_SUSPICIOUS' => "OWNER_NAME",
                   'TEXTAREA_MIN_HEIGHT' => 30,
                   'TEXTAREA_MAX_HEIGHT' => 60,
                   //'INPUT_VALUE' => isset($arResult["DATA"]["OWNER"]) ? $arResult["DATA"]["OWNER"] : '',
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
        'content' => '',
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
        "id" => "BOOK_NAME",
        "name" => GetMessage("FIELD_BOOK_NAME"),
        "type"=>"label",
        "value" => "<b>".$arBook["NAME"]."</b>"
    ),  
    array(
        "id" => "USERID",
        "required"=>true,   
        "name" => GetMessage("FIELD_USERID"),
        "componentParams" => array(
                "NAME" => "action_edit_user",
                "INPUT_NAME" => "USER_BY_ID",
                "SEARCH_INPUT_NAME" => "USER_BY_NAME",
        ),
        "type" => "intranet_user_search"
    ),
    array(
        "id" => "TXDATE",
        "name" => GetMessage("FIELD_TXDATE"),
        "type"=>"date",
        "value" => ConvertTimeStamp(false, "FULL")  
    ),  
	array(
		"id" => "COMMENT",
		"name" => GetMessage("FIELD_COMMENT"),
		"type" => "vertical_container", //textarea
        "value" => $sValEdit,
        //"value" => $arResult["DATA"]["PROPERTY_AUTHOR_VALUE"]
	)
);


// Вкладка дополнительно
/*$arResult["FIELDS"]["tab_2"] = array(

);*/

$this->IncludeComponentTemplate();
?>
