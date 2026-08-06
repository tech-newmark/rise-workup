function initRating() {
	document.querySelectorAll(".rate").forEach((rate) => {
		const items = rate.querySelectorAll('input[type="radio"]');

		const fillStars = (index) => {
			items.forEach((item, i) => {
				item.parentNode.classList.toggle("filled", i <= index);
			});
		};

		items.forEach((item, index) => {
			if (item.dataset.ratingInitialized) {
				return;
			}

			item.dataset.ratingInitialized = "true";
			item.addEventListener("change", () => fillStars(index));
		});

		const checkedIndex = Array.from(items).findIndex((item) => item.checked);

		if (checkedIndex >= 0) {
			fillStars(checkedIndex);
		}
	});
}

BX.ready(initRating);
BX.addCustomEvent("onAjaxSuccess", initRating);
