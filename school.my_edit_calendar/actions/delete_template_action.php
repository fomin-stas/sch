<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("bitrix.schoolschedule");
?>
<? 
//коммент для сохранения кодировки
	MCSchedule::DeleteTemplate($_POST['templ_id']);
?>

<?
$tmp_str=str_replace ("\\", '/',__FILE__);
$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/')).'/lesson_time.php' ;
require_once $content_reload;
?>