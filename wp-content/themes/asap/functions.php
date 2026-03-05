<?php
/*
 * AsapTheme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package AsapTheme
 */

define('ASAP_THEME_DIR', get_template_directory() );
define('ASAP_THEME_URL', get_template_directory_uri() );
define('ASAP_VERSION', '1.0.0');


function asap_createPageMenu() {
    add_menu_page(
        'Opciones Asap Theme', 
        'Asap Theme',         
        'manage_options',      
        'asap-menu',           
        'asap_renderMenuPage', 
        asap_icon_svg(), 
        99                     
    );

    add_submenu_page(
        'asap-menu',          
        'Opciones',            
        'Opciones',           
        'manage_options',     
        'asap-menu',          
        ''                  
    );
}


add_action('admin_menu', 'asap_createPageMenu');


/*
 * Asap CSS
 */
add_action('wp_enqueue_scripts', 'asap_css');
function asap_css() {
	wp_enqueue_style( 'asap-style', 
		get_stylesheet_directory_uri() . '/assets/css/main.min.css', 
		array(), 
		'04280925'
	);
	$design_type = get_theme_mod('asap_home_design_type', 1);
	if (get_theme_mod('asap_enable_newspaper_design', false) &&
		($design_type == 1 || $design_type == 2 || $design_type == 3) && 
    	(is_home() || is_category() || is_tag() || is_author() || 
    	(is_single() || is_page()) && get_post_meta(get_the_ID(), 'asap_add_news_css', true) === "1")) {
		wp_enqueue_style(
		    "asap-home-type-{$design_type}",
		    get_stylesheet_directory_uri() . '/assets/css/home/design-type'.$design_type.'.css',
		    array(),
		    '01020924'
		);		
	}

	/*
	 * Asap Dynamic CSS
	 */
	require get_template_directory() . '/inc/css.php';
}




/* 
 * Assign body class
 */
function asap_add_body_class($classes) {
    if (get_theme_mod('asap_enable_newspaper_design', false)) {
        $design_type = get_theme_mod('asap_home_design_type', 1);
        $classes[] = 'design-' . esc_attr($design_type);
    }
    return $classes;
}
add_filter('body_class', 'asap_add_body_class');



/*
 * Asap Admin CSS
 */

add_action('admin_enqueue_scripts', 'asap_admin_scripts');
function asap_admin_scripts() {
    wp_enqueue_style('asap-admin-css', 
        ASAP_THEME_URL . '/assets/css/mainAdmin.css', 
        array(), 
        '90128082024'
    );

    wp_enqueue_script('asap-admin-scripts',  
        ASAP_THEME_URL . '/assets/js/mainAdmin.js', 
        array('jquery'), 
        '04360422', 
        true
    ); 
}

add_action('enqueue_block_editor_assets', function(){
    wp_enqueue_style('font-awesome-5', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css');
});

add_action( 'wp_head', 'asap_theme_color' );
function asap_theme_color() { 
	if (get_theme_mod('asap_header_top') )
	{
		$theme_color  = get_theme_mod('asap_top_header_background') ? : '#2471a3';				
	}
	else
	{
		$theme_color  = get_theme_mod('asap_header_background') ? : '#2471a3';		
	}
	echo '<meta name="theme-color" content="' . $theme_color . '">';
}



/*
 * Load translations
 */
add_action('after_setup_theme', 'asap_setup');
function asap_setup(){
    load_theme_textdomain('asap', get_template_directory() . '/languages');
    if ( function_exists( 'determine_locale' ) ) {
        $locale = determine_locale();
        $mofile = get_template_directory() . '/languages/' . $locale . '.mo';
      	load_textdomain( 'asap', $mofile );
    } else {
        load_theme_textdomain( 'asap', get_template_directory() . '/languages' );
    }
}

/*
 * Optimize scripts
 */

add_action('wp_head', 'asap_preconnect', 1);

function asap_preconnect(){ 
$options_fonts = get_theme_mod('asap_options_fonts', 2);
$optimize_analytics = get_option('asap_optimize_analytics'); 
$optimize_adsense = get_option('asap_optimize_adsense'); 
?>
<?php if ( $optimize_analytics ) : ?>
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<?php endif; ?>
<?php if ( $options_fonts == 1 ) : ?>
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<?php endif; ?>
<?php if ( $optimize_analytics ) : ?>
<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
<?php endif; ?>
<?php if ($optimize_adsense) : ?>
<link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
<link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
<link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>
<link rel="preconnect" href="https://stats.g.doubleclick.net" crossorigin>
<link rel="preconnect" href="https://cm.g.doubleclick.net" crossorigin>
<link rel="preload" href="https://securepubads.g.doubleclick.net/tag/js/gpt.js" as="script">
<?php endif; ?>
<?php 
}


/*
 * Preload attachment image
 */

function asap_preload_post_thumbnail() {
	 
	if ( get_option('asap_preload_featured_image', true) ) {
   
		global $post;

		if ( ! is_singular() ) {
			return;
		}

		$image_size = 'full';

		if ( is_singular( 'product' ) ) {
			$image_size = 'woocommerce_single';
		} else if ( is_singular( 'post' ) ) {
			$image_size = 'large';
		}

		$image_size = apply_filters( 'preload_post_thumbnail_image_size', $image_size, $post );
		$thumbnail_id = apply_filters( 'preload_post_thumbnail_id', get_post_thumbnail_id( $post->ID ), $post );
		$image = wp_get_attachment_image_src( $thumbnail_id, $image_size );
		$src = '';
		$additional_attr_array = array();
		$additional_attr = '';

		if ( $image ) {

			list( $src, $width, $height ) = $image;
			$image_meta = wp_get_attachment_metadata( $thumbnail_id );

			if ( is_array( $image_meta ) ) {
				$size_array = array( absint( $width ), absint( $height ) );
				$srcset     = wp_calculate_image_srcset( $size_array, $src, $image_meta, $thumbnail_id );
				$sizes      = wp_calculate_image_sizes( $size_array, $src, $image_meta, $thumbnail_id );

				if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
					$additional_attr_array['imagesrcset'] = $srcset;

					if ( empty( $attr['sizes'] ) ) {
						$additional_attr_array['imagesizes'] = $sizes;
					}
				}
			}

			foreach ( $additional_attr_array as $name => $value ) {
				$additional_attr .= "$name=" . '"' . $value . '" ';
			}

		} else {
			return;
		}

		printf( '<link rel="preload" as="image" href="%s" %s/>', esc_url( $src ), $additional_attr );
		
	}
}

add_action( 'wp_head', 'asap_preload_post_thumbnail' );



/*
 * Asap Setup
 */
function asap_ini() {

	add_theme_support( 'html5', array(
		'search-form',
		'gallery',
		'caption',
	));
	
	add_theme_support( 'post-thumbnails' );
	
	add_theme_support( 'side-thumbnails' );
	
	add_theme_support( 'title-tag' );
	
	add_theme_support( 'automatic-feed-links' );
	
	add_theme_support( 'custom-logo', array(
		'height'      => 50,
		'width'       => 250,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	));
	
	add_theme_support( 'customize-selective-refresh-widgets' );


}

add_action('after_setup_theme', 'asap_ini');



/*
 * Asap Security
 */

function asap_security_headers() {
	if ( get_option('asap_content_type_options') ) {
		header('X-Content-Type-Options: nosniff');
	}

	if ( get_option('asap_frame_options') ) {
		header('X-Frame-Options: SAMEORIGIN');
	}

	if ( get_option('asap_xxs_protection') ) {
		header('X-XSS-Protection: 1; mode=block');
	}

	if ( get_option('asap_strict_transport_security') ) {
		header('Strict-Transport-Security: max-age=31536000;');
	}

	if ( get_option('asap_referrer_policy') ) {
		header('Referrer-Policy: strict-origin-when-cross-origin');
	}
}
add_action('send_headers', 'asap_security_headers');

add_filter('the_generator', 'asap_remove_version');	

function asap_remove_version() {

	if ( get_option('asap_delete_version', true) ) : 
	
		return ''; 
	
	endif;	
	
}

if ( get_option('asap_delete_wlw', true) ) :

	remove_action('wp_head', 'wlwmanifest_link');

endif;

if ( get_option('asap_delete_rds', true) ) :

	remove_action('wp_head', 'rsd_link');

	add_filter('xmlrpc_enabled', '__return_false');

endif;


if ( get_option('asap_delete_api_rest_link', true) ) :

	remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');

	remove_action('wp_head', 'rest_output_link_wp_head');

	remove_action('template_redirect', 'rest_output_link_header', 11, 0);

endif;



/*
 * Header and footer code
 */

add_action('wp_head','asap_head_code', 20);

function asap_head_code() {
	
$head_code =  base64_decode( ( get_theme_mod('asap_code_analytics') ) );

if ( $head_code ) : 
	
	echo $head_code; 
	
endif;
	
}

add_action('wp_body_open','asap_body_code');

function asap_body_code() {
	
$body_code =  base64_decode( ( get_theme_mod('asap_body_code') ) );

if ( $body_code ) : 
	
	echo $body_code; 
	
endif;
	
}

add_action('wp_footer','asap_footer_code', 20);

function asap_footer_code() {
	
$footer_code =  base64_decode( ( get_theme_mod('asap_footer_code') ) );

if ( $footer_code ) : 
	
	echo $footer_code; 
	
endif;
	
}

/*
 * Update software
 */
add_action ( 'after_setup_theme' , 'asap_theme_updater' ); 
function asap_theme_updater () { 
	require ( get_template_directory () . '/updater/theme-updater.php' ); 
} 


/* Menu */

register_nav_menus( array(
	'header-menu' => __('Main menu', 'asap'),
));



/*
 * Thumbnails
 */

/*
 * Thumbnails
 */
add_action( 'after_setup_theme', 'asap_setup_thumbnails' );
function asap_setup_thumbnails() {

	if ( get_option( 'asap_enable_asap_thumbnails', '1' ) ) {

		add_image_size(
			'post-thumbnail',
			absint( get_theme_mod( 'asap_thumb_width', 400 ) ),
			absint( get_theme_mod( 'asap_thumb_height', 267 ) ),
			true
		);

		add_image_size(
			'side-thumbnail',
			absint( get_theme_mod( 'asap_side_thumb_width', 300 ) ),
			absint( get_theme_mod( 'asap_side_thumb_height', 140 ) ),
			true
		);
	}
}

add_filter( 'image_size_names_choose', function ( $sizes ) {
	return array_merge(
		$sizes,
		[
			'post-thumbnail' => __( 'ASAP thumbnail', 'asap' ),
			'side-thumbnail' => __( 'ASAP side', 'asap' ),
		]
	);
} );

function asap_get_thumbnail_url( $size = 'post-thumbnail', $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	if ( ! has_post_thumbnail( $post_id ) ) {
		return false;
	}
	$thumb_id = get_post_thumbnail_id( $post_id );
	$image    = wp_get_attachment_image_src( $thumb_id, $size )
		?: wp_get_attachment_image_src( $thumb_id, 'full' );
	return $image && is_array( $image ) ? $image[0] : false;
}
function asap_post_thumbnail( $size = 'post-thumbnail', $post_id = null ) {
	return asap_get_thumbnail_url( $size, $post_id );
}
function asap_side_thumbnail( $post_id = null ) {
	return asap_get_thumbnail_url( 'side-thumbnail', $post_id );
}



/* 
 * Include functions
 */

// Cargar autoload de Composer (Readability y otras librerías)
if (file_exists(get_template_directory() . '/vendor/autoload.php')) {
    require get_template_directory() . '/vendor/autoload.php';
} else {
    // Mostrar aviso en admin si falta vendor/
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p><strong>ASAP Theme:</strong> La carpeta vendor/ no existe. Ejecuta <code>composer install</code> o sube la carpeta vendor/ completa al servidor.</p></div>';
    });
}

// Cargar autoloader de clases de IA (CRÍTICO: antes de new.php)
if (file_exists(get_template_directory() . '/inc/ia/autoloader.php')) {
require get_template_directory() . '/inc/ia/autoloader.php';
}

if (file_exists(get_template_directory() . '/inc/ia/new.php')) {
require get_template_directory() . '/inc/ia/new.php';
}

require get_template_directory() . '/inc/fonts.php';
require get_template_directory() . '/inc/fonts-local.php';
require get_template_directory() . '/inc/ads.php';
require get_template_directory() . '/inc/comments.php';
require get_template_directory() . '/inc/customizer.php';
// TODO: Implementar en versión futura
// require get_template_directory() . '/inc/dark-mode.php';
require get_template_directory() . '/inc/megamenu/class-asap-megamenu.php';
require get_template_directory() . '/inc/schema.php';
require get_template_directory() . '/inc/categories.php';
require get_template_directory() . '/inc/toc.php';
require get_template_directory() . '/inc/export-import.php';
require get_template_directory() . '/inc/metabox.php';
require get_template_directory() . '/inc/shortcodes.php';
require get_template_directory() . '/inc/breadcrumbs.php';
require get_template_directory() . '/inc/home.php';

/*
 * Resources Module CSS/JS Functions
 */
if (get_option('asap_resources_module')) {
	require get_template_directory() . '/inc/resources.php';
}

/*
 * Predictive Search Functions
 */
if (get_option('asap_predictive_enabled')) {
	require get_template_directory() . '/inc/predictive.php';
}

/*
 * Rating Functions
 */

function asap_show_stars($show_stars) {
    if ($show_stars) {
        $average_rating = get_post_meta(get_the_ID(), 'average_rating', true);
        if ($average_rating) {
            $rounded_rating = round($average_rating); // Redondea al entero más cercano
            echo '<div class="average-rating-loop">';
            echo '<span class="average-rating">';
            for ($i = 1; $i <= 5; $i++) {
                echo $i <= $rounded_rating ? '<span style="color: #e88330;">&#9733;</span>' : '<span style="color: #adb5bd;">&#9733;</span>';
            }
            echo '</span>';
            echo '</div>';
        }
    }
}

function asap_show_stars_news($show_stars, $post_id) {
    if ($show_stars) {
        $average_rating = get_post_meta($post_id, 'average_rating', true);
        if ($average_rating) {
            $rounded_rating = round($average_rating); // Redondea al entero más cercano
            echo '<div class="average-rating-loop">';
            echo '<span class="average-rating">';
            for ($i = 1; $i <= 5; $i++) {
                echo $i <= $rounded_rating ? '<span style="color: #e88330;">&#9733;</span>' : '<span style="color: #adb5bd;">&#9733;</span>';
            }
            echo '</span>';
            echo '</div>';
        }
    }
}

function asap_comment_rating_display_average_rating() {
    global $post;

    $post_id = $post->ID;
    $post_type = get_post_type($post_id);

    if (get_option("asap_stars_enable_{$post_type}") !== '1') {
        return '';
    }

    $average = get_post_meta($post_id, 'average_rating', true);
    $votes = get_post_meta($post_id, 'rating_votes', true);

    if ($average === false || $average <= 0  || $votes === false || $votes <= 0) {
        return '';
    }

    $rounded_average = round($average); // Redondea al entero más cercano solo para mostrar las estrellas
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rounded_average ? '<span style="color: #e88330;">&#9733;</span>' : '<span style="color: #adb5bd;">&#9733;</span>';
    }

	$custom_content = '<div class="average-rating average-post-page"><span>' 
    . $stars 
    . '</span><span>' 
    . sprintf(
        /* translators: 1: puntuación promedio, 2: número de votos */
        __( 'Rating: %1$s (%2$d votes)', 'asap' ), 
        $average, 
        $votes
    ) 
    . '</span></div>';

    return $custom_content;
}

$post_types = get_post_types(array('public' => true), 'names');
foreach ($post_types as $post_type) {
    $option_name = "asap_stars_enable_{$post_type}";
    $is_rating_enabled = get_option($option_name, '0');
    if ($is_rating_enabled) {
        break;
    }
}
if ($is_rating_enabled) {
	require get_template_directory() . '/inc/rating.php';
}



/**
 * Increment post views
 */
function asap_increment_post_views_count() {
    if (is_single() || is_page()) {
        global $post;

        if ($post) {
            $post_id = $post->ID;
            $count_key = 'asap_post_views_count';

            // Utilizar transients para cachear el meta
            $transient_key = "asap_post_views_{$post_id}";
            $count_data = get_transient($transient_key);

            if ($count_data === false) {
                $count_data = get_post_meta($post_id, $count_key, true);

                // Asegurarse de que $count_data sea un array
                if (!is_array($count_data)) {
                    $count_data = array();
                }
            }

            $current_date = date('Y-m-d');

            // Incrementar la cuenta de vistas
            if (isset($count_data[$current_date])) {
                $count_data[$current_date]++;
            } else {
                $count_data[$current_date] = 1;
            }

            // Almacenar en transient para uso futuro inmediato
            set_transient($transient_key, $count_data, 3600); // Cachear por 1 hora

            // Actualizar el meta de la publicación
            update_post_meta($post_id, $count_key, $count_data);
        }
    }
}


add_action('wp', 'asap_increment_post_views');
function asap_increment_post_views() {
    $is_newspaper_active = get_theme_mod('asap_enable_newspaper_design', false);
    $is_most_viewed_active = (get_theme_mod('asap_home_top_articles', 'latest') == 'most_viewed');
    if ($is_newspaper_active && $is_most_viewed_active) {
   		asap_increment_post_views_count();
    }
}


/*
 * Modify Main Query for Featured Posts
 */
function asap_modify_main_query( $query ) {

	$show_featured_first = get_theme_mod('asap_show_featured_first', true);

    if ( $show_featured_first && $query->is_main_query() && !is_admin() ) {
        if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() ) {
			$query->set( 'meta_query', array(
				'relation' => 'OR',
				array(
					'key' => 'featured_post',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key' => 'featured_post',
					'compare' => 'EXISTS',
				)
			));
			$query->set( 'orderby', array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ) );
		}
    }
}
add_action( 'pre_get_posts', 'asap_modify_main_query' );


/*
 * Editor
 */

if ( ( class_exists( 'Classic_Editor' ) ) || ( get_option('asap_delete_guten_css') ) ) {

require get_template_directory() . '/inc/thickbox.php';
	
} else {

require get_template_directory() . '/inc/gutenberg/clusters.php';
require get_template_directory() . '/inc/gutenberg/shortcodes.php';
require get_template_directory() . '/inc/gutenberg/blocks.php';
	
}

/*
 * Script defer
 */
if ( !is_admin() && !is_customize_preview() ) {
    add_filter( 'script_loader_tag', 'defer_parsing_of_js', 10, 3 );
    function defer_parsing_of_js( $tag, $handle, $src ) {
        if ( get_option('asap_enable_js_defer') ) {
            // Excluir jQuery y scripts críticos de WP
            if ( strpos( $handle, 'jquery' ) === false &&
                 strpos( $handle, 'wp-i18n' ) === false &&
                 strpos( $handle, 'wp-hooks' ) === false ) {
                return str_replace( ' src', ' defer src', $tag );
            }
        }
        return $tag;
    }
}



/* 
 * Widget
 */
add_action('widgets_init', 'add_widget_support');
function add_widget_support()	{
	
register_sidebar(array(
		'name'          => __('Barra lateral de página de inicio 1', 'asap'),
		'id'            => 'home-before',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));
	
	register_sidebar(array(
		'name'          => __('Barra lateral de entradas 1', 'asap'),
		'id'            => 'single-before',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));
	
	register_sidebar(array(
		'name'          => __('Barra lateral de páginas 1', 'asap'),
		'id'            => 'page-before',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));
	
	register_sidebar(array(
		'name'          => __('Barra lateral de categorías 1', 'asap'),
		'id'            => 'cat-before',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));

	register_sidebar(array(
		'name'          => __('Barra lateral de etiquetas 1', 'asap'),
		'id'            => 'tag-before',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));	



	register_sidebar(array(
		'name'          => __('Barra lateral de página de inicio 2', 'asap'),
		'id'            => 'home',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));
	
	register_sidebar(array(
		'name'          => __('Barra lateral de entradas 2', 'asap'),
		'id'            => 'single',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));
	
	register_sidebar(array(
		'name'          => __('Barra lateral de páginas 2', 'asap'),
		'id'            => 'page',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));
	
	register_sidebar(array(
		'name'          => __('Barra lateral de categorías 2', 'asap'),
		'id'            => 'cat',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));

	register_sidebar(array(
		'name'          => __('Barra lateral de etiquetas 2', 'asap'),
		'id'            => 'tag',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	));	




	if ( function_exists( 'is_woocommerce' ) ) {

		register_sidebar(array(
			'name'          => __('Producto', 'asap'),
			'id'            => 'products',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<p class="sidebar-title">',
			'after_title'   => '</p>',
		));

	  	// Shop page sidebar
	    register_sidebar( array(
	        'name'          => __( 'Página de tienda', 'asap' ),
	        'id'            => 'shop',
	        'before_widget' => '<div>',
	        'after_widget'  => '</div>',
	        'before_title'  => '<p class="sidebar-title">',
	        'after_title'   => '</p>',
	    ) );

	    // Product category archive sidebar
	    register_sidebar( array(
	        'name'          => __( 'Categoría de producto','asap' ),
	        'id'            => 'product-category',
	        'before_widget' => '<div>',
	        'after_widget'  => '</div>',
	        'before_title'  => '<p class="sidebar-title">',
	        'after_title'   => '</p>',
	    ) );		
		
	}
		
	register_sidebar( array(
		'name'          => __('Redes sociales − Cabecera', 'asap'),
		'id'            => 'hsocial',
		'description'  	=> __('Location for the links to your social networks.', 'asap'),
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	) );

	register_sidebar( array(
		'name'          => __('Redes sociales − Pie de página', 'asap'),
		'id'            => 'social',
		'description'  	=> __('Location for the links to your social networks.', 'asap'),
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	) );
	
	
	register_sidebar( array(
		'name'          => __('Redes sociales − Menú móvil', 'asap'),
		'id'            => 'msocial',
		'description'  	=> __('Location for the links to your social networks.', 'asap'),
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="sidebar-title">',
		'after_title'   => '</p>',
	) );

}


/*
 * ASAP Widgets
 */

require get_template_directory() . '/inc/widgets.php';
require get_template_directory() . '/inc/popular.php';


/* 
 * Categorys
 */
function current_category() {
	global $cat;
	if (is_category() && $cat) {
		return $cat;
	} else {
		$var = get_the_category();
		if (count($var) > 0) {
			return $var[0]->cat_ID;
		} else {
			return false;
		}
	}
}

function asap_categories() {
	$cats = array();
	foreach (get_the_category() as $c) {
	$cat = get_category($c);
		array_push($cats, $cat->term_id);
	}
	return $cats;
}


/* 
 * Breadcrumb
 */

function asap_breadcrumbs($schema) {
	$args = array(
		'container'   => 'div',
		'show_browse' => false,
		'show_schema' => $schema,
	);
	breadcrumb_trail($args);
}

/* 
 * Breadcrumb Pages
 */

function asap_breadcrumbs_pages($post,$schema)
{

	if ( ! get_theme_mod('asap_hide_breadcrumb_page') && ! get_post_meta( get_the_ID(), 'hide_breadcrumbs', true ) )
	{
		
		$url_pillar_page = get_post_meta(get_the_ID() , 'single_bc_url_pillar_page', true);

		$text_pillar_page = get_post_meta(get_the_ID() , 'single_bc_text_pillar_page', true);

		$post_title = get_post_meta(get_the_ID() , 'single_bc_text', true) ? : get_the_title();

		$label = get_theme_mod('asap_breadcrumb_text') ? : get_bloginfo('name');

		if ($schema)
		{
			$format = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="%s" title="%s" itemprop="item"><span itemprop="name">%s</span></a><meta itemprop="position" content="%s"></li>';
		}
		else
		{
			$format = '<li><a href="%s" title="%s"><span>%s</span></a></li>';
		}

		if ($url_pillar_page && $text_pillar_page)
		{
			$count = 3;
		}
		else
		{
			$anc = array_map('get_post', array_reverse((array)get_post_ancestors($post)));

			$count = count($anc);

			$count = $count + 2;

			$links = array_map('get_permalink', $anc);

		}

		if ($schema)
		{
			printf('<div role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb">');

			printf('<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">');

			printf('<meta name="numberOfItems" content="' . $count . '">');

			printf('<meta name="itemListOrder" content="Ascending">');

			printf('<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="%s" itemprop="item"><span itemprop="name">' . $label . '</span></a><meta itemprop="position" content="1"></li>', esc_url(home_url()));
		}
		else
		{
			printf('<div class="breadcrumb-trail breadcrumbs">');

			printf('<ul class="breadcrumb">');

			printf('<li><a href="%s"><span>' . $label . '</span></a></li>', esc_url(home_url()));
		}

		$meta = 2;

		if ($url_pillar_page && $text_pillar_page)
		{
			printf($format, $url_pillar_page, $text_pillar_page, $text_pillar_page, $meta);

			$meta = $meta + 1;
		}
		else
		{
			foreach ($anc as $i => $apost)
			{
				
				$title = get_post_meta( $apost->ID , 'single_bc_text', true);
			
				if ( ! $title ) {

				$title = apply_filters('the_title', $apost->post_title);
					
				}

				printf($format, $links[$i], esc_attr($title) , esc_html($title) , $meta);

				$meta = $meta + 1;
			}

		}

		if ( ! get_theme_mod('asap_hide_breadcrumb_title') ) {
			if ($schema)
			{
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">' . $post_title . '</span><meta itemprop="position" content="' . $meta . '"></li>';
			}
			else
			{
				echo '<li><span>' . $post_title . '</span></li>';
			}
		}

		printf('</ul>');

		printf('</div>');
		
	}
	
}


/*
 * Awesome 
 */


add_action( 'wp_footer', 'add_awesome' );

function add_awesome() {
	
	if ( get_option('asap_enable_awesome') ) {

		wp_enqueue_style( 'awesome-styles','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css');
		
	}
	
}




/* 
 * Footer widgets
 */

add_action( 'widgets_init', 'register_footer_sidebars' );
function register_footer_sidebars() {
	
  	register_sidebar( array(
        'name' => __( 'Footer 1', 'asap' ),
        'id' => 'widget-footer-1',
        'before_widget' => '<div class="widget-area">',
        'after_widget' => '</div>',
		'before_title' => '<p class="widget-title">',
        'after_title' => '</p>',
    ) );
 
    register_sidebar( array(
        'name' => __( 'Footer 2', 'asap' ),
        'id' => 'widget-footer-2',
        'before_widget' => '<div class="widget-area">',
        'after_widget' => '</div>',
		'before_title' => '<p class="widget-title">',
        'after_title' => '</p>',
    ) );
 
    register_sidebar( array(
        'name' => __( 'Footer 3', 'asap' ),
        'id' => 'widget-footer-3',
        'before_widget' => '<div class="widget-area">',
        'after_widget' => '</div>',
		'before_title' => '<p class="widget-title">',
        'after_title' => '</p>',
    ) );
 
    register_sidebar( array(
        'name' => __( 'Footer 4', 'asap' ),
        'id' => 'widget-footer-4',
        'before_widget' => '<div class="widget-area">',
        'after_widget' => '</div>',
		'before_title' => '<p class="widget-title">',
        'after_title' => '</p>',
    ) ); 

    register_sidebar( array(
        'name' => __( 'Pie de página horizontal', 'asap' ),
        'id' => 'widget-footer-bottom',
        'before_widget' => '<div class="widget-bottom-area">',
        'after_widget' => '</div>',
		'before_title' => '<p class="widget-bottom-title">',
        'after_title' => '</p>',
    ) );  
	
}


/* 
 * Move scripts to footer
 */	 
function asap_move_scripts_from_head_to_footer() {
	if (  get_option('asap_enable_js_defer') && !is_admin()  && !is_customize_preview() ) {
		remove_action( 'wp_head', 'wp_print_scripts' );
		remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_footer', 'wp_print_scripts', 5);
		add_action( 'wp_footer', 'wp_print_head_scripts', 5);
	}
}
add_action('wp_enqueue_scripts', 'asap_move_scripts_from_head_to_footer');


/* 
 * Load JS scripts
 */	 
function asap_load_scripts() {
	

	if ( !function_exists('is_plugin_active') ) {
	    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	
	if ( !is_plugin_active('elementor/elementor.php')	 && !is_admin() && !current_user_can('manage_options') && get_option('asap_deactivate_jquery') ) 
	{
		
		wp_deregister_script('jquery');
		
		wp_register_script( 'asap-scripts', 
						get_template_directory_uri() . '/assets/js/asap.vanilla.min.js',
						false, '01170124', true );		
	}
	else
	{
		wp_register_script( 'asap-scripts', 
						get_template_directory_uri() . '/assets/js/asap.min.js',
						array( 'jquery'), '01170124', true );			
	}

	wp_enqueue_script( 'asap-scripts' );
		
	if (  get_theme_mod('asap_no_sticky_header')  &&  ! get_theme_mod('asap_float_menu') ) :
	
		wp_register_script( 'asap-menu', 
						   get_template_directory_uri() . '/assets/js/menu.min.js', 
						   false, '02270623', true );
		
		wp_enqueue_script( 'asap-menu' );
	
	endif;
	
	if ( get_theme_mod('asap_toc_sticky') && ( is_single() || ( is_page() ) ) ) :
		
		wp_register_script( 'asap-toc', 
						   get_template_directory_uri() . '/assets/js/toc.min.js',
						   false, '02270225', true );	
	
		wp_enqueue_script( 'asap-toc' );

	
	endif;
	
	if ( get_theme_mod('asap_float_design') ) :
	
		wp_register_script( 'asap-menu-responsive', 
						   get_template_directory_uri() . '/assets/js/menu-responsive.min.js',
						   false, '07190523', true );	
		wp_enqueue_script( 'asap-menu-responsive' );
	
	endif;
	
}
add_action( 'wp_enqueue_scripts', 'asap_load_scripts' );







/* 
 * Remove emojis from header
 */	 
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'print_emoji_detection_script', 7);


/*
 * Schema Navigation
 */

add_filter( 'nav_menu_link_attributes', 'asap_add_menu_attributes', 10, 3 );
function asap_add_menu_attributes( $atts, $item, $args ) {
  $atts['itemprop'] = 'url';
	 return $atts;
}

/* 
 * Default settings
 */	

function get_columns() {

	global $post;
	
	$columns 				= get_theme_mod('asap_columns') ? : 3;
	$columns_featured 		= get_theme_mod('asap_columns_featured') ? : 3;
	$rows_featured 			= get_theme_mod('asap_rows_featured') ? : 1;
	$show_category 			= get_theme_mod('asap_show_post_category') ? true : false;
	$show_date_loop 		= get_theme_mod('asap_show_date_loop') ? true : false;
	$show_extract 			= get_theme_mod('asap_show_post_extract') ? true : false;
	$thumb_width 			= get_theme_mod('asap_thumb_width') ? : 400;
	$thumb_height 			= get_theme_mod('asap_thumb_height') ? : 267;
	$text_show_more 		= get_theme_mod('asap_text_button_more');
	$deactivate_background 	= get_theme_mod('asap_deactivate_background');
	$loop_format 			= get_theme_mod('asap_loop_format') ? : 'p';
	$featured_text			= get_theme_mod('asap_featured_text')  ? : __("Featured", "asap");
	$show_advice_new_posts 	= get_theme_mod('asap_show_advice_new_posts');
	$message_new 			= get_theme_mod('asap_advice_new_posts_text', 'Nuevo');
	$days_new 				= intval(get_theme_mod('asap_advice_new_posts_days', 7));
	$current_time			= current_time('timestamp');

	/*
	 * Stars
	 */
    $post_type = get_post_type($post);
    if (get_option("asap_stars_enable_{$post_type}") == '1' && get_option('asap_stars_show_loop', true)) {
        $show_stars				= true;
    } else {
        $show_stars				= false;
    }

	set_query_var('columns', $columns);
	set_query_var('columns_featured', $columns_featured);
	set_query_var('rows_featured', $rows_featured);
	set_query_var('show_category', $show_category);
	set_query_var('show_date_loop', $show_date_loop);
	set_query_var('show_extract', $show_extract);
	set_query_var('text_show_more', $text_show_more);
	set_query_var('deactivate_background', $deactivate_background);
	set_query_var('thumb_width', $thumb_width);	
	set_query_var('thumb_height', $thumb_height);
	set_query_var('loop_format', $loop_format);
	set_query_var('featured_text', $featured_text);
	set_query_var('show_advice_new_posts', $show_advice_new_posts);
	set_query_var('message_new', $message_new);
	set_query_var('days_new', $days_new);	
	set_query_var('current_time', $current_time);	
	set_query_var('show_stars', $show_stars);	
}

function asap_data_images() {

	global $post;
	
	$deactivate_background 	= get_theme_mod('asap_deactivate_background');
	$columns 				= get_theme_mod('asap_columns') ? : 3;
	$thumb_width 			= get_theme_mod('asap_thumb_width') ? : 400;
	$thumb_height 			= get_theme_mod('asap_thumb_height') ? : 267;
	$side_thumb_width 		= get_theme_mod('asap_side_thumb_width') ? : 300;
	$side_thumb_height 		= get_theme_mod('asap_side_thumb_height') ? : 140;
	$columns_rels 			= get_theme_mod('asap_columns_related') ? : ( get_theme_mod('asap_loop_design') ? 2 : 3 );
	$featured_text			= get_theme_mod('asap_featured_text')  ? : __("Featured", "asap");
	$show_advice_new_posts 	= get_theme_mod('asap_show_advice_new_posts');	
	$message_new 			= get_theme_mod('asap_advice_new_posts_text', 'Nuevo');
	$days_new 				= intval(get_theme_mod('asap_advice_new_posts_days', 7));
	$current_time			= current_time('timestamp');

	/*
	 * Stars
	 */
    $post_type = get_post_type($post);
    if (get_option("asap_stars_enable_{$post_type}") == '1' && get_option('asap_stars_show_loop', true)) {
        $show_stars				= true;
    } else {
        $show_stars				= false;
    }

	set_query_var('deactivate_background', $deactivate_background);
	set_query_var('thumb_width', $thumb_width);	
	set_query_var('thumb_height', $thumb_height);	
	set_query_var('side_thumb_width', $side_thumb_width);	
	set_query_var('side_thumb_height', $side_thumb_height);
	set_query_var('columns', $columns);
	set_query_var('columns_rels', $columns_rels);	
	set_query_var('show_advice_new_posts', $show_advice_new_posts);	
	set_query_var('featured_text', $featured_text);	
	set_query_var('message_new', $message_new);
	set_query_var('days_new', $days_new);	
	set_query_var('current_time', $current_time);	
	set_query_var('show_stars', $show_stars);	
	
}

/*
 * Loop extract
 */

add_filter( 'excerpt_length', 'asap_extract', 999 );
function asap_extract( $length ) {
	
	$extract_long = get_theme_mod('asap_extract_long') ?: 12;
	
	if ( is_admin() ) :	return $length; endif;
	
	return $extract_long;
}

add_filter ('excerpt_more', 'asap_extract_text');
function asap_extract_text ( $more ) {
	global $post;
}

/*
 * Remove texturize 
 */

remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_filter('the_title', 'wptexturize');



/*
 * Gutenberg
 */

add_action( 'after_setup_theme', 'asap_theme_supported_features' );
function asap_theme_supported_features() {
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
}

add_filter( 'user_contactmethods', 'asap_user_social' );
function asap_user_social( $user_contact ) {
	$user_contact['author_fb'] 	= 'Facebook'; 
	$user_contact['author_tw'] 	= 'Twitter'; 
    $user_contact['author_ig'] 	= 'Instagram'; 
    $user_contact['author_pin'] = 'Pinterest'; 
    $user_contact['author_yt'] 	= 'YouTube'; 
    $user_contact['author_lk'] 	= 'Linkedin';     
	return $user_contact;
}


/* Remove Gutenberg on Widgets  */
add_action( 'after_setup_theme', 'asap_ce_widgets' );
function asap_ce_widgets() {
	if ( get_option('asap_classic_editor_widgets') )
	{
		remove_theme_support( 'widgets-block-editor' );
	}
}

/* Lazy Load  */
add_filter('the_content', 'asap_lazyload');

function asap_lazyload($content) 
{
	$content = str_replace('<iframe', '<iframe loading="lazy"', $content);
	
	$content = str_replace('<img', '<img loading="lazy"', $content);

	return $content;
}

function lazy_load_comment_avatars($avatar) {
    $avatar = str_replace('<img', '<img loading="lazy"', $avatar);
    return $avatar;
}
add_filter('get_avatar', 'lazy_load_comment_avatars');

/*
 * Performance
 */
add_action('widgets_init', 'asap_delete_css_recentcomments');

function asap_delete_css_recentcomments() {
    global $wp_widget_factory;
    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
        remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
    }
}

if ( get_option('asap_delete_guten_css') ) {
	add_filter('use_block_editor_for_post', '__return_false', 100);
	add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
	add_filter( 'use_widgets_block_editor', '__return_false' );
}


add_action( 'wp_enqueue_scripts', 'asap_remove_gutenberg_styles' );
function asap_remove_gutenberg_styles(){
	if ( get_option('asap_delete_guten_css') ) {
		wp_dequeue_style( 'global-styles' );
	    wp_dequeue_style('wp-block-library'); // Elimina el CSS de Gutenberg para el front-end.
	    wp_dequeue_style('wp-block-library-theme'); // Elimina el CSS del tema de Gutenberg.
	    wp_dequeue_style('wc-block-style'); // Elimina los estilos de bloques de WooCommerce si estás usando WooCommerce.
	}
}


if ( get_option('asap_delete_guten_css') ) {
    add_action( 'wp_enqueue_scripts', 'asap_remover_scripts_gutenberg', 100 );
}

function asap_remover_scripts_gutenberg() {
    wp_dequeue_script( 'wp-hooks' );
    wp_deregister_script( 'wp-hooks' );
    wp_dequeue_script( 'wp-i18n' );
    wp_deregister_script( 'wp-i18n' );
}



add_filter( 'body_class', 'asap_body_class', 10, 2 );

function asap_body_class( $wp_classes, $extra_classes )
{
		
    $blacklist = array('blog' , 
					   'tag', 
					   'post-template-default', 
					   'page-template-default', 
					   'single-post', 
					   'single-format-standard',
					   'wp-custom-logo', 
					   'no-customize-support');
	
    $wp_classes = array_diff( $wp_classes, $blacklist );
	
	return array_merge( $wp_classes, (array) $extra_classes );

}


add_filter( 'body_class', 'asap_body_class_box',  10, 2  );

function asap_body_class_box ( $classes ) 
{
	// Agregar clase si "Ocultar texto del logo" está activo en megamenu
	if ( get_option('asap_megamenu_hide_logo', '0') === '1' ) {
		$classes[] = 'asap-hide-logo-text';
	}
	
	if ( get_theme_mod('asap_design') ) 
	{
		$classes[] = 'asap-box-design';
		
		if ( get_theme_mod('asap_loop_design') ) 
		{
			$classes[] = 'asap-loop-horizontal';		
		}
		
	}

    return $classes;
}

	
add_action( 'init', 'asap_disable_embeds_code_init', 9999 );

function asap_disable_embeds_code_init() {
	
	if ( get_option('asap_disable_embed') ) :
	
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		add_filter( 'embed_oembed_discover', '__return_false' );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
	
	endif; 
	
}
	


/*
 * Extra Text Category
 */

add_filter('category_edit_form_fields', 'asap_cat_description', 99);
add_action('edited_category', 'asap_save_extra_category_fields');

function asap_cat_description($tag)
{
	$cat_extra_description = get_term_meta($tag->term_id, 'cat_extra_description', true);
	?>
	<table class="form-table">
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="description"><?php _e('Bottom Description', 'asap'); ?></label>
			</th>
			<td>
			<?php
			$settings = array(
				'wpautop' => true,
				'media_buttons' => true,
				'quicktags' => true,
				'textarea_rows' => '15',
				'textarea_name' => 'cat_extra_description',
				'drag_drop_upload' => true
			);
			wp_editor(wp_kses_post($cat_extra_description, ENT_QUOTES, 'UTF-8') , 'cat_extra_description', $settings); 
			?>
			<br />
			<span class="description"><?php _e('This description will be displayed below the list of entries.', 'asap'); ?></span>
		</td>
	</tr>
	</table><?php
}

function asap_save_extra_category_fields($term_id)
{
    if (isset($_POST['cat_extra_description']))
    {
        update_term_meta($_POST['tag_ID'], 'cat_extra_description', $_POST['cat_extra_description']);
    }
}


/*
 * WooCommerce
 */

add_action( 'after_setup_theme', 'asap_woocommerce_support' );

function asap_woocommerce_support() {
	
   add_theme_support( 'woocommerce' );
	
}

require get_template_directory() . '/inc/wc.php';



/*
 * Home text
 */

function asap_show_home_text_before() {
	
	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	
	$asap_home_text_before = get_theme_mod('asap_home_text_before');	
	
	if ( ( $asap_home_text_before ) && ( $paged == 1 ) && ( is_front_page() ) ) : ?>
	
		<div class="content-home-text">
			
			<?php echo do_shortcode($asap_home_text_before); ?>	
					
		</div>
	
	<?php endif; ?>

<?php
	
}
	

function asap_show_home_text_after() {

	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	
	$asap_home_text = get_theme_mod('asap_home_text');	
	
	if ( ( $asap_home_text ) && ( $paged == 1 ) && ( is_front_page() ) ) : ?>
	
		<div class="content-home-text">
			
			<?php echo do_shortcode($asap_home_text); ?>
						
		</div>
	
	<?php endif; ?>

<?php
	
}

/*
 * Video Responsive
 */

add_filter( 'embed_oembed_html', 'wpse_embed_oembed_html', 99, 4 );

function wpse_embed_oembed_html( $cache, $url, $attr, $post_ID ) {
    $classes = array();

    $classes_all = array(
        'responsive',
    );

    if ( false !== strpos( $url, 'vimeo.com' ) ) {
        $classes[] = 'vimeo';
    }

    if ( false !== strpos( $url, 'youtube.com' ) ) {
        $classes[] = 'youtube';
    }

    $classes = array_merge( $classes, $classes_all );

    return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $cache . '</div>';
}


/*
 * Remove Yoast SEO breadcrumb Schema
 */

if ( ( class_exists('WPSEO_Options') ) && ( ! get_theme_mod('asap_hide_breadcrumb') ) )  {
	
	add_filter( 'wpseo_schema_breadcrumb', '__return_false' );
	
}	




/*
 * Number of comments
 */

function asap_comments_count() {

    $comment_count_key = is_single() ? 'asap_comment_count_title' : 'asap_comment_count_title_page';

    $title = get_theme_mod($comment_count_key);

    if ( !empty($title) ) {

        $comments_number = get_comments_number( get_the_ID() );

        printf( 
            '<p>%1$s %2$s</p>',
            intval( $comments_number ),
            esc_html( $title )
        );
    }       
}



function asap_disable_header() {
	
	$disable_header = false;
	
	$disable_single_header = get_post_meta( get_the_ID(), 'disable_header', true ); 
	
	if ( ( get_theme_mod('asap_disable_header') ) || ( $disable_single_header ) ) {
		
		$disable_header = true;
		
	}

	return $disable_header;
	
}

function asap_disable_footer() {
	
	$disable_footer = false;
	
	$disable_single_footer = get_post_meta( get_the_ID(), 'disable_footer', true ); 
	
	if ( ( get_theme_mod('asap_disable_footer') ) || ( $disable_single_footer ) ) {
		
		$disable_footer = true;
		
	}

	return $disable_footer;
	
}



/*
 * Optimize YouTube videos
 */

function asap_optimize_video( $str, $data, $url )  {
	if ( get_option('asap_optimize_youtube') ) {
		if ( ($yt = $data->provider_name == 'YouTube') || ($vm = $data->provider_name == 'Vimeo') ) {
			if($yt) $html = str_replace('feature=oembed', 'feature=oembed&autoplay=1', $str);
			else $html = str_replace('" width=', '?autoplay=1" width=', $str);
			$html = htmlentities($html, ENT_QUOTES);
			$img = $data->thumbnail_url; 
			$title = esc_attr($data->title);
			return '<div onclick="this.outerHTML=\'' . $html . '\'"><img src="'. $img . '"  title="' . $title . '" class="asap-oembed"></div>';
		}
	}
    return $str;
}
add_filter( 'oembed_dataparse', 'asap_optimize_video', 10, 3 );


function asap_clean_cache_menu($wp_admin_bar) {
    if (!empty($_SERVER['REQUEST_URI'])) {
        $referer = filter_var(wp_unslash($_SERVER['REQUEST_URI']), FILTER_SANITIZE_URL);
        $referer = '&_wp_http_referer=' . rawurlencode(remove_query_arg('fl_builder', $referer));
    } else {
        $referer = '';
    }

    $wp_admin_bar->add_menu([
        'id' => 'asap-theme',
        'title' => '<span class="ab-icon dashicons"></span>' . __('Asap Theme', 'asap'),
        'href' => admin_url('admin.php?page=asap-menu'),
    ]);

    $wp_admin_bar->add_menu([
        'id' => 'options-asap',
        'title' => __('Opciones', 'asap'),
        'parent' => 'asap-theme',
        'href' => admin_url('admin.php?page=asap-menu'),
    ]);

    $wp_admin_bar->add_menu([
        'id' => 'clean-cache-asap',
        'title' => __('Borrar datos en caché', 'asap'),
        'parent' => 'asap-theme',
        'href' => wp_nonce_url(admin_url('admin-post.php?action=clean_cache_asap' . $referer), 'clean_cache_asap'),
    ]);
}
add_action('admin_bar_menu', 'asap_clean_cache_menu', 100);

function do_admin_post_clean_cache_asap() {
    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'clean_cache_asap') && isset($_GET['action']) && $_GET['action'] === 'clean_cache_asap') {
        global $wpdb;

        // Borrar todos los transitorios que comienzan con 'asap_cache_'
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_asap_cache_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_asap_cache_%'");

        // Redirigir de vuelta a la página anterior
        wp_safe_redirect(esc_url_raw(wp_get_referer()));
        exit;
    }
}
add_action('admin_post_clean_cache_asap', 'do_admin_post_clean_cache_asap');


		  

/*
 * Ads functions
 */


function asap_show_ads( $place ) {
	
	$cats = asap_categories();

	$show_ads = get_theme_mod('asap_show_ads');
				
	$hide_ads = get_post_meta( get_the_ID(), 'hide_ads', true ); 
	
	/*
	 * Single and Page before content 
	 */
	if ( $place == 1 && $show_ads && ! $hide_ads ) {
		
		$ads_before = 	base64_decode( get_theme_mod('asap_ads_before') );

		if ( $ads_before ) :

			$ads_before_cat 		= get_theme_mod('asap_ads_before_cat');
		
			if ( ( in_array( $ads_before_cat , $cats ) ) || ( ! $ads_before_cat ) ) :

				$ads_before_style 	= get_theme_mod('asap_ads_before_style');	
				
				$ads_before_type 	= get_theme_mod('asap_ads_before_type');	

				$ads_before_device	= get_theme_mod('asap_ads_before_device');	

				$ads_before_margin	= get_theme_mod('asap_ads_before_margin');

				$ads_before_style_margin 	= '';				

				if ( ! $ads_before_style ) : $ads_before_style = 'ads-asap-aligncenter'; endif;		
			
				if ( ! $ads_before_type ) : $ads_before_type = '2'; endif;	

				if ( $ads_before_margin ) : $ads_before_style_margin = '" style="padding:'.$ads_before_margin.'px'; endif;

				switch ( $ads_before_device ) :

					case 2:
						$ads_before_style = $ads_before_style.' ads-asap-desktop';
						break;

					case 3:
						$ads_before_style = $ads_before_style.' ads-asap-mobile';
						break;

				endswitch;
	
				if ( ( is_single() && ( $ads_before_type == '1' ||  $ads_before_type == '2' ) ) ||
				     ( is_page() && ( $ads_before_type == '1' ||  $ads_before_type == '3' ) ) ) : ?>
	
				<div class="ads-asap <?php echo $ads_before_style . $ads_before_style_margin; ?>">
						
				<?php echo do_shortcode($ads_before); ?>

				</div>
					
				<?php
				
				endif;

			endif;

		endif;
			
	}
	
	
	/*
	 * Single and Page after content 
	 */	
	if ( $place == 2 && $show_ads && ! $hide_ads ) {
		
		$ads_after = base64_decode( get_theme_mod('asap_ads_after') );

		if ( $ads_after ) :

			$ads_after_cat 		= get_theme_mod('asap_ads_after_cat');
		
			if ( ( in_array( $ads_after_cat , $cats ) ) || ( ! $ads_after_cat ) ) :

				$ads_after_style 	= get_theme_mod('asap_ads_after_style');	
				
				$ads_after_type 	= get_theme_mod('asap_ads_after_type');	

				$ads_after_device	= get_theme_mod('asap_ads_after_device');	

				$ads_after_margin	= get_theme_mod('asap_ads_after_margin');

				$ads_after_style_margin 	= '';				

				if ( ! $ads_after_style ) : $ads_after_style = 'ads-asap-aligncenter'; endif;		
			
				if ( ! $ads_after_type ) : $ads_after_type = '2'; endif;	

				if ( $ads_after_margin ) : $ads_after_style_margin = '" style="padding:'.$ads_after_margin.'px';	endif;

				switch ( $ads_after_device ) :

					case 2:
						
						$ads_after_style = $ads_after_style.' ads-asap-desktop';

						break;

					case 3:
						
						$ads_after_style = $ads_after_style.' ads-asap-mobile';

						break;

				endswitch;
	
				if ( ( is_single() && ( $ads_after_type == '1' ||  $ads_after_type == '2' ) ) ||
				     ( is_page() && ( $ads_after_type == '1' ||  $ads_after_type == '3' ) ) ) : ?>
	
				<div class="ads-asap <?php echo $ads_after_style . $ads_after_style_margin; ?>">

				<?php echo do_shortcode($ads_after); ?>

				</div>
					
				<?php
	
				endif;
				
			endif;

		endif;
		
	}	
	

		
	
	/*
	 * Sidebar before
	 */
	if ( $place == 3 && $show_ads && ! $hide_ads ) {
		
		$ads_before_sidebar = base64_decode ( get_theme_mod('asap_ads_before_sidebar') );

		if ( $ads_before_sidebar ) :
		
			$ads_before_sidebar_cat = get_theme_mod('asap_ads_before_sidebar_cat');

			if ( ( in_array( $ads_before_sidebar_cat , $cats ) ) || ( ! $ads_before_sidebar_cat ) ) :
						
				$ads_before_sidebar_device = get_theme_mod('asap_ads_before_sidebar_device');
				
				$ads_before_sidebar_style  = '';

				switch ( $ads_before_sidebar_device ) :

					case 2:
						$ads_before_sidebar_style = 'ads-asap-desktop';
						break;

					case 3:
						$ads_before_sidebar_style = 'ads-asap-mobile';
						break;

				endswitch;		

				$show_home  = get_theme_mod('asap_ads_before_sidebar_show_home');
				$show_cats  = get_theme_mod('asap_ads_before_sidebar_show_cats');
				$show_tags  = get_theme_mod('asap_ads_before_sidebar_show_tags');
				$show_posts = get_theme_mod('asap_ads_before_sidebar_show_posts');
				$show_pages = get_theme_mod('asap_ads_before_sidebar_show_pages');

				if ( ( is_home() 		&& $show_home  ) ||
					 ( is_category() 	&& $show_cats  ) || 
					 ( is_tag() 		&& $show_tags  ) || 
					 ( is_single() 		&& $show_posts ) || 
					 ( is_page() 		&& $show_pages ) ) :

				?>
	
				<div class="ads-asap ads-asap-aligncenter <?php echo $ads_before_sidebar_style; ?>">

				<?php echo do_shortcode($ads_before_sidebar); ?>

				</div>
					
				<?php
					
				endif;
	
			endif;
	
		endif;
		
	}
	
	
	/*
	 * Sidebar after
	 */
	if ( $place == 4 && $show_ads && ! $hide_ads ) {
		
		$ads_after_sidebar = base64_decode ( get_theme_mod('asap_ads_after_sidebar') );

		if ( $ads_after_sidebar ) :
		
			$ads_after_sidebar_cat = get_theme_mod('asap_ads_after_sidebar_cat');

			if ( ( in_array( $ads_after_sidebar_cat , $cats ) ) || ( ! $ads_after_sidebar_cat ) ) :
								
				$ads_after_sidebar_device = get_theme_mod('asap_ads_after_sidebar_device');
		
				$ads_after_sidebar_style  = '';

				switch ( $ads_after_sidebar_device ) :

					case 2:
						$ads_after_sidebar_style = 'ads-asap-desktop';
						break;

					case 3:
						$ads_after_sidebar_style = 'ads-asap-mobile';
						break;

				endswitch;		
				
				$show_home  = get_theme_mod('asap_ads_after_sidebar_show_home');
				$show_cats  = get_theme_mod('asap_ads_after_sidebar_show_cats');
				$show_tags  = get_theme_mod('asap_ads_after_sidebar_show_tags');
				$show_posts = get_theme_mod('asap_ads_after_sidebar_show_posts');
				$show_pages = get_theme_mod('asap_ads_after_sidebar_show_pages');

				if ( ( is_home() 		&& $show_home  ) ||
					 ( is_category() 	&& $show_cats  ) || 
					 ( is_tag() 		&& $show_tags  ) || 
					 ( is_single() 		&& $show_posts ) || 
					 ( is_page() 		&& $show_pages ) ) :

				?>
	
				<div class="ads-asap ads-asap-aligncenter <?php echo $ads_after_sidebar_style; ?> sticky">

				<?php echo do_shortcode($ads_after_sidebar); ?>

				</div>
					
				<?php
					
				endif;
	
			endif;
	
		endif;
		
	}	
	
	
	/*
	 * After header
	 */	
	if ( $place == 5 && $show_ads && ! $hide_ads ) {

		$ads_header = base64_decode( get_theme_mod('asap_ads_header') );

		if ( $ads_header ) :

			$ads_header_cat 	= get_theme_mod('asap_ads_header_cat');

			if ( ( in_array( $ads_header_cat , $cats ) ) || ( ! $ads_header_cat ) ) :
				
				$ads_header_device	= 	get_theme_mod('asap_ads_header_device');	

				$ads_header_margin	= 	get_theme_mod('asap_ads_header_margin');

				$ads_header_style_margin 	= '';		
				
				$ads_header_style = '';
					
				if ( $ads_header_margin ) : $ads_header_style_margin = '" style="padding:'.$ads_header_margin.'px'; endif;

				switch ( $ads_header_device ) :

					case 2:
						
						$ads_header_style = 'ads-asap-desktop';

						break;

					case 3:
						
						$ads_header_style = 'ads-asap-mobile';

						break;

				endswitch;	

				$show_home  = get_theme_mod('asap_ads_header_show_home');
				$show_cats  = get_theme_mod('asap_ads_header_show_cats');
				$show_tags  = get_theme_mod('asap_ads_header_show_tags');
				$show_posts = get_theme_mod('asap_ads_header_show_posts');
				$show_pages = get_theme_mod('asap_ads_header_show_pages');

				if ( ( is_home() 		&& $show_home  ) ||
					 ( is_category() 	&& $show_cats  ) || 
					 ( is_tag() 		&& $show_tags  ) || 
					 ( is_single() 		&& $show_posts ) || 
					 ( is_page() 		&& $show_pages ) ) :
				
				?>

				<div class="ads-asap ads-asap-top ads-asap-aligncenter <?php echo $ads_header_style . $ads_header_style_margin; ?>">

				<?php echo do_shortcode($ads_header); ?>

				</div>

				<?php

				endif;

			endif;

		endif;
		
	}
	
	
	/*
	 * Single and Page before featured image
	 */
	if ( $place == 6 && $show_ads && ! $hide_ads ) {
		
		$ads_before_image = 	base64_decode( get_theme_mod('asap_ads_before_image') );

		if ( $ads_before_image ) :

			$ads_before_image_cat 		= get_theme_mod('asap_ads_before_image_cat');
		
			if ( ( in_array( $ads_before_image_cat , $cats ) ) || ( ! $ads_before_image_cat ) ) :

				$ads_before_image_style 	= get_theme_mod('asap_ads_before_image_style');	
				
				$ads_before_image_type 	= get_theme_mod('asap_ads_before_image_type');	

				$ads_before_image_device	= get_theme_mod('asap_ads_before_image_device');	

				$ads_before_image_margin	= get_theme_mod('asap_ads_before_image_margin');

				$ads_before_image_style_margin 	= '';				

				if ( ! $ads_before_image_style ) : $ads_before_image_style = 'ads-asap-aligncenter'; endif;		
			
				if ( ! $ads_before_image_type ) : $ads_before_image_type = '2'; endif;	

				if ( $ads_before_image_margin ) : $ads_before_image_style_margin = '" style="padding:'.$ads_before_image_margin.'px'; endif;

				switch ( $ads_before_image_device ) :

					case 2:
						$ads_before_image_style = $ads_before_image_style.' ads-asap-desktop';
						break;

					case 3:
						$ads_before_image_style = $ads_before_image_style.' ads-asap-mobile';
						break;

				endswitch;
	
				if ( ( is_single() && ( $ads_before_image_type == '1' ||  $ads_before_image_type == '2' ) ) ||
				     ( is_page() && ( $ads_before_image_type == '1' ||  $ads_before_image_type == '3' ) ) ) : ?>
	
				<div class="ads-asap <?php echo $ads_before_image_style . $ads_before_image_style_margin; ?>">
						
				<?php echo do_shortcode($ads_before_image); ?>

				</div>
					
				<?php
				
				endif;

			endif;

		endif;
			
	}
	
}


/*
 * Human Time Diff
 */
function asap_human_time_diff( $post_date ) {
    return sprintf( __('%s ago', 'asap'), human_time_diff(strtotime($post_date), current_time('timestamp')) );
}


/*
 * Show author
 */

function asap_show_author() {
    global $post;
	$asap_disable_author = get_post_meta($post->ID, 'asap_disable_author', true);
    if ($asap_disable_author) {
        return false;
    }
    if (asap_is_author_visible()) {
        asap_display_author_box();
    } elseif (asap_is_date_visible()) {
        asap_display_date_box();
    }
}

function asap_is_author_visible() {
    return (get_theme_mod('asap_show_author') && is_single()) || (get_theme_mod('asap_show_author_page') && is_page());
}

function asap_is_date_visible() {
    return (get_theme_mod('asap_show_date_single') && is_single()) || (get_theme_mod('asap_show_date_page') && is_page());
}

function asap_display_author_box() {
    ?>
    <div class="content-author">
        <div class="author-image">
            <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
        </div>
        <div class="author-desc">
            <p>
                <?php if (is_single() && !get_theme_mod('asap_deactivate_author_link')) { ?>
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                        <?php the_author(); ?>
                    </a>
                <?php } else { ?>
                    <span class="asap-author"><?php the_author(); ?></span>
                <?php } ?>
            </p>
            <?php if (asap_is_date_visible()) { ?>
                <p><?php asap_display_date(); ?></p>
            <?php } ?>
        </div>
    </div>
    <?php
}

function asap_display_date_box() {
    ?>
    <div class="show-date">
        <p><?php asap_display_date(); ?></p>
    </div>
    <?php
}

function asap_display_date($show_updated_text = true) {
    $show_relative_dates = get_theme_mod('asap_show_relative_dates', true);
    $post_date = get_the_date('Y-m-d H:i:s');
    $post_modified = get_the_modified_date('Y-m-d H:i:s');

    if ($show_relative_dates) {
        echo asap_human_time_diff($post_date);

        if ($post_date != $post_modified && !asap_should_hide_update_date() && $show_updated_text) {
            echo '<span class="asap-post-update"> · ' . __('Updated', 'asap') . ' ' . asap_human_time_diff($post_modified) . '</span>';
        }
    } else {
        echo get_the_date('d/m/Y');

        if ($post_date != $post_modified && !asap_should_hide_update_date() && $show_updated_text) {
            echo '<span class="asap-post-update"> · ' . __('Updated on:', 'asap') . ' ' . get_the_modified_date('d/m/Y') . '</span>';
        }
    }
}


function asap_should_hide_update_date() {
    return (is_single() && get_theme_mod('asap_hide_update_date_single')) || 
           (is_page() && get_theme_mod('asap_hide_update_date_page'));
}



/*
 * Show author box
 */


function asap_show_author_box() {
    if ((get_theme_mod('asap_show_box_author') && is_single()) || 
        (get_theme_mod('asap_show_box_author_page') && is_page())) {

        $author_social_profiles = [
            'facebook' => get_the_author_meta('author_fb'),
            'twitter' => get_the_author_meta('author_tw'),
			'instagram' => get_the_author_meta('author_ig'),
            'pinterest' => get_the_author_meta('author_pin'),
            'youtube' => get_the_author_meta('author_yt'),
            'linkedin' => get_the_author_meta('author_lk'),           
        ];

        $author_social_profiles = array_filter($author_social_profiles);

        ?>
        <div class="author-box">
            <div class="author-box-avatar">
                <?php echo get_avatar(get_the_author_meta('email'), '80'); ?>
            </div>
            <div class="author-box-info">
                <p class="author-box-name">
                    <?php if (is_single() && !get_theme_mod('asap_deactivate_author_link')) { ?>
                        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>
                    <?php } else { ?>
                        <?php the_author(); ?>
                    <?php } ?>
                </p>
                <p class="author-box-desc"><?php the_author_meta('description'); ?></p>
                <?php if (!empty($author_social_profiles)) : ?>
                    <div class="author-box-social">
                        <?php foreach ($author_social_profiles as $network => $url) :
                            if (!empty($url)) : 
                                $svg_icon = asap_get_social_network_svg($network); 
                                if (!empty($svg_icon)) : ?>
                                    <a href="<?php echo esc_url($url); ?>" title="<?php echo esc_attr(ucfirst($network)); ?>" class="asap-icon icon-<?php echo esc_attr($network); ?>" target="_blank" rel="nofollow noopener">
                                        <?php echo $svg_icon; ?>
                                    </a>
                                <?php endif;
                            endif;
                        endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

function asap_get_social_network_svg($network) {
    switch ($network) {
        case 'twitter':
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/> <path d="M4 4l11.733 16h4.267l-11.733 -16z" /><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" /></svg>';
        case 'facebook':
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" /></svg>';
        case 'instagram':
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="4" /><circle cx="12" cy="12" r="3" /><line x1="16.5" y1="7.5" x2="16.5" y2="7.501" /></svg>';
        case 'pinterest':
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="8" y1="20" x2="12" y2="11" /><path d="M10.7 14c.437 1.263 1.43 2 2.55 2c2.071 0 3.75 -1.554 3.75 -4a5 5 0 1 0 -9.7 1.7" /><circle cx="12" cy="12" r="9" /></svg>';
        case 'youtube':
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="4" /><path d="M10 9l5 3l-5 3z" /></svg>';
        case 'linkedin':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M8 11l0 5" /><path d="M8 8l0 .01" /><path d="M12 16l0 -5" /><path d="M16 16v-3a2 2 0 0 0 -4 0" /></svg>';
        default:
            return ''; // En caso de que no haya un SVG para la red dada
    }
}




	
/*
 * Tags in pages
 */

function asap_tags_support() {
	if ( get_theme_mod('asap_enable_tags_page') ) {
		register_taxonomy_for_object_type('post_tag', 'page');
	}
}

function asap_tags_support_query($wp_query) {
	if ( get_theme_mod('asap_enable_tags_page') ) {	
		if ( $wp_query->get('tag') ) $wp_query->set('post_type', 'any');
	}
}

add_action('init', 'asap_tags_support');

add_action('pre_get_posts', 'asap_tags_support_query');



function asap_show_ads_loop( $count ) {
	$show_ads = get_theme_mod('asap_show_ads');
	
	if ( ! $show_ads ) {
		return;
	}

	$ads = [
		1 => base64_decode( get_theme_mod('asap_ads_loop_1') ),
		2 => base64_decode( get_theme_mod('asap_ads_loop_2') ),
		3 => base64_decode( get_theme_mod('asap_ads_loop_3') ),
		4 => base64_decode( get_theme_mod('asap_ads_loop_4') ),
		5 => base64_decode( get_theme_mod('asap_ads_loop_5') ),
	];

	$columns = get_theme_mod('asap_columns', 3);

	foreach ($ads as $index => $ad) {
		if ($ad) {
			$ad_place = get_theme_mod("asap_ads_loop_{$index}_place", 1);
			$show_position = $ad_place * $columns;

			if ($count == $show_position) {
				$show_home = get_theme_mod("asap_ads_loop_{$index}_show_home");
				$show_cats = get_theme_mod("asap_ads_loop_{$index}_show_cats");
				$show_tags = get_theme_mod("asap_ads_loop_{$index}_show_tags");

				if ((is_home() && $show_home) || (is_category() && $show_cats) || (is_tag() && $show_tags)) {
					$ad_device = get_theme_mod("asap_ads_loop_{$index}_device");
					$ad_style = '';

					if ($ad_device == 2) {
						$ad_style = 'ads-asap-desktop';
					} elseif ($ad_device == 3) {
						$ad_style = 'ads-asap-mobile';
					}

					?>
					<div class="ads-asap ads-asap-aligncenter ads-asap-loop <?php echo esc_attr($ad_style); ?>">
						<?php echo apply_filters('the_content', $ad); ?>
					</div>
					<?php
				}
			}
		}
	}
}


/*
 * Dynamic Last Paragraph
 */

function asap_show_dynamic_single()
{

	$disable_dinamyc = get_post_meta( get_the_ID(), 'asap_disable_dynamic', true );
	
	if ( get_theme_mod('asap_show_last_paragraph_single') && ! $disable_dinamyc )
	{
		
		$tit = '<strong>' . get_the_title() . '</strong>';
		
		$cats = get_the_category();
		$cat_link = '';
		$cat_name = '';

		if ( ! empty( $cats ) && isset( $cats[0]->term_id ) ) {
			$cat_link = esc_url( get_category_link( $cats[0]->term_id ) );
			$cat_name = esc_html( $cats[0]->name );
		}

		$cat = ! empty( $cat_link ) ? '<a href="' . $cat_link . '"><strong>' . $cat_name . '</strong></a>' : '';

    	$tags = get_the_tags(get_the_ID());
		$tag_link = '';
		$tag_name = '';

		if ( ! empty( $tags ) && isset( $tags[0]->term_id ) ) {
			$tag_link = esc_url( get_tag_link( $tags[0]->term_id ) );
			$tag_name = esc_html( $tags[0]->name );
		}

		$tag = ! empty( $tag_link ) ? '<a href="' . $tag_link . '"><strong>' . $tag_name . '</strong></a>' : '';

		$year = date('Y');
		
		$str = get_theme_mod('asap_last_paragraph_single');
		
		if ( ! $str ) {
			$str = __( 'Si quieres conocer otros artículos parecidos a %%title%% puedes visitar la categoría %%category%%.', 'asap' );
		}

		$placeholders = array("%%title%%", "%%category%%", "%%tag%%", "%%currentyear%%");
		$values = array($tit, $cat, $tag, $year);
		$message = str_replace($placeholders, $values, $str);

		echo '<p>' . wp_kses_post( $message ) . '</p>';
	}
	
}


function asap_show_dynamic_page()
{
	$disable_dinamyc = get_post_meta( get_the_ID(), 'asap_disable_dynamic', true );

	if ( get_theme_mod('asap_show_last_paragraph_page') && ! $disable_dinamyc )
	{
				
		$tit = '<strong>' . get_the_title() . '</strong>';
		
    $tags = get_the_tags(get_the_ID());
    $tag_link = '';
    $tag_name = '';

    if ( ! empty( $tags ) && isset( $tags[0]->term_id ) ) {
        $tag_link = esc_url( get_tag_link( $tags[0]->term_id ) );
        $tag_name = esc_html( $tags[0]->name );
    }

    $tag = ! empty( $tag_link ) ? '<a href="' . $tag_link . '"><strong>' . $tag_name . '</strong></a>' : '';
		
		$year = date('Y');
	
		$str = get_theme_mod('asap_last_paragraph_page');

    if ( ! $str ) {
        $str = __( 'Esperamos que te haya gustado este artículo sobre %%title%%.', 'asap' );
    }

    $placeholders = array("%%title%%", "%%tag%%", "%%currentyear%%");
    $values = array($tit, $tag, $year);
    $message = str_replace($placeholders, $values, $str);

    echo '<p>' . wp_kses_post( $message ) . '</p>';
	}
	
}


/**
 * Search = título o meta, sólo posts/páginas publicados.
 * Copia este bloque; borra los anteriores.
 */
add_action( 'pre_get_posts', 'asap_hardened_search', 99 );
function asap_hardened_search( WP_Query $q ) {

    if ( ! $q->is_main_query() || ! $q->is_search() || is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return;
    }

    /* 1. Tipos de contenido permitidos ------------------------------ */
    $types = get_theme_mod( 'asap_search_post_types', [ 'post', 'page' ] );
    $types = array_unique( array_map( 'trim', (array) $types ) );
    $types = array_diff( $types, [ 'attachment' ] );         // fuera las imágenes
    $q->set( 'post_type', $types ?: [ 'post', 'page' ] );

    /* 2. Sólo publicados -------------------------------------------- */
    $q->set( 'post_status', [ 'publish' ] );

    /* 3. Reemplazamos la cláusula de búsqueda ----------------------- */
    // Eliminamos posibles residuos
    remove_filter( 'posts_search', 'asap_search_title_or_meta', 10 );
    remove_filter( 'posts_where',  'asap_search_title_and_meta_exists', 10 );

    add_filter( 'posts_search', 'asap_search_title_or_meta', 10, 2 );
}

function asap_search_title_or_meta( $search, WP_Query $q ) {

    if ( ! $q->is_main_query() || ! $q->is_search() ) {
        return $search;
    }

    // Nos desenganchamos inmediatamente para no afectar a otras queries
    remove_filter( 'posts_search', __FUNCTION__, 10 );

    $term = $q->get( 's' );
    if ( $term === '' ) {
        return $search;
    }

    global $wpdb;
    $like = '%' . $wpdb->esc_like( $term ) . '%';

    return $wpdb->prepare(
        " AND ( {$wpdb->posts}.post_title LIKE %s
                OR EXISTS (
                    SELECT 1
                    FROM {$wpdb->postmeta} pm
                    WHERE pm.post_id = {$wpdb->posts}.ID
                      AND pm.meta_value LIKE %s
                )
              ) ",
        $like,
        $like
    );
}


/*
 * Custom codes
 */

add_action('wp_head', 'asap_custom_code_header', 99);

function asap_custom_code_header()
{ 
	if ( is_single() || is_page() )
	{
		global $post;
	
		$head_custom_code = get_post_meta( $post->ID, 'head_custom_code', true );
		
		if ( $head_custom_code )
		{
			echo $head_custom_code;
		}
	}
}

add_action('wp_footer', 'asap_custom_code_footer', 99);

function asap_custom_code_footer()
{ 
	if ( is_single() || is_page() )
	{
		global $post;

		$foot_custom_code = get_post_meta( $post->ID, 'foot_custom_code', true );
		
		if (  $foot_custom_code )
		{
			echo $foot_custom_code;			
		}
	}
}



function asap_get_hero_content($post_type) {
    $content = [
        'title' => '',
        'subtitle' => '',
        'thumbnail' => '',
        'show_search' => false
    ];

    if ($post_type == 'post' || $post_type == 'page') {
        global $post;
        $content['title'] = get_the_title();
        $content['subtitle'] = get_post_meta($post->ID, 'subtitle_post', true);
        $content['thumbnail'] = get_the_post_thumbnail(get_the_ID(), 'full', [ 'loading' => false ]);
        $header_design = get_post_meta(get_the_ID(), 'asap_header_design', true) ?: get_theme_mod('asap_hero_' . $post_type, 'normal');
    } elseif ($post_type == 'cat') {
        $content['title'] = single_cat_title('', false);
        $term = get_queried_object();
        $header_design = get_term_meta($term->term_id, 'asap_header_design', true) ?: get_theme_mod('asap_hero_cat', 'normal');
        $image_id = get_term_meta($term->term_id, 'category-cover-image-id', true);
        $content['thumbnail'] = wp_get_attachment_image($image_id, 'full', false, ['loading' => false]);
    }

    if (isset($header_design) && $header_design == 1) {
        $content['show_search'] = true;
    }

    return $content;
}

function asap_show_hero($post_type) {
    $content = asap_get_hero_content($post_type);
    ?>
    <div class="asap-hero">
        <picture>
            <?php echo $content['thumbnail']; ?>
        </picture>
        <div class="asap-hero-content">
            <h1><?php echo esc_html($content['title']); ?></h1>
            <?php if (!empty($content['subtitle'])): ?>
				<?php echo wp_kses_post( do_shortcode( wpautop( $content['subtitle'] ) ) ); ?>
		    <?php endif; ?>
            <?php if ($content['show_search']): ?>
                <?php echo do_shortcode('[asap_search]'); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}


function asap_remove_comments_links($comment_text) {
	$remove_links = get_option('asap_remove_comments_links', true);
	if ( $remove_links )
	{
		$comment_text = preg_replace('/<a[^>]+>/', '', $comment_text); 
		$comment_text = preg_replace('/<\/a>/', '', $comment_text); 
	}
	return $comment_text;		

}

add_filter('comment_text', 'asap_remove_comments_links');



/*
 * Get primary category for loops
 */

function asap_get_category() {
    $primary_cat_id = null;
    $post_id = get_the_ID();

    if ( class_exists('WPSEO_Meta') ) {
        $primary_cat_id = get_post_meta( $post_id, '_yoast_wpseo_primary_category', true );
    }

    if ( empty($primary_cat_id) && class_exists('RankMath') ) {
        $primary_cat_id = get_post_meta( $post_id, 'rank_math_primary_category', true );
    }

    if ( empty($primary_cat_id) ) {
        $categories = get_the_category( $post_id );
        if (!empty($categories)) {
            return $categories[0];
        }
    } else {
        return get_category( $primary_cat_id );
    }

    return false; 
}



/*
 * Check if is new post
 */
function asap_is_new($days_new, $current_time) 
{

    $post_time = get_the_time('U');

    $date_diff = ($current_time - $post_time) / DAY_IN_SECONDS;

    return $date_diff <= $days_new;
}






/*
 * Función año actual en Editor clásico
 */
function asap_replace_current_year_in_widgets( $text ) {
    $current_year = date('Y');
    $text = str_replace('%%currentyear%%', $current_year, $text);
    return $text;
}
add_filter('widget_text', 'asap_replace_current_year_in_widgets');
add_filter('widget_title', 'asap_replace_current_year_in_widgets');


/*
 * Función año actual en Gutenberg
 */
function asap_replace_current_year_shortcode() {
    return date('Y');
}
add_shortcode('currentyear', 'asap_replace_current_year_shortcode');



/*
 * Función para agregar las imágenes al feed RSS
 */
function asap_add_featured_image_to_feed($content) {
    global $post;
    if(has_post_thumbnail($post->ID)) {
        $content = '<div>' . get_the_post_thumbnail($post->ID, 'large') . '</div>' . $content;
    }
    return $content;
}
add_filter('the_excerpt_rss', 'asap_add_featured_image_to_feed', 1000, 1);
add_filter('the_content_feed', 'asap_add_featured_image_to_feed', 1000, 1);




/*
 * Función para remover el feed
 */
function asap_remove_rss_feed_links() {
	if (get_option('asap_remove_feed_links')):
	    remove_action('wp_head', 'feed_links', 2);
	    remove_action('wp_head', 'feed_links_extra', 3);
	    add_action('template_redirect', function() {
	        if (is_feed()) {
	            wp_redirect(home_url(), 301);  // Redirect to home page or use get_404_template() for a 404 page
	            exit;
	        }
	    });
	endif;
}

add_action('wp', 'asap_remove_rss_feed_links');



/*
 * Función para mostrar el árbol de categorías
 */
function display_category_tree($current_category_id) {
	    static $shown_categories = array(); // Array para guardar las categorías que se han mostrado
	    $category = get_category($current_category_id);
	    $parent_id = $category->parent;
	    $grandparent_id = ($parent_id != 0) ? get_category($parent_id)->parent : 0;

	    $checksubcat = get_terms(array(
	        'taxonomy' => 'category',
	        'parent' => $current_category_id,
	        'hide_empty' => false
	    ));

	    // Si no hay subcategorías, no mostrar nada
	    if (empty($checksubcat) && empty($parent_id) && empty($grandparent_id)) {
	        return;
	    }

	    echo '<div class="category-filters">';

	    // Mostrar la categoría abuelo si existe
	    if ($grandparent_id != 0 && !in_array($grandparent_id, $shown_categories)) {
	        $grandparent_active_class = ($grandparent_id == get_queried_object_id() || $grandparent_id == $parent_id || $grandparent_id == $current_category_id || $grandparent_id == get_category($category->parent)->parent) ? 'checked' : '';
	        echo '<a href="' . get_category_link($grandparent_id) . '" class="filter-link ' . $grandparent_active_class . '"><span class="checkbox"></span>' . get_cat_name($grandparent_id) . '</a>';
	        $shown_categories[] = $grandparent_id; // Agregar al array de categorías mostradas

	        // Mostrar las hermanas del padre (categorías tías)
	        $aunt_categories = get_terms(array(
	            'taxonomy' => 'category',
	            'parent' => $grandparent_id,
	            'hide_empty' => false
	        ));

	        echo '<div class="sibling-categories">';
	        foreach ($aunt_categories as $aunt) {
	            if (!in_array($aunt->term_id, $shown_categories)) {
	                $link = get_term_link($aunt->term_id);
	                $active_class = ($aunt->term_id == $parent_id) ? 'checked' : '';
	                echo '<div class="subcategory">';
	                echo '<a href="' . esc_url($link) . '" class="filter-link ' . $active_class . '"><span class="checkbox"></span>' . esc_html($aunt->name) . '</a>';
	                echo '</div>';
	                $shown_categories[] = $aunt->term_id; // Agregar al array de categorías mostradas
	            }
	        }
	        echo '</div>';
	    }

	    // Mostrar la categoría padre si existe
	    if ($parent_id != 0 && !in_array($parent_id, $shown_categories)) {
	        $parent_active_class = ($parent_id == get_queried_object_id() || $parent_id == $current_category_id || $parent_id == $category->parent) ? 'checked' : '';
	        echo '<a href="' . get_category_link($parent_id) . '" class="filter-link ' . $parent_active_class . '"><span class="checkbox"></span>' . get_cat_name($parent_id) . '</a>';
	        $shown_categories[] = $parent_id; // Agregar al array de categorías mostradas

	        // Mostrar las categorías hermanas
	        $sibling_categories = get_terms(array(
	            'taxonomy' => 'category',
	            'parent' => $parent_id,
	            'hide_empty' => false
	        ));

	        echo '<div class="sibling-categories">';
	        foreach ($sibling_categories as $sibling) {
	            if (!in_array($sibling->term_id, $shown_categories)) {
	                $link = get_term_link($sibling->term_id);
	                $active_class = ($sibling->term_id == $current_category_id) ? 'checked' : '';
	                echo '<div class="subcategory">';
	                echo '<a href="' . esc_url($link) . '" class="filter-link ' . $active_class . '"><span class="checkbox"></span>' . esc_html($sibling->name) . '</a>';
	                echo '</div>';
	                $shown_categories[] = $sibling->term_id; // Agregar al array de categorías mostradas
	            }
	        }
	        echo '</div>';
	    }

	    // Mostrar las subcategorías de la categoría actual
	    $subcategories = get_terms(array(
	        'taxonomy' => 'category',
	        'parent' => $current_category_id,
	        'hide_empty' => false
	    ));

	    if (!empty($subcategories) || !in_array($current_category_id, $shown_categories)) {
	        if (!in_array($current_category_id, $shown_categories)) {
	            // Enlace "Todas" para la categoría actual
	            $all_class = ($current_category_id == get_queried_object_id()) ? 'checked' : '';
	            echo '<a href="' . get_category_link($current_category_id) . '" class="filter-link ' . $all_class . '"><span class="checkbox"></span>' . get_cat_name($current_category_id) . '</a>';
	            $shown_categories[] = $current_category_id; // Agregar al array de categorías mostradas
	        }

	        if (!empty($subcategories)) {
	            echo '<div class="sibling-categories">';
	            foreach ($subcategories as $subcategory) {
	                if (!in_array($subcategory->term_id, $shown_categories)) {
	                    $link = get_term_link($subcategory->term_id);
	                    $active_class = ($subcategory->term_id == get_queried_object_id()) ? 'checked' : '';
	                    echo '<div class="subcategory">';
	                    echo '<a href="' . esc_url($link) . '" class="filter-link ' . $active_class . '"><span class="checkbox"></span>' . esc_html($subcategory->name) . '</a>';
	                    echo '</div>';
	                    $shown_categories[] = $subcategory->term_id; // Agregar al array de categorías mostradas
	                }
	            }
	            echo '</div>';
	        }
	    }

	    echo '</div>';
}



function asap_get_category_comment_count($category_id) {
    // Intentar obtener el conteo de comentarios desde la caché
    $cached_count = get_transient('asap_category_comments_count_' . $category_id);
    if ($cached_count !== false) {
        return $cached_count;
    }

    // Si no está en caché, realizar la consulta
    $args = array(
        'category' => $category_id,
        'status' => 'approve'
    );
    $comments_query = new WP_Comment_Query;
    $comments = $comments_query->query($args);
    $count = count($comments);

    // Guardar el conteo en la caché por un día
    set_transient('asap_category_comments_count_' . $category_id, $count, DAY_IN_SECONDS);

    return $count;
}



/*
 * Optimizer
 */

require get_template_directory() . '/inc/optimizer.php';


/*
 * Create Page
 */
function asap_renderMenuPage() {
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    if (empty($active_tab)) {
        $active_tab = 'options_settings';
    }

    $tabs = [
        'options_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M19.875 6.27a2.225 2.225 0 0 1 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z" />
                    <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                </svg>',
            'label' => 'General Options'
        ],
        'performance_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#464646" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cloud-lightning">
                    <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path><polyline points="13 11 9 17 15 17 11 23"></polyline>
                </svg>',
            'label' => 'Performance'
        ],
        'security_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shield-half" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                    <path d="M12 3v18" />
                </svg>',
            'label' => 'Security'
        ],
        'schema_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
							  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							  <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
							  <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
							  <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
							  <path d="M4 20l14 0" />
							</svg>',
            'label' => 'Schema'
        ],
        'stars_settings' => [
            'icon' => '
                 <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
							  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							  <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
							</svg>   ',
            'label' => 'Valoración de estrellas'
        ],
        'predictive_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-2-search" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
						  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
						  <path d="M8 9h8" />
						  <path d="M8 13h5" />
						  <path d="M12 21l-.5 -.5l-2.5 -2.5h-3a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v4.5" />
						  <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
						  <path d="M20.2 20.2l1.8 1.8" />
						</svg>  ',
            'label' => 'Predictive Search'
        ],
        'progress_settings' => [
            'icon' => '
			<svg
			  xmlns="http://www.w3.org/2000/svg"
			  width="20"
			  height="20"
			  viewBox="0 0 24 24"
			  fill="none"
			  stroke="#464646"
			  stroke-width="1.25"
			  stroke-linecap="round"
			  stroke-linejoin="round"
			><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" />
			  <path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
			  <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" />
			  <path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
			  <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" />
			  <path d="M9 12l2 2l4 -4" />
			</svg>',
            'label' => 'Barra de progreso'
        ],        
        'cookies_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cookie" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
							  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							  <path d="M8 13v.01" />
							  <path d="M12 17v.01" />
							  <path d="M12 12v.01" />
							  <path d="M16 14v.01" />
							  <path d="M11 8v.01" />
							  <path d="M13.148 3.476l2.667 1.104a4 4 0 0 0 4.656 6.14l.053 .132a3 3 0 0 1 0 2.296c-.497 .786 -.838 1.404 -1.024 1.852c-.189 .456 -.409 1.194 -.66 2.216a3 3 0 0 1 -1.624 1.623c-1.048 .263 -1.787 .483 -2.216 .661c-.475 .197 -1.092 .538 -1.852 1.024a3 3 0 0 1 -2.296 0c-.802 -.503 -1.419 -.844 -1.852 -1.024c-.471 -.195 -1.21 -.415 -2.216 -.66a3 3 0 0 1 -1.623 -1.624c-.265 -1.052 -.485 -1.79 -.661 -2.216c-.198 -.479 -.54 -1.096 -1.024 -1.852a3 3 0 0 1 0 -2.296c.48 -.744 .82 -1.361 1.024 -1.852c.171 -.413 .391 -1.152 .66 -2.216a3 3 0 0 1 1.624 -1.623c1.032 -.256 1.77 -.476 2.216 -.661c.458 -.19 1.075 -.531 1.852 -1.024a3 3 0 0 1 2.296 0z" />
							</svg>',
            'label' => 'Cookie Notice'
        ],    

        'megamenu_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-grid" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
				  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
				  <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
				  <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
				  <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
				  <path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
				</svg>',
            'label' => 'Megamenu'
        ],   
               'addson_settings' => [
            'icon' => '
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-table-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.25" stroke="#464646" fill="none" stroke-linecap="round" stroke-linejoin="round">
				  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
				  <path d="M12.5 21h-7.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5" />
				  <path d="M3 10h18" />
				  <path d="M10 3v18" />
				  <path d="M16 19h6" />
				  <path d="M19 16v6" />
				</svg>',
            'label' => 'Complementos'
        ],                 
    ];

    ?>

    <div class="wrap wrapper-asap-options">
        <div class="nav-tab-wrapper">
            <div style="text-align:center;">
                <img class="asap-logo" src="<?php echo ASAP_THEME_URL; ?>/assets/img/logo.png" width="200" height="54" />
            </div>

            <?php if (current_user_can('manage_options')) : ?>
                <?php foreach ($tabs as $tab_key => $tab_info): ?>
                    <a href="?page=<?php echo $_GET['page']; ?>&tab=<?php echo $tab_key; ?>" class="nav-tab <?php echo $active_tab == $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo $tab_info['icon']; ?>
                        <span><?php _e($tab_info['label'], 'asap'); ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <section id="asap-options options-general" class="asap-options section-content active">
            <?php
            // Include the file corresponding to the active tab
            if (isset($tabs[$active_tab])) {
                include(ASAP_THEME_DIR . '/settings/' . str_replace('_settings', '', $active_tab) . '.php');
            }
            ?>
        </section>
         <script>
                jQuery(function(){
                  jQuery('input[type=checkbox]').switchify();
                });
           </script>
    </div>
    <?php
}


function asap_count_posts_in_category_and_subcategories($term_id) {
    $term = get_term($term_id, 'category');
    $count = $term->count;  // Contador inicial de la categoría principal

    $subcategories = get_terms(array(
        'taxonomy' => 'category',
        'parent' => $term_id,
        'hide_empty' => false
    ));

    foreach ($subcategories as $subcategory) {
        $count += asap_count_posts_in_category_and_subcategories($subcategory->term_id); // Suma recursiva para las subcategorías
    }

    return $count;
}


function asap_invalidate_cache_on_status_change($new_status, $old_status, $post) {
	$enable_cache = get_theme_mod('asap_home_enable_cache', false);
    if ($post->post_type == 'post' && $new_status !== $old_status && $enable_cache) {
        global $wpdb;

        // Borrar todos los transitorios que comienzan con 'asap_cache_'
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_asap_cache_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_asap_cache_%'");
    }
}
add_action('transition_post_status', 'asap_invalidate_cache_on_status_change', 10, 3);


function asap_redirect_404_to_home() {
    if (is_404() && get_option('asap_redirect_404_home')) {
        wp_redirect(home_url(), 301);
        exit();
    }
}
add_action('template_redirect', 'asap_redirect_404_to_home');



// Función auxiliar para detectar si se están usando bloques de Gutenberg en la página
function asap_is_page_using_gutenberg() {
    global $post;
    if ( ! $post ) {
        return false;
    }

    if ( has_blocks( $post->ID ) ) {
        return true;
    }

    return false;
}


function remove_lazy_loading_from_asap_hero($content) {
    // Remueve `loading="lazy"` solo de imágenes dentro de `.asap-herob-img`
    return preg_replace_callback('/<img[^>]+class="([^"]*asap-herob-img[^"]*)"[^>]*>/i', function ($matches) {
        return preg_replace('/\sloading="lazy"/i', '', $matches[0]);
    }, $content);
}
add_filter('the_content', 'remove_lazy_loading_from_asap_hero', 20);


function asap_enqueue_codemirror() {
    wp_enqueue_code_editor(array('type' => 'text/css'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
}
add_action('admin_enqueue_scripts', 'asap_enqueue_codemirror');

function asap_custom_codemirror_styles() {
    echo '<style>
        .postmetabox .CodeMirror {
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            min-height: 150px;
            max-height: 150px; /* Establece una altura máxima */
            height: auto;
        }
       	.postmetabox .CodeMirror-scroll {
            min-height: 150px;		
            max-height: 150px; /* Controla la altura del área de scroll */
        }
        .postmetabox .CodeMirror-gutters {
            background-color: #f5f5f5;
            border-right: 1px solid #ddd;
        }
       	.postmetabox .CodeMirror-linenumbers {
            padding-right: 8px;
        }
    </style>';
}
add_action('admin_head', 'asap_custom_codemirror_styles');


/**
 * Elimina las migas de pan (breadcrumb) de Yoast en su JSON-LD, 
 * tanto la propiedad "breadcrumb" dentro de WebPage/CollectionPage
 * como el nodo BreadcrumbList, para que no existan duplicados ni referencias rotas.
 */
add_filter('wpseo_schema_graph', 'remove_yoast_breadcrumbs_completely', 999);
function remove_yoast_breadcrumbs_completely($graph) {

    foreach ($graph as $index => $node) {

        // 1) Si el nodo es de tipo "BreadcrumbList" (la lista de migas), lo quitamos del array.
        if (isset($node['@type']) && $node['@type'] === 'BreadcrumbList') {
            unset($graph[$index]);
            continue;
        }

        // 2) Si es un "WebPage" o "CollectionPage" que contiene "breadcrumb", también lo quitamos.
        if (
            isset($node['@type']) 
            && ( $node['@type'] === 'WebPage' || $node['@type'] === 'CollectionPage' )
            && isset($node['breadcrumb'])
        ) {
            unset($graph[$index]['breadcrumb']);
        }
    }

    // Reordena los índices del array para evitar huecos 
    // (no suele ser crítico, pero así queda limpio).
    return array_values($graph);
}


/*-------------------------------------------------
 * 1) Quita jquery‑migrate de la dependencia de jQuery
 *------------------------------------------------*/
add_action( 'wp_default_scripts', function ( WP_Scripts $scripts ) {

	// Solo visitantes + opción activa
	if ( is_user_logged_in() || ! get_option( 'asap_deactivate_no_escencial', false ) ) {
		return;
	}

	// Ajustar deps de jQuery
	if ( isset( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			[ 'jquery-migrate' ]
		);
	}

	// Por si algún plugin lo llama directo
	$scripts->remove( 'jquery-migrate' );
}, 10 );

/*-------------------------------------------------
 * 2) Dequeue + deregister de lo demás, bien tarde
 *------------------------------------------------*/
add_action( 'wp_print_scripts', function () {

	if ( is_user_logged_in() || ! get_option( 'asap_deactivate_no_escencial', false ) ) {
		return;
	}

	$handles = [ 'wp-hooks-js', 'wp-i18n', 'hoverintent-js' ];

	foreach ( $handles as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}

}, 999 );


/*─────────────────────────────────────────────────────────────
 * Optimizar Contact Form 7 SOLO si la opción está activa
 *───────────────────────────────────────────────────────────*/

/**
 * Hook inicial: comprueba la opción y, si está ON,
 * instala los filtros/acciones que controlan CF7.
 */
add_action( 'init', 'asap_cf7_maybe_optimize_assets' );

function asap_cf7_maybe_optimize_assets() {

	if ( ! get_option( 'asap_deactivate_contactform', false ) ) {
		return;
	}

	if ( ! defined( 'WPCF7_VERSION' ) && ! class_exists( 'WPCF7' ) ) {
		return;
	}

	add_filter( 'wpcf7_load_js',  '__return_false', 5 );
	add_filter( 'wpcf7_load_css', '__return_false', 5 );

	add_action( 'wp_enqueue_scripts', 'asap_cf7_enqueue_assets_if_needed', 20 );
}

/**
 * Detecta si la página contiene un formulario CF7 y,
 * de ser así, vuelve a encolar CSS y JS.
 */
function asap_cf7_enqueue_assets_if_needed() {

	/* Evitamos admin, feeds, Ajax, Customizer */
	if ( is_admin() || wp_doing_ajax() || is_feed() || is_customize_preview() ) {
		return;
	}

	/* --- Detección dinámica (shortcode o bloque) --- */
	if ( is_singular() ) {

		$post_id = get_queried_object_id();
		$post    = $post_id ? get_post( $post_id ) : null;

		if ( ! $post ) {
			return;
		}

		$has_form =
			has_shortcode( $post->post_content, 'contact-form-7' ) ||
			( function_exists( 'has_block' )
			  && has_block( 'contact-form-7/contact-form-selector', $post ) );

		if ( $has_form ) {
			asap_cf7_enqueue_assets();
		}
	}

	/* --- Opción alternativa: página fija ‘contacto’ ---
	 * Descomentar para control manual:
	 *
	 * if ( is_page( 'contacto' ) ) {
	 *     asap_cf7_enqueue_assets();
	 * }
	 */
}

/**
 * Helper: encola CSS y JS originales de CF7.
 */
function asap_cf7_enqueue_assets() {

	if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
		wpcf7_enqueue_scripts();
	}
	if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
		wpcf7_enqueue_styles();
	}

	/* Opcional: marca el JS para ‘defer’ y no bloquear render */
	if ( function_exists( 'wp_script_add_data' ) ) {
		wp_script_add_data( 'contact-form-7', 'strategy', 'defer' );
	}
}


/* ------------------------------------------------------
 *  Reading Progress Bar – frontend
 * -----------------------------------------------------*/

/** Comprueba si debe mostrarse en esta vista */
function asap_progress_should_load() : bool {
	if ( ! get_option( 'asap_progress_enabled', '0' ) || ! is_singular() ) {
		return false;
	}
	$type = get_post_type();
	return '1' === get_option( "asap_progress_{$type}", '0' );
}

/** Mark‑up (usa wp_body_open para colocarlo lo más arriba posible) */
add_action( 'wp_body_open', function () {
	if ( ! asap_progress_should_load() ) return;

	echo '<div id="asap-reading-progress-bar"></div>';

	if ( '1' === get_option( 'asap_progress_show_percentage', '0' ) ) {
		echo '<span id="asap-reading-progress-percent">0%</span>';
	}
} );

/** Estilos inline */
add_action( 'wp_head', function () {
	if ( ! asap_progress_should_load() ) return;

	$height      = absint( get_option( 'asap_progress_height', 4 ) );
	$color       = esc_attr( get_option( 'asap_progress_color', '#1e73be' ) );
	$position    = get_option( 'asap_progress_position', 'top' ) === 'bottom' ? 'bottom:0;top:auto;' : 'top:0;';
	$hide_mobile = get_option( 'asap_progress_show_mobile', '1' ) === '0';
	?>
	<style>
		#asap-reading-progress-bar{
			position:fixed;left:0;<?php echo $position; ?>width:0;
			height:<?php echo $height; ?>px;background:<?php echo $color; ?>;
			z-index:9999;transition:width .15s ease-out;
		}
		#asap-reading-progress-percent{
			position:fixed;right:10px;<?php echo strpos( $position, 'bottom' ) !== false ? 'bottom:4px;' : 'top:4px;'; ?>
			font-size:12px;font-weight:600;color:<?php echo $color; ?>;z-index:10000;
		}
		<?php if ( $hide_mobile ) : ?>
		@media(max-width:767px){
			#asap-reading-progress-bar,#asap-reading-progress-percent{display:none;}
		}
		<?php endif; ?>
	</style>
	<?php
} );

/** Script inline */
add_action( 'wp_footer', function () {
	if ( ! asap_progress_should_load() ) return;
	?>
	<script>
	(function(){
		const content = document.querySelector('.the-content');
		if(!content) return;

		const bar  = document.getElementById('asap-reading-progress-bar');
		const pct  = document.getElementById('asap-reading-progress-percent');
		const win  = window;

		function update(){
			const docY  = win.scrollY || win.pageYOffset;
			const top   = content.offsetTop;
			const max   = content.offsetHeight - win.innerHeight;
			const perc  = Math.min(Math.max((docY - top) / max, 0), 1);

			bar.style.width = (perc * 100) + '%';
			if (pct) pct.textContent = Math.round(perc * 100) + '%';
		}

		update();
		win.addEventListener('scroll',  update, {passive:true});
		win.addEventListener('resize',  update);
	})();
	</script>
	<?php
} );



function asap_icon_svg( $base64 = true ) {

		$svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">  <image id="image0" width="512" height="512" x="0" y="0"
		    href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAMAAADDpiTIAAAABGdBTUEAALGPC/xhBQAAACBjSFJN
		AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAC/VBMVEUAAACmqq6nqq2nqayn
		qq2nqq2oqK2oq66oqq2hrq6AgICnqq2nqq2nq62mqaynqq2oq6+/v7+nqq2oqq2np6+mq62nqq2f
		n5+qqqqnqq2mq62kpKSnqa2qqqqmprOnqq2mqq6nqq2qqqqoqa2mrKyqqqqqqqqoq66nq62oqK6n
		qq2nq66oqa2nqq2nrKyqqqqnqq2mqq6pqa2nqa6nqq2oqa2nqq2nqq6oqqyoqq2oq66kra2nqa6o
		qqynqq2nqq2oqqyxsbGnqq2oqK6nqq2mqaynq6ynqa2np7Cnqqynqq2lrKynqq2qqqqnqq2nrKyn
		qq2nqqymqq6nq6umqq2nqq2nqq2lqaymprH///+pqa2mq62nqq2mqa2mq62nqq2nqq2nqq2nqa6q
		qqqnqq2mqq2nqq2mqq6mqq6nq6ymqayoqa2nqq6nqq2nq6uoqqynqq2nqq2nqq2mqaynqa2nq66n
		qq2nqq6nqq2ZmZmnqq2mqq6nqq2mqa2nqq2lqq+pqa2nqq2nq6ymqq2nqq2nqq2oq662tramqa2n
		qq2nqq2oqqymrKynqq2nq6ynq62np7Goqa2ioqKlqq+oqK6nqqynqq2mqq6mqq2nqq2oqK2nqq2l
		qayoqq2irq6nqq2oq66nq6unqq2mrKynqq2nq66oq66nqq2nqq2oqK+mqaynqa2mqq6nqq2jra2n
		qq6oq66nqq2nqq2kqrClpbSoq6unqa6nqq2mqaynqq2lra2oqqymq62nq62oqqyoqqyoqa2nq6yn
		qa2nqq2nq66oqK6nq6unqq2oqq2nqq2mqq6oq66zs7Oqqqqnqq2oqqynqa6nq66fr6+mqq6oqq2n
		qq2lqq+oqK2nqq6qqqqmqq6nqq2nqq2lra2nqq2nqq2qqqqmqa2nqq2nqq2nq62oqq2oqa2mqq6q
		qqqqqqqnqq2mqaynrKypqa2nqq2nqqymq62nqa6mqq6nq6ynqq2mq62oqq2nqq2nqq2nqq2nqq2m
		q62qqqqnqq3///9b8eCgAAAA/XRSTlMAP/dixd4yTLUTAtD9f1nsSQTczCBnnAgJ5HMOgBsU9Ejo
		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
		YII=" />
		</svg>';

		if ( $base64 ) {
			//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- This encoding is intended.
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		return $svg;
	}

	/**
	 * === ASAP | Optimización de Imágenes + WebP + Prefetch de enlaces ===
	 */

	// ---------- Helpers ----------
	if (!function_exists('asap_img_opt_is_enabled')) {
	    function asap_img_opt_is_enabled() {
	        return get_option('asap_image_opt_enable', '0') === '1';
	    }
	}
	if (!function_exists('asap_img_opt_quality')) {
	    function asap_img_opt_quality() {
	        $q = absint( get_option('asap_image_opt_quality', 82) );
	        return max(10, min(100, $q));
	    }
	}
	if (!function_exists('asap_img_opt_webp_enabled')) {
	    function asap_img_opt_webp_enabled() {
	        return get_option('asap_image_opt_webp', '0') === '1';
	    }
	}
	if (!function_exists('asap_can_generate_webp')) {
	    function asap_can_generate_webp() {
	        // GD
	        if (function_exists('imagewebp')) {
	            return true;
	        }
	        // Imagick
	        if (class_exists('Imagick')) {
	            try {
	                $formats = \Imagick::queryFormats('WEBP');
	                return $formats && in_array('WEBP', $formats, true);
	            } catch (\Throwable $e) { /* ignore */ }
	        }
	        return false;
	    }
	}

	/**
	 * Forzar calidad en ediciones/generación de tamaños (JPEG/WEBP/PNG*)
	 * Afecta a imágenes NUEVAS/generadas; no reescribe originales ya subidos.
	 */
	add_filter('wp_editor_set_quality', function($quality, $mime_type){
	    if (!asap_img_opt_is_enabled()) { return $quality; }
	    return asap_img_opt_quality();
	}, 10, 2);

	// Compatibilidad histórica (algunas rutas aún consultan este filtro)
	add_filter('jpeg_quality', function($q){ 
	    if (!asap_img_opt_is_enabled()) { return $q; }
	    return asap_img_opt_quality();
	});

	/**
	 * Convertir a WebP (copias paralelas) al generar metadata de adjuntos.
	 * - Crea .webp para original y para cada tamaño.
	 * - No cambia el MIME del adjunto; se sirve con <picture> (abajo).
	 */
	add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id){

	    if (!asap_img_opt_is_enabled() || !asap_img_opt_webp_enabled() || !asap_can_generate_webp()) {
	        return $metadata;
	    }

	    $upload = wp_upload_dir();
	    if (empty($metadata['file']) || empty($upload['basedir']) || empty($upload['baseurl'])) {
	        return $metadata;
	    }

	    $quality = asap_img_opt_quality();

	    // Ruta absoluta del archivo original subido (relativo en $metadata['file'])
	    $original_rel = $metadata['file'];
	    $original_abs = trailingslashit($upload['basedir']) . $original_rel;

	    asap_generate_single_webp($original_abs, $quality);

	    // Para cada tamaño intermedio
	    if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
	        $dir = trailingslashit( dirname($original_abs) );
	        foreach ($metadata['sizes'] as $size_key => $size_data) {
	            if (empty($size_data['file'])) { continue; }
	            $size_abs = $dir . $size_data['file'];
	            asap_generate_single_webp($size_abs, $quality);
	        }
	    }

	    // Flag meta opcional por si querés consultarlo
	    update_post_meta($attachment_id, '_asap_webp_generated', 1);

	    return $metadata;
	}, 10, 2);

	/**
	 * Genera un .webp a la par del archivo dado (si aún no existe).
	 * Soporta GD o Imagick. Devuelve ruta del .webp o false.
	 */
	if (!function_exists('asap_generate_single_webp')) {
	    function asap_generate_single_webp($src_abs_path, $quality) {
	        if (!file_exists($src_abs_path)) { return false; }

	        $ext = strtolower(pathinfo($src_abs_path, PATHINFO_EXTENSION));
	        if (!in_array($ext, ['jpg','jpeg','png','gif'], true)) {
	            return false; // si ya es webp u otro formato, salimos
	        }

	        $webp_abs = preg_replace('/\.[^.]+$/', '.webp', $src_abs_path);
	        if ($webp_abs === $src_abs_path) { return false; }
	        if (file_exists($webp_abs)) { return $webp_abs; }

	        // Intento Imagick primero
	        if (class_exists('Imagick')) {
	            try {
	                $img = new \Imagick($src_abs_path);
	                $img->setImageFormat('webp');
	                $img->setImageCompressionQuality($quality);
	                $img->writeImage($webp_abs);
	                $img->clear(); $img->destroy();
	                if (file_exists($webp_abs)) { return $webp_abs; }
	            } catch (\Throwable $e) { /* fallback a GD */ }
	        }

	        // Fallback GD
	        if (function_exists('imagewebp')) {
	            switch ($ext) {
	                case 'jpg':
	                case 'jpeg':
	                    $im = @imagecreatefromjpeg($src_abs_path);
	                    break;
	                case 'png':
	                    $im = @imagecreatefrompng($src_abs_path);
	                    // preservar transparencia
	                    if ($im) { imagepalettetotruecolor($im); imagealphablending($im, true); imagesavealpha($im, true); }
	                    break;
	                case 'gif':
	                    $im = @imagecreatefromgif($src_abs_path);
	                    break;
	                default:
	                    $im = null;
	            }
	            if ($im) {
	                @imagewebp($im, $webp_abs, $quality);
	                imagedestroy($im);
	                if (file_exists($webp_abs)) { return $webp_abs; }
	            }
	        }

	        return false;
	    }
	}

	/**
	 * Servir WebP cuando existe: envolvemos la <img> con <picture><source type="image/webp">.
	 * No tocamos el HTML si no hay .webp disponibles.
	 */
	add_filter('wp_get_attachment_image', function($html, $attachment_id, $size, $icon, $attr){
	    if (!asap_img_opt_is_enabled() || !asap_img_opt_webp_enabled()) {
	        return $html;
	    }

	    $webp_srcset = asap_build_webp_srcset($attachment_id, $size);
	    $webp_src    = asap_build_webp_src($attachment_id, $size);

	    if (!$webp_srcset && !$webp_src) {
	        return $html; // no hay webp generado
	    }

	    $sizes_attr = function_exists('wp_get_attachment_image_sizes')
	        ? wp_get_attachment_image_sizes($attachment_id, $size)
	        : '';

	    $out  = '<picture>';
	    if ($webp_srcset) {
	        $out .= '<source type="image/webp" srcset="' . esc_attr($webp_srcset) . '"'
	             .  ($sizes_attr ? ' sizes="' . esc_attr($sizes_attr) . '"' : '')
	             .  '>';
	    } elseif ($webp_src) {
	        $out .= '<source type="image/webp" srcset="' . esc_url($webp_src) . '">';
	    }
	    $out .= $html; // fallback <img> original
	    $out .= '</picture>';

	    return $out;
	}, 10, 5);

	if (!function_exists('asap_build_webp_src')) {
	    function asap_build_webp_src($attachment_id, $size) {
	        $src = wp_get_attachment_image_src($attachment_id, $size);
	        if (!$src || empty($src[0])) { return false; }
	        $url = $src[0];

	        $uploads = wp_upload_dir();
	        if (empty($uploads['baseurl']) || empty($uploads['basedir'])) { return false; }

	        $path = str_replace($uploads['baseurl'], $uploads['basedir'], $url);
	        $webp_path = preg_replace('/\.[^.]+$/', '.webp', $path);
	        if ($webp_path && file_exists($webp_path)) {
	            return str_replace($uploads['basedir'], $uploads['baseurl'], $webp_path);
	        }
	        return false;
	    }
	}
	if (!function_exists('asap_build_webp_srcset')) {
	    function asap_build_webp_srcset($attachment_id, $size) {
	        $srcset = wp_get_attachment_image_srcset($attachment_id, $size);
	        if (!$srcset) { return false; }

	        $uploads = wp_upload_dir();
	        if (empty($uploads['baseurl']) || empty($uploads['basedir'])) { return false; }

	        $out = [];
	        $candidates = array_map('trim', explode(',', $srcset));
	        foreach ($candidates as $c) {
	            if (!$c) continue;
	            // formato: URL [space] WIDTHw
	            if (!preg_match('~\s+(\d+)w$~', $c, $m)) {
	                continue;
	            }
	            $width = $m[1];
	            $url   = trim(str_replace($m[0], '', $c));

	            $path = str_replace($uploads['baseurl'], $uploads['basedir'], $url);
	            $webp_path = preg_replace('/\.[^.]+$/', '.webp', $path);
	            if ($webp_path && file_exists($webp_path)) {
	                $webp_url = str_replace($uploads['basedir'], $uploads['baseurl'], $webp_path);
	                $out[] = $webp_url . ' ' . $width . 'w';
	            }
	        }

	        return $out ? implode(', ', $out) : false;
	    }
	}

	/**
	 * Precarga de enlaces internos (prefetch al hover/touch)
	 */
	add_action('wp_footer', function(){
	    if (get_option('asap_link_prefetch', '0') !== '1') { return; }
	    ?>
	    <script>
	    (function(){
	        try {
	            // Soporte básico de prefetch
	            var linkEl = document.createElement('link');
	            if (!('relList' in linkEl) || !linkEl.relList.supports || !linkEl.relList.supports('prefetch')) return;
	        } catch(e) { return; }

	        var prefetched = new Set();

	        function isInternal(url) {
	            try { var u = new URL(url, location.href); return u.origin === location.origin; } catch(e){ return false; }
	        }

	        function prefetch(url) {
	            if (prefetched.has(url)) return;
	            var l = document.createElement('link');
	            l.rel = 'prefetch';
	            l.href = url;
	            l.as = 'document';
	            document.head.appendChild(l);
	            prefetched.add(url);
	        }

	        function handler(ev) {
	            var a = ev.target && ev.target.closest ? ev.target.closest('a[href]') : null;
	            if (!a) return;
	            var url = a.href;
	            if (!isInternal(url)) return;
	            if (a.target === '_blank') return;
	            if (a.hasAttribute('download')) return;

	            // Evitar anchors dentro de la misma página
	            try {
	                var u = new URL(url);
	                if (u.hash && u.pathname === location.pathname) return;
	            } catch(e){}

	            // Pequeño delay para no prefetchear por hover accidental
	            setTimeout(function(){ prefetch(url); }, 65);
	        }

	        document.addEventListener('mouseover', handler, {passive:true, capture:true});
	        document.addEventListener('touchstart', handler, {passive:true, capture:true});
	    })();
	    </script>
	    <?php
	}, 99);



// ===== Helpers (si ya existen en tu código, estos if evitarán duplicados) =====
if (!function_exists('asap_img_opt_is_enabled')) {
    function asap_img_opt_is_enabled() {
        return get_option('asap_image_opt_enable', '0') === '1';
    }
}
if (!function_exists('asap_img_opt_quality')) {
    function asap_img_opt_quality() {
        $q = absint( get_option('asap_image_opt_quality', 82) );
        return max(10, min(100, $q));
    }
}
if (!function_exists('asap_img_opt_max_w')) {
    function asap_img_opt_max_w() {
        return absint( get_option('asap_image_opt_max_w', 0) );
    }
}
if (!function_exists('asap_img_opt_max_h')) {
    function asap_img_opt_max_h() {
        return absint( get_option('asap_image_opt_max_h', 0) );
    }
}

// ===== Redimensionar original al subir/sideload (mantiene aspect ratio, solo si excede) =====
if (!function_exists('asap_downscale_original_on_handle')) {
    function asap_downscale_original_on_handle($file, $context = null) {
        if (!asap_img_opt_is_enabled()) { return $file; }
        if (empty($file['type']) || strpos($file['type'], 'image/') !== 0) { return $file; }
        $max_w = asap_img_opt_max_w();
        $max_h = asap_img_opt_max_h();
        if ($max_w <= 0 && $max_h <= 0) { return $file; }

        $path = isset($file['file']) ? $file['file'] : '';
        if (!$path || !file_exists($path)) { return $file; }

        $editor = wp_get_image_editor($path);
        if (is_wp_error($editor)) { return $file; }

        // Corrige orientación por EXIF si aplica.
        if (method_exists($editor, 'maybe_exif_rotate')) { $editor->maybe_exif_rotate(); }

        $size = $editor->get_size();
        $w = isset($size['width']) ? (int) $size['width'] : 0;
        $h = isset($size['height']) ? (int) $size['height'] : 0;

        $need_resize = ($max_w > 0 && $w > $max_w) || ($max_h > 0 && $h > $max_h);
        if (!$need_resize) { return $file; }

        // Redimensiona sin recortar (mantiene proporciones). No hace upscale.
        $target_w = ($max_w > 0) ? $max_w : null;
        $target_h = ($max_h > 0) ? $max_h : null;
        $res = $editor->resize($target_w, $target_h, false);
        if (!is_wp_error($res)) {
            if (method_exists($editor, 'set_quality')) { $editor->set_quality( asap_img_opt_quality() ); }
            $saved = $editor->save($path); // sobrescribe original
            if (!is_wp_error($saved) && !empty($saved['mime-type'])) {
                // Actualiza MIME por si cambió al re-guardar
                $file['type'] = $saved['mime-type'];
            }
        }

        return $file;
    }
}
add_filter('wp_handle_upload',   'asap_downscale_original_on_handle', 20, 2);
add_filter('wp_handle_sideload', 'asap_downscale_original_on_handle', 20, 2);

// ===== Alinear el umbral de "big image" de WP con tu ancho máximo (evita duplicados innecesarios) =====
add_filter('big_image_size_threshold', function($threshold, $imagesize, $file, $attachment_id) {
    if (!asap_img_opt_is_enabled()) { return $threshold; }
    $max_w = asap_img_opt_max_w();
    return ($max_w > 0) ? $max_w : $threshold;
}, 10, 4);

// ====== Helpers (evita duplicados si ya existen) ======
if (!function_exists('asap_img_opt_is_enabled')) {
    function asap_img_opt_is_enabled() { return get_option('asap_image_opt_enable', '0') === '1'; }
}
if (!function_exists('asap_img_opt_quality')) {
    function asap_img_opt_quality() {
        $q = absint( get_option('asap_image_opt_quality', 82) );
        return max(10, min(100, $q));
    }
}
if (!function_exists('asap_img_opt_strip_exif_enabled')) {
    function asap_img_opt_strip_exif_enabled() { return get_option('asap_image_opt_strip_exif', '0') === '1'; }
}

// Deducción simple de mime por extensión (para el post-proceso de tamaños)
if (!function_exists('asap_guess_mime_by_ext')) {
    function asap_guess_mime_by_ext($path) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg'], true)) return 'image/jpeg';
        if ($ext === 'png') return 'image/png';
        if ($ext === 'gif') return 'image/gif';
        if (in_array($ext, ['tif','tiff'], true)) return 'image/tiff';
        if ($ext === 'webp') return 'image/webp';
        return '';
    }
}

// Limpiador de metadatos: Imagick (ideal) o fallback GD
if (!function_exists('asap_strip_metadata_from_image')) {
    function asap_strip_metadata_from_image($abs_path, $mime = '') {
        if (!file_exists($abs_path)) return false;
        if (!$mime) { $mime = asap_guess_mime_by_ext($abs_path); }

        // No hace falta limpiar WebP (no suele traer EXIF) ni SVG (no procesamos acá)
        if (in_array($mime, ['image/webp','image/svg+xml'], true)) return true;

        // Preferir Imagick
        if (class_exists('Imagick')) {
            try {
                $img = new \Imagick($abs_path);
                // Aplicar strip y re-guardar con calidad cuando aplique
                $img->stripImage();
                if ($mime === 'image/jpeg') {
                    $img->setImageFormat('jpeg');
                    $img->setImageCompressionQuality( asap_img_opt_quality() );
                }
                $img->writeImage($abs_path);
                $img->clear(); $img->destroy();
                return file_exists($abs_path);
            } catch (\Throwable $e) {
                // continúa a GD
            }
        }

        // Fallback GD: re-encodea (suele eliminar EXIF)
        if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
            $im = @imagecreatefromjpeg($abs_path);
            if (!$im) return false;
            @imagejpeg($im, $abs_path, asap_img_opt_quality());
            imagedestroy($im);
            return file_exists($abs_path);
        }

        if ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
            $im = @imagecreatefrompng($abs_path);
            if (!$im) return false;
            // preservar alpha y comprimir nivel 6
            imagepalettetotruecolor($im);
            imagesavealpha($im, true);
            @imagepng($im, $abs_path, 6);
            imagedestroy($im);
            return file_exists($abs_path);
        }

        if ($mime === 'image/gif' && function_exists('imagecreatefromgif')) {
            $im = @imagecreatefromgif($abs_path);
            if (!$im) return false;
            @imagegif($im, $abs_path);
            imagedestroy($im);
            return file_exists($abs_path);
        }

        return false;
    }
}

/**
 * 1) Al subir/sideload: aplicar rotación EXIF (si corresponde) y luego limpiar metadatos.
 *    Prioridad > a cualquier redimensionado tuyo? Mejor después del downscale (si lo tenés en 20, usamos 22).
 */
if (!function_exists('asap_strip_exif_on_handle')) {
    function asap_strip_exif_on_handle($file, $context = null) {
        if (!asap_img_opt_is_enabled() || !asap_img_opt_strip_exif_enabled()) return $file;
        if (empty($file['type']) || strpos($file['type'], 'image/') !== 0) return $file;
        $path = isset($file['file']) ? $file['file'] : '';
        if (!$path || !file_exists($path)) return $file;

        // 1a) Asegurar orientación correcta antes de limpiar EXIF
        $editor = wp_get_image_editor($path);
        if (!is_wp_error($editor)) {
            if (method_exists($editor, 'maybe_exif_rotate')) { $editor->maybe_exif_rotate(); }
            if (method_exists($editor, 'set_quality')) { $editor->set_quality( asap_img_opt_quality() ); }
            $saved = $editor->save($path); // re‑encodea en el mismo path
            if (!is_wp_error($saved) && !empty($saved['mime-type'])) {
                $file['type'] = $saved['mime-type']; // ajustar mime si cambió
            }
        }

        // 1b) Strip de metadatos en el archivo resultante
        asap_strip_metadata_from_image($path, $file['type']);

        return $file;
    }
}
add_filter('wp_handle_upload',   'asap_strip_exif_on_handle', 22, 2);
add_filter('wp_handle_sideload', 'asap_strip_exif_on_handle', 22, 2);

/**
 * 2) Tras generar tamaños: limpiar metadatos del original y de cada tamaño.
 *    Corre después de que WP termine de crear los sizes.
 */
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id){
    if (!asap_img_opt_is_enabled() || !asap_img_opt_strip_exif_enabled()) return $metadata;

    $upload = wp_upload_dir();
    if (empty($metadata['file']) || empty($upload['basedir'])) return $metadata;

    // Original
    $original_abs = trailingslashit($upload['basedir']) . $metadata['file'];
    asap_strip_metadata_from_image($original_abs, asap_guess_mime_by_ext($original_abs));

    // Cada tamaño
    if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
        $dir = trailingslashit( dirname($original_abs) );
        foreach ($metadata['sizes'] as $size_data) {
            if (empty($size_data['file'])) continue;
            $size_abs = $dir . $size_data['file'];
            asap_strip_metadata_from_image($size_abs, asap_guess_mime_by_ext($size_abs));
        }
    }
    return $metadata;
}, 15, 2);

// Fallback por si tu tema usa current_category() y no existe
if ( ! function_exists('current_category') ) {
    function current_category($post_id = null) {
        $post_id = $post_id ?: get_the_ID();
        $cats = wp_get_post_categories($post_id);
        return $cats ? $cats : [];
    }
}

/**
 * Devuelve IDs de posts "siguientes" en anillo respecto del post actual.
 *
 * @param int   $current_post_id
 * @param array $base_tax_args   Filtros de taxonomía (category__in / tag__in) y post_type
 * @param int   $take            Cuántos posts queremos (N)
 * @param int   $offset          Desplazamiento opcional (p.ej. para no repetir con sidebar)
 * @param array $order_args      ['orderby' => 'date'|'ID', 'order' => 'DESC'|'ASC']
 * @return int[]                 IDs en el orden final que usaremos para mostrar
 */
function asap_get_circular_related_ids($current_post_id, $base_tax_args, $take, $offset = 0, $order_args = []) {
    $take   = max(1, (int) $take);
    $offset = max(0, (int) $offset);

    $orderby = isset($order_args['orderby']) ? $order_args['orderby'] : 'date';
    $order   = isset($order_args['order'])   ? $order_args['order']   : 'DESC';

    // Armamos una query sólo para obtener IDs del pool completo (mismo tipo y misma taxonomía)
    $args_all = wp_parse_args($base_tax_args, [
        'fields'                   => 'ids',
        'posts_per_page'          => -1,            // Si tu sitio es MUY grande, cambia por un límite y/o cachea
        'orderby'                 => $orderby,
        'order'                   => $order,
        'no_found_rows'           => true,
        'update_post_meta_cache'  => false,
        'update_post_term_cache'  => false,
        'suppress_filters'        => false,
    ]);

    $pool_q = new WP_Query($args_all);
    $all_ids = $pool_q->posts;

    if (empty($all_ids)) {
        return [];
    }

    // Ubicamos la posición del post actual
    $idx = array_search($current_post_id, $all_ids, true);

    // Si por algún motivo el post actual no está en el pool, devolvemos los primeros N distintos del actual
    if ($idx === false) {
        $filtered = array_values(array_diff($all_ids, [$current_post_id]));
        return array_slice($filtered, 0, min($take, count($filtered)));
    }

    // Punto de inicio: siguiente al actual + offset, con wrap-around
    $count = count($all_ids);
    if ($count === 1) {
        return []; // Sólo existe este post
    }

    $start = ($idx + 1 + $offset) % $count;

    $out = [];
    // Queremos N posts distintos del actual, pasando por el array con wrap-around
    for ($i = 0; $i < $count && count($out) < $take; $i++) {
        $pos = ($start + $i) % $count;
        if ((int) $all_ids[$pos] === (int) $current_post_id) {
            continue;
        }
        $out[] = (int) $all_ids[$pos];
    }

    return $out;
}
