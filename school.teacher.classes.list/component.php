<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
 	$arParams["IBLOCK_TYPE"] = "school";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);


$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);

if(!$USER->IsAuthorized())
 return ShowError(GetMessage("SCHOOL_USER_NOT_AUTHORIZED"));

if($this->StartResultCache(false, array($arParams, $USER->GetID())))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(!CModule::IncludeModule("bitrix.schoolschedule"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("SCHOOL_MODULE_NOT_INSTALLED"));
		return;
	}
 
 $arClasses = Array();
 $arClassSection = array();
 
 if(!$USER->IsAdmin())
 {
  $rsUser = CUser::GetByID($USER->GetID());
  $arUser = $rsUser->Fetch();
  if(isset($arUser["UF_CLASS_SUBJECT"]) && is_array($arUser["UF_CLASS_SUBJECT"]) && count($arUser["UF_CLASS_SUBJECT"]))
  {
   $classUniqueIds = array();
   foreach($arUser["UF_CLASS_SUBJECT"] as $val)
   {
    $val = unserialize($val);
    $classID = intval($val["CLASS"]);
    if($classID>0 && !in_array($classID,$classUniqueIds))
    {
     $classUniqueIds[] = $classID;
     $cRes = CIBlockElement::GetByID($classID);
     if($cRes = $cRes->Fetch())
     {
      $classSection = intval($cRes["IBLOCK_SECTION_ID"]);
      if(!in_array($classSection,$arClassSection))
      {
       $arClassSection[] = $classSection;
      }
      
      $cRes["DETAIL_URL"] = $arParams["DETAIL_URL"];
      $cRes["DETAIL_URL"] = str_replace("#CLASS_ID#",$cRes["ID"],$cRes["DETAIL_URL"]);
      
      $arClasses[$classSection][] = $cRes;
     }
    }
   }
  }
  else
   return ShowError(GetMessage("SCHOOL_CLASS_NOT_FOUND"));
 }
 else
 {
  $cRes = CIBlockElement::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y"), false, false, Array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME"));
  while($arRes = $cRes->Fetch())
  {
   $classSection = intval($arRes["IBLOCK_SECTION_ID"]);
   if(!in_array($classSection,$arClassSection))
   {
    $arClassSection[] = $classSection;
   }
   
   $arButtons = CIBlock::GetPanelButtons(
			 $arRes["IBLOCK_ID"],
			 $arRes["ID"],
			 $arRes["IBLOCK_SECTION_ID"],
			 array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		 );
		 $arRes["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		 $arRes["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
   
   $arRes["DETAIL_URL"] = $arParams["DETAIL_URL"];
   $arRes["DETAIL_URL"] = str_replace("#CLASS_ID#",$arRes["ID"],$arRes["DETAIL_URL"]);
   
   $arClasses[$classSection][] = $arRes;
  }
 }
 
	$arResult = array();
	 
	if(count($arClassSection)>0)
	{
		$arFilter = array();
		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
		if(!$USER->IsAdmin())
   $arFilter["ID"] = $arClassSection;
		$rsSections = CIBlockSection::GetList(array("sort"=>"asc", "name"=>"asc"), $arFilter, false);
		while($sec = $rsSections->Fetch())
		{
			$arResult["ITEMS"][$sec["ID"]] = array();
			$arResult["ITEMS"][$sec["ID"]]["ID"] = $sec["ID"];
			$arResult["ITEMS"][$sec["ID"]]["IBLOCK_ID"] = $sec["IBLOCK_ID"];
			$arResult["ITEMS"][$sec["ID"]]["NAME"] = $sec["NAME"];
   
   $arButtons = CIBlock::GetPanelButtons(
			 $sec["IBLOCK_ID"],
			 0,
			 $sec["ID"],
			 array("SESSID"=>false)
		 );
		 $arResult["ITEMS"][$sec["ID"]]["EDIT_LINK"] = $arButtons["edit"]["edit_section"]["ACTION_URL"];
		 $arResult["ITEMS"][$sec["ID"]]["DELETE_LINK"] = $arButtons["edit"]["delete_section"]["ACTION_URL"];
   
			$arResult["ITEMS"][$sec["ID"]]["CLASSES"] = $arClasses[$sec["ID"]];
		}
	}
	$this->IncludeComponentTemplate();
}

if($USER->IsAuthorized() && $APPLICATION->GetShowIncludeAreas())
{
 $arButtons = CIBlock::GetPanelButtons(
  $arParams["IBLOCK_ID"],
  0,
  0
 );

 $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
}
?>