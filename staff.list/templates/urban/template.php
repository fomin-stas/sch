<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="box_collectiv">
    <ul>
<?
    $arDeptsChain = array();
    $arCurrentDepth = array();
    
    foreach ($arResult['DEPARTMENTS'] as $arDept) {
        if($arDept['CODE'] == 'workers') continue;
        $arDeptsChain[$arDept['DEPTH_LEVEL']] = '<a href="'.$arParams['STRUCTURE_PAGE'].'?set_filter_'.$arParams['STRUCTURE_FILTER'].'=Y&'.$arParams['STRUCTURE_FILTER'].'_UF_DEPARTMENT='.$arDept['ID'].'">'.htmlspecialchars($arDept['NAME']).'</a>';
    
        if (count($arDept['USERS']) > 0) {
?>
    <?if($arDept['CODE'] == 'admin'):?>
    <li>
        <?$arResize = CFile::ResizeImageGet($arResult['dir']['PERSONAL_PHOTO'], array('width' => 160, 'height' => 134), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
        <img src="<?=$arResize['src']?>" width="<?=$arResize['width']?>" height="<?=$arResize['height']?>" alt="<?=$arResult['dir']['LAST_NAME']?> <?=$arResult['dir']['NAME']?> <?=$arResult['dir']['SECOND_NAME']?>">
        <div class="collectiv_category"><?=htmlspecialchars($arDept['NAME'])?></div>
        <div class="collectiv_name"><?=$arResult['dir']['LAST_NAME']?> <?=$arResult['dir']['NAME']?> <?=$arResult['dir']['SECOND_NAME']?></div>
        <div class="collectiv_post"><?=$arResult['dir']['WORK_POSITION']?></div>
        <div class="collectiv_mail"><a href="mailto:<?=$arResult['dir']['EMAIL']?>?subject=feedback" "email me"><?=$arResult['dir']['EMAIL']?></a></div>
    </li>
    
    <?foreach ($arDept['USERS'] as $arUser):?>
        <?if(($arUser['UF_IS_DIR']) || ($arUser['NAME'] == '')) continue;?>
        <li>
            <?if (strlen($arUser['PERSONAL_PHOTO']) > 0) {?>
            <?$arResize = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width' => 160, 'height' => 134), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
            <img src="<?=$arResize['src']?>" width="<?=$arResize['width']?>" height="<?=$arResize['height']?>" alt="<?=$arUser['LAST_NAME']?> <?=$arUser['NAME']?> <?=$arUser['SECOND_NAME']?>">
            <?} else {?>
            <img src="<?=SITE_TEMPLATE_PATH?>/images/collectiv_img_no_<?=($arUser["PERSONAL_GENDER"] == "F" ? "women" : "men")?>.jpg">
            <?}?>
            <div class="collectiv_category"><?=htmlspecialchars($arDept['NAME'])?></div>
            <div class="collectiv_name"><?=$arUser['LAST_NAME']?> <?=$arUser['NAME']?> <?=$arUser['SECOND_NAME']?></div>
            <div class="collectiv_post"><?=$arUser['WORK_POSITION']?></div>
            <div class="collectiv_mail"><a href="mailto:<?=$arUser['EMAIL']?>?subject=feedback" "email me"><?=$arUser['EMAIL']?></a></div>
        </li>
    <?endforeach;?>
    <?else:?>
<?foreach ($arDept['USERS'] as $arUser):?>
    <li>
        <?if (strlen($arUser['PERSONAL_PHOTO']) > 0) {?>
        <?$arResize = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width' => 160, 'height' => 134), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
        <img src="<?=$arResize['src']?>" width="<?=$arResize['width']?>" height="<?=$arResize['height']?>" alt="<?=$arUser['LAST_NAME']?> <?=$arUser['NAME']?> <?=$arUser['SECOND_NAME']?>">
        <?} else {?>
        <img src="<?=SITE_TEMPLATE_PATH?>/images/collectiv_img_no_<?=($arUser["PERSONAL_GENDER"] == "F" ? "women" : "men")?>.jpg">
        <?}?>
        <div class="collectiv_category"><?=htmlspecialchars($arDept['NAME'])?></div>
        <div class="collectiv_name"><?=$arUser['LAST_NAME']?> <?=$arUser['NAME']?> <?=$arUser['SECOND_NAME']?></div>
        <div class="collectiv_post"><?=$arUser['WORK_POSITION']?></div>
        <div class="collectiv_mail"><a href="mailto:<?=$arUser['EMAIL']?>?subject=feedback" "email me"><?=$arUser['EMAIL']?></a></div>
    </li>
<?endforeach;?>
    <?endif;?>
<?
        }
    
    }
?>
    </ul>
</div>