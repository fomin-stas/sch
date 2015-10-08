<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes();

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

//* * * * * * * * * * * Parameters  * * * * * * * * * * *
$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
	"IBLOCK_TYPE" => Array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("EC_P_IBLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arTypesEx,
		"REFRESH" => "Y",
		),
  
	"IBLOCK_ID" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("EC_P_CLASSES_IBLOCK"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlocks,
		),
  
	"DETAIL_URL" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("T_IBLOCK_DESC_DETAIL_PAGE_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => "/teachers/lists_students/#CLASS_ID#/",
	),
	"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	"SET_TITLE" => array(),
 )
);
?>
