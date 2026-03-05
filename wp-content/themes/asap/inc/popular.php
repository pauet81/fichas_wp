<?php

function asap_register_popular_posts_widget() {
    register_widget( 'asap_Popular_Posts_Widget' );
}
add_action( 'widgets_init', 'asap_register_popular_posts_widget' );

class asap_Popular_Posts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'asap_popular_posts_widget',
            __('ASAP − Más populares', 'text_domain'),
            array( 'description' => __('Muestra las entradas con más vistas', 'text_domain'), )
        );
        $this->defaults = array(
            'title'                 => 'Más popular',
            'selected_period'       => 30,
            'design'                => 'asap-style1',
            'post_type'             => 'post',
            'number_of_posts'       => 5,
            'show_option'           => 'all'
        );

    }

    public function form($instance) {
        $instance = wp_parse_args((array) $instance, $this->defaults);
        
        $title = $instance['title'];
        $selected_period = $instance['selected_period'];
        $design = $instance['design'];
        $selected_post_type = $instance['post_type'];
        $number_of_posts = $instance['number_of_posts'];
        $show_option = isset($instance['show_option']) ? $instance['show_option'] : 'all'; 

        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Título:' ), 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php _e( esc_attr( 'Número de contenidos a mostrar:' ), 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['number_of_posts'] ); ?>" />
        </p>
         <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'selected_period' ) ); ?>"><?php _e( esc_attr( 'Contenidos más visitados en:' ), 'text_domain' ); ?></label>
        <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'selected_period' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'selected_period' ) ); ?>">
            <option value="1" <?php selected( $selected_period, '1' ); ?>>Último día</option>
            <option value="7" <?php selected( $selected_period, '7' ); ?>>Última semana</option>
            <option value="30" <?php selected( $selected_period, '30' ); ?>>Último mes</option>
            <option value="182" <?php selected( $selected_period, '182' ); ?>>Últimos seis meses</option>
            <option value="365" <?php selected( $selected_period, '365' ); ?>>Último año</option>
        </select>
        </p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php _e( esc_attr( 'Tipo de contenidos a mostrar:' ), 'text_domain' ); ?></label>
        <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>">
            <option value="post" <?php selected( $selected_post_type, 'post' ); ?>>Entradas</option>
            <option value="page" <?php selected( $selected_post_type, 'page' ); ?>>Páginas</option>
        </select>
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'design' ) ); ?>"><?php _e( esc_attr( 'Diseño:' ), 'text_domain' ); ?></label>
        <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'design' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'design' ) ); ?>">
            <option value="asap-style1" <?php selected( $design, 'asap-style1' ); ?>>Estilo 1</option>
            <option value="asap-style2" <?php selected( $design, 'asap-style2' ); ?>>Estilo 2</option>
        </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_option' ) ); ?>"><?php _e( 'Mostrar:', 'text_domain' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_option' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_option' ) ); ?>">
                <option value="all" <?php selected( $instance['show_option'], 'all' ); ?>>Mostrar todos</option>
                <option value="same_category" <?php selected( $instance['show_option'], 'same_category' ); ?>>Mostrar solo misma categoría</option>
            </select>
        </p>        
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('clear_cache'); ?>" name="<?php echo $this->get_field_name('clear_cache'); ?>" type="checkbox" />
            <label for="<?php echo $this->get_field_id('clear_cache'); ?>"><?php _e('Borrar caché', 'text_domain'); ?></label>
        </p>
    <?php
    }


    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number_of_posts'] = (!empty($new_instance['number_of_posts'])) ? absint($new_instance['number_of_posts']) : 5; 
        $instance['selected_period'] = (!empty($new_instance['selected_period'])) ? $new_instance['selected_period'] : '30'; 
        $instance['post_type'] = (!empty($new_instance['post_type'])) ? $new_instance['post_type'] : 'post';
        $instance['design'] = (!empty($new_instance['design'])) ? $new_instance['design'] : 'asap-style1';
        $instance['show_option'] = (!empty($new_instance['show_option'])) ? $new_instance['show_option'] : 'all';

        $post_type_changed = isset($old_instance['post_type'], $new_instance['post_type']) && $old_instance['post_type'] !== $new_instance['post_type'];
        $period_changed = isset($old_instance['selected_period'], $new_instance['selected_period']) && $old_instance['selected_period'] !== $new_instance['selected_period'];
        
        $clear_cache = ! empty( $new_instance['clear_cache'] );
        
        if ($clear_cache || $post_type_changed || $period_changed) {
            $cache_key = 'asap_popular_posts_cache';
            delete_transient($cache_key);
        }

        return $instance;
    }


    public function widget($args, $instance) {

        global $post;
        
        // Incrementar vistas
        $is_newspaper_active = get_theme_mod('asap_enable_newspaper_design', false);
        $is_most_viewed_active = (get_theme_mod('asap_home_top_articles', 'latest') == 'most_viewed');
        if (!$is_newspaper_active || !$is_most_viewed_active) {
            asap_increment_post_views_count();
        }

        $current_post_id = is_single() ? $post->ID : null;

        $show_option = isset($instance['show_option']) ? $instance['show_option'] : 'all';

        $selected_design = isset($instance['design']) ? $instance['design'] : 'asap-style1';
        
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __('Más popular', 'text_domain');

        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        echo $args['before_widget'] . '<div class="' . esc_attr($selected_design) . ' asap-popular">';

        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cache_key = 'asap_popular_posts_cache';
        
        $popular_posts = get_transient($cache_key);

        // Si no hay datos en caché, obtenemos los datos frescos
        if (false === $popular_posts) {
          
            // Obtiene el número de días desde el widget y calcula la fecha
            $days = isset($instance['selected_period']) ? intval($instance['selected_period']) : 30; // valor por defecto a 30 días

            $date = new DateTime();
            $date->modify('-' . $days . ' days');
            $since_date = $date->format('Y-m-d');
            
            $selected_post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';

            $query_args = array(
                'post_type'         => $selected_post_type,  // usa la selección del usuario aquí
                'posts_per_page'    => -1,
                'meta_key'          => 'asap_post_views_count',
                'orderby'           => 'meta_value_num',
                'order'             => 'DESC',
                'post__not_in'      => [$current_post_id]  // Excluir la entrada actual
            );

            // Si la opción es mostrar solo las entradas de la misma categoría
            if ($show_option === 'same_category' && is_single()) {
                $categories = get_the_category();
                if (!empty($categories)) {
                    $category_ids = array_map(function($category) {
                        return $category->term_id;
                    }, $categories);
                    $query_args['category__in'] = $category_ids;
                }
            }
            
            $all_posts = new WP_Query($query_args);
            $popular_posts = array();

            // Recorre todas las publicaciones y suma las vistas que están dentro del rango de fechas
            if ($all_posts->have_posts()) {
                while ($all_posts->have_posts()) : $all_posts->the_post();
                    $count_data = get_post_meta(get_the_ID(), 'asap_post_views_count', true);

                    if (is_array($count_data) && !empty($count_data)) {
                        $total_views = 0;
                        foreach ($count_data as $date => $count) {
                            if ($date >= $since_date) {
                                $total_views += $count;
                            }
                        }
                        if ($total_views > 0) {
                            $popular_posts[get_the_ID()] = $total_views;
                        }
                    }
                endwhile;
                wp_reset_postdata();

                // Guarda los datos en el caché para reducir las consultas a la base de datos
                // El caché se mantendrá por un día
                set_transient($cache_key, $popular_posts, DAY_IN_SECONDS);
            }
        }

        // Ordena las publicaciones por vistas
        arsort($popular_posts);

        $limit = ! empty( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : 5;

        if (!empty($popular_posts)) {
            echo '<ol>';
            foreach ($popular_posts as $post_id => $views) {
                echo '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
                if (--$limit <= 0) break;
            }
            echo '</ol>';
        } 

        echo '</div>' . $args['after_widget'];
    }
}