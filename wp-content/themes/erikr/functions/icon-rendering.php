<?php
function render_icon($url, $alt = '') {
    if (!is_string($url) || $url === '') {
        return '';
    }

    $alt = esc_attr($alt);

    if (preg_match('/\.svg(\?.*)?$/i', $url)) {
        $path = parse_url($url, PHP_URL_PATH);

        $file = ABSPATH . ltrim(str_replace(site_url('/'), '', $url), '/');

        if (file_exists($file)) {
            return file_get_contents($file);
        }

        return '<img src="' . esc_url($url) . '" alt="' . $alt . '">';
    }

    return '<img src="' . esc_url($url) . '" alt="' . $alt . '">';
}
