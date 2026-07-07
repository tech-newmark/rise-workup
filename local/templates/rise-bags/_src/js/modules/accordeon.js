const accordeon = document.querySelector(".accordeon");

console.log("accordeons", accordeon);

if (accordeon) {
	const items = accordeon.querySelectorAll(".accordeon-header");

	items.forEach((item) => {
		item.addEventListener("click", (event) => {
			console.log("clicked");
			if (event.target.closest("a")) {
				return;
			}

			item.parentNode.classList.toggle("expanded");
		});
	});
}
