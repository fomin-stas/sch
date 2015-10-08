<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_TEACHER_CLASSES_LIST"),
	"DESCRIPTION" => GetMessage("SCHOOL_TEACHER_CLASSES_LIST_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 20,
	"COMPLEX" => "N",
	"PATH" => array(
		"ID" => "school",
		"NAME" => GetMessage("SCHOOL"),
		"SORT" => 5,
		"CHILD" => array(
			"ID" => "students_list",
			"NAME" => GetMessage("SCHOOL_STUDENTS_LIST"),
			"SORT" => 40,
		)
	),
);
?>