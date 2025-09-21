import { animate } from "animejs";

const toggle = document.getElementById("dark-mode-toggle");
const mobileToggle = document.getElementById("dark-mode-toggle-mobile");

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
}

function instantlyHide(el: HTMLElement | null) {
    if (!el) return;
    el.style.display = "none";
    el.style.opacity = "0";
}

function hideThen(el: HTMLElement | null, next: () => void) {
    if (!el) return next();
    animate(el, {
        opacity: 0,
        scale: 0.8,
        duration: 300,
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
    animate(el, {
        opacity: 1,
        scale: 1,
        duration: 300,
        ease: "inOutQuad",
    });
}

function setTheme(next: "light" | "dark") {
    document.documentElement.setAttribute("data-theme", next);
    localStorage.setItem("theme", next);
}

const handleToggle = () => {
    if (animating) return;
    animating = true;

    const current = (document.documentElement.getAttribute("data-theme") ||
        "light") as "light" | "dark";
    const next: "light" | "dark" = current === "light" ? "dark" : "light";

    if (next === "light") {
        hideThen(iconSun, () => {
            setTheme("light");
            show(iconMoon);
            animating = false;
        });
        hideThen(iconSunMobile, () => {
            setTheme("light");
            show(iconMoonMobile);
            animating = false;
        });
    } else {
        hideThen(iconMoon, () => {
            setTheme("dark");
            show(iconSun);
            animating = false;
        });
        hideThen(iconMoonMobile, () => {
            setTheme("dark");
            show(iconSunMobile);
            animating = false;
        });
    }
};

if (toggle) {
    toggle.addEventListener("click", handleToggle);
}
if (mobileToggle) {
    mobileToggle.addEventListener("click", handleToggle);
}

const saved = (localStorage.getItem("theme") as "light" | "dark") || "light";
setTheme(saved);
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
