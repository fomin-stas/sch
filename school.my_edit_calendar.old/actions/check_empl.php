<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("bitrix.schoolschedule");
?>

<?
//коммент для сохранения кодировки	
	$ar=json_decode(str_replace ('`', '"', $_POST['parms']),true);
	$er=false;
if (isset ($ar['site'])) {
	$siteID = trim($ar['site']);
	$rsSite = CSite::GetByID($siteID);
	if (!$rsSite->GetNext()) {
		$siteID = SITE_ID;
	}
} else {
	$siteID = SITE_ID;
}
	$templ=MCSchedule::GetTemplateList(array('SITE_ID'=>$siteID));
	//print_r ($templ);
	foreach ($_POST["periods"] as $key=>$val)
	{
		if ($_POST['p_start'])
		{
			$p_st=$_POST['p_start'];
		}
		else
		{
			$p_st=$templ[$_POST["rtemplate_id"]] ['lessons'][$val]['START'];
		}
		
		if ($_POST['p_end'])
		{
			$p_en=$_POST['p_end'] ;
		}
		else
		{
			$p_en=$templ[$_POST["rtemplate_id"]] ['lessons'][$val]['END'];
		} 
		
		$arCheck=array(
				'WEEK_START' => $ar['week_start'],
				'WEEK_DAY' => $ar['week_day'],
				'EMPLOYEE' => $_POST['teacher'],
				'TIME_START' => $p_st,
				'TIME_END' => $p_en,
		);
		
		$searc=MCSchedule::CheckEmployee($arCheck);
		if (count($searc)>0)
		{
			$er=true;
		}
	}
	
	
	
	if ($er)
	{
		echo "er";
	}
	else
	{
		echo "ok";
	}
	
	
	
	//$tmp_str=str_replace ("\\", '/',__FILE__);
	//$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/')).'/content_reload.php' ;
	//require_once $content_reload;
?>