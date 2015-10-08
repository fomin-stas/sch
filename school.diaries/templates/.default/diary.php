<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"school:school.student.diary",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"DETAIL_URL" => $arResult["URL_TEMPLATES"]["lesson"],
		"ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" =>  $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"]
	),
	$component
);?>