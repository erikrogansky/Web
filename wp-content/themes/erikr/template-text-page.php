<?php
/**
 * Template Name: Text Page
 * Description: Simple template for text-heavy pages like privacy policy, terms, etc.
 */

get_header();
?>

<main class="text-page">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('text-page__content'); ?>>
            <header class="text-page__header">
                <h1 class="text-page__title"><?php the_title(); ?></h1>
                <?php if ( get_the_modified_time() ) : ?>
                    <p class="text-page__meta paragraph paragraph--small">
                        Last updated: <time datetime="<?php echo get_the_modified_date('c'); ?>"><?php echo get_the_modified_date(); ?></time>
                    </p>
                <?php endif; ?>
            </header>

            <div class="text-page__body">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();
?>
