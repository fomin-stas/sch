<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;
$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if (strlen($arParams["IBLOCK_TYPE"]) <= 0)
	$arParams["IBLOCK_TYPE"] = "school";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["CLASS_ID"] = intval($arParams["CLASS_ID"]);
$arParams["SUBJECT_ID"] = intval($arParams["SUBJECT_ID"]);
$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);
$arParams["DETAIL_URL"] = str_replace(Array("#CLASS_ID#", "#SUBJECT_ID#"), Array($arParams["CLASS_ID"], $arParams["SUBJECT_ID"]), $arParams["DETAIL_URL"]);
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if (strlen($arParams["ACTIVE_DATE_FORMAT"]) <= 0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
if (!$USER->IsAuthorized())
	return ShowError(GetMessage("SCHOOL_USER_NOT_AUTHORIZED"));
$arrFilter = array();
if (empty($_REQUEST["START_WEEK"]) || empty($_REQUEST["END_WEEK"])) {
	// Получаем элементы за текущую неделю
	$cur_week_day = date("w");
	$today = MakeTimeStamp(date("d.m.Y"), "DD.MM.YYYY HH:MI:SS");
	$arrAddFirst = array("DD" => -($cur_week_day - 1));
	$arrAddLast = array("DD" => (7 - $cur_week_day));
	$arrFilter["ACTIVE_FROM"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), AddToTimeStamp($arrAddFirst, $today));
	$arrFilter["ACTIVE_TO"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), AddToTimeStamp($arrAddLast, $today));
}
else {
	$arrFilter["ACTIVE_FROM"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), MakeTimeStamp($_REQUEST["START_WEEK"]));
	$arrFilter["ACTIVE_TO"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), MakeTimeStamp($_REQUEST["END_WEEK"]));
}
$ViewingWeekStart = $arrFilter["ACTIVE_FROM"];
$ViewingWeekEnd = $arrFilter["ACTIVE_TO"];
if ($this->StartResultCache(false, array($USER->GetID(), $arrFilter))) {
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
	if (empty($arClassSubject['LINKS']))
		return ShowError(GetMessage("SCHOOL_CLASS_SUBJECT_NOT_FOUND"));

	if (!array_key_exists($arParams["CLASS_ID"],$arClassSubject['LINKS'])) {
		$this->AbortResultCache();
		ShowError(GetMessage("SCHOOL_CLASS_NOT_FOUND"));
		return;
	}

	$rsSubject = CIBlockElement::GetByID($arParams["SUBJECT_ID"]);

	$arResult["SUBJECT"] = $rsSubject->Fetch();
	$arMarkTypes = Array();
	$arResult["MARK_TYPES"] = CSchool::GetMarkTypes();
	foreach ($arResult["MARK_TYPES"] as $type => $val)
		$arMarkTypes[$type] = 1;
	$arAllMarks = CSchool::GetMarks();
	$arResult["MARK_COLORS"] = Array();
	foreach ($arAllMarks as $mark)
		$arResult["MARK_COLORS"][$mark] = CSchool::GetMarkColor($mark);
	$arResult["ITEMS"] = Array();
	$arFilter = Array(
		"UF_EDU_STRUCTURE" => $arParams["CLASS_ID"],
	);
	$rsStudents = CUser::GetList(($by = "last_name"), ($order = "asc"), $arFilter);
	while ($arStudent = $rsStudents->Fetch()) {
		$arResult["ITEMS"][] = $arStudent;
	}
	$arResult["SELECT_DATES"] = Array();

	$rangeFilter = array(
		'CLASS'=>$arParams["CLASS_ID"],
		'SUBJECT'=>$arParams["SUBJECT_ID"],
	);
	if ($employee) {
		$rangeFilter['EMPLOYEE'] = $employee;
	}
	$scheduleRange = MCSchedule::GetScheduleWeekRange($rangeFilter);

	if ($scheduleRange['FIRST_WEEK'] && $scheduleRange['LAST_WEEK']) {
		if (!empty($_REQUEST["START_WEEK"]))
			$cur_date = MakeTimeStamp($_REQUEST["START_WEEK"]);
		else
			$cur_date = MakeTimeStamp(date("d.m.Y"), "DD.MM.YYYY");
		$startDateSt = strtotime($scheduleRange['FIRST_WEEK']);
		$endDateSt = strtotime($scheduleRange['LAST_WEEK']);
		$endDateSt = AddToTimeStamp(Array("DD" => 7), $endDateSt);
		$week_day = date("w", $startDateSt);
		$arrAddFirst = array("DD" => -($week_day - 1));
		$arrAddLast = array("DD" => (7 - $week_day));
		$firstDateSt = AddToTimeStamp($arrAddFirst, $startDateSt);
		$lastDateSt = AddToTimeStamp($arrAddLast, $startDateSt);
		do {
			$firstDate = ConvertTimeStamp($firstDateSt, "SHORT");
			$lastDate = ConvertTimeStamp($lastDateSt, "SHORT");
			$arPeriod = Array(
				"TITLE" => CSchool::GetDiaryPeriod($firstDate, $lastDate),
				"HREF"  => $APPLICATION->GetCurPageParam('START_WEEK='.$firstDate.'&END_WEEK='.$lastDate, array("START_WEEK", "END_WEEK"))
			);
			if ($cur_date >= $firstDateSt && $cur_date <= $lastDateSt)
				$arPeriod["SELECTED"] = "Y";
			else
				$arPeriod["SELECTED"] = "N";
			$arResult["SELECT_DATES"][] = $arPeriod;
			$firstDateSt = AddToTimeStamp(Array("DD" => 7), $firstDateSt);
			$lastDateSt = AddToTimeStamp(Array("DD" => 7), $lastDateSt);
		} while ($firstDateSt < $endDateSt);
	}

	$rangeFilter['WEEK_START'] = $arrFilter["ACTIVE_FROM"];
	$lessons = MCSchedule::GetLessons($rangeFilter);
	$url_template = $arParams['DETAIL_URL'];
	if (empty($url_template))
		$url_template = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "DETAIL_PAGE_URL");

	$arResult["LESSONS"] = array();
	foreach ($lessons as $lesson) {

		$date = date('d.m.Y',AddToTimeStamp(Array("DD" => $lesson['week_day']-1), strtotime($lesson['week_start'])));
		$start = $date.' '.$lesson['lesson_start'];
		$arResult["LESSONS"][$start] = array(
			'ACTIVE_FROM' => $start,
			'ACTIVE_TO' => $date.' '.$lesson['lesson_end'],
			'DISPLAY_ACTIVE_FROM' => $date,
			'START' => $lesson['lesson_start'],
			'END' => $lesson['lesson_end'],
			'MARK_TYPES'=> empty($arMarkTypes)?array("FULL" => "", "SHORT" => ""):$arMarkTypes,
			'CLASS_ID' => $arParams['CLASS_ID'],
			'SUBJECT_ID' => $arParams['SUBJECT_ID'],
			'ELEMENT_ID' => $lesson['id_period'].'_'.$lesson['id_template'].'_'.$lesson['period_number'].'_'.$date,
		);
		$arResult["LESSONS"][$start]['DETAIL_PAGE_URL'] = str_replace('#ELEMENT_ID#',$arResult["LESSONS"][$start]['ELEMENT_ID'],str_replace('#SUBJECT_ID#',$arParams['SUBJECT_ID'],str_replace('#CLASS_ID#',$arParams['CLASS_ID'],$url_template)));
	}
	$arSelect = Array(
		"ID",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"NAME",
		"ACTIVE_FROM",
		"ACTIVE_TO",
		"DETAIL_PAGE_URL",
		"PROPERTY_*"
	);
	$arFilter = Array(
		"IBLOCK_ID"         => $arParams["IBLOCK_ID"],
		"IBLOCK_LID"        => SITE_ID,
		"ACTIVE"            => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"SECTION_ID"        => $arResult["SECTION"]["ID"],
		"PROPERTY_SUBJECT"  => $arParams["SUBJECT_ID"],
	);
	$arSort = Array("active_from" => "asc");
	$rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter, $arrFilter), false, false, $arSelect);
	$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
	while ($obElement = $rsElement->GetNextElement()) {
		$arItem = $obElement->GetFields();
		if (strlen($arItem["ACTIVE_FROM"]) > 0)
			$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
		else
			$arItem["DISPLAY_ACTIVE_FROM"] = "";
		$arItem["PROPERTIES"] = $obElement->GetProperties();
	}

	// Определяем границы предыдущей недели
	$prWeekStart = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), AddToTimeStamp(Array("DD" => -7), MakeTimeStamp($ViewingWeekStart)));
	$prWeekEnd = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), AddToTimeStamp(Array("DD" => -1), MakeTimeStamp($ViewingWeekStart)));
	// Определяем границы следующей недели
	$fwWeekStart = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), AddToTimeStamp(Array("DD" => 1), MakeTimeStamp($ViewingWeekEnd)));
	$fwWeekEnd = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), AddToTimeStamp(Array("DD" => 7), MakeTimeStamp($ViewingWeekEnd)));
	$arResult["WEEK_FIRS_DAY"] = $ViewingWeekStart;
	$arResult["WEEK_LAST_DAY"] = $ViewingWeekEnd;
	$arResult["WEEK_FORWARD"] = $APPLICATION->GetCurPageParam('START_WEEK='.$fwWeekStart.'&END_WEEK='.$fwWeekEnd, array("START_WEEK", "END_WEEK"));
	$arResult["WEEK_BACK"] = $APPLICATION->GetCurPageParam('START_WEEK='.$prWeekStart.'&END_WEEK='.$prWeekEnd, array("START_WEEK", "END_WEEK"));
	$arResult["WEEK_PERIOD_TITLE"] = CSchool::GetDiaryPeriod($ViewingWeekStart, $ViewingWeekEnd);
	for ($i = 0, $cStudents = count($arResult["ITEMS"]); $i < $cStudents; $i++) {
		$arResult["ITEMS"][$i]["LESSONS"] = Array();
		foreach ($arResult["LESSONS"] as $arLesson) {
			$arItem = Array(
				"ID"                  => $arLesson["ID"],
				"NAME"                => $arLesson["NAME"],
				"ACTIVE_FROM"         => $arLesson["ACTIVE_FROM"],
				"ACTIVE_TO"           => $arLesson["ACTIVE_TO"],
				"DISPLAY_ACTIVE_FROM" => $arLesson["DISPLAY_ACTIVE_FROM"],
				"DISPLAY_ACTIVE_TO"   => $arLesson["DISPLAY_ACTIVE_TO"],
				"DETAIL_PAGE_URL"     => $arLesson["DETAIL_PAGE_URL"],
			);
			foreach ($arLesson["MARK_TYPES"] as $type => $val)
				$arItem["MARKS"][$type] = "";
			foreach ($arLesson["PROPERTIES"]["MARKS"]["VALUE"] as $arMark) {
				if ($arMark["USER"] == $arResult["ITEMS"][$i]["ID"])
					$arItem["MARKS"][$arMark["TYPE"]] = $arMark["MARK"];
			}
			$arResult["ITEMS"][$i]["LESSONS"][] = $arItem;
		}
	}
	$this->IncludeComponentTemplate();
}
$APPLICATION->SetPageProperty("title", $arResult["SUBJECT"]["NAME"].", ".$arResult["SECTION"]["NAME"]);
?>
