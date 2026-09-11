(function () {
	"use strict";

	if (!!window.RiseCatalogFilteredSection) return;

	window.RiseCatalogFilteredSection = function (params) {
		this.formPosting = false;
		this.siteId = params.siteId || "";
		this.ajaxId = params.ajaxId || "";
		this.template = params.template || "";
		this.componentPath = params.componentPath || "";
		this.parameters = params.parameters || "";

		if (params.navParams) {
			this.navParams = {
				NavNum: params.navParams.NavNum || 1,
				NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
				NavPageCount: parseInt(params.navParams.NavPageCount) || 1,
			};
		}

		this.bigData = params.bigData || { enabled: false };
		this.root = document.querySelector(
			'[data-catalog-list="' + params.container + '"]:not([data-list-initialized])',
		);
		if (!this.root) return;
		this.root.dataset.listInitialized = "true";
		this.container = this.root.querySelector('[data-entity="' + params.container + '"]');
		this.error = this.root.querySelector('[data-catalog-list-error]');
		this.showMoreButton = null;
		this.showMoreButtonMessage = null;

		if (
			this.bigData.enabled &&
			BX.util.object_keys(this.bigData.rows).length > 0
		) {
			BX.cookie_prefix = this.bigData.js.cookiePrefix || "";
			BX.cookie_domain = this.bigData.js.cookieDomain || "";
			BX.current_server_time = this.bigData.js.serverTime;

			BX.ready(BX.delegate(this.bigDataLoad, this));
		}

		if (params.initiallyShowHeader) {
			BX.ready(BX.delegate(this.showHeader, this));
		}

		if (params.deferredLoad) {
			BX.ready(BX.delegate(this.deferredLoad, this));
		}

		if (params.lazyLoad) {
			this.showMoreButton = this.root.querySelector('[data-catalog-show-more]');
			if (!this.showMoreButton) return;
			this.showMoreButtonMessage = this.showMoreButton.innerHTML;
			BX.bind(this.showMoreButton, "click", BX.proxy(this.showMore, this));
		}

		if (params.loadOnScroll) {
			BX.bind(window, "scroll", BX.proxy(this.loadOnScroll, this));
		}
	};

	window.RiseCatalogFilteredSection.prototype = {
		checkButton: function () {
			if (
				this.showMoreButton &&
				this.navParams.NavPageNomer == this.navParams.NavPageCount
			) {
				BX.remove(this.showMoreButton);
			}
		},

		enableButton: function () {
			if (this.showMoreButton) {
				BX.removeClass(this.showMoreButton, "disabled");
				this.showMoreButton.disabled = false;
				this.showMoreButton.innerHTML = this.showMoreButtonMessage;
			}
		},

		disableButton: function () {
			if (this.showMoreButton) {
				BX.addClass(this.showMoreButton, "disabled");
				this.showMoreButton.disabled = true;
				this.showMoreButton.innerHTML = BX.message(
					"BTN_MESSAGE_LAZY_LOAD_WAITER",
				);
			}
		},

		loadOnScroll: function () {
			if (!this.root.isConnected || !this.container.getClientRects().length || (this.error && !this.error.hidden)) return;
			var scrollTop = BX.GetWindowScrollPos().scrollTop,
				containerBottom = BX.pos(this.container).bottom;

			if (scrollTop + window.innerHeight > containerBottom) {
				this.showMore();
			}
		},

		showMore: function () {
			if (this.navParams.NavPageNomer < this.navParams.NavPageCount) {
				var data = {};
				data["action"] = "showMore";
				data["PAGEN_" + this.navParams.NavNum] =
					this.navParams.NavPageNomer + 1;

				if (!this.formPosting) {
					this.formPosting = true;
					if (this.error) this.error.hidden = true;
					this.root.setAttribute("aria-busy", "true");
					this.disableButton();
					this.sendRequest(data);
				}
			}
		},

		bigDataLoad: function () {
			// need remove all use this method
		},

		deferredLoad: function () {
			this.sendRequest({ action: "deferredLoad" });
		},

		finishRequest: function (failed) {
			this.formPosting = false;
			this.root.setAttribute("aria-busy", "false");
			this.enableButton();
			if (this.error) this.error.hidden = !failed;
		},

		sendRequest: function (data) {
			var defaultData = {
				siteId: this.siteId,
				template: this.template,
				parameters: this.parameters,
			};
			if (this.ajaxId) defaultData.AJAX_ID = this.ajaxId;

			var settled = false;
			var timer;
			var finish = BX.delegate(function (failed) {
				if (settled) return;
				settled = true;
				clearTimeout(timer);
				this.finishRequest(failed);
			}, this);
			// Also recover if loading a script from the response never completes.
			timer = setTimeout(function () { finish(true); }, 65000);
			try {
				BX.ajax({
					url: this.componentPath + "/ajax.php" +
						(document.location.href.indexOf("clear_cache=Y") !== -1 ? "?clear_cache=Y" : ""),
					method: "POST",
					dataType: "json",
					timeout: 60,
					data: BX.merge(defaultData, data),
					onfailure: function () { finish(true); },
					onsuccess: BX.delegate(function (result) {
						if (settled) return;
						if (!result || typeof result.items !== "string" || !result.items.trim()) {
							finish(true);
							return;
						}
						try {
							BX.ajax.processScripts(BX.processHTML(result.JS || "").SCRIPT, undefined, BX.delegate(function () {
								if (settled) return;
								try {
									this.showAction(result, data);
									finish(false);
								} catch (error) {
									finish(true);
								}
							}, this));
						} catch (error) {
							finish(true);
						}
					}, this),
				});
			} catch (error) {
				finish(true);
			}
		},

		showAction: function (result, data) {
			if (!data) return;

			switch (data.action) {
				case "showMore":
					this.processShowMoreAction(result);
					break;
				case "deferredLoad":
					this.processDeferredLoadAction(result, data.bigData === "Y");
					break;
			}
		},

		processShowMoreAction: function (result) {
			if (result) {
				this.processItems(result.items);
				this.processPagination(result.pagination);
				this.processEpilogue(result.epilogue);
				this.navParams.NavPageNomer++;
				this.checkButton();
			}
		},

		processDeferredLoadAction: function (result, bigData) {
			if (!result) return;

			var position = bigData ? this.bigData.rows : {};

			this.processItems(result.items, BX.util.array_keys(position));
		},

		processItems: function (itemsHtml, position) {
			if (!itemsHtml) return;

			var processed = BX.processHTML(itemsHtml, false),
				temporaryNode = BX.create("DIV");

			var items, k, origRows;

			temporaryNode.innerHTML = processed.HTML;
			if (!temporaryNode.querySelector(".catalog-filtered__grid")) {
				throw new Error("Invalid catalog items response");
			}
			items = temporaryNode.querySelectorAll('[data-entity="items-row"]');

			if (items.length) {
				this.showHeader(true);

				for (k in items) {
					if (items.hasOwnProperty(k)) {
						origRows = position
							? this.container.querySelectorAll('[data-entity="items-row"]')
							: false;
						items[k].style.opacity = 0;

						if (origRows && BX.type.isDomNode(origRows[position[k]])) {
							origRows[position[k]].parentNode.insertBefore(
								items[k],
								origRows[position[k]],
							);
						} else {
							this.container.appendChild(items[k]);
						}
					}
				}

				new BX.easing({
					duration: 2000,
					start: { opacity: 0 },
					finish: { opacity: 100 },
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
					step: function (state) {
						for (var k in items) {
							if (items.hasOwnProperty(k)) {
								items[k].style.opacity = state.opacity / 100;
							}
						}
					},
					complete: function () {
						for (var k in items) {
							if (items.hasOwnProperty(k)) {
								items[k].removeAttribute("style");
							}
						}
					},
				}).animate();
			}

			BX.ajax.processScripts(processed.SCRIPT);
		},

		processPagination: function (paginationHtml) {
			if (!paginationHtml) return;

			var pagination = this.root.querySelectorAll(
				'[data-pagination-num="' + this.navParams.NavNum + '"]',
			);
			for (var k in pagination) {
				if (pagination.hasOwnProperty(k)) {
					pagination[k].innerHTML = paginationHtml;
				}
			}
		},

		processEpilogue: function (epilogueHtml) {
			if (!epilogueHtml) return;

			var processed = BX.processHTML(epilogueHtml, false);
			BX.ajax.processScripts(processed.SCRIPT);
		},

		showHeader: function (animate) {
			var parentNode = BX.findParent(this.container, {
					attr: { "data-entity": "parent-container" },
				}),
				header;

			if (parentNode && BX.type.isDomNode(parentNode)) {
				header = parentNode.querySelector('[data-entity="header"]');

				if (header && header.getAttribute("data-showed") != "true") {
					header.style.display = "";

					if (animate) {
						new BX.easing({
							duration: 2000,
							start: { opacity: 0 },
							finish: { opacity: 100 },
							transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
							step: function (state) {
								header.style.opacity = state.opacity / 100;
							},
							complete: function () {
								header.removeAttribute("style");
								header.setAttribute("data-showed", "true");
							},
						}).animate();
					} else {
						header.style.opacity = 100;
					}
				}
			}
		},
	};
})();
