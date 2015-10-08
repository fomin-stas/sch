<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule('intranet');

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$ufClass = array();
if(!empty($arCurrentValues["IBLOCK_ID"]))
{
	$rsClass = CIBlockElement::GetList(array("NAME"=>"ASC"),array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
	while($c = $rsClass->Fetch())
		$ufClass[$c["ID"]] = $c["NAME"];
}


$arComponentParameters = array(
	'GROUPS' => array(
		'FILTER' => array(
			'NAME' => GetMessage('INTR_ISL_GROUP_FILTER'),
		),
	),
	
	'PARAMETERS' => array(
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IB_CLASS_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IB_CLASS_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["IBLOCK_ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
	
		"CLASS_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CLASS_ID"),
			"TYPE" => "LIST",
			"VALUES" => $ufClass,
			"DEFAULT" => '={$_REQUEST["CLASS_ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		
		'FILTER_NAME' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'users',
			'PARENT' => 'FILTER',
			'NAME' => GetMessage('INTR_ISL_PARAM_FILTER_NAME'),
		),
		
		'FILTER_1C_USERS' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'NAME' => GetMessage('INTR_ISL_PARAM_FILTER_1C_USERS'),
			'PARENT' => 'BASE'
		),
		'FILTER_SECTION_CURONLY' => array(
			'TYPE' => 'LIST',
			'VALUES' => array('Y' => GetMessage('INTR_ISL_PARAM_FILTER_SECTION_CURONLY_VALUE_Y'), 'N' => GetMessage('INTR_ISL_PARAM_FILTER_SECTION_CURONLY_VALYE_N')),
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			'NAME' => GetMessage('INTR_ISL_PARAM_FILTER_SECTION_CURONLY'),
			'PARENT' => 'BASE'
		),
		
		'NAME_TEMPLATE' => array(
			'TYPE' => 'LIST',
			'NAME' => GetMessage('INTR_ISL_PARAM_NAME_TEMPLATE'),
			'VALUES' => CComponentUtil::GetDefaultNameTemplates(),
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'Y',
			'DEFAULT' => "#NOBR##LAST_NAME# #NAME##/NOBR#",
			'PARENT' => 'BASE',
		),
		
		'SHOW_ERROR_ON_NULL' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'NAME' => GetMessage('INTR_ISL_PARAM_SHOW_ERROR_ON_NULL'),
			'PARENT' => 'BASE'
		),
		
		'USERS_PER_PAGE' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => '10',
			'NAME' => GetMessage('INTR_ISL_PARAM_USERS_PER_PAGE'),
			'PARENT' => 'BASE'
		),
		'NAV_TITLE' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'DEFAULT' => GetMessage('INTR_ISL_PARAM_NAV_TITLE_DEFAULT'),
			'NAME' => GetMessage('INTR_ISL_PARAM_NAV_TITLE'),
			'PARENT' => 'BASE'
		),
		'SHOW_NAV_TOP' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'NAME' => GetMessage('INTR_ISL_PARAM_SHOW_NAV_TOP'),
			'PARENT' => 'BASE'
		),
		'SHOW_NAV_BOTTOM' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'Y',
			'NAME' => GetMessage('INTR_ISL_PARAM_SHOW_NAV_BOTTOM'),
			'PARENT' => 'BASE'
		),
		'SHOW_UNFILTERED_LIST' => array(
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			'NAME' => GetMessage('INTR_ISL_PARAM_SHOW_UNFILTERED_LIST'),
			'PARENT' => 'BASE'
		),
		"CACHE_TIME" => array('DEFAULT' => 3600),
		"SET_TITLE" => array(),
	),
);

?>