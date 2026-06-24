<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");
const HIDE_SIDEBAR = true;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

/** @global CMain $APPLICATION */

$APPLICATION->SetTitle("Страница не найдена"); ?>

<section class="base-section nfp" style="
    padding: 100px 20px;
    color: #333;
    text-align: center;
">
	<div class="container" style="
        max-width: 1100px;
        margin: 0 auto;
    ">
		<div class="nfp__wrapper content" style="
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        ">
			<h1 style="
                color: var(--primary);
                font-size: clamp(84px, 16vw, 280px);
                margin: 0;
                font-weight: bold;
            ">404</h1>

			<p style="
                font-size: clamp(16px, 2vw, 20px);
                line-height: 1.6;
                max-width: 600px;
				margin: 0;
            ">К сожалению, такой страницы не существует или она была удалена...</p>

			<a href="/" style="
                color: var(--primary);
                font-size: 1rem;
                text-decoration: underline;">
				Вернуться на главную
			</a>
		</div>
	</div>
</section>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
