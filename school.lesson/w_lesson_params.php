<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$langFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/school/school.lesson/lang/".LANGUAGE_ID."/w_lesson_params.php";
require_once($langFile);
IncludeAJAX();
CAjax::Init();
CModule::IncludeModule("iblock");
CModule::IncludeModule("bitrix.schoolschedule");


$popupWindow = new CJSPopup(GetMessage("LESSON_PARAMS_W_TITLE"), array());
//$popupWindow->ShowError(GetMessage("ACCESS_DENIED"));

$popupWindow->ShowTitlebar(GetMessage("PAGE_NEW_WINDOW_TITLE"));
$popupWindow->StartContent();

?>
<style>
#lessonParamsForm{}
#lessonParamsTable{}
#lessonParamsTable td,
#lessonParamsTable th{
	border:none!important;
	background:none!important;
}
</style>
<?
$ELEMENT_ID = intval($_REQUEST["ELEMENT_ID"]);
if($ELEMENT_ID>0)
{
	$el = CIBlockElement::GetByID($ELEMENT_ID);
	$lessonTheme = $lessonDesc = "";
	if($el = $el->Fetch())
	{
		$rsLessonTheme = CIBlockElement::GetProperty($el["IBLOCK_ID"],$ELEMENT_ID, array(), Array("CODE"=>"LESSON_THEME"));
		$lessonTheme = $rsLessonTheme->Fetch();
		$lessonTheme = $lessonTheme["VALUE"];
		$lessonDesc = $el["DETAIL_TEXT"];
	}
	
	$lessonTheme = htmlentities($lessonTheme,ENT_QUOTES,SITE_CHARSET);
	$lessonDesc = htmlentities($lessonDesc,ENT_QUOTES,SITE_CHARSET);
	?>
	<form id="lessonParamsForm">
	<table id='lessonParamsTable' style='width:100%;'>
	<tr>
		<td style='width:90px;' valign=top><?=GetMessage("LESSON_THEME")?>:</td>
		<td><input style='width:100%' name='lesson_theme' id="lesson_theme" value="<?=$lessonTheme?>"/></td>
	</tr>
	<tr>
		<td valign=top><?=GetMessage("LESSON_DESC")?>:</td>
		<td><textarea name='lesson_desc' style='width:100%; height:150px;' id="lesson_desc"><?=$lessonDesc?></textarea></td>
	</tr>
	</table>
	</form>
	<?
}
$popupWindow->EndContent();
$popupWindow->StartButtons();
?>
<?if($ELEMENT_ID>0){?><input name="btn_lesson_save" id="btn_lesson_save" onclick='return lessonSave();' type="button" value="<?=GetMessage("LESSON_SAVE_PARAMS")?>" title="<?=GetMessage("LESSON_SAVE_PARAMS")?>" /><?}?>
<input name="btn_popup_close" type="button" value="<?=GetMessage("LESSON_PARAMS_CANCEL")?>" onclick="<?=$popupWindow->jsPopup?>.CloseDialog()" title="<?=GetMessage("LESSON_PARAMS_CANCEL")?>" />
<?$popupWindow->EndButtons();?>
<script>
function lessonSave()
{
	var oLessonTheme = document.getElementById("lesson_theme");
	var oLessonDesc = document.getElementById("lesson_desc");
	
	function ShowResult(data) 
	{ 
		<?=$popupWindow->jsPopup?>.CloseDialog();
		location.reload();
	} 
	var TID = jsAjaxUtil.PostData(document.location, {'mode':'ajax','action':'save_lesson_desc','lessonTheme':oLessonTheme.value,'lessonDesc':oLessonDesc.value}, ShowResult);
	return false;
}
</script>































