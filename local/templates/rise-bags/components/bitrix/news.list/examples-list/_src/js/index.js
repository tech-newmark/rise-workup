const slider = document.querySelector(".examples-slider");

if (slider) {
	const pagination = slider.querySelector(".swiper-pagination");
	const btnNext = slider.querySelector(".swiper-button-next");
	const btnPrev = slider.querySelector(".swiper-button-prev");

	new window.Swiper(slider, {
		slidesPerView: "auto",
		spaceBetween: 20,
		watchOverflow: true,

		breakpoints: {
			768: {
				spaceBetween: 30,
			},

			1040: {
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
