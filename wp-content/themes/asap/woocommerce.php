<?php 
/**
 * The template for displaying WooCommerce settings
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package AsapTheme
 */

get_header(); 

// Recupero los tres flags, con default false
$show_prod  = get_theme_mod( 'asap_show_sidebar_products',      false );
$show_shop  = get_theme_mod( 'asap_show_sidebar_shop',          false );
$show_cats  = get_theme_mod( 'asap_show_sidebar_categories',    false );

// Si cumple cualquiera, usamos la clase "content-thin", sino "article-full"
if (
    ( $show_prod && is_product() ) 
 || ( $show_shop && is_shop() ) 
 || ( $show_cats && is_product_category() )
) {
    $class_content = 'content-thin';
} else {
    $class_content = 'article-full';
}

$disable_breadcrumbs = get_theme_mod('asap_wc_disable_breadcrumbs');

?>

<main class="content-wc">

	<?php if ( have_posts() ) : ?>
		
	<article class="<?php echo $class_content; ?>">
		
	<?php if ( ! $disable_breadcrumbs && ( is_product() || is_product_category() ) ) : woocommerce_breadcrumb(); endif; ?>
					
	<?php 
	woocommerce_content(); 
	?>
			
	</article>
	
	<?php else : ?>
	
	<?php get_template_part('template-parts/none/content', 'none'); ?>
	
	<?php endif; ?>
	
	<?php
    $show_prod  = get_theme_mod( 'asap_show_sidebar_products', false );
    $show_shop  = get_theme_mod( 'asap_show_sidebar_shop', false );
    $show_cats  = get_theme_mod( 'asap_show_sidebar_categories', false );

    if (
        ( $show_prod && is_product() )
     || ( $show_shop && is_shop() )
     || ( $show_cats && is_product_category() )
    ) {
        get_sidebar();
    }
	?>

</main>

<?php 

get_footer(); 

?>
