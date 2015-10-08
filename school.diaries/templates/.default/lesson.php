<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"school:school.lesson",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"USER_TYPE" => "STUDENT",
		"ACTIVE_DATE_FORMAT" => $arParams["LESSON_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	 "CACHE_TIME" => $arParams["CACHE_TIME"],
	),
 $component
);?>