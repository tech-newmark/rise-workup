<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Пользовательское соглашение об обработке персональных данных в соответствии с 152-ФЗ");
$APPLICATION->SetPageProperty("title", "Политика конфиденциальности RISE");
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