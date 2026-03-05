<?php
/**
 * Child footer override to add legal links.
 */

?>

	<?php

	$disable_footer = asap_disable_footer();

	if ( ! $disable_footer ) {

		$enable_newspaper_design = get_theme_mod('asap_enable_newspaper_design', false);
		$enable_newspaper_design_blog = get_theme_mod('asap_enable_newspaper_design_blog', true);

	?>

	<?php if ( ! function_exists( 'is_woocommerce' ) ) : ?>


	<?php if (
		    is_home() && 
		    !is_front_page() && 
		    !get_theme_mod('asap_hide_breadcrumb_blog_page') && 
		    ( 
		        !$enable_newspaper_design || 
		        ( $enable_newspaper_design && !$enable_newspaper_design_blog ) 
		    )

		)
		 : 
	?>

	<div class="footer-breadcrumb">
				
		<?php asap_breadcrumbs(false); ?>
			
	</div>
		
	<?php endif; ?>

	<?php if ( is_category() && ! get_theme_mod('asap_hide_breadcrumb') ) : ?>
		
	<div class="footer-breadcrumb">
				
		<?php asap_breadcrumbs(false); ?>
			
	</div>
		
	<?php endif; ?>

	<?php if ( is_single() && ! get_theme_mod('asap_hide_breadcrumb') && ! get_post_meta( get_the_ID(), 'hide_breadcrumbs', true ) ) : ?>
		
	<div class="footer-breadcrumb">
				
		<?php asap_breadcrumbs(false); ?>
			
	</div>
		
	<?php endif; ?>

	<?php if ( is_page() && ! get_theme_mod('asap_hide_breadcrumb_page') && ! get_post_meta( get_the_ID(), 'hide_breadcrumbs', true )  ) : ?>
		
	<div class="footer-breadcrumb">
				
		<?php echo asap_breadcrumbs_pages( $post, false ); ?>
			
	</div>
		
	<?php endif; ?>

	<?php if ( ( is_single() || is_page() || is_category() ) && ( ! get_theme_mod('asap_hide_rise_button') ) ) : ?>
		
		<span class="go-top"><span><?php _e("Go up", "asap"); ?></span><i class="arrow arrow-up"></i></span>
		
	<?php endif; ?>

	<?php endif; ?>

	<?php if ( is_active_sidebar( 'social' ) ) : ?>

	<div class="content-footer-social">

		<?php dynamic_sidebar( 'social' );	?>
		
	</div>
		
	<?php endif; 	?>

	<?php 
		
	if (is_active_sidebar( 'widget-footer-1' )	|| 
		is_active_sidebar( 'widget-footer-2' )	||
		is_active_sidebar( 'widget-footer-3' )	|| 
		is_active_sidebar( 'widget-footer-4' )  ||
		is_active_sidebar( 'widget-footer-bottom')) 

		: ?>

	<footer>
	
		<div class="content-footer">

			<div class="widget-content-footer">
				
				<?php if ( ( has_custom_logo() ) && ( ! get_theme_mod('asap_hide_logo_footer') ) ) : ?>

				<div class="logo-footer"><?php the_custom_logo(); ?></div>

				<?php endif; ?>

				<?php if ( is_active_sidebar( 'widget-footer-1' ) ) : ?>
				
					<?php dynamic_sidebar( 'widget-footer-1' ); ?>
				
				<?php endif; ?>
				
				<?php if ( is_active_sidebar( 'widget-footer-2' ) ) : ?>
				
					<?php dynamic_sidebar( 'widget-footer-2' ); ?>
				
				<?php endif; ?>
				
				<?php if ( is_active_sidebar( 'widget-footer-3' ) ) : ?>
				
					<?php dynamic_sidebar( 'widget-footer-3' ); ?>
				
				<?php endif; ?>
				
				<?php if ( is_active_sidebar( 'widget-footer-4' ) ) : ?>
				
					<?php dynamic_sidebar( 'widget-footer-4' ); ?>
				
				<?php endif; ?>

			</div>

			<?php if ( is_active_sidebar( 'widget-footer-bottom' ) ) : ?>
				
				<div class="widget-content-footer-bottom">

				<?php dynamic_sidebar( 'widget-footer-bottom' ); ?>
				
				</div>

			<?php endif; ?>

			<div class="fichas-legal-links">
				<?php
				$aviso = get_page_by_path('aviso-legal');
				$priv = get_page_by_path('politica-de-privacidad');
				$cookies = get_page_by_path('politica-de-cookies');
				$links = array(
					$aviso ? get_permalink($aviso->ID) : '',
					$priv ? get_permalink($priv->ID) : '',
					$cookies ? get_permalink($cookies->ID) : '',
				);
				$labels = array('Aviso legal', 'Politica de privacidad', 'Politica de cookies');
				for ($i = 0; $i < count($links); $i++) {
					if (!$links[$i]) continue;
					echo '<a href="' . esc_url($links[$i]) . '">' . esc_html($labels[$i]) . '</a>';
				}
				?>
			</div>

		</div>

	</footer>

	<?php endif; ?>

	<?php } ?>

	<?php 

	if ( get_option('asap_show_cookies') ) : 

	$cookies_text 		= 	get_option('asap_cookies_text');
	$cookies_text_btn 	= 	get_option('asap_cookies_text_btn');
	$cookies_link 		= 	get_option('asap_cookies_link');
	$cookies_text_link 	= 	get_option('asap_cookies_text_link');

	?>

	<div id="cookiesbox" class="cookiesn">
	
	<p>
		<?php echo $cookies_text; ?>
		<a href="<?php echo get_the_permalink($cookies_link); ?>"><?php echo $cookies_text_link; ?></a>
	</p>
	<p>
		<button onclick="allowCookies()"><?php echo $cookies_text_btn; ?></button>			
	</p>
		
	</div>

	<?php endif; ?>

	<?php wp_footer(); ?>

  </body>
</html>
