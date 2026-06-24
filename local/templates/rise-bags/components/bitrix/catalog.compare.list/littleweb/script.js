(function (window) {
	if (!!window.JCCatalogCompareList) {
		return;
	}

	window.JCCatalogCompareList = function (params) {
		this.obCompare = null;
		this.obAdminPanel = null;
		this.visual = params.VISUAL;
		this.ajax = params.AJAX;

		BX.ready(BX.proxy(this.init, this));
	};

	window.JCCatalogCompareList.prototype.init = function () {
		this.obCompare = BX(this.visual.ID);
		if (BX.type.isElementNode(this.obCompare)) {
			BX.addCustomEvent(window, "OnCompareChange", BX.proxy(this.reload, this));
			BX.bindDelegate(
				this.obCompare,
				"click",
				{ tagName: "button" },
				BX.proxy(this.deleteCompare, this),
			);
		}
	};

	window.JCCatalogCompareList.prototype.reload = function () {
		BX.showWait(this.obCompare);
		BX.ajax.post(
			this.ajax.url,
			this.ajax.reload,
			BX.proxy(this.reloadResult, this),
		);
	};

	window.JCCatalogCompareList.prototype.reloadResult = function (result) {
		var isFilled = false;
		BX.closeWait();
		this.obCompare.innerHTML = result;
		if (BX.type.isNotEmptyString(result)) {
			if (result.indexOf("<ul") >= 0) {
				isFilled = true;
			}
		}

		if (!BX.hasClass(this.obCompare, "active")) {
			BX.addClass(this.obCompare, "active");
		}

		if (!isFilled) {
			BX.removeClass(this.obCompare, "active");
		}
	};

	window.JCCatalogCompareList.prototype.deleteCompare = function () {
		var target = BX.proxy_context,
			itemID,
			url;

		if (!!target && target.hasAttribute("data-id")) {
			itemID = parseInt(target.getAttribute("data-id"), 10);
			if (!isNaN(itemID)) {
				BX.showWait(this.obCompare);
				url = this.ajax.url + this.ajax.templates.delete + itemID.toString();
				BX.ajax.loadJSON(
					url,
					this.ajax.params,
					BX.proxy(this.deleteCompareResult, this),
				);
			}
		}
	};

	window.JCCatalogCompareList.prototype.deleteCompareResult = function (
		result,
	) {
		BX.closeWait();

		if (
			!BX.type.isPlainObject(result) ||
			result.STATUS !== "OK" ||
			!result.ID
		) {
			return;
		}

		BX.onCustomEvent("onCatalogDeleteCompare", [result.ID]);

		var listContainer = this.obCompare.querySelector(
			'ul[data-block="item-list"]',
		);
		var targetId = "row" + result.ID;

		if (listContainer) {
			var itemToRemove = Array.from(listContainer.querySelectorAll("li")).find(
				function (item) {
					return item.getAttribute("data-row-id") === targetId;
				},
			);

			if (itemToRemove) {
				BX.remove(itemToRemove);
			}
		}

		if (result.COUNT !== undefined) {
			var newCount = parseInt(result.COUNT, 10);
			if (!isNaN(newCount)) {
				var counterElement = this.obCompare.querySelector(
					'span[data-block="count"]',
				);
				if (counterElement) {
					counterElement.innerHTML = newCount;
				}
				if (window.RiseBagsUpdateCompareCounter) {
					window.RiseBagsUpdateCompareCounter(newCount);
				}

				BX[newCount > 0 ? "addClass" : "removeClass"](this.obCompare, "active");
			}
		}

		if (listContainer && listContainer.querySelectorAll("li").length === 0) {
			this.reload();
		}
	};

	window.JCCatalogCompareList.prototype.setVerticalAlign = function () {
		var topSize;
		if (
			BX.type.isElementNode(this.obCompare) &&
			BX.type.isElementNode(this.obAdminPanel)
		) {
			topSize = parseInt(this.obAdminPanel.offsetHeight, 10);
			if (isNaN(topSize)) {
				topSize = 0;
			}
			topSize += 5;
			BX.style(this.obCompare, "top", topSize.toString() + "px");
		}
	};
})(window);
