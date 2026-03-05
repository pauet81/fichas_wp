<?php

function asap_search_autocomplete() {
    if (!isset($_POST['query'])) {
        return;
    }

    $query = sanitize_text_field($_POST['query']);
    $enabled = get_option('asap_predictive_enabled', '0');
    if ($enabled !== '1') {
        return;
    }

    $post_types = array();
    $all_post_types = get_post_types(array('public' => true), 'names');
    foreach ($all_post_types as $post_type) {
        if (get_option("asap_predictive_{$post_type}") === '1') {
            $post_types[] = $post_type;
        }
    }

    $results_count = get_option('asap_predictive_results_count', '5');

    $args = array(
        's' => $query,
        'posts_per_page' => $results_count,
        'post_status' => 'publish',
        'post_type' => $post_types
    );
    $search_query = new WP_Query($args);

    if ($search_query->have_posts()) {
        echo '<ul>';
        while ($search_query->have_posts()) {
            $search_query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
    } else {
            echo '<p>' . __('We did not find any results for your search.', 'asap') . '</p>';
    }
    wp_die();
}
add_action('wp_ajax_asap_search_autocomplete', 'asap_search_autocomplete');
add_action('wp_ajax_nopriv_asap_search_autocomplete', 'asap_search_autocomplete');


function asap_enqueue_search_autocomplete_script() {
    // Generar una versión basada en la fecha y hora actual
    $version = '0109062024';

    wp_enqueue_script('asap-search-autocomplete', get_template_directory_uri() . '/assets/js/search-autocomplete.js', array('jquery'), $version, true);

    // Pasar las URLs del sitio y ajax a JavaScript
    wp_localize_script('asap-search-autocomplete', 'asap_vars', array(
        'siteUrl' => get_site_url(),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'asap_enqueue_search_autocomplete_script');


?>