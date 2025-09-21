import { animate } from "animejs";

type Bubble = HTMLDivElement;

const BUBBLE_COUNT = 4;
const SIZE_MIN_VMIN = 90;
const SIZE_MAX_VMIN = 100;
const DURATION_MIN = 9000;
const DURATION_MAX = 13000;
const DELAY_MAX = 2000;
const MIN_CENTER_RATIO = 0.45;
const MAX_RETRIES = 18;

const container = document.getElementById("bg-bubbles")!;
const reduceMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
).matches;

function vminToPx(v: number) {
    const vmin = Math.min(window.innerWidth, window.innerHeight);
    return (v / 100) * vmin;
}
function rand(min: number, max: number) {
    return Math.random() * (max - min) + min;
}

function getRadius(el: Bubble) {
    return el.getBoundingClientRect().width / 2;
}
function getCenter(el: Bubble) {
    const rect = el.getBoundingClientRect();
    return { cx: rect.left + rect.width / 2, cy: rect.top + rect.height / 2 };
}
function distance(
    a: { cx: number; cy: number },
    b: { cx: number; cy: number },
) {
    return Math.hypot(a.cx - b.cx, a.cy - b.cy);
}
function meetsSeparation(
    ca: { cx: number; cy: number },
    ra: number,
    cb: { cx: number; cy: number },
    rb: number,
) {
    const d = distance(ca, cb);
    return d >= (ra + rb) * MIN_CENTER_RATIO;
}

function nonOverlappingSpot(sizePx: number, others: Bubble[]) {
    const r = sizePx / 2;
    const overflow = sizePx * 0.25;

    const minX = -overflow;
    const maxX = window.innerWidth - sizePx + overflow;
    const minY = -overflow;
    const maxY = window.innerHeight - sizePx + overflow;

    for (let i = 0; i < MAX_RETRIES; i++) {
        const left = rand(minX, maxX);
        const top = rand(minY, maxY);
        const cx = left + r,
            cy = top + r;

        const ok = others.every((o) =>
            meetsSeparation({ cx, cy }, r, getCenter(o), getRadius(o)),
        );
        if (ok) return { left, top };
    }
    return { left: rand(minX, maxX), top: rand(minY, maxY) };
}

function pickNonOverlappingDelta(el: Bubble, all: Bubble[]) {
    const rect = el.getBoundingClientRect();
    const r = rect.width / 2;
    const start = { cx: rect.left + r, cy: rect.top + r };

    const overflow = r * 2 * 0.25;
    const minCx = r - overflow;
    const maxCx = window.innerWidth - r + overflow;
    const minCy = r - overflow;
    const maxCy = window.innerHeight - r + overflow;

    for (let i = 0; i < MAX_RETRIES; i++) {
        const toCx = rand(minCx, maxCx);
        const toCy = rand(minCy, maxCy);

        const ok = all.every((o) => {
            if (o === el) return true;
            return meetsSeparation(
                { cx: toCx, cy: toCy },
                r,
                getCenter(o),
                getRadius(o),
            );
        });
        if (ok) return { dx: toCx - start.cx, dy: toCy - start.cy };
    }
    return { dx: rand(-40, 40), dy: rand(-40, 40) };
}

function makeBubble(bubbles: Bubble[]): Bubble {
    const el = document.createElement("div");
    el.className = "bg-bubble";

    const sizeVmin = rand(SIZE_MIN_VMIN, SIZE_MAX_VMIN);
    const sizePx = vminToPx(sizeVmin);
    el.style.width = `${sizePx}px`;
    el.style.height = `${sizePx}px`;
    el.style.position = "absolute";

    const spot = nonOverlappingSpot(sizePx, bubbles);
    el.style.left = `${spot.left}px`;
    el.style.top = `${spot.top}px`;

    container.appendChild(el);
    return el;
}

function drift(el: Bubble, all: Bubble[]) {
    if (reduceMotion) return;

    const { dx, dy } = pickNonOverlappingDelta(el, all);
    const scale = rand(0.96, 1.06);

    animate(el, {
        translateX: dx,
        translateY: dy,
        scale,
        duration: rand(DURATION_MIN, DURATION_MAX),
        delay: rand(0, DELAY_MAX),
        ease: "inOutSine",
        direction: "alternate",
        loop: false,
        onComplete: () => drift(el, all),
    });
}

function bubbleCount() {
    const byWidth = Math.floor(window.innerWidth / 150);
    return Math.max(1, Math.min(BUBBLE_COUNT, byWidth));
}

function init() {
    const count = bubbleCount();
    const bubbles: Bubble[] = [];
    for (let i = 0; i < count; i++) {
        bubbles.push(makeBubble(bubbles));
    }
    bubbles.forEach((b) => drift(b, bubbles));
}

let resizeT: number | undefined;
window.addEventListener("resize", () => {
    clearTimeout(resizeT);
    resizeT = window.setTimeout(() => {
        container.querySelectorAll(".bg-bubble").forEach((b) => b.remove());
        init();
    }, 120);
});

init();
