<?
//коммент для сохранения кодировки
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$tmp_str=str_replace ("\\", '/',__FILE__);
$work_path=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
require_once($_SERVER["DOCUMENT_ROOT"].$work_path.'/lang/'.LANGUAGE_ID.'/copy_week.php');
CModule::IncludeModule("bitrix.schoolschedule");
echo '<label for="weeks">'.GetMessage('T_COPY_FROM').'</label>';

if (isset ($_POST['site'])) {
	$siteID = trim($_POST['site']);
	$rsSite = CSite::GetByID($siteID);
	if (!$rsSite->GetNext()) {
		$siteID = SITE_ID;
	}
} else {
	$siteID = SITE_ID;
}

$ar=MCWeek::GetList(array('CLASS'=>$_POST['empl'], 'SITE_ID'=>$siteID));

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
$test=$_POST['week'];
?>


<div id="select_reload">
	<select style="display:none;" id="add_sel_period_type" name="add_sel_period_type" class="add_select">
		<?
			foreach ($ar as $val)
			{
				$w_start=strtotime($val['week_start'])+7*24*60*60;
				$w_end=strtotime($val['week_end'])+7*24*60*60;
				
		?>
				<option <?if  ($val['week_start']==date('d.m.Y',$_POST['week'])) echo "selected"?> value="<?=$val['week_start']?>">
				<?=date('d',strtotime($val['week_start'])).' '
				.$month[intval(date('m',strtotime($val['week_start'])))].' - '
				.date('d',strtotime($val['week_end'])).' '
				.$month[intval(date('m',strtotime($val['week_end'])))].' '
				.date('Y',strtotime($val['week_end']))
				?></option>
		<?
		if  ($val['week_start']==date('d.m.Y',$_POST['week'])) 
		{
			$dat_str=date('d',strtotime($val['week_start'])).' '
				.$month[intval(date('m',strtotime($val['week_start'])))].' - '
				.date('d',strtotime($val['week_end'])).' '
				.$month[intval(date('m',strtotime($val['week_end'])))].' '
				.date('Y',strtotime($val['week_end']));
		};
			}
		?>
	</select>
	<?echo $dat_str?>
</div>
<?
$tmp_str=str_replace ("\\", '/',__FILE__);
$dir=substr(dirname($tmp_str), strrpos($tmp_str, '/bitrix/'));
$dir=substr($dir, 0, -6);
$week_selector = $_SERVER["DOCUMENT_ROOT"].$dir.'/actions/week_selector.php' ;
echo '<br><label for="weeks">'.GetMessage('T_COPY_TO').'</label>';
require_once $week_selector;
?>
<div class="checks">
	<div class="item">
		<input id="check_2" name="copy_interval" type="radio" value="N"  checked="checked" />
		<label for="check_2"><?=GetMessage('T_ALL_WEEKS')?></label>
		<div class="clear"></div>
	</div><br>
	<div class="item">
		<input id="check_1" name="copy_interval" type="radio" value="Y"/>
		<label for="check_1"><?=GetMessage('T_N_ALL_WEEKS')?></label>
		<div class="clear"></div>
	</div>
</div>

<div class="repeats">
	<label for="repeat"><?=GetMessage('T_REPEAT')?></label>
	<input name="repeat" id="repeat_inp" type="text" value="1" />
	<span><?=GetMessage('T_COUNT')?></span>
	<div class="clear"></div>
</div>

<input id="empl_val" type="hidden" value="<?=intval($_POST['empl'])?>">

<div id="status_div"></div>

<div id="error_message1">
<div style="height: 5px"></div>
<div class="icon-error"></div>
<div id="er_message" style="padding:3px 0 0 40px;">asdasd</div><div class="clear"></div>
<div class="center_buttons">

	<input type="button" value="<?=GetMessage('T_CLOSE')?>" name="close_er" onclick="javascript:close_er1()">
	
</div>

</div>
