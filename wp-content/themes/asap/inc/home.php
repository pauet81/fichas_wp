<?php

function asap_get_excluded_post_ids() {
    // Obtener todos los posts excluidos
    $post_type = get_theme_mod('asap_home_content_type', 'post');
    $excluded_posts = get_posts(array(
        'post_type' => $post_type,
        'meta_key' => 'asap_exclude_news',
        'meta_value' => '1',
        'fields' => 'ids',
        'posts_per_page' => -1,
    ));
    return $excluded_posts ? $excluded_posts : array();
}

function asap_get_theme_mods() {
    $show_stars = (get_option("asap_stars_enable_post") == '1') && get_option('asap_stars_show_loop', true);
    
    return [
        'home_categories' => get_theme_mod('asap_home_categories'),
        'home_top_articles' => get_theme_mod('asap_home_top_articles'),
        'home_show_latest_posts' => get_theme_mod('asap_home_show_latest_posts', false),
        'home_last_post_count' => get_theme_mod('asap_home_last_post_count', 8),
        'sort_order' => get_theme_mod('asap_category_sort_order', 'alphabetical'),
        'show_category' => get_theme_mod('asap_show_post_category') ? true : false,
        'show_date_loop' => get_theme_mod('asap_show_date_loop') ? true : false,
        'show_extract' => get_theme_mod('asap_show_news_extract') ? true : false,
        'show_advice_new_posts' => get_theme_mod('asap_show_advice_new_posts'),
        'columns' => get_theme_mod('asap_columns', 4),
        'show_stars' => $show_stars,
        'show_featured_first' => get_theme_mod('asap_show_featured_first', true),
        'enable_cache' => get_theme_mod('asap_home_enable_cache', false),
        'cache_period' => max(1, intval(get_theme_mod('asap_home_cache_period', 24))),
        'content_type' => get_theme_mod('asap_home_content_type', 'post'),
        'home_tags' => get_theme_mod('asap_home_tags'),
        'tag_sort_order' => get_theme_mod('asap_tag_sort_order', 'alphabetical'),        
    ];
}

function asap_get_new_post_info() {
    $show_advice_new_posts = get_theme_mod('asap_show_advice_new_posts');
    if ($show_advice_new_posts) {
        return [
            'message_new' => get_theme_mod('asap_advice_new_posts_text', 'Nuevo'),
            'days_new' => intval(get_theme_mod('asap_advice_new_posts_days', 7)),
            'current_time' => current_time('timestamp'),
        ];
    } else {
        return null;
    }
}

function asap_render_featured_posts(&$post_ids_shown, $new_post_info, $show_category, $show_date, $show_stars, $enable_cache, $cache_period, $mods, $excluded_posts, $category_id = null, $author_id = null, $tag_id = null) {
    $design_type = get_theme_mod('asap_home_design_type', 1);
    $columns = intval(get_theme_mod('asap_columns', 3));
    $posts_map = [
        1 => 5,
        2 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,            
        ],
        3 => 6,
        4 => 2,
        5 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,            
        ], 
        6 => 5,    
        7 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,            
        ],            
        8 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,            
        ],      
        9 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,            
        ],  
        10 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        11 => 5,
        12 => 5,  
        13 => 6,        
                                    
    ];

    $number_of_posts = $posts_map[$design_type][$columns] ?? $posts_map[$design_type] ?? 5;

    $transient_key = '';
    $cached_posts = false;
    
    if ($enable_cache) {
        $transient_key_parts = compact('category_id', 'author_id', 'tag_id');
        $transient_key = 'asap_cache_featured_posts_' . md5(json_encode($transient_key_parts) . json_encode($mods));
        $cached_posts = get_transient($transient_key);
    }

    if ($cached_posts === false) {
        $query_args = [
            'post_type' => $mods['content_type'],
            'posts_per_page' => $number_of_posts,
            'post__not_in' => array_merge($post_ids_shown, $excluded_posts),
            'orderby' => $mods['show_featured_first'] ? ['meta_value_num' => 'DESC', 'date' => 'DESC'] : 'date',
            'order' => 'DESC',
            'fields' => ['ID', 'post_title', 'post_date'],
            'meta_query' => $mods['show_featured_first'] ? [
                'relation' => 'OR',
                ['key' => 'featured_post', 'compare' => 'NOT EXISTS'],
                ['key' => 'featured_post', 'compare' => 'EXISTS']
            ] : null,
        ];

        if ($category_id) {
            $query_args['category__in'] = array_merge([$category_id], get_term_children($category_id, 'category'));
        } elseif ($author_id) {
            $query_args['author'] = $author_id;
        } elseif ($tag_id) {
            $query_args['tag_id'] = $tag_id;
        }

        $posts = get_posts($query_args);
        $cached_posts = array_map(function($post) use ($show_category, $new_post_info) {
            return [
                'post_id' => $post->ID,
                'permalink' => get_permalink($post->ID),
                'title' => $post->post_title,
                'thumbnail' => has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID, 'large') : '',
                'featured_text' => get_post_meta($post->ID, 'single_bc_featured', true) ?: __("Featured", "asap"),
                'is_new' => $new_post_info ? asap_is_new($new_post_info['days_new'], $new_post_info['current_time']) : false,
                'message_new' => $new_post_info['message_new'] ?? '',
                'category' => $show_category ? (get_the_category($post->ID)[0]->name ?? '') : '',
                'date' => $post->post_date,
            ];
        }, $posts);

        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));

        if ($enable_cache) {
            set_transient($transient_key, $cached_posts, $cache_period * HOUR_IN_SECONDS);
        }
    } else {
        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));
    }

    if ($cached_posts) {
        echo '<div class="grid-container">';
        foreach ($cached_posts as $index => $post) {
            asap_render_grid_item(
                $post['post_id'],
                $index + 1,
                $post['is_new'],
                $post['message_new'],
                $show_category,
                $show_date,
                $show_stars,
                $post['permalink'],
                $post['title'],
                $post['thumbnail'],
                $post['featured_text'],
                $post['category'],
                $post['date']
            );
        }
        echo '</div>';
    }
}


function asap_render_grid_item($post_id, $counter, $is_new, $message_new, $show_category, $show_date, $show_stars, $permalink, $title, $thumbnail, $featured_text, $category, $date) {
    $featured_post = get_post_meta($post_id, 'featured_post', true);
    $single_featured_text = get_post_meta($post_id, 'single_bc_featured', true);
    $featured_text = $single_featured_text ?: $featured_text;
    $design_type = get_theme_mod('asap_home_design_type', 1);

    echo '<div class="grid-item item-' . $counter . '">';
    echo '<a href="' . esc_url($permalink) . '">';

    echo '<div class="grid-image-container">';
    asap_show_stars_news($show_stars, $post_id);
    if ($featured_post || $single_featured_text) {
        echo "<span class='item-featured'>{$featured_text}</span>";
    }
    if ($is_new) {
        echo "<span class='item-new'>{$message_new}</span>";
    }
    $thumbnail_size = asap_thumbnail_size('featured', $counter - 1, $design_type);
    echo get_the_post_thumbnail($post_id, $thumbnail_size);
    echo '</div>';

    echo '<div class="main-h2-container">';
    asap_display_category_and_date($show_category, $show_date, $category, $date);
    echo '<h2>' . esc_html($title) . '</h2>';


    $show_extract = get_theme_mod('asap_show_news_extract', false);

    if (
        ($design_type == 5 && $counter == 1) ||         
        ($design_type == 6 && $counter == 1) || 
        ($design_type == 4 && $show_extract) || 
        ($design_type == 7 && $show_extract && $counter == 1) ||                
        $design_type == 8 ||
        ($design_type == 9 && $show_extract) ||  
        ($design_type == 10 && $counter == 1) ||     
        ($design_type == 12 && $show_extract && $counter == 1)
        ) {
        echo '<p>' . wp_kses_post(get_the_excerpt($post_id)) . '</p>';
    }

    echo '</div>';
    echo '</a>';
    echo '</div>';
}


function asap_render_latest_posts($home_last_post_count, $columns, $show_category, $show_date, $show_extract, $show_stars, $show_featured_first, $enable_cache, $cache_period, $content_type, $new_post_info, &$post_ids_shown, $excluded_posts) {

    $box_design = get_theme_mod('asap_design', false); // Definición de la variable $box_design
    $design_type = get_theme_mod('asap_home_design_type', 1);
    $columns = ($design_type == 3) ? 1 : $columns;

    $transient_key = '';
    $cached_posts = false;
    
    if ($enable_cache) {
        $transient_key = 'asap_cache_latest_posts_' . md5(json_encode([
            $home_last_post_count, 
            $columns, 
            $show_category, 
            $show_date, 
            $show_extract, 
            $show_stars, 
            $show_featured_first,
            $post_ids_shown
        ]));
        $cached_posts = get_transient($transient_key);
    }

    if ($cached_posts === false) {
        $args = [
            'post_type' => $content_type,
            'posts_per_page' => $home_last_post_count,
            'post__not_in' => array_merge($post_ids_shown, $excluded_posts),
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => ['ID', 'post_title', 'post_date'], // Limitar los campos devueltos para optimizar
        ];

        $posts = get_posts($args);
        $cached_posts = array_map(function($post) use ($new_post_info, $show_category) {
            $category_name = $show_category ? (get_the_category($post->ID)[0]->name ?? '') : '';
            return [
                'permalink' => get_permalink($post->ID),
                'title' => $post->post_title,
                'thumbnail' => has_post_thumbnail($post->ID) ? $post->ID : '',
                'post_id' => $post->ID,
                'is_featured' => get_post_meta($post->ID, 'featured_post', true),
                'is_new' => $new_post_info ? asap_is_new($new_post_info['days_new'], $new_post_info['current_time']) : false,
                'message_new' => $new_post_info['message_new'] ?? '',
                'category' => $category_name,
                'date' => $post->post_date
            ];
        }, $posts);

        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));

        if ($enable_cache) {
            set_transient($transient_key, $cached_posts, $cache_period * HOUR_IN_SECONDS);
        } 
    } else {
        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));
    }

    if (!empty($cached_posts)) {
        echo '<div class="home-categories-h2"><h2><span>' . (get_theme_mod('asap_home_last_post_legend', 'Últimos artículos') ?: 'Últimos artículos') . '</span></h2></div>';
        echo '<div class="content-area latest-post-container">';
        foreach ($cached_posts as $post) {
            echo '<article class="article-loop asap-columns-' . $columns . '">';
            echo '<a href="' . esc_url($post['permalink']) . '" rel="bookmark">';
            if ($post['thumbnail']) {
                echo '<div class="lastest-post-img">';
                asap_show_stars_news($show_stars, $post['post_id']);

               $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);

                // Verifica si es destacado o si el texto no está vacío
                if ($post['is_featured'] || !empty($featured_text)) {
                    // Si el texto está vacío, utiliza "Featured" por defecto
                    $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                    echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                }

                if ($post['is_new']) {
                    echo "<span class='item-new'>{$post['message_new']}</span>";
                }
                $thumbnail_size = asap_thumbnail_size('latest', 0, $design_type);
                echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                echo '</div>';
            }
            echo $box_design ? '<div class="home-box-loop">' : '<div class="home-content-loop">';
            asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
            echo '<h3 class="entry-title">' . esc_html($post['title']) . '</h3>';

            if ($show_extract && $design_type != 9 && $design_type != 10) {
                echo '<div class="show-extract">';
                echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                echo '</div>';
            }
            echo '</div>'; // Cierre del div home-box-loop o igual
            
            echo '</a>'; // Cierre de la etiqueta a
            echo '</article>';
        }
        echo '</div>';
    }
}


function asap_render_home_categories($home_categories, $sort_order, $show_category, $show_extract, $show_date, $show_stars, $show_featured_first, $enable_cache, $cache_period, $content_type, $new_post_info, &$post_ids_shown, $excluded_posts) {
    if (empty($home_categories)) {
        return;
    }
    $design_type = get_theme_mod('asap_home_design_type', 1);
    $columns = intval(get_theme_mod('asap_columns', 3));
    $posts_map = [
        1 => 6,
        2 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        3 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ],
        4 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ], 
        5 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],           
        6 => 6,             
        8 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ], 
        9 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        10 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],   
        11 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],  
        12 => 5,    
        13 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],                              
    ];

    $number_of_posts = $posts_map[$design_type][$columns] ?? $posts_map[$design_type] ?? 5;

    if (!is_array($home_categories)) {
        $home_categories = array_filter(explode(',', $home_categories), 'is_numeric');
    }

    $sorted_categories = false;
    if ($enable_cache) {
        $transient_key = 'asap_cache_sort_home_categories_' . md5(json_encode([$home_categories, $sort_order, $post_ids_shown]));
        $sorted_categories = get_transient($transient_key);
    }

    if ($sorted_categories === false) {
        if ($sort_order === 'post_count') {
            $sort_function = function($a, $b) {
                return get_category($b)->count - get_category($a)->count;
            };
        } elseif ($sort_order === 'comment_count') {
            $sort_function = function($a, $b) {
                return asap_get_category_comment_count($b) - asap_get_category_comment_count($a);
            };
        } elseif ($sort_order === 'alphabetical') {
            $sort_function = function($a, $b) {
                return strcmp(get_cat_name($a), get_cat_name($b));
            };
        } else {
            $sort_function = function($a, $b) {
                return strcmp(get_cat_name($a), get_cat_name($b));
            };
        }

        usort($home_categories, $sort_function);

        if ($enable_cache) {
            set_transient($transient_key, $home_categories, $cache_period * HOUR_IN_SECONDS);
        }
        $sorted_categories = $home_categories;
    }


    if (!empty($sorted_categories)) {
        echo '<div class="home-categories-container">';
        foreach ($sorted_categories as $category_id) {
            $category_name = get_cat_name($category_id);
            $category_link = get_category_link($category_id);

            $cached_posts = false;
            if ($enable_cache) {
                $transient_key = 'asap_cache_category_posts_' . $category_id . '_' . md5(json_encode([
                    'show_featured_first' => $show_featured_first,
                    'show_category' => $show_category,
                    'show_extract' => $show_extract,
                    'show_date' => $show_date,
                    'show_stars' => $show_stars,
                    'post_ids_shown' => $post_ids_shown
                ]));
                $cached_posts = get_transient($transient_key);
            }

            if ($cached_posts === false) {
                $args = [
                    'post_type' => $content_type,
                    'cat' => $category_id,
                    'posts_per_page' => $number_of_posts,
                    'post__not_in' => array_merge($post_ids_shown, $excluded_posts),
                    'orderby' => $show_featured_first ? ['meta_value_num' => 'DESC', 'date' => 'DESC'] : 'date',
                    'order' => 'DESC',
                    'meta_query' => $show_featured_first ? [
                        'relation' => 'OR',
                        ['key' => 'featured_post', 'compare' => 'NOT EXISTS'],
                        ['key' => 'featured_post', 'compare' => 'EXISTS']
                    ] : null
                ];

                // Usar get_posts en lugar de WP_Query
                $posts = get_posts($args);
                $cached_posts = array_map(function($post) use ($new_post_info, $show_category) {
                    $category_name = $show_category ? (get_the_category($post->ID)[0]->name ?? '') : '';
                    return [
                        'permalink' => get_permalink($post->ID),
                        'title' => $post->post_title,
                        'thumbnail' => has_post_thumbnail($post->ID) ? $post->ID : '',
                        'post_id' => $post->ID,
                        'is_featured' => get_post_meta($post->ID, 'featured_post', true),
                        'is_new' => $new_post_info ? asap_is_new($new_post_info['days_new'], $new_post_info['current_time']) : false,
                        'message_new' => $new_post_info['message_new'] ?? '',
                        'category' => $category_name,
                        'date' => $post->post_date
                    ];
                }, $posts);

                $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));

                if ($enable_cache) {
                    set_transient($transient_key, $cached_posts, $cache_period * HOUR_IN_SECONDS);
                }
            } else {
                $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));
            }

            if (!empty($cached_posts)) {

                if (get_theme_mod('asap_home_add_link_to_cat', true)) {
                    echo '<div class="home-categories-h2"><h2><span><a href="' . esc_url($category_link) . '">' . esc_html($category_name) . '</a></span></h2></div>';
                } else {
                    echo '<div class="home-categories-h2"><h2><span>' . esc_html($category_name) . '</span></h2></div>';
                }

                if (get_theme_mod('asap_home_show_cat_desc', false)) {
                    $description = category_description($category_id);
                    if ($description) {
                        echo '<div class="home-des-category">' . $description . '</div>';
                    }
                }

                echo '<div class="category-posts">';

                $counter = 0;
                foreach ($cached_posts as $post) {
                    if ($counter == 0) {                            
                        if ($design_type != 2 && $design_type != 3 && $design_type != 4) {
                            // Generar el post destacado solo si el design type no es 2 o 3
                            echo '<article class="featured-post">';
                            echo '<a href="' . esc_url($post['permalink']) . '">';
                            if ($post['thumbnail']) {
                                echo '<div class="featured-post-img">';
                                asap_show_stars_news($show_stars, $post['post_id']);
                                $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);

                                // Verifica si es destacado o si el texto no está vacío
                                if ($post['is_featured'] || !empty($featured_text)) {
                                    // Si el texto está vacío, utiliza "Featured" por defecto
                                    $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                                    echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                                }
                                if ($post['is_new']) {
                                    echo "<span class='item-new'>{$post['message_new']}</span>";
                                }
                                $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                                echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                                echo '</div>';
                            }
                            echo '<div class="featured-post-details">';
                            asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                            echo '<h3>' . esc_html($post['title']) . '</h3>';
                            if ($show_extract) {
                                echo '<div class="show-extract">';
                                echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                                echo '</div>';
                            }
                            echo '</div>';
                            echo '</a>';
                            echo '</article>';
                        } 
                        // Abrir la sección regular-posts para el primer post
                        echo '<div class="regular-posts">';
                    }

                    // Generar el post regular
                    if ($design_type == 2 || $design_type == 3 || $design_type == 4 || $counter > 0) {
                        echo '<article class="regular-post">';
                        echo '<a href="' . esc_url($post['permalink']) . '">';
                        if ($post['thumbnail']) {
                            $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                            echo '<div class="regular-post-img">';
                             if ($design_type == 4 || $design_type == 5 || $design_type == 7 || $design_type == 8 || $design_type == 11) {
                                asap_show_stars_news($show_stars, $post['post_id']);
                                $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);
                                // Verifica si es destacado o si el texto no está vacío
                                if ($post['is_featured'] || !empty($featured_text)) {
                                    // Si el texto está vacío, utiliza "Featured" por defecto
                                    $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                                    echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                                }
                                if ($post['is_new']) {
                                    echo "<span class='item-new'>{$post['message_new']}</span>";
                                }
                            }                           
                            echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                            echo '</div>';
                        }
                        echo '<div class="post-details">';
                        asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                        echo '<h3>' . esc_html($post['title']) . '</h3>';
                        if ($show_extract && ($design_type == 2 || $design_type == 3 || $design_type == 4 || $design_type == 8 || $design_type == 9 || $design_type == 13)) {
                            echo '<div class="show-extract">';
                            echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</a>';
                        echo '</article>';
                    }
                    $counter++;
                }
                echo '</div>'; // Cierre de regular-posts
                echo '</div>'; // Cierre de category-posts
            }
        }
        echo '</div>'; // Cierre de home-categories-container
    }
}

function asap_render_single_category($category_id, $show_category, $show_extract, $show_date, $show_stars, $show_featured_first, $enable_cache, $cache_period, $content_type, $new_post_info, &$post_ids_shown, $excluded_posts) {
    if (empty($category_id) || !is_numeric($category_id)) {
        return;
    }

    $design_type = get_theme_mod('asap_home_design_type', 1);
    $columns = intval(get_theme_mod('asap_columns', 3));
    $posts_map = [
        1 => 6,
        2 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        3 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ],
        4 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ],
        5 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],         
        6 => 6,         
        8 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],  
        9 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        10 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],  
        11 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],   
        12 => 5, 
        13 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],  
    ];

    $number_of_posts = $posts_map[$design_type][$columns] ?? $posts_map[$design_type] ?? 5;


    $category_name = get_cat_name($category_id);
    $category_link = get_category_link($category_id);

    $cached_posts = false;
    if ($enable_cache) {
        $transient_key = 'asap_cache_single_category_posts_' . $category_id . '_' . md5(json_encode([
            'show_featured_first' => $show_featured_first,
            'show_category' => $show_category,
            'show_extract' => $show_extract,
            'show_date' => $show_date,
            'show_stars' => $show_stars,
            'post_ids_shown' => $post_ids_shown
        ]));
        $cached_posts = get_transient($transient_key);
    }

    if ($cached_posts === false) {
        $args = [
            'post_type' => $content_type,
            'cat' => $category_id,
            'posts_per_page' => $number_of_posts,
            'post__not_in' => array_merge($post_ids_shown, $excluded_posts),
            'orderby' => $show_featured_first ? ['meta_value_num' => 'DESC', 'date' => 'DESC'] : 'date',
            'order' => 'DESC',
            'meta_query' => $show_featured_first ? [
                'relation' => 'OR',
                ['key' => 'featured_post', 'compare' => 'NOT EXISTS'],
                ['key' => 'featured_post', 'compare' => 'EXISTS']
            ] : null
        ];

        // Usar get_posts en lugar de WP_Query para optimizar
        $posts = get_posts($args);
        $cached_posts = array_map(function($post) use ($new_post_info, $show_category) {
            $category_name = $show_category ? (get_the_category($post->ID)[0]->name ?? '') : '';
            return [
                'permalink' => get_permalink($post->ID),
                'title' => $post->post_title,
                'thumbnail' => has_post_thumbnail($post->ID) ? $post->ID : '',
                'post_id' => $post->ID,
                'is_featured' => get_post_meta($post->ID, 'featured_post', true),
                'is_new' => $new_post_info ? asap_is_new($new_post_info['days_new'], $new_post_info['current_time']) : false,
                'message_new' => $new_post_info['message_new'] ?? '',
                'category' => $category_name,
                'date' => $post->post_date
            ];
        }, $posts);

        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));

        if ($enable_cache) {
            set_transient($transient_key, $cached_posts, $cache_period * HOUR_IN_SECONDS);
        }
    } else {
        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));
    }

    if (!empty($cached_posts)) {
        echo '<div class="home-categories-h2"><h2><span><a href="' . esc_url($category_link) . '">' . esc_html($category_name) . '</a></span></h2></div>';

         if (get_theme_mod('asap_home_show_cat_desc', false)) {
            $description = category_description($category_id);
            if ($description) {
                echo '<div class="home-des-category">' . $description . '</div>';
            }
        }
       
        echo '<div class="category-posts">';

        $counter = 0;
        foreach ($cached_posts as $post) {
            if ($counter == 0) {
                if ($design_type != 2 && $design_type != 3 && $design_type != 4) {
                    // Generar el post destacado solo si el design type no es 2 o 3
                    echo '<article class="featured-post">';
                    echo '<a href="' . esc_url($post['permalink']) . '">';
                    if ($post['thumbnail']) {
                        echo '<div class="featured-post-img">';
                        asap_show_stars_news($show_stars, $post['post_id']);
                       $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);

                        // Verifica si es destacado o si el texto no está vacío
                        if ($post['is_featured'] || !empty($featured_text)) {
                            // Si el texto está vacío, utiliza "Featured" por defecto
                            $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                            echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                        }
                        if ($post['is_new']) {
                            echo "<span class='item-new'>{$post['message_new']}</span>";
                        }
                        $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                        echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                        echo '</div>';
                    }
                    echo '<div class="featured-post-details">';
                    asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                    echo '<h3>' . esc_html($post['title']) . '</h3>';
                    if ($show_extract) {
                        echo '<div class="show-extract">';
                        echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</a>';
                    echo '</article>';
                }
                // Abrir la sección regular-posts para el primer post si es design type 3
                echo '<div class="regular-posts">';
            }

            // Generar el post regular
            if ($design_type == 2 || $design_type == 3 || $design_type == 4 || $counter > 0) {
                echo '<article class="regular-post">';
                echo '<a href="' . esc_url($post['permalink']) . '">';
                if ($post['thumbnail']) {
                    $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                    echo '<div class="regular-post-img">';
                    if ($design_type == 4 || $design_type == 5 || $design_type == 7 || $design_type == 8 || $design_type == 11) {
                        asap_show_stars_news($show_stars, $post['post_id']);
                        $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);
                        // Verifica si es destacado o si el texto no está vacío
                        if ($post['is_featured'] || !empty($featured_text)) {
                            // Si el texto está vacío, utiliza "Featured" por defecto
                            $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                            echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                        }
                        if ($post['is_new']) {
                            echo "<span class='item-new'>{$post['message_new']}</span>";
                        }
                    }                    
                    echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                    echo '</div>';
                }
                echo '<div class="post-details">';
                asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                echo '<h3>' . esc_html($post['title']) . '</h3>';
                if ($show_extract && ($design_type == 2 || $design_type == 3 || $design_type == 4 || $design_type == 8 || $design_type == 9 || $design_type == 13)) {
                    echo '<div class="show-extract">';
                    echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</a>';
                echo '</article>';
            }
            $counter++;
        }
        echo '</div>'; // Cierre de regular-posts
        echo '</div>'; // Cierre de category-posts
    }
}


function asap_render_home_tags($home_tags, $sort_order, $show_category, $show_extract, $show_date, $show_stars, $show_featured_first, $enable_cache, $cache_period, $content_type, $new_post_info, &$post_ids_shown, $excluded_posts) {
    if (empty($home_tags)) {
        return;
    }

    $design_type = get_theme_mod('asap_home_design_type', 1);
    $columns = intval(get_theme_mod('asap_columns', 3));    
    $posts_map = [
        1 => 6,
        2 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        3 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ],
        4 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ], 
        5 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],         
        6 => 6,               
        8 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],    
        9 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        10 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],   
        11 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],    
        12 => 5,  
        13 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],              
    ];

    $number_of_posts = $posts_map[$design_type][$columns] ?? $posts_map[$design_type] ?? 5;


    if (!is_array($home_tags)) {
        $home_tags = array_filter(explode(',', $home_tags), 'is_numeric');
    }

    $sorted_tags = false;
    if ($enable_cache) {
        $transient_key = 'asap_cache_sort_home_tags_' . md5(json_encode([$home_tags, $sort_order, $post_ids_shown]));
        $sorted_tags = get_transient($transient_key);
    }

    if ($sorted_tags === false) {
        if ($sort_order === 'post_count') {
            $sort_function = function($a, $b) {
                return get_tag($b)->count - get_tag($a)->count;
            };
        } elseif ($sort_order === 'comment_count') {
            $sort_function = function($a, $b) {
                return asap_get_tag_comment_count($b) - asap_get_tag_comment_count($a);
            };
        } elseif ($sort_order === 'alphabetical') {
            $sort_function = function($a, $b) {
                return strcmp(get_tag($a)->name, get_tag($b)->name);
            };
        } else {
            $sort_function = function($a, $b) {
                return strcmp(get_tag($a)->name, get_tag($b)->name);
            };
        }

        usort($home_tags, $sort_function);

        if ($enable_cache) {
            set_transient($transient_key, $home_tags, $cache_period * HOUR_IN_SECONDS);
        }
        $sorted_tags = $home_tags;
    }

    if (!empty($sorted_tags)) {
        echo '<div class="home-categories-container">';
        foreach ($sorted_tags as $tag_id) {
            $tag_name = get_tag($tag_id)->name;
            $tag_link = get_tag_link($tag_id);

            $cached_posts = false;
            if ($enable_cache) {
                $transient_key = 'asap_cache_tag_posts_' . $tag_id . '_' . md5(json_encode([
                    'show_featured_first' => $show_featured_first,
                    'show_category' => $show_category,
                    'show_extract' => $show_extract,
                    'show_date' => $show_date,
                    'show_stars' => $show_stars,
                    'post_ids_shown' => $post_ids_shown
                ]));
                $cached_posts = get_transient($transient_key);
            }

            if ($cached_posts === false) {
                $args = [
                    'post_type' => $content_type,
                    'tag_id' => $tag_id,
                    'posts_per_page' => $number_of_posts,
                    'post__not_in' => array_merge($post_ids_shown, $excluded_posts),
                    'orderby' => $show_featured_first ? ['meta_value_num' => 'DESC', 'date' => 'DESC'] : 'date',
                    'order' => 'DESC',
                    'meta_query' => $show_featured_first ? [
                        'relation' => 'OR',
                        ['key' => 'featured_post', 'compare' => 'NOT EXISTS'],
                        ['key' => 'featured_post', 'compare' => 'EXISTS']
                    ] : null
                ];

                // Usar get_posts en lugar de WP_Query para optimizar
                $posts = get_posts($args);
                $cached_posts = array_map(function($post) use ($new_post_info, $show_category) {
                    $tag_name = $show_category ? (get_the_tags($post->ID)[0]->name ?? '') : '';
                    return [
                        'permalink' => get_permalink($post->ID),
                        'title' => $post->post_title,
                        'thumbnail' => has_post_thumbnail($post->ID) ? $post->ID : '',
                        'post_id' => $post->ID,
                        'is_featured' => get_post_meta($post->ID, 'featured_post', true),
                        'is_new' => $new_post_info ? asap_is_new($new_post_info['days_new'], $new_post_info['current_time']) : false,
                        'message_new' => $new_post_info['message_new'] ?? '',
                        'category' => $tag_name,
                        'date' => $post->post_date
                    ];
                }, $posts);

                $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));

                if ($enable_cache) {
                    set_transient($transient_key, $cached_posts, $cache_period * HOUR_IN_SECONDS);
                }
            } else {
                $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));
            }

            if (!empty($cached_posts)) {
                echo '<div class="home-categories-h2"><h2><span><a href="' . esc_url($tag_link) . '">' . esc_html($tag_name) . '</a></span></h2></div>';

                if (get_theme_mod('asap_home_show_tag_desc', false)) {
                    $description = tag_description($tag_id);
                    if ($description) {
                        echo '<div class="home-des-category">' . $description . '</div>';
                    }
                }
             
                echo '<div class="category-posts">';

                $counter = 0;
                foreach ($cached_posts as $post) {
                    if ($counter == 0) {
                        if ($design_type != 2 && $design_type != 3 && $design_type != 4) {
                            // Generar el post destacado solo si el design type no es 2 o 3
                            echo '<article class="featured-post">';
                            echo '<a href="' . esc_url($post['permalink']) . '">';
                            if ($post['thumbnail']) {
                                echo '<div class="featured-post-img">';
                                asap_show_stars_news($show_stars, $post['post_id']);
                                $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);

                                // Verifica si es destacado o si el texto no está vacío
                                if ($post['is_featured'] || !empty($featured_text)) {
                                    // Si el texto está vacío, utiliza "Featured" por defecto
                                    $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                                    echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                                }
                                if ($post['is_new']) {
                                    echo "<span class='item-new'>{$post['message_new']}</span>";
                                }
                                $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                                echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                                echo '</div>';
                            }
                            echo '<div class="featured-post-details">';
                            asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                            echo '<h3>' . esc_html($post['title']) . '</h3>';
                            if ($show_extract) {
                                echo '<div class="show-extract">';
                                echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                                echo '</div>';
                            }
                            echo '</div>';
                            echo '</a>';
                            echo '</article>';
                        }
                        // Abrir la sección regular-posts para el primer post
                        echo '<div class="regular-posts">';
                    }

                    // Generar el post regular - Acá van solo los que se excluyeron arriba
                    if ($design_type == 2 || $design_type == 3 || $design_type == 4 || $counter > 0) {
                        echo '<article class="regular-post">';
                        echo '<a href="' . esc_url($post['permalink']) . '">';
                        if ($post['thumbnail']) {
                            $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                            echo '<div class="regular-post-img">';
                            asap_show_stars_news($show_stars, $post['post_id']);
                            if ($design_type == 4 || $design_type == 5 || $design_type == 7 || $design_type == 8 || $design_type == 11) {
                                asap_show_stars_news($show_stars, $post['post_id']);
                                $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);
                                // Verifica si es destacado o si el texto no está vacío
                                if ($post['is_featured'] || !empty($featured_text)) {
                                    // Si el texto está vacío, utiliza "Featured" por defecto
                                    $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                                    echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                                }
                                if ($post['is_new']) {
                                    echo "<span class='item-new'>{$post['message_new']}</span>";
                                }
                            }
                            echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                            echo '</div>';
                        }
                        echo '<div class="post-details">';
                        asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);   
                        echo '<h3>' . esc_html($post['title']) . '</h3>';
                        if ($show_extract && ($design_type == 2 || $design_type == 3 || $design_type == 4 || $design_type == 8 || $design_type == 9 || $design_type == 13)) {
                            echo '<div class="show-extract">';
                            echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</a>';
                        echo '</article>';
                    }
                    $counter++;
                }
                echo '</div>'; // Cierre de regular-posts
                echo '</div>'; // Cierre de category-posts
            }
        }
        echo '</div>'; // Cierre de home-categories-container
    }
}


function asap_render_single_tag($tag_id, $show_category, $show_extract, $show_date, $show_stars, $show_featured_first, $enable_cache, $cache_period, $content_type, $new_post_info, &$post_ids_shown, $excluded_posts) {
    if (empty($tag_id) || !is_numeric($tag_id)) {
        return;
    }
    $design_type = get_theme_mod('asap_home_design_type', 1);
    $columns = intval(get_theme_mod('asap_columns', 3));
    $posts_map = [
        1 => 6,
        2 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        3 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ],
        4 => [
            1 => 1,
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
        ],   
        5 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],          
        6 => 6,            
        8 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ], 
        9 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],
        10 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],    
        11 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],        
        12 => 5, 
        13 => [
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
        ],              
    ];

    $number_of_posts = $posts_map[$design_type][$columns] ?? $posts_map[$design_type] ?? 5;


    $tag_name = get_tag($tag_id)->name;
    $tag_link = get_tag_link($tag_id);

    $cached_posts = false;
    if ($enable_cache) {
        $transient_key = 'asap_cache_single_tag_posts_' . $tag_id . '_' . md5(json_encode([
            'show_featured_first' => $show_featured_first,
            'show_category' => $show_category,
            'show_extract' => $show_extract,
            'show_date' => $show_date,
            'show_stars' => $show_stars,
            'post_ids_shown' => $post_ids_shown
        ]));
        $cached_posts = get_transient($transient_key);
    }

    if ($cached_posts === false) {
        $args = [
            'post_type' => $content_type,
            'tag_id' => $tag_id,
            'posts_per_page' => $number_of_posts,
            'post__not_in' => array_merge($post_ids_shown, $excluded_posts),
            'orderby' => $show_featured_first ? ['meta_value_num' => 'DESC', 'date' => 'DESC'] : 'date',
            'order' => 'DESC',
            'meta_query' => $show_featured_first ? [
                'relation' => 'OR',
                ['key' => 'featured_post', 'compare' => 'NOT EXISTS'],
                ['key' => 'featured_post', 'compare' => 'EXISTS']
            ] : null
        ];

        // Usar get_posts en lugar de WP_Query para optimizar
        $posts = get_posts($args);
        $cached_posts = array_map(function($post) use ($new_post_info, $show_category) {
            $tag_name = $show_category ? (get_the_tags($post->ID)[0]->name ?? '') : '';
            return [
                'permalink' => get_permalink($post->ID),
                'title' => $post->post_title,
                'thumbnail' => has_post_thumbnail($post->ID) ? $post->ID : '',
                'post_id' => $post->ID,
                'is_featured' => get_post_meta($post->ID, 'featured_post', true),
                'is_new' => $new_post_info ? asap_is_new($new_post_info['days_new'], $new_post_info['current_time']) : false,
                'message_new' => $new_post_info['message_new'] ?? '',
                'category' => $tag_name,
                'date' => $post->post_date
            ];
        }, $posts);

        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));

        if ($enable_cache) {
            set_transient($transient_key, $cached_posts, $cache_period * HOUR_IN_SECONDS);
        }
    } else {
        $post_ids_shown = array_merge($post_ids_shown, array_column($cached_posts, 'post_id'));
    }

    if (!empty($cached_posts)) {
        echo '<div class="home-tags-h2"><h2><span><a href="' . esc_url($tag_link) . '">' . esc_html($tag_name) . '</a></span></h2></div>';

        if (get_theme_mod('asap_home_show_tag_desc', false)) {
            $description = tag_description($tag_id);
            if ($description) {
                echo '<div class="home-des-category">' . $description . '</div>';
            }
        }

        echo '<div class="tag-posts">';

        $counter = 0;
        foreach ($cached_posts as $post) {
            if ($counter == 0) {
                if ($design_type != 2 && $design_type != 3) {
                    // Generar el post destacado solo si el design type no es 2 o 3
                    echo '<article class="featured-post">';
                    echo '<a href="' . esc_url($post['permalink']) . '">';
                    if ($post['thumbnail']) {
                        echo '<div class="featured-post-img">';
                        asap_show_stars_news($show_stars, $post['post_id']);
                        $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);

                        // Verifica si es destacado o si el texto no está vacío
                        if ($post['is_featured'] || !empty($featured_text)) {
                            // Si el texto está vacío, utiliza "Featured" por defecto
                            $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                            echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                        }
                        if ($post['is_new']) {
                            echo "<span class='item-new'>{$post['message_new']}</span>";
                        }
                        $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                        echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                        echo '</div>';
                    }
                    echo '<div class="featured-post-details">';
                    asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                    echo '<h3>' . esc_html($post['title']) . '</h3>';
                    if ($show_extract) {
                        echo '<div class="show-extract">';
                        echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</a>';
                    echo '</article>';
                }
                // Abrir la sección regular-posts para el primer post si es design type 3
                echo '<div class="regular-posts">';
            }

            // Generar el post regular
            if ($design_type == 2 || $design_type == 3 || $design_type == 4 || $counter > 0) {
                echo '<article class="regular-post">';
                echo '<a href="' . esc_url($post['permalink']) . '">';
                if ($post['thumbnail']) {
                    $thumbnail_size = asap_thumbnail_size('categories', $counter, $design_type);
                    echo '<div class="regular-post-img">';
                    if ($design_type == 4 || $design_type == 5 || $design_type == 7 || $design_type == 8 || $design_type == 11) {
                        asap_show_stars_news($show_stars, $post['post_id']);
                        $featured_text = get_post_meta($post['post_id'], 'single_bc_featured', true);
                        // Verifica si es destacado o si el texto no está vacío
                        if ($post['is_featured'] || !empty($featured_text)) {
                            // Si el texto está vacío, utiliza "Featured" por defecto
                            $display_text = !empty($featured_text) ? $featured_text : __("Featured", "asap");
                            echo '<span class="item-featured">' . esc_html($display_text) . '</span>';
                        }
                        if ($post['is_new']) {
                            echo "<span class='item-new'>{$post['message_new']}</span>";
                        }
                    }
                    echo get_the_post_thumbnail($post['post_id'], $thumbnail_size);
                    echo '</div>';
                }
                echo '<div class="post-details">';
                asap_display_category_and_date($show_category, $show_date, $post['category'], $post['date']);
                if ($show_extract && ($design_type == 2 || $design_type == 3 || $design_type == 4 || $design_type == 8 || $design_type == 9 || $design_type == 13)) {
                    echo '<div class="show-extract">';
                    echo '<p>' . esc_html(get_the_excerpt($post['post_id'])) . '</p>';
                    echo '</div>';
                }
                echo '<h3>' . esc_html($post['title']) . '</h3>';
                echo '</div>';
                echo '</a>';
                echo '</article>';
            }
            $counter++;
        }
        echo '</div>'; // Cierre de regular-posts
        echo '</div>'; // Cierre de tag-posts
    }
}


function asap_display_category_and_date($show_category, $show_date, $category = '', $date = '') {
    if ($show_category || $show_date) {
        echo '<span class="home-tag">';
        if ($show_category && !empty($category)) {
            echo esc_html($category);
        }
        if ($show_date && !empty($date)) {
            if ($show_category && !empty($category)) {
                echo ' · ';
            }
            echo asap_human_time_diff($date);
        }
        echo '</span>';
    }
}

add_action( 'after_setup_theme', function () {

    if ( ! get_option( 'asap_enable_asap_thumbnails', '1' ) ) {
        return;
    }

    add_image_size( 'custom-thumb-last',
        absint( get_theme_mod( 'asap_thumb_home_last_width', 400 ) ),
        absint( get_theme_mod( 'asap_thumb_home_last_height', 226 ) ),
        true
    );

    add_image_size( 'custom-thumb-featured',
        absint( get_theme_mod( 'asap_thumb_home_featured_width', 600 ) ),
        absint( get_theme_mod( 'asap_thumb_home_featured_height', 339 ) ),
        true
    );
} );


function asap_featured_posts_shortcode($atts, $content = null) {
    global $post_ids_shown;
    if (!isset($post_ids_shown)) {
        $post_ids_shown = [];
    }

    $new_post_info = asap_get_new_post_info();
    $mods = asap_get_theme_mods();
    $excluded_posts = asap_get_excluded_post_ids();

    // Verificar que haya un post ID y excluirlo
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $excluded_posts[] = $current_post_id;
    }

    ob_start();
    asap_render_featured_posts(
        $post_ids_shown,
        $new_post_info,
        $mods['show_category'],
        $mods['show_date_loop'],
        $mods['show_stars'],
        $mods['enable_cache'],
        $mods['cache_period'],
        $mods,
        $excluded_posts
    );
    return ob_get_clean();
}
add_shortcode('asap_featured_posts', 'asap_featured_posts_shortcode');

function asap_latest_posts_shortcode($atts, $content = null) {
    global $post_ids_shown;
    if (!isset($post_ids_shown)) {
        $post_ids_shown = [];
    }

    $mods = asap_get_theme_mods();
    $new_post_info = asap_get_new_post_info();
    $excluded_posts = asap_get_excluded_post_ids();

    // Verificar que haya un post ID y excluirlo
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $excluded_posts[] = $current_post_id;
    }

    ob_start();
    echo '<div class="home-categories">';
    asap_render_latest_posts(
        $mods['home_last_post_count'],
        $mods['columns'],
        $mods['show_category'],
        $mods['show_date_loop'],
        $mods['show_extract'],
        $mods['show_stars'],
        $mods['show_featured_first'],
        $mods['enable_cache'],
        $mods['cache_period'],
        $mods['content_type'],
        $new_post_info,
        $post_ids_shown,
        $excluded_posts
    );
    echo '</div>';    
    return ob_get_clean();
}
add_shortcode('asap_latest_posts', 'asap_latest_posts_shortcode');

function asap_home_categories_shortcode($atts, $content = null) {
    global $post_ids_shown;
    if (!isset($post_ids_shown)) {
        $post_ids_shown = [];
    }

    $mods = asap_get_theme_mods();
    $new_post_info = asap_get_new_post_info();
    $excluded_posts = asap_get_excluded_post_ids();

    // Verificar que haya un post ID y excluirlo
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $excluded_posts[] = $current_post_id;
    }

    ob_start();
    echo '<div class="home-categories">';    
    asap_render_home_categories(
        $mods['home_categories'],
        $mods['sort_order'],
        $mods['show_category'],
        $mods['show_extract'],
        $mods['show_date_loop'],
        $mods['show_stars'],
        $mods['show_featured_first'],
        $mods['enable_cache'],
        $mods['cache_period'],
        $mods['content_type'],
        $new_post_info,
        $post_ids_shown,
        $excluded_posts
    );
    echo '</div>';    
    return ob_get_clean();
}
add_shortcode('asap_home_categories', 'asap_home_categories_shortcode');


function asap_single_category_shortcode($atts, $content = null) {
    global $post_ids_shown;
    if (!isset($post_ids_shown)) {
        $post_ids_shown = [];
    }

    $mods = asap_get_theme_mods();
    $new_post_info = asap_get_new_post_info();
    $excluded_posts = asap_get_excluded_post_ids();

    // Verificar que haya un post ID y excluirlo
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $excluded_posts[] = $current_post_id;
    }

    // Obtener el ID de la categoría desde los atributos del shortcode
    $atts = shortcode_atts(['id' => ''], $atts);
    $category_id = intval($atts['id']);

    ob_start();
    echo '<div class="home-categories">';    
    asap_render_single_category(
        $category_id,
        $mods['show_category'],
        $mods['show_extract'],
        $mods['show_date_loop'],
        $mods['show_stars'],
        $mods['show_featured_first'],
        $mods['enable_cache'],
        $mods['cache_period'],
        $mods['content_type'],
        $new_post_info,
        $post_ids_shown,
        $excluded_posts
    );
    echo '</div>';    
    return ob_get_clean();
}
add_shortcode('asap_home_cat', 'asap_single_category_shortcode');



function asap_home_tags_shortcode($atts, $content = null) {
    global $post_ids_shown;
    if (!isset($post_ids_shown)) {
        $post_ids_shown = [];
    }

    $mods = asap_get_theme_mods();
    $new_post_info = asap_get_new_post_info();
    $excluded_posts = asap_get_excluded_post_ids();

    // Verificar que haya un post ID y excluirlo
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $excluded_posts[] = $current_post_id;
    }

    ob_start();
    echo '<div class="home-categories">';    
    asap_render_home_tags(
        $mods['home_tags'],
        $mods['tag_sort_order'],
        $mods['show_category'],
        $mods['show_extract'],
        $mods['show_date_loop'],
        $mods['show_stars'],
        $mods['show_featured_first'],
        $mods['enable_cache'],
        $mods['cache_period'],
        $mods['content_type'],
        $new_post_info,
        $post_ids_shown,
        $excluded_posts
    );
    echo '</div>';    
    return ob_get_clean();
}
add_shortcode('asap_home_tags', 'asap_home_categories_shortcode');

function asap_single_tag_shortcode($atts, $content = null) {
    global $post_ids_shown;
    if (!isset($post_ids_shown)) {
        $post_ids_shown = [];
    }

    $mods = asap_get_theme_mods();
    $new_post_info = asap_get_new_post_info();
    $excluded_posts = asap_get_excluded_post_ids();

    // Verificar que haya un post ID y excluirlo
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $excluded_posts[] = $current_post_id;
    }

    // Obtener el ID de la etiqueta desde los atributos del shortcode
    $atts = shortcode_atts(['id' => ''], $atts);
    $tag_id = intval($atts['id']);

    ob_start();
    asap_render_single_tag(
        $tag_id,
        $mods['show_category'],
        $mods['show_extract'],
        $mods['show_date_loop'],
        $mods['show_stars'],
        $mods['show_featured_first'],
        $mods['enable_cache'],
        $mods['cache_period'],
        $mods['content_type'],
        $new_post_info,
        $post_ids_shown,
        $excluded_posts
    );
    return ob_get_clean();
}
add_shortcode('asap_home_tag', 'asap_single_tag_shortcode');


function asap_thumbnail_size($context, $counter, $design_type) {
    switch($context) {
        case 'featured':
            switch($design_type) {
                case 1:
                case 11:  
                    return 'medium'; // chequear esto
                case 2:
                case 4:
                case 5:  
                case 6: 
                case 7:                 
                case 8:                                             
                case 9:                                                                            
                case 10:      
                case 12:
                case 13:                                                            
                    return $counter == 0 ? 'custom-thumb-featured' : 'custom-thumb-last';
                case 3:
                    return 'medium';
            }
            break;
        case 'categories':
            switch($design_type) {
                case 1:
                case 2:
                case 4:               
                case 5:   
                case 6:   
                case 7:                               
                case 8:
                case 9:                                           
                case 10:    
                case 11:
                case 12:
                case 13:                
                    return $counter == 0 ? 'custom-thumb-featured' : 'custom-thumb-last';
                case 3:
                    return 'custom-thumb-last';
            }
            break;
        case 'latest':
            return 'custom-thumb-last';
        default:
            return 'medium'; 
    }
}


?>
