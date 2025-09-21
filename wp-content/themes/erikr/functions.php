<?php
$func_dir = get_template_directory() . '/functions';

foreach (glob($func_dir . '/*.php') as $file) {
    require_once $file;
}
