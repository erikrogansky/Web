/**
 * Testimonials Block - accessible horizontal rail controls
 */

const prefersReducedMotion = (): boolean =>
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

const getRailGap = (rail: HTMLElement): number => {
    const styles = window.getComputedStyle(rail);
    const gaps = [styles.columnGap, styles.gap];
    const gap = gaps
        .map((value) => Number.parseFloat(value || "0"))
        .find((value) => !Number.isNaN(value));

    return gap || 0;
};

const getScrollStep = (rail: HTMLElement): number => {
    const item = rail.querySelector<HTMLElement>("[data-testimonial-item]");

    if (!item) {
        return rail.clientWidth;
    }

    return item.getBoundingClientRect().width + getRailGap(rail);
};

const updateControlStates = (
    rail: HTMLElement,
    prev: HTMLButtonElement,
    next: HTMLButtonElement,
): void => {
    const maxScroll = rail.scrollWidth - rail.clientWidth;
    const atStart = rail.scrollLeft <= 1;
    const atEnd = rail.scrollLeft >= maxScroll - 1;

    prev.disabled = atStart;
    next.disabled = atEnd || maxScroll <= 1;
};

export function initTestimonialsBlocks(): void {
    const blocks = document.querySelectorAll<HTMLElement>(
        "[data-testimonials-block]",
    );

    blocks.forEach((block) => {
        const rail = block.querySelector<HTMLElement>(
            "[data-testimonials-rail]",
        );
        const prev = block.querySelector<HTMLButtonElement>(
            "[data-testimonials-prev]",
        );
        const next = block.querySelector<HTMLButtonElement>(
            "[data-testimonials-next]",
        );

        if (!rail || !prev || !next) return;

        const scrollByStep = (direction: -1 | 1): void => {
            rail.scrollBy({
                left: getScrollStep(rail) * direction,
                behavior: prefersReducedMotion() ? "auto" : "smooth",
            });
        };

        prev.addEventListener("click", () => scrollByStep(-1));
        next.addEventListener("click", () => scrollByStep(1));

        rail.addEventListener("scroll", () =>
            updateControlStates(rail, prev, next),
        );
        window.addEventListener("resize", () =>
            updateControlStates(rail, prev, next),
        );

        updateControlStates(rail, prev, next);
    });
}
