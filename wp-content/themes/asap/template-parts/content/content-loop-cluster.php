<?php 

$post_id 					= get_the_ID();

$is_new 					= false;

$show_cluster_extract 		= get_theme_mod('asap_show_cluster_extract');

$cluster_columns 			= get_query_var('cluster_columns', 3);
$text_show_more 			= get_query_var('text_show_more');
$deactivate_background 		= get_query_var('deactivate_background');
$loop_format 				= get_query_var('format', 'p');
$show_advice_new_posts 		= get_query_var('show_advice_new_posts');
$show_stars	 				= get_query_var('show_stars');

$asap_anchor_cluster		= get_post_meta( $post_id, 'asap_anchor_cluster', true );  
$featured_post 				= get_post_meta( $post_id, 'featured_post', true );  
$single_featured_text		= get_post_meta( $post_id, 'single_bc_featured', true );  

$featured_text 				= $single_featured_text ?: get_query_var('featured_text');	

if ( isset($show_advice_new_posts) && $show_advice_new_posts ) {

	$message_new 				= get_query_var('message_new') ? : 'Nuevo';
	$days_new 					= get_query_var('days_new') ? : 7;
	$current_time				= get_query_var('current_time');

	$is_new = asap_is_new($days_new, $current_time);

}

if ( get_theme_mod('asap_design') ) :

?>

	<article class="article-loop asap-columns-<?php echo $cluster_columns; ?>">

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

				<?php if ( ! $deactivate_background ) : ?>

				<div style="background-image: url('<?php echo asap_post_thumbnail(); ?>');" class="article-image"></div>

				<?php else : 

				the_post_thumbnail(); 

				endif; ?>

			</div>

			<?php endif; ?>

			<div class="asap-box-container">

			<?php

			$title = $asap_anchor_cluster ?: get_the_title();

			echo '<'.$loop_format.' class="entry-title">' . $title . '</'.$loop_format.'>';

			?>

			<?php if ( $show_cluster_extract ) : ?>

			<div class="show-extract">

				<?php the_excerpt(); ?>	

				<?php if ( $text_show_more ) : ?>

				<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $text_show_more ); ?></a>

				<?php endif; ?>

			</div>

			<?php endif; ?>
			
			</div>

		</a>

	</article>

<?php else : ?>

	<article class="article-loop asap-columns-<?php echo $cluster_columns; ?>">

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

				<?php if ( ! $deactivate_background ) : ?>

				<div style="background-image: url('<?php echo asap_post_thumbnail(); ?>');" class="article-image"></div>

				<?php else : 

				the_post_thumbnail(); 

				endif; ?>

			</div>

			<?php endif; ?>

			<?php

			$title = $asap_anchor_cluster ?: get_the_title();

			echo '<'.$loop_format.' class="entry-title">' . $title . '</'.$loop_format.'>';

			?>

		</a>

		<?php if ( $show_cluster_extract ) : ?>

		<div class="show-extract">

			<?php the_excerpt(); ?>	

			<?php if ( $text_show_more ) : ?>

			<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $text_show_more; ?></a>

			<?php endif; ?>

		</div>

		<?php endif; ?>

	</article>

<?php endif; ?>