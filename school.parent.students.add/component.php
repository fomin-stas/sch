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
 if($USER->IsAuthorized())
 {
  $rsUser = CUser::GetByID($USER->GetID());
  $arUser = $rsUser->Fetch();
  $arResult["MESSAGE"] = "";
  $APPLICATION->GetCurPageParam("", array("strMessage"));
 // process POST data
   $arResult["ERRORS"] = array();
 if (check_bitrix_sessid() && !empty($_REQUEST["student_submit"])){
     if(!empty($_REQUEST["student_code"])){
       $arNewFilter = Array(
        "UF_STUDENT_LINK_CODE" => array($_REQUEST["student_code"])
       );
       $rsNewUser = CUser::GetList(($by="id"), ($order="desc"), $arNewFilter, Array("SELECT"=>Array("UF_STUDENT_LINK_CODE"))); 
       if($arNewUser = $rsNewUser->Fetch()){
         // Update parent
         if(in_array($_REQUEST["student_code"], $arUser["UF_PARENT_LINK_CODE"]) !== true)
           $arUser["UF_PARENT_LINK_CODE"][] = $_REQUEST["student_code"];

         $arUpdateFields = array("UF_PARENT_LINK_CODE" => $arUser["UF_PARENT_LINK_CODE"]);
         $user = new CUser;
         if(!$user->Update($arUser["ID"], $arUpdateFields))
           $arResult["ERRORS"][] = $user->LAST_ERROR;
         else{
           $sRedirectUrl = $APPLICATION->GetCurPageParam("strMessage=".urlencode(GetMessage("CHILDRENS_ADDED")), array("strMessage"));
           LocalRedirect($sRedirectUrl);
         }
       }
       else{
         $arResult["ERRORS"][] = GetMessage("CHILDRENS_NOT_FOUND");
       }
     }
   }  
   if(isset($arUser["UF_PARENT_LINK_CODE"]) && is_array($arUser["UF_PARENT_LINK_CODE"]) && count($arUser["UF_PARENT_LINK_CODE"]))
   {
    $arUFilter = Array(
     "UF_STUDENT_LINK_CODE" => $arUser["UF_PARENT_LINK_CODE"],
    );
    
    $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $arUFilter, Array("SELECT"=>Array("UF_EDU_STRUCTURE")));
    while($arUser = $rsUsers->Fetch())
    {
      $arResult["CHILDRENS"][] = $arUser;
    }
   }
 }
 else
 {
  ShowError(GetMessage("USER_NOT_AUTHORIZED"));
  return;
 }
 
 $arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strMessage"]);
  
	$this->IncludeComponentTemplate();
?>
