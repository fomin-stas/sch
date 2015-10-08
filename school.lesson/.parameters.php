<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = CIBlockParameters::GetIBlockTypes();

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
			),
		
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
			),
		
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BND_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={intVal($_REQUEST["ELEMENT_ID"])}',
			),
		
		"USER_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BND_USER_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={intVal($_REQUEST["USER_ID"])}',
			),
		
		"USER_TYPE" => array(
			"PARENT" => "BASE",
			"TYPE" => "LIST",
			"VALUES" => array(
				"TEACHER"=>GetMessage("SL_TEACHER"),
				"PARENT"=>GetMessage("SL_PARENT"),
				"STUDENT"=>GetMessage("SL_STUDENT"),
				),
			"DEFAULT" => 'TEACHER',
			"NAME" => GetMessage("USER_TYPE"),
			),
		"ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "BASE"),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		
		/*
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("LESSON_SET_STATUS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		*/
		"SET_TITLE" => array(),
	),
);
?>
