<?php

$post_id 					= get_the_ID();

$is_new 					= false;

$columns 					= get_query_var('cluster_columns') ? : get_query_var('columns_featured');
$show_category 				= get_query_var('show_category'); 
$loop_format 				= get_query_var('loop_format');
$show_advice_new_posts 		= get_query_var('show_advice_new_posts');

$featured_post 				= get_post_meta( $post_id, 'featured_post', true );  
$single_featured_text		= get_post_meta( $post_id, 'single_bc_featured', true );  
$asap_anchor_home			= get_post_meta( $post_id, 'asap_anchor_home', true );  

$featured_text 				= $single_featured_text ?: get_query_var('featured_text');	

if ( isset($show_advice_new_posts) && $show_advice_new_posts ) {

	$message_new 				= get_query_var('message_new') ? : 'Nuevo';
	$days_new 					= get_query_var('days_new') ? : 7;
	$current_time				= get_query_var('current_time');

	$is_new = asap_is_new($days_new, $current_time);

}

?>

<article class="article-loop-featured asap-columns-<?php echo $columns; ?>">
	
	<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
				
		<div style="background-image: url('<?php echo asap_post_thumbnail(); ?>');" class="article-image-featured">

			<?php asap_show_stars($show_stars);	?>

			<?php if ( $featured_post || $single_featured_text ) : ?>

			<span class="item-featured"><?php echo $featured_text; ?></span>

			<?php endif; ?>

			<?php if ( $is_new ) : ?>

			<span class="item-new"><?php echo $message_new; ?></span>

			<?php endif; ?>	
			
			<?php

			$title = $asap_anchor_home ?: get_the_title();
				
			echo '<'.$loop_format.' class="entry-title">' . $title . '</'.$loop_format.'>';

			?>
				
		</div>			

	</a>
	
</article>