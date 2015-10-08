<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
$tmp_str=str_replace ("\\", '/',__FILE__);
$my_edit_calendar_work_path = '/bitrix/components/school/school.my_edit_calendar/actions';
//$my_edit_calendar_work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$my_edit_calendar_work_path.'/lang/'.LANGUAGE_ID.'/lesson_time.php');
CModule::IncludeModule("bitrix.schoolschedule");
?>
<input type="hidden" name="site" value="<?=$siteID?>">

<?
//коммент для сохранения кодировки

$sp=10;


$templ=MCSchedule::GetTemplateList(array('SITE_ID'=>$siteID));
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
				<a href="javascript:showtemplate(<?=$key?>)" style="width:190px"><? echo $value['NAME']?></a>&nbsp
				<div id="delete_period_<?=$key?>"class="period_edit">
					<a href="javascript:if(confirm('<?=GetMessage('T_PERIOD_DELETE_ALERT')?>')) deletetemplate(<?=$key?>)"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/delete.png"></a>
				</div>
			</div>
			<?
	}
	
	?>
	<br>
	<div style="text-align:center" id="add_period_link">
		<input type="button" onClick="javascript:show_add_rasp()" value="<?=GetMessage('T_ADD_PERIOD')?>">
	</div>
	</div>

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
		$nk=0;
			foreach ($templ[$selected_prtiod]['lessons'] as $key=>$value)
			{
		?>
				<div class="times_block" >
				<div style="width:80px; float:left; text-align:right; padding:3px 5px 0 0"><?=$key?></div>
				
				<?$APPLICATION->IncludeComponent("bitrix:main.clock","high_z",Array(
																				"INPUT_ID" => "st_time".$key, 
																				"INPUT_NAME" => "st_time", 
																				"INIT_TIME" => substr($value['START'],0,-3), 
																				"zIndex"=>'2000',
																				"STEP" => "0",
																				 
																			)
												);
				?>
				 - 
				
				<?$APPLICATION->IncludeComponent("bitrix:main.clock","high_z",Array(
																				"INPUT_ID" => "en_time".$key, 
																				"INPUT_NAME" => "en_time",  
																				"INIT_TIME" => substr($value['END'],0,-3), 
																				"STEP" => "0", 
																				"zIndex"=>2000
																			)
												);
				?>
				
				</div><br>
		<?
				$nk=$nk+1;
			}
			
			$nk=$nk+1;
			$cnt=intval($_POST['lc']);
			
			while ($nk<=$cnt)
			{
			?>
				<div class="times_block" >
				<div style="width:80px; float:left; text-align:right; padding:3px 5px 0 0"><?=$nk?></div>
				<?$APPLICATION->IncludeComponent("bitrix:main.clock","high_z",Array(
																				"INPUT_ID" => "st_time".$nk, 
																				"INPUT_NAME" => "st_time", 
																				"INIT_TIME" => "", 
																				"zIndex"=>'2000',
																				"STEP" => "0",
																				 
																			)
												);
				?>
				 - 
				
				<?$APPLICATION->IncludeComponent("bitrix:main.clock","high_z",Array(
																				"INPUT_ID" => "en_time".$nk, 
																				"INPUT_NAME" => "en_time",  
																				"INIT_TIME" => "", 
																				"STEP" => "0", 
																				"zIndex"=>2000
																			)
												);
				?>
				</div><br>
				
				
			<?
				
				$nk=$nk+1;
			}
		?>
		
		<input style="display:none" id="add_button" type="button" value="<?=GetMessage('T_SAVE')?>" name="add_new_lesson_time" onclick='javascript:add_new_lesson_time("0")'>
		<input id="update_button" type="button" value="<?=GetMessage('T_SAVE')?>" name="add_new_period" onclick='javascript:update_lesson_time("<?=$selected_prtiod?>")'>
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