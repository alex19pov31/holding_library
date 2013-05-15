<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Подключение модуля
if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	// Список гупп параметров
	"GROUPS" => array(
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
        "ID_ELEMENT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("ID_ELEMENT"),
            "TYPE" => "STRING"
        ),
        "LIST_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("LIST_URL"),
            "TYPE" => "STRING"
        ),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
?>
