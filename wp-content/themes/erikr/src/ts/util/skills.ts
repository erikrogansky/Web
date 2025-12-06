/**
 * Skills Block - Handles filtering, search, and show more functionality
 */

interface SkillCard extends HTMLElement {
    dataset: {
        skillCard: string;
        category: string;
        name: string;
    };
}

export class SkillsBlock {
    private container: HTMLElement;
    private pills: NodeListOf<HTMLButtonElement>;
    private searchInput: HTMLInputElement;
    private skillCards: NodeListOf<SkillCard>;
    private showMoreBtn: HTMLButtonElement | null;
    private noResultsMsg: HTMLElement;
    private categoryTriggers: NodeListOf<HTMLAnchorElement>;
    private controlsSection: HTMLElement | null;
    private currentCategory: string = "all";
    private isExpanded: boolean = false;
    private searchTerm: string = "";

    constructor(container: HTMLElement) {
        this.container = container;
        this.pills = container.querySelectorAll("[data-category]");
        this.searchInput = container.querySelector("[data-search-input]")!;
        this.skillCards = container.querySelectorAll("[data-skill-card]");
        this.showMoreBtn = container.querySelector("[data-show-more]");
        this.noResultsMsg = container.querySelector("[data-no-results]")!;
        this.categoryTriggers = container.querySelectorAll(
            "[data-category-trigger]",
        );
        this.controlsSection = container.querySelector("[data-controls]");

        this.init();
    }

    private init(): void {
        // Pill click handlers
        this.pills.forEach((pill) => {
            pill.addEventListener("click", () =>
                this.handleCategoryChange(pill),
            );
        });

        // Featured category card click handlers
        this.categoryTriggers.forEach((trigger) => {
            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                const category = trigger.dataset.categoryTrigger || "all";
                this.activateCategoryFromTrigger(category);
            });
        });

        // Search input handler
        this.searchInput.addEventListener("input", (e) => {
            this.searchTerm = (e.target as HTMLInputElement).value
                .toLowerCase()
                .trim();
            this.filterCards();
        });

        // Show more button handler
        if (this.showMoreBtn) {
            this.showMoreBtn.addEventListener("click", () =>
                this.toggleShowMore(),
            );
        }

        // Initial filter
        this.filterCards();
    }

    private activateCategoryFromTrigger(category: string): void {
        // Find the pill with this category
        const targetPill = Array.from(this.pills).find(
            (pill) => pill.dataset.category === category,
        );

        if (targetPill) {
            // Activate category immediately
            this.handleCategoryChange(targetPill as HTMLButtonElement);

            // Scroll to controls section in parallel
            if (this.controlsSection) {
                const offset = 100;
                const elementPosition =
                    this.controlsSection.getBoundingClientRect().top +
                    window.pageYOffset;

                window.scrollTo({
                    top: elementPosition - offset,
                    behavior: "smooth",
                });
            }
        }
    }

    private handleCategoryChange(clickedPill: HTMLButtonElement): void {
        const category = clickedPill.dataset.category || "all";

        // Update active pill
        this.pills.forEach((pill) =>
            pill.classList.remove("skills-block__pill--active"),
        );
        clickedPill.classList.add("skills-block__pill--active");

        // Update current category
        this.currentCategory = category;

        // Reset search when changing category
        this.searchInput.value = "";
        this.searchTerm = "";

        // Reset expanded state when changing category
        this.isExpanded = false;
        if (this.showMoreBtn) {
            this.showMoreBtn.classList.remove(
                "skills-block__show-more--active",
            );
            const text = this.showMoreBtn.querySelector(
                "[data-show-more-text]",
            );
            if (text) text.textContent = "Show more";
        }

        // Filter cards
        this.filterCards();
    }

    private filterCards(): void {
        let visibleCount = 0;
        let totalMatchingCards = 0;

        this.skillCards.forEach((card) => {
            const cardCategory = card.dataset.category || "others";
            const cardName = card.dataset.name || "";

            // Check category match
            const categoryMatch =
                this.currentCategory === "all" ||
                cardCategory === this.currentCategory;

            // Check search match
            const searchMatch =
                !this.searchTerm || cardName.includes(this.searchTerm);

            const isMatch = categoryMatch && searchMatch;

            if (isMatch) {
                totalMatchingCards++;

                // Show first 6 cards, or all if expanded
                if (this.isExpanded || totalMatchingCards <= 6) {
                    card.classList.remove(
                        "skills-block__card--hidden",
                        "skills-block__card--filtered-out",
                    );
                    visibleCount++;
                } else {
                    card.classList.add("skills-block__card--hidden");
                    card.classList.remove("skills-block__card--filtered-out");
                }
            } else {
                card.classList.add("skills-block__card--filtered-out");
                card.classList.remove("skills-block__card--hidden");
            }
        });

        // Update show more button visibility
        if (this.showMoreBtn) {
            if (totalMatchingCards > 6) {
                this.showMoreBtn.style.display = "flex";
            } else {
                this.showMoreBtn.style.display = "none";
            }
        }

        // Show/hide no results message
        if (visibleCount === 0) {
            this.noResultsMsg.style.display = "block";
        } else {
            this.noResultsMsg.style.display = "none";
        }
    }

    private toggleShowMore(): void {
        this.isExpanded = !this.isExpanded;

        if (this.showMoreBtn) {
            const text = this.showMoreBtn.querySelector(
                "[data-show-more-text]",
            );

            if (this.isExpanded) {
                this.showMoreBtn.classList.add(
                    "skills-block__show-more--active",
                );
                if (text) text.textContent = "Show less";
            } else {
                this.showMoreBtn.classList.remove(
                    "skills-block__show-more--active",
                );
                if (text) text.textContent = "Show more";
            }
        }

        this.filterCards();

        // Scroll to top of block if collapsing
        if (!this.isExpanded) {
            const containerTop =
                this.container.getBoundingClientRect().top + window.pageYOffset;
            const offset = 100; // Offset from top
            window.scrollTo({
                top: containerTop - offset,
                behavior: "smooth",
            });
        }
    }
}

/**
 * Initialize all skills blocks on the page
 */
export function initSkillsBlocks(): void {
    const skillsBlocks = document.querySelectorAll("[data-skills-block]");

    skillsBlocks.forEach((block) => {
        new SkillsBlock(block as HTMLElement);
    });
}
