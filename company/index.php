<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Информация о компании RISE: производство и поставка сумок, рюкзаков, шопперов и аксессуаров для розничных, оптовых и корпоративных клиентов.");
$APPLICATION->SetPageProperty("title", "О компании RISE | Производство и продажа сумок и рюкзаков");
$APPLICATION->SetTitle("О компании");
?><section class="section content-page">
	<div class="container">
		<div class="grid">
			<div class="content">
				<? $APPLICATION->IncludeFile(
					SITE_DIR . "include/company/desc.php",
					array(),
					array(
						"MODE" => "html",
						"NAME" => "текст о компании",
						"TEMPLATE" => "include_area.php",
					)
				); ?>
			</div>
			<div class="content">
				<div class="content-page__img-wrapper">
					<? $APPLICATION->IncludeFile(
						SITE_DIR . "include/company/image.php",
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