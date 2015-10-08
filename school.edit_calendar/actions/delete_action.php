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
	$arFilter['WEEK_START']=$ar['week_start'];
	$arFilter['WEEK_DAY']=$ar['week_day'];
	$arFilter['CLASS']=$ar['clas'];
	$arFilter['EMPLOYEE']=$ar['id_employee'];
	$arFilter['CABINET']=$ar['id_cabinet'];
	$arFilter['PERIOD']=$ar['id_period'];
	$arFilter['SERVICE']=$ar['services'];
	$arFilter['SITE_ID']=$siteID;
	$arFilter['TEMPLATE']=$ar['id_template'];
	$arFilter['PERIOD_NUMBER']=$ar['period_number'];
	MCSchedule::Delete($arFilter);
?>

<?
$tmp_str=str_replace ("\\", '/',__FILE__);
$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strpos($tmp_str, '/bitrix/')).'/content_reload.php' ;
require_once $content_reload;
?>