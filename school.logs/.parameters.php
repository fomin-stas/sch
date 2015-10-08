<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
  "LOG_SETTINGS" => array(
			"NAME" => GetMessage("S_LOG_SETTINGS"),
		),
		"LESSON_SETTINGS" => array(
			"NAME" => GetMessage("S_LESSON_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => Array(
			"CLASS_ID" => Array("NAME" => GetMessage("BN_P_CLASS_ID_DESC")),
			"SUBJECT_ID" => Array("NAME" => GetMessage("BN_P_SUBJECT_ID_DESC")),
			"ELEMENT_ID" => Array("NAME" => GetMessage("BN_P_LESSON_ID_DESC")),
		),
		"SEF_MODE" => Array(
			"classes" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_CLASSES"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"log" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_LOG"),
				"DEFAULT" => "#CLASS_ID#/#SUBJECT_ID#/",
				"VARIABLES" => array("CLASS_ID", "SUBJECT_ID"),
			),
   "lesson" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_LESSON"),
				"DEFAULT" => "#CLASS_ID#/#SUBJECT_ID#/#ELEMENT_ID#/",
				"VARIABLES" => array("CLASS_ID", "SUBJECT_ID", "ELEMENT_ID"),
			),
		),
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BN_P_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"CLASSES_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BN_P_CLASSES_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"ADDITIONAL_VALUES" => "Y",
		),
  "SUBJECTS_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BN_P_SUBJECTS_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"ADDITIONAL_VALUES" => "Y",
		),
  "LESSONS_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BN_P_LESSONS_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"ADDITIONAL_VALUES" => "Y",
		),
  "CLASS_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_CLASS_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={intVal($_REQUEST["CLASS_ID"])}',
		),
  "SUBJECT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_SUBJECT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={intVal($_REQUEST["SUBJECT_ID"])}',
		),
  "ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_LESSON_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={intVal($_REQUEST["ELEMENT_ID"])}',
		),
  "DAYS_COUNT" => Array(
			"PARENT" => "LOG_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
  "ACTIVE_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "LOG_SETTINGS"),
  "LESSON_DATE_FORMAT" => CIBlockParameters::GetDateFormat(GetMessage("T_IBLOCK_DESC_ACTIVE_DATE_FORMAT"), "LESSON_SETTINGS"),
		
	 "CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
?>
