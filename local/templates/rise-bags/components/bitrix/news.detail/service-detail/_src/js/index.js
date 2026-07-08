const collapsibleSections = document.querySelectorAll(".collapsed-section");

collapsibleSections.forEach((section) => {
	const toggleButton = section.querySelector(".clear-btn");
	const content = section.querySelector(".content");

	if (toggleButton && content) {
		toggleButton.addEventListener("click", () => {
			const isExpanded = content.classList.toggle("expanded");

			toggleButton.textContent = isExpanded ? "Свернуть" : "Развернуть";
		});
	}
});
