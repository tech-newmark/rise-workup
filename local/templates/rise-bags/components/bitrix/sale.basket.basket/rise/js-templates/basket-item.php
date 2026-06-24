<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */

$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);

// $restoreColSpan = 2 + $usePriceInAdditionalColumn + $useSumColumn + $useActionColumn;

?>
<script id="basket-item-template" type="text/html">
	<li class="bx-basket__list-item {{#SHOW_RESTORE}}bx-basket__list-item--removed{{/SHOW_RESTORE}}"
		id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
		{{#SHOW_LOADING}}
			<div class="basket-items-list-item-overlay"></div>
		{{/SHOW_LOADING}}


		{{#SHOW_RESTORE}}
			<div class="bx-basket__list-item-removed">
				<div class="bx-basket__list-item-removed-container">
					<span class="bx-basket__list-item-removed-text">
						<?= Loc::getMessage('SBB_BASKET_ITEM_DELETED_MSGVER_1', ['#NAME#' => '<strong>{{NAME}}</strong>']) ?>
					</span>

					<button type="button" class="main-btn" data-entity="basket-item-restore-button">
						<?= Loc::getMessage('SBB_BASKET_ITEM_RESTORE') ?>
					</button>

					<!-- <button type="button" class="bx-basket__list-item-removed-delete-btn" data-entity="basket-item-close-restore-button">
						Удалить
					</button> -->
				</div>
			</div>
		{{/SHOW_RESTORE}}

		<div class="grid">
			{{#HAS_SIMILAR_ITEMS}}
				<div class="alert alert-line" data-entity="basket-item-sku-notification">
					<span><?= Loc::getMessage('SBB_BASKET_ITEM_SIMILAR_P4') ?> {{SIMILAR_ITEMS_QUANTITY}} {{MEASURE_TEXT}}</span>
					<br>
					<a href="javascript:void(0)" class="basket-items-list-item-double-anchor"
						data-entity="basket-item-merge-sku-link">
						<?= Loc::getMessage('SBB_BASKET_ITEM_SIMILAR_P3') ?>
						{{TOTAL_SIMILAR_ITEMS_QUANTITY}} {{MEASURE_TEXT}}?
					</a>
				</div>
			{{/HAS_SIMILAR_ITEMS}}

			<div class="bx-basket__list-item-section bx-basket__list-item-section--picture">
				<? if (in_array('PREVIEW_PICTURE', $arParams['COLUMNS_LIST'])): ?>

					{{#DETAIL_PAGE_URL}}
						<a href="/catalog/{{DETAIL_PAGE_URL}}">
					{{/DETAIL_PAGE_URL}}

					<img alt="{{NAME}}" src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?= $templateFolder ?>/images/no_photo.png{{/IMAGE_URL}}">

					<? if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y'): ?>
						{{#DISCOUNT_PRICE_PERCENT}}
							<span class="product-label product-label--discount">
								-{{DISCOUNT_PRICE_PERCENT_FORMATED}}
							</span>
						{{/DISCOUNT_PRICE_PERCENT}}
					<? endif; ?>

					{{#DETAIL_PAGE_URL}}
						</a>
					{{/DETAIL_PAGE_URL}}
				<? endif; ?>
			</div>

			<div class="bx-basket__list-item-section bx-basket__list-item-section--main">
				{{#SHOW_LABEL}}
					<div class="bx-basket__list-item-info-row">
						<div class="product-label-container">
							{{#LABEL_VALUES}}
								<span class="product-label product-label--{{TYPE}}" title="{{NAME}}">{{NAME}}</span>
							{{/LABEL_VALUES}}
						</div>
					</div>
				{{/SHOW_LABEL}}

				<div class="bx-basket__list-item-info-row">
					<!-- Название товара -->
					{{#DETAIL_PAGE_URL}}
						<a class="bx-basket__list-item-title" href="/catalog/{{DETAIL_PAGE_URL}}">
					{{/DETAIL_PAGE_URL}}
					{{^DETAIL_PAGE_URL}}
						<h2 class="bx-basket__list-item-title">
					{{/DETAIL_PAGE_URL}}

					<span data-entity="basket-item-name">{{NAME}}</span>

					{{#DETAIL_PAGE_URL}}
						</a>
					{{/DETAIL_PAGE_URL}}
					{{^DETAIL_PAGE_URL}}
						</h2>
					{{/DETAIL_PAGE_URL}}
					<!-- Название товара конец -->
				</div>

				<!-- Предупреждения -->

				{{#NOT_AVAILABLE}}
					<div class="alert alert-line">
						<?= Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE') ?>.
					</div>
				{{/NOT_AVAILABLE}}

				{{#DELAYED}}
					<div class="alert alert-line">
						<span><?= Loc::getMessage('SBB_BASKET_ITEM_DELAYED') ?>.</span>
						<button type="button" class="main-btn main-btn--line" data-entity="basket-item-remove-delayed">
							<?= Loc::getMessage('SBB_BASKET_ITEM_REMOVE_DELAYED') ?>
						</button>
					</div>
				{{/DELAYED}}

				{{#WARNINGS.length}}
					<div class="alert alert-danger" data-entity="basket-item-warning-node">
						<!-- <span class="close" data-entity="basket-item-warning-close">&times;</span> -->
						{{#WARNINGS}}
							<div data-entity="basket-item-warning-text">{{{.}}}</div>
						{{/WARNINGS}}
					</div>
				{{/WARNINGS.length}}

				<!-- Предупреждения -->

				<div class="bx-basket__list-item-info-row">
					<?
					if (!empty($arParams['PRODUCT_BLOCKS_ORDER'])):
						foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $blockName):

							switch (trim((string)$blockName)):
								case 'props':
									if (in_array('PROPS', $arParams['COLUMNS_LIST'])):
					?>
										{{#PROPS}}
											<div class="basket-item-property<?= (!isset($mobileColumns['PROPS']) ? ' hidden-xs' : '') ?>">
												<div class="basket-item-property-name">
													{{{NAME}}}
												</div>
												<div class="basket-item-property-value"
													data-entity="basket-item-property-value" data-property-code="{{CODE}}">
													{{{VALUE}}}
												</div>
											</div>
										{{/PROPS}}
									<?
									endif;

									break;
								case 'sku':
									?>

									{{#SKU_BLOCK_LIST}}
										{{#IS_IMAGE}}
											<div class="bx-basket__list-item-scu-prop sku-prop-block" data-entity="basket-item-sku-block">
												<div class="sku-prop-container">
													<span class="sku-prop-name">{{NAME}}</span>
													<ul class="sku-prop-list">
														{{#SKU_VALUES_LIST}}
															<li
																title="{{NAME}}"
																data-entity="basket-item-sku-field"
																data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
																data-value-id="{{VALUE_ID}}"
																data-sku-name="{{NAME}}"
																data-property="{{PROP_CODE}}"
																class="sku-prop-list-item">

																<button type="button" class="sku-prop-list-item-value {{#SELECTED}}selected{{/SELECTED}} {{#NOT_AVAILABLE_OFFER}}disabled{{/NOT_AVAILABLE_OFFER}}">
																	<img src="{{PICT}}" alt="{{NAME}}" width="40" height="40">
																</button>
															</li>
														{{/SKU_VALUES_LIST}}
													</ul>
												</div>
											</div>
										{{/IS_IMAGE}}

										{{^IS_IMAGE}}
											<div class="bx-basket__list-item-scu-prop sku-prop-block" data-entity="basket-item-sku-block">
												<div class="sku-prop-container">
													<span class="sku-prop-name">{{NAME}}</span>
													<ul class="sku-prop-list">
														{{#SKU_VALUES_LIST}}
															<li
																title="{{NAME}}"
																data-entity="basket-item-sku-field"
																data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
																data-value-id="{{VALUE_ID}}"
																data-sku-name="{{NAME}}"
																data-property="{{PROP_CODE}}"
																class="sku-prop-list-item">

																<button type="button"
																	class="sku-prop-list-item-value {{#SELECTED}}selected{{/SELECTED}} {{#NOT_AVAILABLE_OFFER}}disabled{{/NOT_AVAILABLE_OFFER}}">
																	<span>{{NAME}}</span>
																</button>
															</li>
														{{/SKU_VALUES_LIST}}
													</ul>
												</div>
											</div>
										{{/IS_IMAGE}}
									{{/SKU_BLOCK_LIST}}


								<?
									break;
								case 'columns':
								?>
									{{#COLUMN_LIST}}
										{{#IS_IMAGE}}
											<div class="prop-list-item" data-entity="basket-item-property">
												<span class="prop-list-item-name">{{NAME}}</span>
												<span class="prop-list-item-value">
													{{#VALUE}}
														<img class="prop-list-item-img"
															src="{{{IMAGE_SRC}}}" data-image-index="{{INDEX}}"
															data-column-property-code="{{CODE}}" width="32" height="32" alt="{{NAME}}">
													{{/VALUE}}
												</span>
											</div>
										{{/IS_IMAGE}}

										{{#IS_TEXT}}
											<div class="prop-list-item" data-entity="basket-item-property">
												<span class="prop-list-item-name">{{NAME}}</span>
												<span class="prop-list-item-value"
													data-column-property-code="{{CODE}}"
													data-entity="basket-item-property-column-value">
													{{VALUE}}
												</span>
											</div>
										{{/IS_TEXT}}

										{{#IS_HTML}}
											<div class="prop-list-item" data-entity="basket-item-property">
												<span class="prop-list-item-name">{{NAME}}</span>
												<span class="prop-list-item-value" data-column-property-code="{{CODE}}"
													data-entity="basket-item-property-column-value">
													{{{VALUE}}}
												</span>
											</div>
										{{/IS_HTML}}

										{{#IS_LINK}}
											<div class="prop-list-item" data-entity="basket-item-property">
												<span class="prop-list-item-name">{{NAME}}</span>
												<span class="prop-list-item-value" data-column-property-code="{{CODE}}"
													data-entity="basket-item-property-column-value">
													{{#VALUE}}
														{{{LINK}}}{{^IS_LAST}}<br>{{/IS_LAST}}
													{{/VALUE}}
												</span>
											</div>
										{{/IS_LINK}}
									{{/COLUMN_LIST}}
					<?
									break;
							endswitch;
						endforeach;
					endif;
					?>
				</div>

			</div>


			<div class="bx-basket__list-item-section bx-basket__list-item-section--info">
				<div class="bx-basket__list-item-price-wrapper">
					{{#SHOW_DISCOUNT_PRICE}}
						<span class="bx-basket__list-item-price bx-basket__list-item-price--old">
							{{{FULL_PRICE_FORMATED}}}
						</span>
					{{/SHOW_DISCOUNT_PRICE}}

					<span class="bx-basket__list-item-price bx-basket__list-item-price--current" id="basket-item-price-{{ID}}">
						{{{PRICE_FORMATED}}}
					</span>

					<small class="bx-basket__list-item-price-note">
						*<?= Loc::getMessage(
								'SBB_BASKET_ITEM_PRICE_FOR_MSGVER_1',
								[
									'#MEASURE_RATIO#' => '{{MEASURE_RATIO}}',
									'#MEASURE_TEXT#' => '{{MEASURE_TEXT}}',
								],
							); ?>
					</small>
				</div>

				<div class="bx-basket__list-item-section bx-basket__list-item-section--counter">
					<small class="bx-basket__list-item-price-note bx-basket__list-item-price-note--counter">
						Выбор количества:
					</small>
					<div class="counter{{#NOT_AVAILABLE}}disabled{{/NOT_AVAILABLE}} counter--sm" data-entity="basket-item-quantity-block">
						<button type="button" class="counter-btn counter-btn--dec" data-entity="basket-item-quantity-minus">
							<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
								<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-minus'></use>
							</svg>
						</button>
						<input type="number" value="{{QUANTITY}}"
							{{#NOT_AVAILABLE}} disabled="disabled" {{/NOT_AVAILABLE}}
							data-value="{{QUANTITY}}" data-entity="basket-item-quantity-field"
							id="basket-item-quantity-{{ID}}">
						<button type="button" class="counter-btn counter-btn--inc" data-entity="basket-item-quantity-plus">
							<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
								<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#icon-plus'></use>
							</svg>
						</button>
					</div>
				</div>
			</div>

			<button class="bx-basket__list-item-delete" data-entity="basket-item-delete">
				<svg width='24' height='24' role='img' aria-hidden='true' focusable='false'>
					<use xlink:href='<?= SITE_TEMPLATE_PATH ?>/_dist/sprite.svg#cross-icon'></use>
				</svg>
			</button>
		</div>

	</li>
</script>