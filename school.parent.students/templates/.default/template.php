<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section-list">
<ul>
<?
foreach($arResult["CHILDRENS"] as $arChildren):
?>
	<li id="<?=$this->GetEditAreaId($arChildren['ID']);?>"><a href="<?=$arChildren["DETAIL_PAGE_URL"]?>"><?=$arChildren["LAST_NAME"].' '.$arChildren["NAME"]?></a></li>
<?endforeach?>
</ul>
</div>
