<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$langFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/school/school.lesson/lang/".LANGUAGE_ID."/w_comment_student.php";
require_once($langFile);
IncludeAJAX();
CAjax::Init();
CModule::IncludeModule("iblock");
CModule::IncludeModule("bitrix.schoolschedule");

$popupWindow = new CJSPopup(GetMessage("ADDCOMMENT_W_TITLE"), array());
//$popupWindow->ShowError(GetMessage("ACCESS_DENIED"));

$popupWindow->ShowTitlebar(GetMessage("ADDCOMMENT_W_TITLE"));
$popupWindow->StartContent();

$USER_ID = intval($_REQUEST["USER_ID"]);
$ELEMENT_ID = intval($_REQUEST["ELEMENT_ID"]);

if($USER_ID>0 && $ELEMENT_ID>0)
{
	$el = CIBlockElement::GetByID($ELEMENT_ID);
	
	
	
	
	if($el = $el->Fetch())
	{
		$rs = CIBlockElement::GetProperty($el["IBLOCK_ID"],$ELEMENT_ID, array(), Array("CODE"=>"STUDENT_WORK_COMMENT"));
		$stComment = "";
		while($comment = $rs->Fetch())
		{
			if($comment["VALUE"]["USER"]==$USER_ID)
			{
				$stComment = $comment["VALUE"]["COMMENT"];
			}
		}
	}
	?>
	<form id="lessonComment">
		<textarea name='lesson_comment' style='width:100%; height:130px;' id="lesson_comment"><?=$stComment?></textarea>
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
	var oLessonComment = document.getElementById("lesson_comment");

	function ShowResult(data) 
	{ 
		<?=$popupWindow->jsPopup?>.CloseDialog();
		location.reload();
	} 
	var TID = jsAjaxUtil.PostData(document.location, {'mode':'ajax','action':'save_lesson_comment','USER_ID':<?=$USER_ID?>, 'comment':oLessonComment.value}, ShowResult);
	return false;
}
</script>































