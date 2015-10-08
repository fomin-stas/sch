<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_DIARY"),
	"DESCRIPTION" => GetMessage("SCHOOL_DIARY_DESCRIPTION"),
	"ICON" => "/images/diary.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"COMPLEX" => "N",
	"PATH" => array(
		"ID" => "school",
  "NAME" => GetMessage("SCHOOL"),
  "SORT" => 5,
		"CHILD" => array(
			"ID" => "diary",
			"NAME" => GetMessage("SCHOOL_DIARIES"),
   "SORT" => 15,
		)
	),
);

?>