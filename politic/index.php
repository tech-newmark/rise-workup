<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Политика конфиденциальности");
?>
<section>
	<div class="container">
		<div class="content">
			<? $APPLICATION->IncludeFile(
				SITE_DIR . "include/policy.php",
				array(),
				array(
					"MODE" => "html",
					"NAME" => "Текст",
					"TEMPLATE" => "include_area.php",
				)
			); ?>
		</div>
	</div>
</section>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>