<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Карта цветов");
?>
<section class="section ">
	<div class="container">
		<? $APPLICATION->IncludeFile(
			SITE_DIR . "include/color-chart-top.php",
			array(),
			array(
				"MODE" => "html",
				"NAME" => "Верхний блок текста",
				"TEMPLATE" => "include_area.php",
			)
		); ?>
		<? $APPLICATION->IncludeFile(
			SITE_DIR . "include/color-chart-bottom.php",
			array(),
			array(
				"MODE" => "html",
				"NAME" => "Нижний блок текста",
				"TEMPLATE" => "include_area.php",
			)
		); ?>
	</div>
</section> <? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>