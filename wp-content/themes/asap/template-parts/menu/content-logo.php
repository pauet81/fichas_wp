<?php 

if ( get_theme_mod('asap_enable_newspaper_design') && get_theme_mod('asap_home_show_title_as_h1', true) && is_home() ) :

?>
	<?php if ( has_custom_logo() ) : ?>

	    <div class="site-logo">
	        <h1>
	            <?php
				    $custom_logo_id = get_theme_mod('custom_logo');
				    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');

				    if ($logo) {
				        $logo_url = esc_url($logo[0]);
				        $logo_width = esc_attr($logo[1]); // Ancho de la imagen
				        $logo_height = esc_attr($logo[2]); // Alto de la imagen

				        echo '<a href="' . esc_url(home_url('/')) . '" title="' . get_bloginfo('name') . '">';
				        echo '<img src="' . $logo_url . '" width="' . $logo_width . '" height="' . $logo_height . '" alt="' . get_bloginfo('name') . '">';
				        echo '</a>';
				    }
				?>
	            <span class="screen-reader-text"><?php bloginfo('name'); ?></span>
	        </h1>
	    </div>
		<?php else: ?>
		
	    <div class="site-name">
	        <a href="<?php echo esc_url(home_url('/')); ?>"><h1><?php bloginfo('name'); ?></h1></a>
	    </div>


	<?php endif; ?>

<?php else: ?>

	<?php if ( has_custom_logo() ) : ?>

		<div class="site-logo"><?php the_custom_logo(); ?></div>

		<?php else: ?>
		
		<div class="site-name">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		</div>

	<?php endif; ?>

<?php endif; ?>