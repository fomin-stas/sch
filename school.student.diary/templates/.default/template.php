<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="logs">
<?if(count($arResult["SELECT_DATES"])):?>
 <select class="logsFilter" onChange="datesChange(this.value)">
  <option value=""><?=GetMessage("SC_DIARY_SELECT_DATES")?></option>
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
<?foreach($arResult["ITEMS"] as $key=> $arDay):?>  
  <h3 class="alt"><?echo $arDay["NAME"] . ', ' . strtolower($arDay["DISPLAY_ACTIVE_FROM"])?></h3>
  <div class="logsContainer">
  <table cellpadding="0" cellspacing="0" class="logsTable diaryLogs">
    <colgroup span="1" />
    <colgroup span="1" />
    <colgroup span="1" />
    <?foreach($arDay["MARKS_TYPES"] as $mark_type):?>
      <colgroup span="1" />
    <?endforeach?>  
    <colgroup span="1" /> 
    <colgroup span="1" />
    <colgroup span="1" />
    <thead>
        <tr>
            <td rowspan="2"></td>
            <td rowspan="2"><?=GetMessage("SC_DIARY_LESSON")?></td>
            <td rowspan="2"><?=GetMessage("SC_DIARY_HOME_WORK")?></td>
            <td colspan="<?=count($arDay["MARKS_TYPES"])?>"><?=GetMessage("SC_DIARY_MARKS")?></td>
            <td rowspan="2"><?=GetMessage("SC_DIARY_COMMENTS")?></td>
        </tr>
        <tr>
          <?foreach($arDay["MARKS_TYPES"] as $mark_type):?>
            <td><span title="<?=$mark_type["FULL"]?>"><?=strtolower($mark_type["SHORT"])?></span></td>
          <?endforeach?>
        </tr>
    </thead>
    <tbody>  
  <?foreach($arDay["LESSONS"] as $lesson_key => $arItem):
    if(empty($arItem['NAME'])) continue;
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>  
    <tr <?=$lesson_key%2? '':'class="even"'?> id="<?=$this->GetEditAreaId($arItem['ID']);?>">
      <td class="time"><?=$arItem["TIME"]?></td>
      <td class="class">
		  <?if (isset($arItem["DETAIL_PAGE_URL"])):?>
		  <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
		  <?else:?>
			  <?echo $arItem["NAME"]?>
		  <?endif?>
	  </td>
      <td class="homeTask">
        <?=htmlspecialchars_decode($arItem["PROPERTIES"]["HOME_WORK"]["VALUE"])?>
        <?if(is_array($arItem["PROPERTIES"]["HOME_WORK_FILES"]["VALUE"]) && count($arItem["PROPERTIES"]["HOME_WORK_FILES"]["VALUE"])):?>
         <div class="homework_files"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("SC_DIARY_HOME_WORK_FILES")?></a> (<?=count($arItem["PROPERTIES"]["HOME_WORK_FILES"]["VALUE"]);?>)</div>
        <?endif?>
      </td>
    <?
      
      foreach($arItem["PROPERTIES"]["MARKS"]['VALUE'] as $mark){
      ?>
      <td class="marks" style="background: <?=$mark["COLOR"]?>">
        <?if(!empty($mark["MARK"])):?>
          <span class="num" title="<?=$mark["FULL"]?>"><?=$mark["MARK"]?></span>
        <?endif?>  
      </td>          
      <?      
      }
      if(empty($arItem["PROPERTIES"]["MARKS"]['VALUE'])):
          ?><td class="marks">&nbsp;</td><? 
      endif;
    ?>      
      <td class="mention">
        <?      
          foreach($arItem["PROPERTIES"]["STUDENT_WORK_COMMENT"]["VALUE"] as $comment){
            
            if(!empty($comment["COMMENT"])){
              echo $comment["COMMENT"].'<br />';
            }
          }
       ?>      
      </td>
    </tr>    
  <?endforeach;?>
      </tbody>
    </table>
  </div>
<?endforeach;?> 
</div>