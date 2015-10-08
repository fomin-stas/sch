<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("school:school.parent.students", "theacher_diary", Array(
	"SECTION_URL" => $arResult["URL_TEMPLATES"]["diary"],	// URL, ведущий на страницу с содержимым раздела
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"CACHE_NOTES" => "",
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	),
	false
);?>