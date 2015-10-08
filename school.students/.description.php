<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_STUDENTS_CMPX"),
	"DESCRIPTION" => GetMessage("SCHOOL_STUDENTS_CMPX_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 12,
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "school",
		"NAME" => GetMessage("SCHOOL"),
		"SORT" => 40,
		"CHILD" => array(
			"ID" => "students_list",
			"NAME" => GetMessage("SCHOOL_STUDENTS_LIST"),
			"SORT" => 5,
		)
	),
);
?>