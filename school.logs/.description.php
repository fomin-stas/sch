<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_LOGS_CMPX"),
	"DESCRIPTION" => GetMessage("SCHOOL_LOGS_CMPX_DESCRIPTION"),
	"ICON" => "/images/logs_icon.gif",
 "SORT" => 10,
	"COMPLEX" => "Y",
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