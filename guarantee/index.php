<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Условия гарантии и возврата продукции RISE для розничных и оптовых клиентов. Порядок обращения по качеству, обмену, возврату и рассмотрению заявок после покупки.");
$APPLICATION->SetPageProperty("title", "Условия гарантии и возврата продукции | RISE");
$APPLICATION->SetTitle("Гарантия");
?>
<section class="section content-page">
	<div class="container">
		<div class="grid">
			<div class="content">
				<? $APPLICATION->IncludeFile(
					SITE_DIR . "include/guarantee.php",
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
						SITE_DIR . "include/guarantee-img.php",
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
</section>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>