BX.ready(function () {
	const sliders = document.querySelectorAll(".catalog-slider");

	if (sliders.length) {
		sliders.forEach((slider) => {
			const pagination = slider.querySelector(".swiper-pagination");
			const btnNext = slider.querySelector(".swiper-button-next");
			const btnPrev = slider.querySelector(".swiper-button-prev");

			new window.Swiper(slider, {
				slidesPerView: "auto",
				spaceBetween: 20,
				watchOverflow: true,
				breakpoints: {
					560: {
						slidesPerView: 2,
					},
					960: {
						slidesPerView: 4,
					},
					1280: {
						slidesPerView: 6,
					},
				},

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
