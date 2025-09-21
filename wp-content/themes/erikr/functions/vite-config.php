<?php
function theme_vite_is_dev_running($devHost = 'http://host.docker.internal:3000'): bool {
    $url = rtrim($devHost, '/') . '/@vite/client';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_TIMEOUT_MS     => 250,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return $code >= 200 && $code < 500;
}

function theme_vite_manifest(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $path = get_theme_file_path('public/dist/.vite/manifest.json');
    if (!file_exists($path)) return $cache = [];
    $json = file_get_contents($path);
    return $cache = (json_decode($json, true) ?: []);
}

add_action('wp_enqueue_scripts', function () {
    $dist_path = get_theme_file_path('public/dist');
    $dist_url  = get_theme_file_uri('public/dist');

    $handle  = 'theme-app';
    $devHost = 'http://localhost:3000';

    if (theme_vite_is_dev_running()) {
        echo '<script type="module" src="' . $devHost . '/@vite/client"></script>';
        echo '<script type="module" src="' . $devHost . '/src/ts/app.ts"></script>';
        return;
    }

    $app_js = glob($dist_path . '/app.*.js');
    if (!empty($app_js)) {
        $app_js_url = $dist_url . '/' . basename($app_js[0]);
        wp_enqueue_script('theme-app', $app_js_url, [], null, true);
        wp_script_add_data('theme-app', 'type', 'module');
    }

    $styles_css = glob($dist_path . '/styles.*.css');
    if (!empty($styles_css)) {
        $styles_css_url = $dist_url . '/' . basename($styles_css[0]);
        wp_enqueue_style('theme-styles', $styles_css_url, [], null);
    }
}, 20);
