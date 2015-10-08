<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"school:school.logs.detail",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["LESSONS_IBLOCK_ID"],
		"DAYS_COUNT" => $arParams["DAYS_COUNT"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["lesson"],
		"ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],
		"CLASS_ID" => $arResult["VARIABLES"]["CLASS_ID"],
		"SUBJECT_ID" => $arResult["VARIABLES"]["SUBJECT_ID"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	 "CACHE_TIME" => $arParams["CACHE_TIME"],
	),
 $component
);?>