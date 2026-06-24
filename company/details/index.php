<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Реквизиты");
?>
<section class="section">
	<div class="container">
		<div class="content">
			<? $APPLICATION->IncludeFile(
				SITE_DIR . "include/company/details.php",
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