<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_STUDENTS_LIST_ADD_NAME"),
	"DESCRIPTION" => GetMessage("SCHOOL_STUDENTS_LIST_ADD_DESCRIPTION"),
	"ICON" => "/images/students_list.gif",
	"SORT" => 80,   
	"PATH" => array(
		"ID" => "school",
    "NAME" => GetMessage("SCHOOL"),
    "SORT" => 5,
	),
);

?>