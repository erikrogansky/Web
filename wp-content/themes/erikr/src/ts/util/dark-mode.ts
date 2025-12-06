import { animate } from "animejs";

const toggle = document.getElementById(
    "dark-mode-toggle",
) as HTMLElement | null;
const mobileToggle = document.getElementById(
    "dark-mode-toggle-mobile",
) as HTMLElement | null;

const iconLabel = document.getElementById(
    "dark-mode-toggle-label",
) as HTMLElement | null;
const iconLabelMobile = document.getElementById(
    "dark-mode-toggle-label-mobile",
) as HTMLElement | null;

const iconSun = document.getElementById(
    "dark-mode-toggle-icon-sun",
) as HTMLElement | null;
const iconMoon = document.getElementById(
    "dark-mode-toggle-icon-moon",
) as HTMLElement | null;

const iconSunMobile = document.getElementById(
    "dark-mode-toggle-icon-sun-mobile",
) as HTMLElement | null;
const iconMoonMobile = document.getElementById(
    "dark-mode-toggle-icon-moon-mobile",
) as HTMLElement | null;

let animating = false;

function instantlyShow(el: HTMLElement | null) {
    if (!el) return;
    el.style.display = "inline-block";
    el.style.opacity = "1";
    el.style.transform = "scale(1)";
}

function instantlyHide(el: HTMLElement | null) {
    if (!el) return;
    el.style.display = "none";
    el.style.opacity = "0";
    el.style.transform = "scale(0.8)";
}

function hideThen(el: HTMLElement | null, next: () => void) {
    if (!el) return next();
    animate(el, {
        opacity: 0,
        scale: 0.8,
        duration: 200,
        ease: "inOutQuad",
        onComplete: () => {
            el.style.display = "none";
            next();
        },
    });
}

function show(el: HTMLElement | null) {
    if (!el) return;
    el.style.display = "inline-block";
    el.style.opacity = "0";
    el.style.transform = "scale(0.8)";
    animate(el, { opacity: 1, scale: 1, duration: 300, ease: "inOutQuad" });
}

function swapMobileLabel(text: string) {
    if (!iconLabelMobile) return;
    animate(iconLabelMobile, {
        opacity: 0,
        duration: 200,
        scale: 0.9,
        ease: "inOutQuad",
        onComplete: () => {
            iconLabelMobile.textContent = text;
            animate(iconLabelMobile, {
                opacity: 1,
                scale: 1,
                duration: 200,
                ease: "inOutQuad",
            });
        },
    });
}

function setLabelsForTheme(theme: "light" | "dark") {
    const txt = theme === "light" ? "Toggle Dark Mode" : "Toggle Light Mode";
    if (iconLabel) iconLabel.textContent = txt;
    if (iconLabelMobile) iconLabelMobile.textContent = txt;
    if (toggle) toggle.setAttribute("aria-label", txt);
    if (mobileToggle) mobileToggle.setAttribute("aria-label", txt);
}

function setTheme(next: "light" | "dark") {
    document.documentElement.setAttribute("data-theme", next);
    localStorage.setItem("theme", next);
    setLabelsForTheme(next);
}

const handleToggle = () => {
    if (animating) return;
    animating = true;

    const current = (document.documentElement.getAttribute("data-theme") ||
        "light") as "light" | "dark";
    const next: "light" | "dark" = current === "light" ? "dark" : "light";
    const afterTxt =
        next === "light" ? "Toggle Dark Mode" : "Toggle Light Mode";

    setTheme(next);
    swapMobileLabel(afterTxt);
    if (iconLabel) iconLabel.textContent = afterTxt;
    if (toggle) toggle.setAttribute("aria-label", afterTxt);
    if (mobileToggle) mobileToggle.setAttribute("aria-label", afterTxt);

    let pending = 2;
    const done = () => {
        pending -= 1;
        if (pending <= 0) animating = false;
    };

    if (next === "light") {
        hideThen(iconSun, () => {
            show(iconMoon);
            done();
        });
        hideThen(iconSunMobile, () => {
            show(iconMoonMobile);
            done();
        });
    } else {
        hideThen(iconMoon, () => {
            show(iconSun);
            done();
        });
        hideThen(iconMoonMobile, () => {
            show(iconSunMobile);
            done();
        });
    }
};

if (toggle) toggle.addEventListener("click", handleToggle);
if (mobileToggle) mobileToggle.addEventListener("click", handleToggle);

// Apply saved theme BEFORE page renders to prevent color flash
const saved = (localStorage.getItem("theme") as "light" | "dark") || "light";
document.documentElement.setAttribute("data-theme", saved);
setLabelsForTheme(saved);
if (saved === "light") {
    instantlyShow(iconMoon);
    instantlyHide(iconSun);
    instantlyShow(iconMoonMobile);
    instantlyHide(iconSunMobile);
} else {
    instantlyShow(iconSun);
    instantlyHide(iconMoon);
    instantlyShow(iconSunMobile);
    instantlyHide(iconMoonMobile);
}

// Enable transitions after initial theme is set
requestAnimationFrame(() => {
    document.documentElement.classList.add("transitions-enabled");
});
