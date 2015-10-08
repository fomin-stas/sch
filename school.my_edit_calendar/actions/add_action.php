<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("bitrix.schoolschedule");
?>
<? 

function utf8_urldecode($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
    return html_entity_decode($str,null,'UTF-8');;
}

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
}


?>

<?
//коммент для сохранения кодировки	
	$ar=json_decode(str_replace ('`', '"', $_POST['parms']),true);
	
	if ($ar['delete']==1)
	{
		$arFilter['WEEK_START']=$ar['week_start'];
		$arFilter['WEEK_DAY']=$ar['week_day'];
		$arFilter['CLASS']=$ar['clas'];
		$arFilter['EMPLOYEE']=$_POST['del_empl'];
		$arFilter['CABINET']=$_POST['del_cab'];
		$arFilter['PERIOD']=$_POST['del_period'];
		$arFilter['SERVICE']=$_POST['del_serv'];
		$arFilter['SITE_ID']=$siteID;
		$arFilter['TEMPLATE']=intval($_POST['del_templ']);
		$arFilter['PERIOD_NUMBER']=$_POST['del_per'];
		MCSchedule::Delete($arFilter);
		$arFilter=array();
	}
	
	$period_type=MCPeriodType::GetById($_POST['period_type']);
	if ($period_type['lesson'])
	{
		$arFilter=array
		(
			'TIME_START'=>array('COMPARSION'=>'=', 'TIME'=>"00:00:00"),
			'TIME_END'=>array('COMPARSION'=>'=', 'TIME'=>"00:00:00"),
			'SITE_ID'=>$siteID,
			'TYPE'=>$_POST['period_type']
		);
	}
	else
	{
		$arFilter=array
		(
			'TIME_START'=>array('COMPARSION'=>'=', 'TIME'=>$_POST['p_start']),
			'TIME_END'=>array('COMPARSION'=>'=', 'TIME'=>$_POST['p_end']),
			'SITE_ID'=>$siteID,
			'TYPE'=>$_POST['period_type']
		);
	}
	
	
	
	$period_id=0;
	
	$periods=MCPeriod::GetList($arFilter);
	foreach ($periods as $key=>$val)
	{
		$period_id=$val['ID'];
	}

	
	if ($period_id==0)
	{
		
		$arFields['PERIOD_TYPE']=$_POST['period_type'];
		if ($_POST['p_start'])
		{
			$arFields['TIME_START']=$_POST['p_start'];
		}
		else
		{
			$arFields['TIME_START']="00:00:00" ;
		}
		
		if ($_POST['p_end'])
		{
			$arFields['TIME_END']=$_POST['p_end'] ;
		}
		else
		{
			$arFields['TIME_END']="00:00:00";
		}
		
		$period_id=MCPeriod::Add($arFields);
	}
	
	$arFilter=array(
		'CLASS'=>$ar['clas'],
		'WEEK_DAY'=>$ar['week_day'],
		'WEEK_START'=>array('COMPARSION'=>'=' ,'START_DATE'=>$ar['week_start']),
		
	);
	$tmpl_search=MCSchedule::GetLessons($arFilter);
	$templ=false;
	foreach ($tmpl_search as $key=>$val)
	{
		$val['id_template'];
		if (!$templ) $templ=$val['id_template'];
	}
	if (!$templ) $templ=$_POST["rtemplate_id"];
	if ($period_type['lesson'])
	{
		foreach ($_POST["periods"] as $key=>$val)
		{
			$arFields=array(
				'week_start'=>$ar['week_start'],
				'week_day'=>$ar["week_day"],
				'clas'=>$ar["clas"],
				'id_employee'=>$_POST['teacher'],
				'id_cabinet'=>$_POST["cabinet"],
				'id_period'=>$period_id,
				'service_id'=>$_POST["lesson"],
				'site_id'=>$siteID,
				'id_template'=>$templ,
				'period_number'=>$val,
				'comment'=>utf8_urldecode($_POST["comment"]),
				'canceled'=>$_POST["cancl"]);
			MCSchedule::Add($arFields);
		}
	}
	else
	{

			$arFields=array(
				'week_start'=>$ar['week_start'],
				'week_day'=>$ar["week_day"],
				'clas'=>$ar["clas"],
				'id_employee'=>$_POST['teacher'],
				'id_cabinet'=>$_POST["cabinet"],
				'id_period'=>$period_id,
				'service_id'=>$_POST["lesson"],
				'site_id'=>$siteID,
				'id_template'=>$templ,
				'period_number'=>1,
				'comment'=>utf8_urldecode($_POST["comment"]),
				'canceled'=>$_POST["cancl"]);
		MCSchedule::Add($arFields);
	}
	
	$tmp_str=str_replace ("\\", '/',__FILE__);
	$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/')).'/content_reload.php' ;
	require_once $content_reload;
?>