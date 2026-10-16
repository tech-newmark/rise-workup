BX.ready(() => {
	const tabSlider = document.querySelector(".bx-catalog-element__tabs-slider");
	const tabButtons = document.querySelectorAll(
		".bx-catalog-element__tabs .bx-catalog-element__tabs-slider button",
	);
	const tabContentItems = document.querySelectorAll(
		".bx-catalog-element__tabs .bx-catalog-element__tabs-item",
	);

	if (tabSlider) {
		new window.Swiper(tabSlider, {
			slidesPerView: "auto",
			spaceBetween: 6,

			focusableElements: "input, select, option, textarea, video, label",
			preventClicks: true,
			preventClicksPropagation: true,

			breakpoints: {
				768: {
					spaceBetween: 16,
				},
			},
		});
	}

	if (tabButtons.length && tabContentItems.length) {
		const toggleTab = (evt) => {
			if (!evt.currentTarget.classList.contains("active")) {
				tabButtons.forEach((tabButton) => {
					tabButton.classList.remove("active");
				});

				const value = evt.currentTarget.dataset.tabId;

				evt.currentTarget.classList.add("active");

				tabContentItems.forEach((tabContentItem) => {
					tabContentItem.classList.remove("active");

					if (tabContentItem.dataset.tabContentId === value) {
						tabContentItem.classList.add("active");
					}
				});
			}
		};

		tabButtons.forEach((tabButton) => {
			tabButton.addEventListener("click", toggleTab);
		});
	}
});
