<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"])):?>
	<div class='itemRow'>
	<?
  $i = 0;
	foreach($arResult["ITEMS"] as $sec)
	{
    if($i == 4){  
      echo "</div><div class='itemRow'>";
      $i = 0;
    }
    
    $this->AddEditAction($sec['ID'], $sec['EDIT_LINK'], CIBlock::GetArrayByID($sec["IBLOCK_ID"], "SECTION_EDIT"));
	   $this->AddDeleteAction($sec['ID'], $sec['DELETE_LINK'], CIBlock::GetArrayByID($sec["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
		?>
    <div class="item" id="<?=$this->GetEditAreaId($sec['ID']);?>">
    <b><?=$sec["NAME"]?></b><?
		if(is_array($sec["CLASSES"]))
		{
			?>
      
        <ul class="subjects-list"><?
          foreach($sec["CLASSES"] as $c)
          {
            $this->AddEditAction($c['ID'], $c['EDIT_LINK'], CIBlock::GetArrayByID($c["IBLOCK_ID"], "ELEMENT_EDIT"));
		          $this->AddDeleteAction($c['ID'], $c['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
            ?><li id="<?=$this->GetEditAreaId($c['ID']);?>"><a href="<?=$c["DETAIL_URL"]?>"><?=$c["NAME"]?></a></li><?
          }
      ?>
        </ul>      
      <?
		}
		?></div><?
	}
	?>
	</div>
<?endif?>