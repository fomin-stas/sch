<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_STUDENTS_LIST_NAME"),
	"DESCRIPTION" => GetMessage("SCHOOL_STUDENTS_LIST_DESCRIPTION"),
	"ICON" => "/images/students_list.gif",
	"SORT" => 25,  
	"CACHE_PATH" => "Y",
	"COMPLEX" => "N",  
	"PATH" => array(
		"ID" => "school",
  "NAME" => GetMessage("SCHOOL"),
  "SORT" => 5,
		"CHILD" => array(
			"ID" => "diary",
			"NAME" => GetMessage("SCHOOL_DIARIES"),
   "SORT" => 25,
		)
	),
);

?>