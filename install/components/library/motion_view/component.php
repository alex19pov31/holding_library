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


if($arParams["ID_ELEMENT"] > 0){
    $aFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_MOTION"],
        "ID" => $arParams["ID_ELEMENT"]
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
 
} else{
	$el = new CIBlockElement();
	$arResult["DATA"] = false;
}

//уникальный идентификатор формы
$arResult["FORM_ID"] = "motion_form";
    
$rsUser = CUser::GetByID($arResult["DATA"]["PROPERTY_USERID_VALUE"]);
$arUser = $rsUser->Fetch();
$arResult["OWNER"] = "<a href=\"/company/personal/user/".$arResult["DATA"]["PROPERTY_USERID_VALUE"]."/\">".$arUser["NAME"]." ".$arUser["LAST_NAME"]."</a>";

$rsBook = CIBlockElement::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_BOOKS"], "ID" => $arParams["PROPERTY_BOOKID_VALUE"]));
$arBook = $rsBook->Fetch();

$arResult["DATA"] = array(
    "BOOK_NAME" => $arBook["NAME"],
    "USERID" => $arResult["OWNER"],
    "TXDATE" => $arResult["DATA"]["PROPERTY_TXDATE_VALUE"],
    "RXDATE" => $arResult["DATA"]["PROPERTY_RXDATE_VALUE"],
    "COMMENT" => $arResult["DATA"]["PROPERTY_COMMENT_VALUE"]
);

// Вкладка основное
$arResult["FIELDS"]["tab_1"] = array(
    array(
        "id" => "BOOK_NAME", 
        "name" => GetMessage("FIELD_BOOK_NAME"),
        "type" => "label"
    ),
    array(
        "id" => "USERID",
        "name" => GetMessage("FIELD_USERID"),
        "type" => "label"
    ),  
    array(
        "id" => "TXDATE",
        "name" => GetMessage("FIELD_TXDATE"),
        "type" => "label"
    ),
    array(
        "id" => "RXDATE",
        "name" => GetMessage("FIELD_RXDATE"),
        "type" => "label"
    ),
    array(
        "id" => "COMMENT",
        "name" => GetMessage("FIELD_COMMENT"),
        "type" => "label"
    )
);

$this->IncludeComponentTemplate();
?>
