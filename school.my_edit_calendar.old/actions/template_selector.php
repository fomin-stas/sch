<?
//коммент для сохранения кодировки
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$tmp_str=str_replace ("\\", '/',__FILE__);
$work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
$work_path=substr($work_path,0,-8);
require_once($_SERVER["DOCUMENT_ROOT"].$work_path.'/forms/lang/'.LANGUAGE_ID.'/copy_week.php');
CModule::IncludeModule("bitrix.schoolschedule");
$month=array
(
		1=>GetMessage('CALENDAR_MONTH_1'),
		2=>GetMessage('CALENDAR_MONTH_2'),
		3=>GetMessage('CALENDAR_MONTH_3'),
		4=>GetMessage('CALENDAR_MONTH_4'),
		5=>GetMessage('CALENDAR_MONTH_5'),
		6=>GetMessage('CALENDAR_MONTH_6'),
		7=>GetMessage('CALENDAR_MONTH_7'),
		8=>GetMessage('CALENDAR_MONTH_8'),
		9=>GetMessage('CALENDAR_MONTH_9'),
		10=>GetMessage('CALENDAR_MONTH_10'),
		11=>GetMessage('CALENDAR_MONTH_11'),
		12=>GetMessage('CALENDAR_MONTH_12')
);

if (isset($_POST['new_id']))
{
	$new_id=$_POST['new_id'];
}
$templ=MCSchedule::GetTemplateList(array('SITE_ID'=>$siteID));
?>




<select id="lesson_time" name="lesson_time"  multiple="multiple">
<?
	foreach ($templ[$new_id]['lessons'] as $key=>$val)
	{			
?>
	<option value="<?=$key?>"><?=$key.'. '.$val['START'].' - '.$val['END']?></option>
<?}?>
</select>