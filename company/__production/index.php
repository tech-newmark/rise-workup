<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Производство");
?>
<section class="section production">
	<div class="container">
		<div class="content">
			<? $APPLICATION->IncludeFile(
				SITE_DIR . "include/company/production/index.php",
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