const DESKTOP_BREAKPOINT = 1034;

const opener = document.querySelector(".search-title-opener");
const closer = document.querySelector(".search-title-closer");
const searchTitle = document.querySelector(".search-title");

if (opener && searchTitle) {
	const setEventListeners = (bool) => {
		if (bool === true) {
			opener.removeEventListener("click", searchOpenHandler);
			closer.addEventListener("click", searchCloseHandler);
			document.addEventListener("click", outsideClickHandler);
			window.addEventListener("resize", resizeHandler);
		} else {
			opener.addEventListener("click", searchOpenHandler);
			closer.removeEventListener("click", searchCloseHandler);
			document.removeEventListener("click", outsideClickHandler);
			window.removeEventListener("resize", resizeHandler);
		}
	};

	const searchOpenHandler = (evt) => {
		evt.stopPropagation();

		searchTitle.classList.add("mobile-expanded");
		setEventListeners(true);
	};

	const closeSearch = () => {
		searchTitle.classList.remove("mobile-expanded");
		setEventListeners(false);
	};

	const outsideClickHandler = (evt) => {
		if (!searchTitle.contains(evt.target)) {
			closeSearch();
		}
	};

	const searchCloseHandler = () => {
		closeSearch();
	};

	const resizeHandler = () => {
		if (window.innerWidth >= DESKTOP_BREAKPOINT) {
			closeSearch();
		}
	};

	opener.addEventListener("click", searchOpenHandler);
}
