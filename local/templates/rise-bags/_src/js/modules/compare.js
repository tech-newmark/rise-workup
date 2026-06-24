const compareIds = new Set((window.RiseBagsCompareIds || []).map(Number));

const getProductId = (value) => {
	const productId = Number(value);

	return Number.isFinite(productId) && productId > 0 ? productId : 0;
};

const getCompareName = (button) =>
	button?.dataset.compareName || "CATALOG_COMPARE_LIST";

const getCompareIblockId = (button) => {
	const iblockId = Number(button?.dataset.compareIblockId);

	return Number.isFinite(iblockId) && iblockId > 0 ? iblockId : 0;
};

const syncButtonState = (button, productId) => {
	const isCompared = compareIds.has(productId);
	const checkbox = button.querySelector('[data-entity="compare-checkbox"]');

	button.classList.toggle("active", isCompared);
	button.setAttribute(
		"aria-label",
		isCompared ? "Удалить товар из сравнения" : "Добавить товар в сравнение",
	);

	if (checkbox) {
		checkbox.checked = isCompared;
	}
};

const syncButtonsByProductId = (productId) => {
	document
		.querySelectorAll(`[data-compare-toggle][data-product-id="${productId}"]`)
		.forEach((button) => syncButtonState(button, productId));
};

const syncAllCompareButtons = () => {
	document.querySelectorAll("[data-compare-toggle]").forEach((button) => {
		syncButtonState(button, getProductId(button.dataset.productId));
	});
};

const updateCompareCounter = (count) => {
	const normalizedCount = Number(count) || 0;

	document.querySelectorAll("[data-compare-counter]").forEach((counter) => {
		counter.textContent = String(normalizedCount);
		counter.style.display = normalizedCount > 0 ? "" : "none";
	});
};

window.RiseBagsUpdateCompareCounter = updateCompareCounter;

const replaceCompareIds = (ids) => {
	compareIds.clear();

	(ids || []).map(Number).forEach((productId) => {
		if (getProductId(productId)) {
			compareIds.add(productId);
		}
	});

	window.RiseBagsCompareIds = Array.from(compareIds);
};

const loadCompareStatus = (compareName = "CATALOG_COMPARE_LIST") =>
	fetch(`/local/ajax/compare.php?action=status&compare_name=${encodeURIComponent(compareName)}`, {
		method: "GET",
		headers: {
			"X-Requested-With": "XMLHttpRequest",
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (!data || !data.success) {
				return null;
			}

			replaceCompareIds(data.ids);
			syncAllCompareButtons();
			updateCompareCounter(data.count);

			return data;
		});

window.RiseBagsUpdateCompareButtonState = (button, productId) => {
	const normalizedProductId = getProductId(productId);

	if (!button || !normalizedProductId) {
		return;
	}

	button.dataset.productId = String(normalizedProductId);
	syncButtonState(button, normalizedProductId);
};

window.RiseBagsRefreshCompareState = (compareName = "CATALOG_COMPARE_LIST") =>
	loadCompareStatus(compareName).catch((error) => {
		console.error(error);
	});

const appendAjaxAction = (url) => {
	const separator = url.indexOf("?") === -1 ? "?" : "&";

	return `${url}${separator}ajax_action=Y`;
};

const toggleCompareItem = (button, productId) => {
	const formData = new FormData();

	formData.append("action", "toggle");
	formData.append("product_id", String(productId));
	formData.append("compare_name", getCompareName(button));
	formData.append("iblock_id", String(getCompareIblockId(button)));

	return fetch("/local/ajax/compare.php", {
		method: "POST",
		headers: {
			"X-Requested-With": "XMLHttpRequest",
		},
		body: formData,
	}).then((response) => response.json());
};

document.addEventListener("DOMContentLoaded", () => {
	syncAllCompareButtons();
	loadCompareStatus().catch((error) => {
		console.error(error);
	});
});

document.addEventListener(
	"click",
	(event) => {
		const button = event.target.closest("[data-compare-toggle]");

		if (!button) {
			return;
		}

		const productId = getProductId(button.dataset.productId);

		if (!productId) {
			return;
		}

		if (!getCompareIblockId(button)) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();

		if (button.dataset.loading === "true") {
			return;
		}

		button.dataset.loading = "true";

		toggleCompareItem(button, productId)
			.then((data) => {
				if (!data || !data.success) {
					return;
				}

				replaceCompareIds(data.ids);
				syncAllCompareButtons();
				updateCompareCounter(data.count);

				if (window.BX && BX.onCustomEvent) {
					BX.onCustomEvent("OnCompareChange");
					BX.onCustomEvent(window, "OnCompareChange", []);
				}
			})
			.catch((error) => {
				console.error(error);
			})
			.finally(() => {
				delete button.dataset.loading;
			});
	},
	true,
);
