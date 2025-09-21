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
        mobileOverlay.style.display = "block";
        mobileMenu.style.display = "flex";
    });

    mobileOverlay.addEventListener("click", () => {
        mobileOverlay.style.display = "none";
        mobileMenu.style.display = "none";
    });
});
