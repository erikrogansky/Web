/**
 * Smooth scroll with offset for anchor links
 * Ensures headings aren't hidden behind the sticky header
 */

// Calculate header offset (header height + top position + extra padding)
const getScrollOffset = (): number => {
    const header = document.querySelector(".site-header") as HTMLElement;
    if (!header) return 100; // Fallback offset

    const headerHeight = header.offsetHeight;
    const headerTop = 12; // From CSS: top: 12px
    const extraPadding = 40; // Additional spacing for better UX

    return headerHeight + headerTop + extraPadding;
};

// Smooth scroll to element with offset
const scrollToElement = (element: HTMLElement): void => {
    const offset = getScrollOffset();
    const elementPosition =
        element.getBoundingClientRect().top + window.scrollY;
    const offsetPosition = elementPosition - offset;

    window.scrollTo({
        top: offsetPosition,
        behavior: "smooth",
    });
};

// Handle initial page load with hash
const handleInitialHash = (): void => {
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            // Wait for page to fully load before scrolling
            setTimeout(() => {
                scrollToElement(targetElement);
            }, 100);
        }
    }
};

// Handle anchor link clicks
const handleAnchorClicks = (): void => {
    document.addEventListener("click", (e: MouseEvent) => {
        const target = e.target as HTMLElement;
        const anchor = target.closest('a[href^="#"]') as HTMLAnchorElement;

        if (!anchor) return;

        const href = anchor.getAttribute("href");
        if (!href || href === "#") return;

        const targetId = href.substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            e.preventDefault();
            scrollToElement(targetElement);

            // Update URL hash without jumping
            history.pushState(null, "", href);
        }
    });
};

// Handle browser back/forward with hash changes
const handleHashChange = (): void => {
    window.addEventListener("hashchange", () => {
        if (window.location.hash) {
            const targetId = window.location.hash.substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                scrollToElement(targetElement);
            }
        }
    });
};

// Initialize smooth scroll functionality
export const initSmoothScroll = (): void => {
    handleInitialHash();
    handleAnchorClicks();
    handleHashChange();
};

// Auto-initialize on DOM ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSmoothScroll);
} else {
    initSmoothScroll();
}
