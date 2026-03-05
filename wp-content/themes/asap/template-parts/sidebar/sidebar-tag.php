<?php

	asap_show_ads(3);

	if ( is_active_sidebar( 'tag-before' ) ) :

	dynamic_sidebar( 'tag-before' );

	endif;

	if ( get_theme_mod('asap_show_last_tag')) :

	get_template_part('template-parts/loops/loop','sidebar');	

	endif;

	if ( is_active_sidebar( 'tag' ) ) :

	dynamic_sidebar( 'tag' );

	endif;

	asap_show_ads(4);

?>