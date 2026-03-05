<?php
/**
 * Single post template
 */
get_header();
?>

<main class="blog-single">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <?php
        $thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        $has_thumb = ! empty( $thumb );
        $date = get_the_date();
        $cats = get_the_category_list(', ');
        $author = get_the_author();
        ?>

        <section class="blog-single-hero<?php echo $has_thumb ? '' : ' is-fallback'; ?>"<?php echo $has_thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ')"' : ''; ?>>
            <div class="blog-single-hero-inner">
                <div class="blog-single-meta">
                    <span><?php echo esc_html( $date ); ?></span>
                    <span class="blog-card-sep">·</span>
                    <span>Por <?php echo esc_html( $author ); ?></span>
                    <?php if ( $cats ) : ?>
                        <span class="blog-card-sep">·</span>
                        <span class="blog-card-cats"><?php echo $cats; ?></span>
                    <?php endif; ?>
                </div>
                <h1><?php the_title(); ?></h1>
            </div>
        </section>

        <section class="blog-single-content">
            <?php if ( function_exists('fichas_breadcrumbs') ) { fichas_breadcrumbs(); } ?>
            <article class="blog-single-article">
                <?php the_content(); ?>
            </article>

            <nav class="blog-single-nav">
                <div class="blog-single-prev"><?php previous_post_link('%link', '← Anterior'); ?></div>
                <div class="blog-single-next"><?php next_post_link('%link', 'Siguiente →'); ?></div>
            </nav>
        </section>
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
