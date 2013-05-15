<?php
if(!check_bitrix_sessid()) return;
$obBlocktype = new CIBlockType;
$ib = new CIBlock;
$ibp = new CIBlockProperty;
// Список сайтов
$site_res = CSite::GetList($by="sort",$order="desc",Array("ACTIVE"=>"Y"));
while ($site = $site_res->Fetch()) $site_list[] = $site["ID"];


// Новый тип инфоблоков
$arFields = Array(
		'ID'=>'books_library',
		'SECTIONS'=>'Y',
		'IN_RSS'=>'N',
		'SORT'=>100,
		'LANG'=>Array(
				'en'=>Array(
						'NAME'=>'Books ibrary',
						'SECTION_NAME'=>'Sections',
						'ELEMENT_NAME'=>'Elements'
				),
				'ru'=>Array(
						'NAME'=>'Библиотека',
						'SECTION_NAME'=>'Разделы',
						'ELEMENT_NAME'=>'Элементы'
				)
		)
);
$res = $obBlocktype->Add($arFields);
if(!$res) {
	echo 'Error: '.$obBlocktype->LAST_ERROR.'<br>';
	return;
}

// Инфоблок книги
$arFields = Array(
		"ACTIVE" => "Y",
		"NAME" => "Книги",
		"CODE" => "books",
		"IBLOCK_TYPE_ID" => $res,
		"SITE_ID" => $site_list
);
$books = $ib->Add($arFields);
if(!$books) {
	echo 'Error: '.$ib->LAST_ERROR.'<br>';
	return;
}
else{
	$arFields = Array();
	$arFields[] = Array(
			"NAME" => GetMessage("BOOKS_AUTHOR"),
			"ACTIVE" => "Y",
			"SORT" => "200",
			"CODE" => "author",
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
            "IBLOCK_ID" => $books
	);
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_THEME"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "theme",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "IBLOCK_ID" => $books
    );
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_TYPE"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "type",
            "PROPERTY_TYPE" => "L",
            "MULTIPLE" => "N",
            "IBLOCK_ID" => $books,
            "VALUES" => Array(
                Array("VALUE" => "Книга", "DEF" => "Y", "SORT" => "100"),
                Array("VALUE" => "Журнал", "DEF" => "N", "SORT" => "200")
            )   
    );
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_YEAR"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "year",
            "PROPERTY_TYPE" => "N",
            "MULTIPLE" => "N",
            "IBLOCK_ID" => $books
    );
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_PUBLISHER"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "publisher",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "IBLOCK_ID" => $books
    );
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_LOST"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "lost",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "IBLOCK_ID" => $books
    );
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_AVAILABLE"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "available",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "IBLOCK_ID" => $books
    );
    $arFields[] = Array(
            "NAME" => GetMessage("BOOKS_OWNER"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "owner",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "USER_TYPE" => "employee",
            "IBLOCK_ID" => $books
    );
    foreach ($arFields as $arField) $ibp->Add($arField);
}

// Инфоблок перемещения книг
$arFields = Array(
		"ACTIVE" => "Y",
		"NAME" => "Перемещения книг",
		"CODE" => "motion",
		"IBLOCK_TYPE_ID" => $res,
		"SITE_ID" => $site_list
);
$motion = $ib->Add($arFields);
if(!$motion) {
	echo 'Error: '.$ib->LAST_ERROR.'<br>';
	return;
}
else{
	$arFields = Array();
	$arFields[] = Array(
			"NAME" => GetMessage("MOTION_BOOK_ID"),
			"ACTIVE" => "Y",
			"SORT" => "100",
			"CODE" => "bookId",
            "PROPERTY_TYPE" => "E",
            "MULTIPLE" => "N",
            "MULTIPLE_CNT" => "5",
            "LINK_IBLOCK_ID" => $books,
            "IBLOCK_ID" => $motion 
	);
	$arFields[] = Array(
			"NAME" => GetMessage("MOTION_USER_ID"),
			"ACTIVE" => "Y",
			"SORT" => "100",
			"CODE" => "userId",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "USER_TYPE" => "employee",
            "IBLOCK_ID" => $motion 
	);
    $arFields[] = Array(
            "NAME" => GetMessage("MOTION_TX_DATE"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "txDate",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "USER_TYPE" => "DateTime",
            "IBLOCK_ID" => $motion
    );
    $arFields[] = Array(
            "NAME" => GetMessage("MOTION_RX_DATE"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "rxDate",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "USER_TYPE" => "DateTime",
            "IBLOCK_ID" => $motion
    );
    $arFields[] = Array(
            "NAME" => GetMessage("MOTION_COMMENT"),
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "comment",
            "PROPERTY_TYPE" => "S",
            "MULTIPLE" => "N",
            "USER_TYPE" => "HTML",
            "USER_TYPE_SETTINGS" => 'a:1:{s:6:"height";i:200;}',
            "IBLOCK_ID" => $motion
    );
	foreach ($arFields as $arField) $ibp->Add($arField);
}

echo CAdminMessage::ShowNote(GetMessage("LIBRARY_INSTALL_COMPLETE"));
?>