<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("SCHOOL_DIARY_CMPX"),
	"DESCRIPTION" => GetMessage("SCHOOL_DIARY_CMPX_DESCRIPTION"),
	"ICON" => "/images/diaries_icon.gif",
  "SORT" => 15,
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "school",
    "NAME" => GetMessage("SCHOOL"),
    "SORT" => 5,
		"CHILD" => array(
			"ID" => "diary",
			"NAME" => GetMessage("SCHOOL_DIARY"),
      "SORT" => 25,
		)
	),
);
?>