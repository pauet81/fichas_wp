<?php

add_action('comment_form_logged_in_after', 'asap_comment_rating_rating_field');
add_action('comment_form_after_fields', 'asap_comment_rating_rating_field');
function asap_comment_rating_rating_field() {
    global $post;
    $post_type = get_post_type($post);
    if (get_option("asap_stars_enable_{$post_type}") !== '1') {
        return;
    }

    $legend_texts = json_encode(array(
        __('Not useful', 'asap'),
        __('So-so', 'asap'),
        __('Normal', 'asap'),
        __('Useful', 'asap'),
        __('Very useful', 'asap')
    ));

    $rating_text = __('Your score', 'asap');

    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ratings = document.querySelectorAll(".rating input");
            const legend = document.getElementById("rating-legend");
            const legendTexts = <?php echo $legend_texts; ?>;
            const ratingText = "<?php echo $rating_text; ?>";
            
            // Configurar la leyenda inicial
            const defaultRating = document.querySelector(".rating input:checked");
            if (defaultRating) {
                legend.innerHTML = ratingText + ": <strong>" + legendTexts[defaultRating.value - 1] + "</strong>";
            } else {
                legend.innerHTML = ratingText + ": <strong>" + legendTexts[2] + "</strong>";
            }

            ratings.forEach(rating => {
                rating.addEventListener("change", function() {
                    const value = this.value;
                    legend.innerHTML = ratingText + ": <strong>" + legendTexts[value - 1] + "</strong>";
                });
            });
        });
    </script>

    <?php
}

add_action('comment_post', 'asap_comment_rating_save_comment_rating');
function asap_comment_rating_save_comment_rating($comment_id) {
    $post_id = get_comment($comment_id)->comment_post_ID;
    $post_type = get_post_type($post_id);
    if (get_option("asap_stars_enable_{$post_type}") !== '1') {
        return;
    }

    if (isset($_POST['rating']) && '' !== $_POST['rating']) {
        $rating = intval($_POST['rating']);
        add_comment_meta($comment_id, 'rating', $rating);

        // Actualizar la calificación promedio y el número de votos del post
        asap_update_post_rating($post_id);
    }
}

function asap_update_post_rating($post_id) {
    $average_rating = asap_comment_rating_get_average_ratings($post_id);
    $votes = asap_comment_rating_get_votes($post_id);
    update_post_meta($post_id, 'average_rating', $average_rating);
    update_post_meta($post_id, 'rating_votes', $votes);
}
// Función para actualizar la calificación promedio del post
function asap_update_post_average_rating($post_id) {
    $average_rating = asap_comment_rating_get_average_ratings($post_id);
    update_post_meta($post_id, 'average_rating', $average_rating);
}
add_filter('preprocess_comment', 'asap_comment_rating_require_rating');
function asap_comment_rating_require_rating($commentdata) {
    $post_id = $commentdata['comment_post_ID'];
    $post_type = get_post_type($post_id);
    if (get_option("asap_stars_enable_{$post_type}") !== '1') {
        return $commentdata;
    }

    if (!is_admin() && (!isset($_POST['rating']) || 0 === intval($_POST['rating']))) {
        wp_die(__('Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.'));
    }
    return $commentdata;
}

add_filter('comment_text', 'asap_comment_rating_display_rating');
function asap_comment_rating_display_rating($comment_text) {
    $comment_id = get_comment_ID();
    $comment = get_comment($comment_id);
    
    // Verificar si se obtuvo un comentario válido
    if (!$comment) {
        return $comment_text;
    }
    
    $post_id = $comment->comment_post_ID;
    $post_type = get_post_type($post_id);
    if (get_option("asap_stars_enable_{$post_type}") !== '1') {
        return $comment_text;
    }

    if ($rating = get_comment_meta($comment_id, 'rating', true)) {
        $stars = '<p class="stars">';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $rating ? '<span style="color: #e88330;">&#9733;</span>' : '<span style="color: #adb5bd;">&#9733;</span>';
        }
        $stars .= '</p>';
        $comment_text .= $stars;
    }
    return $comment_text;
}


function asap_comment_rating_get_average_ratings($id) {
    $comments = get_approved_comments($id);
    if ($comments) {
        $i = 0;
        $total = 0;
        foreach ($comments as $comment) {
            $rate = get_comment_meta($comment->comment_ID, 'rating', true);
            if (isset($rate) && '' !== $rate) {
                $i++;
                $total += $rate;
            }
        }
        if (0 === $i) {
            return false;
        } else {
            return round($total / $i, 1);
        }
    } else {
        return false;
    }
}

function asap_comment_rating_get_votes($id) {
    $comments = get_approved_comments($id);
    $votes = 0;
    if ($comments) {
        foreach ($comments as $comment) {
            $rate = get_comment_meta($comment->comment_ID, 'rating', true);
            if (isset($rate) && '' !== $rate) {
                $votes++;
            }
        }
    }
    return $votes;
}


add_action('add_meta_boxes', 'asap_register_meta_boxes', 20);
function asap_register_meta_boxes() {
    $post_types = get_post_types(array('public' => true), 'names');
    foreach ($post_types as $post_type) {
        if (get_option("asap_stars_enable_{$post_type}") == '1') {
            add_meta_box('asap_rating_metabox', 'ASAP − Valoración', 'asap_display_rating_metabox', $post_type, 'normal', 'high');
        }
    }
}

 function asap_display_rating_metabox($post) {
    $average_rating = get_post_meta($post->ID, 'average_rating', true);
    if ($average_rating === '') {
        $average_rating = 0;
    }
    $votes = get_post_meta($post->ID, 'rating_votes', true);
    if ($votes === '') {
        $votes = 0;
    }
    wp_nonce_field(basename(__FILE__), 'asap_rating_nonce');
    ?>
    <div class="postmetabox">
        <div class="postmetabox_left">
            <div class="metabox_option metabox_mbottom">
                <label class="label"><?php _e('Puntaje promedio', 'asap'); ?></label>
            </div>
            <div class="metabox_option">
                <label class="label"><?php _e('Cantidad de votos', 'asap'); ?></label>
            </div>
        </div>
        <div class="postmetabox_right">
            <div class="metabox_option">
                <input type="number" name="average_rating" id="average_rating" value="<?php echo esc_attr($average_rating); ?>" step="0.1" min="0" max="5">
            </div>
            <div class="metabox_option">
                <input type="number" name="rating_votes" id="rating_votes" value="<?php echo esc_attr($votes); ?>" min="0">
            </div>
        </div>
    </div>
    <style>
        .metabox_option input[type=number] {
            width: 20% !important;
            padding: 6px 10px;
        }
    </style>
    <?php
}
   

add_action('save_post', 'asap_save_rating_metabox');
function asap_save_rating_metabox($post_id) {
    if (!isset($_POST['asap_rating_nonce']) || !wp_verify_nonce($_POST['asap_rating_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    $post_type = get_post_type($post_id);
    if (get_option("asap_stars_enable_{$post_type}") !== '1') {
        return $post_id;
    }

    $average_rating = (isset($_POST['average_rating']) && is_numeric($_POST['average_rating'])) ? floatval($_POST['average_rating']) : 0;
    $rating_votes = (isset($_POST['rating_votes']) && is_numeric($_POST['rating_votes'])) ? intval($_POST['rating_votes']) : 0;

    update_post_meta($post_id, 'average_rating', $average_rating);
    update_post_meta($post_id, 'rating_votes', $rating_votes);
}
