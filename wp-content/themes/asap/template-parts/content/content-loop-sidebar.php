<?php 

$is_new 					= false;

$deactivate_background 		= get_query_var('deactivate_background');
$show_advice_new_posts 		= get_query_var('show_advice_new_posts');
$show_stars	 				= get_query_var('show_stars');

$asap_anchor_side			= get_post_meta( get_the_ID(), 'asap_anchor_side', true ); 
$featured_post 				= get_post_meta( get_the_ID(), 'featured_post', true );  
$single_featured_text		= get_post_meta( get_the_ID(), 'single_bc_featured', true ); 

$featured_text 				= $single_featured_text ?: get_query_var('featured_text');	

if ( isset($show_advice_new_posts) && $show_advice_new_posts ) {

	$message_new 				= get_query_var('message_new') ? : 'Nuevo';
	$days_new 					= get_query_var('days_new') ? : 7;
	$current_time				= get_query_var('current_time');

	$is_new = asap_is_new($days_new, $current_time);

}

?>

<article class="article-loop asap-columns-1">
	
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
			
			<div style="background-image: url('<?php echo asap_side_thumbnail(); ?>');" class="article-image"></div>
			
			<?php else : 
			
			the_post_thumbnail('side-thumbnail'); 
			
			endif; ?>
			
		</div>
		
		<?php endif; ?>
			
		<?php
		
		$title = $asap_anchor_side ?: get_the_title();

		$title = esc_html( $title );
		
		echo '<p class="entry-title">' . $title . '</p>';
	
		?>						
			
	</a>
	
</article>