	<?php	
	
	if ( have_posts() ) :

		get_columns();

		$columns = intval( get_query_var('columns_featured') ) * intval ( get_query_var('rows_featured') ); 
		$count = 1;
		$enable_featured_posts = get_theme_mod('asap_enable_featured_posts');

		while ( have_posts() ) : the_post();

			if ( $count <= $columns && $enable_featured_posts && !get_theme_mod('asap_enable_newspaper_design') ) {
				get_template_part('template-parts/content/content', 'loop-featured');	
			} else {
				get_template_part('template-parts/content/content', 'loop');	
			}

			asap_show_ads_loop( $count );
			
			$count++;

		endwhile;

	else :

		get_template_part('template-parts/none/content', 'none');

	endif;

	$paginate = paginate_links( array(
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $wp_query->max_num_pages,
		'prev_text' => '«',
		'next_text' => '»',
	));	

	if ( $paginate ) : ?>

	<nav class="pagination"><?php echo $paginate; ?></nav>

	<?php endif; ?>
