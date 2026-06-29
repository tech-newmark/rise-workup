<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Сертификаты и документы RISE, подтверждающие качество продукции, информацию о компании и соответствие требованиям для клиентов, партнеров и оптовых покупателей.");
$APPLICATION->SetPageProperty("title", "Сертификаты и документы | RISE");
$APPLICATION->SetTitle("Сертификаты");
?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("О компании");
?><section class="section content-page">
	<div class="container">
		<div class="grid">
			<div class="content">
				<? $APPLICATION->IncludeFile(
					SITE_DIR . "include/company/certificates.php",
					array(),
					array(
						"MODE" => "html",
						"NAME" => "текст",
						'SHOW_BORDER' => true
					)
				); ?>
			</div>
			<div class="content">
				<div class="content-page__img-wrapper">
					<? $APPLICATION->IncludeFile(
						SITE_DIR . "include/company/certificates-img.php",
						array(),
						array(
							"MODE" => "html",
							"NAME" => "изображение",
							'SHOW_BORDER' => true
						)
					); ?>


				</div>
			</div>
		</div>
	</div>
</section> <? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>