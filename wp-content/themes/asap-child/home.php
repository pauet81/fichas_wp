<?php
/**
 * Blog index template (posts page)
 */
get_header();
?>

<main class="blog-index">
    <section class="blog-hero">
        <div class="blog-hero-inner">
            <h1>Blog</h1>
            <p>Novedades, recursos y consejos para acompañar el aprendizaje.</p>
        </div>
    </section>

    <section class="blog-wrapper">
        <?php
        $blog_categories = get_categories(array(
            'taxonomy'   => 'category',
            'hide_empty' => true,
            'parent'     => 0,
        ));
        ?>
        <?php if ( ! empty( $blog_categories ) ) : ?>
            <div class="blog-categories">
                <?php foreach ( $blog_categories as $cat ) : ?>
                    <a class="blog-cat-card" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                        <span class="blog-cat-title"><?php echo esc_html( $cat->name ); ?></span>
                        <span class="blog-cat-count"><?php echo intval( $cat->count ); ?> entradas</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( have_posts() ) : ?>
            <div class="blog-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php
                    $thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                    $has_thumb = ! empty( $thumb );
                    $date = get_the_date();
                    $cats = get_the_category_list(', ');
                    $author = get_the_author();
                    $excerpt = get_the_excerpt();
                    ?>
                    <article <?php post_class('blog-card'); ?>>
                        <a class="blog-card-link" href="<?php the_permalink(); ?>">
                            <div class="blog-card-media<?php echo $has_thumb ? '' : ' is-fallback'; ?>"<?php echo $has_thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ')"' : ''; ?>></div>
                            <div class="blog-card-body">
                                <div class="blog-card-meta">
                                    <span><?php echo esc_html( $date ); ?></span>
                                    <span class="blog-card-sep">·</span>
                                    <span>Por <?php echo esc_html( $author ); ?></span>
                                    <?php if ( $cats ) : ?>
                                        <span class="blog-card-sep">·</span>
                                        <span class="blog-card-cats"><?php echo $cats; ?></span>
                                    <?php endif; ?>
                                </div>
                                <h2 class="blog-card-title"><?php the_title(); ?></h2>
                                <?php if ( $excerpt ) : ?>
                                    <p class="blog-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="blog-pagination">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <p class="blog-empty">Aún no hay artículos publicados.</p>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>
