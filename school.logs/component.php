<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
$arDefaultUrlTemplates404 = array(
	"classes" => "",
	"log"     => "#CLASS_ID#/#SUBJECT_ID#/",
	"lesson"  => "#CLASS_ID#/#SUBJECT_ID#/#ELEMENT_ID#/",
);
$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
$arComponentVariables = array(
	"CLASS_ID",
	"SUBJECT_ID",
	"ELEMENT_ID",
);
if ($arParams["SEF_MODE"] == "Y") {
	$arVariables = array();
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);
	$componentPage = CComponentEngine::ParseComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);
	if (!$componentPage)
		$componentPage = "classes";
	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	$arResult = array(
		"FOLDER"        => $arParams["SEF_FOLDER"],
		"URL_TEMPLATES" => $arUrlTemplates,
		"VARIABLES"     => $arVariables,
		"ALIASES"       => $arVariableAliases,
	);
}
else {
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);
	$componentPage = "";
	if (isset($arVariables["CLASS_ID"]) && intval($arVariables["CLASS_ID"]) > 0 && isset($arVariables["SUBJECT_ID"]) && intval($arVariables["SUBJECT_ID"]) > 0 && isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
		$componentPage = "lesson";
	elseif (isset($arVariables["CLASS_ID"]) && intval($arVariables["CLASS_ID"]) > 0 && isset($arVariables["SUBJECT_ID"]) && intval($arVariables["SUBJECT_ID"]) > 0)
		$componentPage = "log";
	else
		$componentPage = "classes";
	$arResult = array(
		"FOLDER"        => "",
		"URL_TEMPLATES" => Array(
			"classes" => htmlspecialchars($APPLICATION->GetCurPage()),
			"log"     => htmlspecialchars($APPLICATION->GetCurPage()."?".$arVariableAliases["CLASS_ID"]."=#CLASS_ID#&".$arVariableAliases["SUBJECT_ID"]."=#SUBJECT_ID#"),
			"lesson"  => htmlspecialchars($APPLICATION->GetCurPage()."?".$arVariableAliases["CLASS_ID"]."=#CLASS_ID#&".$arVariableAliases["SUBJECT_ID"]."=#SUBJECT_ID#&".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#"),
		),
		"VARIABLES"     => $arVariables,
		"ALIASES"       => $arVariableAliases
	);
}
$this->IncludeComponentTemplate($componentPage);
?>