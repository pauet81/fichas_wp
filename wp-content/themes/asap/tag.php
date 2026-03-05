<?php
/**
 * The template for displaying all tags.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package AsapTheme
 */

get_header(); 

$show_sidebar = get_theme_mod('asap_show_sidebar_tag');
$enable_featured_posts = get_theme_mod('asap_enable_featured_posts');
$enable_newspaper_design = get_theme_mod('asap_enable_newspaper_design', false);
$design_type = get_theme_mod('asap_home_design_type', 1);
$not_allowed_design_types = [2,9,11];
$is_design_allowed = !in_array($design_type, $not_allowed_design_types);

$post_ids_shown = [];
$excluded_posts = [];

if ($enable_newspaper_design && $is_design_allowed) { 
	$new_post_info = asap_get_new_post_info();
	$mods = asap_get_theme_mods(); // Obtener los mods
	$current_tag = get_queried_object();
	$tag_id = null;
    if ($current_tag && isset($current_tag->term_id)) {
        $tag_id = $current_tag->term_id;
    }
    $excluded_posts = asap_get_excluded_post_ids();
}

?>

<main class="content-loop">
	
	<?php echo asap_show_ads(5); ?>

	<?php get_template_part('template-parts/header/content', 'header'); ?>	
	
	<?php
	if ($enable_newspaper_design && !is_paged() && $is_design_allowed) {
	    asap_render_featured_posts(
	        $post_ids_shown,
            $new_post_info,
            $mods['show_category'],
            $mods['show_date_loop'],
            $mods['show_stars'],
            $mods['enable_cache'],
            $mods['cache_period'],             
            $mods,
            $excluded_posts,
	        null,
	        null,
	        $tag_id
	    );
	}
	?>

	<?php if ( $show_sidebar ) : ?>
	
	<section class="content-all">
		
	<section class="content-thin">
		
	<?php endif; ?>
	
	<section class="content-area">
		
		<?php if ( have_posts() ) : ?>
		
		<?php get_columns(); ?>
		
		<?php $columns = intval( get_query_var('columns_featured') ) * intval ( get_query_var('rows_featured') );  ?>
		
		<?php $count = 1; ?>
		
		<?php while ( have_posts() ) : the_post(); ?>

        <?php 
        if ( $enable_newspaper_design && in_array( get_the_ID(), $post_ids_shown ) && $is_design_allowed ) {
            continue; // Saltar posts que ya se han mostrado
        }
        ?>			
	
		<?php if ( $count <= $columns && $enable_featured_posts && !$enable_newspaper_design) { ?>
		
		<?php get_template_part('template-parts/content/content', 'loop-featured'); ?>
		
		<?php } else { ?>
			
		<?php get_template_part('template-parts/content/content', 'loop'); ?>
	
		<?php } ?>
		
		<?php asap_show_ads_loop( $count ); ?>
		
		<?php $count++; ?>		
	
		<?php endwhile; else : ?>
		
		<?php get_template_part('template-parts/none/content', 'none'); ?>
		
		<?php endif; ?>
		
		<?php 

		$paginate = paginate_links( array(
			'prev_text' => '«',
			'next_text' => '»',
		));	

		if ( $paginate ) : ?>

		<nav class="pagination"><?php echo $paginate; ?></nav>

		<?php endif; ?>
		
	</section>
		
	<?php if ( $show_sidebar ) : ?>
	
	</section>
		
	<?php get_sidebar(); ?>
		
	</section>
		
	<?php endif; ?>
	
</main>

<?php get_footer(); ?>