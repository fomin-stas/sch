<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section-list">
<?if(count($arResult["CHILDRENS"]) > 0):?>
<ul>
<?foreach($arResult["CHILDRENS"] as $arChildren):?>
	<li id="<?=$this->GetEditAreaId($arChildren['ID']);?>"><?=$arChildren["LAST_NAME"].' '.$arChildren["NAME"]?></li>
<?endforeach?>
</ul>
<?else:?>
 <?=GetMessage("CHILDRENS_LIST_EMPTY")?>
<?endif?>
<br>
<br>
<h2><?=GetMessage("ADD_NEW_CHILDREN")?></h2>
<?
  if(count($arResult["ERRORS"])){
    ShowError(implode("<br />", $arResult["ERRORS"]));
  }elseif (strlen($arResult["MESSAGE"]) > 0){
    ShowNote($arResult["MESSAGE"]);
  }
?>
<form name="student_add" method="post" enctype="multipart/form-data">
  <?=bitrix_sessid_post()?>
  <?=GetMessage("STUDENT_CODE_TITLE")?><br />
  <input type="text" size="25" value="" name="student_code">&nbsp;&nbsp;
  <input type="submit" name="student_submit" value="<?=GetMessage("STUDENT_FORM_SUBMIT")?>" />
</form>
</div>
