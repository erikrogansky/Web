/**
 * Beta Notice Modal
 * Displays a modal on first visit to inform users about the new website experience
 */

const STORAGE_KEY = "beta_notice_dismissed";

class BetaNoticeModal {
    private modal: HTMLElement | null;
    private continueBtn: HTMLElement | null;

    constructor() {
        this.modal = document.getElementById("beta-notice-modal");
        this.continueBtn = document.getElementById("beta-notice-continue");

        this.init();
    }

    private init(): void {
        if (!this.modal) return;

        // Check if modal has been dismissed in this session
        const dismissed = sessionStorage.getItem(STORAGE_KEY);

        if (!dismissed) {
            this.show();
            this.attachEventListeners();
        }
    }

    private attachEventListeners(): void {
        // Continue button
        this.continueBtn?.addEventListener("click", () => {
            this.dismiss();
        });
    }

    private show(): void {
        if (!this.modal) return;

        // Prevent body scroll
        document.body.style.overflow = "hidden";

        // Show modal with animation
        this.modal.classList.add("beta-notice-modal--visible");

        // Focus management for accessibility
        const firstFocusableElement = this.modal.querySelector(
            "button, a",
        ) as HTMLElement;
        firstFocusableElement?.focus();
    }

    private dismiss(): void {
        if (!this.modal) return;

        // Hide modal
        this.modal.classList.remove("beta-notice-modal--visible");

        // Restore body scroll
        document.body.style.overflow = "";

        // Mark as dismissed for this session
        sessionStorage.setItem(STORAGE_KEY, "true");
    }
}

// Initialize when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        new BetaNoticeModal();
    });
} else {
    new BetaNoticeModal();
}

export default BetaNoticeModal;
