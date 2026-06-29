(function (window) {
	"use strict";

	if (window.JCCatalogItem) return;

	var BasketButton = function (params) {
		BasketButton.superclass.constructor.apply(this, arguments);
		this.buttonNode = BX.create("button", {
			props: {
				className: "main-btn",
				id: this.id,
			},
			style: typeof params.style === "object" ? params.style : {},
			text: params.text,
			events: this.contextEvents,
		});

		if (BX.browser.IsIE()) {
			this.buttonNode.setAttribute("hideFocus", "hidefocus");
		}
	};
	BX.extend(BasketButton, BX.PopupWindowButton);

	window.JCCatalogItem = function (arParams) {
		this.swiperInstance = null;
		this.productType = 0;
		this.showQuantity = true;
		this.showAbsent = true;
		// this.secondPict = false;
		this.showOldPrice = false;
		this.showMaxQuantity = "N";
		this.relativeQuantityFactor = 5;
		this.showPercent = false;
		this.showSkuProps = false;
		this.basketAction = "ADD";
		this.showClosePopup = false;
		this.useCompare = false;
		this.showSubscription = false;
		this.visual = {
			ID: "",
			PICT_ID: "",
			// SECOND_PICT_ID: "",
			PICT_SLIDER_ID: "",
			QUANTITY_ID: "",
			QUANTITY_UP_ID: "",
			QUANTITY_DOWN_ID: "",
			PRICE_ID: "",
			PRICE_OLD_ID: "",
			DSC_PERC: "",
			SECOND_DSC_PERC: "",
			DISPLAY_PROP_DIV: "",
			BASKET_PROP_DIV: "",
			SUBSCRIBE_ID: "",
		};
		this.product = {
			checkQuantity: false,
			maxQuantity: 0,
			stepQuantity: 1,
			isDblQuantity: false,
			canBuy: true,
			name: "",
			pict: {},
			id: 0,
			addUrl: "",
			buyUrl: "",
		};

		this.basketMode = "";
		this.basketData = {
			useProps: false,
			emptyProps: false,
			quantity: "quantity",
			props: "prop",
			basketUrl: "",
			sku_props: "",
			sku_props_var: "basket_props",
			add_url: "",
			buy_url: "",
		};

		this.compareData = {
			compareUrl: "",
			compareDeleteUrl: "",
			comparePath: "",
		};

		this.quantityDelay = null;
		this.quantityTimer = null;

		this.checkQuantity = false;
		this.maxQuantity = 0;
		this.minQuantity = 0;
		this.stepQuantity = 1;
		this.isDblQuantity = false;
		this.canBuy = true;
		this.precision = 6;
		this.precisionFactor = Math.pow(10, this.precision);
		this.bigData = false;
		this.fullDisplayMode = false;
		this.viewMode = "";
		this.templateTheme = "";

		this.currentPriceMode = "";
		this.currentPrices = [];
		this.currentPriceSelected = 0;
		this.currentQuantityRanges = [];
		this.currentQuantityRangeSelected = 0;

		this.offers = [];
		this.offerNum = 0;
		this.treeProps = [];
		this.selectedValues = {};

		this.obProduct = null;
		this.blockNodes = {};
		this.obQuantity = null;
		this.obQuantityUp = null;
		this.obQuantityDown = null;
		this.obQuantityLimit = {};
		this.obPict = null;
		// this.obSecondPict = null;
		this.showSlider = arParams.SHOW_SLIDER;
		this.obPictSlider = null;
		// this.obPictSliderIndicator = null;
		this.obPrice = null;
		this.obTree = null;
		this.obBuyBtn = null;
		this.obBasketActions = null;
		this.obNotAvail = null;
		this.obSubscribe = null;
		this.obDscPerc = null;
		this.obSecondDscPerc = null;
		this.obSkuProps = null;
		this.obMeasure = null;
		this.obCompare = null;

		this.obPopupWin = null;
		this.basketUrl = "";
		this.basketParams = {};
		this.isTouchDevice = BX.hasClass(document.documentElement, "bx-touch");
		this.hoverTimer = null;
		this.hoverStateChangeForbidden = false;
		this.mouseX = null;
		this.mouseY = null;

		this.useEnhancedEcommerce = false;
		this.dataLayerName = "dataLayer";
		this.brandProperty = false;

		this.errorCode = 0;

		if (typeof arParams === "object") {
			if (arParams.PRODUCT_TYPE) {
				this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
			}

			this.showQuantity = arParams.SHOW_QUANTITY;
			this.showAbsent = arParams.SHOW_ABSENT;
			// this.secondPict = arParams.SECOND_PICT;
			this.showOldPrice = arParams.SHOW_OLD_PRICE;
			this.showMaxQuantity = arParams.SHOW_MAX_QUANTITY;
			this.relativeQuantityFactor = parseInt(arParams.RELATIVE_QUANTITY_FACTOR);
			this.showPercent = arParams.SHOW_DISCOUNT_PERCENT;
			this.showSkuProps = arParams.SHOW_SKU_PROPS;
			this.showSubscription = arParams.USE_SUBSCRIBE;

			if (arParams.ADD_TO_BASKET_ACTION) {
				this.basketAction = arParams.ADD_TO_BASKET_ACTION;
			}

			this.showClosePopup = arParams.SHOW_CLOSE_POPUP;
			this.useCompare = arParams.DISPLAY_COMPARE;
			this.fullDisplayMode = arParams.PRODUCT_DISPLAY_MODE === "Y";
			this.bigData = arParams.BIG_DATA;
			this.viewMode = arParams.VIEW_MODE || "";
			this.templateTheme = arParams.TEMPLATE_THEME || "";
			this.useEnhancedEcommerce = arParams.USE_ENHANCED_ECOMMERCE === "Y";
			this.dataLayerName = arParams.DATA_LAYER_NAME;
			this.brandProperty = arParams.BRAND_PROPERTY;

			this.visual = arParams.VISUAL;

			switch (this.productType) {
				case 0: // no catalog
				case 1: // product
				case 2: // set
				case 7: // service
					if (arParams.PRODUCT && typeof arParams.PRODUCT === "object") {
						this.currentPriceMode = arParams.PRODUCT.ITEM_PRICE_MODE;
						this.currentPrices = arParams.PRODUCT.ITEM_PRICES;
						this.currentPriceSelected = arParams.PRODUCT.ITEM_PRICE_SELECTED;
						this.currentQuantityRanges = arParams.PRODUCT.ITEM_QUANTITY_RANGES;
						this.currentQuantityRangeSelected =
							arParams.PRODUCT.ITEM_QUANTITY_RANGE_SELECTED;

						if (this.showQuantity) {
							this.product.checkQuantity = arParams.PRODUCT.CHECK_QUANTITY;
							this.product.isDblQuantity = arParams.PRODUCT.QUANTITY_FLOAT;

							if (this.product.checkQuantity) {
								this.product.maxQuantity = this.product.isDblQuantity
									? parseFloat(arParams.PRODUCT.MAX_QUANTITY)
									: parseInt(arParams.PRODUCT.MAX_QUANTITY, 10);
							}

							this.product.stepQuantity = this.product.isDblQuantity
								? parseFloat(arParams.PRODUCT.STEP_QUANTITY)
								: parseInt(arParams.PRODUCT.STEP_QUANTITY, 10);

							this.checkQuantity = this.product.checkQuantity;
							this.isDblQuantity = this.product.isDblQuantity;
							this.stepQuantity = this.product.stepQuantity;
							this.maxQuantity = this.product.maxQuantity;
							this.minQuantity =
								this.currentPriceMode === "Q"
									? parseFloat(
											this.currentPrices[this.currentPriceSelected]
												.MIN_QUANTITY,
										)
									: this.stepQuantity;

							if (this.isDblQuantity) {
								this.stepQuantity =
									Math.round(this.stepQuantity * this.precisionFactor) /
									this.precisionFactor;
							}
						}

						this.product.canBuy = arParams.PRODUCT.CAN_BUY;

						if (arParams.PRODUCT.MORE_PHOTO_COUNT) {
							this.product.morePhotoCount = arParams.PRODUCT.MORE_PHOTO_COUNT;
							this.product.morePhoto = arParams.PRODUCT.MORE_PHOTO;
						}

						if (arParams.PRODUCT.RCM_ID) {
							this.product.rcmId = arParams.PRODUCT.RCM_ID;
						}

						this.canBuy = this.product.canBuy;
						this.product.name = arParams.PRODUCT.NAME;
						this.product.pict = arParams.PRODUCT.PICT;
						this.product.id = arParams.PRODUCT.ID;
						this.product.DETAIL_PAGE_URL = arParams.PRODUCT.DETAIL_PAGE_URL;

						if (arParams.PRODUCT.ADD_URL) {
							this.product.addUrl = arParams.PRODUCT.ADD_URL;
						}

						if (arParams.PRODUCT.BUY_URL) {
							this.product.buyUrl = arParams.PRODUCT.BUY_URL;
						}

						if (arParams.BASKET && typeof arParams.BASKET === "object") {
							this.basketData.useProps = arParams.BASKET.ADD_PROPS;
							this.basketData.emptyProps = arParams.BASKET.EMPTY_PROPS;
						}
					} else {
						this.errorCode = -1;
						console.log("КОД ОШИБКИ:", this.errorCode);
					}

					break;
				case 3: // sku
					if (arParams.PRODUCT && typeof arParams.PRODUCT === "object") {
						this.product.name = arParams.PRODUCT.NAME;
						this.product.id = arParams.PRODUCT.ID;
						this.product.DETAIL_PAGE_URL = arParams.PRODUCT.DETAIL_PAGE_URL;
						this.product.morePhotoCount = arParams.PRODUCT.MORE_PHOTO_COUNT;
						this.product.morePhoto = arParams.PRODUCT.MORE_PHOTO;

						if (arParams.PRODUCT.RCM_ID) {
							this.product.rcmId = arParams.PRODUCT.RCM_ID;
						}
					}

					if (arParams.OFFERS && BX.type.isArray(arParams.OFFERS)) {
						this.offers = arParams.OFFERS;
						this.offers.PREVIEW_PICTURE_SECOND = null;
						this.offerNum = 0;

						if (arParams.OFFER_SELECTED) {
							this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
						}

						if (isNaN(this.offerNum)) {
							this.offerNum = 0;
						}

						if (arParams.TREE_PROPS) {
							this.treeProps = arParams.TREE_PROPS;
						}

						if (arParams.DEFAULT_PICTURE) {
							this.defaultPict = arParams.DEFAULT_PICTURE.PICTURE;
						}
					}

					break;
				default:
					this.errorCode = -1;
			}
			if (arParams.BASKET && typeof arParams.BASKET === "object") {
				if (arParams.BASKET.QUANTITY) {
					this.basketData.quantity = arParams.BASKET.QUANTITY;
				}

				if (arParams.BASKET.PROPS) {
					this.basketData.props = arParams.BASKET.PROPS;
				}

				if (arParams.BASKET.BASKET_URL) {
					this.basketData.basketUrl = arParams.BASKET.BASKET_URL;
				}

				if (3 === this.productType) {
					if (arParams.BASKET.SKU_PROPS) {
						this.basketData.sku_props = arParams.BASKET.SKU_PROPS;
					}
				}

				if (arParams.BASKET.ADD_URL_TEMPLATE) {
					this.basketData.add_url = arParams.BASKET.ADD_URL_TEMPLATE;
				}

				if (arParams.BASKET.BUY_URL_TEMPLATE) {
					this.basketData.buy_url = arParams.BASKET.BUY_URL_TEMPLATE;
				}

				if (this.basketData.add_url === "" && this.basketData.buy_url === "") {
					this.errorCode = -1024;
					console.log("КОД ОШИБКИ:", this.errorCode);
				}
			}

			if (this.useCompare) {
				if (arParams.COMPARE && typeof arParams.COMPARE === "object") {
					if (arParams.COMPARE.COMPARE_PATH) {
						this.compareData.comparePath = arParams.COMPARE.COMPARE_PATH;
					}

					if (arParams.COMPARE.COMPARE_URL_TEMPLATE) {
						this.compareData.compareUrl = arParams.COMPARE.COMPARE_URL_TEMPLATE;
					} else {
						this.useCompare = false;
					}

					if (arParams.COMPARE.COMPARE_DELETE_URL_TEMPLATE) {
						this.compareData.compareDeleteUrl =
							arParams.COMPARE.COMPARE_DELETE_URL_TEMPLATE;
					} else {
						this.useCompare = false;
					}
				} else {
					this.useCompare = false;
				}
			}

			this.isFacebookConversionCustomizeProductEventEnabled =
				arParams.IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED;
		}

		if (this.errorCode === 0) {
			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCCatalogItem.prototype = {
		init: function () {
			var i = 0,
				treeItems = null;

			this.obProduct = BX(this.visual.ID);
			if (!this.obProduct) {
				this.errorCode = -1;
				console.log("КОД ОШИБКИ:", this.errorCode);
			}

			this.obPict = BX(this.visual.PICT_ID);
			this.obPictSlider = BX(this.visual.PICT_SLIDER_ID);
			if (!this.obPict && !this.obPictSlider) {
				this.errorCode = -2;
				console.log("КОД ОШИБКИ:", this.errorCode);
			}

			this.blockNodes.name = this.obProduct.querySelector(
				'[data-entity="name"]',
			);
			this.blockNodes.nameLink = this.obProduct.querySelector(
				'[data-entity="name-link"]',
			);
			this.blockNodes.detailLinks = this.obProduct.querySelectorAll(
				'[data-detail-link="Y"]',
			);

			if (!this.obPictSlider) {
				this.errorCode = -4;
				console.log("КОД ОШИБКИ:", this.errorCode);
			}

			this.obPrice = BX(this.visual.PRICE_ID);
			this.obPriceOld = BX(this.visual.PRICE_OLD_ID);
			this.obPriceTotal = BX(this.visual.PRICE_TOTAL_ID);
			if (!this.obPrice) {
				this.errorCode = -16;
				console.log("КОД ОШИБКИ:", this.errorCode);
			}

			if (this.showQuantity && this.visual.QUANTITY_ID) {
				this.obQuantity = BX(this.visual.QUANTITY_ID);
				this.blockNodes.quantity = this.obProduct.querySelector(
					'[data-entity="quantity-block"]',
				);

				if (!this.isTouchDevice) {
					BX.bind(this.obQuantity, "focus", BX.proxy(this.onFocus, this));
					BX.bind(this.obQuantity, "blur", BX.proxy(this.onBlur, this));
				}

				if (this.visual.QUANTITY_UP_ID) {
					this.obQuantityUp = BX(this.visual.QUANTITY_UP_ID);
				}

				if (this.visual.QUANTITY_DOWN_ID) {
					this.obQuantityDown = BX(this.visual.QUANTITY_DOWN_ID);
				}
			}

			if (this.visual.QUANTITY_LIMIT && this.showMaxQuantity !== "N") {
				this.obQuantityLimit.all = BX(this.visual.QUANTITY_LIMIT);
				if (this.obQuantityLimit.all) {
					this.obQuantityLimit.value = this.obQuantityLimit.all.querySelector(
						'[data-entity="quantity-limit-value"]',
					);
					if (!this.obQuantityLimit.value) {
						this.obQuantityLimit.all = null;
					}
				}
			}

			if (this.productType === 3 && this.fullDisplayMode) {
				if (this.visual.TREE_ID) {
					this.obTree = BX(this.visual.TREE_ID);
					if (!this.obTree) {
						this.errorCode = -256;
						console.log("КОД ОШИБКИ:", this.errorCode);
					}
				}

				if (this.visual.QUANTITY_MEASURE) {
					this.obMeasure = BX(this.visual.QUANTITY_MEASURE);
				}
			}

			this.obBasketActions = BX(this.visual.BASKET_ACTIONS_ID);
			if (this.obBasketActions) {
				if (this.visual.BUY_ID) {
					this.obBuyBtn = BX(this.visual.BUY_ID);
				}
			}

			this.obNotAvail = BX(this.visual.NOT_AVAILABLE_MESS);

			if (this.showSubscription) {
				this.obSubscribe = BX(this.visual.SUBSCRIBE_ID);
			}

			if (this.showPercent) {
				if (this.visual.DSC_PERC) {
					this.obDscPerc = BX(this.visual.DSC_PERC);
				}
			}

			if (this.showSkuProps) {
				if (this.visual.DISPLAY_PROP_DIV) {
					this.obSkuProps = BX(this.visual.DISPLAY_PROP_DIV);
				}
			}
			if (this.errorCode === 0) {
				if (this.bigData) {
					var links = BX.findChildren(this.obProduct, { tag: "a" }, true);
					if (links) {
						for (i in links) {
							if (links.hasOwnProperty(i)) {
								if (
									links[i].getAttribute("href") == this.product.DETAIL_PAGE_URL
								) {
									BX.bind(
										links[i],
										"click",
										BX.proxy(this.rememberProductRecommendation, this),
									);
								}
							}
						}
					}
				}

				if (this.showQuantity) {
					var startEventName = this.isTouchDevice ? "touchstart" : "mousedown";
					var endEventName = this.isTouchDevice ? "touchend" : "mouseup";

					if (this.obQuantityUp) {
						BX.bind(
							this.obQuantityUp,
							startEventName,
							BX.proxy(this.startQuantityInterval, this),
						);
						BX.bind(
							this.obQuantityUp,
							endEventName,
							BX.proxy(this.clearQuantityInterval, this),
						);
						BX.bind(
							this.obQuantityUp,
							"mouseout",
							BX.proxy(this.clearQuantityInterval, this),
						);
						BX.bind(
							this.obQuantityUp,
							"click",
							BX.delegate(this.quantityUp, this),
						);
					}

					if (this.obQuantityDown) {
						BX.bind(
							this.obQuantityDown,
							startEventName,
							BX.proxy(this.startQuantityInterval, this),
						);
						BX.bind(
							this.obQuantityDown,
							endEventName,
							BX.proxy(this.clearQuantityInterval, this),
						);
						BX.bind(
							this.obQuantityDown,
							"mouseout",
							BX.proxy(this.clearQuantityInterval, this),
						);
						BX.bind(
							this.obQuantityDown,
							"click",
							BX.delegate(this.quantityDown, this),
						);
					}

					if (this.obQuantity) {
						BX.bind(
							this.obQuantity,
							"change",
							BX.delegate(this.quantityChange, this),
						);
					}
				}

				switch (this.productType) {
					case 0: // no catalog
					case 1: // product
					case 2: // set
					case 7: // service
						if (this.obPictSlider) {
							this.initializeSlider();
						}

						this.checkQuantityControls();

						break;
					case 3: // sku
						if (this.offers.length > 0) {
							treeItems = BX.findChildren(this.obTree, { tagName: "li" }, true);

							if (treeItems && treeItems.length) {
								for (i = 0; i < treeItems.length; i++) {
									BX.bind(
										treeItems[i],
										"click",
										BX.delegate(this.selectOfferProp, this),
									);
								}
							}
							this.setCurrent();
						} else if (this.obPictSlider) {
							this.initializeSlider();
						}

						break;
				}

				if (this.obBuyBtn) {
					if (this.basketAction === "ADD") {
						BX.bind(this.obBuyBtn, "click", BX.proxy(this.add2Basket, this));
					} else {
						BX.bind(this.obBuyBtn, "click", BX.proxy(this.buyBasket, this));
					}
				}

				if (this.useCompare) {
					this.obCompare = BX(this.visual.COMPARE_LINK_ID);
					if (this.obCompare) {
						BX.bind(this.obCompare, "click", BX.proxy(this.compare, this));
					}

					BX.addCustomEvent(
						"onCatalogDeleteCompare",
						BX.proxy(this.checkDeletedCompare, this),
					);
				}
			}
		},

		setAnalyticsDataLayer: function (action) {
			if (!this.useEnhancedEcommerce || !this.dataLayerName) return;

			var item = {},
				info = {},
				variants = [],
				i,
				k,
				j,
				propId,
				skuId,
				propValues;

			switch (this.productType) {
				case 0: //no catalog
				case 1: //product
				case 2: //set
				case 7: // service
					item = {
						id: this.product.id,
						name: this.product.name,
						price:
							this.currentPrices[this.currentPriceSelected] &&
							this.currentPrices[this.currentPriceSelected].PRICE,
						brand: BX.type.isArray(this.brandProperty)
							? this.brandProperty.join("/")
							: this.brandProperty,
					};
					break;
				case 3: //sku
					for (i in this.offers[this.offerNum].TREE) {
						if (this.offers[this.offerNum].TREE.hasOwnProperty(i)) {
							propId = i.substring(5);
							skuId = this.offers[this.offerNum].TREE[i];

							for (k in this.treeProps) {
								if (
									this.treeProps.hasOwnProperty(k) &&
									this.treeProps[k].ID == propId
								) {
									for (j in this.treeProps[k].VALUES) {
										propValues = this.treeProps[k].VALUES[j];
										if (propValues.ID == skuId) {
											variants.push(propValues.NAME);
											break;
										}
									}
								}
							}
						}
					}

					item = {
						id: this.offers[this.offerNum].ID,
						name: this.offers[this.offerNum].NAME,
						price:
							this.currentPrices[this.currentPriceSelected] &&
							this.currentPrices[this.currentPriceSelected].PRICE,
						brand: BX.type.isArray(this.brandProperty)
							? this.brandProperty.join("/")
							: this.brandProperty,
						variant: variants.join("/"),
					};
					break;
			}

			switch (action) {
				case "addToCart":
					info = {
						ecommerce: {
							currencyCode:
								(this.currentPrices[this.currentPriceSelected] &&
									this.currentPrices[this.currentPriceSelected].CURRENCY) ||
								"",
							add: {
								products: [
									{
										name: item.name || "",
										id: item.id || "",
										price: item.price || 0,
										brand: item.brand || "",
										category: item.category || "",
										variant: item.variant || "",
										quantity:
											this.showQuantity && this.obQuantity
												? this.obQuantity.value
												: 1,
									},
								],
							},
						},
					};
					break;
			}

			window[this.dataLayerName] = window[this.dataLayerName] || [];
			window[this.dataLayerName].push(info);
		},

		getCookie: function (name) {
			var matches = document.cookie.match(
				new RegExp(
					"(?:^|; )" +
						name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") +
						"=([^;]*)",
				),
			);

			return matches ? decodeURIComponent(matches[1]) : null;
		},

		getOfferDetailUrl: function (offerId) {
			var urlParts = this.product.DETAIL_PAGE_URL.split("#"),
				hash = urlParts.length > 1 ? "#" + urlParts.slice(1).join("#") : "",
				url = urlParts[0],
				queryIndex = url.indexOf("?"),
				path = queryIndex === -1 ? url : url.substring(0, queryIndex),
				query = queryIndex === -1 ? "" : url.substring(queryIndex + 1),
				params = query ? query.split("&") : [],
				filteredParams = [],
				i,
				paramName;

			for (i = 0; i < params.length; i++) {
				paramName = params[i].split("=")[0];
				if (decodeURIComponent(paramName) !== "offer") {
					filteredParams.push(params[i]);
				}
			}

			if (
				this.offers &&
				this.offers.length > 0 &&
				parseInt(this.offers[0].ID, 10) === parseInt(offerId, 10)
			) {
				return (
					path +
					(filteredParams.length ? "?" + filteredParams.join("&") : "") +
					hash
				);
			}

			filteredParams.push("offer=" + encodeURIComponent(offerId));

			return path + "?" + filteredParams.join("&") + hash;
		},

		updateDetailLinks: function (offerId) {
			var i, detailUrl;

			if (!this.blockNodes.detailLinks || !this.blockNodes.detailLinks.length) {
				return;
			}

			detailUrl = this.getOfferDetailUrl(offerId);

			for (i = 0; i < this.blockNodes.detailLinks.length; i++) {
				this.blockNodes.detailLinks[i].setAttribute("href", detailUrl);
			}
		},

		rememberProductRecommendation: function () {
			// save to RCM_PRODUCT_LOG
			var cookieName = BX.cookie_prefix + "_RCM_PRODUCT_LOG",
				cookie = this.getCookie(cookieName),
				itemFound = false;

			var cItems = [],
				cItem;

			if (cookie) {
				cItems = cookie.split(".");
			}

			var i = cItems.length;

			while (i--) {
				cItem = cItems[i].split("-");

				if (cItem[0] == this.product.id) {
					// it's already in recommendations, update the date
					cItem = cItems[i].split("-");

					// update rcmId and date
					cItem[1] = this.product.rcmId;
					cItem[2] = BX.current_server_time;

					cItems[i] = cItem.join("-");
					itemFound = true;
				} else {
					if (BX.current_server_time - cItem[2] > 3600 * 24 * 30) {
						cItems.splice(i, 1);
					}
				}
			}

			if (!itemFound) {
				// add recommendation
				cItems.push(
					[this.product.id, this.product.rcmId, BX.current_server_time].join(
						"-",
					),
				);
			}

			// serialize
			var plNewCookie = cItems.join("."),
				cookieDate = new Date(
					new Date().getTime() + 1000 * 3600 * 24 * 365 * 10,
				).toUTCString();

			document.cookie =
				cookieName +
				"=" +
				plNewCookie +
				"; path=/; expires=" +
				cookieDate +
				"; domain=" +
				BX.cookie_domain;
		},

		startQuantityInterval: function () {
			var target = BX.proxy_context;
			var func =
				target.id === this.visual.QUANTITY_DOWN_ID
					? BX.proxy(this.quantityDown, this)
					: BX.proxy(this.quantityUp, this);

			this.quantityDelay = setTimeout(
				BX.delegate(function () {
					this.quantityTimer = setInterval(func, 150);
				}, this),
				300,
			);
		},

		clearQuantityInterval: function () {
			clearTimeout(this.quantityDelay);
			clearInterval(this.quantityTimer);
		},

		quantityUp: function () {
			var curValue = 0,
				boolSet = true;

			if (this.errorCode === 0 && this.showQuantity && this.canBuy) {
				curValue = this.isDblQuantity
					? parseFloat(this.obQuantity.value)
					: parseInt(this.obQuantity.value, 10);
				if (!isNaN(curValue)) {
					curValue += this.stepQuantity;
					if (this.checkQuantity) {
						if (curValue > this.maxQuantity) {
							boolSet = false;
						}
					}

					if (boolSet) {
						if (this.isDblQuantity) {
							curValue =
								Math.round(curValue * this.precisionFactor) /
								this.precisionFactor;
						}

						this.obQuantity.value = curValue;

						this.setPrice();
					}
				}
			}
		},

		quantityDown: function () {
			var curValue = 0,
				boolSet = true;

			if (this.errorCode === 0 && this.showQuantity && this.canBuy) {
				curValue = this.isDblQuantity
					? parseFloat(this.obQuantity.value)
					: parseInt(this.obQuantity.value, 10);
				if (!isNaN(curValue)) {
					curValue -= this.stepQuantity;

					this.checkPriceRange(curValue);

					if (curValue < this.minQuantity) {
						boolSet = false;
					}

					if (boolSet) {
						if (this.isDblQuantity) {
							curValue =
								Math.round(curValue * this.precisionFactor) /
								this.precisionFactor;
						}

						this.obQuantity.value = curValue;

						this.setPrice();
					}
				}
			}
		},

		quantityChange: function () {
			var curValue = 0,
				intCount;

			if (this.errorCode === 0 && this.showQuantity) {
				if (this.canBuy) {
					curValue = this.isDblQuantity
						? parseFloat(this.obQuantity.value)
						: Math.round(this.obQuantity.value);
					if (!isNaN(curValue)) {
						if (this.checkQuantity) {
							if (curValue > this.maxQuantity) {
								curValue = this.maxQuantity;
							}
						}

						this.checkPriceRange(curValue);

						intCount =
							Math.floor(
								Math.round(
									(curValue * this.precisionFactor) / this.stepQuantity,
								) / this.precisionFactor,
							) || 1;
						curValue =
							intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity;
						curValue =
							Math.round(curValue * this.precisionFactor) /
							this.precisionFactor;

						if (curValue < this.minQuantity) {
							curValue = this.minQuantity;
						}

						this.obQuantity.value = curValue;
					} else {
						this.obQuantity.value = this.minQuantity;
					}
				} else {
					this.obQuantity.value = this.minQuantity;
				}

				this.setPrice();
			}
		},

		quantitySet: function (index) {
			var resetQuantity, strLimit;

			var newOffer = this.offers[index],
				oldOffer = this.offers[this.offerNum];

			if (this.errorCode === 0) {
				this.canBuy = newOffer.CAN_BUY;

				this.currentPriceMode = newOffer.ITEM_PRICE_MODE;
				this.currentPrices = newOffer.ITEM_PRICES;
				this.currentPriceSelected = newOffer.ITEM_PRICE_SELECTED;
				this.currentQuantityRanges = newOffer.ITEM_QUANTITY_RANGES;
				this.currentQuantityRangeSelected =
					newOffer.ITEM_QUANTITY_RANGE_SELECTED;

				if (this.canBuy) {
					if (this.blockNodes.quantity) {
						BX.style(this.blockNodes.quantity, "display", "");
					}

					if (this.obBasketActions) {
						BX.style(this.obBasketActions, "display", "");
					}

					if (this.obNotAvail) {
						BX.style(this.obNotAvail, "display", "none");
					}

					if (this.obSubscribe) {
						BX.style(this.obSubscribe, "display", "none");
					}
				} else {
					if (this.blockNodes.quantity) {
						BX.style(this.blockNodes.quantity, "display", "none");
					}

					if (this.obBasketActions) {
						BX.style(this.obBasketActions, "display", "none");
					}

					if (this.obNotAvail) {
						BX.style(this.obNotAvail, "display", "");
					}

					if (this.obSubscribe) {
						if (newOffer.CATALOG_SUBSCRIBE === "Y") {
							BX.style(this.obSubscribe, "display", "");
							this.obSubscribe.setAttribute("data-item", newOffer.ID);
							BX(this.visual.SUBSCRIBE_ID + "_hidden").click();
						} else {
							BX.style(this.obSubscribe, "display", "none");
						}
					}
				}

				this.isDblQuantity = newOffer.QUANTITY_FLOAT;
				this.checkQuantity = newOffer.CHECK_QUANTITY;

				if (this.isDblQuantity) {
					this.stepQuantity =
						Math.round(
							parseFloat(newOffer.STEP_QUANTITY) * this.precisionFactor,
						) / this.precisionFactor;
					this.maxQuantity = parseFloat(newOffer.MAX_QUANTITY);
					this.minQuantity =
						this.currentPriceMode === "Q"
							? parseFloat(
									this.currentPrices[this.currentPriceSelected].MIN_QUANTITY,
								)
							: this.stepQuantity;
				} else {
					this.stepQuantity = parseInt(newOffer.STEP_QUANTITY, 10);
					this.maxQuantity = parseInt(newOffer.MAX_QUANTITY, 10);
					this.minQuantity =
						this.currentPriceMode === "Q"
							? parseInt(
									this.currentPrices[this.currentPriceSelected].MIN_QUANTITY,
								)
							: this.stepQuantity;
				}

				if (this.showQuantity) {
					var isDifferentMinQuantity =
						oldOffer.ITEM_PRICES.length &&
						oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED] &&
						oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED].MIN_QUANTITY !=
							this.minQuantity;

					if (this.isDblQuantity) {
						resetQuantity =
							Math.round(
								parseFloat(oldOffer.STEP_QUANTITY) * this.precisionFactor,
							) /
								this.precisionFactor !==
								this.stepQuantity ||
							isDifferentMinQuantity ||
							oldOffer.MEASURE !== newOffer.MEASURE ||
							(this.checkQuantity &&
								parseFloat(oldOffer.MAX_QUANTITY) > this.maxQuantity &&
								parseFloat(this.obQuantity.value) > this.maxQuantity);
					} else {
						resetQuantity =
							parseInt(oldOffer.STEP_QUANTITY, 10) !== this.stepQuantity ||
							isDifferentMinQuantity ||
							oldOffer.MEASURE !== newOffer.MEASURE ||
							(this.checkQuantity &&
								parseInt(oldOffer.MAX_QUANTITY, 10) > this.maxQuantity &&
								parseInt(this.obQuantity.value, 10) > this.maxQuantity);
					}

					this.obQuantity.disabled = !this.canBuy;

					if (resetQuantity) {
						this.obQuantity.value = this.minQuantity;
					}

					if (this.obMeasure) {
						if (newOffer.MEASURE) {
							BX.adjust(this.obMeasure, { html: newOffer.MEASURE });
						} else {
							BX.adjust(this.obMeasure, { html: "" });
						}
					}
				}

				if (this.obQuantityLimit.all) {
					if (!this.checkQuantity || this.maxQuantity == 0) {
						BX.adjust(this.obQuantityLimit.value, { html: "" });
						BX.adjust(this.obQuantityLimit.all, { style: { display: "none" } });
					} else {
						if (this.showMaxQuantity === "M") {
							strLimit =
								this.maxQuantity / this.stepQuantity >=
								this.relativeQuantityFactor
									? BX.message("RELATIVE_QUANTITY_MANY")
									: BX.message("RELATIVE_QUANTITY_FEW");
						} else {
							strLimit = this.maxQuantity;

							if (newOffer.MEASURE) {
								strLimit += " " + newOffer.MEASURE;
							}
						}

						BX.adjust(this.obQuantityLimit.value, { html: strLimit });
						BX.adjust(this.obQuantityLimit.all, { style: { display: "" } });
					}
				}
			}
		},

		initializeSlider: function () {
			var swiperContainer = this.obPictSlider
				? this.obPictSlider.closest(".swiper")
				: null;

			if (swiperContainer) {
				this.clearSliderHoverAreas(swiperContainer);
			}

			// Если Swiper уже инициализирован, уничтожаем его
			if (this.swiperInstance) {
				this.swiperInstance.destroy(true, true);
				this.swiperInstance = null;
			}

			// Инициализируем новый Swiper
			if (typeof Swiper !== "undefined" && this.obPictSlider) {
				var slideCount =
					this.obPictSlider.querySelectorAll(".swiper-slide").length;

				if (swiperContainer && slideCount > 0) {
					this.swiperInstance = new Swiper(swiperContainer, {
						// Базовые настройки
						slidesPerView: 1,
						spaceBetween: 0,
						loop: slideCount > 1,

						// Пагинация - ищем внутри текущего контейнера
						pagination: {
							el: swiperContainer.parentNode.querySelector(
								".swiper-pagination",
							),
							clickable: true,
						},

						// Навигация (если нужны стрелки)
						// navigation: {
						// 	nextEl: swiperContainer.querySelector('.swiper-button-next'),
						// 	prevEl: swiperContainer.querySelector('.swiper-button-prev'),
						// },
					});

					if (slideCount > 1 && this.canUseSliderHover()) {
						this.initSliderHoverAreas(swiperContainer, slideCount);
					}
				} else if (!swiperContainer) {
					console.error("Swiper container not found");
				}
			}
		},

		canUseSliderHover: function () {
			return (
				!this.isTouchDevice &&
				window.matchMedia &&
				window.matchMedia("(hover: hover) and (pointer: fine)").matches
			);
		},

		clearSliderHoverAreas: function (swiperContainer) {
			var hoverAreas = swiperContainer.querySelector(
				".product-item-slider-hover",
			);

			if (hoverAreas) {
				hoverAreas.parentNode.removeChild(hoverAreas);
			}
		},

		initSliderHoverAreas: function (swiperContainer, slideCount) {
			var hoverAreas = BX.create("div", {
				props: {
					className: "product-item-slider-hover",
				},
				attrs: {
					"aria-hidden": "true",
				},
			});

			hoverAreas.style.setProperty("--product-slider-hover-count", slideCount);

			for (var i = 0; i < slideCount; i++) {
				(function (slideIndex, catalogItem) {
					hoverAreas.appendChild(
						BX.create("span", {
							props: {
								className: "product-item-slider-hover__area",
							},
							attrs: {
								"data-slide-index": slideIndex,
							},
							events: {
								mouseenter: function () {
									if (!catalogItem.swiperInstance) {
										return;
									}

									catalogItem.swiperInstance.slideToLoop(slideIndex, 0);
								},
							},
						}),
					);
				})(i, this);
			}

			swiperContainer.appendChild(hoverAreas);
		},

		selectOfferProp: function () {
			var i = 0,
				value = "",
				strTreeValue = "",
				arTreeItem = [],
				rowItems = null,
				target = BX.proxy_context;

			if (target && target.hasAttribute("data-treevalue")) {
				if (BX.hasClass(target, "selected")) return;

				strTreeValue = target.getAttribute("data-treevalue");
				arTreeItem = strTreeValue.split("_");
				if (this.searchOfferPropIndex(arTreeItem[0], arTreeItem[1])) {
					rowItems = BX.findChildren(
						target.parentNode,
						{ tagName: "li" },
						false,
					);
					if (rowItems && 0 < rowItems.length) {
						for (i = 0; i < rowItems.length; i++) {
							value = rowItems[i].getAttribute("data-onevalue");
							if (value === arTreeItem[1]) {
								BX.addClass(rowItems[i], "selected");
							} else {
								BX.removeClass(rowItems[i], "selected");
							}
						}
					}
				}
			}
		},

		searchOfferPropIndex: function (strPropID, strPropValue) {
			var strName = "",
				arShowValues = false,
				i,
				j,
				arCanBuyValues = [],
				allValues = [],
				index = -1,
				arFilter = {},
				tmpFilter = [];

			for (i = 0; i < this.treeProps.length; i++) {
				if (this.treeProps[i].ID === strPropID) {
					index = i;
					break;
				}
			}

			if (-1 < index) {
				for (i = 0; i < index; i++) {
					strName = "PROP_" + this.treeProps[i].ID;
					arFilter[strName] = this.selectedValues[strName];
				}
				strName = "PROP_" + this.treeProps[index].ID;
				arShowValues = this.getRowValues(arFilter, strName);
				if (!arShowValues) {
					return false;
				}
				if (!BX.util.in_array(strPropValue, arShowValues)) {
					return false;
				}
				arFilter[strName] = strPropValue;
				for (i = index + 1; i < this.treeProps.length; i++) {
					strName = "PROP_" + this.treeProps[i].ID;
					arShowValues = this.getRowValues(arFilter, strName);
					if (!arShowValues) {
						return false;
					}
					allValues = [];
					if (this.showAbsent) {
						arCanBuyValues = [];
						tmpFilter = [];
						tmpFilter = BX.clone(arFilter, true);
						for (j = 0; j < arShowValues.length; j++) {
							tmpFilter[strName] = arShowValues[j];
							allValues[allValues.length] = arShowValues[j];
							if (this.getCanBuy(tmpFilter))
								arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					} else {
						arCanBuyValues = arShowValues;
					}
					if (
						this.selectedValues[strName] &&
						BX.util.in_array(this.selectedValues[strName], arCanBuyValues)
					) {
						arFilter[strName] = this.selectedValues[strName];
					} else {
						if (this.showAbsent)
							arFilter[strName] =
								arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0];
						else arFilter[strName] = arCanBuyValues[0];
					}
					this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
				}
				this.selectedValues = arFilter;
				this.changeInfo();
			}
			return true;
		},

		updateRow: function (intNumber, activeID, showID, canBuyID) {
			var i = 0,
				value = "",
				isCurrent = false,
				rowItems = null;

			var lineContainer = this.obTree.querySelectorAll(
					'[data-entity="sku-line-block"]',
				),
				listContainer;

			if (intNumber > -1 && intNumber < lineContainer.length) {
				listContainer = lineContainer[intNumber].querySelector("ul");
				rowItems = BX.findChildren(listContainer, { tagName: "li" }, false);
				if (rowItems && 0 < rowItems.length) {
					for (i = 0; i < rowItems.length; i++) {
						value = rowItems[i].getAttribute("data-onevalue");
						isCurrent = value === activeID;
						if (isCurrent) {
							BX.addClass(rowItems[i], "selected");
						} else {
							BX.removeClass(rowItems[i], "selected");
						}

						if (BX.util.in_array(value, canBuyID)) {
							BX.removeClass(rowItems[i], "notallowed");
						} else {
							BX.addClass(rowItems[i], "notallowed");
						}

						rowItems[i].style.display = BX.util.in_array(value, showID)
							? ""
							: "none";

						if (isCurrent) {
							lineContainer[intNumber].style.display =
								value == 0 && canBuyID.length == 1 ? "none" : "";
						}
					}
				}
			}
		},

		getRowValues: function (arFilter, index) {
			var i = 0,
				j,
				arValues = [],
				boolSearch = false,
				boolOneSearch = true;

			if (0 === arFilter.length) {
				for (i = 0; i < this.offers.length; i++) {
					if (!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
						arValues[arValues.length] = this.offers[i].TREE[index];
					}
				}
				boolSearch = true;
			} else {
				for (i = 0; i < this.offers.length; i++) {
					boolOneSearch = true;
					for (j in arFilter) {
						if (arFilter[j] !== this.offers[i].TREE[j]) {
							boolOneSearch = false;
							break;
						}
					}
					if (boolOneSearch) {
						if (!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
							arValues[arValues.length] = this.offers[i].TREE[index];
						}
						boolSearch = true;
					}
				}
			}
			return boolSearch ? arValues : false;
		},

		getCanBuy: function (arFilter) {
			var i,
				j,
				boolSearch = false,
				boolOneSearch = true;

			for (i = 0; i < this.offers.length; i++) {
				boolOneSearch = true;
				for (j in arFilter) {
					if (arFilter[j] !== this.offers[i].TREE[j]) {
						boolOneSearch = false;
						break;
					}
				}
				if (boolOneSearch) {
					if (this.offers[i].CAN_BUY) {
						boolSearch = true;
						break;
					}
				}
			}

			return boolSearch;
		},

		setCurrent: function () {
			var i,
				j = 0,
				arCanBuyValues = [],
				strName = "",
				arShowValues = false,
				arFilter = {},
				tmpFilter = [],
				current = this.offers[this.offerNum].TREE;

			for (i = 0; i < this.treeProps.length; i++) {
				strName = "PROP_" + this.treeProps[i].ID;
				arShowValues = this.getRowValues(arFilter, strName);
				if (!arShowValues) {
					break;
				}
				if (BX.util.in_array(current[strName], arShowValues)) {
					arFilter[strName] = current[strName];
				} else {
					arFilter[strName] = arShowValues[0];
					this.offerNum = 0;
				}
				if (this.showAbsent) {
					arCanBuyValues = [];
					tmpFilter = [];
					tmpFilter = BX.clone(arFilter, true);
					for (j = 0; j < arShowValues.length; j++) {
						tmpFilter[strName] = arShowValues[j];
						if (this.getCanBuy(tmpFilter)) {
							arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					}
				} else {
					arCanBuyValues = arShowValues;
				}
				this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
			}
			this.selectedValues = arFilter;
			this.changeInfo();
		},

		changeInfo: function () {
			var i,
				j,
				index = -1,
				boolOneSearch = true,
				quantityChanged;

			for (i = 0; i < this.offers.length; i++) {
				boolOneSearch = true;
				for (j in this.selectedValues) {
					if (this.selectedValues[j] !== this.offers[i].TREE[j]) {
						boolOneSearch = false;
						break;
					}
				}
				if (boolOneSearch) {
					index = i;
					break;
				}
			}
			if (index > -1) {
				const btns = this.obProduct.querySelectorAll("[data-1clickbuy-id]");
				btns.forEach((btn) => {
					btn.setAttribute("data-1clickbuy-id", this.offers[index].ID);
				});
				this.updateDetailLinks(this.offers[index].ID);

				if (this.obPictSlider) {
					var offer = this.offers[index];
					var slides = [];

					if (this.showSlider === "Y" && offer.MORE_PHOTO.length) {
						for (i in offer.MORE_PHOTO) {
							if (offer.MORE_PHOTO[i].SRC) {
								slides.push(offer.MORE_PHOTO[i]);
							}
						}
					} else if (this.showSlider === "N" && offer.MORE_PHOTO.length) {
						slides.push(offer.MORE_PHOTO[0]);
					} else if (!offer.MORE_PHOTO.length) {
						slides.push(this.defaultPict);
					}

					BX.cleanNode(this.obPictSlider);

					if (slides.length > 0) {
						for (i = 0; i < slides.length; i++) {
							this.obPictSlider.appendChild(
								BX.create("div", {
									props: {
										className: "swiper-slide",
									},
									children: [
										BX.create("img", {
											props: {
												src: slides[i].SRC,
												alt: this.product.name || "product image",
											},
											attrs: {
												loading: "lazy",
											},
										}),
									],
								}),
							);
						}
						this.obPictSlider.style.display = "";
					} else {
						if (this.swiperInstance) {
							this.swiperInstance.destroy(true, true);
							this.swiperInstance = null;
						}
					}
					this.initializeSlider();
				}

				if (this.showSkuProps && this.obSkuProps) {
					if (this.offers[index].DISPLAY_PROPERTIES.length) {
						BX.adjust(this.obSkuProps, {
							style: { display: "" },
							html: this.offers[index].DISPLAY_PROPERTIES,
						});
					} else {
						BX.adjust(this.obSkuProps, {
							style: { display: "none" },
							html: "",
						});
					}
				}

				if (this.blockNodes.name && this.offers[index].NAME) {
					BX.adjust(this.blockNodes.name, {
						text: this.offers[index].NAME,
						attrs: {
							title: this.offers[index].NAME,
						},
					});
				}

				if (this.blockNodes.nameLink && this.offers[index].NAME) {
					BX.adjust(this.blockNodes.nameLink, {
						attrs: {
							title: this.offers[index].NAME,
						},
					});
				}

				this.quantitySet(index);
				this.setPrice();
				this.setCompared(this.offers[index].COMPARED);

				this.offerNum = index;
				this.updateFavoriteButton(this.offers[index].ID);
				this.updateCompareButton(
					this.offers[index].ID,
					this.offers[index].COMPARED,
				);
			}
		},

		updateFavoriteButton: function (productId) {
			var buttons = this.obProduct
				? this.obProduct.querySelectorAll("[data-favorite-toggle]")
				: [];

			for (var i = 0; i < buttons.length; i++) {
				buttons[i].setAttribute("data-product-id", productId);

				if (window.RiseBagsUpdateFavoriteButtonState) {
					window.RiseBagsUpdateFavoriteButtonState(buttons[i], productId);
				}
			}
		},

		updateCompareButton: function (productId, isCompared) {
			var buttons = this.obProduct
				? this.obProduct.querySelectorAll("[data-compare-toggle]")
				: [];

			for (var i = 0; i < buttons.length; i++) {
				var oldProductId = buttons[i].getAttribute("data-product-id");
				var oldProductIdRegexp = oldProductId
					? new RegExp("([?&]id=)" + oldProductId + "($|&)")
					: null;

				buttons[i].setAttribute("data-product-id", productId);

				if (this.compareData.compareUrl) {
					var compareAddUrl =
						this.compareData.compareUrl.indexOf("#ID#") !== -1
							? this.compareData.compareUrl.replace("#ID#", productId)
							: this.compareData.compareUrl;

					if (oldProductIdRegexp) {
						compareAddUrl = compareAddUrl.replace(
							oldProductIdRegexp,
							"$1" + productId + "$2",
						);
					}

					buttons[i].setAttribute("data-compare-add-url", compareAddUrl);
				} else if (oldProductIdRegexp && buttons[i].dataset.compareAddUrl) {
					buttons[i].setAttribute(
						"data-compare-add-url",
						buttons[i].dataset.compareAddUrl.replace(
							oldProductIdRegexp,
							"$1" + productId + "$2",
						),
					);
				}

				if (this.compareData.compareDeleteUrl) {
					var compareDeleteUrl =
						this.compareData.compareDeleteUrl.indexOf("#ID#") !== -1
							? this.compareData.compareDeleteUrl.replace("#ID#", productId)
							: this.compareData.compareDeleteUrl;

					if (oldProductIdRegexp) {
						compareDeleteUrl = compareDeleteUrl.replace(
							oldProductIdRegexp,
							"$1" + productId + "$2",
						);
					}

					buttons[i].setAttribute("data-compare-delete-url", compareDeleteUrl);
				} else if (oldProductIdRegexp && buttons[i].dataset.compareDeleteUrl) {
					buttons[i].setAttribute(
						"data-compare-delete-url",
						buttons[i].dataset.compareDeleteUrl.replace(
							oldProductIdRegexp,
							"$1" + productId + "$2",
						),
					);
				}

				if (window.RiseBagsUpdateCompareButtonState) {
					window.RiseBagsUpdateCompareButtonState(buttons[i], productId);
				} else {
					var checkbox = buttons[i].querySelector(
						'[data-entity="compare-checkbox"]',
					);

					if (checkbox) {
						checkbox.checked = !!isCompared;
					}
				}
			}
		},

		checkPriceRange: function (quantity) {
			if (typeof quantity === "undefined" || this.currentPriceMode != "Q")
				return;

			var range,
				found = false;

			for (var i in this.currentQuantityRanges) {
				if (this.currentQuantityRanges.hasOwnProperty(i)) {
					range = this.currentQuantityRanges[i];

					if (
						parseInt(quantity) >= parseInt(range.SORT_FROM) &&
						(range.SORT_TO == "INF" ||
							parseInt(quantity) <= parseInt(range.SORT_TO))
					) {
						found = true;
						this.currentQuantityRangeSelected = range.HASH;
						break;
					}
				}
			}

			if (!found && (range = this.getMinPriceRange())) {
				this.currentQuantityRangeSelected = range.HASH;
			}

			for (var k in this.currentPrices) {
				if (this.currentPrices.hasOwnProperty(k)) {
					if (
						this.currentPrices[k].QUANTITY_HASH ==
						this.currentQuantityRangeSelected
					) {
						this.currentPriceSelected = k;
						break;
					}
				}
			}
		},

		getMinPriceRange: function () {
			var range;

			for (var i in this.currentQuantityRanges) {
				if (this.currentQuantityRanges.hasOwnProperty(i)) {
					if (
						!range ||
						parseInt(this.currentQuantityRanges[i].SORT_FROM) <
							parseInt(range.SORT_FROM)
					) {
						range = this.currentQuantityRanges[i];
					}
				}
			}

			return range;
		},

		checkQuantityControls: function () {
			if (!this.obQuantity) return;

			var reachedTopLimit =
					this.checkQuantity &&
					parseFloat(this.obQuantity.value) + this.stepQuantity >
						this.maxQuantity,
				reachedBottomLimit =
					parseFloat(this.obQuantity.value) - this.stepQuantity <
					this.minQuantity;

			if (reachedTopLimit) {
				BX.addClass(this.obQuantityUp, "disabled");
			} else if (BX.hasClass(this.obQuantityUp, "disabled")) {
				BX.removeClass(this.obQuantityUp, "disabled");
			}

			if (reachedBottomLimit) {
				BX.addClass(this.obQuantityDown, "disabled");
			} else if (BX.hasClass(this.obQuantityDown, "disabled")) {
				BX.removeClass(this.obQuantityDown, "disabled");
			}

			if (reachedTopLimit && reachedBottomLimit) {
				this.obQuantity.setAttribute("disabled", "disabled");
			} else {
				this.obQuantity.removeAttribute("disabled");
			}
		},

		formatPrice: function (value, currency) {
			if (
				typeof BX !== "undefined" &&
				BX.Currency &&
				typeof BX.Currency.currencyFormat === "function"
			) {
				return BX.Currency.currencyFormat(value, currency, true);
			}

			var numericValue = parseFloat(value);
			if (!isFinite(numericValue)) {
				return value;
			}

			return currency ? numericValue + " " + currency : String(numericValue);
		},

		setPrice: function () {
			var obData, price;

			if (this.obQuantity) {
				this.checkPriceRange(this.obQuantity.value);
			}

			this.checkQuantityControls();

			price = this.currentPrices[this.currentPriceSelected];

			if (this.obPrice) {
				if (price) {
					BX.adjust(this.obPrice, {
						html: this.formatPrice(price.RATIO_PRICE, price.CURRENCY),
					});
				} else {
					BX.adjust(this.obPrice, { html: "" });
				}

				if (this.showOldPrice && this.obPriceOld) {
					if (price && price.RATIO_PRICE !== price.RATIO_BASE_PRICE) {
						BX.adjust(this.obPriceOld, {
							style: { display: "" },
							html: this.formatPrice(price.RATIO_BASE_PRICE, price.CURRENCY),
						});
					} else {
						BX.adjust(this.obPriceOld, {
							style: { display: "none" },
							html: "",
						});
					}
				}

				if (this.obPriceTotal) {
					if (
						price &&
						this.obQuantity &&
						this.obQuantity.value != this.stepQuantity
					) {
						BX.adjust(this.obPriceTotal, {
							html:
								BX.message("PRICE_TOTAL_PREFIX") +
								" <strong>" +
								this.formatPrice(
									price.PRICE * this.obQuantity.value,
									price.CURRENCY,
								) +
								"</strong>",
							style: { display: "" },
						});
					} else {
						BX.adjust(this.obPriceTotal, {
							html: "",
							style: { display: "none" },
						});
					}
				}

				if (this.showPercent) {
					if (price && parseInt(price.PERCENT) > 0) {
						obData = { style: { display: "" }, html: -price.PERCENT + "%" };
					} else {
						obData = { style: { display: "none" }, html: "" };
					}

					if (this.obDscPerc) {
						BX.adjust(this.obDscPerc, obData);
					}

					if (this.obSecondDscPerc) {
						BX.adjust(this.obSecondDscPerc, obData);
					}
				}
			}
		},

		compare: function (event) {
			var checkbox = this.obCompare.querySelector(
					'[data-entity="compare-checkbox"]',
				),
				target = BX.getEventTarget(event),
				checked = true;

			if (checkbox) {
				checked = target === checkbox ? checkbox.checked : !checkbox.checked;
			}

			var url = checked
					? this.compareData.compareUrl
					: this.compareData.compareDeleteUrl,
				compareLink;

			if (url) {
				if (target !== checkbox) {
					BX.PreventDefault(event);
					this.setCompared(checked);
				}

				switch (this.productType) {
					case 0: // no catalog
					case 1: // product
					case 2: // set
					case 7: // service
						compareLink = url.replace("#ID#", this.product.id.toString());
						break;
					case 3: // sku
						compareLink = url.replace("#ID#", this.offers[this.offerNum].ID);
						break;
				}

				BX.ajax({
					method: "POST",
					dataType: checked ? "json" : "html",
					url:
						compareLink +
						(compareLink.indexOf("?") !== -1 ? "&" : "?") +
						"ajax_action=Y",
					onsuccess: checked
						? BX.proxy(this.compareResult, this)
						: BX.proxy(this.compareDeleteResult, this),
				});
			}
		},

		compareResult: function (result) {
			if (!BX.type.isPlainObject(result)) return;

			if (this.offers.length > 0) {
				this.offers[this.offerNum].COMPARED = result.STATUS === "OK";
			}

			if (result.STATUS === "OK") {
				BX.onCustomEvent("OnCompareChange");
				if (window.RiseBagsRefreshCompareState) {
					window.RiseBagsRefreshCompareState();
				}
				// Если нужно показать какое то окно или уведомление, то пишем тут
			}
		},

		compareDeleteResult: function () {
			BX.onCustomEvent("OnCompareChange");
			if (window.RiseBagsRefreshCompareState) {
				window.RiseBagsRefreshCompareState();
			}

			if (this.offers && this.offers.length) {
				this.offers[this.offerNum].COMPARED = false;
			}
		},

		setCompared: function (state) {
			if (!this.obCompare) return;

			var checkbox = this.obCompare.querySelector(
				'[data-entity="compare-checkbox"]',
			);
			if (checkbox) {
				checkbox.checked = state;
			}
		},

		setCompareInfo: function (comparedIds) {
			if (!BX.type.isArray(comparedIds) || !BX.type.isArray(this.offers))
				return;
			for (var i = 0; i < this.offers.length; i++) {
				if (this.offers[i] && this.offers[i].ID) {
					this.offers[i].COMPARED = BX.util.in_array(
						this.offers[i].ID,
						comparedIds,
					);
				}
			}
		},

		compareRedirect: function () {
			if (this.compareData.comparePath) {
				location.href = this.compareData.comparePath;
			} else {
				this.obPopupWin.close();
			}
		},

		checkDeletedCompare: function (id) {
			switch (this.productType) {
				case 0: // no catalog
				case 1: // product
				case 2: // set
				case 7: // service
					if (this.product.id == id) {
						this.setCompared(false);
					}

					break;
				case 3: // sku
					var i = this.offers.length;
					while (i--) {
						if (this.offers[i].ID == id) {
							this.offers[i].COMPARED = false;

							if (this.offerNum == i) {
								this.setCompared(false);
							}

							break;
						}
					}
			}
		},

		initBasketUrl: function () {
			this.basketUrl =
				this.basketMode === "ADD"
					? this.basketData.add_url
					: this.basketData.buy_url;
			switch (this.productType) {
				case 1: // product
				case 2: // set
				case 7: // service
					this.basketUrl = this.basketUrl.replace(
						"#ID#",
						this.product.id.toString(),
					);
					break;
				case 3: // sku
					this.basketUrl = this.basketUrl.replace(
						"#ID#",
						this.offers[this.offerNum].ID,
					);
					break;
			}
			this.basketParams = {
				ajax_basket: "Y",
			};
			if (this.showQuantity) {
				this.basketParams[this.basketData.quantity] = this.obQuantity.value;
			}
			if (this.basketData.sku_props) {
				this.basketParams[this.basketData.sku_props_var] =
					this.basketData.sku_props;
			}
		},

		fillBasketProps: function () {
			if (!this.visual.BASKET_PROP_DIV) {
				return;
			}
			var i = 0,
				propCollection = null,
				foundValues = false,
				obBasketProps = null;

			if (this.basketData.useProps && !this.basketData.emptyProps) {
				if (this.obPopupWin && this.obPopupWin.contentContainer) {
					obBasketProps = this.obPopupWin.contentContainer;
				}
			} else {
				obBasketProps = BX(this.visual.BASKET_PROP_DIV);
			}
			if (obBasketProps) {
				propCollection = obBasketProps.getElementsByTagName("select");
				if (propCollection && propCollection.length) {
					for (i = 0; i < propCollection.length; i++) {
						if (!propCollection[i].disabled) {
							switch (propCollection[i].type.toLowerCase()) {
								case "select-one":
									this.basketParams[propCollection[i].name] =
										propCollection[i].value;
									foundValues = true;
									break;
								default:
									break;
							}
						}
					}
				}
				propCollection = obBasketProps.getElementsByTagName("input");
				if (propCollection && propCollection.length) {
					for (i = 0; i < propCollection.length; i++) {
						if (!propCollection[i].disabled) {
							switch (propCollection[i].type.toLowerCase()) {
								case "hidden":
									this.basketParams[propCollection[i].name] =
										propCollection[i].value;
									foundValues = true;
									break;
								case "radio":
									if (propCollection[i].checked) {
										this.basketParams[propCollection[i].name] =
											propCollection[i].value;
										foundValues = true;
									}
									break;
								default:
									break;
							}
						}
					}
				}
			}
			if (!foundValues) {
				this.basketParams[this.basketData.props] = [];
				this.basketParams[this.basketData.props][0] = 0;
			}
		},

		add2Basket: function () {
			this.basketMode = "ADD";
			this.basket();
		},

		getBasketProductId: function () {
			if (this.productType === 3 && this.offers[this.offerNum]) {
				return this.offers[this.offerNum].ID;
			}

			return this.product.id;
		},

		removeFavoriteAfterBasketAdd: function () {
			if (window.RiseBagsRemoveFavoriteProduct) {
				window
					.RiseBagsRemoveFavoriteProduct(this.getBasketProductId())
					.catch(function (error) {
						console.error(error);
					});
			}
		},

		buyBasket: function () {
			this.basketMode = "BUY";
			this.basket();
		},

		sendToBasket: function () {
			if (!this.canBuy) {
				return;
			}

			// check recommendation
			if (this.product && this.product.id && this.bigData) {
				this.rememberProductRecommendation();
			}

			this.initBasketUrl();
			this.fillBasketProps();
			BX.ajax({
				method: "POST",
				dataType: "json",
				url: this.basketUrl,
				data: this.basketParams,
				onsuccess: BX.proxy(this.basketResult, this),
			});
		},

		basket: function () {
			var contentBasketProps = "";
			if (!this.canBuy) {
				return;
			}
			switch (this.productType) {
				case 1: // product
				case 2: // set
				case 7: // service
					if (this.basketData.useProps && !this.basketData.emptyProps) {
						this.initPopupWindow();
						this.obPopupWin.setTitleBar(BX.message("TITLE_BASKET_PROPS"));
						if (BX(this.visual.BASKET_PROP_DIV)) {
							contentBasketProps = BX(this.visual.BASKET_PROP_DIV).innerHTML;
						}
						this.obPopupWin.setContent(contentBasketProps);
						this.obPopupWin.setButtons([
							new BasketButton({
								text: BX.message("BTN_MESSAGE_SEND_PROPS"),
								events: {
									click: BX.delegate(this.sendToBasket, this),
								},
							}),
						]);
						this.obPopupWin.show();
					} else {
						this.sendToBasket();
					}
					break;
				case 3: // sku
					this.sendToBasket();
					break;
			}
		},

		basketResult: function (arResult) {
			var strContent = "",
				strPict = "",
				successful,
				buttons = [];

			if (this.obPopupWin) this.obPopupWin.close();

			if (!BX.type.isPlainObject(arResult)) return;

			successful = arResult.STATUS === "OK";

			if (successful) {
				this.setAnalyticsDataLayer("addToCart");
			}

			if (successful && this.basketAction === "BUY") {
				this.basketRedirect();
			} else {
				this.initPopupWindow();

				if (successful) {
					BX.onCustomEvent("OnBasketChange");
					this.removeFavoriteAfterBasketAdd();

					if (
						BX.findParent(
							this.obProduct,
							{ className: "bx_sale_gift_main_products" },
							10,
						)
					) {
						BX.onCustomEvent("onAddToBasketMainProduct", [this]);
					}

					switch (this.productType) {
						case 1: // product
						case 2: // set
						case 7: // service
							strPict = this.product.pict.SRC;
							break;
						case 3: // sku
							strPict = this.offers[this.offerNum].PREVIEW_PICTURE
								? this.offers[this.offerNum].PREVIEW_PICTURE.SRC
								: this.defaultPict;
							break;
					}

					strContent = "<p>" + this.product.name + "</p>";

					if (this.showClosePopup) {
						buttons = [
							new BasketButton({
								text: BX.message("BTN_MESSAGE_BASKET_REDIRECT"),
								events: {
									click: BX.delegate(this.basketRedirect, this),
								},
								style: { marginRight: "10px" },
							}),
							new BasketButton({
								text: BX.message("BTN_MESSAGE_CLOSE_POPUP"),
								events: {
									click: BX.delegate(this.obPopupWin.close, this.obPopupWin),
								},
							}),
						];
					} else {
						buttons = [
							new BasketButton({
								text: BX.message("BTN_MESSAGE_BASKET_REDIRECT"),
								events: {
									click: BX.delegate(this.basketRedirect, this),
								},
							}),
						];
					}
				} else {
					strContent =
						"<p>" +
						(arResult.MESSAGE
							? arResult.MESSAGE
							: BX.message("BASKET_UNKNOWN_ERROR")) +
						"</p>";
					buttons = [
						new BasketButton({
							text: BX.message("BTN_MESSAGE_CLOSE"),
							events: {
								click: BX.delegate(this.obPopupWin.close, this.obPopupWin),
							},
						}),
					];
				}
				this.obPopupWin.setTitleBar(
					successful
						? BX.message("TITLE_SUCCESSFUL")
						: BX.message("TITLE_ERROR"),
				);
				this.obPopupWin.setContent(strContent);
				this.obPopupWin.setButtons(buttons);
				this.obPopupWin.show();
			}
		},

		basketRedirect: function () {
			location.href = this.basketData.basketUrl
				? this.basketData.basketUrl
				: BX.message("BASKET_URL");
		},

		initPopupWindow: function () {
			if (this.obPopupWin) return;

			this.obPopupWin = BX.PopupWindowManager.create(
				"CatalogSectionBasket_" + this.visual.ID,
				null,
				{
					autoHide: true,
					offsetLeft: 0,
					offsetTop: 0,
					overlay: true,
					closeByEsc: true,
					titleBar: true,
					closeIcon: true,
					contentColor: "white",
					className: this.templateTheme ? "bx-" + this.templateTheme : "",
				},
			);
		},
	};
})(window);
