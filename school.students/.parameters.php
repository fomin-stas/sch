<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))return;


$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}


$ufClass = array();

if(!empty($arCurrentValues["IBLOCK_ID"]))
{
	$sc = CIBlockSection::GetList(array("SORT"=>"ASC","NAME"=>"ASC"),array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
	while($s = $sc->Fetch())
	{
		$res = CIBlockElement::GetList(array("SORT"=>"ASC","NAME"=>"ASC"),array("SECTION_ID"=>$s["ID"],"IBLOCK_ID"=>$ib_class));
		while($c = $res->Fetch())
		{
			$ufClass[$c["ID"]] = $c["NAME"];
		}
	}
}

$arComponentParameters = array(
	/*
	"GROUPS" => array(
		"CALENDAR_SETTINGS" => array(
			"NAME" => GetMessage("CN_P_DETAIL_SETTINGS"),
		),
	),
	*/
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => Array(
			"CLASS_ID" => Array("NAME" => GetMessage("SS_CLASS_ID")),
		),
		
		"SEF_MODE" => Array(
			"classes" => array(
				"NAME" => GetMessage("SS_CLASS_URL"),
				"DEFAULT" => "",
			),
			"students" => array(
				"NAME" => GetMessage("SS_STUDENTS_URL"),
				"DEFAULT" => "#CLASS_ID#/",
				"VARIABLES" => array("CLASS_ID"),
			),
		),
		
		"AJAX_MODE" => array(),
		"SET_TITLE" => Array(),
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SS_STUDENTS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SS_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SS_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),
		
		"CLASS_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SS_CLASS_ID"),
			"TYPE" => "LIST",
			"VALUES" => $ufClass,
			"DEFAULT" => '={$_REQUEST["CLASS_ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
	),
);
?>
