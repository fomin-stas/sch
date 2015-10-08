<?
$dt = strtotime($arResult["ACTIVE_FROM"]);
$arResult["ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("H:i", $dt);
$dt = strtotime($arResult["ACTIVE_TO"]);
$arResult["ACTIVE_TO"] = CIBlockFormatProperties::DateFormat("H:i", $dt);
?>