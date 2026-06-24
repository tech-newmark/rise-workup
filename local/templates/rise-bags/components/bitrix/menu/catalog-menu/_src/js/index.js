document.addEventListener("DOMContentLoaded", () => {
	const catalogMenu = document.querySelector(".catalog-menu");

	if (catalogMenu) {
		const openers = catalogMenu.querySelectorAll(".catalog-menu-sidelist li");

		if (openers) {
			const contentBlocks = catalogMenu.querySelectorAll(
				".catalog-menu-main ul",
			);

			const setActive = (list) => {
				if (active === list) return;

				active.classList.remove("active");
				active = list;
				list.classList.add("active");
			};

			let active = contentBlocks[0];
			active.classList.add("active");

			const onMouseOverHandler = (evt) => {
				const target = evt.currentTarget;

				const list = Array.from(contentBlocks).find(
					(item) => item.dataset.id === target.dataset.id,
				);
				if (list) setActive(list);
			};

			openers.forEach((opener) => {
				opener.addEventListener("mouseover", onMouseOverHandler);
				opener.addEventListener("focusin", onMouseOverHandler);
			});

			catalogMenu.addEventListener("mouseleave", () => {
				active.classList.remove("active");
				active = contentBlocks[0];
				active.classList.add("active");
			});
		}
	}
});
