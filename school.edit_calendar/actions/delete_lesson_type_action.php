<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("bitrix.schoolschedule");
?>
<? 
//коммент для сохранения кодировки
	MCPeriodType::Delete($_POST['tp_id']);
?>

<?
$tmp_str=str_replace ("\\", '/',__FILE__);
$content_reload = $_SERVER["DOCUMENT_ROOT"].substr(dirname($tmp_str), strpos($tmp_str, '/bitrix/')).'/lesson_type.php' ;
require_once $content_reload;
?>