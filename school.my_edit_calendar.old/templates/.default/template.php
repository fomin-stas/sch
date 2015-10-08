<div id="shedule">
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
//$arParams['EMPL_TYPE']='admin';
//коммент для сохранения кодировки
?>
<p><a name="calend"></a></p>
	<label><?=GetMessage('MC_EdCal_Check_Employee')?></label>
<select class="select1" name="class_sel" id='empl_sel' onChange="change_sel()">
<?foreach ($arResult['CLASSES'] as $structureGroup):?>
	<optgroup label="<?=$structureGroup['NAME']?>">
		<?foreach ($structureGroup['VALUES'] as $key=>$val):?>
			<option <?if ($val['selected']) echo 'selected'?> value="<?=$val['ID']?>"><?=$val['NAME']?></option>
		<?endforeach;?>
	</optgroup>
<?endforeach;?>
</select><br><br>


<?

$arCheck=array(
		
		'WEEK_START' => '2013-02-04',
		'WEEK_DAY' => 2,
		'EMPLOYEE' => 2,
		'TIME_START' => '08:00:00',
		'TIME_END' => '08:40:00',
		);

print_r(MCSchedule::CheckEmployee($arCheck));

$month=array
(
		1=>GetMessage('CALENDAR_MONTH_1'),
		2=>GetMessage('CALENDAR_MONTH_2'),
		3=>GetMessage('CALENDAR_MONTH_3'),
		4=>GetMessage('CALENDAR_MONTH_4'),
		5=>GetMessage('CALENDAR_MONTH_5'),
		6=>GetMessage('CALENDAR_MONTH_6'),
		7=>GetMessage('CALENDAR_MONTH_7'),
		8=>GetMessage('CALENDAR_MONTH_8'),
		9=>GetMessage('CALENDAR_MONTH_9'),
		10=>GetMessage('CALENDAR_MONTH_10'),
		11=>GetMessage('CALENDAR_MONTH_11'),
		12=>GetMessage('CALENDAR_MONTH_12')
);

$cell_styles=array(
	1=>'turn_color',
	2=>'free_class1',
	3=>'free_class0',
	4=>'vacation_color'
);

?>

<?if (($arParams['EMPL_TYPE']!='admin') or (isset($_GET['empl'])) ){?>

<div class="main_calendar" id="calendararea">
		<div class="calendar_header">
			<h2><?=GetMessage("CALENDAR_HEADER")?></h2>
		</div>
		<div class="week_selector">
					<table class="simple">
				<tr>
					<td class="week_but_prev" style="text-align: right;">
								
								<?if ((CanEdit($arParams['USER_GROUP'],$arParams['EDIT_GROUP'])) or $arParams['pr_week'])
								{?>
									<a href="<?echo $APPLICATION->GetCurPageParam('week='.($arParams['WEEK_NUMBER']-1),array('week'))?>#calend"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/prev_week.jpg" title="<?=GetMessage('CALENDAR_PREV_WEEK')?>"></a>
								<?}?>
					</td>
					<td>
						<?
							echo date('j',$arParams['WEEK']['weekStart']).' '.$month[intval(date('m',$arParams['WEEK']['weekStart']))].' - '.date('j',$arParams['WEEK']['weekEnd']).' '.$month[intval(date('m',$arParams['WEEK']['weekEnd']))].' '.date('Y',$arParams['WEEK']['weekEnd']);
						?>
					</td>
					<td class="week_but_next" style="text-align: left;">
								<?if ((CanEdit($arParams['USER_GROUP'],$arParams['EDIT_GROUP'])) or $arParams['nx_week'])
								{?>
									<a  href="<?echo $APPLICATION->GetCurPageParam('week='.($arParams['WEEK_NUMBER']+1),array('week'))?>#calend"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/next_week.jpg" title="<?=GetMessage('CALENDAR_NEXT_WEEK')?>"></a>
								<?}?>
					</td>
			</tr>
			</table>
		</div>	
		<div class="time_area">
			<table class="simple">
			<thead>
				<tr>
					<?$txtToday=''?>
					<th width="6%">
						<img height="20px" width="17px" alt="" src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/icon_time.png">
					</th>
						<?for ($weekDay = 1; $weekDay<=7; $weekDay++):?>
						<?
							if ($arParams['WEEK'][$weekDay]==strtotime(date('d.m.Y')))
							{
								$additionalCalss = 'currentDay';
								$img="icon_edit.gif";
							}
							else
							{
								$additionalCalss = '';
								$txtToday='';
								$img="icon_edit.png";
							}
						?>
							<th width="13%" class="week_day_<?=$weekDay?> <?=$additionalCalss?>">
								<?
								$week_key_edit=date('Y-m-d',$arParams['WEEK']['weekStart']);
								$m_ar['week_start']=$week_key_edit;
								$m_ar['week_day']=$weekDay;
								$m_ar['clas']=$arParams['CLASS'];
								$m_ar['IBLOCK_ID']=$arParams['SUBJECTS_IBLOCK_ID'];
								$m_ar['CLASSES_IBLOCK_ID']=$arParams['CLASSES_IBLOCK_ID'];
								$m_ar['CABINETS_IBLOCK_ID']=$arParams['CABINETS_IBLOCK_ID'];
								$ar=json_encode($m_ar);
								$ar=str_replace ('"', '`', $ar)
								?>
								<span title="<?=GetMessage('CALENDAR_DAY_FULL_'.$weekDay).', '.date('d', $arParams['WEEK'][$weekDay]).' '.$month[date('m', $arParams['WEEK'][$weekDay])].' '.date('Y', $arParams['WEEK'][$weekDay]).GetMessage('CALENDAR_YEAR_S'.$weekDay)?>">
								<?=GetMessage('CALENDAR_DAY_'.$weekDay)?>,
								<?echo date('d', $arParams['WEEK'][$weekDay]);?></span>
								<?if (CanEdit($arParams['USER_GROUP'],$arParams['EDIT_GROUP'])){?>
									<div style="display:inline;margin-left:4px;"><a title="<?=GetMessage("T_EDIT_ALT")?>" href='javascript:ShowDayEditDialog("<?=$ar?>","<?=date("d", $arParams["WEEK"][$weekDay])." ".$month[intval(date("m",$arParams["WEEK"][$weekDay]))]." ".date("Y", $arParams["WEEK"][$weekDay])?>")'><img alt="<?=GetMessage("T_EDIT_ALT")?>" src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/<?=$img?>"></a></div>
								<?}?>
							</th>
						<?endfor?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="first"></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="last"></td>
				</tr>
			</tfoot>	
			
			<tbody>			
			<? $DayS=($arParams['DAY_START']=='')?$arResult['day_s']:$arParams['DAY_START']*60;?>
			<? $DayE=($arParams['DAY_END']=='')?$arResult['day_e']:$arParams['DAY_END']*60;?>
			<? $step=$arParams['WEB_STEP'];?>
			<?$i=1;
			for ($dc = 1; $dc <= 7; $dc++)
			{
				$ci[$dc]=1;
				$first_unon[$dc]=false;
			}
			?>
			<?while ($i<=$arParams['LESSON_COUNT']){?>
			<?
						
			for ($dc = 1; $dc <= 7; $dc++)
			{
				if ($ci[$dc]>$union[$dc])
				{
					$union[$dc]=0;
					$ci[$dc]=1;
				}
			}
			
			
			?>
				<tr>
					<th><?=$i?></th>
					<?
					for ($dc = 1; $dc <= 7; $dc++) 
					{
						if ($union[$dc]==0)
						{ 
							$union[$dc]=intval($arResult['WEEK_FREE'][$dc][$i][0]['count']);
							if ($union[$dc])
							{
								$first_unon[$dc]=true;	
							}
						}
						elseif ($union[$dc]!=0)
						{
							$first_unon[$dc]=false;
						}
					?>
					
					<?if (($union[$dc]==0) or ($first_unon[$dc])){?> 
					<td <?if ($arResult['WEEK_FREE'][$dc][$i]) {echo 'rowspan="'.$arResult['WEEK_FREE'][$dc][$i][0]['count'].'"';}?>>
						<?if ($arResult['WEEK_SCHEDULE'][$dc][$i])
						{
							$start_lesson=substr($arResult['WEEK_SCHEDULE'][$dc][$i][0]['lesson_start'],0,-3);
							$end_lesson=substr($arResult['WEEK_SCHEDULE'][$dc][$i][0]['lesson_end'],0,-3);
							
						}
						foreach($arResult['WEEK_SCHEDULE'][$dc][$i] as $key=>$val)
						{
							if (!$val['cancled'])
							{
								$color='talon_color';	
							}
							else
							{
								$color='old_color';
							}
							?>
							<div class="item <?=$color?>">
							<?
							echo $start_lesson. ' - ' .$end_lesson.'<br>';
							echo $val['service_id']['NAME'].'<br>';
							echo $val['id_cabinet']['NAME'].'<br>';
							echo $val['id_employee']['NAME_SHORT'];
							if (CanEdit($arParams['USER_GROUP'],$arParams['EDIT_GROUP'])){
								if (!$val['cancled'])
								{
									$url2=$_SERVER['SCRIPT_NAME'].									
									'?cw_start='.date('Y-m-d',$arParams['WEEK']['weekStart']).
									'&c_day='.$dc.
									'&clas='.$arParams['CLASS'].
									'&c_empl='.$val['id_employee']['ID'].
									'&c_cab='.$val['id_cabinet']['ID'].
									'&c_per='.$val['id_period'].
									'&c_serv='.$val['service_id']['ID'].
									'&c_templ='.intval($val['id_template']).
									'&c_pernum='.$val['period_number'].
									'&week='.intval($_GET['week']).
									'&cancl=Y#calend';
									$l="javascript:if(confirm('".GetMessage("TIME_CANCL_ALERT")."')) jsUtils.Redirect([], '".$url2."');";
									echo '<a href="'.htmlspecialcharsbx($l).'"><div class="delete"></div></a>';
								}
								else
								{
									$url2=$_SERVER['SCRIPT_NAME'].
									'?cw_start='.date('Y-m-d',$arParams['WEEK']['weekStart']).
									'&c_day='.$dc.
									'&clas='.$arParams['CLASS'].
									'&c_empl='.$val['id_employee']['ID'].
									'&c_cab='.$val['id_cabinet']['ID'].
									'&c_per='.$val['id_period'].
									'&c_serv='.$val['service_id']['ID'].
									'&c_templ='.intval($val['id_template']).
									'&c_pernum='.$val['period_number'].
									'&week='.intval($_GET['week']).
									'&ret=Y#calend';
									echo '<a href="'.$url2.'"><div class="retr"></div></a>';
								}
							}
							?>
							</div>
							<?
						}
						
						if ($arResult['WEEK_SCHEDULE'][$dc][$i]){?>
					
						<?}?>
						
						
						<?if ($arResult['WEEK_FREE'][$dc][$i])
						{						
						foreach($arResult['WEEK_FREE'][$dc][$i] as $key=>$val)
						{
							if (!$val['cancled'])
							{
								$color='line_color';
							}
							else
							{
								$color='old_color';
							}
						}
						
						?>
						<div class="item <?=$color?> resize">
						<?
						$ltime_start=substr($arResult['WEEK_FREE'][$dc][$i][0]['time_start'],0,-3);
						$ltime_end=substr($arResult['WEEK_FREE'][$dc][$i][0]['time_end'],0,-3);
						echo $ltime_start. ' - ' .$ltime_end.'<br>';
						}
							foreach($arResult['WEEK_FREE'][$dc][$i] as $key=>$val)
							{
								echo $val['service_id']['NAME'].'<br>';
								echo $val['id_cabinet']['NAME'].'<br>';
								echo $val['id_employee']['NAME_SHORT'].'<br><br>';
							}
						if ($arResult['WEEK_FREE'][$dc][$i]){
						
						if (CanEdit($arParams['USER_GROUP'],$arParams['EDIT_GROUP'])){
							if (!$val['cancled'])
							{
								$url2=$_SERVER['SCRIPT_NAME'].
								'?cw_start='.date('Y-m-d',$arParams['WEEK']['weekStart']).
								'&c_day='.$dc.
								'&clas='.$arParams['CLASS'].
								'&c_empl='.$val['id_employee']['ID'].
								'&c_cab='.$val['id_cabinet']['ID'].
								'&c_per='.$val['id_period'].
								'&c_serv='.$val['service_id']['ID'].
								'&c_templ='.intval($val['id_template']).
								'&c_pernum='.$val['period_number'].
								'&week='.intval($_GET['week']).
								'&cancl=Y#calend';
								$l="javascript:if(confirm('".GetMessage("TIME_CANCL_ALERT")."')) jsUtils.Redirect([], '".$url2."');";
								echo '<a href="'.$l.'"><div class="delete"></div></a>';
							}
							else
							{
								$url2=$_SERVER['SCRIPT_NAME'].
								'?cw_start='.date('Y-m-d',$arParams['WEEK']['weekStart']).
								'&c_day='.$dc.
								'&clas='.$arParams['CLASS'].
								'&c_empl='.$val['id_employee']['ID'].
								'&c_cab='.$val['id_cabinet']['ID'].
								'&c_per='.$val['id_period'].
								'&c_serv='.$val['service_id']['ID'].
								'&c_templ='.intval($val['id_template']).
								'&c_pernum='.$val['period_number'].
								'&week='.intval($_GET['week']).
								'&ret=Y#calend';
								echo '<a href="'.$url2.'"><div class="retr"></div></a>';
							}
						}?>
						</div>
						<?}?>
						
					</td>
					<?}?>					
					<?}?>

					<?
					for ($dc = 1; $dc <= 7; $dc++)
					{
						$ci[$dc]=$ci[$dc]+1;
					}
					?>
					
				</tr>
			<?
				$i=$i+1;
			}?>
			</tbody>
			</table>
		</div>
	</div>
	<div class="clear"></div>
	<?if (CanEdit($arParams['USER_GROUP'],$arParams['EDIT_GROUP'])){?>
	<a href="javascript:ShowWeekCopyDialog(<?=$arParams['CLASS']?>,<?=$arParams['WEEK']['weekStart']?>)"><div style="float:left"><div style="display:inline"><img height="24px" width="24px" src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/copy_week.png"></div><span style="margin:5px"><?=GetMessage('COPY_WEEK')?></span></div></a>
	<a href="javascript:ShowTimetableEditDialog(<?=$arParams['LESSON_COUNT']?>)"><div style="float:left"><div style="display:inline"><img height="24px" width="24px" src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/table.png"></div><span style="margin:5px"><?=GetMessage('LESSON_TIME_EDIT')?></span></div></a>
	<a href="javascript:ShowActivityTypesEditDialog()"><div style="float:left"><div style="display:inline"><img height="24px" width="24px" src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/pencil.png"></div><span style="margin:5px"><?=GetMessage('LESSON_TYPE_EDIT')?></span></div></a>
	<br><br>
	<?}?>

	<br><br>

	<div class="whois">
		<ul>
			<li>
				<span class="legend talon_color">&nbsp;</span>
				<span class="legend_text"><?=GetMessage('LESSON_TYPE_LEGEND')?></span>
			</li>
			
			<li>
				<span class="legend line_color">&nbsp;</span>
				<span class="legend_text"><?=GetMessage('FREE_TYPE_LEGEND')?></span>
			</li>
			
			<li>
				<span class="legend old_color">&nbsp;</span>
				<span class="legend_text"><?=GetMessage('CANCLED_TYPE_LEGEND')?></span>
			</li>
		</ul>
			<div class="clear"></div>
	</div>
	<?}?>
</div>
<script type="text/javascript">
//	$(document).ready(function(){
//		$('#shedule .main_calendar .time_area table tbody td .resize').each(function(){
//			var height = $(this).parents('td').height();
//			$(this).css('height', height - 8);
//		});
//	});
</script>
