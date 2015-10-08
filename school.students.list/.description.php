<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("STUDENTS_LIST_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("STUDENTS_LIST_COMPONENT_DESCR"),
	"ICON" => "/images/comp.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "school",
		'NAME' => GetMessage('SCHOOL'),
		"CHILD" => array(
			"ID" => "students_list",
			"NAME" => GetMessage("SCHOOL_STUDENTS_LIST"),
		)
	),
);
?>