<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"school:school.students.list",
	($_GET["print"]=="Y")?"print":"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CLASS_ID" => $arResult["VARIABLES"]["CLASS_ID"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"USER_PROPERTY" => $arParams["USER_PROPERTY"],
	),
 $component
);?>