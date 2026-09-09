const initCatalogPopularSliders = () => {
	if (typeof window.Swiper === "undefined") {
		return;
	}

	document.querySelectorAll(".catalog-popular-slider").forEach((slider) => {
		if (slider.swiper) {
			return;
		}

		const pagination = slider.querySelector(
			".catalog-popular-slider__pagination",
		);

		new window.Swiper(slider, {
			slidesPerView: "auto",
			spaceBetween: 20,
			watchOverflow: true,
			pagination: {
				el: pagination,
				clickable: true,
			},
		});
	});
};

if (window.BX) {
	BX.ready(initCatalogPopularSliders);
} else if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initCatalogPopularSliders);
} else {
	initCatalogPopularSliders();
}
