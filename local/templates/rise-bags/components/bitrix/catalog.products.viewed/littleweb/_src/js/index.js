BX.ready(function () {
	const sliders = document.querySelectorAll(".catalog-viewed-slider");

	if (sliders.length) {
		sliders.forEach((slider) => {
			if (slider.swiper) {
				return;
			}

			const pagination = slider.querySelector(
				".catalog-viewed-slider__pagination",
			);
			const btnNext = slider.querySelector(".swiper-button-next");
			const btnPrev = slider.querySelector(".swiper-button-prev");

			new window.Swiper(slider, {
				slidesPerView: "auto",
				spaceBetween: 20,
				watchOverflow: true,

				navigation: {
					nextEl: btnNext ? btnNext : null,
					prevEl: btnPrev ? btnPrev : null,
				},

				pagination: {
					el: pagination ? pagination : null,
					clickable: true,
				},
			});
		});
	}
});
