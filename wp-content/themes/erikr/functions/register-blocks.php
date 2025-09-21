<?php
add_action('elementor/elements/categories_registered', function($elements_manager) {
    $elements_manager->add_category('er-elements', [
        'title' => 'Erik Elements',
    ]);
});

add_action('elementor/widgets/register', function($widgets_manager) {
    $before = get_declared_classes();

    $dir = new RecursiveDirectoryIterator(__DIR__ . '/../blocks');
    $iter = new RecursiveIteratorIterator($dir);
    $phpFiles = new RegexIterator($iter, '/\.php$/i');

    foreach ($phpFiles as $file) {
        require_once $file->getPathname();
    }

    $after = get_declared_classes();
    $new_classes = array_diff($after, $before);

    foreach ($new_classes as $class) {
        if (is_subclass_of($class, \Elementor\Widget_Base::class)) {
            $widgets_manager->register(new $class());
        }
    }
});

add_action( 'elementor/editor/after_enqueue_scripts', function() {
    ?>
    <style>
        #elementor-panel-category-pro-elements,
        #elementor-panel-category-woocommerce-elements,
        #elementor-panel-category-theme-elements,
        #elementor-panel-category-general,
        #elementor-panel-category-favorites,
        #elementor-panel-category-layout,
        #elementor-panel-category-link-in-bio,
        #elementor-panel-category-helloplus,
        #elementor-panel-category-v4-elements,
        #elementor-panel-category-theme-elements-single,
        #elementor-panel-category-get-pro-elements,
        #elementor-panel-category-get-pro-elements-sticky,
        .elementor-nerd-box,
        #elementor-panel-get-pro-elements-sticky {
            display: none !important;
        }
    </style>
    <?php
});