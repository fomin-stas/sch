<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
CModule::IncludeModule('iblock');
CPageOption::SetOptionString("main", "nav_page_in_session", "N");
if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000;
$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if (strlen($arParams["IBLOCK_TYPE"]) <= 0)
	$arParams["IBLOCK_TYPE"] = "school";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
$arParams["USER_ID"] = $arParams["USER_ID"];

$rsCurrentUser = CUser::GetByID($USER->GetID());
//echo;
if ($arUser = $rsCurrentUser->Fetch()) {
	$USER_TYPE = empty($arParams["USER_TYPE"]) ? "" : $arParams["USER_TYPE"];
	// Если зашел ученик
	if (isset($arUser["UF_EDU_STRUCTURE"]) && !empty($arUser["UF_EDU_STRUCTURE"]) && $USER_TYPE != "PARENT") {
		$ClassID = $arUser["UF_EDU_STRUCTURE"];
		$arUser["VIEWING_USER"] = $arUser["ID"];
	}
	// Если зашел не ученик и запросил дневник ученика
	elseif ($arParams["USER_ID"]) {
		$rsReqUser = CUser::GetByID($arParams["USER_ID"]);
		if ($arReqUser = $rsReqUser->Fetch()) {
			// Если дневник ученика запросил родитель
			if (in_array($arReqUser["UF_STUDENT_LINK_CODE"], $arUser["UF_PARENT_LINK_CODE"]) !== false) {
				$ClassID = $arReqUser["UF_EDU_STRUCTURE"];
				$arUser["VIEWING_USER"] = $arReqUser["ID"];
			}
			else { 
				$arGroups = $USER->GetUserGroupArray();
				if(in_array(8,$arGroups)) {
					$ClassID =  $arReqUser["UF_EDU_STRUCTURE"];
					$arUser["VIEWING_USER"] = $arReqUser["ID"];
				}else return ShowError(GetMessage("EC_SCHOOL_USER_ACCESS"));
			}
		}
		else {
			return ShowError(GetMessage("EC_SCHOOL_USER_NOT_FOUND"));
		}
	}
	$arParams["CLASS_ID"] = $ClassID;
	$arSFilter = Array('IBLOCK_ID' => $arParams["IBLOCK_ID"], 'GLOBAL_ACTIVE' => 'Y', 'UF__EDU_STRUCTURE' => $ClassID);
	$rsSection = CIBlockSection::GetList(Array(), $arSFilter, false);
	$rsSection->SetUrlTemplates();
	if ($arSection = $rsSection->GetNext()){
		$arParams["PARENT_SECTION"] = intval($arSection["ID"]);
	}else{
echo '123'.$arParams["PARENT_SECTION"];
		return ShowError(GetMessage("EC_SCHOOL_USER_ACCESS"));
	}
}
$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"] != "N";
$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if (strlen($arParams["SORT_BY1"]) <= 0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
$arParams["SORT_ORDER1"] = strtoupper($arParams["SORT_ORDER1"]);
if ($arParams["SORT_ORDER1"] != "ASC")
	$arParams["SORT_ORDER1"] = "DESC";
if (strlen($arParams["SORT_BY2"]) <= 0)
	$arParams["SORT_BY2"] = "SORT";
if (strlen($arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
	$arrFilter = array();
}
else {
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if (!is_array($arrFilter))
		$arrFilter = array();
}
$arParams["DETAIL_URL"] = trim($arParams["~DETAIL_URL"]);
$arParams["DETAIL_URL"] = str_replace("#USER_ID#", $arUser["VIEWING_USER"], $arParams["~DETAIL_URL"]);
$arParams["SET_TITLE"] = $arParams["SET_TITLE"] != "N";
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if (strlen($arParams["ACTIVE_DATE_FORMAT"]) <= 0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
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
$arMarktypes = CSchool::GetMarkTypes();
$ViewingWeekStart = $arrFilter["ACTIVE_FROM"];
$ViewingWeekEnd = $arrFilter["ACTIVE_TO"];
if ($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"] === "N" ? false : $USER->GetGroups()), $arNavigation, $arrFilter))) {
	if (!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if (is_numeric($arParams["IBLOCK_ID"])) {
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID"     => $arParams["IBLOCK_ID"],
		));
	}
	else {
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE"  => "Y",
			"CODE"    => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	if ($arResult = $rsIBlock->GetNext()) {
		$arResult["USER_HAVE_ACCESS"] = true; //$bUSER_HAVE_ACCESS;
		//SELECT
		$arSelect = array(
			"ID",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"NAME",
			"ACTIVE_FROM",
			"ACTIVE_TO",
			"DETAIL_PAGE_URL",
			"DETAIL_TEXT",
			"DETAIL_TEXT_TYPE",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PREVIEW_PICTURE",
			"PROPERTY_*"
		);
		//WHERE
		$arFilter = array(
			"IBLOCK_ID"         => $arResult["ID"],
			"IBLOCK_LID"        => SITE_ID,
			"ACTIVE"            => "Y",
			"CHECK_PERMISSIONS" => "N",
		);
		$arParams["PARENT_SECTION"] = CIBlockFindTools::GetSectionID(
			$arParams["PARENT_SECTION"],
			$arParams["PARENT_SECTION_CODE"],
			array(
				"GLOBAL_ACTIVE" => "Y",
				"IBLOCK_ID"     => $arResult["ID"],
			)
		);
		if ($arParams["PARENT_SECTION"] > 0) {
			$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
			if ($arParams["INCLUDE_SUBSECTIONS"])
				$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
			$arResult["SECTION"] = array("PATH" => array());
			$rsPath = GetIBlockSectionPath($arResult["ID"], $arParams["PARENT_SECTION"]);
			$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
			while ($arPath = $rsPath->GetNext()) {
				$arResult["SECTION"]["PATH"][] = $arPath;
			}
		}
		else {
			$arResult["SECTION"] = false;
		}
		//ORDER BY
		$arSort["ACTIVE_FROM"] = "ASC";
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
		$arResult['ITEMS'] = array();
		$rangeFilter = array(
			'CLASS'=>$arParams["CLASS_ID"],
		);
		$rangeFilter['WEEK_START'] = $arrFilter["ACTIVE_FROM"];
		$lessons = MCSchedule::GetLessons($rangeFilter);
		$subjectNames = array();
		$rsLessonSection = CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'UF__EDU_STRUCTURE'=>$arParams['CLASS_ID']),false,array('ID','UF__EDU_STRUCTURE'));
		$arLessonSection = $rsLessonSection->GetNext();
		foreach ($lessons as $lesson) {
			$date = date('d.m.Y',AddToTimeStamp(Array("DD" => $lesson['week_day']-1), strtotime($lesson['week_start'])));
			if (!array_key_exists($date,$arResult["ITEMS"]))
				$arResult["ITEMS"][$date] = array(
					'NAME' => GetMessage(strtoupper(date('l', strtotime($date)))),
					'LESSONS' => array(),
					'MARKS_TYPES' => array(),
				);
			$start = $date.' '.$lesson['lesson_start'];
			if (!array_key_exists($lesson['service_id'],$subjectNames)) {
				$rsSubject = CIBlockElement::GetByID($lesson['service_id']);
				$arSubject = $rsSubject->GetNext();
				$subjectNames[$lesson['service_id']] = $arSubject['NAME'];
			}
			$arResult["ITEMS"][$date]['LESSONS'][$start] = array(
				'ACTIVE_FROM' => $start,
				'TIME' => $lesson['lesson_start'],
				'ACTIVE_TO' => $date.' '.$lesson['lesson_end'],
				'START' => $lesson['time_start']=='00:00:00'?$date.' '.$lesson['lesson_start']:$date.' '.$lesson['time_start'],
				'END' => $lesson['time_end']=='00:00:00'?$date.' '.$lesson['lesson_end']:$date.' '.$lesson['time_end'],
				'MARK_TYPES'=> empty($arMarkTypes)?array("FULL" => "", "SHORT" => ""):$arMarkTypes,
				'CLASS_ID' => $arParams['CLASS_ID'],
				'SUBJECT_ID' => $lesson['service_id'],
				'ELEMENT_ID' => $lesson['id_period'].'_'.$lesson['id_template'].'_'.$lesson['period_number'].'_'.$date,
				'NAME' => $subjectNames[$lesson['service_id']],
			);
			$arFilter = array(
				'ACTIVE_FROM' => $lesson['time_start']=='00:00:00'?$date.' '.$lesson['lesson_start']:$date.' '.$lesson['time_start'],
				'ACTIVE_TO' => $lesson['time_end']=='00:00:00'?$date.' '.$lesson['lesson_end']:$date.' '.$lesson['time_end'],
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'SECTION_ID' => intval($arLessonSection['ID']),
				'PROPERTY_AUDIENCE' => $lesson['id_cabinet'],
				'PROPERTY_SUBJECT' => $arParams['SUBJECT_ID'],
				'PROPERTY_TEACHER' => $lesson['id_employee'],
			);
			$rsLesson = CIBlockElement::GetList(array(),$arFilter);
			$rsLesson->SetUrlTemplates($arParams["DETAIL_URL"]);
			$arLesson = $rsLesson->GetNextElement();
			if ($arLesson) {
				$arResult["ITEMS"][$date]['LESSONS'][$start]['PROPERTIES'] = $arLesson->GetProperties();
				
				if (!empty($arResult["ITEMS"][$date]['LESSONS'][$start]['PROPERTIES']['MARK_TYPES']['VALUE'])){
					foreach ($arResult["ITEMS"][$date]['LESSONS'][$start]['PROPERTIES']['MARK_TYPES']['VALUE'] as $mType) {
						if (!array_key_exists($mType,$arResult["ITEMS"][$date]['MARKS_TYPES']))
							$arResult["ITEMS"][$date]['MARKS_TYPES'][$mType] = $arMarktypes[$mType];
					}
				}
				$arResult["ITEMS"][$date]['LESSONS'][$start]['DETAIL_PAGE_URL'] = $arLesson->fields['DETAIL_PAGE_URL'];
				$arButtons = CIBlock::GetPanelButtons(
					$arLesson->fields["IBLOCK_ID"],
					$arLesson->fields["ID"],
					0,
					array("SECTION_BUTTONS" => false, "SESSID" => false)
				);
				$arResult["ITEMS"][$date]['LESSONS'][$start]["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
				$arResult["ITEMS"][$date]['LESSONS'][$start]["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			}
		}

		foreach ($arResult['ITEMS'] as $key_date => $arItem) {
			foreach ($arItem['LESSONS'] as $lesson_key => $Lesson) {
				$Marks = $arItem['MARKS_TYPES'];
				foreach ($Lesson['PROPERTIES']['MARKS']['VALUE'] as $mark_key => $mark) {
					if (empty($mark))
						continue;
					if ($mark["USER"] == $arUser["VIEWING_USER"]) {
						$Marks[$mark['TYPE']] = array(
							'USER'  => $mark['USER'],
							'MARK'  => $mark['MARK'],
							'FULL'  => $Marks[$mark['TYPE']]['FULL'],
							'SHORT' => $Marks[$mark['TYPE']]['SHORT'],
							'COLOR' => CSchool::GetMarkColor($mark["MARK"])
						);
					}
				}
				$arResult['ITEMS'][$key_date]['LESSONS'][$lesson_key]['PROPERTIES']['MARKS']['VALUE'] = $Marks;
			}
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
		$this->SetResultCacheKeys(array(
									  "ID",
									  "IBLOCK_TYPE_ID",
									  "NAV_CACHED_DATA",
									  "NAME",
									  "SECTION",
								  ));
		$this->IncludeComponentTemplate();
	}
	else {
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
		@define("ERROR_404", "Y");
		if ($arParams["SET_STATUS_404"] === "Y")
			CHTTP::SetStatus("404 Not Found");
	}
}
if (isset($arResult["ID"])) {
	$arTitleOptions = null;
	if ($USER->IsAuthorized()) {
		if (
			$APPLICATION->GetShowIncludeAreas()
			|| (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"] !== "N")
			|| $arParams["SET_TITLE"]
		) {
			if (CModule::IncludeModule("iblock")) {
				$arButtons = CIBlock::GetPanelButtons(
					$arResult["ID"],
					0,
					$arParams["PARENT_SECTION"],
					array("SECTION_BUTTONS" => false)
				);
				if ($APPLICATION->GetShowIncludeAreas())
					$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
				if (
					is_array($arButtons["intranet"])
					&& is_object($GLOBALS["INTRANET_TOOLBAR"])
					&& $arParams["INTRANET_TOOLBAR"] !== "N"
				) {
					foreach ($arButtons["intranet"] as $arButton)
						$GLOBALS["INTRANET_TOOLBAR"]->AddButton($arButton);
				}
				if ($arParams["SET_TITLE"]) {
					$arTitleOptions = array(
						'ADMIN_EDIT_LINK'  => $arButtons["submenu"]["edit_iblock"]["ACTION"],
						'PUBLIC_EDIT_LINK' => "",
						'COMPONENT_NAME'   => $this->GetName(),
					);
				}
			}
		}
	}
	$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);
	if ($arParams["SET_TITLE"]!="N") {
		$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);
	}
	if ($arParams["INCLUDE_IBLOCK_INTO_CHAIN"] && isset($arResult["NAME"])) {
		$APPLICATION->AddChainItem($arResult["NAME"]);
	}
	if ($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"])) {
		foreach ($arResult["SECTION"]["PATH"] as $arPath) {
			$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
		}
	}
}

?>