<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arTemplateParameters = array(
	"TITLE" => array(
		"NAME" => GetMessage("SUBSCRIBE_TITLE"),
		"TYPE" => "TEXT",
		"DEFAULT" => "Подпишитесь на рассылку и получите скидку 10% на товары в розницу",
		"PARENT" => "VISUAL",
	),
	"DESCRIPTION" => array(
		"NAME" => GetMessage("SUBSCRIBE_DESCRIPTION"),
		"TYPE" => "TEXT",
		"DEFAULT" => "Добро пожаловать в сообщество Rise Bags! Ваш промокод на скидку 10% внутри",
		"PARENT" => "VISUAL",
	),

	"ANSWER" => array(
		"NAME" => GetMessage("SUBSCRIBE_ANSWER"),
		"TYPE" => "TEXT",
		"DEFAULT" => "Спасибо! Вы успешно подписались на рассылку!",
		"PARENT" => "VISUAL",
	),
);
