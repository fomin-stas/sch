<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["SECTION_URL"]=trim($arParams["SECTION_URL"]);
$arResult["CHILDRENS"]=array();

/*************************************************************************
			Work with cache
*************************************************************************/
if($this->StartResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()).$USER->GetID()))
{
  if($USER->IsAuthorized())
  {
   $rsUser = CUser::GetByID($USER->GetID());
   $arUser = $rsUser->Fetch();

	//Если учитель
	$arGroups = $USER->GetUserGroupArray();
	if(in_array(8,$arGroups)) {
		$rsUsers2 = CUser::GetList(($by="last_name"), ($order="asc"), array("GROUPS_ID"=> Array(9))); 
		while($arItem2 = $rsUsers2->GetNext()) 
			$arResult["CHILDRENS"][] = $arItem2;
	}else

   if(isset($arUser["UF_PARENT_LINK_CODE"]) && is_array($arUser["UF_PARENT_LINK_CODE"]) && count($arUser["UF_PARENT_LINK_CODE"]))
   {
    $arUFilter = Array(
     "UF_STUDENT_LINK_CODE" => $arUser["UF_PARENT_LINK_CODE"],
    );
    $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $arUFilter, Array("SELECT"=>Array("UF_EDU_STRUCTURE")));
    while($arUser = $rsUsers->Fetch())
    {
      if (empty($arUser['LAST_NAME']) && empty($arUser['NAME']) && empty($arUser['SECOND_NAME']))
	$arUser['LAST_NAME'] = $arUser['LOGIN'];
      if($arParams["SECTION_URL"])
        $arUser["DETAIL_PAGE_URL"] = str_replace("#USER_ID#",$arUser["ID"], $arParams["SECTION_URL"]);      
      else  
        $arUser["DETAIL_PAGE_URL"] = $APPLICATION->GetCurPageParam('USER_ID='.$arUser["ID"], array("USER_ID")); ;
      $arResult["CHILDRENS"][] = $arUser;
    }
    
    if(!count($arResult["CHILDRENS"]))
    {
     $this->AbortResultCache();
		   ShowError(GetMessage("CHILDRENS_NOT_FOUND"));
		   return;
    }
   }
   else
   {
    $this->AbortResultCache();
		  ShowError(GetMessage("CHILDRENS_NOT_FOUND"));
		  return;
   }
  }
  else
  {
   $this->AbortResultCache();
		 ShowError(GetMessage("USER_NOT_AUTHORIZED"));
		 return;
  }
  
	$this->IncludeComponentTemplate();
}

if($arResult["SECTIONS_COUNT"] > 0 || isset($arResult["SECTION"]))
{
	if(
		$USER->IsAuthorized()
		&& $APPLICATION->GetShowIncludeAreas()
		&& CModule::IncludeModule("iblock")
	)
	{
		$UrlDeleteSectionButton = "";
		if(isset($arResult["SECTION"]) && $arResult["SECTION"]['IBLOCK_SECTION_ID'] > 0)
		{
			$rsSection = CIBlockSection::GetList(
				array(),
				array("=ID" => $arResult["SECTION"]['IBLOCK_SECTION_ID']),
				false,
				array("SECTION_PAGE_URL")
			);
			$rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
			$arSection = $rsSection->GetNext();
			$UrlDeleteSectionButton = $arSection["SECTION_PAGE_URL"];
		}

		if(empty($UrlDeleteSectionButton))
		{
			$url_template = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "LIST_PAGE_URL");
			$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"]);
			$arIBlock["IBLOCK_CODE"] = $arIBlock["CODE"];
			$UrlDeleteSectionButton = CIBlock::ReplaceDetailURL($url_template, $arIBlock, true, false);
		}

		$arReturnUrl = array(
			"add_section" => (
				strlen($arParams["SECTION_URL"])?
				$arParams["SECTION_URL"]:
				CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_PAGE_URL")
			),
			"add_element" => (
				strlen($arParams["SECTION_URL"])?
				$arParams["SECTION_URL"]:
				CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_PAGE_URL")
			),
			"delete_section" => $UrlDeleteSectionButton,
		);
		$arButtons = CIBlock::GetPanelButtons(
			$arParams["IBLOCK_ID"],
			0,
			$arResult["SECTION"]["ID"],
			array("RETURN_URL" =>  $arReturnUrl)
		);

		$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
	}

	if($arParams["ADD_SECTIONS_CHAIN"] && isset($arResult["SECTION"]) && is_array($arResult["SECTION"]["PATH"]))
	{
		foreach($arResult["SECTION"]["PATH"] as $arPath)
		{
			$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
		}
	}
}
?>