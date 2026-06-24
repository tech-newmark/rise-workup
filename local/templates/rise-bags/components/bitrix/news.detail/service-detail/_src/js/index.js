BX.ready(function () {
	const slider = document.querySelector(".examples-slider");

	if (slider) {
		const pagination = slider.querySelector(".swiper-pagination");
		const btnNext = slider.querySelector(".swiper-button-next");
		const btnPrev = slider.querySelector(".swiper-button-prev");

		new window.Swiper(slider, {
			slidesPerView: "auto",
			spaceBetween: 20,

			breakpoints: {
				780: {
					spaceBetween: 30,
				},
				1240: {
					spaceBetween: 40,
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
	}
});
