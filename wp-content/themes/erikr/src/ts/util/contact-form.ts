// Contact Form Handler with reCAPTCHA v3

interface ContactFormData {
    reason: string;
    name: string;
    email: string;
    phone: string;
    message: string;
}

export function initContactForms(): void {
    const forms = document.querySelectorAll<HTMLFormElement>(".contact-form");

    forms.forEach((form) => {
        // Initialize custom selects
        initCustomSelects(form);

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            await handleFormSubmit(form);
        });
    });
}

// Custom Select Dropdown Logic
function initCustomSelects(container: HTMLElement): void {
    const customSelects =
        container.querySelectorAll<HTMLElement>(".custom-select");

    customSelects.forEach((customSelect) => {
        const trigger = customSelect.querySelector<HTMLElement>(
            ".custom-select__trigger",
        );
        const valueSpan = customSelect.querySelector<HTMLElement>(
            ".custom-select__value",
        );
        const options = customSelect.querySelectorAll<HTMLElement>(
            ".custom-select__option",
        );
        const selectId = customSelect.dataset.selectId;
        const nativeSelect = document.getElementById(
            selectId!,
        ) as HTMLSelectElement;

        if (!trigger || !valueSpan || !nativeSelect) return;

        // Set first option as default
        const firstOption = options[0];
        if (firstOption) {
            const firstValue = firstOption.dataset.value || "";
            nativeSelect.value = firstValue;
            firstOption.classList.add("is-selected");
        }

        // Toggle dropdown
        trigger.addEventListener("click", (e) => {
            e.stopPropagation();
            closeAllSelects();
            customSelect.classList.toggle("is-open");
        });

        // Handle option selection
        options.forEach((option) => {
            option.addEventListener("click", () => {
                const value = option.dataset.value || "";
                const text = option.textContent || "";

                // Update native select
                nativeSelect.value = value;

                // Update custom select display
                valueSpan.textContent = text;

                // Update placeholder state
                if (value === "") {
                    valueSpan.classList.add("is-placeholder");
                } else {
                    valueSpan.classList.remove("is-placeholder");
                }

                // Update selected state
                options.forEach((opt) => opt.classList.remove("is-selected"));
                option.classList.add("is-selected");

                // Close dropdown
                customSelect.classList.remove("is-open");

                // Trigger change event on native select
                nativeSelect.dispatchEvent(
                    new Event("change", { bubbles: true }),
                );
            });
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener("click", closeAllSelects);
}

function closeAllSelects(): void {
    document.querySelectorAll(".custom-select.is-open").forEach((select) => {
        select.classList.remove("is-open");
    });
}

async function handleFormSubmit(form: HTMLFormElement): Promise<void> {
    const submitBtn = form.querySelector<HTMLButtonElement>(
        ".contact-form__submit",
    );
    const messagesDiv = form.querySelector<HTMLDivElement>(
        ".contact-form__messages",
    );

    if (!submitBtn || !messagesDiv) return;

    // Get widget ID and settings
    const widgetId = form.dataset.widgetId;
    if (!widgetId) {
        showMessage(messagesDiv, "error", "Form configuration error.");
        return;
    }

    // Disable form
    setFormLoading(form, submitBtn, true);

    try {
        // Get form data
        const formData = getFormData(form);

        // Validate form data
        const validationError = validateFormData(formData);
        if (validationError) {
            throw new Error(validationError);
        }

        // Get reCAPTCHA token
        const recaptchaToken = await getRecaptchaToken();

        // Submit form via AJAX with timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

        try {
            const response = await fetch(ajaxurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    action: "er_contact_form_submit",
                    widget_id: widgetId,
                    recaptcha_token: recaptchaToken,
                    reason: formData.reason,
                    name: formData.name,
                    email: formData.email,
                    phone: formData.phone,
                    message: formData.message,
                    nonce: window.er_contact_form_nonce || "",
                }),
                signal: controller.signal,
            });

            clearTimeout(timeoutId);

            const result = await response.json();

            if (result.success) {
                showMessage(
                    messagesDiv,
                    "success",
                    result.data.message || "Message sent successfully!",
                );
                form.reset();
            } else {
                throw new Error(
                    result.data.message ||
                        "Something went wrong. Please try again.",
                );
            }
        } catch (fetchError) {
            clearTimeout(timeoutId);

            if (
                fetchError instanceof Error &&
                fetchError.name === "AbortError"
            ) {
                throw new Error(
                    "Request timed out. The server may be experiencing issues.",
                );
            }
            throw fetchError;
        }
    } catch (error) {
        const message =
            error instanceof Error
                ? error.message
                : "An error occurred. Please try again.";
        showMessage(messagesDiv, "error", message);
    } finally {
        setFormLoading(form, submitBtn, false);
    }
}

function getFormData(form: HTMLFormElement): ContactFormData {
    // Get values directly from form elements instead of using FormData
    const reasonSelect =
        form.querySelector<HTMLSelectElement>('[name="reason"]');
    const nameInput = form.querySelector<HTMLInputElement>('[name="name"]');
    const emailInput = form.querySelector<HTMLInputElement>('[name="email"]');
    const phoneInput = form.querySelector<HTMLInputElement>('[name="phone"]');
    const messageTextarea =
        form.querySelector<HTMLTextAreaElement>('[name="message"]');

    return {
        reason: reasonSelect?.value || "",
        name: nameInput?.value || "",
        email: emailInput?.value || "",
        phone: phoneInput?.value || "",
        message: messageTextarea?.value || "",
    };
}

function validateFormData(data: ContactFormData): string | null {
    // Trim all fields to handle whitespace-only inputs
    const trimmedReason = data.reason.trim();
    const trimmedName = data.name.trim();
    const trimmedEmail = data.email.trim();
    const trimmedMessage = data.message.trim();

    if (!trimmedReason) {
        return "Please select a contact reason.";
    }

    if (!trimmedName) {
        return "Please enter your name.";
    }

    if (!trimmedEmail) {
        return "Please enter your email address.";
    }

    if (!trimmedMessage) {
        return "Please enter a message.";
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(trimmedEmail)) {
        return "Please enter a valid email address.";
    }

    return null; // No errors
}

async function getRecaptchaToken(): Promise<string> {
    return new Promise((resolve) => {
        if (typeof grecaptcha === "undefined" || !grecaptcha.enterprise) {
            resolve(""); // reCAPTCHA not configured
            return;
        }

        grecaptcha.enterprise.ready(async () => {
            const scriptTag = document.querySelector(
                'script[src*="recaptcha/enterprise.js"]',
            );

            const siteKey = scriptTag
                ?.getAttribute("src")
                ?.match(/render=([^&]+)/)?.[1];

            if (!siteKey) {
                resolve("");
                return;
            }

            try {
                const token = await grecaptcha.enterprise.execute(siteKey, {
                    action: "contact_form",
                });
                resolve(token);
            } catch {
                resolve("");
            }
        });
    });
}

function setFormLoading(
    form: HTMLFormElement,
    submitBtn: HTMLButtonElement,
    isLoading: boolean,
): void {
    const inputs = form.querySelectorAll<
        HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement
    >("input, select, textarea");

    inputs.forEach((input) => {
        input.disabled = isLoading;
    });

    submitBtn.disabled = isLoading;

    if (isLoading) {
        submitBtn.classList.add("loading");
    } else {
        submitBtn.classList.remove("loading");
    }
}

function showMessage(
    messagesDiv: HTMLDivElement,
    type: "success" | "error",
    message: string,
): void {
    messagesDiv.className = `contact-form__messages ${type}`;
    messagesDiv.textContent = message;
    messagesDiv.style.display = "block";

    // Auto-hide success messages after 5 seconds
    if (type === "success") {
        setTimeout(() => {
            messagesDiv.style.display = "none";
        }, 5000);
    }
}

// Type definition for grecaptcha
declare global {
    interface Window {
        ajaxurl: string;
        er_contact_form_nonce: string;
    }

    interface GrecaptchaEnterprise {
        // eslint-disable-next-line no-unused-vars
        ready: (callback: () => void) => void;
        execute: (
            // eslint-disable-next-line no-unused-vars
            siteKey: string,
            // eslint-disable-next-line no-unused-vars
            options: { action: string },
        ) => Promise<string>;
    }

    const grecaptcha: {
        enterprise: GrecaptchaEnterprise;
    };

    const ajaxurl: string;
}
