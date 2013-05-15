<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// CSS для кнопок
global $APPLICATION;
//$APPLICATION->AddHeadString('<link href="/bitrix/components/bitrix/crm.company.menu/templates/.default/style.css";  type="text/css" rel="stylesheet" />',true);

$arTabs = array();
$arTabs[] = array(
		"id" => "tab_1",
		"name" => "Основное",
		"title" => "Регистрационная информация",
		"icon" => "",
        "type" => "custom", 
		"fields"=> $arResult["FIELDS"]["tab_1"]
);
?>
<?

    $arButtons["BUTTONS"][] = array(
       "TEXT"=>"Список",
       "TITLE"=>"Список книг",
       "LINK"=>$arParams["LIST_URL"],
       "ICON"=>"btn-list"
    );

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