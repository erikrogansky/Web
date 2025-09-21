<?php
function mytheme_register_menus() {
    register_nav_menus( array(
        'header_menu' => __( 'Header', 'erikr' ),
        'footer_menu' => __( 'Footer', 'erikr' ),
        'social_media_links' => __( 'Social Media Links', 'erikr' ),
    ) );
}
add_action( 'after_setup_theme', 'mytheme_register_menus' );
