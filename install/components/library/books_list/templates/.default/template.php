<!-- HTML шаблон компонента -->

<?php 
if($arResult["PERMISSION"] == "W" || $arResult["PERMISSION"] == "X"){
    $arButtons = array(
                    "BUTTONS"=>array(
                        array(
                                "TEXT"=>"Добавить книгу",
                                "TITLE"=>"Добавить книгу",
                                "LINK"=>$arParams["EDIT_URL"],
                                "ICON"=>"btn-new",
                        ),
                        array(
                                "TEXT"=>"Импорт из CSV",
                                "TITLE"=>"Импорт из CSV",
                                "LINK"=>$APPLICATION->GetCurPage().'?import',
                                "ICON"=>"btn-excel",
                        ),
                        array(
                                "TEXT"=>"Экспорт в CSV",
                                "TITLE"=>"Экспорт в CSV",
                                "LINK"=>$APPLICATION->GetCurPage().'?export',
                                "ICON"=>"btn-excel",
                        ),
                    ),
            );
} else $arButtons = array();


//покажем панель с кнопками
$APPLICATION->IncludeComponent(
		"bitrix:main.interface.toolbar",
		"",
        $arButtons,
        $component
);



$APPLICATION->IncludeComponent(
		'bitrix:main.interface.grid',
		'',
		array(
				'GRID_ID' => $arResult["GRID_ID"],
				'HEADERS' => $arResult["HEADERS"],
				'SORT' => $arResult['SORT'],
				'SORT_VARS' => $arResult['SORT_VARS'],
				'ROWS' => $arResult['ROWS'],
				'FOOTER' => $arParams['~FOOTER'],
				'EDITABLE' => $arParams['~EDITABLE'],
				'ACTIONS' => $arParams['~ACTIONS'],
				'ACTION_ALL_ROWS' => $arParams['~ACTION_ALL_ROWS'],
				'NAV_OBJECT' => $db_res,
				'FORM_ID' => $arParams['~FORM_ID'],
				'TAB_ID' => $arParams['~TAB_ID'],
				'AJAX_MODE' => $arParams['~AJAX_MODE'],
				'AJAX_ID' => isset($arParams['~AJAX_ID']) ? $arParams['~AJAX_ID'] : '',
				'AJAX_OPTION_JUMP' => isset($arParams['~AJAX_OPTION_JUMP']) ? $arParams['~AJAX_OPTION_JUMP'] : 'N',
				'AJAX_OPTION_HISTORY' => isset($arParams['~AJAX_OPTION_HISTORY']) ? $arParams['~AJAX_OPTION_HISTORY'] : 'N',
				'AJAX_INIT_EVENT' => isset($arParams['~AJAX_INIT_EVENT']) ? $arParams['~AJAX_INIT_EVENT'] : '',
				'FILTER' => $arResult["FILTER"],
				'FILTER_PRESETS' => $arParams['~FILTER_PRESETS']
		),
		$component, array('HIDE_ICONS' => 'Y')
);
?>