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



//получаем данные книги
if($arParams["ID_MOTION"]>0){
    $aFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_MOTION"],
        "ID" => $arParams["ID_MOTION"]
    );
    $arSelectFields = array(
        "NAME",
        "ID",
        "DETAIL_TEXT",
        "PROPERTY_bookId",
        "PROPERTY_userId",
        "PROPERTY_txDate",
        "PROPERTY_rxDate",
        "PROPERTY_comment"
    );
	$db_res = CIBlockElement::GetList(array("SORT"=>"ASC"), $aFilter,0,0,$arSelectFields);
	$arResult["DATA"] = $db_res->Fetch();  
    
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
    $rsBook = CIBlockElement::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_BOOKS"], "ID" => $arResult["DATA"]["PROPERTY_BOOKID_VALUE"]), 0, 0, $arSelectFields);
    $arBook = $rsBook->Fetch();
} else{
	$el = new CIBlockElement();
	$arResult["DATA"] = false;
}




//здесь обработка POST
if($_REQUEST['apply']||$_REQUEST['saveAndView']){
    /**
     * BOOKID
     * USERID
     * TXDATE
     * RXDATE
     * COMMENT
     */
     
     // Возврат книги
     $el = new CIBlockElement;
     $PROP = array(
        "bookId" => $arResult["DATA"]["PROPERTY_BOOKID_VALUE"],
        "userId" => $arResult["DATA"]["PROPERTY_USERID_VALUE"],
        "txDate" => $arResult["DATA"]["PROPERTY_TXDATE_VALUE"],
        "rxDate" => $_REQUEST["RXDATE"],
        "comment" => $_REQUEST["DESCRIPTION"]
     );
     
     $elementProps = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "PROPERTY_VALUES" => $PROP,
        "DETAIL_TEXT" => $_REQUEST["DESCRIPTION"],
        "ACTIVE" => "Y",
     );

    $el->Update($arParams["ID_MOTION"], $elementProps);
    
     // Делаем книгу доступной, отчищаем поле "Владелец"
     $PROP = array(
        "available" => "Y",
        "owner" => "",
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
     
   $el->Update($arResult["DATA"]["PROPERTY_BOOKID_VALUE"], $elementProps);     
   LocalRedirect($arParams["LIST_URL"]);
}



//уникальный идентификатор формы
$arResult["FORM_ID"] = "motion_form";

    ob_start();
    $ar = array(
        'inputName' => 'DESCRIPTION',
        'inputId' => 'DESCRIPTION',
        'height' => '180',
        'content' => $arResult["DATA"]["DETAIL_TEXT"] ? $arResult["DATA"]["DETAIL_TEXT"] : "",
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
    
    
$rsUser = CUser::GetByID($arResult["DATA"]["PROPERTY_USERID_VALUE"]);
$arUser = $rsUser->Fetch();
$arResult["OWNER"] = "<a href=\"/company/personal/user/".$arResult["DATA"]["PROPERTY_USERID_VALUE"]."/\">".$arUser["NAME"]." ".$arUser["LAST_NAME"]."</a>";


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
        "id" => "USERID",  
        "name" => GetMessage("FIELD_USERID"),
        "type" => "label",
        "value" => $arResult["OWNER"]
    ),
    array(
        "id" => "BOOK_NAME",
        "name" => GetMessage("FIELD_BOOK_NAME"),
        "type"=>"label",
        "value" => $arBook["NAME"]
    ),  
    array(
        "id" => "TXDATE",
        "name" => GetMessage("FIELD_TXDATE"),
        "type"=>"label",
        "value" =>  $arResult["DATA"]["PROPERTY_TXDATE_VALUE"]
    ),   
    array(
        "id" => "RXDATE",
        "name" => GetMessage("FIELD_RXDATE"),
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
