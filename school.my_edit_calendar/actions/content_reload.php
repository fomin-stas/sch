<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
$tmp_str=str_replace ("\\", '/',__FILE__);
$my_edit_calendar_work_path = '/bitrix/components/school/school.my_edit_calendar/actions';//
//$my_edit_calendar_work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$my_edit_calendar_work_path.'/lang/'.LANGUAGE_ID.'/content_reload.php');
CModule::IncludeModule("bitrix.schoolschedule");
MCSchedule::ClearBase();
?>
<input type="hidden" name="site" value="<?=$siteID?>">

<?
$dat=strtotime($ar['week_start']);
//коммент для сохранения кодировки	


$arFilter=array(
		'CLASS'=>$ar['clas'],
		'WEEK_DAY'=>$ar['week_day'],
		'WEEK_START'=>array('COMPARSION'=>'=' ,'START_DATE'=>date('d.m.Y',$dat)),
		'SITE_ID' => $siteID,
		
);
$schedule=MCSchedule::GetLessons($arFilter);


foreach ($schedule as $key=>$val)
{
	$res = CIBlockElement::GetByID($val['id_class']);
	if($ar_res = $res->GetNext())
	{
		$schedule[$key]['id_class']=array('ID'=>$val['id_class'],'NAME'=>$ar_res['NAME']);
	}
	
	$res = CIBlockElement::GetByID($val['id_cabinet']);
	if($ar_res = $res->GetNext())
	{
		$schedule[$key]['id_cabinet']=array('ID'=>$val['id_cabinet'],'NAME'=>$ar_res['NAME']);
	}
	
	$res = CIBlockElement::GetByID($val['service_id']);
	if($ar_res = $res->GetNext())
	{
		$schedule[$key]['service_id']=array('ID'=>$val['service_id'],'NAME'=>$ar_res['NAME']);
	}
	
	$rsUser = CUser::GetByID($val['id_employee']);
	if ($arUser = $rsUser->Fetch())
	{
		$schedule[$key]['id_employee']=array('ID'=>$val['id_employee'],'NAME'=>$arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME']);
	}
	
	$templ_id=intval($val['id_template']);
	
}

$fr_time=MCSchedule::GetFreeTime($arFilter);

foreach ($fr_time as $key=>$val)
{
	$res = CIBlockElement::GetByID($val['id_class']);
	if($ar_res = $res->GetNext())
	{
		$fr_time[$key]['id_class']=array('ID'=>$val['id_class'],'NAME'=>$ar_res['NAME']);
	}
	
	$res = CIBlockElement::GetByID($val['id_cabinet']);
	if($ar_res = $res->GetNext())
	{
		$fr_time[$key]['id_cabinet']=array('ID'=>$val['id_cabinet'],'NAME'=>$ar_res['NAME']);
	}
	
	$res = CIBlockElement::GetByID($val['service_id']);
	if($ar_res = $res->GetNext())
	{
		$fr_time[$key]['service_id']=array('ID'=>$val['service_id'],'NAME'=>$ar_res['NAME']);
	}
	
	$rsUser = CUser::GetByID($val['id_employee']);
	if ($arUser = $rsUser->Fetch())
	{
		$fr_time[$key]['id_employee']=array('ID'=>$val['id_employee'],'NAME'=>$arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME']);
	}
	if (!$templ_id)
	{
		$templ_id=intval($val['id_template']);
	}
}


$ar_types=MCPeriodType::GetList();
$les_str='s';
$type_selected=false;
foreach ($ar_types as $val)
{
	if ($val['LESSON'])
	{
		$les_str=$les_str.$val['ID'].':';
	}
}

$n_ar['week_start']=$ar['week_start'];
$n_ar['week_day']=$ar['week_day'];
$n_ar['clas']=$ar['clas'];
$n_ar['IBLOCK_ID']=$ar['IBLOCK_ID'];
$n_ar['CLASSES_IBLOCK_ID']=$ar['CLASSES_IBLOCK_ID'];
$n_ar['CABINETS_IBLOCK_ID']=$ar['CABINETS_IBLOCK_ID'];
$ar=$schedule[$ar['employee']]['schedule'][$ar['week_start']][$ar['week_day']];
$n_ar['site']=$siteID;
$templ=MCSchedule::GetTemplateList(array('SITE_ID'=>$siteID));
$par=json_encode($n_ar);
$par=str_replace ('"', '`', $par);
?>
<div id=content_edit_form>
<div id="periods_block">
<div>
	<?
	if ((count($schedule)==0) and (count($fr_time)==0))
	{
		$empty_day=true;
		$sel_disp="block";
		$lab_disp="none";
		
	}
	else
	{
		$empty_day=false;
		$sel_disp="none";
		$lab_disp="block";
	}
	?>
	<div id="change_templ_sel" style="display:<?=$sel_disp?>">
		<select id="templ_sel" name="templ_sel" style="width:80%" onChange="javascript:templ_change()">
		<?
		foreach ($templ as $key=>$val)
		{
			if (!$templ_id) $templ_id=$val['ID'];
			?>
				<option <?if ($templ_id==$val['ID']) {echo selected; $teml_name=$val['NAME'];}?> value="<?=$val['ID']?>"><?=$val['NAME']?></option>
			<?}
		?>
		</select>
	</div>
	
	<div id="change_templ_label" style="display:<?=$lab_disp?>">
		<?=GetMessage('T_SC_RINGS')?> <?=$teml_name?>
	</div>
	
	<div id="change_templ" class="daytemplaccept" style="margin-top:8px; margin-right:21px; display:none">
		<a href="javascript:if(confirm('<?=GetMessage('T_PERIOD_TEMPLATE_CHANGE_ALERT')?>')) day_template_change('<?=$n_ar['week_start']?>',<?=$n_ar['week_day']?>,<?=$n_ar['clas']?>,'<?=$par?>')"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/confirm.png">
		<?=GetMessage('T_SC_ACCEPT')?></a>
	</div>
	
	<?if (!$empty_day){?>
	<div id="show_change_templ" class="daytemplaccept" style="margin-top:8px; margin-right:21px;">
		<a href="javascript:show_changetempl()"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/pencil.png">
		<?=GetMessage('T_SC_CHANGE')?></a>
	</div>
	<?}?>

</div>
<br>
<?
$sel1='font-weight:bold; background-color:#F5F5F5';
$sel2='</b>';
$selected_prtiod=0;
if ($empty_day)
{
?>
<div id="np" class="period_rec" style="margin:0px"><?=GetMessage('T_NO_PERIODS')?></div>
<?
}
foreach ($schedule as $key=>$value)
{
	if  (is_numeric($key))
	{
		?>
		<div id="period_<?=$key?>" class="period_rec" style="<?=$sel1?>"> 
			<?
			$n_ar['services']=$value['service_id']['ID'];
			$n_ar['id_cabinet']=$value['id_cabinet']['ID'];
			$n_ar['id_period']=$value['id_period'];
			$n_ar['id_template']=$value['id_template'];
			$n_ar['period_number']=$value['period_number'];
			$n_ar['id_employee']=$value['id_employee']['ID'];
			$n_ar['site'] = $siteID;
			$par=json_encode($n_ar);
			$par=str_replace ('"', '`', $par)
			?>
			<a href="javascript:showPeriod(<?=$key?>)"><? echo ($value['period_number'].' - '.$value['service_id']['NAME'])?></a> 
			
			<div id="delete_period_<?=$key?>"class="period_edit">
				<a href="javascript:if(confirm('<?=GetMessage('T_PERIOD_DELETE_ALERT')?>')) deletePeriod(<?=$key?>,'<?=$par?>')"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/delete.png"></a>
			</div>
		</div>
		<?
		if ($sel1<>'')
		{
			$selected_prtiod=$key;
		}
		$sel1='';
		$sel2='';
	}
}

foreach ($fr_time as $key=>$value)
{
	if  (is_numeric($key))
	{
		?>
		<div id="period_<?="fr_".$key?>" class="period_rec" style="<?=$sel1?>"> 
			<?
			$n_ar['services']=$value['service_id']['ID'];
			$n_ar['id_cabinet']=$value['id_cabinet']['ID'];
			$n_ar['id_period']=$value['id_period'];
			$n_ar['id_template']=$value['id_template'];
			$n_ar['period_number']=$value['period_number'];
			$n_ar['id_employee']=$value['id_employee']['ID'];
			$n_ar['site']=$siteID;
			$par=json_encode($n_ar);
			$par=str_replace ('"', '`', $par)
			?>
			<a href="javascript:showPeriod('<?="fr_".$key?>')"><? echo ($value['service_id']['NAME'])?></a> 
			
			<div id="delete_period_<?=$key?>"class="period_edit">
				<a href="javascript:if(confirm('<?=GetMessage('T_PERIOD_DELETE_ALERT')?>')) deletePeriod(<?=$key?>,'<?=$par?>')"><img src="/bitrix/images/bitrix.schoolschedule/calendar/event_calendar/delete.png"></a>
			</div>
		</div>
		<?
		if ($sel1<>'')
		{
			$selected_prtiod1=$key;
		}
		$sel1='';
		$sel2='';
	}
}

?>
<br>
<div style="text-align:center" id="add_period_link">
	<input type="button" onClick="javascript:show_add_period()" value="<?=GetMessage('T_ADD_PERIOD')?>">
</div>
</div>
<div id="description_block">
<div id="descr">
<?
$j_arr_of_starts='[ ';
$j_arr_of_ends='[ ';
$period_shown=false;
if ($empty_day)
{
	echo '<div style="padding:10px;">',GetMessage('T_INSTR'),'</div>';
}
	foreach ($schedule as $key=>$value)
	{
		if  (is_numeric($key)){
			$j_arr_of_starts=$j_arr_of_starts.'"'.$value['time_start'].'",';
			$j_arr_of_ends=$j_arr_of_ends.'"'.$value['time_end'].'",';
			if ($key==$selected_prtiod)
			{
				$tmp_cl="period_info";
				$period_shown=true;
			}
			else 
			{
				$tmp_cl="period_info_hiden";
			}
			?>
			
			
			<div class="<?=$tmp_cl?>" id="period_info_<?=$key?>"> 
			<? if ($val['lesson']=0){?>
				<div class="param"><b><?=GetMessage('T_PERIOD_TIME')?></b> - <?=$value['FORMATTED_TIME_START']?> - <?=$value['FORMATTED_TIME_END']?></div>
			<?} else {?>
				<div class="param"><b><?=GetMessage('T_PERIOD_TIME')?></b> - <?=$value['FORMATTED_LESSON_START']?> - <?=$value['FORMATTED_LESSON_END']?></div>
			<?}?>
			<div class="param"><b><?=GetMessage('T_PERIOD_TYPE')?></b> - <?echo $value['type_name']?></div>
			<div class="param"><b><?=GetMessage('T_PERIOD_SECTOR')?></b> - <?echo $value['id_class']['NAME']?></div>
			<div class="param"><b><?=GetMessage('T_TEACHER')?></b> - <?echo $value['id_employee']['NAME']?></div>
			<div class="param"><b><?=GetMessage('T_PERIOD_BUILDING')?></b> - <?echo $value['id_cabinet']['NAME']?></div>
			<div class="param"><b><?=GetMessage('T_PERIOD_SRVICES')?></b> - <?echo $value['service_id']['NAME']?></div>
			<div class="param"><b>Комментарий</b> - <?echo $value['comment']?></div>
			<? if ($value['cancled']){?>
				<div class="param"><?=GetMessage('T_CANCLED_PERIOD')?></div>
			<?}?>
			<div>
				<input type="button" onClick="javascript:show_update_period('<?=$value['week_start']?>',
					'<?=$value['week_day']?>',
					<?=$value['id_class']['ID']?>,
					<?=$value['id_employee']['ID']?>,
					<?=$value['id_cabinet']['ID']?>,
					<?=$value['id_period']?>,
					<?=$value['service_id']['ID']?>,
					<?=$value['id_type']?>,
					<?=$value['id_template']?>,
					<?=$value['period_number']?>,'','','<?=$les_str?>',
					<?=$value['cancled']?>,'<?echo $value['comment']?>')"
					value="<?=GetMessage('T_PERIOD_UPDATE')?>">
			</div>
			</div>
			<?
		}
	}
	
	foreach ($fr_time as $key=>$value)
	{ 
		if  (is_numeric($key)){
			$j_arr_of_starts=$j_arr_of_starts.'"'.$value['time_start'].'",';
			$j_arr_of_ends=$j_arr_of_ends.'"'.$value['time_end'].'",';
			if (!$period_shown)
			{
				if ($key==$selected_prtiod1)
				{
					$tmp_cl="period_info";
				}
				else 
				{
					$tmp_cl="period_info_hiden";
				}
			}
			else
			{
				$tmp_cl="period_info_hiden";
			}
			
			
			?>
			
			<div class="<?=$tmp_cl?>" id="period_info_<?="fr_".$key?>"> 
				<? if ($val['lesson']=0){?>
					<div class="param"><b><?=GetMessage('T_PERIOD_START')?></b> - <?echo $value['time_start']?></div>
					<div class="param"><b><?=GetMessage('T_PERIOD_END')?></b> - <?echo $value['time_end']?></div>
				<?} else {?>
					<div class="param"><b><?=GetMessage('T_PERIOD_START')?></b> - <?echo $value['time_start']?></div>
					<div class="param"><b><?=GetMessage('T_PERIOD_END')?></b> - <?echo $value['time_end']?></div>
				<?}?>
				<div class="param"><b><?=GetMessage('T_PERIOD_TYPE')?></b> - <?echo $value['type_name']?></div>
				<div class="param"><b><?=GetMessage('T_PERIOD_SECTOR')?></b> - <?echo $value['id_class']['NAME']?></div>
				<div class="param"><b><?=GetMessage('T_TEACHER')?></b> - <?echo $value['id_employee']['NAME']?></div>
				<div class="param"><b><?=GetMessage('T_PERIOD_BUILDING')?></b> - <?echo $value['id_cabinet']['NAME']?></div>
				<div class="param"><b><?=GetMessage('T_PERIOD_SRVICES')?></b> - <?echo $value['service_id']['NAME']?></div>
				<div class="param"><b>Комментарий</b> - <?echo $value['comment']?></div>
				<? if ($value['cancled']){?>
					<div class="param"><?=GetMessage('T_CANCLED_PERIOD')?></div>
				<?}?>


				<div>
					<input type="button" onClick="javascript:show_update_period('<?=$value['week_start']?>',
						'<?=$value['week_day']?>',
						<?=$value['id_class']['ID']?>,
						<?=$value['id_employee']['ID']?>,
						<?=$value['id_cabinet']['ID']?>,
						<?=$value['id_period']?>,
						<?=$value['service_id']['ID']?>,
						<?=$value['id_type']?>,
						<?=$value['id_template']?>,
						<?=$value['period_number']?>,'<?=$value['time_start']?>','<?=$value['time_end']?>','<?=$les_str?>',
						<?=$value['cancled']?>,'<?echo $value['comment']?>')"
						value="<?=GetMessage('T_PERIOD_UPDATE')?>">
				</div>
			</div>
			<?
		}
	}
	$j_arr_of_starts=substr($j_arr_of_starts,0,-1).']';
	$j_arr_of_ends=substr($j_arr_of_ends,0,-1).']';
	
?>


</div>
<div id="add_period_block">
<div id="edit_period_form">
	<div id="edit_period_block">
		<b><?=GetMessage('T_PERIOD_SRVICES')?>:</b><br><div class="field_separator"></div>
		<div id="add_period_services">
			<select id="add_period_services_block" name="add_period_services">
				<?
				$arSelect = Array("ID", "NAME", 'IBLOCK_SECTION_ID');
				$arFilter = Array("IBLOCK_ID"=>$n_ar['IBLOCK_ID'], "ACTIVE"=>"Y");
				$sect_res = CIBlockElement::GetList(Array('NAME'=>'ASC'), $arFilter, false, false, $arSelect);
				$arLessonSections = array();
				$arLessons = array();
				while($sect_ob = $sect_res->GetNext()){
					if (empty($sect_ob['IBLOCK_SECTION_ID']))
						$arLessons[$sect_ob['ID']] = $sect_ob;
					elseif (!array_key_exists($sect_ob['IBLOCK_SECTION_ID'],$arLessonSections)) {
						$rsSection = CIBlockSection::GetList(array(),array('IBLOCK_ID'=>$n_ar['IBLOCK_ID'],'ID'=>$sect_ob['IBLOCK_SECTION_ID']),false,array('NAME','SORT'));
						$arSection = $rsSection->GetNext();
						$arLessonSections[$sect_ob['IBLOCK_SECTION_ID']] = array(
							'NAME'=> $arSection['NAME'],
							'SORT'=> $arSection['SORT'],
							'VALUES' => array($sect_ob['IBLOCK_SECTION_ID']=>$sect_ob)
						);
					}
					else {
						$arLessonSections[$sect_ob['IBLOCK_SECTION_ID']]['VALUES'][$sect_ob['ID']] = $sect_ob;
					}
				}
				function MCSortSections($a, $b){
				    if ($a['SORT'] == $b['SORT'])
						return 0;
					return ($a['SORT'] < $b['SORT']) ? -1 : 1;
				}
				usort($arLessonSections, "MCSortSections");
				?>
				<?if (!empty($arLessonSections)):?>
					<?foreach ($arLessonSections as $arLessonSection):?>
						<optgroup label="<?=$arLessonSection['NAME']?>">
							<?foreach ($arLessonSection['VALUES'] as $arLesson):?>
								<option value="<?=$arLesson['ID']?>"><?=$arLesson['NAME']?></option>
							<?endforeach;?>
						</optgroup>
					<?endforeach;?>
				<?endif;?>
				<?foreach ($arLessons as $arLesson):?>
					<option value="<?=$arLesson['ID']?>"><?=$arLesson['NAME']?></option>
				<?endforeach;?>
			</select>
		</div><br>
		<b><?=GetMessage('T_PERIOD_TYPE')?>:</b><br><div class="field_separator"></div>

		<select id="add_sel_period_type" name="add_sel_period_type" class="add_select" onChange="javascript:change_period_type('<?=$les_str?>')">
		<?
			$les_str='s';
			$type_selected=false;
			foreach ($ar_types as $val)
			{
				if ($val['LESSON'])
				{
					$les_str=$les_str.$val['ID'].':';
				}
				if (!$type_selected)
				{
					$type_selected=$val['ID'];
					if ($val['LESSON'])
					{
						$les=true;
					}
					else
					{
						$les=false;
					}
				}
		?>
					<option <?if ($type_selected==$val['ID']) echo 'selected';?> value="<?=$val['ID']?>"><?=$val['NAME']?></option>
		<?
			}
		?>
		</select><br><br>
	
		
		<b><?=GetMessage('T_PERIOD_TIME')?>:</b> <i><?if (!$les) echo GetMessage('T_PERIOD_TIME_FORMAT')?></i> <br><div class="field_separator"></div>
		
		<?
		if ($les)
		{
			$free_time_style="display:none";
			$lesson_time_style="display:block";
		}
		else
		{
			$free_time_style="display:block";
			$lesson_time_style="display:none";
		}
		?>
		<div style="<?=$free_time_style?>" id="free_time_block">
		
		
		<?$APPLICATION->IncludeComponent("bitrix:main.clock","high_z",Array(
								"INPUT_ID" => "add_time_from", 
								"INPUT_NAME" => "add_time_from", 
								"INIT_TIME" => "", 
								"zIndex"=>'2000',
								"STEP" => "0",
								)
							);
				?>
		 -
		 <?$APPLICATION->IncludeComponent("bitrix:main.clock","high_z",Array(
								"INPUT_ID" => "add_time_to", 
								"INPUT_NAME" => "add_time_to", 
								"INIT_TIME" => "", 
								"zIndex"=>'2000',
								"STEP" => "0",
								)
							);
				?>
		</div>

		<div style="<?=$lesson_time_style?>" id="lesson_time_block">
			<select id="lesson_time" name="lesson_time"  multiple="multiple">
			<?
			foreach ($templ[$templ_id]['lessons'] as $key=>$val)
			{			
			?>
				<option value="<?=$key?>"><?=$key.'. '.$val['START'].' - '.$val['END']?></option>
			<?}?>
			</select>
		</div>
		<br>
		
		<b><?=GetMessage('T_TEACHER')?>:</b><br><div class="field_separator"></div>
		<?
		$GroupId = COption::GetOptionString('bitrix.schoolschedule', 'TEACHER_GROUP', '');

		$filter = Array
			(
				"ACTIVE"              => "Y",
				"GROUPS_ID"           => Array($GroupId)
			);
			$rsUsers = CUser::GetList(($by="last_name"), ($order="ASC"), $filter); // выбираем пользователей
			
		?>
		<select id="teacher_selector" name="teacher_selector" class="add_select">
			<?
			while($arUsers=$rsUsers->GetNext()){
			?>
				<option value="<?=$arUsers['ID']?>"><?=empty($arUsers['LAST_NAME'])?$arUsers['LOGIN']:$arUsers['LAST_NAME'].' '.$arUsers['NAME'].' '.$arUsers['SECOND_NAME']?></option>
			<?}?>
		</select><br><br>
		
		
		<b><?=GetMessage('T_PERIOD_BUILDING')?>:</b><br><div class="field_separator"></div>
		<select id="t_cabinet" name="t_cabinet" class="add_select">
			<?
			$arSelect = Array("ID", "NAME");
			$arFilter = Array("IBLOCK_ID"=>$n_ar['CABINETS_IBLOCK_ID'], "ACTIVE"=>"Y");
			$sect_res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			
			while($sect_ob = $sect_res->GetNext())
			{
			?>
				<option value="<?=$sect_ob['ID']?>"><?=$sect_ob['NAME']?></option>
			<?
			}
			?>
		</select><br><br>
<b>Комментарий:</b><br><div class="field_separator"></div>
		<input id="t_comment" name="t_comment" class="add_select" type="text" value="" />
		<br><br>
		<b><?=GetMessage('T_CANCLE_PERIOD')?>:</b>	<input id="cancle_input" type="checkbox">
	</div>
	<?
	?>
	<div class="center_buttons">
	<?
		$n_ar['delete']=0;
		$par=json_encode($n_ar);
		$par=str_replace ('"', '`', $par);
		$par='"'.$par.'"';
	?>
	
	<input id="id_empl" type="hidden">
	<input id="id_cab" type="hidden">
	<input id="id_per" type="hidden">
	<input id="id_serv" type="hidden">
	<input id="id_templ" type="hidden">
	<input id="per_num" type="hidden">
	
	
	
	<input id="add_button" type="button" value="<?=GetMessage('T_SAVE')?>" name="add_new_period" onclick='javascript:add_period("<?=$les_str?>",<?=$par?>)'>
	
	<?
		$n_ar['delete']=1;
		$par=json_encode($n_ar);
		$par=str_replace ('"', '`', $par);
		$par='"'.$par.'"';
	?>
	<input id="update_button" type="button" value="<?=GetMessage('T_SAVE')?>" name="add_new_period" onclick='javascript:add_period("<?=$les_str?>",<?=$par?>)'>
	<div style="float: right; width: 10px">&nbsp;</div>
	<input id="close_button" type="button" value="<?=GetMessage('T_CLOSE')?>" name="closse_add_new_period" onclick='javascript:close_add_period()'>
	<div id="test_block"></div>
	</div>
</div>	
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