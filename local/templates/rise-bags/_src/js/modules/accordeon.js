const accordeon = document.querySelector(".accordeon");

console.log("accordeons", accordeon);

if (accordeon) {
	const items = accordeon.querySelectorAll(".accordeon-header");

	items.forEach((item) => {
		item.addEventListener("click", () => {
			item.parentNode.classList.toggle("expanded");
		});
	});
}
