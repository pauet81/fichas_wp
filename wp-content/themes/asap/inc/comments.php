<?php
function asap_enable_threaded_comments() {
    if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('get_header', 'asap_enable_threaded_comments');

add_filter('comment_form_default_fields', 'asap_disable_url_comment');
function asap_disable_url_comment($fields) {
    if (!get_option('asap_show_comments_url')) {
        unset($fields['url']);
        return $fields;
    }
}

add_filter('comment_form_defaults', 'asap_modify_fields_form');
function asap_modify_fields_form($args) {
    global $post;
    $post_type = get_post_type($post);
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " required " : '');

    $author = '<input placeholder="' . __('Name') . ($req ? ' *' : '') . '" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' />';
    $email = '<div class="fields-wrap"><input placeholder="' . __('Email') . ($req ? ' *' : '') . '" id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' />';
    $url = '<div class="fields-wrap"><input placeholder="' . __('URL') . '" id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" />';
    $comment = '<textarea placeholder="' . _x('Comment', '') . '" id="comment" name="comment" cols="45" rows="5" required></textarea>';

    // Agregar el campo de calificación de estrellas solo si está habilitado para el tipo de post actual
    if (get_option("asap_stars_enable_{$post_type}") == '1') {
        $rating_text_c = __('Your score', 'asap');
        $legend_text_c = __('Useful', 'asap');
        $rating = '<fieldset class="rating">';
        for ($i = 5; $i >= 1; $i--) {
            $checked = ($i == 4) ? 'checked' : ''; // Seleccionar por defecto "Útil" que es la posición 4
            $rating .= '<input type="radio" id="rating-' . $i . '" name="rating" value="' . $i . '" ' . $checked . ' />
                        <label for="rating-' . $i . '" title="' . $i . ' stars">&#9733;</label>';
        }
        $rating .= '</fieldset>';
        $rating .= '<div id="rating-legend">'.$rating_text_c.': <strong>'.$legend_text_c.'</strong></div>'; // Leyenda por defecto

        $comment .= $rating; // Coloca el campo de calificación debajo del textarea de comentarios
    }

    $args['fields']['author'] = $author;
    $args['fields']['email'] = $email;

    if (get_option('asap_show_comments_url')) {
        $args['fields']['url'] = $url;
    }

    $args['comment_field'] = $comment;
    return $args;
}

add_filter('comment_form_fields', 'asap_modify_order_fields');
function asap_modify_order_fields($fields) {
    $val = $fields['comment'];
    unset($fields['comment']);
    $fields += array('comment' => $val);
    return $fields;
}

add_filter('comment_text', 'asap_wrap_comments_div');
function asap_wrap_comments_div($content) {
    return '<div class="asap-user-comment-text">' . wpautop($content) . '</div>';
}
?>