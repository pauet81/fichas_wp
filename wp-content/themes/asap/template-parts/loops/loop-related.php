<?php
$related_by      = get_theme_mod('asap_related_by', 1);
$show            = (int) get_theme_mod('asap_number_related_single', 6);
$show_sidebar    = get_theme_mod('asap_show_sidebar_single', 0); 
$show_last_post  = get_theme_mod('asap_show_last_single', 0);
$related_title_text = get_theme_mod('asap_related_title_text', ''); 
$showside        = (int) get_theme_mod('asap_showposts_last_sidebar', 5);
$typeside        = (int) get_theme_mod('asap_sidebar_type_posts', 0);

$enable_circular = (int) get_theme_mod('asap_enable_circular_linking', 0);
$current_id      = get_the_ID();
$post_type       = get_post_type($current_id);

// --- Caso A: Enlazado circular activo ---
if ( $enable_circular ) {

    // Definimos base de filtrado por taxonomía de acuerdo a $related_by
    $base_args = [
        'post_type' => $post_type,
    ];

    $offset_circ = 0;

    switch ((int) $related_by) {
        case 1: // por categoría
            $base_args['category__in'] = current_category();
            if ($typeside == 1 && $show_sidebar && $show_last_post) {
                $offset_circ = $showside;
            }
            break;

        case 2: // por etiquetas
            $tags    = wp_get_post_tags($current_id);
            $tag_ids = wp_list_pluck($tags, 'term_id');
            if (!empty($tag_ids)) {
                $base_args['tag__in'] = $tag_ids;
            }
            if ($typeside == 3 && $show_sidebar && $show_last_post) {
                $offset_circ = $showside;
            }
            break;

        case 3: // "aleatorio" no aplica al anillo; usamos todo el post_type con orden estable
        default:
            // sin filtros de taxonomía
            break;
    }

    // Orden estable para el anillo (evita rand)
    $order_args = [
        'orderby' => 'date',
        'order'   => 'DESC',
    ];

    // Calculamos los IDs "siguientes" en el anillo
    $ids_circular = asap_get_circular_related_ids(
        $current_id,
        array_merge($base_args, ['post_type' => $post_type]),
        $show,
        $offset_circ,
        $order_args
    );

    // Si por alguna razón no hay suficientes, no mostramos nada
    if ( ! empty($ids_circular) ) {

        $query = new WP_Query([
            'post_type'      => $post_type,
            'posts_per_page' => count($ids_circular),
            'post__in'       => $ids_circular,
            'orderby'        => 'post__in', // mantener el orden calculado
            'no_found_rows'  => true,
        ]);

        if ($query->have_posts()) :
            if ($related_title_text) : ?>
                <div class="comment-respond others-items"><p><?php echo esc_html($related_title_text); ?></p></div>
            <?php endif; ?>

            <div class="related-posts">
                <?php
                while ($query->have_posts()) : $query->the_post();
                    get_template_part('template-parts/content/content', 'loop-related');
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php endif;
    }

// --- Caso B: Modo actual (no circular), tu lógica original ---
} else {

    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => $show,
        'post__not_in'   => array($current_id),
    );

    switch ($related_by) {
        case 1:
            $args['category__in'] = current_category();
            if ($typeside == 1 && $show_sidebar && $show_last_post) {
                $args['offset'] = $showside;
            }
            break;

        case 2:
            $tags    = wp_get_post_tags($current_id);
            $tag_ids = wp_list_pluck($tags, 'term_id');
            if (!empty($tag_ids)) {
                $args['tag__in'] = $tag_ids;
            }
            if ($typeside == 3 && $show_sidebar && $show_last_post) {
                $args['offset'] = $showside;
            }
            break;

        case 3:
            $args['orderby'] = 'rand';
            break;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        if ($related_title_text) : ?>
            <div class="comment-respond others-items"><p><?php echo esc_html($related_title_text); ?></p></div>
        <?php endif; ?>

        <div class="related-posts">
            <?php
            while ($query->have_posts()) : $query->the_post();
                get_template_part('template-parts/content/content', 'loop-related');
            endwhile;
            wp_reset_postdata();
            ?>
        </div>

    <?php endif;
}
?>
