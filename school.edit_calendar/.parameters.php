<?
//коммент для сохранения кодировки
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$grfilter = Array
(
		"ACTIVE"         => "Y"
);
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="ASC"), $grfilter);
while($val=$rsGroups->Fetch()) {
	$arTypesEx[$val['ID']]=$val['NAME'];
}



$arSorts = Array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = Array(
		"ID"=>GetMessage("T_IBLOCK_DESC_FID"),
		"NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
		"ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
		"SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
		"TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
	);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"EDIT_GROUP" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "registry",
			//"REFRESH" => "Y",
		),
		'LESSON_COUNT' => array(
			'TYPE' => 'STRING',
			'MULTIPLE' => 'N',
			'NAME' => GetMessage('LESSON_COUNT'),
			'PARENT' => 'ADDITIONAL_SETTINGS',
			"DEFAULT" => "10"
		),
	),
);
?>
