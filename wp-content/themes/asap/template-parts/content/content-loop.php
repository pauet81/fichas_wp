<?php

$post_id 					= get_the_ID();

$post_type 					= get_post_type( $post_id );

$is_new 					= false;

$columns 					= get_query_var('columns'); 
$show_extract 				= get_query_var('show_extract'); 
$show_date 					= get_query_var('show_date_loop'); 
$show_category 				= get_query_var('show_category'); 
$text_show_more 			= get_query_var('text_show_more');
$deactivate_background 		= get_query_var('deactivate_background');
$loop_format 				= get_query_var('loop_format');
$show_advice_new_posts 		= get_query_var('show_advice_new_posts');
$show_stars	 				= get_query_var('show_stars');

$featured_post 				= get_post_meta( $post_id, 'featured_post', true );  
$single_featured_text		= get_post_meta( $post_id, 'single_bc_featured', true );  

$featured_text 				= $single_featured_text ?: get_query_var('featured_text');	

$asap_anchor_home 			= get_post_meta( $post_id, 'asap_anchor_home', true );

// En la página de búsqueda, usa 'asap_anchor_cluster' si 'asap_anchor_home' está vacío o es una página
if ( is_search() && ( empty($asap_anchor_home) || $post_type == 'page' ) ) {
    $asap_anchor_home = get_post_meta( $post_id, 'asap_anchor_cluster', true );
}


if ( isset($show_advice_new_posts) && $show_advice_new_posts ) {

	$message_new 				= get_query_var('message_new') ? : 'Nuevo';
	$days_new 					= get_query_var('days_new') ? : 7;
	$current_time				= get_query_var('current_time');

	$is_new = asap_is_new($days_new, $current_time);

}

if ( get_theme_mod('asap_design') ) :

?>

	<article class="article-loop asap-columns-<?php echo $columns; ?>">

		<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">

			<?php if ( has_post_thumbnail() ) : ?>

			<div class="article-content">

				<?php asap_show_stars($show_stars);	?>
			    
				<?php if ( $featured_post || $single_featured_text ) : ?>

				<span class="item-featured"><?php echo $featured_text; ?></span>

				<?php endif; ?>

				<?php if ( $is_new ) : ?>

				<span class="item-new"><?php echo $message_new; ?></span>

				<?php endif; ?>			

				<?php if ( $show_category && !is_category() ) : ?>

				<div class="content-item-category">

				    <?php 
				    
				    $primary_category = asap_get_category();

				    if ( $primary_category ) : ?>

				        <span><?php echo $primary_category->name; ?></span>

				    <?php endif; ?>

				</div>

				<?php endif; ?>

				<?php if ( ! $deactivate_background ) : ?>

				<div style="background-image: url('<?php echo asap_post_thumbnail(); ?>');" class="article-image"></div>

				<?php else : 

				the_post_thumbnail('post-thumbnail'); 

				endif; ?>

			</div>

			<?php endif; ?>

			<div class="asap-box-container">

			<?php if ( $show_date ) { ?><span class="asap-date-loop"><?php echo get_the_date('d/m/Y'); ?></span><?php } ?>				
			
			<?php

			$title = $asap_anchor_home ?: get_the_title();

			echo '<'.$loop_format.' class="entry-title">' . $title . '</'.$loop_format.'>';

			?>

			<?php if ($show_extract) : ?>

				<div class="show-extract">

				<?php the_excerpt(); ?>	

				<?php if ( $text_show_more ) : ?>

					<span class="asap-read-more"><?php echo esc_html( $text_show_more ); ?></span>

				<?php endif; ?>

				</div>

			<?php endif; ?>	

			</div>

		</a>

	</article>

<?php else : ?>

	<article class="article-loop asap-columns-<?php echo $columns; ?>">

		<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">

			<?php if ( has_post_thumbnail() ) : ?>

			<div class="article-content">

				<?php asap_show_stars($show_stars);	?>

				<?php if ( $featured_post || $single_featured_text ) : ?>

				<span class="item-featured"><?php echo $featured_text; ?></span>

				<?php endif; ?>

				<?php if ( $is_new ) : ?>

				<span class="item-new"><?php echo $message_new; ?></span>

				<?php endif; ?>	

				<?php if ( $show_category && !is_category() ) : ?>

				<div class="content-item-category">

				    <?php 
				    
				    $primary_category = asap_get_category();

				    if ( $primary_category ) : ?>
				        
				        <span><?php echo $primary_category->name; ?></span>

				    <?php endif; ?>

				</div>

				<?php endif; ?>

				<?php if ( ! $deactivate_background ) : ?>

				<div style="background-image: url('<?php echo asap_post_thumbnail(); ?>');" class="article-image"></div>

				<?php else : 

				the_post_thumbnail('post-thumbnail'); 

				endif; ?>


			</div>

			<?php endif; ?>

			<?php if ( $show_date ) { ?><span class="asap-date-loop"><?php echo get_the_date('d/m/Y'); ?></span><?php } ?>	
			
			<?php

			$title = $asap_anchor_home ?: get_the_title();

			echo '<'.$loop_format.' class="entry-title">' . $title . '</'.$loop_format.'>';

			?>

		</a>

		<?php if ($show_extract) : ?>

		<div class="show-extract">

			<?php the_excerpt(); ?>	

			<?php if ( $text_show_more ) : ?>

			<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $text_show_more; ?></a>

			<?php endif; ?>

		</div>

		<?php endif; ?>

	</article>

<?php endif; ?>