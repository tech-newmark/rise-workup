const initCatalogTagsSliders = () => {
	const sliders = document.querySelectorAll(".catalog-tags__slider");

	if (!sliders.length || typeof window.Swiper === "undefined") {
		return false;
	}

	sliders.forEach((slider) => {
		if (slider.dataset.catalogTagsSliderInited) {
			return;
		}

		slider.dataset.catalogTagsSliderInited = "true";

		const tagsBlock = slider.closest(".catalog-tags");
		const btnPrev = tagsBlock
			? tagsBlock.querySelector(".catalog-tags__button--prev")
			: null;
		const btnNext = tagsBlock
			? tagsBlock.querySelector(".catalog-tags__button--next")
			: null;

		new window.Swiper(slider, {
			slidesPerView: "auto",
			spaceBetween: 12,
			watchOverflow: true,
			navigation: {
				prevEl: btnPrev ? btnPrev : null,
				nextEl: btnNext ? btnNext : null,
			},
		});
	});
	return true;
};

const initWhenReady = () => {
	if (initCatalogTagsSliders()) {
		return;
	}

	let attempts = 0;
	const interval = setInterval(() => {
		attempts += 1;

		if (initCatalogTagsSliders() || attempts >= 30) {
			clearInterval(interval);
		}
	}, 100);
};

window.initCatalogTagsSliders = initCatalogTagsSliders;

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initWhenReady);
} else {
	initWhenReady();
}
