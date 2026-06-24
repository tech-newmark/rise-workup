<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Iblock\SectionPropertyTable;

$this->setFrameMode(true);

?>

<div class="bx-filter <?= $arParams["FILTER_EXPANDED"] && $arParams["FILTER_EXPANDED"] === "Y" ? 'expanded' : '' ?>">
	<button type="button" class="main-btn filter-opener-btn" id="smartfilter_form_opener">
		<svg width='16' height='16' role='img' aria-hidden='true' focusable='false'>
			<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-filter'></use>
		</svg>

		<span>Фильтр</span>
	</button>

	<form style="<?= $arParams["FILTER_EXPANDED"] && $arParams["FILTER_EXPANDED"] !== "Y" ? 'display: none' : '' ?>" name="<?= $arResult["FILTER_NAME"] . "_form" ?>" action="<?= $arResult["FORM_ACTION"] ?>" method="get" class="smartfilter" id="smartfilter_form">

		<? foreach ($arResult["HIDDEN"] as $arItem): ?>
			<input type="hidden" name="<?= $arItem["CONTROL_NAME"] ?>" id="<?= $arItem["CONTROL_ID"] ?>" value="<?= $arItem["HTML_VALUE"] ?>" />
		<? endforeach; ?>

		<div class="bx-filter-header">
			<span>Фильтр</span>
			<button type="button" class="bx-filter-closer" aria-label="Закрыть фильтр">
				<svg width='16' height='16' role='img' aria-hidden='true' focusable='false'>
					<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#cross-icon'></use>
				</svg>
			</button>
		</div>

		<div class="bx-filter-content">
			<? foreach ($arResult["ITEMS"] as $key => $arItem) //prices
			{
				$key = $arItem["ENCODED_ID"];
				if (isset($arItem["PRICE"])):
					if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
						continue;

					$precision = 0;
					$step_num = 4;
					$step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / $step_num;
					$prices = array();
					if (Bitrix\Main\Loader::includeModule("currency")) {
						for ($i = 0; $i < $step_num; $i++) {
							$prices[$i] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MIN"]["VALUE"] + $step * $i, $arItem["VALUES"]["MIN"]["CURRENCY"], false);
						}
						$prices[$step_num] = CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MAX"]["VALUE"], $arItem["VALUES"]["MAX"]["CURRENCY"], false);
					} else {
						$precision = $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0;
						for ($i = 0; $i < $step_num; $i++) {
							$prices[$i] = number_format($arItem["VALUES"]["MIN"]["VALUE"] + $step * $i, $precision, ".", "");
						}
						$prices[$step_num] = number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "");
					}
			?>
					<div class="bx-filter-section">
						<span class="bx-filter-section-title"><?= $arItem["NAME"] ?></span>

						<div class="bx-filter-block" data-role="bx_filter_block">
							<div class="bx-filter-block-wrapper">
								<div class="bx-filter-input-group">
									<label class="bx-filter-input">
										<?/*<span><?= GetMessage("CT_BCSF_FILTER_FROM") ?></span> */ ?>
										<input
											type="text"
											name="<?= $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
											id="<?= $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
											value="<?= $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?>"
											placeholder="<?= $arItem["VALUES"]["MIN"]["VALUE"] ?>"
											size="5"
											onkeyup="smartFilter.keyup(this)" />
									</label>
									<label class="bx-filter-input">
										<? /* <span><?= GetMessage("CT_BCSF_FILTER_TO") ?></span> */ ?>
										<input
											class="max-price"
											type="text"
											name="<?= $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
											id="<?= $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
											value="<?= $arItem["VALUES"]["MAX"]["HTML_VALUE"] ?>"
											placeholder="<?= $arItem["VALUES"]["MAX"]["VALUE"] ?>"
											size="5"
											onkeyup="smartFilter.keyup(this)" />
									</label>
								</div>

								<div class="bx-ui-slider-track-container">
									<div class="bx-ui-slider-track" id="drag_track_<?= $key ?>">
										<div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?= $key ?>"></div>
										<div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?= $key ?>"></div>
										<div class="bx-ui-slider-pricebar-v" style="left: 0;right: 0;" id="colorAvailableActive_<?= $key ?>"></div>
										<div class="bx-ui-slider-range" id="drag_tracker_<?= $key ?>" style="left: 0%; right: 0%;">
											<div class="bx-ui-slider-handle bx-ui-slider-handle--left" style="left:0;" id="left_slider_<?= $key ?>"></div>
											<div class="bx-ui-slider-handle bx-ui-slider-handle--right" style="right:0;" id="right_slider_<?= $key ?>"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?
						$arJsParams = array(
							"leftSlider" => 'left_slider_' . $key,
							"rightSlider" => 'right_slider_' . $key,
							"tracker" => "drag_tracker_" . $key,
							"trackerWrap" => "drag_track_" . $key,
							"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
							"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
							"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
							"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
							"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
							"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
							"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"],
							"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
							"precision" => $precision,
							"colorUnavailableActive" => 'colorUnavailableActive_' . $key,
							"colorAvailableActive" => 'colorAvailableActive_' . $key,
							"colorAvailableInactive" => 'colorAvailableInactive_' . $key,
						);
						?>
						<script>
							BX.ready(function() {
								window[' trackBar<?= $key ?>'] = new BX.Iblock.SmartFilter(<?= CUtil::PhpToJSObject($arJsParams) ?>);
							});
						</script>
					</div>

				<? endif;
			}

			//not prices
			foreach ($arResult["ITEMS"] as $key => $arItem):
				if (
					empty($arItem["VALUES"])
					|| isset($arItem["PRICE"])
				)
					continue;

				if (
					$arItem["DISPLAY_TYPE"] === SectionPropertyTable::NUMBERS_WITH_SLIDER
					&& ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
				)
					continue;
				?>
				<div class="bx-filter-section">
					<span class="bx-filter-section-title">
						<?= $arItem["NAME"] ?>
						<? if ($arItem["FILTER_HINT"]): ?>
							<div class="bx-filter-tooltip-opener">
								<svg width="19" height="19" role="img" aria-hidden="true" focusable="false">
									<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#icon-question"></use>
								</svg>
							</div>

							<div class="bx-filter-tooltip">
								<?= $arItem["FILTER_HINT"] ?>
							</div>
						<? endif; ?>
					</span>

					<div class="bx-filter-block" data-role="bx_filter_block">
						<?
						$arCur = current($arItem["VALUES"]);
						switch ($arItem["DISPLAY_TYPE"]):
							case SectionPropertyTable::NUMBERS_WITH_SLIDER: //NUMBERS_WITH_SLIDER
						?>
								<div class="bx-filter-block-wrapper">
									<div class="bx-filter-input-group">
										<label class="bx-filter-input">
											<?/*<span><?= GetMessage("CT_BCSF_FILTER_FROM") ?></span> */ ?>
											<input
												type="text"
												name="<?= $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
												id="<?= $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
												value="<?= $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?>"
												placeholder="<?= $arItem["VALUES"]["MIN"]["VALUE"] ?>"
												size="5"
												onkeyup="smartFilter.keyup(this)" />
										</label>
										<label class="bx-filter-input">
											<?/*<span><?= GetMessage("CT_BCSF_FILTER_TO") ?></span> */ ?>
											<input
												type="text"
												name="<?= $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
												id="<?= $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
												value="<?= $arItem["VALUES"]["MAX"]["HTML_VALUE"] ?>"
												placeholder="<?= $arItem["VALUES"]["MAX"]["VALUE"] ?>"
												size="5"
												onkeyup="smartFilter.keyup(this)" />
										</label>
									</div>

									<div class="bx-ui-slider-track-container">
										<div class="bx-ui-slider-track" id="drag_track_<?= $key ?>">
											<div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?= $key ?>"></div>
											<div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?= $key ?>"></div>
											<div class="bx-ui-slider-pricebar-v" style="left: 0;right: 0;" id="colorAvailableActive_<?= $key ?>"></div>
											<div class="bx-ui-slider-range" id="drag_tracker_<?= $key ?>" style="left: 0;right: 0;">
												<div class="bx-ui-slider-handle bx-ui-slider-handle--left" style="left:0;" id="left_slider_<?= $key ?>"></div>
												<div class="bx-ui-slider-handle bx-ui-slider-handle--right" style="right:0;" id="right_slider_<?= $key ?>"></div>
											</div>
										</div>
									</div>
									<?
									$arJsParams = array(
										"leftSlider" => 'left_slider_' . $key,
										"rightSlider" => 'right_slider_' . $key,
										"tracker" => "drag_tracker_" . $key,
										"trackerWrap" => "drag_track_" . $key,
										"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
										"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
										"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
										"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
										"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
										"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
										"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"],
										"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
										"precision" => $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0,
										"colorUnavailableActive" => 'colorUnavailableActive_' . $key,
										"colorAvailableActive" => 'colorAvailableActive_' . $key,
										"colorAvailableInactive" => 'colorAvailableInactive_' . $key,
									);
									?>
									<script>
										BX.ready(function() {
											window['trackBar<?= $key ?>'] = new BX.Iblock.SmartFilter(<?= CUtil::PhpToJSObject($arJsParams) ?>);
										});
									</script>
								</div>
							<?
								break;
							case SectionPropertyTable::NUMBERS: //NUMBERS
							?>
								<div class="bx-filter-block-wrapper">
									<div class="bx-filter-input-group">
										<label class="bx-filter-input">
											<?/*<span><?= GetMessage("CT_BCSF_FILTER_FROM") ?></span>*/ ?>
											<!-- change the type -->
											<input
												class="min-price"
												type="text"
												name="<?= $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
												id="<?= $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
												value="<?= $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?>"
												placeholder="<?= $arItem["VALUES"]["MIN"]["VALUE"] ?>"
												size="5"
												onkeyup="smartFilter.keyup(this)" />
										</label>

										<label class="bx-filter-input">
											<?/*<span><?= GetMessage("CT_BCSF_FILTER_FROM") ?></span>*/ ?>
											<input
												class="max-price"
												type="text"
												name="<?= $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
												id="<?= $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
												value="<?= $arItem["VALUES"]["MAX"]["HTML_VALUE"] ?>"
												placeholder="<?= $arItem["VALUES"]["MAX"]["VALUE"] ?>"
												size="5"
												onkeyup="smartFilter.keyup(this)" />
										</label>
									</div>
								</div>
							<?
								break;
							case SectionPropertyTable::CHECKBOXES_WITH_PICTURES: //CHECKBOXES_WITH_PICTURES
							?>
								<div class="bx-filter-block-wrapper bx-filter-block-wrapper--row">
									<? foreach ($arItem["VALUES"] as $val => $ar): ?>
										<label class="bx-filter-image-checkbox<?= $ar["DISABLED"] ? ' disabled' : '' ?>" data-role="label_<?= $ar["CONTROL_ID"] ?>" onclick="smartFilter.keyup(BX('<?= CUtil::JSEscape($ar["CONTROL_ID"]) ?>')); BX.toggleClass(this, '');">
											<input
												style="display: none"
												type="checkbox"
												name="<?= $ar["CONTROL_NAME"] ?>"
												id="<?= $ar["CONTROL_ID"] ?>"
												value="<?= $ar["HTML_VALUE"] ?>"
												<?= $ar["CHECKED"] ? 'checked="checked"' : '' ?> />

											<? if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])): ?>
												<div class="bx-filter-image-checkbox__img-wrapper">
													<img src="<?= $ar["FILE"]["SRC"] ?>" alt="<?= $ar["VALUE"]; ?>" width="24" height="24">
												</div>
											<? endif ?>
										</label>
									<? endforeach ?>
								</div>
							<?
								break;
							case SectionPropertyTable::CHECKBOXES_WITH_PICTURES_AND_LABELS: //CHECKBOXES_WITH_PICTURES_AND_LABELS
							?>
								<div class="bx-filter-block-wrapper bx-filter-block-wrapper--row">
									<? foreach ($arItem["VALUES"] as $val => $ar): ?>
										<label class="bx-filter-image-checkbox<?= $ar["DISABLED"] ? ' disabled' : '' ?>" data-role="label_<?= $ar["CONTROL_ID"] ?>"
											onclick="smartFilter.keyup(BX('<?= CUtil::JSEscape($ar["CONTROL_ID"]) ?>')); BX.toggleClass(this, '');">
											<input
												type="checkbox"
												name="<?= $ar["CONTROL_NAME"] ?>"
												id="<?= $ar["CONTROL_ID"] ?>"
												value="<?= $ar["HTML_VALUE"] ?>"
												<?= $ar["CHECKED"] ? 'checked="checked"' : '' ?> />

											<? if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])): ?>
												<div class="bx-filter-image-checkbox__img-wrapper">
													<img src="<?= $ar["FILE"]["SRC"] ?>" alt="<?= $ar["VALUE"]; ?>" width="24" height="24">
												</div>
											<? endif ?>

											<span>
												<?= $ar["VALUE"]; ?>
												<?
												if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
												?> (<span data-role="count_<?= $ar["CONTROL_ID"] ?>"><?= $ar["ELEMENT_COUNT"]; ?></span>)
												<?
												endif; ?>
											</span>
										</label>
									<? endforeach ?>
								</div>
							<?
								break;
							case SectionPropertyTable::DROPDOWN: //DROPDOWN
								$checkedItemExist = false;
							?>
								<!-- DROPDOWN -->
								<div class="bx-filter-block-wrapper">
									<div class="bx-filter-select" onclick="smartFilter.showDropDownPopup(this, '<?= CUtil::JSEscape($key) ?>')">
										<div class="bx-filter-select-header" data-role="currentOption">
											<? foreach ($arItem["VALUES"] as $val => $ar): ?>
												<? if ($ar["CHECKED"]): ?>
													<span><?= $ar["VALUE"]; ?></span>
												<? $checkedItemExist = true;
												endif; ?>
											<? endforeach; ?>

											<? if (!$checkedItemExist): ?>
												<span><?= GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
											<? endif; ?>

											<!-- <svg width="24" height="24" role="img" aria-hidden="true" focusable="false">
												<use xlink:href="/local/templates/rise-bags/_dist/sprite.svg#chevron"></use>
											</svg> -->
										</div>

										<input
											style="display: none"
											type="radio"
											name="<?= $arCur["CONTROL_NAME_ALT"] ?>"
											id="<?= "all_" . $arCur["CONTROL_ID"] ?>"
											value="" />
										<? foreach ($arItem["VALUES"] as $val => $ar): ?>
											<input
												style="display: none"
												type="radio"
												name="<?= $ar["CONTROL_NAME_ALT"] ?>"
												id="<?= $ar["CONTROL_ID"] ?>"
												value="<?= $ar["HTML_VALUE_ALT"] ?>"
												<?= $ar["CHECKED"] ? 'checked="checked"' : '' ?> />
										<? endforeach ?>

										<div class="bx-filter-select-content" data-role="dropdownContent" style="display: none;">
											<ul>
												<li>
													<label for="<?= "all_" . $arCur["CONTROL_ID"] ?>" class="" data-role="label_<?= "all_" . $arCur["CONTROL_ID"] ?>" onclick="smartFilter.selectDropDownItem(this, '<?= CUtil::JSEscape("all_" . $arCur["CONTROL_ID"]) ?>')">
														<span><?= GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
													</label>
												</li>
												<? foreach ($arItem["VALUES"] as $val => $ar): ?>
													<li>
														<label for="<?= $ar["CONTROL_ID"] ?>" class="<?= $ar["DISABLED"] ? 'disabled' : '' ?>" data-role="label_<?= $ar["CONTROL_ID"] ?>" onclick="smartFilter.selectDropDownItem(this, '<?= CUtil::JSEscape($ar["CONTROL_ID"]) ?>')">
															<span><?= $ar["VALUE"] ?></span>
														</label>
													</li>
												<? endforeach ?>
											</ul>
										</div>
									</div>
								</div>
							<?
								break;
							case SectionPropertyTable::DROPDOWN_WITH_PICTURES_AND_LABELS: //DROPDOWN_WITH_PICTURES_AND_LABELS
								$checkedItemExist = false;
							?>
								<div class="bx-filter-block-wrapper">
									<div class="bx-filter-select" onclick="smartFilter.showDropDownPopup(this, '<?= CUtil::JSEscape($key) ?>')">
										<div class="bx-filter-select-header" data-role="currentOption">

											<? foreach ($arItem["VALUES"] as $val => $ar): ?>
												<? if ($ar["CHECKED"]): ?>
													<? if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])): ?>
														<div class="bx-filter-select-img-wrapper">
															<img src="<?= $ar["FILE"]["SRC"] ?>" alt="<?= $ar["VALUE"]; ?>" width="24" height="24">
														</div>
													<? endif ?>
													<?= $ar["VALUE"] ?>
												<?
													$checkedItemExist = true;
												endif; ?>
											<? endforeach; ?>

											<? if (!$checkedItemExist): ?>
												<span><?= GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
											<? endif; ?>
										</div>

										<input
											style="display: none"
											type="radio"
											name="<?= $arCur["CONTROL_NAME_ALT"] ?>"
											id="<?= "all_" . $arCur["CONTROL_ID"] ?>"
											value="" />
										<? foreach ($arItem["VALUES"] as $val => $ar): ?>
											<input
												style="display: none"
												type="radio"
												name="<?= $ar["CONTROL_NAME_ALT"] ?>"
												id="<?= $ar["CONTROL_ID"] ?>"
												value="<?= $ar["HTML_VALUE_ALT"] ?>"
												<?= $ar["CHECKED"] ? 'checked="checked"' : '' ?> />
										<? endforeach ?>
										<div class="bx-filter-select-content" data-role="dropdownContent" style="display: none">
											<ul>
												<li>
													<label for="<?= "all_" . $arCur["CONTROL_ID"] ?>" class="" data-role="label_<?= "all_" . $arCur["CONTROL_ID"] ?>" onclick="smartFilter.selectDropDownItem(this, '<?= CUtil::JSEscape("all_" . $arCur["CONTROL_ID"]) ?>')">
														<span><?= GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
													</label>
												</li>
												<? foreach ($arItem["VALUES"] as $val => $ar): ?>
													<li>
														<label for="<?= $ar["CONTROL_ID"] ?>" data-role="label_<?= $ar["CONTROL_ID"] ?>" class="<?= $ar["DISABLED"] ? 'disabled' : '' ?>" onclick="smartFilter.selectDropDownItem(this, '<?= CUtil::JSEscape($ar["CONTROL_ID"]) ?>')">
															<? if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])): ?>
																<div class="bx-filter-select-img-wrapper">
																	<img src="<?= $ar["FILE"]["SRC"] ?>" alt="<?= $ar["VALUE"]; ?>" width="24" height="24">
																</div>
															<? endif ?>
															<span><?= $ar["VALUE"] ?></span>
														</label>
													</li>
												<? endforeach ?>
											</ul>
										</div>
									</div>
								</div>
							<?
								break;
							case SectionPropertyTable::RADIO_BUTTONS: //RADIO_BUTTONS
							?>
								<div class="bx-filter-block-wrapper bx-filter-block-wrapper--row">
									<? foreach ($arItem["VALUES"] as $val => $ar): ?>
										<label data-role="label_<?= $ar["CONTROL_ID"] ?>" class="bx-filter-checkbox <?= $ar["DISABLED"] ? 'disabled' : '' ?>">

											<input
												type="radio"
												value="<?= $ar["HTML_VALUE_ALT"] ?>"
												name="<?= $ar["CONTROL_NAME_ALT"] ?>"
												id="<?= $ar["CONTROL_ID"] ?>"
												<?= $ar["CHECKED"] ? 'checked="checked"' : '' ?>
												onclick="smartFilter.click(this)" />
											<span class="bx-filter-param-text" title="<?= $ar["VALUE"]; ?>">
												<?= $ar["VALUE"]; ?><? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?>&nbsp;(<span data-role="count_<?= $ar["CONTROL_ID"] ?>"><?= $ar["ELEMENT_COUNT"]; ?></span>)<? endif; ?>
											</span>
										</label>
									<? endforeach; ?>
									<label class="bx-filter-checkbox bx-filter-checkbox--show-all">
										<input
											type="radio"
											value=""
											name="<?= $arCur["CONTROL_NAME_ALT"] ?>"
											id="<?= "all_" . $arCur["CONTROL_ID"] ?>"
											onclick="smartFilter.click(this)" />
										<span class="bx-filter-param-text" title="<?= $ar["VALUE"]; ?>"><?= GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
									</label>
								</div>

							<?
								break;
							case SectionPropertyTable::CALENDAR: //CALENDAR
							?>
								<div class="bx-filter-block-wrapper">
									<div class="bx-filter-calendar-container">
										<div class="bx-filter-calendar-wrapper">
											<? $APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'',
												array(
													'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
													'SHOW_INPUT' => 'Y',
													'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="' . FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]) . '" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
													'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
													'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
													'SHOW_TIME' => 'N',
													'HIDE_TIMEBAR' => 'Y',
												),
												null,
												array('HIDE_ICONS' => 'Y')
											); ?>
										</div>
										<div class="bx-filter-calendar-wrapper">
											<? $APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'',
												array(
													'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
													'SHOW_INPUT' => 'Y',
													'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="' . FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]) . '" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
													'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
													'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
													'SHOW_TIME' => 'N',
													'HIDE_TIMEBAR' => 'Y',
												),
												null,
												array('HIDE_ICONS' => 'Y')
											); ?>
										</div>
									</div>
								</div>
							<?
								break;
							default: //CHECKBOXES
							?>
								<div class="bx-filter-block-wrapper bx-filter-block-wrapper--row">
									<? foreach ($arItem["VALUES"] as $val => $ar): ?>
										<label class="bx-filter-checkbox" data-role="label_<?= $ar["CONTROL_ID"] ?>" class=" <?= $ar["DISABLED"] ? 'disabled' : '' ?>">
											<input
												type="checkbox"
												value="<?= $ar["HTML_VALUE"] ?>"
												name="<?= $ar["CONTROL_NAME"] ?>"
												id="<?= $ar["CONTROL_ID"] ?>"
												<?= $ar["CHECKED"] ? 'checked="checked"' : '' ?>
												onclick="smartFilter.click(this)" />
											<span><?= $ar["VALUE"]; ?><? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?>&nbsp;(<span data-role="count_<?= $ar["CONTROL_ID"] ?>"><?= $ar["ELEMENT_COUNT"]; ?></span>)<? endif; ?></span>
										</label>
									<? endforeach; ?>
								</div>
						<? endswitch; ?>
					</div>
				</div>
			<? endforeach; ?>
		</div>

		<div class="bx-filter-footer">
			<button
				class="bx-filter-btn"
				type="submit"
				id="set_filter"
				name="set_filter"
				value="<?= GetMessage("CT_BCSF_SET_FILTER") ?>"
				disabled>
				<span>Показать&nbsp;<span id="filter_count"></span></span>
			</button>

			<button
				class="bx-filter-btn outlined"
				type="submit"
				id="del_filter"
				name="del_filter">
				<span>Сбросить</span>
			</button>
		</div>
	</form>
</div>

<div id="modef" style="display: none;">
	<a href="<?= $arResult["FILTER_URL"] ?>" target=""><?= GetMessage("CT_BCSF_FILTER_SHOW") ?></a>
</div>

<?
// Добавил кнопки открытия фильтра в передаваемые скрипту параметры
$arResult["JS_FILTER_PARAMS"]["FILTER_OPENER_IDS"] = [
	'smartfilter_form_opener',
	'smartfilter_sticky_filter_opener',
];
$filterAjaxAction = preg_replace('#^(.*)/filter/.*/apply/?$#', '$1/', $arResult["FORM_ACTION"]);
?>
<script>
	var smartFilter = new JCSmartFilter('<?= CUtil::JSEscape($filterAjaxAction) ?>', <?= CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"]) ?>);
</script>
