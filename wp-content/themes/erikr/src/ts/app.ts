import "@scss/styles.scss";
import "@ts/beta-notice-modal";
import "@ts/util/animated-bubbles";
import "@ts/util/icons";
import "@ts/util/header-alignment";
import "@ts/util/dark-mode";
import "@ts/util/mobile-menu";
import "@ts/util/education-timeline";
import "@ts/util/smooth-scroll";
import { initSkillsBlocks } from "@ts/util/skills";
import { initContactForms } from "@ts/util/contact-form";
import { initServiceOfferingsFAQ } from "@ts/util/service-offerings";

// Initialize skills blocks when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    initSkillsBlocks();
    initContactForms();
    initServiceOfferingsFAQ();
});
