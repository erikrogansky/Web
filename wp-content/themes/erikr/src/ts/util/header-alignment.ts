document.addEventListener("DOMContentLoaded", function () {
    const headerBranding = document.getElementById("header-site-branding");
    const headerCtas = document.getElementById("header-site-ctas");

    function adjustHeaderAlignment() {
        if (headerBranding) headerBranding.style.width = "";
        if (headerCtas) headerCtas.style.width = "";

        if (window.innerWidth < 1160) return;

        const brandingWidth = headerBranding ? headerBranding.offsetWidth : 0;
        const ctasWidth = headerCtas ? headerCtas.offsetWidth : 0;
        const maxWidth = Math.max(brandingWidth, ctasWidth);

        if (brandingWidth < maxWidth && headerBranding) {
            headerBranding.style.width = `${maxWidth}px`;
        } else if (ctasWidth < maxWidth && headerCtas) {
            headerCtas.style.width = `${maxWidth}px`;
        }
    }

    adjustHeaderAlignment();

    window.addEventListener("resize", adjustHeaderAlignment);
});
