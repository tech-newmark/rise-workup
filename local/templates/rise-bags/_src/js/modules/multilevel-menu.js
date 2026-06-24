const items = document.querySelectorAll(".mobile-menu__link");

function isMobile() {
	return window.matchMedia("(max-width: 959px)").matches;
}

if (items) {
	items.forEach((item) => {
		item.addEventListener("click", (evt) => {
			if (isMobile()) {
				const hasInnerLevel = item.nextElementSibling;

				if (!hasInnerLevel) return;
				evt.preventDefault();
				item.classList.toggle("mobile-active");
			}
		});
	});
}

window.addEventListener("resize", () => {
	if (!isMobile()) {
		items.forEach((item) => {
			item.classList.contains("mobile-active")
				? item.classList.remove("mobile-active")
				: null;
		});
	}
});

const backBtns = document.querySelectorAll(".mobile-menu__mobile-back");

if (backBtns) {
	backBtns.forEach((btn) => {
		btn.addEventListener("click", () => {
			btn.parentNode.previousElementSibling.classList.remove("mobile-active");
		});
	});
}
