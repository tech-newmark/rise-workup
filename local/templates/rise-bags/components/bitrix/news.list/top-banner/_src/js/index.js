BX.ready(function () {
	const sliders = document.querySelectorAll(".banners-slider");

	if (sliders.length) {
		sliders.forEach((slider) => {
			const pagination = slider.querySelector(".swiper-pagination");

			new window.Swiper(slider, {
				slidesPerView: 1,
				spaceBetween: 20,
				loop: true,

				pagination: {
					el: pagination ? pagination : null,
					clickable: true,
				},
			});
		});
	}
});
