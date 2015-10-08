<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()))))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	}
	else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	if($arResult = $rsIBlock->GetNext())
	{
		$iTeachersDept = 0;
		$iAdminDept = 0;
		$iWorkersDept = 0;

		$dbRes = CIBlockSection::GetTreeList(array("IBLOCK_ID" => $arResult['ID']));
		while ($arRes = $dbRes->Fetch()) {
			$arResult["DEPARTMENTS"][$arRes["ID"]] = $arRes;
			if($arRes["CODE"] == "admin") $iAdminDept = $arRes["ID"];
			if($arRes["CODE"] == "teachers") $iTeachersDept = $arRes["ID"];
			if($arRes["CODE"] == "workers") $iWorkersDept = $arRes["ID"];
		}

		$cUser = new CUser; 
		$sort_by = "LAST_NAME";
		$sort_ord = "ASC";
	$no_show_users = CGroup::GetGroupUser(11);
		$arFilter = array(
		   "ACTIVE" => "Y",
		   "ID" => "~".implode("& ~", $no_show_users),
		);
		$dbUsers = $cUser->GetList($sort_by, $sort_ord, $arFilter, array("SELECT" => array("UF_*")));
		while ($arUser = $dbUsers->Fetch()) {
			if ($arUser["UF_IS_DIR"]) $arResult["dir"] = $arUser;
			foreach ($arUser["UF_DEPARTMENT"] as $dept_id) {
				if (is_array($arResult["DEPARTMENTS"][$dept_id])) {
					$arResult["DEPARTMENTS"][$dept_id]["USERS"][] = $arUser;
				}
			}
		}

		function myComp($a, $b) {
			if ($a['ID'] === $b['ID']) return 0;
			return($a['ID'] > $b['ID']) ? 1 : -1;
		}

		$arResult['DEPARTMENTS'][$iTeachersDept]['USERS'] = array_udiff(array_merge($arResult['DEPARTMENTS'][$iTeachersDept]['USERS'], $arResult['DEPARTMENTS'][$iWorkersDept]['USERS']), $arResult['DEPARTMENTS'][$iAdminDept]['USERS'], 'myComp');

		
		$this->SetResultCacheKeys(array(
			"DEPARTMENTS",
		));
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}
?>