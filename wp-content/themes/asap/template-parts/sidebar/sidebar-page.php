<?php

	asap_show_ads(3);

	if ( is_active_sidebar( 'page-before' ) ) :

	dynamic_sidebar( 'page-before' );

	endif;

	if ( get_theme_mod('asap_show_last_page')) :

	get_template_part('template-parts/loops/loop','sidebar');	

	endif;

	if ( is_active_sidebar( 'page' ) ) :

	dynamic_sidebar( 'page' );

	endif;

	asap_show_ads(4);

?>