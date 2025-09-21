        <footer class="site-footer">
            <a href="/" class="site-branding not-selectable clickable">
                <span class="site-title">Erik Roganský</span>
                <span class="site-description">UX/UI Designer • Developer • Translator</span>
            </a>

            <?php
            wp_nav_menu( array(
                'theme_location' => 'social_media_links',
                'container'      => 'div',
                'container_class'=> 'social-menu',
                'menu_class'     => '',
                'items_wrap'     => '%3$s',
                'walker'         => new Social_Menu_Walker(),
            ) );
            ?>

            <?php
            wp_nav_menu( array(
                'theme_location' => 'footer_menu',
                'container'      => 'nav',
                'container_class'=> '',
                'menu_class'     => 'footer-menu',
                'walker'         => new Footer_Menu_Walker(),
            ) );
            ?>

            <span class="site-credits paragraph paragraph--mini font-weight-800">Copyright &copy; <?php echo date('Y'); ?> Erik Roganský. All rights reserved.</span>

        </footer>
    <?php wp_footer(); ?>
    </body>
</html>