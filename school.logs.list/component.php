<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;
$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if (strlen($arParams["IBLOCK_TYPE"]) <= 0)
	$arParams["IBLOCK_TYPE"] = "school";
$arParams["CLASSES_IBLOCK_ID"] = trim($arParams["CLASSES_IBLOCK_ID"]);
$arParams["SUBJECTS_IBLOCK_ID"] = trim($arParams["SUBJECTS_IBLOCK_ID"]);
$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);
if (!$USER->IsAuthorized())
	return ShowError(GetMessage("SCHOOL_USER_NOT_AUTHORIZED"));
if ($this->StartResultCache(false, array($arParams, $USER->GetID()))) {
	if (!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if (!CModule::IncludeModule("bitrix.schoolschedule")) {
		$this->AbortResultCache();
		ShowError(GetMessage("SCHOOL_MODULE_NOT_INSTALLED"));
		return;
	}

	$employee = false;
	if (!$USER->IsAdmin()) {
		$employee = $USER->GetID;
	}
	$arClassSubject = MCSchedule::GetSubjectsForClasses($employee);
	if (empty($arClassSubject['LINKS'])) {
		$this->AbortResultCache();
		ShowError(GetMessage("SCHOOL_SUBJECT_NOT_FOUND"));
		return;
	}

	$arResult = Array(
		"ITEMS"    => Array(),
		"SUBJECTS" => Array(),
		"CLASSES"  => Array(),
	);
	$arSecSubjects = Array();
	$arFilter = Array('IBLOCK_ID' => $arParams["SUBJECTS_IBLOCK_ID"], 'GLOBAL_ACTIVE' => 'Y');
	$rsSections = CIBlockSection::GetList(Array("sort" => "asc", "name" => "asc"), $arFilter, false);
	while ($arSection = $rsSections->GetNext()) {
		$arSection["ITEMS"] = Array();
		$arSecSubjects[$arSection["ID"]] = $arSection;
	}
	$arSelect = Array("ID", "NAME", "IBLOCK_SECTION_ID");
	$arFilter = Array("IBLOCK_ID" => $arParams["SUBJECTS_IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
	$arFilter["ID"] = $arClassSubject['SUBJECTS'];
	$rsSubjects = CIBlockElement::GetList(Array("sort" => "asc", "name" => "asc"), $arFilter, false, false, $arSelect);
	while ($arSubject = $rsSubjects->GetNext()) {
		$arResult["SUBJECTS"][$arSubject["ID"]] = $arSubject;
		$arSecSubjects[$arSubject["IBLOCK_SECTION_ID"]]["ITEMS"][] = $arSubject["ID"];
	}


	$arSecClasses = Array();
	$arFilter = Array('IBLOCK_ID' => $arParams["CLASSES_IBLOCK_ID"], 'GLOBAL_ACTIVE' => 'Y');
	$rsSections = CIBlockSection::GetList(Array("sort" => "asc", "name" => "asc"), $arFilter, false, Array("UF_SUBJECTS"));
	while ($arSection = $rsSections->GetNext()) {
		$arSection["ITEMS"] = Array();
		$arSecClasses[$arSection["ID"]] = $arSection;
	}
	$arSelect = Array("ID", "NAME", "IBLOCK_SECTION_ID");
	$arFilter = Array("IBLOCK_ID" => $arParams["CLASSES_IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
	$arFilter["ID"] = $arClassSubject['CLASSES'];
	$rsClasses = CIBlockElement::GetList(Array("sort" => "asc", "name" => "asc"), $arFilter, false, false, $arSelect);
	while ($arClass = $rsClasses->GetNext()) {
		$arSecClasses[$arClass["IBLOCK_SECTION_ID"]]["ITEMS"][] = $arClass;
		$arResult["CLASSES"][$arClass["ID"]] = $arClass;
	}

	if (count($arResult["SUBJECTS"]) && count($arResult["CLASSES"])) {
		foreach ($arSecClasses as $arClassSection) {
			foreach ($arClassSection["ITEMS"] as $arClass) {
				$arClass["ITEMS"] = Array();
					foreach ($arClassSubject['LINKS'][$arClass["ID"]] as $SubjectID) {
						$arSubject = $arResult["SUBJECTS"][$SubjectID];
						$arSubject["DETAIL_PAGE_URL"] = str_replace(Array("#CLASS_ID#", "#SUBJECT_ID#"), Array($arClass["ID"], $arSubject["ID"]), $arParams["DETAIL_URL"]);
						$arClass["ITEMS"][] = $arSubject;
					}
				if (count($arClass["ITEMS"]))
					$arResult["ITEMS"][] = $arClass;
			}
		}
	}
	$this->IncludeComponentTemplate();
}
?>