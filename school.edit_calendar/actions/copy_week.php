<?
//коммент для сохранения кодировки	
Require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
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


$ar_filter=array(
	'CLASS'=>$_POST['clas'],
	'WEEK_START'=>array('COMPARSION'=>'=','START_DATE'=>$_POST['en']),
	'SITE_ID'=>SITE_ID	
);




$a_parms=array
(
	'CLASS'=>$_POST['clas'],
	'COPY_FROM'=>$_POST['st']
);

$a_changes=array(
	'COPY_DATE'=>$_POST['en']	
);


$cnt=$_POST['cnt'];
$ch=$_POST['ch'];

if ($ch=="Y")
{
	$inerv=2;
}
else 
{
	$inerv=1;
}
$i=1;

for ($i = 1; $i <= $cnt; $i++) {
	
		$ar_filter=array(
				'CLASS'=>$a_parms['clas'],
				'WEEK_START'=>$a_changes['COPY_DATE']
		);
	
	MCSchedule::Delete($ar_filter);
	
	
	MCWeek::Copy($a_parms,$a_changes);
	$new_dat=MCDateTimeTools::AddDays($a_changes['COPY_DATE'],7);

	if ($ch=="Y")
	{
		$new_dat=MCDateTimeTools::AddDays($new_dat,7);
	}
	$a_changes['COPY_DATE']=$new_dat;
}
?>

