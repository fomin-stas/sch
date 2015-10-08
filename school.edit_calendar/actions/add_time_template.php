<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$tmp_str=str_replace ("\\", '/',__FILE__);
$work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$work_path.'/lang/'.LANGUAGE_ID.'/add_time_template.php');
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

function check_time($time)
{
	$time_parts = explode(":", $time);
	if (count($time_parts)!=2)
	{
		return false;
	}
	else 
	{
		if ((intval($time_parts[0])<0) or (intval($time_parts[0])>23))
		{
			return false;
		}
		else 
		{
			if (!is_numeric($time_parts[0]))
			{
				return false;
			}
		}
		
		if ((intval($time_parts[1])<0) or (intval($time_parts[1])>59))
		{
			return false;
		}
		else
		{
			if (!is_numeric($time_parts[1]))
			{
				return false;
			}
		}
		return MCDateTimeTools::AddLeadingZero(intval($time_parts[0])).':'.MCDateTimeTools::AddLeadingZero(intval($time_parts[1])).':00';
	}
}


function check_time_array($ar)
{
	$emp=false;
	foreach ($ar as $key=>$val)
	{
		if (($emp) and ($val))
		{
			return false;
		}
		
		if (!$val)
		{
			$emp=true;
		}
	}
	return true;
}



//коммент для сохранения кодировки
$errors=false;

$time_array=array();

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

foreach ($_POST['arr_start'] as $key=>$val)
{
	if (strlen($val)>0)
	{
		if (check_time($val))
		{
			if (!check_time($_POST['arr_end'][$key]))
			{
				$errors=true;
				$num=$key+1;
				echo GetMessage('T_TIME_FORMAT_END'). $num .'<br>';
			}
			if (!$errors)
			{
				$time_array[]=array('start'=>check_time($val), 'end'=>check_time($_POST['arr_end'][$key]));
			}
		}
		else
		{
			$errors=true;
			$num=$key+1;
			echo GetMessage('T_TIME_FORMAT_START').$num.'<br>';
		}
	}
}

if (!$errors)
{
	if (count($time_array)==0)
	{
		$errors=true;
		echo GetMessage('T_NO_TEMPLATE_TIMES');
	}
}

$old_time=false;
if (!$errors)
{
	foreach ($time_array as $key=>$value)
	{
		if ($value['start']>=$value['end'])
		{
			$errors=true;
			$num=$key+1;
			echo GetMessage('T_TIME_START_END1').$num.GetMessage('T_TIME_START_END2').'<br>';
		}
		
		if ($old_time)
		{
			if ($old_time>$value['start'])
			{
				$errors=true;
				$num=$key+1;
				echo GetMessage('T_TIME_INTERSECT').$key.' '.GetMessage('T_TIME_AND'). ' '. $num. '<br>';
			}
		}
		$old_time=$value['end'];
	}
}

if (!$errors)
{
	if ((!check_time_array($_POST['arr_start'])) or (!check_time_array($_POST['arr_end'])))
	{
		$errors=true;
		echo GetMessage('T_LESSON_MISSED') .'<br>';
	}
}


if (!$errors)
{
	if (!$_POST['templ'])
	{
		$selected_prtiod=MCSchedule::AddLessontTemplate($nam,$time_array,$siteID);
	}
	else
	{
		$selected_prtiod=MCSchedule::UpdateLessontTemplate($_POST['templ'],$nam,$time_array,$siteID);
	}
	$tmp_str=str_replace ("\\", '/',__FILE__);
	$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strpos($tmp_str, '/bitrix/')).'/lesson_time.php' ;
	require_once $content_reload;
}
else
{
	echo 'ERR';
}

?>