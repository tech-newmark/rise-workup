import { bodyLocker } from "../functions/bodyLocker";

BX.ready(function () {
	var openers = document.querySelectorAll(
		"[data-form-id], [data-1clickbuy-id], [data-quickview-id]",
	);

	openers.forEach(function (opener) {
		BX.bind(BX(opener), "click", function (evt) {
			opener.style.pointerEvents = "none";
			evt.preventDefault();

			// просто форма
			var formId = opener.getAttribute("data-form-id");
			if (formId) {
				var url = "/local/ajax/popup_form.php?form_id=" + formId;
			}

			// купить в 1 клик
			var oneClickBuyProductId = opener.getAttribute("data-1clickbuy-id");
			if (oneClickBuyProductId) {
				url =
					"/local/ajax/oneclickbuy.php?offer_id=" +
					encodeURIComponent(oneClickBuyProductId);
			}

			// быстрый просмотр
			var quickviewProductId = opener.getAttribute("data-quickview-id");
			if (quickviewProductId) {
				url =
					"/local/ajax/quickview.php?product_id=" +
					encodeURIComponent(quickviewProductId);

				const quickviewOfferId = opener.getAttribute("data-quickview-offer-id");
				if (quickviewOfferId) {
					url += "&offer_id=" + encodeURIComponent(quickviewOfferId);
				}
			}

			BX.ajax({
				url, // путь к файлу с компонентом формы
				method: "GET",
				dataType: "html",

				onsuccess: function (data) {
					var popup = new BX.PopupWindow("popup_form_" + formId, null, {
						content: data,
						closeIcon: true,
						overlay: true,
						autoHide: true,
						zIndex: 1000,
						events: {
							onPopupClose: function () {
								this.destroy();
							},
						},
					});

					// === АНИМАЦИЯ ПОЯВЛЕНИЯ ===
					const originalShow = popup.show.bind(popup);

					popup.show = function () {
						const overlayEl = this.overlay.element;
						const popupEl = this.popupContainer;

						if (!popupEl) return;
						bodyLocker(true);
						// Начальное состояние
						if (overlayEl) {
							overlayEl.style.opacity = "0";
							overlayEl.style.transition =
								"opacity 0.4s ease, transform 0.4s ease";
						}
						popupEl.style.opacity = "0";
						popupEl.style.transform = "translateY(20px)";
						popupEl.style.transition = "none";
						// Начальное состояние
						// Trigger reflow
						if (overlayEl) overlayEl.offsetHeight;
						popupEl.offsetHeight;
						// Trigger reflow

						// Показ модального окна
						originalShow();
						// Показ модального окна

						// Анимации
						if (overlayEl) {
							overlayEl.style.opacity = "1";
						}

						popupEl.style.transition = "opacity 0.4s ease, transform 0.4s ease";
						popupEl.style.opacity = "1";
						popupEl.style.transform = "translateY(0)";
						// Анимации
					};

					const originalClose = popup.close.bind(popup);

					popup.close = function () {
						const overlayEl = this.overlay.element;
						const popupEl = this.popupContainer;

						if (!popupEl || (overlayEl && overlayEl.style.opacity === "0")) {
							return originalClose();
						}

						// Анимация исчезновения
						if (overlayEl) {
							overlayEl.style.transition = "opacity .4s ease";
							overlayEl.style.opacity = "0";
						}
						popupEl.style.transition = "opacity 0.3s ease, transform 0.3s ease";
						popupEl.style.opacity = "0";
						popupEl.style.transform = "translateY(20px)";
						// Анимация исчезновения

						// Скрытие модального окна
						setTimeout(() => {
							bodyLocker(false);
							originalClose();
							opener.style.pointerEvents = "";
						}, 450);
						// Скрытие модального окна
					};

					popup.show();
				},
			});
		});
	});
});
