<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>> 
    <header class="site-header">
        <a id="header-site-branding" href="/" class="site-branding not-selectable clickable">
            <span class="site-title">Erik Roganský</span>
            <span class="site-description">UX/UI Designer • Developer • Translator</span>
        </a>

        <?php
        wp_nav_menu( array(
            'theme_location' => 'header_menu',
            'container'      => 'nav',
            'menu_class'     => 'header-menu',
            'walker'         => new Header_Menu_Walker(),
        ) );
        ?>

        <div id="header-site-ctas" class="site-ctas">
            <button id="dark-mode-toggle" class="btn btn--outline-primary btn--icon-circle">
                <i id="dark-mode-toggle-icon-moon" data-lucide="moon-star"></i>
                <i id="dark-mode-toggle-icon-sun" data-lucide="sun"></i>
                <span class="visually-hidden">Toggle Dark Mode</span>
            </button>
            <a class="btn btn--primary" href="/contact">Get In Touch</a>
        </div>
    </header>

    <div id="bg-bubbles" aria-hidden="true"></div>
