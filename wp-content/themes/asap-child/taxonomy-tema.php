<?php
/**
 * Template for Tema taxonomy archives.
 */
get_header();

$term = get_queried_object();
$term_name = $term ? $term->name : '';
$term_desc = $term ? $term->description : '';
?>

<?php if (function_exists('fichas_breadcrumbs')): ?>
    <?php fichas_breadcrumbs(); ?>
<?php endif; ?>

<section class="seccion-fichas">
    <div class="content-wrapper">
        <h1 class="section-titulo">Tema: <?php echo esc_html($term_name); ?></h1>
        <?php if (!empty($term_desc)): ?>
            <p class="section-descripcion"><?php echo esc_html($term_desc); ?></p>
        <?php endif; ?>

        <?php
        if ($term && !is_wp_error($term)) {
            $args = array(
                'post_type' => 'ficha',
                'post_status' => 'publish',
                'posts_per_page' => 50,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'tema',
                        'field' => 'term_id',
                        'terms' => $term->term_id
                    )
                )
            );

            $fichas_query = new WP_Query($args);
            if ($fichas_query->have_posts()):
        ?>
            <div class="fichas-grid">
                <?php while ($fichas_query->have_posts()) : $fichas_query->the_post(); ?>
                    <?php
                    $edad = get_field('edad_recomendada');
                    $tiempo = get_field('tiempo_estimado');
                    $asignaturas_ficha = get_the_terms(get_the_ID(), 'asignatura');
                    $dificultad = get_the_terms(get_the_ID(), 'dificultad');
                    $nivel = get_the_terms(get_the_ID(), 'nivel-educativo');
                    $curso = get_the_terms(get_the_ID(), 'curso');

                    $nivel_class = 'nivel-primaria';
                    if ($nivel && !is_wp_error($nivel)) {
                        $nivel_slug_item = $nivel[0]->slug;
                        if ($nivel_slug_item === 'infantil') {
                            $nivel_class = 'nivel-infantil';
                        } elseif ($nivel_slug_item === 'secundaria') {
                            $nivel_class = 'nivel-secundaria';
                        }
                    }
                    ?>
                    <div class="ficha-card-mini-v2 <?php echo $nivel_class; ?>">
                        <a href="<?php the_permalink(); ?>" class="ficha-card-link-v2">
                            <div class="ficha-card-header-v2">
                                <div class="badges-row">
                                    <?php if ($nivel && !is_wp_error($nivel)): ?>
                                        <span class="badge-nivel-mini"><?php echo esc_html($nivel[0]->name); ?></span>
                                    <?php endif; ?>
                                    <?php if ($curso && !is_wp_error($curso)): ?>
                                        <span class="badge-curso-mini"><?php echo esc_html($curso[0]->name); ?></span>
                                    <?php endif; ?>
                                    <?php if ($asignaturas_ficha && !is_wp_error($asignaturas_ficha)): ?>
                                        <span class="badge-asignatura-mini"><?php echo esc_html($asignaturas_ficha[0]->name); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="badge-vistas-card">
                                    &#x1F441;&#xFE0F; <?php echo fichas_format_vistas(get_the_ID()); ?>
                                </div>
                            </div>

                            <div class="ficha-card-body-v2">
                                <h3 class="ficha-card-titulo-v2"><?php the_title(); ?></h3>
                                <div class="ficha-card-meta-v2">
                                    <?php if ($edad): ?>
                                        <span class="meta-item-v2">
                                            <span class="meta-icon">&#x1F464;</span>
                                            <span class="meta-text"><?php echo esc_html($edad); ?> a&ntilde;os</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($tiempo): ?>
                                        <span class="meta-item-v2">
                                            <span class="meta-icon">&#x23F1;&#xFE0F;</span>
                                            <span class="meta-text"><?php echo esc_html($tiempo); ?> min</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($dificultad && !is_wp_error($dificultad)): ?>
                                        <span class="meta-item-v2">
                                            <span class="meta-icon">&#x2B50;</span>
                                            <span class="meta-text"><?php echo esc_html($dificultad[0]->name); ?></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="ficha-card-footer-v2">
                                <span class="ficha-card-cta-v2">
                                    <span class="cta-text">Empezar ficha</span>
                                    <span class="cta-arrow">&#x2192;</span>
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php
            else:
        ?>
            <div class="no-fichas">
                <p>Todavia no hay fichas en este tema.</p>
            </div>
        <?php
            endif;
            wp_reset_postdata();
        }
        ?>
    </div>
</section>

<?php get_footer(); ?>
