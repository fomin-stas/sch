<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"])):?>
  <div class='itemRow'>
   <?$index=0?>
   <?foreach($arResult["ITEMS"] as $arClass):?>
    <?if($index == 3):?></div><div class='itemRow'><?$index=0?><?endif?>
    <div class="item">
     <b><?=$arClass["NAME"]?></b>
     <ul class="subjects-list">
      <?foreach($arClass["ITEMS"] as $arSubject):?>
       <li><a href="<?=$arSubject["DETAIL_PAGE_URL"]?>"><?=$arSubject["NAME"]?></a></li>
      <?endforeach?>
     </ul>
    </div>
    <?$index++?>
   <?endforeach?>
  </tr>
 </div>
<?endif?>