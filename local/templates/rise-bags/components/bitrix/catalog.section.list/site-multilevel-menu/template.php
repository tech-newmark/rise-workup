<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
?>




<nav class="mobile-menu">
	<? if ($arResult["SECTIONS_COUNT"] > 0): ?>
		<ul class="mobile-menu__list">
			<li>
				<a href="/catalog/" class="mobile-menu__link">
					Каталог
				</a>

				<ul>
					<li class="mobile-menu__mobile-back"><span class="mobile-menu__link">Назад</span></li>
					<li class="mobile-menu__mobile-title"><a class="mobile-menu__link" href="/catalog/">Каталог</a></li>
					<?
					$intCurrentDepth = 0;
					$boolFirst = true;

					foreach ($arResult['SECTIONS'] as &$arSection):
						$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
						$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

						if ($intCurrentDepth < $arSection['RELATIVE_DEPTH_LEVEL']) {
							if (0 < $intCurrentDepth) {
								echo "\n", str_repeat("\t", $arSection['RELATIVE_DEPTH_LEVEL']), '<ul>';
								echo "<li class='mobile-menu__mobile-back'><span class='mobile-menu__link'>Назад</span></li>";

								if (in_array($arSection['IBLOCK_SECTION_ID'], array_column($arResult["SECTION_TITLES"], 'ID'))) {
									if ($arResult["SECTION_TITLES"][$arSection['IBLOCK_SECTION_ID']]["SECTION_PAGE_URL"]) {
										echo '<li class="mobile-menu__mobile-title"><a class="mobile-menu__link" href="' . $arResult["SECTION_TITLES"][$arSection['IBLOCK_SECTION_ID']]["SECTION_PAGE_URL"] . '">' . $arResult["SECTION_TITLES"][$arSection['IBLOCK_SECTION_ID']]["NAME"] . '</a></li>';
									} else {
										echo '<li class="mobile-menu__mobile-title"><span class="mobile-menu__link">' . $arResult["SECTION_TITLES"][$arSection['IBLOCK_SECTION_ID']]["NAME"] . '</span></li>';
									}
								}
							}
						} elseif ($intCurrentDepth == $arSection['RELATIVE_DEPTH_LEVEL']) {
							if (!$boolFirst) {
								echo '</li>';
							}
						} else {
							while ($intCurrentDepth > $arSection['RELATIVE_DEPTH_LEVEL']) {
								echo '</li>', "\n", str_repeat("\t", $intCurrentDepth), '</ul>', "\n", str_repeat("\t", $intCurrentDepth - 1);
								$intCurrentDepth--;
							}

							echo str_repeat("\t", $intCurrentDepth - 1), '</li>';
						}

						echo (!$boolFirst ? "\n" : ''), str_repeat("\t", $arSection['RELATIVE_DEPTH_LEVEL']);
					?>
						<li id="<?= $this->GetEditAreaId($arSection['ID']); ?>">
							<? if ($arSection["SECTION_PAGE_URL"]): ?>
								<a class="mobile-menu__link" href="<?= $arSection["SECTION_PAGE_URL"]; ?>"><?= $arSection["NAME"]; ?></a>
							<? else: ?>
								<span class="mobile-menu__link"><?= $arSection["NAME"]; ?></span>
							<? endif; ?>
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
					}
						?>
				</ul>
			</li>
		</ul>
	<? endif; ?>
</nav>