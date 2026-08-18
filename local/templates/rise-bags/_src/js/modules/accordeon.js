const accordeon = document.querySelector(".accordeon");

if (accordeon) {
	const items = accordeon.querySelectorAll(".accordeon-header");

	items.forEach((item) => {
		item.addEventListener("click", (event) => {
			if (event.target.closest("a")) {
				return;
			}

			item.parentNode.classList.toggle("expanded");
		});
	});
}
