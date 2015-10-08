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

$arFilter=array
(
	'WEEK_START'=>$_POST['week'],
	'WEEK_DAY'=>$_POST['day'],
	'CLASS'=>$_POST['clas'],
	'SITE_ID'=>$siteID,
);


$arFields=array(
	'TEMPLATE'=>$_POST['templ']
);
MCSchedule::Update($arFields,$arFilter);
		
$tmp_str=str_replace ("\\", '/',__FILE__);
$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/')).'/content_reload.php' ;
require_once $content_reload;
?>