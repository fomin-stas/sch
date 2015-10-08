<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

IncludeAJAX();
CAjax::Init();
CUtil::InitJSCore(array('window'));

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)$arParams["IBLOCK_TYPE"] = "school";

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);


if(!is_array($arParams["FIELD_CODE"]))$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
{
	if(empty($val))unset($arParams["FIELD_CODE"][$key]);
}
if(!is_array($arParams["PROPERTY_CODE"]))$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
{
	if(empty($v))unset($arParams["PROPERTY_CODE"][$k]);
}

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));	

//---------------------------------{ get all params for current user
$filter = array();
$filter["ID"] = CUser::GetID();
$rsUsers = CUser::GetList(($by="NAME"), ($order="asc"), $filter,array("SELECT"=>array("UF_*")));	
$curUser = $rsUsers->Fetch();
//---------------------------------} get all params for current user

$IBLOCK_ID = intval($arParams["IBLOCK_ID"]);
$PARENT_CHILDRENS = array();


if(!CUser::isAuthorized())
{
	ShowError(GetMessage("USER_NOT_AUTHORIZED"));
	return;
}
if(!CModule::IncludeModule("iblock"))
{
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

if(!CModule::IncludeModule("bitrix.schoolschedule"))
{
	$this->AbortResultCache();
	ShowError(GetMessage("SCHOOL_MODULE_NOT_INSTALLED"));
	return;
}

if (is_numeric($arParams["ELEMENT_ID"])) {
	$ELEMENT_ID = intval($arParams["ELEMENT_ID"]);
} else {
	$arElementParams = explode('_',$arParams['ELEMENT_ID']);
	$ELEMENT_ID = 0;
	if (count($arElementParams)==4) {
		$arFilter = array(
			'PERIOD' => intval($arElementParams[0]),
			'TEMPLATE' => intval($arElementParams[1]),
			'LESSON_NUMBER' => intval($arElementParams[2]),
			'DATE' => $arElementParams[3],
			'LESSON' => intval($arParams['SUBJECT_ID']),
			'CLASS' => intval($arParams['CLASS_ID']),
		);
		$lesson = MCSchedule::GetLessons($arFilter);
		$lesson = array_shift($lesson);
		$date = date('d.m.Y',AddToTimeStamp(Array("DD" => $lesson['week_day']-1), strtotime($lesson['week_start'])));

		$rsSubject = CIBlockElement::GetByID($arParams['SUBJECT_ID']);
		$arSubject = $rsSubject->GetNext();
		$rsLessonSection = CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$IBLOCK_ID, 'UF__EDU_STRUCTURE'=>$arParams['CLASS_ID']),false,array('ID','UF__EDU_STRUCTURE'));
		$arLessonSection = $rsLessonSection->GetNext();
		$arFilter = array(
			'ACTIVE_FROM' => $lesson['time_start']=='00:00:00'?$date.' '.$lesson['lesson_start']:$date.' '.$lesson['time_start'],
			'ACTIVE_TO' => $lesson['time_end']=='00:00:00'?$date.' '.$lesson['lesson_end']:$date.' '.$lesson['time_end'],
			'ACTIVE' => 'Y',
			'IBLOCK_ID' => $IBLOCK_ID,
			'SECTION_ID' => intval($arLessonSection['ID']),
			'PROPERTY_AUDIENCE' => $lesson['id_cabinet'],
			'PROPERTY_SUBJECT' => $arParams['SUBJECT_ID'],
			'PROPERTY_TEACHER' => $lesson['id_employee'],
		);
		$rsLesson = CIBlockElement::GetList(array(),$arFilter);
		$arLesson = $rsLesson->GetNext();
		if ($arLesson) {
			$ELEMENT_ID = $arLesson['ID'];
		} else {
			$el = new CIBlockElement();
			$arProps = array(
				'SUBJECT' => array('VALUE'=>$arParams['SUBJECT_ID']),
				'TEACHER' => array('VALUE'=>$lesson['id_employee']),
				'AUDIENCE' => array('VALUE'=>$lesson['id_cabinet']),
			);
			$arFields = array(
				'NAME' => $arSubject['NAME'],
				'ACTIVE_FROM' => $lesson['time_start']=='00:00:00'?$date.' '.$lesson['lesson_start']:$date.' '.$lesson['time_start'],
				'ACTIVE_TO' => $lesson['time_end']=='00:00:00'?$date.' '.$lesson['lesson_end']:$date.' '.$lesson['time_end'],
				'PROPERTY_VALUES' => $arProps,
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $IBLOCK_ID,
				'IBLOCK_SECTION_ID' => intval($arLessonSection['ID']),
			);
			$ELEMENT_ID = $el->Add($arFields);
		}
	}
}

$arUser["ID"] = CUser::GetID();

if($ELEMENT_ID>0 && $IBLOCK_ID > 0)
{
	$arParams['ELEMENT_ID'] = $ELEMENT_ID;
	if($this->StartResultCache(false, array($arParams, $arUser["ID"])))
	{

		//---------------------------------------------------{ FILL arResult
		$arResult = Array();
		$rs = CIBlockElement::GetByID($ELEMENT_ID);
		if(!($arResult = $rs->Fetch()))
		{
			$this->AbortResultCache();
			ShowError(GetMessage("S_LESSON_DETAIL_NF"));
			/*
			@define("ERROR_404", "Y");
			if($arParams["SET_STATUS_404"]==="Y")
				CHTTP::SetStatus("404 Not Found");
			*/
			return;
		}
		
		$sectionRs = CIBlockSection::GetList(array(), array("IBLOCK_ID"=>$arResult["IBLOCK_ID"],"ID"=>$arResult["IBLOCK_SECTION_ID"]),false,array("UF__EDU_STRUCTURE"));
		$sectionRs = $sectionRs->fetch();
		
		$dt = strtotime($arResult["ACTIVE_FROM"]);
		$arResult["DATE"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], $dt);
		
		$dayNum = date("w",$dt);
		$weekDay = GetMessage("WEEKDAY_".$dayNum);
		
		$arResult["DATE_FORMATED"] = $weekDay.", ".CIBlockFormatProperties::DateFormat("j F Y", $dt);
		
		
		$arResult["SECTION"] = $sectionRs;
		
		$rsProps = CIBlockElement::GetProperty($IBLOCK_ID, $ELEMENT_ID);
		$arResult["PROPERTIES"] = array();
		$arMarktypes = CSchool::GetMarkTypes();
		
		while($prop = $rsProps->Fetch())
		{
			if($prop["MULTIPLE"] == "Y")
			{
				if(!is_set($arResult["PROPERTIES"][$prop["CODE"]]))
				{
					$val = $prop["VALUE"];
					if(!empty($val))
					{
						$prop["VALUE"] = array();
						switch($prop["CODE"])
						{
							case "MARK_TYPES":
								$prop["VALUE"][] = array(
									"VALUE" => $val,
									"FULL" => $arMarktypes[$val]["FULL"],
									"SHORT" => $arMarktypes[$val]["SHORT"],
									);
								break;
							default:
								$prop["VALUE"][] = $val;
								break;
						}
						$arResult["PROPERTIES"][$prop["CODE"]] = $prop;
					}
				}
				else
				{
					switch($prop["CODE"])
					{
						case "MARK_TYPES":
							if(!empty($prop["VALUE"]))
							{
								$arResult["PROPERTIES"][$prop["CODE"]]["VALUE"][] = array(
									"VALUE" => $prop["VALUE"],
									"FULL" => $arMarktypes[$prop["VALUE"]]["FULL"],
									"SHORT" => $arMarktypes[$prop["VALUE"]]["SHORT"],
									);
							}
							break;
						default:
							if(!empty($prop["VALUE"]))$arResult["PROPERTIES"][$prop["CODE"]]["VALUE"][] = $prop["VALUE"];
							break;
					}
				}
			}
			else
			{
				switch($prop["CODE"])
				{
					case "HOME_WORK":
						if(array_key_exists("TEXT",$prop["VALUE"]))$prop["VALUE"] = $prop["VALUE"]["TEXT"];
						//$prop["VALUE"] = htmlentities($prop["VALUE"],ENT_QUOTES,SITE_CHARSET);
						$arResult["PROPERTIES"][$prop["CODE"]] = $prop;
						break;
					case "SUBJECT":
						$SUBJECT_ID = intval($prop["VALUE"]);
						if($SUBJECT_ID>0)
						{
							$subject = CIBlockElement::GetByID($SUBJECT_ID);
							if($subject = $subject->GetNext())
							{
								$prop["VALUE"] = $subject;
							}
						}
						$arResult["PROPERTIES"][$prop["CODE"]] = $prop;
						break;
					case "AUDIENCE":
						$AUDIENCE_ID = intval($prop["VALUE"]);
						if($AUDIENCE_ID>0)
						{
							$audience = CIBlockElement::GetByID($AUDIENCE_ID);
							if($audience = $audience->Fetch())
							{
								$stage = CIBlockSection::GetByID($audience["IBLOCK_SECTION_ID"]);
								if($stage = $stage->Fetch())
								{
									$audience["STAGE"] = $stage;
								}
							}
							$arResult["PROPERTIES"][$prop["CODE"]] = $audience;
						}
						break;
					case "TEACHER":
						$TEACHER_ID = intval($prop["VALUE"]);
						$teacher = false;
						if($TEACHER_ID>0)
						{
							$filter = array();
							$filter["ID"] = $TEACHER_ID;
							$teacher = CUser::GetList(($by="ID"), ($order="asc"), $filter, array("SELECT"=>array("UF_*")));
							$teacher = $teacher->GetNext();
						}
						$arResult["PROPERTIES"][$prop["CODE"]] = $teacher;
						break;
					default:
						$arResult["PROPERTIES"][$prop["CODE"]] = $prop;
						break;
				}
			}
		}
		
		
		$MARKS = array();//все оценки за урок
		foreach($arResult["PROPERTIES"]["MARKS"]["VALUE"] as $mv)
		{
			$MARKS[$mv["USER"]][$mv["TYPE"]] = array(
				"VALUE" => $mv["MARK"],
				"COLOR" => CSchool::GetMarkColor($mv["MARK"]),
				);
		}
		
		$COMMENTS = array();//комментарии ученикам за урок
		foreach($arResult["PROPERTIES"]["STUDENT_WORK_COMMENT"]["VALUE"] as $comm)
		{
			$COMMENTS[$comm["USER"]] = $comm["COMMENT"];
		}
		
		
		
		$MARK_TYPES = array();//все типы оценок для урока
		
		//добавляем типы работ на уроке из свойства элемента
		foreach($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"] as $mt)
		{
			if(!empty($mt["VALUE"]))
			{
				$MARK_TYPES[$mt["VALUE"]] = array(
					"FULL" => $mt["FULL"],
					"SHORT" => $mt["SHORT"],
					);
			}
			
		}
		
		//добавляем типы работы на уроках для оценок, которые выставлены, но в списке типов работ урока нет... x_X
		foreach($MARKS as $m)
		{
			foreach($m as $k=>$v)
			{
				$MARK_TYPES[$k] = $arMarktypes[$k];
			}
		}
		
		
		$arResult["MARK_TYPES"] = $MARK_TYPES;
		
		//---- {отсортировать типы оценок в соответствии с настройками модуля
		$markTypesSorted = array();
		foreach($arMarktypes as $k => $mt)
		{
			if(array_key_exists($k,$MARK_TYPES))
			{
				$markTypesSorted[] = array(
						"VALUE"=>$k,
						"FULL"=>$MARK_TYPES[$k]["FULL"],
						"SHORT"=>$MARK_TYPES[$k]["SHORT"],
					);
			}
		}
		$arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"] = $markTypesSorted;
		//-----}
		$CLASS_ID = intval($arResult["SECTION"]["UF__EDU_STRUCTURE"]);
		if($CLASS_ID>0)
		{
			$arResult["JOURNAL"] = array();
			
			if($arParams["USER_TYPE"] == "PARENT")
			{
				$filter = array();
				$filter["UF_STUDENT_LINK_CODE"] = $curUser["UF_PARENT_LINK_CODE"];
				$chRs = CUser::GetList(($by="ID"), ($order="asc"), $filter, array("SELECT"=>array("UF_*")));
				while($ch = $chRs->Fetch())
				{
					$PARENT_CHILDRENS[] = $ch["ID"];
				}
			}
			
					
			$filter = array();
			$filter["UF_EDU_STRUCTURE"] = $CLASS_ID;
			
			$students = CUser::GetList(($by = "LAST_NAME"), ($order="asc"), $filter,array("SELECT"=>array("UF_*")));
			while($s = $students->GetNext())
			{
				$goNext = false;
				switch($arParams["USER_TYPE"])
				{
					case "STUDENT":
						if($s["ID"] != $curUser["ID"])$goNext = true;
						break;
					case "PARENT":
						if(!in_array($s["ID"],$PARENT_CHILDRENS))
						{
							$goNext = true;
						}
						else
						{	
							$USER_ID = intval($arParams["USER_ID"]);
							if($USER_ID>0)
							{
								if($s["ID"]!=$USER_ID)$goNext = true;
							}
							else $goNext = true;
						}
						break;
					case "TEACHER":
						break;
				}
				
				if($goNext)continue;
				
				foreach($arMarktypes as $k=>$mt)
				{
					if(array_key_exists($k,$MARK_TYPES))
					{
						$s["MARKS"][$k] = $MARKS[$s["ID"]][$k];
					}
				}
				$s["STUDENT_WORK_COMMENT"] = "";
				if (empty($s['LAST_NAME']) && empty($s['NAME']) && empty($s['SECOND_NAME'])) $s['LAST_NAME'] = $s['LOGIN'];
				if(!empty($COMMENTS[$s["ID"]]))$s["STUDENT_WORK_COMMENT"] = $COMMENTS[$s["ID"]];
				$arResult["JOURNAL"][] = $s;
			}
		}
		//---------------------------------------------------} FILL arResult
		
		
		//----------------------------------------------------------{ ACCESS TO LESSON
		$ACCESS_TO_LESSON = false;
		if(CUser::isAdmin())
		{
			$ACCESS_TO_LESSON = true;
			$arResult["TEACHER_ACCESS"] = "Y";
		}
		
		switch($arParams["USER_TYPE"])
		{
			case "TEACHER":
				if(is_array($curUser["UF_CLASS_SUBJECT"]))
				{
					foreach($curUser["UF_CLASS_SUBJECT"] as $vCS)
					{
						$vCS = unserialize($vCS);
						if($vCS["SUBJECT"] == $arResult["PROPERTIES"]["SUBJECT"]["VALUE"]["ID"])
						{
							if($arResult["PROPERTIES"]["TEACHER"] == false)
							{//если на урок не назначен другой учитель, то назначить текущего учителя, т.к. он ведет данный урок по-умолчанию
								$arResult["PROPERTIES"]["TEACHER"] = $curUser;
							}
							$ACCESS_TO_LESSON = true;
						}
					}
				}
				
				if($arResult["PROPERTIES"]["TEACHER"]["ID"] == $curUser["ID"])
				{
					$ACCESS_TO_LESSON = true;
				}
				
				if($ACCESS_TO_LESSON)
				{
					$arResult["TEACHER_ACCESS"] = "Y";
				}
				
				break;
			case "PARENT":
				if(is_array($curUser["UF_PARENT_LINK_CODE"]))
				{
					$filter = array();
					$filter["UF_STUDENT_LINK_CODE"] = $curUser["UF_PARENT_LINK_CODE"];
					$chRs = CUser::GetList(($by="ID"), ($order="asc"), $filter, array("SELECT"=>array("UF_*")));
					while($ch = $chRs->Fetch())
					{
						if(!empty($ch["UF_EDU_STRUCTURE"]))
						{
							$filter = array();
							$filter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
							$filter["UF_EDU_STRUCTURE"] = $ch["UF_EDU_STRUCTURE"];
							$rs = CIBlockSection::GetList(array("SORT"=>"ASC"),$filter,false);
							if($rs = $rs->Fetch())
							{
								$filter = array();
								$filter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
								$filter["SECTION_ID"] = $rs["ID"];
								$res = CIBlockElement::GetList(false,$filter);
								while($les = $res->Fetch())
								{
									if($les["ID"] == $ELEMENT_ID)
									{
										$ACCESS_TO_LESSON = true;
									}
								}
							}
							
						}
					}
				}
				break;
			case "STUDENT":
				if(!empty($curUser["UF_EDU_STRUCTURE"]))
				{
					$filter = array();
					$filter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
					$filter["UF__EDU_STRUCTURE"] = $curUser["UF_EDU_STRUCTURE"];
					$rs = CIBlockSection::GetList(array("SORT"=>"ASC"),$filter,false);
					if($rs = $rs->Fetch())
					{
						$filter = array();
						$filter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
						$filter["SECTION_ID"] = $rs["ID"];
						$res = CIBlockElement::GetList(false,$filter);
						while($les = $res->Fetch())
						{
							if($les["ID"] == $ELEMENT_ID)
							{
								$ACCESS_TO_LESSON = true;
							}
						}
					}
					
				}
				break;
			default:
				break;
		}
		/*
		if(!$ACCESS_TO_LESSON)
		{
			ShowError(GetMessage("S_LESSON_ACCESS_DENIED"));
			return;
		} */
		//----------------------------------------------------------} ACCESS TO LESSON
		$this->IncludeComponentTemplate();
	}
	
	if($arResult["TEACHER_ACCESS"] == "Y")
	{//добавить функции редактирования на страницу
		$arResult["TEACHER_ACCESS"] = "Y";
		if($_REQUEST["mode"] == "ajax")
		{
			$APPLICATION->RestartBuffer(); 
			function global_decode($str)
			{
				return html_entity_decode(preg_replace_callback(
					'|(?:%u.{4})|',
					create_function(
						'$matches',
						'return \'&#\'.hexdec(substr($matches[0], 2)).\';\';'
					),
					$str
				),ENT_QUOTES, SITE_CHARSET);
			}
			
			switch($_REQUEST["action"])
			{
				case "save_lesson_desc":
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$lessonTheme = global_decode($_REQUEST["lessonTheme"]);
					$lessonDesc = global_decode($_REQUEST["lessonDesc"]);
					
					// ---------- update lesson theme
					$PROPERTY_CODE = "LESSON_THEME";
					$PROPERTY_VALUE = $lessonTheme;
					CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
					// ---------- update lesson description
					$el = new CIBlockElement;
					$arFields = Array(
						"MODIFIED_BY" => CUser::GetID(),
						"DETAIL_TEXT"=>$lessonDesc,
						);
					$res = $el->Update($ELEMENT_ID, $arFields);
					//обязательно нужно обновлять элемент, чтобы обновился кэш компонента
					$this->AbortResultCache();
					break;
				case "save_lesson_homework":
					//PR($_FILES);
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$homework = global_decode($_REQUEST["homework"]);
					// ---------- update lesson homework
					$PROPERTY_CODE = "HOME_WORK";
					$PROPERTY_VALUE = $homework;
					CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
					
					$db_props = CIBlockElement::GetProperty($IBLOCK_ID, $ELEMENT_ID, "sort", "asc", Array("CODE"=>"HOME_WORK_FILES"));
					
					$filesProps = array();
					
					while($prop = $db_props->Fetch())
					{
						$filesProps[$prop["VALUE"]] = $prop["PROPERTY_VALUE_ID"];
					}
						
					$arFiles = array();
					foreach($_FILES as $i => $file)
					{
						$matches = array();
						if(preg_match("/_(n[0-9]+)$/i",$i,$matches))
						{//new file
							$fid = $matches[1];
							
							$descr = "";
							if(!empty($_REQUEST["HOMEWORK_FILE_".$fid."_descr"]))
								$descr = $_REQUEST["HOMEWORK_FILE_".$fid."_descr"];
							
							if($file["size"]>0)
								$arFiles[$fid] = array("VALUE"=>$file,"DESCRIPTION"=>$descr);
						}
						elseif(preg_match("/_([0-9]+)$/i",$i,$matches))
						{//old file
							$fid = $matches[1];
							
							$descr = "";
							if(!empty($_REQUEST["HOMEWORK_FILE_".$fid."_descr"]))
								$descr = $_REQUEST["HOMEWORK_FILE_".$fid."_descr"];
								
							if(!empty($_REQUEST["HOMEWORK_FILE_".$fid."_del"]) && !($file["size"]>0))
							{//just delete file
								$file["del"] = "Y";
								$arFiles[$filesProps[$fid]] = array("VALUE"=>$file);
							}
							elseif(!empty($_REQUEST["HOMEWORK_FILE_".$fid."_del"]) && $file["size"]>0)
							{//replace old file with delete
								CFile::Delete($fid);
								$arFiles[$filesProps[$fid]] = array("VALUE"=>$file,"DESCRIPTION"=>$descr);
							}
							elseif($file["size"]>0)
							{//replace old file
								$arFiles[$filesProps[$fid]] = array("VALUE"=>$file,"DESCRIPTION"=>$descr);
							}
							else
							{//just change description
								$arfile = CFile::MakeFileArray($fid);
								$arFiles[$filesProps[$fid]] = array("VALUE"=>$arfile,"DESCRIPTION"=>$descr);
							}
						}
					}
					
					$PROPERTY_CODE = "HOME_WORK_FILES";
					$PROPERTY_VALUE = $arFiles;
					CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
					
					$el = new CIBlockElement;
					$arFields = Array("MODIFIED_BY" => CUser::GetID());
					//обязательно нужно обновлять элемент, чтобы обновился кэш компонента
					$res = $el->Update($ELEMENT_ID, $arFields);
					
					//LocalRedirect("http://".SITE_SERVER_NAME.$APPLICATION->GetCurPage());
					$this->AbortResultCache();
					LocalRedirect($APPLICATION->GetCurPage());
					break;
				case "lesson_add_worktype":
					//добавление типа работы на уроке
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$worktype = $_REQUEST["worktype"];
					$arMarktypes = CSchool::GetMarkTypes();
					if(array_key_exists($worktype,$arMarktypes))
					{//есть такой тип работы в настройках модуля
						$newWorkTypes = array();
						$newWorkTypes[] = $worktype;
						foreach($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"] as $v)
						{
							$newWorkTypes[] = $v["VALUE"];
						}
						
						$PROPERTY_CODE = "MARK_TYPES";
						$PROPERTY_VALUE = $newWorkTypes;
						CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
						$el = new CIBlockElement;
						$arFields = Array("MODIFIED_BY" => CUser::GetID());
						$res = $el->Update($ELEMENT_ID, $arFields);
					}
					$this->AbortResultCache();
					$this->ClearResultCache();
					LocalRedirect($APPLICATION->GetCurPage());
					break;
				case "deleteWorkType":
					//удаление типа работы на уроке
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$delWorkType = $_REQUEST["workType"];
					$workTypes = $arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"];
					
					$newWorkTypes = array();
					foreach($workTypes as $i=>$v)
					{
						if($v["VALUE"] == $delWorkType)continue;
						$newWorkTypes[] = $v["VALUE"];
					}
					$PROPERTY_CODE = "MARK_TYPES";
					$PROPERTY_VALUE = $newWorkTypes;
					CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
					
					$newMarksValues = array();
					foreach($arResult["PROPERTIES"]["MARKS"]["VALUE"] as $m)
					{
						if($m["TYPE"] == $delWorkType)continue;
						$newMarksValues[] = serialize($m);
					}
					
					$PROPERTY_CODE = "MARKS";
					$PROPERTY_VALUE = $newMarksValues;
					CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
					
					$el = new CIBlockElement;
					$arFields = Array("MODIFIED_BY" => CUser::GetID());
					$res = $el->Update($ELEMENT_ID, $arFields);
					//обязательно нужно обновлять элемент, чтобы обновился кэш компонента
					$this->AbortResultCache();
					LocalRedirect($APPLICATION->GetCurPage());
					break;
				case "setMark":
					//установка оценки для ученика на уроке
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$mark_type = $_REQUEST["mark_type"];
					$user_id = intval($_REQUEST["user_id"]);
					$mark = $_REQUEST["mark"];
					
					$arMarkTypes = CSchool::GetMarkTypes();
					$arMarks = CSchool::GetMarks();
					
					if($user_id>0 && (in_array($mark,$arMarks) || empty($mark)) && array_key_exists($mark_type,$arMarkTypes))
					{
						$markSet = false;
						$newMarks = array();
						foreach($arResult["PROPERTIES"]["MARKS"]["VALUE"] as $m)
						{
							if($m["USER"]==$user_id && $m["TYPE"]==$mark_type)
							{
								$m["MARK"] = $mark;
								$markSet = true;
							}
							$newMarks[] = $m;
						}
						
						if(!$markSet)
						{
							$m = array("USER"=>$user_id,"TYPE"=>$mark_type,"MARK"=>$mark);
							$newMarks[] = $m;
						}
						
						foreach($newMarks as $i=>$v)
						{
							if(!empty($v["MARK"]))$newMarks[$i] = serialize($v);//пропускать не установленные значения оценок (удаление оценок)
						}
						
						$PROPERTY_CODE = "MARKS";
						$PROPERTY_VALUE = $newMarks;
						CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
						
						$el = new CIBlockElement;
						$arFields = Array("MODIFIED_BY" => CUser::GetID());
						$res = $el->Update($ELEMENT_ID, $arFields);
					}
					echo CSchool::GetMarkColor($mark);
					break;
				case "save_lesson_comment":
					//комментарии к работе учеников
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$USER_ID = intval($_REQUEST["USER_ID"]);
					$comment = global_decode($_REQUEST["comment"]);
					
					$comments = array();
					$commentSet = false;
					foreach($arResult["PROPERTIES"]["STUDENT_WORK_COMMENT"]["VALUE"] as $c)
					{
						if($c["USER"]==$USER_ID)
						{
							$c["COMMENT"] = $comment;
							$commentSet = true;
						}
						$comments[] = $c;
					}
					
					if(!$commentSet)
					{
						$c = array("USER"=>$USER_ID,"COMMENT"=>$comment);
						$comments[] = $c;
					}
					
					$newComments = array();
					foreach($comments as $c)
					{
						if(!empty($c["COMMENT"]))$newComments[] = serialize($c);
					}
					
					$PROPERTY_CODE = "STUDENT_WORK_COMMENT";
					$PROPERTY_VALUE = $newComments;
					CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
					
					$el = new CIBlockElement;
					$arFields = Array("MODIFIED_BY" => CUser::GetID());
					$res = $el->Update($ELEMENT_ID, $arFields);
					$this->AbortResultCache();
					break;
				case "lesson_edit_worktype":
					$ELEMENT_ID = $arResult["ID"];
					$IBLOCK_ID = $arResult["IBLOCK_ID"];
					$oldWorkType = $_REQUEST["old_worktype"];
					$newWorkType = $_REQUEST["new_worktype"];
					$arMarkTypes = CSchool::GetMarkTypes();
					
					$curWorkTypes = array();
					foreach($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"] as $mt)
					{
						if(!empty($mt["VALUE"]))$curWorkTypes[] = $mt["VALUE"];
					}
					
					$curMarks = array();
					foreach($arResult["PROPERTIES"]["MARKS"]["VALUE"] as $mv)
					{
						if(is_array($mv))$curMarks[] = $mv;
					}
					
					if(array_key_exists($oldWorkType,$arMarkTypes) && array_key_exists($newWorkType,$arMarkTypes) && $oldWorkType != $newWorkType && in_array($oldWorkType,$curWorkTypes))
					{
						//поменять у оценок их типы
						foreach($curMarks as &$mv)
						{
							if($mv["TYPE"] == $oldWorkType)
							{
								$mv["TYPE"] = $newWorkType;
							}
							elseif($mv["TYPE"] == $newWorkType)
							{
								$mv["TYPE"] = $oldWorkType;
							}
						}
						
						if(!in_array($newWorkType,$curWorkTypes))
						{//поменять тип работы на новый
							//$curWorkTypes
							
							$key = array_search($oldWorkType,$curWorkTypes);
							unset($curWorkTypes[$key]);
							$curWorkTypes[] = $newWorkType;
						}

						$PROPERTY_CODE = "MARK_TYPES";
						$PROPERTY_VALUE = $curWorkTypes;
						CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
						
						$newMarksValues = array();
						foreach($curMarks as $m)
						{
							$newMarksValues[] = serialize($m);
						}
						
						$PROPERTY_CODE = "MARKS";
						$PROPERTY_VALUE = $newMarksValues;
						CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUE, $PROPERTY_CODE);
						
						
						$el = new CIBlockElement;
						$arFields = Array("MODIFIED_BY" => CUser::GetID());
						$res = $el->Update($ELEMENT_ID, $arFields);
						$this->AbortResultCache();
					}
					break;
				default:
					break;
			}
			die();
		}
	}
	
	
	
	if($arParams["SET_TITLE"])
	{
		//$APPLICATION->SetPageProperty("title", $arResult["NAME"]);
		//$APPLICATION->SetTitle($arResult["SECTION"]["NAME"].": ".$arResult["NAME"]);
		$APPLICATION->SetTitle($arResult["SECTION"]["NAME"].": ".$arResult["PROPERTIES"]["SUBJECT"]["VALUE"]["NAME"]);
	}
}
else
{
	ShowError(GetMessage("S_LESSON_DETAIL_NF"));
	/*
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	*/
	return;
}
?>