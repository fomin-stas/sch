<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$langFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/school/school.lesson/lang/".LANGUAGE_ID."/w_add_work_type.php";
require_once($langFile);
IncludeAJAX();
CAjax::Init();
CModule::IncludeModule("iblock");
CModule::IncludeModule("bitrix.schoolschedule");

$popupWindow = new CJSPopup(GetMessage("ADDWORKTYPE_W_TITLE"), array());
//$popupWindow->ShowError(GetMessage("ACCESS_DENIED"));

$popupWindow->ShowTitlebar(GetMessage("PAGE_NEW_WINDOW_TITLE"));
$popupWindow->StartContent();

$ELEMENT_ID = intval($_REQUEST["ELEMENT_ID"]);
$IS_WORK_TYPE = false;
if($ELEMENT_ID>0)
{
	$el = CIBlockElement::GetByID($ELEMENT_ID);
	$markTypes = "";
	if($el = $el->Fetch())
	{
		$rs = CIBlockElement::GetProperty($el["IBLOCK_ID"],$ELEMENT_ID, array(), Array("CODE"=>"MARK_TYPES"));
		while($mt = $rs->Fetch())
		{
			$markTypes[] = $mt["VALUE"];
		}
		
	}
	?>
	<form id="lessonWorkTypes">
		<?
		$arMarktypes = CSchool::GetMarkTypes();
		foreach($arMarktypes as $k=>$v)
		{
			if(in_array($k,$markTypes))unset($arMarktypes[$k]);
		}
		
		if(count($arMarktypes)>0)
		{
			$IS_WORK_TYPE = true;
			?>
			<b><?=GetMessage("LESSON_SELECT_WORKTYPE")?>:</b>
			<select name="addWorkType" id="addWorkTypeSelect"><?
			foreach($arMarktypes as $k=>$mt)
			{
				?><option value='<?=$k?>'><?=$mt["FULL"]?></option><?
			}
			?></select><?
		}
		else
		{
			?><p><?=GetMessage("ALL_WORKTYPES_SELECTED")?></p><?
		}
		
		?>
	</form>
	<?
}
$popupWindow->EndContent();
$popupWindow->StartButtons();
?>
<?if($ELEMENT_ID>0 && $IS_WORK_TYPE){?><input name="btn_lesson_save" id="btn_lesson_save" onclick='return lessonSave();' type="button" value="<?=GetMessage("LESSON_ADD_WORKTYPE")?>" title="<?=GetMessage("LESSON_ADD_WORKTYPE")?>" /><?}?>
<input name="btn_popup_close" type="button" value="<?=GetMessage("LESSON_PARAMS_CANCEL")?>" onclick="<?=$popupWindow->jsPopup?>.CloseDialog()" title="<?=GetMessage("LESSON_PARAMS_CANCEL")?>" />
<?$popupWindow->EndButtons();?>
<script>
function lessonSave()
{
	var oWorkType = document.getElementById("addWorkTypeSelect");
	function ShowResult(data) 
	{ 
		<?=$popupWindow->jsPopup?>.CloseDialog();
		location.reload();
	}
	
	var TID = jsAjaxUtil.PostData(document.location, {'mode':'ajax','action':'lesson_add_worktype','worktype':oWorkType.value}, ShowResult);
	return false;
}
</script>































