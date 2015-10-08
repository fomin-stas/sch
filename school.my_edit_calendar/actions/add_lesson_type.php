<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$tmp_str=str_replace ("\\", '/',__FILE__);
$my_edit_calendar_work_path = '/bitrix/components/school/school.my_edit_calendar/actions';
//$my_edit_calendar_work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$my_edit_calendar_work_path.'/lang/'.LANGUAGE_ID.'/add_time_template.php');
CModule::IncludeModule("bitrix.schoolschedule");
?>
<?
if (isset ($_POST['site'])) {
	$siteID = trim($_POST['site']);
	$rsSite = CSite::GetByID($siteID);
	if (!$rsSite->GetNext()) {
		$siteID = SITE_ID;
	}
} else {
	$siteID = SITE_ID;
}

//коммент для сохранения кодировки
$errors=false;


if (strlen($_POST['pname'])==0)
{
	$errors=true;
	echo GetMessage('T_NO_TEMPLATE_NAME');
}
else
{
	$nam=$_POST['pname'];
	CUtil::decodeURIComponent($nam);
}


$descr=$_POST['p_descr'];
CUtil::decodeURIComponent($descr);




if (!$errors)
{
	$arFields=array();
	$arFields['NAME']=$nam;
	$arFields['DESCRIPTION']=$descr;
	$arFields['LESSON']=$_POST['p_type'];
	
	if (!$_POST['type_id'])
	{
		$selected_prtiod=MCPeriodType::Add($arFields);
	}
	else
	{
		$selected_prtiod=MCPeriodType::Update($_POST['type_id'],$arFields);
	}
	$tmp_str=str_replace ("\\", '/',__FILE__);
	$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/')).'/lesson_type.php' ;
	require_once $content_reload;
}
else
{
	echo 'ERR';
}

?>