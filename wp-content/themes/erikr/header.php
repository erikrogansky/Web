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
            <button id="dark-mode-toggle" class="btn btn--outline-primary btn--icon-circle dark-mode-toggle" aria-pressed="false" aria-label="Toggle Dark Mode">
                <i id="dark-mode-toggle-icon-moon" data-lucide="moon-star"></i>
                <i id="dark-mode-toggle-icon-sun" data-lucide="sun"></i>
                <span id="dark-mode-toggle-label" class="visually-hidden">Toggle Dark Mode</span>
            </button>
            <a class="btn btn--primary contact-button" href="/contact">Get In Touch</a>
            <button class="btn btn--icon menu-icon"><i data-lucide="menu"></i></button>
        </div>
    </header>

    <div class="mobile-menu-backdrop"></div>
    <div class="mobile-menu-container">
        <a href="/" class="site-branding not-selectable clickable">
            <span class="site-title">Erik Roganský</span>
            <span class="site-description">UX/UI Designer • Developer • Translator</span>
        </a>

        <?php
        wp_nav_menu( array(
            'theme_location' => 'header_menu',
            'container'      => false,
            'container_class' => '',
            'menu_class'     => 'mobile-menu-nav',
            'walker'         => new Mobile_Menu_Walker(),
        ) );
        ?>

        <div class="mobile-menu-ctas">
            <a class="btn btn--primary btn--fill-width contact-button-mobile" href="/contact">Get In Touch</a>
            <button id="dark-mode-toggle-mobile" class="btn btn--outline-primary btn--fill-width dark-mode-toggle-mobile" aria-pressed="false" aria-label="Toggle Dark Mode">
                <i id="dark-mode-toggle-icon-moon-mobile" data-lucide="moon-star"></i>
                <i id="dark-mode-toggle-icon-sun-mobile" data-lucide="sun"></i>
                <span id="dark-mode-toggle-label-mobile">Toggle Dark Mode</span>
            </button>
        </div>
    </div>
    

    <div id="bg-bubbles" aria-hidden="true"></div>
