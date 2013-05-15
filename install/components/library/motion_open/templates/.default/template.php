<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

$arTabs = array();
$arTabs[] = array(
		"id" => "tab_1",
		"name" => "Основное",
		"title" => "Регистрационная информация",
		"icon" => "",
		"fields"=> $arResult["FIELDS"]["tab_1"]
);
/*$arTabs[] = array(
		"id" => "tab_2",
		"name" => "Дополнительно",
		"title" => "Регистрационная информация",
		"icon" => "",
		"fields"=> $arResult["FIELDS"]["tab_2"]
);*/
?>
<?$APPLICATION->IncludeComponent(
   "bitrix:main.interface.toolbar",
   "",
   array(
      "BUTTONS" => $arParams["ID_ELEMENT"] ?
      // Если указан ID элемента
      array(
         array(
            "TEXT"=>"Список",
            "TITLE"=>"Список книг",
            "LINK"=>$arParams["LIST_URL"],
            "ICON"=>"btn-list",
         ),
         array(
            "TEXT"=>"Редактирование",
            "TITLE"=>"Редактирование",
            "LINK"=>$arParams["EDIT_URL"]."?ID=".$arParams["ID_ELEMENT"],
            "ICON"=>"btn-settings",
         ),
      ) :
      // При создании новой записи
      array(
         array(
            "TEXT"=>"Список",
            "TITLE"=>"Список книг",
            "LINK"=>$arParams["LIST_URL"],
            "ICON"=>"btn-list",
         )
     )      
   ),
   $component
);?>

<?$APPLICATION->IncludeComponent(
   "bitrix:crm.interface.form",
   "edit",
   array(
//идентификатор формы
      "FORM_ID"=>$arResult["FORM_ID"],
	  'EMPHASIZED_HEADERS' => array(),
//описание вкладок формы
      "TABS"=>$arTabs,
//кнопки формы, возможны кастомные кнопки в виде html в "custom_html"
      "BUTTONS"=>array(
            "back_url"=>$arParams["LIST_URL"], 
            "standard_buttons"=>true,
            'custom_html' => '<input type="hidden" name="book_id" value="'.$arParams["ID_ELEMENT"].'"/>'
           ),
//данные для редактирования
      "DATA"=>$arResult["DATA"],
   ),
   $component
);
?>