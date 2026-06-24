import customSelect from "custom-select";

export function initCustomSelect() {
	const items = document.querySelectorAll(".custom-select");

	items.forEach((select) => {
		if (!select.dataset.customSelectInited) {
			customSelect(select, {});
			select.dataset.customSelectInited = "1";
		}
	});
}

initCustomSelect();

// Экспортируем для внешнего использования
window.initCustomSelect = initCustomSelect;
