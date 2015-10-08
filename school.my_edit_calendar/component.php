<?
//коммент для сохранения кодировки
CAjax::Init();
CUtil::InitJSCore(array('window'));
/** @noinspection PhpUndefinedMethodInspection */
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
CModule::IncludeModule("bitrix.schoolschedule");
GLOBAL $USER;
$arGroups = $USER->GetUserGroupArray();
$arParams['USER_GROUP'] = $arGroups;
if ($arParams['EDIT_GROUP']) {
	$arParams['EDIT_GROUP'] = intval($arParams['EDIT_GROUP']);
}
else {
	$arParams['EDIT_GROUP'] = 1;
}
function CanEdit($UserGroups, $EditGroup) {
	if ((in_array($EditGroup, $UserGroups)) or (in_array(1, $UserGroups))) {
		Return true;
	}
	else {
		Return false;
	}
}

$arParams['EMPL_TYPE'] = 'simple';
if (!CModule::IncludeModule("iblock")) {
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->GetNext();
if (is_array($arUser) && isset($arUser['UF_EDU_STRUCTURE']))
	$arParams['CLASS'] = array_shift($arUser['UF_EDU_STRUCTURE']);
if (isset($_REQUEST['clas'])) {
	$arParams['CLASS'] = intval($_REQUEST['clas']);
}
$arResult['day_templates'] = MCSchedule::GetTemplateList(array('SITE_ID' => SITE_ID));
if ($_GET['cancl'] == "Y") {
	$arUpdFilter['WEEK_START'] = $_GET['cw_start'];
	$arUpdFilter['WEEK_DAY'] = $_GET['c_day'];
	$arUpdFilter['CLASS'] = $_GET['clas'];
	$arUpdFilter['EMPLOYEE'] = $_GET['c_empl'];
	$arUpdFilter['CABINET'] = $_GET['c_cab'];
	$arUpdFilter['PERIOD'] = $_GET['c_per'];
	$arUpdFilter['SERVICE'] = $_GET['c_serv'];
	$arUpdFilter['SITE_ID'] = SITE_ID;
	$arUpdFilter['TEMPLATE'] = $_GET['c_templ'];
	$arUpdFilter['PERIOD_NUMBER'] = $_GET['c_pernum'];
	$arUpdFields['CANCLED'] = 1;
	MCSchedule::Update($arUpdFields, $arUpdFilter);
	if (intval($_GET['week']) > 0) {
		$ur_end = '?clas='.$_GET['clas'].'&week='.$_GET['week'];
	}
	else {
		$ur_end = '?clas='.$_GET['clas'];
	}
	LocalRedirect($_SERVER['SCRIPT_NAME'].$ur_end);
}
if ($_GET['ret'] == "Y") {
	$arUpdFilter['WEEK_START'] = $_GET['cw_start'];
	$arUpdFilter['WEEK_DAY'] = $_GET['c_day'];
	$arUpdFilter['CLASS'] = $_GET['clas'];
	$arUpdFilter['EMPLOYEE'] = $_GET['c_empl'];
	$arUpdFilter['CABINET'] = $_GET['c_cab'];
	$arUpdFilter['PERIOD'] = $_GET['c_per'];
	$arUpdFilter['SERVICE'] = $_GET['c_serv'];
	$arUpdFilter['SITE_ID'] = SITE_ID;
	$arUpdFilter['TEMPLATE'] = $_GET['c_templ'];
	$arUpdFilter['PERIOD_NUMBER'] = $_GET['c_pernum'];
	$arUpdFields['CANCLED'] = "0";
	MCSchedule::Update($arUpdFields, $arUpdFilter);
	if (intval($_GET['week']) > 0) {
		$ur_end = '?clas='.$_GET['clas'].'&week='.$_GET['week'];
	}
	else {
		$ur_end = '?clas='.$_GET['clas'];
	}
	LocalRedirect($_SERVER['SCRIPT_NAME'].$ur_end);
}
if (!isset($_GET['week'])) {
	$arParams['WEEK_NUMBER'] = date('W');
}
else {
	$arParams['WEEK_NUMBER'] = intval($_GET['week']);
}
if (!$_GET['year']) {
	$arParams['YEAR'] = date('Y');
}
else {
	$arParams['YEAR'] = (int)$_GET['year'];
}
$arParams['WEEK'] = MCDateTimeTools::GetWeekDaysStartTimestamp($arParams['WEEK_NUMBER'], $arParams['YEAR']);
$emp_nx = false;
$emp_pr = true;
$arFilter = array(
	'CLASS'      => $arParams['CLASS'],
	'WEEK_DAY'   => $day,
	'WEEK_START' => array(
		'COMPARSION' => '>=',
		'START_DATE' => date('d.m.Y', $arParams['WEEK']['weekStart'])
	),
	'SITE_ID' => SITE_ID,
);
$sch = MCSchedule::GetLessons($arFilter);
if ((count($sch) == 0) and (!CanEdit($arParams['USER_GROUP'], $arParams['EDIT_GROUP']))) {
	$emp_nx = true;
}
$arFilter = array(
	'CLASS'      => $arParams['CLASS'],
	'WEEK_DAY'   => $day,
	'WEEK_START' => array(
		'COMPARSION' => '<=',
		'START_DATE' => date('d.m.Y', $arParams['WEEK']['weekStart'])
	),
	'SITE_ID' => SITE_ID,
);
$sch = MCSchedule::GetLessons($arFilter);
if ((count($sch) == 0) and (!CanEdit($arParams['USER_GROUP'], $arParams['EDIT_GROUP']))) {
	$emp_pr = true;
}
if ($emp_nx and $emp_pr) {
	$arParams['WEEK_NUMBER'] = date('W');
}
$arParams['WEEK'] = MCDateTimeTools::GetWeekDaysStartTimestamp($arParams['WEEK_NUMBER'], $arParams['YEAR']);
$prev_week = MCDateTimeTools::GetWeekDaysStartTimestamp($arParams['WEEK_NUMBER'] - 1, $arParams['YEAR']);
$next_week = MCDateTimeTools::GetWeekDaysStartTimestamp($arParams['WEEK_NUMBER'] + 1, $arParams['YEAR']);
$arFilter1 = array(
	'CLASS'      => $arParams['CLASS'],
	'WEEK_DAY'   => $day,
	'WEEK_START' => array(
		'COMPARSION' => '<=',
		'START_DATE' => date('d.m.Y', $prev_week['weekStart'])
	),
	'SITE_ID' => SITE_ID,
);
$pr_week_ar = MCSchedule::GetLessons($arFilter1);
$arFilter1 = array(
	'CLASS'      => $arParams['CLASS'],
	'WEEK_DAY'   => $day,
	'WEEK_START' => array(
		'COMPARSION' => '>=',
		'START_DATE' => date('d.m.Y', $next_week['weekStart'])
	),
	'SITE_ID' => SITE_ID,
);
$nx_week_ar = MCSchedule::GetLessons($arFilter1);
$arParams['pr_week'] = count($pr_week_ar) > 0;
$arParams['nx_week'] = count($nx_week_ar) > 0;
$arParams['SUBJECTS_IBLOCK_ID'] = COption::GetOptionString('bitrix.schoolschedule', 'IBLOCK_SUBJECTS', '');
$arParams['CLASSES_IBLOCK_ID'] = COption::GetOptionString('bitrix.schoolschedule', 'IBLOCK_CLASSES', '');
$arParams['CABINETS_IBLOCK_ID'] = COption::GetOptionString('bitrix.schoolschedule', 'IBLOCK_CABINETS', '');
$arParams['LESSON_COUNT'] = intval($arParams['LESSON_COUNT']);
if ($arParams['LESSON_COUNT'] == 0)
	$arParams['LESSON_COUNT'] = 10;

$arResult['CLASSES']= MCSchedule::GetClasses($arParams);

$day = 1;
$week_schedule = array();
while ($day <= 7) {
	$arFilter = array(
		'CLASS'      => $arParams['CLASS'],
		'WEEK_DAY'   => $day,
		'WEEK_START' => array(
			'COMPARSION' => '=',
			'START_DATE' => date('d.m.Y', $arParams['WEEK']['weekStart'])
		),
		'SITE_ID' => SITE_ID,
	);
	$sch = MCSchedule::GetLessons($arFilter);
	foreach ($sch as $key => $val) {
		$res = CIBlockElement::GetByID($val['id_class']);
		if ($ar_res = $res->GetNext()) {
			$sch[$key]['id_class'] = array(
				'ID'   => $val['id_class'],
				'NAME' => $ar_res['NAME']
			);
		}
		$el = new CIBlockElement;
		$res = CIBlockElement::GetByID($val['id_cabinet']);
		if ($ar_res = $res->GetNext()) {
			$sch[$key]['id_cabinet'] = array(
				'ID'   => $val['id_cabinet'],
				'NAME' => $ar_res['NAME']
			);
		}
		$res = CIBlockElement::GetByID($val['service_id']);
		if ($ar_res = $res->GetNext()) {
			$sch[$key]['service_id'] = array(
				'ID'   => $val['service_id'],
				'NAME' => $ar_res['NAME']
			);
		}
		$rsUser = CUser::GetByID($val['id_employee']);
		if ($arUser = $rsUser->Fetch()) {
			$sch[$key]['id_employee'] = array(
				'ID'         => $val['id_employee'],
				'NAME'       => $arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'],
				'NAME_SHORT' => $arUser['LAST_NAME'].' '.mb_substr($arUser['NAME'], 0, 1).'.'.mb_substr($arUser['SECOND_NAME'], 0, 1).'.'
			);
		}
		$week_schedule[$day][$val['period_number']][] = $sch[$key];
	}
	$day = $day + 1;
}
$arResult['WEEK_SCHEDULE'] = $week_schedule;
$day = 1;
while ($day <= 7) {
	$arFilter = array(
		'CLASS'      => $arParams['CLASS'],
		'WEEK_DAY'   => $day,
		'WEEK_START' => array(
			'COMPARSION' => '=',
			'START_DATE' => date('d.m.Y', $arParams['WEEK']['weekStart']),
		'SITE_ID' => SITE_ID,
		),
	);
	$sch = MCSchedule::GetFreeTime($arFilter);
	foreach ($sch as $key => $val) {
		$res = CIBlockElement::GetByID($val['id_class']);
		if ($ar_res = $res->GetNext()) {
			$sch[$key]['id_class'] = array(
				'ID'   => $val['id_class'],
				'NAME' => $ar_res['NAME']
			);
		}
		$res = CIBlockElement::GetByID($val['id_cabinet']);
		if ($ar_res = $res->GetNext()) {
			$sch[$key]['id_cabinet'] = array(
				'ID'   => $val['id_cabinet'],
				'NAME' => $ar_res['NAME']
			);
		}
		$res = CIBlockElement::GetByID($val['service_id']);
		if ($ar_res = $res->GetNext()) {
			$sch[$key]['service_id'] = array(
				'ID'   => $val['service_id'],
				'NAME' => $ar_res['NAME']
			);
		}
		$rsUser = CUser::GetByID($val['id_employee']);
		if ($arUser = $rsUser->Fetch()) {
			$sch[$key]['id_employee'] = array(
				'ID'         => $val['id_employee'],
				'NAME'       => $arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'],
				'NAME_SHORT' => $arUser['LAST_NAME'].' '.$arUser['NAME'][0].' '.$arUser['SECOND_NAME'][0]
			);
		}
		$arp = MCSchedule::GetDayTamplateById($val['id_template']);
		foreach ($arp['lessons'] as $pkey => $pval) {
		}
		$c_lesson = 1;
		while (($c_lesson <= 10) and ($arp['lessons'][$c_lesson]['START'] < $val['time_start']) and ($arp['lessons'][$c_lesson]['END'] < $val['time_start'])) {
			$c_lesson = $c_lesson + 1;
		}
		$pstart = $arp['lessons'][$c_lesson]['START'];
		$pstart_id = $c_lesson;
		$c_lesson = 1;
		while (($c_lesson <= 10) and ($arp['lessons'][$c_lesson]['START'] < $val['time_end']) and ($arp['lessons'][$c_lesson]['END'] < $val['time_end'])) {
			$c_lesson = $c_lesson + 1;
		}
		$pend_id = $c_lesson;
		$pend = $arp['lessons'][$c_lesson]['START'];
		$p_count = $pend_id - $pstart_id + 1;
		$sch[$key]['start_id'] = $pstart_id;
		$sch[$key]['end_id'] = $pend_id;
		$sch[$key]['count'] = $p_count;
		$week_fr[$day][$pstart_id][] = $sch[$key];
	}
	$day = $day + 1;
}
$arResult['WEEK_FREE'] = $week_fr;
$this->data['employee'] = $arParams['EMPLOYEE'];
$arParams['SCHEDULE'] = $schedule[$arParams['EMPLOYEE']]['schedule'];
$tmp_str = str_replace("\\", '/', __FILE__);
$pathStart					= substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
$periods_edit_script_path	= $pathStart.'/forms/edit_form.php';
$copy_week_script_path		= $pathStart.'/forms/copy_week.php';
$delete_period_path			= $pathStart.'/actions/delete_action.php';
$content_reload				= $pathStart.'/actions/content_reload.php';
$add_period_path			= $pathStart.'/actions/add_action.php';
$w_selector					= $pathStart.'/actions/week_selector.php';
$templ_selector				= $pathStart.'/actions/template_selector.php';
$templ_update				= $pathStart.'/actions/update_day_template.php';
$show_templ					= $pathStart.'/actions/lesson_time.php';
$w_copy						= $pathStart.'/actions/copy_week.php';
$duplicate_check			= $pathStart.'/actions/duplicate_check.php';
$lessons_time_edit_path		= $pathStart.'/forms/edit_lesson_time_form.php';
$add_time_template			= $pathStart.'/actions/add_time_template.php';
$del_time_template			= $pathStart.'/actions/delete_template_action.php';
$lessons_type_edit_path		= $pathStart.'/forms/edit_lesson_type.php';
$show_type					= $pathStart.'/actions/lesson_type.php';
$add_lesson_type			= $pathStart.'/actions/add_lesson_type.php';
$check_empl					= $pathStart.'/actions/check_empl.php';
$delete_lesson_type_action	= $pathStart.'/actions/delete_lesson_type_action.php';
?>

	<script type="text/javascript">

	if (document.getElementsByClassName) {
		getElementsByClass = function (classList, node) {
			return (node || document).getElementsByClassName(classList)
		}
	}
	else {
		getElementsByClass = function (classList, node) {
			var node = node || document,
				list = node.getElementsByTagName('*'),
				length = list.length,
				classArray = classList.split(/\s+/),
				classes = classArray.length,
				result = [], i, j;
			for (i = 0; i < length; i++) {
				for (j = 0; j < classes; j++) {
					if (list[i].className.search('\\b' + classArray[j] + '\\b') != -1) {
						result.push(list[i]);
						break
					}
				}
			}
			return result
		}
	}
	function time_delay(pause) {
		var clock = new Date();
		var start = clock.getTime();
		while (true) {
			var curr = new Date();
			if (curr.getTime() - start > pause) break;
		}
	}
	function ShowDayEditDialog(parms, ddat) {
		var Dialog = new BX.CDialog({
			title:"<?=GetMessage('T_DIALOG_TITLE')?>" + ' ' + ddat,
			content_url :'<?=$periods_edit_script_path?>',
			icon        :'',
			content_post:'arr=' + parms +'&site=<?=SITE_ID?>',
			resizable   :false,
			draggable   :true,
			height      :'500',
			width       :'900'
		});
		Dialog.SetButtons([
			{
				'title' :'<?=GetMessage('T_CLOSE')?>',
				'id'    :'ok',
				'name'  :'ok',
				'action':function () {
					location.reload();
					this.parentWindow.Close();
				}
			}
		]);
		Dialog.Show();
	}

	function showPeriod(p_id) {
		var el = getElementsByClass('period_info');
		for (var i = 0; i < el.length; i++) {
			el[i].className = 'period_info_hiden';
		}
		el = getElementsByClass('period_rec');
		for (i = 0; i < el.length; i++) {
			el[i].style.fontWeight = 'normal';
			el[i].style.backgroundColor = '#FFFFFF';
		}
		var s_el = document.getElementById('period_info_' + p_id);
		s_el.className = 'period_info';
		s_el = document.getElementById('period_' + p_id);
		s_el.style.fontWeight = 'bold';
		s_el.style.backgroundColor = '#F5F5F5';
		document.getElementById('add_period_block').style.display = "none";
		document.getElementById('descr').style.display = "block";
	}
	function deletePeriod(p_id, p_ar) {
		function fvoid(data) {
			jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
			var s_el = document.getElementById('content_edit_form');
			s_el.innerHTML = data;
		}

		document.getElementById('add_period_block').style.display = "none";
		document.getElementById('descr').style.display = "block";
		var TID = jsAjax.InitThread();
		var obContainer = document.getElementsByClassName('services_specialists');
		jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$delete_period_path?>', {'p_id':p_id, 'p_ar':p_ar});
	}
	function close_add_period() {
		//document.getElementById('gray_conteniner').style.display="none";
		document.getElementById('add_period_block').style.display = "none";
		document.getElementById('descr').style.display = "block";
	}
	function show_add_period() {
		var errorMessage = '';

		if (document.getElementById('templ_sel').length == 0)
			errorMessage = errorMessage + "<?=GetMessage('SCHEDULE_ERR_NO_SCHEDULE_CALLS')?><br>";
		if (document.getElementById('add_sel_period_type').length == 0)
			errorMessage += "<?=GetMessage('SCHEDULE_ERR_NO_PERIOD_TYPES')?><br>";
		if (document.getElementById('teacher_selector').length == 0)
			errorMessage = errorMessage + "<?=GetMessage('SCHEDULE_ERR_NO_TEACHERS')?><br>";
		if (document.getElementById('t_cabinet').length == 0)
			errorMessage = errorMessage + "<?=GetMessage('SCHEDULE_ERR_NO_CABINETS')?><br>";
		if (document.getElementById('add_period_services_block').length == 0)
			errorMessage = errorMessage + "<?=GetMessage('SCHEDULE_ERR_NO_SUBJECTS')?><br>";
		if (errorMessage != '') {
			show_er(errorMessage);
			return false;
		}
		document.getElementById('add_sel_period_type').options[0].selected = "selected";
		document.getElementById('teacher_selector').options[0].selected = "selected";
		document.getElementById('t_cabinet').options[0].selected = "selected";
		document.getElementById('add_period_services_block').options[0].selected = "selected";
		document.getElementById('add_time_from').value = "";
		document.getElementById('add_time_to').value = "";
		document.getElementById('cancle_input').checked = false;
		document.getElementById('id_templ').value = "";
		var objSel = document.getElementById("lesson_time");
		for (var j = 0; j < objSel.options.length; j++) {
			var option = objSel.options[j];
			option.selected = "";
		}
		document.getElementById('update_button').style.display = "none";
		document.getElementById('descr').style.display = "none";
		document.getElementById('add_button').style.display = "";
		;
		document.getElementById('add_period_block').style.display = "block";
	}

	function close_er() {
		document.getElementById('error_message').style.display = "none";
		document.getElementById('gray_conteniner').style.display = "none";
	}
	function show_er(err_text) {
		document.getElementById('er_message').innerHTML = err_text;
		document.getElementById('error_message').style.display = "block";
		document.getElementById('gray_conteniner').style.display = "block";
	}
	function close_er1() {
		document.getElementById('error_message1').style.display = "none";
	}
	function show_er1(err_text) {
		document.getElementById('er_message').innerHTML = err_text;
		document.getElementById('error_message1').style.display = "block";
	}

	function check_time(str) {
		var t_ar = str.split(":");
		if (typeof t_ar[0] == "undefined") return false;
		if (typeof t_ar[1] == "undefined") t_ar[1] = "00";
		if (typeof t_ar[2] == "undefined") t_ar[2] = "00";
		if (!isNaN(t_ar[0]) && (t_ar[0].length == 2)) {
			if ((parseInt(t_ar[0], 10) < 0) || (parseInt(t_ar[0], 10) > 24)) return false;
		}
		else {
			return false;
		}
		if ((!isNaN(t_ar[1])) && (t_ar[1].length == 2)) {
			if ((parseInt(t_ar[1], 10) < 0) || (parseInt(t_ar[1], 10) > 59)) return false;
		}
		else {
			return false;
		}
		if ((!isNaN(t_ar[2])) && (t_ar[2].length == 2)) {
			if ((parseInt(t_ar[2], 10) < 0) || (parseInt(t_ar[2], 10) > 59)) return false;
		}
		else {
			return false;
		}
		return t_ar[0] + ":" + t_ar[1] + ":" + t_ar[2];
	}
	function m_timetoint(t) {
		var t_ar = t.split(":");
		var ft = parseInt(t_ar[0], 10) * 3600 + parseInt(t_ar[1], 10) * 60 + parseInt(t_ar[2], 10);
		return ft;
	}
	function compare_time(t1, t2) {
		return m_timetoint(t1) < m_timetoint(t2)
	}
	function add_period(les, par_arr) {
		var el2 = document.getElementById('cancle_input'); //шаблон расписания
		var cancl = el2.checked;
		el2 = document.getElementById('templ_sel'); //шаблон расписания
		var rtemplate_id = el2.value;
		el2 = document.getElementById('add_sel_period_type'); //тип периода
		var period_type = el2.value;
		el2 = document.getElementById('teacher_selector'); // учитель
		var teacher = el2.value;
		el2 = document.getElementById('t_cabinet'); //кабинет
		var cabinet = el2.value;

		el2 = document.getElementById('t_comment'); //коммент
		var comment = el2.value;

		el2 = document.getElementById('add_period_services_block'); //предмет
		var lesson = el2.value;
		el2 = document.getElementById('add_time_from');
		var t_start = el2.value;
		el2 = document.getElementById('add_time_to');
		var t_end = el2.value;
		el2 = document.getElementById('id_empl');
		var del_empl = el2.value;
		el2 = document.getElementById('id_cab');
		var del_cab = el2.value;
		el2 = document.getElementById('id_per');
		var del_period = el2.value;
		el2 = document.getElementById('id_serv');
		var del_serv = el2.value;
		el2 = document.getElementById('id_templ');
		var del_templ = el2.value;
		el2 = document.getElementById('per_num');
		var del_per = el2.value;
		el2 = document.getElementById('lesson_time');
		var periods = [];
		var c = 0;
		for (var i = 0; i < el2.options.length; i++) {
			var option = el2.options[i];
			//show_er(el2.options[i].value);
			if (option.selected) {
				periods[c] = option.value;
				c = c + 1;
			}
		}
		var v = period_type + ':';
		p_start = '';
		p_end = '';
		if (les.indexOf(v, 0) > 0) {//если тип урок
			if (c < 1) {
				show_er("<?=GetMessage('NO_LESSONS')?>");
				return;
			}
		}
		else {//если тип свободное время
			if (t_start != "") {
				if (check_time(t_start)) {
					var p_start = check_time(t_start);
				}
				else {
					show_er("<?=GetMessage('TIME_FORMAT_ER')?>");
					return;
				}
			}
			else {
				show_er("<?=GetMessage('TIME_ER')?>");
				return;
			}
			if (t_end != "") {
				if (check_time(t_end)) {
					var p_end = check_time(t_end);
				}
				else {
					show_er("<?=GetMessage('TIME_END_FORMAT_ER')?>");
					;
					return;
				}
			}
			else {
				show_er("<?=GetMessage('TIME_END_ER')?>");
				return;
			}
			if (!compare_time(p_start, p_end)) {
				show_er("<?=GetMessage('TIME_COMPARE_ER')?>");
				return;
			}
		}
		function fvoid(data) {
			jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
			var s_el = document.getElementById('content_edit_form');
			if (data == 'er_emp') {
				show_er("<?=GetMessage('FREE_EMPL_ER')?>");
			}
			else if (data == 'er_cab') {
				show_er("<?=GetMessage('FREE_CAB_ER')?>");
			}
			else {
				s_el.innerHTML = data;
			}
		}

		var TID = jsAjax.InitThread();
		var obContainer = document.getElementsByClassName('services_specialists');
		jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$add_period_path?>', {'period_type':period_type, 'p_start':p_start, 'p_end':p_end, 'cabinet':cabinet, 'comment':comment, 'rtemplate_id':rtemplate_id, 'lesson':lesson, 'parms':par_arr, 'teacher':teacher, 'periods':periods, 'del_empl':del_empl, 'del_cab':del_cab, 'del_period':del_period, 'del_serv':del_serv, 'del_templ':del_templ, 'del_per':del_per, 'cancl':cancl});
	}
	function show_update_period(week, day, clas, empl, cabinet, period, service, tp, templ, p_num,t_start,t_end,les,cancl,comment) {
		document.getElementById('add_sel_period_type').value = tp;
		document.getElementById('lesson_time').value = p_num;
		document.getElementById('teacher_selector').value = empl;
		document.getElementById('t_cabinet').value = cabinet;
		document.getElementById('add_period_services_block').value = service;
		document.getElementById('add_time_from').value = t_start;
		document.getElementById('add_time_to').value = t_end;
		document.getElementById('id_empl').value = empl;
		document.getElementById('id_cab').value = cabinet;
		document.getElementById('id_per').value = period;
		document.getElementById('id_serv').value = service;
		document.getElementById('id_templ').value = templ;
		document.getElementById('per_num').value = p_num;
console.log(comment);
		document.getElementById('t_comment').value = comment;
		change_period_type(les);
		document.getElementById('update_button').style.display = "";
		document.getElementById('add_button').style.display = "none";
		document.getElementById('descr').style.display = "none";
		document.getElementById('add_period_block').style.display = "block";
		if (cancl > 0) {
			document.getElementById('cancle_input').checked = "checked";
		}
		else {
			document.getElementById('cancle_input').checked = false;
		}
	}
	isInt = function (field) {
		return !(+field != field || field.indexOf(".") != -1);
	};
	function week_copy() {
		var cur_dat = document.getElementById('add_sel_period_type').value;
		var edit_dat = document.getElementById('week_val').value;
		var clas = document.getElementById('empl_val').value;
		var cnt = document.getElementById('repeat_inp').value;
		var check_1 = document.getElementById('check_1');
		var check_2 = document.getElementById('check_2');
		if (check_1.checked) {
			var ch = check_1.value
		}
		else {
			ch = check_2.value
		}
		if (cur_dat == edit_dat) {
			show_er1("<?=GetMessage('WEEK_EQUAL')?>");
			return
		}
		if (!isInt(cnt)) {
			show_er1("<?=GetMessage('CNT_NB')?>");
			return
		}
		else {
			if (cnt < 1) {
				show_er1("<?=GetMessage('CNT_NB')?>");
				return
			}
		}
		function fvoid(data) {
			jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
			location.reload();
		}
//console.log({'clas':clas, 'st':cur_dat, 'en':edit_dat, 'cnt':cnt, 'ch':ch});
		var TID = jsAjax.InitThread();
		var obContainer = document.getElementsByClassName('services_specialists');
		jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$w_copy?>', {'clas':clas, 'st':cur_dat, 'en':edit_dat, 'cnt':cnt, 'ch':ch});
	}

	function ShowWeekCopyDialog(empl, week) {
		var Dialog = new BX.CDialog({
			title       :"<?=GetMessage('T_DIALOG_CW_TITLE')?>",
			head        :"<?=GetMessage('T_DIALOG_CW_HEAD')?>",
			content_url :'<?=$copy_week_script_path?>',
			icon        :'',
			content_post:'empl=' + empl + '&week=' + week +'&site=<?=SITE_ID?>',
			resizable   :false,
			draggable   :true,
			height      :'290',
			width       :'360'
		});
		Dialog.SetButtons([
			{
				'title' :'<?=GetMessage('T_DIALOG_CLOSE')?>',
				'id'    :'cancel',
				'name'  :'cancel',
				'action':function () {
					this.parentWindow.Close();
				}
			},
			{
				'title' :'<?=GetMessage('T_DIALOG_ACCEPT')?>',
				'id'    :'ok',
				'name'  :'ok',
				'action':function () {
					if (confirm('<?=GetMessage("COPY_WEEK_ALERT")?>')) {
						week_copy(0)
					}
				}
			}
		]);
		Dialog.Show();
	}
	function week_change(w_st) {
		function fvoid(data) {
			jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
			var s_el = document.getElementById('c_week_sel');
			s_el.innerHTML = data;
		}

		var TID = jsAjax.InitThread();
		var obContainer = document.getElementsByClassName('services_specialists');
		jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$w_selector?>', {'newstart':w_st});
	}
	function change_sel() {
		var s_el = document.getElementById('empl_sel');
		var cnt = s_el.options.length;
		for (var i=0;i<cnt;i++) {
			if (s_el.options[i].selected==true) {
				clas = s_el.options[i].value;
				break;
			}
		}
		<?
			/** @var $APPLICATION CMain*/
			$url = $APPLICATION->GetCurPageParam('',array('clas'));
			$url .= strpos($url,'?')===false?'?clas=':'&clas=';
		?>
		document.location.href = "<?=$url?>" + clas;
	}
	function change_period_type(les) {
		//show_er("123123");
		//document.getElementById('gray_conteniner').style.display="none";
		var s_el = document.getElementById('add_sel_period_type');
		var clas = s_el.value;
		var v = clas + ':';
		if (les.indexOf(v, 0) > 0) {
			document.getElementById('lesson_time_block').style.display = "block";
			document.getElementById('free_time_block').style.display = "none";
		}
		else {
			document.getElementById('lesson_time_block').style.display = "none";
			document.getElementById('free_time_block').style.display = "block";
		}
	}
	function templ_change() {
		var s_el = document.getElementById('templ_sel');
		var clas = s_el.value;

		function fvoid(data) {
			var s_el = document.getElementById('lesson_time');
			s_el.outerHTML = data;
		}

		var TID = jsAjax.InitThread();
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$templ_selector?>', {'new_id':clas});
	}
	function day_template_change(week, day, clas, p_ar) {
		var s_el = document.getElementById('templ_sel');
		var templ = s_el.value;

		function fvoid(data) {
			var s_el = document.getElementById('content_edit_form');
			s_el.innerHTML = data;
		}

		var TID = jsAjax.InitThread();
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$templ_update?>', {'week':week, 'day':day, 'clas':clas, 'templ':templ, 'p_ar':p_ar});
	}

	function ShowTimetableEditDialog(lc) {
		var Dialog = new BX.CDialog({
			title       :"<?=GetMessage('RINGS_SCHEDULE')?>",
			content_url :'<?=$lessons_time_edit_path?>',
			content_post:'lc=' +<?=$arParams['LESSON_COUNT']?> +'&site=<?=SITE_ID?>',
			icon        :'',
			resizable   :false,
			draggable   :true,
			height      :'490',
			width       :'660'
		});
		Dialog.SetButtons([
			{
				'title' :'<?=GetMessage('T_CLOSE')?>',
				'id'    :'ok',
				'name'  :'ok',
				'action':function () {
					location.reload();
					this.parentWindow.Close();
				}
			}
		]);
		Dialog.Show();
	}
	function showtemplate(id_template) {
		function fvoid(data) {
			var s_el = document.getElementById('content_edit_form');
			s_el.innerHTML = data;
		}

		var TID = jsAjax.InitThread();
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$show_templ?>', {'SELCTED':id_template, 'lc':<?=$arParams['LESSON_COUNT']?>});
		document.getElementById('update_button').style.display = "block";
		document.getElementById('add_button').style.display = "none";
	}
	function show_add_rasp() {
		document.getElementById('period_name').value = '';
		var el = document.getElementsByName('st_time');
		for (var i = 0; i < el.length; i++) {
			el[i].value = '';
		}
		el = document.getElementsByName('en_time');
		for (i = 0; i < el.length; i++) {
			el[i].value = '';
		}
		document.getElementById('update_button').style.display = "none";
		document.getElementById('add_button').style.display = "block";
		document.getElementById('instr').style.display = "none";
		document.getElementById('descr').style.display = "block";
	}
	function add_new_lesson_time() {
		var p_name = document.getElementById('period_name').value;
		var arr_start = [];
		var arr_end = [];
		var el = document.getElementsByName('st_time');
		for (var i = 0; i < el.length; i++) {
			arr_start.push(el[i].value);
		}
		el = document.getElementsByName('en_time');
		for (i = 0; i < el.length; i++) {
			arr_end.push(el[i].value);
		}
		BX.ajax.post(
			'<?=$add_time_template?>',
			{
				'pname'    :p_name,
				'arr_start':arr_start,
				'arr_end'  :arr_end,
				'lc'       :<?=$arParams['LESSON_COUNT']?>,
				'site':'<?=SITE_ID?>'
			},
			function (result) {
				//show_er(result.substr(0,result.length-3));
				if (result.substr(-3, 3) == "ERR") {
					show_er(result.substr(0, result.length - 3));
				}
				else {
					document.getElementById('gray_conteniner').style.display = "block";
					var TID = jsAjax.InitThread();
					var obContainer = document.getElementById('periods_block');
					jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
					var s_el = document.getElementById('wait_periods_block_' + TID);
					s_el.innerHTML = "<?=GetMessage('SAVE_PROGRESS')?>";
					s_el = document.getElementById('content_edit_form');
					setTimeout(function () {
						s_el.innerHTML = result;
						jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
					}, 500);
				}
			}
		);
	}
	function update_lesson_time(templ_id) {
		var p_name = document.getElementById('period_name').value;
		var arr_start = [];
		var arr_end = [];
		var el = document.getElementsByName('st_time');
		for (var i = 0; i < el.length; i++) {
			arr_start.push(el[i].value);
		}
		el = document.getElementsByName('en_time');
		for (i = 0; i < el.length; i++) {
			arr_end.push(el[i].value);
		}
		BX.ajax.post(
			'<?=$add_time_template?>',
			{
				'pname'    :p_name,
				'arr_start':arr_start,
				'arr_end'  :arr_end,
				'lc'       :<?=$arParams['LESSON_COUNT']?>,
				'templ'    :templ_id,
				'site':'<?=SITE_ID?>'
			},
			function (result) {
				//show_er(result.substr(0,result.length-3));
				if (result.substr(-3, 3) == "ERR") {
					show_er(result.substr(0, result.length - 3));
				}
				else {
					document.getElementById('gray_conteniner').style.display = "block";
					var TID = jsAjax.InitThread();
					var obContainer = document.getElementById('periods_block');
					jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
					var s_el = document.getElementById('wait_periods_block_' + TID);
					s_el.innerHTML = "<?=GetMessage('SAVE_PROGRESS')?>";
					s_el = document.getElementById('content_edit_form');
					setTimeout(function () {
						s_el.innerHTML = result;
						jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
					}, 500);
				}
			}
		);
	}
	function deletetemplate(templ_id) {
		BX.ajax.post(
			'<?=$del_time_template?>',
			{
				'templ_id':templ_id,
				'lc'      :<?=$arParams['LESSON_COUNT']?>,
				'site':'<?=SITE_ID?>'
			},
			function (result) {
				var s_el = document.getElementById('content_edit_form');
				s_el.innerHTML = result;
			}
		);
	}

	function ShowActivityTypesEditDialog() {
		var Dialog = new BX.CDialog({
			title:"<?=GetMessage('LESSON_TYPES')?>",
			content_url :'<?=$lessons_type_edit_path?>',
			content_post:'lc=' +<?=$arParams['LESSON_COUNT']?> +'&site=<?=SITE_ID?>',
			icon        :'',
			resizable   :false,
			draggable   :true,
			height      :'500',
			width       :'660'
		});
		Dialog.SetButtons([
			{
				'title' :'<?=GetMessage('T_CLOSE')?>',
				'id'    :'ok',
				'name'  :'ok',
				'action':function () {
					location.reload();
					this.parentWindow.Close();
				}
			}
		]);
		Dialog.Show();
	}
	function showtype(id_template) {
		function fvoid(data) {
			var s_el = document.getElementById('content_edit_form');
			s_el.innerHTML = data;
		}

		var TID = jsAjax.InitThread();
		jsAjax.AddAction(TID, fvoid);
		jsAjax.Post(TID, '<?=$show_type?>', {'SELCTED':id_template, 'lc':<?=$arParams['LESSON_COUNT']?>});
		document.getElementById('update_button').style.display = "block";
		document.getElementById('add_button').style.display = "none";
	}
	function show_add_lesson_type() {
		document.getElementById('period_name').value = '';
		document.getElementById('descr_area').value = '';
		document.getElementById('update_button').style.display = "none";
		document.getElementById('add_button').style.display = "block";
		document.getElementById('l_type').style.display = "none";
		document.getElementById('l_select').style.display = "block";
		document.getElementById('l_select_div').style.display = "block";
		document.getElementById('instr').style.display = "none";
		document.getElementById('descr').style.display = "block";
	}
	function add_new_lesson_type() {
		var p_name = document.getElementById('period_name').value;
		var p_descr = document.getElementById('descr_area').value;
		var p_type = document.getElementById('l_select').value;
		BX.ajax.post(
			'<?=$add_lesson_type?>',
			{
				'pname'  :p_name,
				'p_descr':p_descr,
				'p_type' :p_type,
				'lc'     :<?=$arParams['LESSON_COUNT']?>,
				'site':'<?=SITE_ID?>'
			},
			function (result) {
				//show_er(result.substr(0,result.length-3));
				if (result.substr(-3, 3) == "ERR") {
					show_er(result.substr(0, result.length - 3));
				}
				else {
					document.getElementById('gray_conteniner').style.display = "block";
					var TID = jsAjax.InitThread();
					var obContainer = document.getElementById('periods_block');
					jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
					var s_el = document.getElementById('wait_periods_block_' + TID);
					s_el.innerHTML = "<?=GetMessage('SAVE_PROGRESS')?>";
					s_el = document.getElementById('content_edit_form');
					setTimeout(function () {
						s_el.innerHTML = result;
						jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
					}, 500);
				}
			}
		);
	}
	function update_lesson_type(tp_id) {
		var p_name = document.getElementById('period_name').value;
		var p_descr = document.getElementById('descr_area').value;
		var p_type = document.getElementById('l_select').value;
		BX.ajax.post(
			'<?=$add_lesson_type?>',
			{
				'pname'  :p_name,
				'p_descr':p_descr,
				'p_type' :p_type,
				'lc'     :<?=$arParams['LESSON_COUNT']?>,
				'type_id':tp_id,
				'site':'<?=SITE_ID?>'
			},
			function (result) {
				//show_er(result.substr(0,result.length-3));
				if (result.substr(-3, 3) == "ERR") {
					show_er(result.substr(0, result.length - 3));
				}
				else {
					document.getElementById('gray_conteniner').style.display = "block";
					var TID = jsAjax.InitThread();
					var obContainer = document.getElementById('periods_block');
					jsAjaxUtil.ShowLocalWaitWindow(TID, obContainer, true);
					var s_el = document.getElementById('wait_periods_block_' + TID);
					s_el.innerHTML = "<?=GetMessage('SAVE_PROGRESS')?>";
					s_el = document.getElementById('content_edit_form');
					setTimeout(function () {
						s_el.innerHTML = result;
						jsAjaxUtil.CloseLocalWaitWindow(TID, obContainer);
					}, 500);
				}
			}
		);
	}
	function delete_lesson_type(tp_id) {
		BX.ajax.post(
			'<?=$delete_lesson_type_action?>',
			{
				'tp_id':tp_id,
				'lc'   :<?=$arParams['LESSON_COUNT']?>,
				'site':'<?=SITE_ID?>'
			},
			function (result) {
				var s_el = document.getElementById('content_edit_form');
				s_el.innerHTML = result;
			}
		);
	}
	function show_changetempl() {
		document.getElementById('change_templ_sel').style.display = "block";
		document.getElementById('change_templ').style.display = "block";
		document.getElementById('change_templ_label').style.display = "none";
		document.getElementById('show_change_templ').style.display = "none";
	}

	</script>

<?
$this->IncludeComponentTemplate();
?>