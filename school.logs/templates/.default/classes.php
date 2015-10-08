<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
 "school:school.logs.list",
 "",
 array(
	 "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	 "CLASSES_IBLOCK_ID" => $arParams["CLASSES_IBLOCK_ID"],
	 "SUBJECTS_IBLOCK_ID" => $arParams["SUBJECTS_IBLOCK_ID"],
	 "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["log"],
	 "CACHE_TYPE" => $arParams["CACHE_TYPE"],
	 "CACHE_TIME" => $arParams["CACHE_TIME"],
	),
	$component
);?>