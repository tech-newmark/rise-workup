<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Информация для потребителей продукции RISE: условия покупки, доставки, оплаты, гарантии, возврата, обмена и обращения по вопросам качества товаров.");
$APPLICATION->SetPageProperty("title", "Информация для потребителей | RISE");
$APPLICATION->SetTitle("Информация потребителю");
?>
<section class="section">
	<div class="container">
		<div class="content">
			<? $APPLICATION->IncludeFile(
				SITE_DIR . "include/company/customers-info.php",
				array(),
				array(
					"MODE" => "html",
					"NAME" => "текст",
					"TEMPLATE" => "include_area.php",
				)
			); ?>
		</div>
	</div>
</section>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>