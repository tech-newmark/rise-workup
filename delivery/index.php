<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Условия доставки продукции RISE по Санкт-Петербургу и регионам России. Информация о способах получения заказов, отправке транспортными компаниями и порядке передачи товара.");
$APPLICATION->SetPageProperty("title", "Условия доставки нашей продукции | RISE");
$APPLICATION->SetTitle("Доставка и оплата");
?>
<section class="section content-page">
	<div class="container">
		<div class="grid">
			<div class="content">
				<? $APPLICATION->IncludeFile(
					SITE_DIR . "include/delivery.php",
					array(),
					array(
						"MODE" => "html",
						"NAME" => "текст",
						"TEMPLATE" => "include_area.php",
					)
				); ?>
			</div>
			<div class="content">
				<div class="content-page__img-wrapper">
					<? $APPLICATION->IncludeFile(
						SITE_DIR . "include/delivery-img.php",
						array(),
						array(
							"MODE" => "html",
							"NAME" => "изображение",
							"TEMPLATE" => "include_area.php",
						)
					); ?>
				</div>
			</div>
		</div>
	</div>
</section> <? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>