const favoriteIds = new Set((window.RiseBagsFavoriteIds || []).map(Number));

const getProductId = (value) => {
	const productId = Number(value);

	return Number.isFinite(productId) && productId > 0 ? productId : 0;
};

const syncButtonState = (button, productId) => {
	const isFavorite = favoriteIds.has(productId);

	button.classList.toggle("active", isFavorite);
	button.setAttribute("aria-pressed", isFavorite ? "true" : "false");
	button.setAttribute(
		"aria-label",
		isFavorite ? "Удалить товар из избранного" : "Добавить товар в избранное",
	);
};

const syncButtonsByProductId = (productId) => {
	document
		.querySelectorAll(`[data-favorite-toggle][data-product-id="${productId}"]`)
		.forEach((button) => syncButtonState(button, productId));
};

const syncAllFavoriteButtons = () => {
	document.querySelectorAll("[data-favorite-toggle]").forEach((button) => {
		syncButtonState(button, getProductId(button.dataset.productId));
	});
};

const updateFavoriteCounter = (count) => {
	const normalizedCount = Number(count) || 0;

	document.querySelectorAll("[data-favorite-counter]").forEach((counter) => {
		counter.textContent = String(normalizedCount);
		counter.style.display = normalizedCount > 0 ? "" : "none";
	});
};

window.RiseBagsUpdateFavoriteCounter = updateFavoriteCounter;

const isFavoritePage = () => {
	const pathname = window.location.pathname.replace(/\/+$/, "");

	return pathname === "/personal/favourite";
};

const getFavoriteItem = (button) =>
	button.closest(".catalog-section-grid-item") || button.closest("[data-favorite-item]");

const removeFavoriteItems = (productId, sourceButton) => {
	const items = new Set();

	if (sourceButton) {
		const sourceItem = getFavoriteItem(sourceButton);

		if (sourceItem) {
			items.add(sourceItem);
		}
	}

	document
		.querySelectorAll(`[data-favorite-toggle][data-product-id="${productId}"]`)
		.forEach((button) => {
			const item = getFavoriteItem(button);

			if (item) {
				items.add(item);
			}
		});

	items.forEach((item) => item.remove());
};

const updateFavoritePageEmptyState = (count) => {
	if (!isFavoritePage()) {
		return;
	}

	const normalizedCount = Number(count) || 0;
	const isEmpty = normalizedCount <= 0;

	document.querySelectorAll("[data-favorite-empty-message]").forEach((message) => {
		message.style.display = isEmpty ? "" : "none";
	});

	document.querySelectorAll("[data-favorite-content]").forEach((content) => {
		content.style.display = isEmpty ? "none" : "";
	});
};

window.RiseBagsUpdateFavoriteButtonState = (button, productId) => {
	const normalizedProductId = getProductId(productId);

	if (!button || !normalizedProductId) {
		return;
	}

	button.dataset.productId = String(normalizedProductId);
	syncButtonState(button, normalizedProductId);
};

const requestFavoriteToggle = (productId) => {
	const formData = new FormData();
	formData.append("product_id", productId);

	if (window.BX && BX.bitrix_sessid) {
		formData.append("sessid", BX.bitrix_sessid());
	}

	return fetch("/local/ajax/favorite.php", {
		method: "POST",
		headers: {
			"X-Requested-With": "XMLHttpRequest",
		},
		body: formData,
	}).then((response) => response.json());
};

const replaceFavoriteIds = (ids) => {
	favoriteIds.clear();

	(ids || []).map(Number).forEach((productId) => {
		if (getProductId(productId)) {
			favoriteIds.add(productId);
		}
	});

	window.RiseBagsFavoriteIds = Array.from(favoriteIds);
};

window.RiseBagsRemoveFavoriteProduct = (productId) => {
	const normalizedProductId = getProductId(productId);

	if (!normalizedProductId || !favoriteIds.has(normalizedProductId)) {
		return Promise.resolve(null);
	}

	return requestFavoriteToggle(normalizedProductId).then((data) => {
		if (!data || !data.success) {
			throw new Error(data && data.message ? data.message : "Favorite error");
		}

		replaceFavoriteIds(data.ids);
		syncButtonsByProductId(normalizedProductId);
		updateFavoriteCounter(data.count);
		updateFavoritePageEmptyState(data.count);

		if (window.BX && BX.onCustomEvent) {
			BX.onCustomEvent(window, "OnBasketChange", [{}]);
		}

		return data;
	});
};

const loadFavoriteStatus = () => {
	fetch("/local/ajax/favorite.php?action=status", {
		method: "GET",
		headers: {
			"X-Requested-With": "XMLHttpRequest",
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (!data || !data.success) {
				return;
			}

			replaceFavoriteIds(data.ids);
			syncAllFavoriteButtons();
			updateFavoriteCounter(data.count);
			updateFavoritePageEmptyState(data.count);
		})
		.catch((error) => {
			console.error(error);
		});
};

document.addEventListener("DOMContentLoaded", () => {
	syncAllFavoriteButtons();
	loadFavoriteStatus();
});

document.addEventListener("click", (event) => {
	const button = event.target.closest("[data-favorite-toggle]");

	if (!button) {
		return;
	}

	event.preventDefault();
	event.stopPropagation();

	if (button.disabled) {
		return;
	}

	const productId = getProductId(button.dataset.productId);

	if (!productId) {
		return;
	}

	button.disabled = true;

	requestFavoriteToggle(productId)
		.then((data) => {
			if (!data || !data.success) {
				throw new Error(data && data.message ? data.message : "Favorite error");
			}

			replaceFavoriteIds(data.ids);
			syncButtonsByProductId(productId);
			updateFavoriteCounter(data.count);

			if (!data.isFavorite) {
				if (isFavoritePage()) {
					removeFavoriteItems(productId, button);
					updateFavoritePageEmptyState(data.count);
				} else {
					button.closest("[data-favorite-item]")?.remove();
				}
			}

			if (window.BX && BX.onCustomEvent) {
				BX.onCustomEvent(window, "OnBasketChange", [{}]);
			}
		})
		.catch((error) => {
			console.error(error);
		})
		.finally(() => {
			button.disabled = false;
		});
});
