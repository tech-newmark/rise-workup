<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>

<script id="basket-total-template" type="text/html">
	<div class="basket-total-block__wrapper" data-entity="basket-checkout-aligner">

		<div class="basket-total-block__row">
			<span class="basket-total-block__title"><?= Loc::getMessage('SBB_TOTAL_MSGVER_1') ?></span>
			<div class="basket-total-block__price-wrapper">
				{{#DISCOUNT_PRICE_FORMATED}}
					<span class="basket-total-block__price basket-total-block__price--old">
						{{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
					</span>
				{{/DISCOUNT_PRICE_FORMATED}}

				<span class="basket-total-block__price basket-total-block__price--current" data-entity="basket-total-price">
					{{{PRICE_FORMATED}}}
				</span>
			</div>
		</div>

		{{#WEIGHT_FORMATED}}
			<div class="basket-total-block__row dashed">
				<?= Loc::getMessage('SBB_WEIGHT_MSGVER_1', ['#WEIGHT_FORMATED#' => '<span class="basket-total-block__row-value">{{{WEIGHT_FORMATED}}}</span>']) ?>
			</div>
		{{/WEIGHT_FORMATED}}

		{{#SHOW_VAT}}
			<div class="basket-total-block__row dashed">
				<?= Loc::getMessage('SBB_VAT_MSGVER_1', ['#VAT_SUM_FORMATED#' => '<span class="basket-total-block__row-value">{{{VAT_SUM_FORMATED}}}</span>']) ?>
			</div>
		{{/SHOW_VAT}}

		{{#DISCOUNT_PRICE_FORMATED}}
			<div class="basket-total-block__row dashed">
				<?= Loc::getMessage('SBB_BASKET_ITEM_ECONOMY_MSGVER_1', ['#DISCOUNT_PRICE_FORMATED#' => '<span class="basket-total-block__row-value">{{{DISCOUNT_PRICE_FORMATED}}}</span>']) ?>
			</div>
		{{/DISCOUNT_PRICE_FORMATED}}

		<? if ($arParams['HIDE_COUPON'] !== 'Y'): ?>
			<div class="bx-coupon">
				<div class="basket-coupon-block-field">
					<div class="bx-coupon-block">
						<label class="bx-coupon-input">
							<input placeholder="Есть промокод?" class="form-control main-input" type="text" data-entity="basket-coupon-input">
						</label>

						{{#COUPON_LIST}}
							<span class="bx-coupon-item">
								<strong class="bx-coupon-item-{{CLASS}}">
									<strong>{{COUPON}}</strong>

									<button class="bx-coupon-remove" type="button" aria-label="Удалить промокод" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}"></button>
									<span class="bx-soa-tooltip bx-soa-tooltip-{{CLASS}}"><?= Loc::getMessage('SBB_COUPON') . ' ' ?>{{JS_CHECK_CODE}}</span>
								</strong>
							</span>

						{{/COUPON_LIST}}
					</div>
				</div>
			</div>
		<? endif; ?>
	</div>

	<div class="basket-total-block__footer">
		<!-- <button class="main-btn outlined  {{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}">Купить в 1 клик</button> -->
		<button class="main-btn {{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
			data-entity="basket-checkout-button">
			<?= Loc::getMessage('SBB_ORDER') ?>
		</button>
	</div>
</script>