// Keep the current products visible until the latest requested panel is ready.
const initCatalogFiltered = () => {
	document.querySelectorAll("[data-catalog-filtered]").forEach((section) => {
		if (section.dataset.initialized === "true") return;
		section.dataset.initialized = "true";

		const buttons = [...section.querySelectorAll("[data-catalog-tab]")];
		const panels = new Map(
			[...section.querySelectorAll("[data-catalog-panel]")].map((panel) => [
				panel.dataset.catalogPanel,
				panel,
			]),
		);
		const requests = new Map();
		const instanceId = BX.util.getRandomString(12);
		let activeTab = "popular";
		let requestedTab = activeTab;

		buttons.forEach((button) => {
			const tab = button.dataset.catalogTab;
			const panel = panels.get(tab);
			if (!panel) return;
			button.id = `catalog-tab-${instanceId}-${tab}`;
			panel.id = `catalog-panel-${instanceId}-${tab}`;
			button.setAttribute("aria-controls", panel.id);
			panel.setAttribute("aria-labelledby", button.id);
		});

		const updateLoading = () => {
			const loading = panels.get(requestedTab)?.dataset.loading === "true";
			section.classList.toggle("is-loading", loading);
			section.setAttribute("aria-busy", String(loading));
		};

		const showPanel = (tab) => {
			activeTab = tab;
			buttons.forEach((button) => {
				const selected = button.dataset.catalogTab === tab;
				button.classList.toggle("outlined", !selected);
				button.setAttribute("aria-selected", String(selected));
				button.tabIndex = selected ? 0 : -1;
			});
			panels.forEach((panel, key) => { panel.hidden = key !== tab; });
			updateLoading();
		};

		const showStatus = (panel, message, isError = false) => {
			const status = document.createElement("p");
			status.className = isError ? "catalog-filtered__error" : "catalog-filtered__status";
			status.setAttribute("role", isError ? "alert" : "status");
			status.textContent = message;
			panel.replaceChildren(status);
		};

		const loadPanel = (tab) => {
			const panel = panels.get(tab);
			if (!panel || panel.dataset.loaded === "true") return Promise.resolve();
			if (requests.has(tab)) return requests.get(tab);

			panel.dataset.loading = "true";
			panel.setAttribute("aria-busy", "true");
			updateLoading();

			const request = Promise.resolve().then(async () => {
				const controller = new AbortController();
				const timeout = setTimeout(() => controller.abort(), 30000);
				try {
					const url = new URL(section.dataset.ajaxUrl, window.location.origin);
					url.searchParams.set("tab", tab);
					const response = await fetch(url, {
						headers: { "X-Requested-With": "XMLHttpRequest" },
						signal: controller.signal,
					});
					if (!response.ok) throw new Error(`HTTP ${response.status}`);

					const processed = BX.processHTML(await response.text(), false);
					const content = document.createElement("div");
					content.innerHTML = processed.HTML;
					if (!content.querySelector("[data-catalog-list]")) {
						throw new Error("Invalid catalog response");
					}
					if (processed.STYLE?.length) BX.loadCSS(processed.STYLE);
					panel.replaceChildren(...content.childNodes);
					await new Promise((resolve, reject) => {
						const scriptTimeout = setTimeout(() => reject(new Error("Script loading timeout")), 30000);
						try {
							BX.ajax.processScripts(processed.SCRIPT, undefined, () => {
								clearTimeout(scriptTimeout);
								resolve();
							});
						} catch (error) {
							clearTimeout(scriptTimeout);
							reject(error);
						}
					});
					panel.dataset.loaded = "true";
				} catch (error) {
					delete panel.dataset.loaded;
					showStatus(panel, section.dataset.errorMessage, true);
					const retry = document.createElement("button");
					retry.type = "button";
					retry.className = "main-btn outlined";
					retry.textContent = section.dataset.retryMessage;
					retry.addEventListener("click", () => selectTab(tab));
					panel.appendChild(retry);
					console.error("Catalog panel loading failed:", error);
				} finally {
					clearTimeout(timeout);
					delete panel.dataset.loading;
					panel.setAttribute("aria-busy", "false");
					requests.delete(tab);
					updateLoading();
				}
			});
			requests.set(tab, request);
			return request;
		};

		const selectTab = async (tab) => {
			const panel = panels.get(tab);
			if (!panel) return;
			requestedTab = tab;
			updateLoading();
			if (panel.dataset.loaded !== "true") await loadPanel(tab);
			if (requestedTab === tab) showPanel(tab);
		};

		buttons.forEach((button, index) => {
			button.addEventListener("click", () => {
				selectTab(button.dataset.catalogTab);
			});
			button.addEventListener("keydown", (event) => {
				let next;
				switch (event.key) {
					case "ArrowRight": next = (index + 1) % buttons.length; break;
					case "ArrowLeft": next = (index + buttons.length - 1) % buttons.length; break;
					case "Home": next = 0; break;
					case "End": next = buttons.length - 1; break;
					default: return;
				}
				event.preventDefault();
				buttons[next].focus();
				buttons[next].click();
			});
		});
		showPanel(activeTab);
	});
};

BX.ready(initCatalogFiltered);
BX.addCustomEvent("onFrameDataReceived", initCatalogFiltered);
