const collapsibleSections = document.querySelectorAll(".collapsible-section");
const updateHandlers = [];

collapsibleSections.forEach((section) => {
	const toggleButton = section.querySelector(".collapsible-section__btn");
	const content = section.querySelector(".collapsible-section__text");

	if (!toggleButton || !content) return;

	const updateButtonVisibility = () => {
		const wasExpanded = content.classList.contains("expanded");

		if (wasExpanded) {
			content.classList.remove("expanded");
		}

		const isButtonNeeded = content.scrollHeight > content.clientHeight;

		toggleButton.classList.toggle("hidden", !isButtonNeeded);

		if (wasExpanded) {
			content.classList.add("expanded");
		}
	};

	updateButtonVisibility();
	updateHandlers.push(updateButtonVisibility);

	toggleButton.addEventListener("click", () => {
		const isExpanded = content.classList.toggle("expanded");

		toggleButton.textContent = isExpanded ? "Свернуть" : "Развернуть";
	});
});

window.addEventListener("resize", () => {
	updateHandlers.forEach((update) => update());
});
