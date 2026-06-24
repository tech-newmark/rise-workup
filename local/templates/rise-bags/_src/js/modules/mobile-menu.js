import { bodyLocker } from "../functions/bodyLocker";

const burger = document.querySelector(".menu-opener");
const nav = document.querySelector(".header__mobile");
const desktopBreakpoint = window.matchMedia("(min-width: 960px)");

const closeMenu = () => {
	nav.classList.remove("active");
	bodyLocker(false);
};

if (burger && nav) {
	const onClickOpenMenu = () => {
		nav.classList.toggle("active");

		if (nav.classList.contains("active")) {
			bodyLocker(true);
		} else {
			bodyLocker(false);
		}
	};

	burger.addEventListener("click", onClickOpenMenu);

	desktopBreakpoint.addEventListener("change", (evt) => {
		if (evt.matches) {
			closeMenu();
		}
	});
}
