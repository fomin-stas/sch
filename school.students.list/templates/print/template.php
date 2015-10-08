<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (is_array($arResult['USERS'])):
	foreach ($arResult['USERS'] as $key => $arUser):
		$APPLICATION->IncludeComponent(
			'bitrix:intranet.system.person',
			'print',
			array(
				'USER' => $arUser,
				'USER_PROPERTY' => $arParams['USER_PROPERTY'],
				'PM_URL' => $arParams['PM_URL'],
				'STRUCTURE_PAGE' => $arParams['STRUCTURE_PAGE'],
				'STRUCTURE_FILTER' => $arParams['STRUCTURE_FILTER'],
				'USER_PROP' => $arResult['USER_PROP'],
				'NAME_TEMPLATE' => $arParams['NAME_TEMPLATE'],
				'SHOW_LOGIN' => $arParams['SHOW_LOGIN'],
				'LIST_OBJECT' => $arParams['LIST_OBJECT'],
				'SHOW_FIELDS_TOOLTIP' => $arParams['SHOW_FIELDS_TOOLTIP'],
				'USER_PROPERTY_TOOLTIP' => $arParams['USER_PROPERTY_TOOLTIP'],
				"DATE_FORMAT" => $arParams["DATE_FORMAT"],
				"DATE_FORMAT_NO_YEAR" => $arParams["DATE_FORMAT_NO_YEAR"],
				"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
				"SHOW_YEAR" => $arParams["SHOW_YEAR"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
				"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);
	endforeach;
endif;
?>
