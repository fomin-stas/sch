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

if (isset($_POST['newstart']))
{
	$w_start=intval($_POST['newstart']);
	$w_end=$w_start+6*24*60*60;
}

?>




<div id="c_week_sel" class="week_selector" style="padding:0; margin:0">
	<div class="weeks">
		<?$nd=MCDateTimeTools::AddDays(date('d.m.Y',$w_start),-7);?>
		<?$cd=MCDateTimeTools::AddDays(MCDateTimeTools::GetWeekStartByDate('d.m.Y'),-7);
		?>
		<div class="prev"><?if ($cd!=$nd){?><a href="javascript:week_change(<?=strtotime($nd)?>)"></a><?}?></div>
		
		<div class="date">
		<?
			echo date('j',$w_start).' '.$month[intval(date('m',$w_start))].' - '.date('j',$w_end).' '.$month[intval(date('m',$w_end))].' '.date('Y',$w_end);
		?>
		</div>
		<?$nd=MCDateTimeTools::AddDays(date('d.m.Y',$w_start),7)?>
		<div class="next"><a href="javascript:week_change(<?=strtotime($nd)?>)"></a></div>
		<div class="clear"></div>
	</div>
</div>
<input id="week_val" type="hidden" value="<?=date('d.m.Y',$w_start)?>">