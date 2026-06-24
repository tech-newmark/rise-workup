import IMask from "imask";

const maskedFields = new WeakSet();

export function initImask() {
	const phoneFields = document.querySelectorAll('[data-type="tel"]');

	const maskOptions = {
		mask: "+{7} (000) 000-00-00",
	};

	phoneFields.forEach((field) => {
		if (maskedFields.has(field)) {
			return;
		}

		IMask(field, maskOptions);
		maskedFields.add(field);
	});
}

document.addEventListener("DOMContentLoaded", initImask);

BX.addCustomEvent("onAjaxSuccess", initImask);

window.initImask = initImask;
