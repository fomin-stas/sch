<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('iblock')) return;

$bSoNet = CModule::IncludeModule('socialnetwork');

$arParams['FILTER_NAME'] = 
		(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z0-9_]*$/", $arParams["FILTER_NAME"])) ? 
		'find_' : $arParams['FILTER_NAME'];


		
		
$arParams['USERS_PER_PAGE'] = intval($arParams['USERS_PER_PAGE']);
$arParams['NAV_TITLE'] = $arParams['NAV_TITLE'] ? $arParams['NAV_TITLE'] : GetMessage('INTR_ISL_PARAM_NAV_TITLE_DEFAULT');

InitBVar($arParams['FILTER_1C_USERS']);
InitBVar($arParams['FILTER_SECTION_CURONLY']);
InitBVar($arParams['SHOW_NAV_TOP']);
InitBVar($arParams['SHOW_NAV_BOTTOM']);
InitBVar($arParams['SHOW_UNFILTERED_LIST']);

$arParams['DETAIL_URL'] = COption::GetOptionString('intranet', 'search_user_url', '/user/#ID#/');


if (!array_key_exists("PM_URL", $arParams))
	$arParams["~PM_URL"] = $arParams["PM_URL"] = "/company/personal/messages/chat/#USER_ID#/";
if (!array_key_exists("PATH_TO_CONPANY_DEPARTMENT", $arParams))
	$arParams["~PATH_TO_CONPANY_DEPARTMENT"] = $arParams["PATH_TO_CONPANY_DEPARTMENT"] = "/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=#ID#";
if (IsModuleInstalled("video") && !array_key_exists("PATH_TO_VIDEO_CALL", $arParams))
	$arParams["~PATH_TO_VIDEO_CALL"] = $arParams["PATH_TO_VIDEO_CALL"] = "/company/personal/video/#USER_ID#/";

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;
	
if ($arParams['CACHE_TYPE'] == 'A')
	$arParams['CACHE_TYPE'] = COption::GetOptionString("main", "component_cache_on", "Y");

$bExcel = $_GET['excel'] == 'yes';
$bNav = $arParams['SHOW_NAV_TOP'] == 'Y' || $arParams['SHOW_NAV_BOTTOM'] == 'Y';

$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas() && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();

// prepare list filter
$arFilter = array('ACTIVE' => 'Y');

if ('Y' == $arParams['FILTER_1C_USERS'])
	$arFilter['UF_1C'] = 1;


$cnt_start = count($arFilter); // we'll cache all variants of selection by UF_DEPARTMENT (and GROUPS_ID with extranet)
$cnt_start_cache_id = '';
foreach ($arFilter as $key => $value)
	$cnt_start_cache_id .= '|'.$key.':'.preg_replace("/[\s]*/", "", var_export($value, true));

if ($GLOBALS[$arParams['FILTER_NAME'].'_POST'])
	$arFilter['WORK_POSITION'] = $GLOBALS[$arParams['FILTER_NAME'].'_POST'];
if ($GLOBALS[$arParams['FILTER_NAME'].'_COMPANY'])
	$arFilter['WORK_COMPANY'] = $GLOBALS[$arParams['FILTER_NAME'].'_COMPANY'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_EMAIL'])
	$arFilter['EMAIL'] = $GLOBALS[$arParams['FILTER_NAME'].'_EMAIL'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_FIO'])
	$arFilter['NAME'] = $GLOBALS[$arParams['FILTER_NAME'].'_FIO'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_PHONE'])
	$arFilter['WORK_PHONE'] = $GLOBALS[$arParams['FILTER_NAME'].'_PHONE'];
	
if ($GLOBALS[$arParams['FILTER_NAME'].'_UF_PHONE_INNER'])
	$arFilter['UF_PHONE_INNER'] = $GLOBALS[$arParams['FILTER_NAME'].'_UF_PHONE_INNER'];
	
if ($GLOBALS[$arParams['FILTER_NAME'].'_KEYWORDS'])
	$arFilter['KEYWORDS'] = $GLOBALS[$arParams['FILTER_NAME'].'_KEYWORDS'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_IS_ONLINE'] == 'Y')
{
	$arFilter['LAST_ACTIVITY'] = 120;
}

if ($GLOBALS[$arParams['FILTER_NAME'].'_LAST_NAME'])
{
	$arFilter['LAST_NAME'] = $GLOBALS[$arParams['FILTER_NAME'].'_LAST_NAME'];
	$arFilter['LAST_NAME_EXACT_MATCH'] = 'Y';
}

$arFilter["!UF_EDU_STRUCTURE"] = false;

if(!empty($arParams["CLASS_ID"]))
{
	$arFilter["UF_EDU_STRUCTURE"] = $arParams["CLASS_ID"];
	
	
	if($arParams["SET_TITLE"]=="Y")
	{
		$class = CIBlockElement::GetByID(intval($arParams["CLASS_ID"]));
		if($class!==false)
		{
			$class = $class->Fetch();
			$dirTitle = $APPLICATION->GetTitle();
			$APPLICATION->AddChainItem($dirTitle, $APPLICATION->GetCurDir());
			
			$classSec = CIBlockSection::GetByID($class["IBLOCK_SECTION_ID"]);
			$classSec = $classSec->Fetch();
			
			$APPLICATION->SetTitle($classSec["NAME"].": ".$class["NAME"]);
		}
	}
}

$extFilter = $_GLOBALS[$arParams['FILTER_NAME']];
array_merge($arFilter,$extFilter);


/*
if ($arParams['SHOW_UNFILTERED_LIST'] == 'N' && !$bExcel && $cnt_start == count($arFilter) && !$arFilter['UF_EDU_STRUCTURE'])
{
	$arResult['EMPTY_UNFILTERED_LIST'] = 'Y';
	$this->IncludeComponentTemplate();
	return;
}
*/

$arParams['bCache'] = 
	/*$arParams['SHOW_UNFILTERED_LIST'] == 'Y' && */$cnt_start == count($arFilter) // we cache only unfiltered list
	&& !$bExcel 
	&& $arParams['CACHE_TYPE'] == 'Y' && $arParams['CACHE_TIME'] > 0;

$arResult['FILTER_VALUES'] = $arFilter;

if (!$bExcel && $bNav)
{
	CPageOption::SetOptionString("main", "nav_page_in_session", "N");
}

if ($arParams['bCache'])
{
	$cache_dir = '/'.SITE_ID.$this->GetRelativePath();
	$cache_dir .= '/'.substr(md5($cnt_start_cache_id), 0, 5);
	$cache_dir .= '/'.trim(CDBResult::NavStringForCache($arParams['USERS_PER_PAGE'], false), '|');
	
	$cache_id = $this->GetName().'|'.SITE_ID;

	if (CModule::IncludeModule('extranet') && CExtranet::IsExtranetSite())
		$cache_id .= '|'.$USER->GetID().'|'.$arParams['EXTRANET_TYPE'];

	$cache_id .= CDBResult::NavStringForCache($arParams['USERS_PER_PAGE'], false);
	$cache_id .= $cnt_start_cache_id."|".$arParams['USERS_PER_PAGE'];
	
	$obCache = new CPHPCache();
}

if ($arParams['bCache'] && $obCache->InitCache($arParams['CACHE_TIME'], $cache_id, $cache_dir))
{
	$bFromCache = true;
	
	$vars = $obCache->GetVars();
	$arUsers = $vars['USERS'];
	$arDepartments = $vars['DEPARTMENTS'];
	$arResult['DEPARTMENT_HEAD'] = $vars['DEPARTMENT_HEAD'];
	$arResult['USERS_NAV'] = $vars['USERS_NAV'];
}
else
{
	$bFromCache = false;

	if ($arParams['bCache'])
	{
		$obCache->StartDataCache();
		global $CACHE_MANAGER; 
		$CACHE_MANAGER->StartTagCache($cache_dir);
		$CACHE_MANAGER->RegisterTag('intranet_users');
	}
	
	// get users list
	$obUser = new CUser();
	
	$arSelect = array('ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL', 'LID', 'DATE_REGISTER',  'PERSONAL_PROFESSION', 'PERSONAL_WWW', 'PERSONAL_ICQ', 'PERSONAL_GENDER', 'PERSONAL_BIRTHDATE', 'PERSONAL_PHOTO', 'PERSONAL_PHONE', 'PERSONAL_FAX', 'PERSONAL_MOBILE', 'PERSONAL_PAGER', 'PERSONAL_STREET', 'PERSONAL_MAILBOX', 'PERSONAL_CITY', 'PERSONAL_STATE', 'PERSONAL_ZIP', 'PERSONAL_COUNTRY', 'PERSONAL_NOTES', 'WORK_COMPANY', 'WORK_DEPARTMENT', 'WORK_POSITION', 'WORK_WWW', 'WORK_PHONE', 'WORK_FAX', 'WORK_PAGER', 'WORK_STREET', 'WORK_MAILBOX', 'WORK_CITY', 'WORK_STATE', 'WORK_ZIP', 'WORK_COUNTRY', 'WORK_PROFILE', 'WORK_LOGO', 'WORK_NOTES', 'PERSONAL_BIRTHDAY', 'LAST_ACTIVITY_DATE');

	$arUsers = array();

	$bDisable = false;

	$arListParams = array('SELECT' => array('UF_*'));
	if (!$bExcel && $arParams['USERS_PER_PAGE'] > 0)
		$arListParams['NAV_PARAMS'] = array('nPageSize' => $arParams['USERS_PER_PAGE'], 'bShowAll' => false);

	if ($bDisable)
	{
		$dbUsers = new CDBResult();
		$dbUsers->InitFromArray(array());
	}
	else
	{
		$dbUsers = $obUser->GetList(
			($sort_by = 'last_name'), ($sort_dir = 'asc'), 
			$arFilter, 
			$arListParams
		);
	}
	
	$arDepartments = array();
	
	while ($arUser = $dbUsers->Fetch())
	{
		$arUsers[$arUser['ID']] = $arUser;
	}

	foreach ($arUsers as $key => $arUser)
	{
		if ($arParams['bCache'])
		{
			$CACHE_MANAGER->RegisterTag('intranet_user_'.$arUser['ID']);
		}
	
		// cache optimization
		foreach ($arUser as $k => $value)
		{
			if (
				is_array($value) && count($value) <= 0 
				|| !is_array($value) && strlen($value) <= 0 
				|| !in_array($k, $arSelect) && substr($k, 0, 3) != 'UF_'
			) 
			{
				unset($arUser[$k]);
			}
			elseif ($k == "PERSONAL_COUNTRY" || $k == "WORK_COUNTRY")
			{
				$arUser[$k] = GetCountryByID($value);
			}
		}
	
		if (is_array($arUser['UF_DEPARTMENT']) && count($arUser['UF_DEPARTMENT']) > 0)
			$arDepartments = array_merge($arDepartments, $arUser['UF_DEPARTMENT']);
		
		$arUsers[$key] = $arUser;
	}
	
	if (count($arDepartments) > 0)
	{
		$dbRes = CIBlockSection::GetList(array('SORT' => 'ASC'), array('ID' => array_unique($arDepartments)));
		$arDepartments = array();
		while ($arSect = $dbRes->Fetch())
		{
			$arDepartments[$arSect['ID']] = $arSect['NAME'];
		}
	}
	
	$arResult["USERS_NAV"] = $bNav ? $dbUsers->GetPageNavStringEx($navComponentObject=null, $arParams["NAV_TITLE"]) : '';
	
	if ($arParams['bCache'])
	{
		$arCache = array(
			'USERS' => $arUsers,
			'DEPARTMENTS' => $arDepartments,
			'DEPARTMENT_HEAD' => $arResult['DEPARTMENT_HEAD'],
			'USERS_NAV' => $arResult['USERS_NAV']
		);
	
		$CACHE_MANAGER->EndTagCache();
		$obCache->EndDataCache($arCache);
	}
}

$arResult['USERS'] = array();
$strUserIDs = '';

$userCnt = count($arUsers);
foreach ($arUsers as $arUser)
{
	$arDep = array();
	if (is_array($arUser['UF_DEPARTMENT']))
	{
		foreach ($arUser['UF_DEPARTMENT'] as $key => $sect)
		{
			$arDep[$sect] = $arDepartments[$sect];
		}
	}
	
	$arUser['UF_DEPARTMENT'] = $arDep;
	
	if ($arParams['DETAIL_URL'])
		$arUser['DETAIL_URL'] = str_replace(array('#ID#', '#USER_ID#'), $arUser['ID'], $arParams['DETAIL_URL']);

	if($userCnt < 200)
	{
		$strUserIDs .= ($strUserIDs == '' ? '' : '|').$arUser['ID'];

		if (!$arUser['PERSONAL_PHOTO'])
		{
			switch ($arUser['PERSONAL_GENDER'])
			{
				case "M":
					$suffix = "male";
					break;
				case "F":
					$suffix = "female";
					break;
				default:
					$suffix = "unknown";
			}
			$arUser['PERSONAL_PHOTO'] = COption::GetOptionInt("socialnetwork", "default_user_picture_".$suffix, false, SITE_ID);
		}

		if($arUser['PERSONAL_PHOTO'])
		{
			if ($bExcel)
			{
				$arUser['PERSONAL_PHOTO'] = CFile::GetPath($arUser['PERSONAL_PHOTO']);
			}
			else
			{
				$arUser['PERSONAL_PHOTO'] = $arImage['IMG'];
			}
		}
	
	}

	
	// emulate list flags ;-)
	/*
	$arUser['IS_ONLINE'] |= rand(1, 100) <= 75;
	$arUser['IS_ABSENT'] |= rand(1, 100) <= 20;
	$arUser['IS_FEATURED'] |= rand(1, 100) <= 5;
	$arUser['IS_BIRTHDAY'] |= rand(1, 100) <= 2;
	*/

	$arResult['USERS'][$arUser['ID']] = $arUser;
}

if (!$bExcel)
{
	if ($bFromCache && $strUserIDs)
	{
		$dbRes = CUser::GetList($by='id', $order='asc', array('ID' => $strUserIDs, 'LAST_ACTIVITY' => 120));
		while ($arRes = $dbRes->Fetch())
		{
			if ($arResult['USERS'][$arRes['ID']])
				$arResult['USERS'][$arRes['ID']]['IS_ONLINE'] = true;
		}
		unset($dbRes);
	}

	$arResult['bAdmin'] = $USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('edit_subordinate_users');

	$this->IncludeComponentTemplate();
}
else
{
	$APPLICATION->RestartBuffer();
	
	// hack. any '.default' customized template should contain 'excel' page
	$this->__templateName = '.default';
	
	Header("Content-Type: application/force-download");
	Header("Content-Type: application/octet-stream");
	Header("Content-Type: application/download");
	//Header("Content-Type: application/vnd.ms-excel; charset=".LANG_CHARSET);
	Header("Content-Disposition: attachment;filename=users.xls");
	Header("Content-Transfer-Encoding: binary");

	$this->IncludeComponentTemplate('excel');
	
	die();
}
?>