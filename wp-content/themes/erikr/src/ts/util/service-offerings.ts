export function initServiceOfferingsFAQ() {
    const faqItems = document.querySelectorAll(".service-offerings__faq-item");

    faqItems.forEach((item) => {
        const button = item.querySelector(
            ".service-offerings__faq-question",
        ) as HTMLButtonElement;
        const answer = item.querySelector(
            ".service-offerings__faq-answer",
        ) as HTMLElement;

        if (!button || !answer) return;

        button.addEventListener("click", () => {
            const isExpanded = button.getAttribute("aria-expanded") === "true";

            // Close all other FAQs
            faqItems.forEach((otherItem) => {
                if (otherItem !== item) {
                    const otherButton = otherItem.querySelector(
                        ".service-offerings__faq-question",
                    ) as HTMLButtonElement;
                    const otherAnswer = otherItem.querySelector(
                        ".service-offerings__faq-answer",
                    ) as HTMLElement;

                    if (otherButton && otherAnswer) {
                        otherButton.setAttribute("aria-expanded", "false");
                        otherAnswer.setAttribute("aria-hidden", "true");
                    }
                }
            });

            // Toggle current FAQ
            if (isExpanded) {
                button.setAttribute("aria-expanded", "false");
                answer.setAttribute("aria-hidden", "true");
            } else {
                button.setAttribute("aria-expanded", "true");
                answer.setAttribute("aria-hidden", "false");
            }
        });
    });
}
