<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arUserFieldNames = array(
	'PERSONAL_PHOTO',
	'ID',
	'LOGIN',
	'NAME',
	'SECOND_NAME',
	'LAST_NAME',
	'EMAIL',
	'DATE_REGISTER',
	'PERSONAL_PROFESSION',
	'PERSONAL_WWW',
	'PERSONAL_BIRTHDAY',
	'PERSONAL_ICQ',
	'PERSONAL_GENDER',
	'PERSONAL_PHONE',
	'PERSONAL_FAX',
	'PERSONAL_MOBILE',
	'PERSONAL_PAGER',
	'PERSONAL_STREET',
	'PERSONAL_MAILBOX',
	'PERSONAL_CITY',
	'PERSONAL_STATE',
	'PERSONAL_ZIP',
	'PERSONAL_COUNTRY',
	'WORK_PHONE',
	'PERSONAL_NOTES',
	'ADMIN_NOTES',
	'XML_ID'
	);

$userProp = array();

foreach ($arUserFieldNames as $name)
{
	$userProp[$name] = GetMessage('SS_'.$name);
}

$arRes = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
if (!empty($arRes))
{
	foreach ($arRes as $key => $val)
	{
		$userProp[$val["FIELD_NAME"]] = '* '.(strlen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
	}
}

$arTemplateParameters = array(
	"USER_PROPERTY"=>array(
		"NAME" => GetMessage('SS_TPL_PARAM_USER_PROPERTY'),
		"TYPE" => "LIST",
		"VALUES" => $userProp,
		"MULTIPLE" => "Y",
		"DEFAULT" => array('UF_EDU_STRUCTURE', 'FULL_NAME'),
	),
); 

if ($arCurrentValues['SHOW_FROM_ROOT'] && $arCurrentValues['SHOW_FROM_ROOT'] == 'Y')
	unset($arTemplateParameters['MAX_DEPTH']);
?>