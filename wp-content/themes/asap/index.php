<?php
/**
 * The main template file.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package AsapTheme
 */

get_header(); 

$enable_newspaper_design = get_theme_mod('asap_enable_newspaper_design', false);
$enable_newspaper_design_blog = get_theme_mod('asap_enable_newspaper_design_blog', true);

if ( $enable_newspaper_design ) {
    // Diseño newspaper habilitado
    if ( is_front_page() || ( $enable_newspaper_design_blog && is_home() ) ) {
        // Mostrar el diseño en la página principal y (si está habilitado) en la página de blog
        get_template_part('template-parts/home/home', 'design');
    } else {
        // En cualquier otro caso, mostrar el loop normal
        get_template_part('template-parts/home/home', 'loop');
    }
} else {
    // Diseño newspaper no habilitado
    get_template_part('template-parts/home/home', 'loop'); 
}


get_footer(); 

?>