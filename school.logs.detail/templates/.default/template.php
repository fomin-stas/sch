<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="logs">
 <h3><?=$arResult["SUBJECT"]["NAME"]?>, <?=$arResult["SECTION"]["NAME"]?></h3>
 <?if(count($arResult["SELECT_DATES"])):?>
  <select class="logsFilter" onChange="datesChange(this.value)">
   <option value=""><?=GetMessage("SCHOOL_LOG_SELECT_DATES")?></option>
   <?foreach($arResult["SELECT_DATES"] as $arPeriod):?>
    <option value="<?=$arPeriod["HREF"]?>"<?/*if($arPeriod["SELECTED"] == "Y"):?> selected<?endif*/?>><?=$arPeriod["TITLE"]?></option>
   <?endforeach?>
  </select>
 <?endif?>
 <ul class="logsSwitch">
  <li class="prev navi"><a href="<?=$arResult["WEEK_BACK"]?>"></a></li>
  <li><?=$arResult["WEEK_PERIOD_TITLE"]?></li>
  <li class="next navi"><a href="<?=$arResult["WEEK_FORWARD"]?>"></a></li>
 </ul>  
<?
if(count($arResult["LESSONS"])>0)
{
?>
 <div class="logsContainer">
  <table cellpadding="0" cellspacing="0" class="logsTable">
   <col class="first" />
   <col />
   <?foreach($arResult["LESSONS"] as $arLesson):?>
    <colgroup span="<?=count($arLesson["MARK_TYPES"])?>" />
   <?endforeach?>
   <thead>
    <tr>
     <th rowspan="2"><?=GetMessage("SCHOOL_LOG_NUMBER")?></th>
     <th rowspan="2"><?=GetMessage("SCHOOL_LOG_STUDENTS")?></th>
     <?foreach($arResult["LESSONS"] as $arLesson):?>
      <th><a href="<?=$arLesson["DETAIL_PAGE_URL"]?>"><?=$arLesson["DISPLAY_ACTIVE_FROM"]?></a></th>
     <?endforeach?>
    </tr>
    <tr>
		<?foreach($arResult["LESSONS"] as $arLesson):?>
			<td><?=$arLesson['START']?> - <?=$arLesson['END']?></td>
		<?endforeach?>
    </tr>
   </thead>
   <tbody>
    <?foreach($arResult["ITEMS"] as $index=>$arStudent):?>
     <tr<?if($index%2):?> class="even"<?endif?>>
      <td><?=($index+1)?></td>
      <td class="names"><?=empty($arStudent["LAST_NAME"]) && empty($arStudent["NAME"])?$arStudent["LOGIN"]:$arStudent["LAST_NAME"].' '.$arStudent["NAME"]?></td>
      <?foreach($arStudent["LESSONS"] as $arLesson):?>
        <td title="<?=$arResult["MARK_TYPES"][$mark_type]["FULL"]?>">
			<?foreach($arLesson["MARKS"] as $mark_type=>$mark):?>
				<?if (empty($mark)) continue;?>
				<span<?if($arResult["MARK_COLORS"][$mark]):?> style="background-color: <?=$arResult["MARK_COLORS"][$mark]?>"<?endif?>>
					<?=$mark?>
				</span>
				<br>
	       <?endforeach?>
		</td>
      <?endforeach?>
     </tr>
    <?endforeach?>
   </tbody>
  </table>
 </div>
</div>
<?
}
else
{
	?><p><?=GetMessage("NO_LESSONS")?></p><?
}
?>