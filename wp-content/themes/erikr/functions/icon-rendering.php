<?php
function render_icon($url, $alt = '') {
    if (!is_string($url) || $url === '') return '';

    if (!preg_match('/\.svg(\?.*)?$/i', $url)) {
        return '<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" />';
    }

    $uploads = wp_get_upload_dir();
    $baseurl = trailingslashit($uploads['baseurl']);
    $basedir = trailingslashit($uploads['basedir']);

    if (strpos($url, $baseurl) !== 0) {
        return '<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" />';
    }

    $path = $basedir . ltrim(str_replace($baseurl, '', $url), '/');
    if (!file_exists($path) || !is_readable($path)) {
        return '<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" />';
    }

    $svg = file_get_contents($path);
    if (!$svg) return '<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" />';

    $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
    $svg = preg_replace('/<!DOCTYPE.*?>/is', '', $svg);

    $allowed = [
        'svg' => ['xmlns'=>true,'viewBox'=>true,'width'=>true,'height'=>true,'role'=>true,'aria-hidden'=>true,'focusable'=>true,'fill'=>true,'stroke'=>true,'class'=>true],
        'g' => ['fill'=>true,'stroke'=>true,'class'=>true,'clip-path'=>true],
        'path' => ['d'=>true,'fill'=>true,'stroke'=>true,'class'=>true,'fill-rule'=>true,'clip-rule'=>true,'stroke-width'=>true,'stroke-linecap'=>true,'stroke-linejoin'=>true],
        'circle'=>['cx'=>true,'cy'=>true,'r'=>true,'fill'=>true,'stroke'=>true,'class'=>true],
        'rect'  =>['x'=>true,'y'=>true,'width'=>true,'height'=>true,'rx'=>true,'ry'=>true,'fill'=>true,'stroke'=>true,'class'=>true],
        'polygon'=>['points'=>true,'fill'=>true,'stroke'=>true,'class'=>true],
        'polyline'=>['points'=>true,'fill'=>true,'stroke'=>true,'class'=>true],
        'line'   =>['x1'=>true,'y1'=>true,'x2'=>true,'y2'=>true,'stroke'=>true,'class'=>true],
        'defs'=>[], 'title'=>[], 'clipPath'=>['id'=>true], 'mask'=>['id'=>true],
        'use'=>['href'=>true,'xlink:href'=>true],
    ];
    $svg = wp_kses($svg, $allowed);

    if ($alt !== '') {
        if (strpos($svg, '<title') === false) {
            $svg = preg_replace('/<svg\b([^>]*)>/', '<svg$1 role="img"><title>'.esc_html($alt).'</title>', $svg, 1);
        } else {
            $svg = preg_replace('/<svg\b([^>]*)>/', '<svg$1 role="img">', $svg, 1);
        }
    } else {
        $svg = preg_replace('/<svg\b([^>]*)>/', '<svg$1 aria-hidden="true" focusable="false">', $svg, 1);
    }

    return $svg;
}
