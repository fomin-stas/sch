<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("bitrix.schoolschedule");
?>

<? 
//коммент для сохранения кодировки
$ar=json_decode(str_replace ('`', '"', $_POST['p_ar']),true);

if (isset ($ar['site'])) {
	$siteID = trim($ar['site']);
	$rsSite = CSite::GetByID($siteID);
	if (!$rsSite->GetNext()) {
		$siteID = SITE_ID;
	}
} else {
	$siteID = SITE_ID;
}
	$arFilter=array();
	$arFilter['WEEK_START']= array('COMPARSION'=>'=','START_DATE'=>$ar['week_start']);
	$arFilter['WEEK_DAY']=$ar['week_day'];
	$arFilter['CLASS']=$ar['clas'];
	$arFilter['EMPLOYEE']=$ar['id_employee'];
	$arFilter['CABINET']=$ar['id_cabinet'];
	$arFilter['PERIOD']=$ar['id_period'];
	$arFilter['SERVICE']=$ar['services'];
	$arFilter['SITE_ID']=$siteID;
	//$arFilter['TEMPLATE']=$ar['id_template'];
	$arFilter['PERIOD_NUMBER']=$ar['period_number'];
	$ls=array();
	$ls=MCSchedule::GetList($arFilter);
	
if ($_POST['show']="Y")
{
	if (count($ls)>0)
	{
		echo 'duplicate';
		$duplicate_er=true;
	}
	else
	{
		echo 'no_error';
		$duplicate_er=false;
	}
}
else
{
if (count($ls)>0)
	{
		$duplicate_er=true;
	}
	else
	{
		$duplicate_er=false;
	}
}
	
?>