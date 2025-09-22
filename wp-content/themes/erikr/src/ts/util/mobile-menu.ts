import { animate } from "animejs";

document.addEventListener("DOMContentLoaded", () => {
    const menuButton = document.querySelector(".menu-icon") as HTMLElement;
    const mobileOverlay = document.querySelector(
        ".mobile-menu-backdrop",
    ) as HTMLElement;
    const mobileMenu = document.querySelector(
        ".mobile-menu-container",
    ) as HTMLElement;

    if (!menuButton || !mobileOverlay || !mobileMenu) return;

    menuButton.addEventListener("click", () => {
        animate(mobileMenu, {
            translateX: ["100%", "0%"],
            duration: 250,
            ease: "outQuad",
            onBegin: () => {
                mobileMenu.style.display = "flex";
                mobileMenu.style.transform = "translateX(100%)";
            },
        });

        animate(mobileOverlay, {
            opacity: [0, 1],
            duration: 250,
            ease: "outQuad",
            onBegin: () => {
                mobileOverlay.style.display = "block";
                mobileOverlay.style.opacity = "0";
            },
        });
    });

    mobileOverlay.addEventListener("click", () => {
        animate(mobileMenu, {
            translateX: ["0%", "100%"],
            duration: 250,
            ease: "outQuad",
            onComplete: () => {
                mobileMenu.style.display = "none";
            },
        });

        animate(mobileOverlay, {
            opacity: [1, 0],
            duration: 250,
            ease: "outQuad",
            onComplete: () => {
                mobileOverlay.style.display = "none";
            },
        });
    });
});
