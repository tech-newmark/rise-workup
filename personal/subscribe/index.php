<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Подписка на рассылку");
?>
<? $APPLICATION->IncludeComponent(
	"bitrix:subscribe.edit",
	"clear",
	[
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"ALLOW_ANONYMOUS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"SET_TITLE" => "Y",
		"SHOW_AUTH_LINKS" => "Y",
		"SHOW_HIDDEN" => "N",
		"COMPONENT_TEMPLATE" => "clear"
	],
	false
); ?>

<? $APPLICATION->IncludeComponent(
	"bitrix:catalog.product.subscribe.list",
	"",
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"LINE_ELEMENT_COUNT" => "3"
	)
); ?>
  
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>

  