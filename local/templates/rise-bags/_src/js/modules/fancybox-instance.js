import { Fancybox } from "@fancyapps/ui";

export function fancyInit() {
	const fancy = document.querySelectorAll("[data-fancybox]");

	if (fancy.length) {
		Fancybox.bind("[data-fancybox]", {
			on: {
				closing: (fancybox) => {
					if (fancybox.trigger) {
						fancybox.trigger.focus();
					}
				},
				close: (fancybox) => {
					fancybox.container.removeAttribute("aria-hidden");
				},
			},
		});
	}
}

fancyInit();

window.FancyboxInit = fancyInit;
