<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// CSS для кнопок
global $APPLICATION;
$APPLICATION->AddHeadString('<link href="/bitrix/components/bitrix/crm.company.menu/templates/.default/style.css";  type="text/css" rel="stylesheet" />',true);


    ob_start();

    $APPLICATION->IncludeComponent(
        "library:motion_list",
        "",
        Array(
            "IBLOCK_MOTION" => "116",
            "IBLOCK_BOOKS" => "115",
            "DETAIL_URL" => "",
            "EDIT_URL" => "",
            "ID_MOTION" => $arParams["ID_ELEMENT"],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000"
        )
    );

    $gridTmp = ob_get_clean();

$arTabs = array();
$arTabs[] = array(
		"id" => "tab_1",
		"name" => "Основное",
		"title" => "Регистрационная информация",
		"icon" => "",
        "type" => "custom", 
		"fields"=> $arResult["FIELDS"]["tab_1"]
);
$arTabs[] = array(
		"id" => "tab_2",
		"name" => "Перемещения книги",
		"title" => "Перемещения книги",
		"icon" => "",
		//"type" => "custom", "value" => $gridTmp, "colspan" => true,
        "fields" => array(
            array("id" => "GRID", "name" => "Тест", "type" => "custom", "value" => $gridTmp, "colspan" => true),
        )
		//"fields"=> $arResult["FIELDS"]["tab_2"]
);
?>
<?
if($arResult["PERMISSION"] == "W" || $arResult["PERMISSION"] == "X"){
    $arButtons = array(
                  "BUTTONS"=>array(
                     array(
                        "TEXT"=>"Список",
                        "TITLE"=>"Список книг",
                        "LINK"=>$arParams["LIST_URL"],
                        "ICON"=>"btn-list",
                     ),
                     array(
                        "TEXT"=>"Редактировать",
                        "TITLE"=>"Редактировать запись",
                        "LINK"=>$arParams["EDIT_URL"]."?ID=".$_GET["ID"],
                        "ICON"=>"btn-edit",
                     ),
                  ),
               );
} else{
    $arButtons["BUTTONS"][] = array(
       "TEXT"=>"Список",
       "TITLE"=>"Список книг",
       "LINK"=>$arParams["LIST_URL"],
       "ICON"=>"btn-list"
    );    
} 

$APPLICATION->IncludeComponent(
   "bitrix:main.interface.toolbar",
   "",
   $arButtons,
   $component
);?>

<?
    $APPLICATION->IncludeComponent(
        "bitrix:main.interface.form",
        "",
        array(
            "FORM_ID" => $arResult["FORM_ID"],
            "TABS" => $arTabs,
            "BUTTONS"=>array("custom_html"=>"", "standard_buttons"=>false),
            "DATA"=>$arResult["DATA"],
            "THEME_GRID_ID"=>"user_grid",
            "SHOW_SETTINGS"=>"Y",
            "AJAX_MODE"=>"N",
            "AJAX_OPTION_JUMP"=>"N",
            "AJAX_OPTION_STYLE"=>"Y",
        ),
        $component
    );
?>