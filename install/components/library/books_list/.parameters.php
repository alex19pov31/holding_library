<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Подключение модуля
/*if(!CModule::IncludeModule("module_name"))
	return;*/

$arComponentParameters = array(
	// Список гупп параметров
	"GROUPS" => array(
		/*"GROUP_NAME" => array(
			"SORT" => 100,				// Индекс сортировки
			"NAME" => GetMessage("GROUP_NAME")	// Имя группы параметров
		)*/
	),
	// Список параметров
	"PARAMETERS" => array(
		"IBLOCK_BOOKS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_BOOKS"),
			"TYPE" => "STRING"
		),
		"IBLOCK_MOTION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_MOTION"),
			"TYPE" => "STRING"
		),
		"DETAIL_URL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DETAIL_URL"),
			"TYPE" => "STRING"
		),
        "EDIT_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EDIT_URL"),
            "TYPE" => "STRING"
        ),
        "ACTION_OPEN_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ACTION_OPEN_URL"),
            "TYPE" => "STRING"
        ),
        "ACTION_CLOSE_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ACTION_CLOSE_URL"),
            "TYPE" => "STRING"
        ),
        "ACTION_VIEW_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ACTION_VIEW_URL"),
            "TYPE" => "STRING"
        ),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
?>
