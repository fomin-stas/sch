<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$langFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/school/school.lesson/lang/".LANGUAGE_ID."/w_edit_work_type.php";
require_once($langFile);
IncludeAJAX();
CAjax::Init();
CModule::IncludeModule("iblock");
CModule::IncludeModule("bitrix.schoolschedule");


$ELEMENT_ID = intval($_REQUEST["ELEMENT_ID"]);
$MARK_TYPE = $_REQUEST["mark_type"];
$arMarktypes = CSchool::GetMarkTypes();


$popupWindow = new CJSPopup(GetMessage("EDITWORKTYPE_W_TITLE"), array());
//$popupWindow->ShowError(GetMessage("ACCESS_DENIED"));




$popupWindow->ShowTitlebar(GetMessage("EDITWORKTYPE_W_TITLE")." \"".$arMarktypes[$MARK_TYPE]["FULL"]."\"");
$popupWindow->StartContent();



if($ELEMENT_ID>0 && array_key_exists($MARK_TYPE,$arMarktypes))
{
	$el = CIBlockElement::GetByID($ELEMENT_ID);
	?><br/><br/><?

	
	
	if(count($arMarktypes)>0)
	{
		?>
		<form id="lessonWorkTypes">
		<b><?=GetMessage("LESSON_SELECT_WORKTYPE")?>:</b>
		<select name="addWorkType" id="editWorkTypeSelect"><?
		foreach($arMarktypes as $k=>$mt)
		{
			if($k == $MARK_TYPE)continue;
			?><option value='<?=$k?>'><?=$mt["FULL"]?></option><?
		}
		?></select>
		</form>
		<?
	}
}
$popupWindow->EndContent();
$popupWindow->StartButtons();
if($ELEMENT_ID>0 && array_key_exists($MARK_TYPE,$arMarktypes))
{
	?><input name="btn_lesson_save" id="btn_lesson_save" onclick='return lessonSave();' type="button" value="<?=GetMessage("LESSON_EDIT_WORKTYPE")?>" title="<?=GetMessage("LESSON_EDIT_WORKTYPE")?>" /><?
}
?>
<input name="btn_popup_close" type="button" value="<?=GetMessage("LESSON_PARAMS_CANCEL")?>" onclick="<?=$popupWindow->jsPopup?>.CloseDialog()" title="<?=GetMessage("LESSON_PARAMS_CANCEL")?>" />
<?$popupWindow->EndButtons();?>
<script>
function lessonSave()
{
	var oWorkType = document.getElementById("editWorkTypeSelect");
	function ShowResult(data) 
	{ 
		<?=$popupWindow->jsPopup?>.CloseDialog();
		location.reload();
	}
	
	var TID = jsAjaxUtil.PostData(document.location, {'mode':'ajax','action':'lesson_edit_worktype','old_worktype':'<?=$MARK_TYPE?>','new_worktype':oWorkType.value}, ShowResult);
	return false;
}
</script>































