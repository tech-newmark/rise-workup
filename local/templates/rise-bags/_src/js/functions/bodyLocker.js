export const bodyLocker = (isLocked) => {
	const body = document.querySelector("body");

	if (isLocked) {
		body.style.overflow = "hidden";
	} else {
		body.style.overflow = "auto";
	}
};
