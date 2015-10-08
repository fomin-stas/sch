<?
//коммент для сохранения кодировки
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$tmp_str=str_replace ("\\", '/',__FILE__);
$work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$work_path.'/lang/'.LANGUAGE_ID.'/edit_form.php');
CModule::IncludeModule('iblock');

if (isset ($_POST['site'])) {
	$siteID = trim($_POST['site']);
	$rsSite = CSite::GetByID($siteID);
	if (!$rsSite->GetNext()) {
		$siteID = SITE_ID;
	}
} else {
	$siteID = SITE_ID;
}

$ar=json_decode(str_replace ('`', '"', $_POST['arr']),true);
$tmp_str=str_replace ("\\", '/',__FILE__);
$dir=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
$dir=substr($dir, 0, -6);
$content_reload = $_SERVER["DOCUMENT_ROOT"].$dir.'/actions/lesson_time.php' ;
?>
<div id=content_edit_form>
<?require_once $content_reload?> 
</div>
