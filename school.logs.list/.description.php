<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_LOGS_LIST"),
	"DESCRIPTION" => GetMessage("SCHOOL_LOGS_LIST_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
 "SORT" => 20,
	"COMPLEX" => "N",
	"PATH" => array(
		"ID" => "school",
  "NAME" => GetMessage("SCHOOL"),
  "SORT" => 5,
		"CHILD" => array(
			"ID" => "logs",
			"NAME" => GetMessage("SCHOOL_LOGS"),
   "SORT" => 10,
		)
	),
);
?>