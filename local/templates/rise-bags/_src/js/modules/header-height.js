const header = document.querySelector(".header");

if (header) {
	const updateHeaderHeight = () => {
		const height = header.getBoundingClientRect().height;

		document.documentElement.style.setProperty(
			"--header-height",
			`${height}px`,
		);

		return height;
	};

	updateHeaderHeight();

	window.addEventListener("resize", () => {
		updateHeaderHeight();
	});
}
