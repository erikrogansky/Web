<?php
function components_path(): array {
    $slug      = function_exists('get_site_slug') ? get_site_slug() : 'web';
    $theme_dir = get_template_directory() . "/components";

    return apply_filters('components_path', $theme_dir, $slug);
}

function resolve_component(string $name): ?string {
    $name = ltrim($name, '/');
    $file = str_ends_with($name, '.php') ? $name : "{$name}.php";

    foreach (components_path() as $base) {
        $candidate = "{$base}/{$file}";
        if (is_file($candidate)) {
            return $candidate;
        }
    }
    return null;
}

function _capture(callable $fn): string {
    ob_start();
    $fn();
    return (string) ob_get_clean();
}

function component(string $name, array $props = [], ?callable $children = null): void {
    $file = resolve_component($name);
    if (!$file) {
        throw new RuntimeException("Component not found: {$name}");
    }
    $slot = $children ? _capture($children) : '';
    require $file;
}

function component_once(string $name, array $props = [], ?callable $children = null): void {
    static $seen = [];
    $key = $name . '|' . md5(serialize($props));
    if (isset($seen[$key])) return;
    $seen[$key] = true;
    component($name, $props, $children);
}

