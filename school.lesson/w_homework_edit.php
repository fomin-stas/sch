<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$langFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/components/school/school.lesson/lang/".LANGUAGE_ID."/w_homework_edit.php";
require_once($langFile);
IncludeAJAX();
CAjax::Init();
CModule::IncludeModule("iblock");
CModule::IncludeModule("bitrix.schoolschedule");

$popupWindow = new CJSPopup(GetMessage("HOMEWORK_W_TITLE"), array());
//$popupWindow->ShowError(GetMessage("ACCESS_DENIED"));

$popupWindow->ShowTitlebar(GetMessage("PAGE_NEW_WINDOW_TITLE"));
$popupWindow->SetSuffix("homework");
$FROM_NAME = $popupWindow->GetFormName();
$popupWindow->StartContent();

$ELEMENT_ID = intval($_REQUEST["ELEMENT_ID"]);
if($ELEMENT_ID>0)
{
	$el = CIBlockElement::GetByID($ELEMENT_ID);
	$homework = "";
	if($el = $el->Fetch())
	{
		$rsHomework = CIBlockElement::GetProperty($el["IBLOCK_ID"],$ELEMENT_ID, array(), Array("CODE"=>"HOME_WORK"));
		$homework = $rsHomework->Fetch();
		$homework = $homework["VALUE"];
		$homework = htmlentities($homework,ENT_QUOTES,SITE_CHARSET);
		?>
		<input type='hidden' name='mode' value='ajax'/>
		<input type='hidden' name='action' value='save_lesson_homework'/>
		<h4><?=GetMessage("LESSON_HOMEWORK")?></h4>
		<?$APPLICATION->IncludeComponent(
			"bitrix:fileman.light_editor",
			"",
			Array(
				"CONTENT" => (array_key_exists("TEXT",$homework))?$homework["TEXT"]:$homework,
				"INPUT_NAME" => "homework",
				"INPUT_ID" => "lesson_homework",
				"WIDTH" => "100%",
				"HEIGHT" => "250px",
				"RESIZABLE" => "Y",
				"AUTO_RESIZE" => "Y",
				"VIDEO_ALLOW_VIDEO" => "N",
				"USE_FILE_DIALOGS" => "N",
				"ID" => "",
				"JS_OBJ_NAME" => ""
			)
		);?>
		<br/>
		<h2><?=GetMessage("HOMEWORK_FILES")?></h2>
		<?
		$rsHomeworkFiles = CIBlockElement::GetProperty($el["IBLOCK_ID"],$ELEMENT_ID, array(), Array("CODE"=>"HOME_WORK_FILES"));
		$homeworkFiles = array();
		if($f = $rsHomeworkFiles->Fetch())
		{
			$homeworkFiles = $f;
			$homeworkFiles["VALUE"] = array();
			do
			{
				$homeworkFiles["VALUE"][] = $f["VALUE"];
			}while($f = $rsHomeworkFiles->Fetch());
		}
		
		$cnt = 0;
		foreach($homeworkFiles["VALUE"] as $res)
		{
			if(empty($res))continue;
			$file = CFile::GetFileArray($res);
			$descr = $file["DESCRIPTION"];
			?><div style='margin-bottom:10px;border:1px solid #ccc; padding:10px; float:left; margin:0 5px 5px 0;width:45%;height:100px;'><?=CFile::InputFile("HOMEWORK_FILE_".$res, 0, $res, false, 0, "", "", 30, "style='border:1px solid #ccc;' value='".$descr."'", ' value="'.$res.'"')?></div><?
			$cnt++;
		}
		
		$cntNew = intval($homeworkFiles["MULTIPLE_CNT"]);
		
		for($i=0;$i<$cntNew;$i++)
		{
			?><div style='margin-bottom:10px;border:1px solid #ccc; padding:10px; float:left; margin:0 5px 5px 0;width:45%;height:100px;'><?=CFile::InputFile("HOMEWORK_FILE_n".$i, 0, 0, false, 0, "", "", 30, "style='border:1px solid #ccc;'", ' value=""')?></div><?
			/*?><div><?=CFile::InputFile("HOMEWORK_FILE_n".$i, 0, 0, false, 0, "", "", 0, "", ' value=""')?></div><?*/
		}
	}
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
	var obFormList = document.getElementsByName("<?=$FROM_NAME?>");
	if(obFormList.length>0)
	{
		obForm = obFormList[0];
		obForm.action = "";
		obForm.method = "POST";
		obForm.enctype = "multipart/form-data";
		//enctype="multipart/form-data"
		obForm.submit();
	}
	return false;
	
}
</script>































