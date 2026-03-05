	<?php 

	$asap_search_text = get_theme_mod('asap_search_text') ?: esc_html( __( "Search", "asap" ) );

	?>

	<?php if  ( get_theme_mod('asap_show_search')  ) : ?>

	<div class="search-header">
	    <form action="<?php echo home_url('/'); ?>" method="get">
	        <input autocomplete="off" id="search-header" placeholder="<?php echo esc_attr( wp_unslash( $asap_search_text ) ); ?>" value="<?php echo get_search_query() ?>" name="s" required>
	        <button class="s-btn" type="submit" aria-label="Buscar">
	            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
	                <circle cx="11" cy="11" r="8"></circle>
	                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
	            </svg>
	        </button>
	    </form>
	    <div id="autocomplete-results">
		    <ul id="results-list">
		    </ul>
		    <div id="view-all-results" style="display: none;">
		        <a href="#" id="view-all-link" class="view-all-button"><?php echo __('View all results', 'asap'); ?></a>
		    </div>
		</div>
	</div>

	<?php endif; ?>