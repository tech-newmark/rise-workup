const blockedEmailMessage = "Почта в зоне .com не принимается";

const emailSelector = [
	'[data-type="email"]',
	'[autocomplete="email"]',
	"#bx-register-user-email",
	"#main-profile-email",
].join(",");

const submitSelector = [
	'button[type="submit"]',
	'input[type="submit"]',
	"[data-save-button]",
	"#bx-soa-orderSave a",
	"#bx-soa-orderSave button",
].join(",");

const isBlockedEmail = (value) => /@[^@\s]+\.com$/i.test(value.trim());

const setFieldInvalidState = (field, isInvalid) => {
	const wrapper = field.closest(
		".main-input-wrapper, .bx-soa-customer-field, .form-group, label"
	);

	field.classList.toggle("invalid-fld", isInvalid);

	if (wrapper) {
		wrapper.classList.toggle("invalid-fld", isInvalid);
	}
};

const validateEmailField = (field, shouldReport = false) => {
	if (!field || !field.matches(emailSelector)) {
		return true;
	}

	if (isBlockedEmail(field.value)) {
		field.setCustomValidity(blockedEmailMessage);
		setFieldInvalidState(field, true);

		if (shouldReport) {
			field.reportValidity();
		}

		return false;
	}

	field.setCustomValidity("");
	setFieldInvalidState(field, false);
	return true;
};

const validateEmailFields = (scope, shouldReport = false) => {
	const emailFields = scope.querySelectorAll(emailSelector);

	for (const field of emailFields) {
		if (!validateEmailField(field, shouldReport)) {
			return false;
		}
	}

	return true;
};

document.addEventListener("input", (event) => {
	validateEmailField(event.target);
});

document.addEventListener("submit", (event) => {
	if (!validateEmailFields(event.target, true)) {
		event.preventDefault();
	}
});

document.addEventListener(
	"click",
	(event) => {
		const submitElement = event.target.closest(submitSelector);

		if (!submitElement) {
			return;
		}

		const scope =
			submitElement.closest("form") ||
			submitElement.closest(".popup-form, .bx-soa, .bx-authform, .profile") ||
			document;

		if (validateEmailFields(scope, true)) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
	},
	true
);
