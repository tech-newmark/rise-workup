<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

if ($arResult["SECTIONS"]): ?>
	<section class="section catalog-section-list">
		<div class="section-header">
			<h2>Каталог нашей продукции</h2>
			<?
			$APPLICATION->IncludeFile(
				SITE_DIR . 'include/front-catalog-preview-text.php',
				array(),
				array('MODE' => 'html', 'NAME' => 'текст', 'SHOW_BORDER' => true)
			);
			?>
		</div>
		<ul>
			<? $intCurrentDepth = 1;
			$boolFirst = true;

			foreach ($arResult['SECTIONS'] as &$arSection):
				$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
				$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

				if ($intCurrentDepth < $arSection['RELATIVE_DEPTH_LEVEL']) {
					if (0 < $intCurrentDepth)
						echo "\n", str_repeat("\t", $arSection['RELATIVE_DEPTH_LEVEL']), '<ul>';
				} elseif ($intCurrentDepth == $arSection['RELATIVE_DEPTH_LEVEL']) {
					if (!$boolFirst)
						echo '</li>';
				} else {
					while ($intCurrentDepth > $arSection['RELATIVE_DEPTH_LEVEL']) {
						echo '</li>', "\n", str_repeat("\t", $intCurrentDepth), '</ul>', "\n", str_repeat("\t", $intCurrentDepth - 1);
						$intCurrentDepth--;
					}
					echo str_repeat("\t", $intCurrentDepth - 1), '</li>';
				}
				echo (!$boolFirst ? "\n" : ''), str_repeat("\t", $arSection['RELATIVE_DEPTH_LEVEL']);

				$sectionBackground = $arSection["DETAIL_PICTURE"]
					? CFile::GetPath($arSection["DETAIL_PICTURE"])
					: SITE_TEMPLATE_PATH . '/_dist/images/card-bg.png';
			?>

				<li id="<?= $this->GetEditAreaId($arSection['ID']); ?>"
					<? if ($arSection["DEPTH_LEVEL"] === '1'): ?>
					style="background-image: url('<?= htmlspecialcharsbx($sectionBackground) ?>');"
					<? endif; ?>>


					<? if ($arSection["DEPTH_LEVEL"] === '1'): ?>
						<img src="<?= $arSection["PICTURE"]["SRC"] ?>" alt="" width="<?= $arSection["PICTURE"]["WIDTH"] ?>" height="<?= $arSection["PICTURE"]["HEIGHT"] ?>">
					<? endif; ?>

					<a href="<?= $arSection["SECTION_PAGE_URL"]; ?>">
						<?= $arSection["NAME"]; ?>
						<? if ($arParams["COUNT_ELEMENTS"] && $arSection['ELEMENT_CNT'] > 0): ?>
							<span>(<?= $arSection["ELEMENT_CNT"]; ?>)</span>
						<? endif; ?>
					</a>
				<?
				$intCurrentDepth = $arSection['RELATIVE_DEPTH_LEVEL'];
				$boolFirst = false;
			endforeach;

			unset($arSection);

			while ($intCurrentDepth > 1) {
				echo '</li>', "\n", str_repeat("\t", $intCurrentDepth), '</ul>', "\n", str_repeat("\t", $intCurrentDepth - 1);
				$intCurrentDepth--;
			}
			if ($intCurrentDepth > 0) {
				echo '</li>', "\n";
			} ?>
		</ul>
	</section>
<? endif; ?>