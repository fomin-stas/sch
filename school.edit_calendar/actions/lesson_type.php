<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
$tmp_str=str_replace ("\\", '/',__FILE__);
$work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$work_path.'/lang/'.LANGUAGE_ID.'/lesson_type.php');
CModule::IncludeModule("bitrix.schoolschedule");
?>
<input type="hidden" name="site" value="<?=$siteID?>">

<?
//коммент для сохранения кодировки

$templ=MCPeriodType::GetList();
?>
	<div style="width: 40%" id="periods_block">
	<?
	if (!$selected_prtiod)
	{
		if (!$_POST['SELCTED'])
		{
			$selected_prtiod=false;
		}
		else
		{
			$selected_prtiod=$_POST['SELCTED'];
		}
	}
	if (count($templ)==0)
	{
	?>
	<div id="np" class="period_rec" style="margin:0px"><?=GetMessage('T_NO_PERIODS')?></div>
	<?
	}
	foreach ($templ as $key=>$value)
	{
		
			if (!$selected_prtiod)
			{
				$selected_prtiod=$key;
			}
			if ($selected_prtiod==$key)
			{
				$sel1='font-weight:bold; background-color:#F5F5F5';
			}
			else
			{
				$sel1='';
			}
			?>
			<div id="period_<?=$key?>" class="period_rec" style="<?=$sel1?>"> 
				<a href="javascript:showtype(<?=$key?>)" style="width:200px;"><? echo $value['NAME']?></a>&nbsp
				<div id="delete_period_<?=$key?>"class="period_edit">
					<a href="javascript:if(confirm('<?=GetMessage('T_PERIOD_DELETE_ALERT')?>')) delete_lesson_type(<?=$key?>)"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/delete.png"></a>
				</div>
			</div>
			
			<?
	}
	
	?>
	<br>
	<div style="text-align:center" id="add_period_link">
		<input type="button" onClick="javascript:show_add_lesson_type()" value="<?=GetMessage('T_ADD_PERIOD')?>">
	</div>
	</div>
	
	<?
	
	?>

	<div style="width: 59%" id="description_block">
	
	<?if (count($templ)==0)
	{
	?>
		<div id="instr">
		<?=GetMessage('T_INSTR')?>
		</div>
		<div id="descr" style="display:none">
	<?
	}
	else
	{ 
	?>
		<div id="instr" style="display:none">
		<?=GetMessage('T_INSTR')?>
		</div>
		<div id="descr">
	<?}?>
		
		<?=GetMessage('T_PERIOD_NAME')?><br><br>
		<input style="width:100%" name='perid_name' id='period_name' value='<?=$templ[$selected_prtiod]['NAME']?>'><br><br>
		<?=GetMessage('T_PERIOD_TIME')?><br><br> 
		<?
		
		?>
		<textarea style='width:366px; height: 200px;'  id="descr_area"><?=$templ[$selected_prtiod]['DESCRIPTION']?></textarea><br><br>
		<?
			if ($templ[$selected_prtiod]['LESSON'])
			{
		?>
				<div id="l_type"><?=GetMessage('T_LESSON')?></div>
		<?
			}
			else
			{
		?>
				<div id="l_type"><?=GetMessage('T_FREE')?></div>
		<?}?>
			<div id="l_select_div" style="width:100%; display:none"><?=GetMessage('T_PERIOD_LESSON')?></div><br> 
			<select id="l_select" style="width:100%; display:none">
				<option value='1' selected><?=GetMessage('T_LESSON')?></option>
				<option value='0'><?=GetMessage('T_FREE')?></option>
			</select><br><br>
		<div>
		<input style="display:none" id="add_button" type="button" value="<?=GetMessage('T_SAVE')?>" name="add_new_lesson_time" onclick='javascript:add_new_lesson_type("0")'>
		<input id="update_button" type="button" value="<?=GetMessage('T_SAVE')?>" name="add_new_period" onclick='javascript:update_lesson_type("<?=$selected_prtiod?>")'>
		<div style="float: right; width: 10px">&nbsp;</div>
		</div>
	
	</div>	

<div ID="gray_conteniner">
	
</div>

<div id="error_message">
<div style="height: 10px"></div>
<div class="icon-error"></div>
<div id="er_message" style="padding:10px 0 0 40px;"></div><div class="clear"></div>
<div class="center_buttons">

	<input type="button" value="<?=GetMessage('T_CLOSE')?>" name="close_er" onclick="javascript:close_er()">
	
</div>
	 
</div>