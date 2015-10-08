<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_LOGS_DETAIL"),
	"DESCRIPTION" => GetMessage("SCHOOL_LOGS_DETAIL_DESCRIPTION"),
	"ICON" => "/images/logs_detail.gif",
 "SORT" => 30,
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