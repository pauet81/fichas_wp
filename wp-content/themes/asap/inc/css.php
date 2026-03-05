<?php
	$loop_horizontal					= get_theme_mod('asap_loop_design');
	$design_box							= get_theme_mod('asap_design');
	$asap_background 					= $design_box ? '#F3F4F8' : '#FFFFFF';
	$asap_body_background     			= get_theme_mod('asap_body_background') ? : $asap_background;
	$asap_header_background   			= get_theme_mod('asap_header_background') ? : '#2471a3';
	$asap_top_header_background   		= get_theme_mod('asap_top_header_background') ? : '#2471a3';
	$asap_header_color        			= get_theme_mod('asap_header_color') ? : '#FFFFFF';
	$asap_footer_background   			= get_theme_mod('asap_footer_background') ? : '#2471a3';
	$asap_footer_color        			= get_theme_mod('asap_footer_color') ? : '#FFFFFF';
	$asap_btn_background      			= get_theme_mod('asap_btn_background') ? : '#2471a3';
	$asap_btn_color           			= get_theme_mod('asap_btn_color') ? : '#FFFFFF';
	$asap_font_color          			= get_theme_mod('asap_font_color') ? : '#181818';
	$asap_link_color         			= get_theme_mod('asap_link_color') ? : '#0183e4';
	$asap_featured_color      			= get_theme_mod('asap_featured_color') ? : '#FFFFFF';
	$asap_featured_background 			= get_theme_mod('asap_featured_background') ? : '#e88330';
	$asap_new_color      				= get_theme_mod('asap_new_color') ? : '#FFFFFF';
	$asap_new_background 				= get_theme_mod('asap_new_background') ? : '#e83030';
	$asap_width_wc            			= get_theme_mod('asap_width_wc') ? : '980';
	$asap_width_page          			= get_theme_mod('asap_width_page') ? : '980';
	$asap_search_header_width 			= get_theme_mod('asap_search_header_width') ? : '200';
	$asap_width_loop          			= get_theme_mod('asap_width_loop') ? : '980';
	$asap_width_single        			= get_theme_mod('asap_width_single') ? : '980';
	$asap_width_header       			= get_theme_mod('asap_width_header') ? : '980';
	$asap_size_h1             			= get_theme_mod('asap_size_h1') ? : '38';
	$asap_size_h2             			= get_theme_mod('asap_size_h2') ? : '32';
	$asap_size_h3             			= get_theme_mod('asap_size_h3') ? : '28';
	$asap_size_h4             			= get_theme_mod('asap_size_h4') ? : '23';
	$asap_size_text           			= get_theme_mod('asap_size_text') ? : '18';
	$asap_size_loop           			= get_theme_mod('asap_size_loop') ? : '18';
	$asap_size_loop_news   				= get_theme_mod('asap_size_loop_news') ? : '18';
	$asap_size_loop_news_featured    	= get_theme_mod('asap_size_loop_news_featured') ? : '25';
	$asap_width_logo          			= get_theme_mod('asap_width_logo') ? : '160';
	$asap_width_logo_footer     		= get_theme_mod('asap_width_logo_footer') ? : '160';
	$asap_search_margin       			= get_theme_mod('asap_search_margin') ? : '0';
	$asap_width_sidebar       			= get_theme_mod('asap_width_sidebar') ? : '300';
	$asap_h1_color            			= get_theme_mod('asap_h1_color') ? : '#181818';
	$asap_h2_color            			= get_theme_mod('asap_h2_color') ? : '#181818';
	$asap_h3_color           			= get_theme_mod('asap_h3_color') ? : '#181818';
	$asap_h4_color            			= get_theme_mod('asap_h4_color') ? : '#181818';
	$asap_index_list          			= get_theme_mod('asap_index_list') ? : '1';
	$borders_radius 					= get_theme_mod('asap_borders_radius') ? : '10';
	$deactivate_background 				= get_theme_mod('asap_deactivate_background');
	$rounded_borders 					= get_theme_mod('asap_rounded_borders');
	$enable_featured_posts 				= get_theme_mod('asap_enable_featured_posts');
	$asap_show_social_buttons_bottom 	= get_theme_mod('asap_show_social_buttons_bottom');
    $asap_social_post_types          	= get_theme_mod('asap_social_post_types');
	$options_fonts 						= get_option('asap_options_fonts', 2);
	$hero_background 					= get_theme_mod('asap_hero_background') ? : $asap_header_background;
	$hero_text 							= get_theme_mod('asap_hero_text') ? : $asap_header_color;
	$asap_index_design					= get_theme_mod('asap_index_design');
	$toc_sticky							= get_theme_mod('asap_toc_sticky');
	$dropdown_right						= get_theme_mod('asap_dropdown_right');
	$asap_header_top					= get_theme_mod('asap_header_top');
	$asap_search_header_left			= get_theme_mod('asap_search_header_left');
	$float_menu 						= get_theme_mod('asap_float_menu');
	$enable_transitions 				= get_theme_mod('asap_enable_transitions', true);
	$columns_featured 					= intval(get_theme_mod('asap_columns_featured', 3));
	$rows_featured 						= intval(get_theme_mod('asap_rows_featured', 1));
	$last_first_row 					= $columns_featured;
	$first_last_row 					= ($rows_featured - 1) * $columns_featured + 1;
	$last_last_row 						= $columns_featured * $rows_featured;
	$search_background					= get_theme_mod('asap_search_background', '#FFFFFF');
	$search_color 						= get_theme_mod('asap_search_color', '#484848');
	$asap_size_h1_featured     			= get_theme_mod('asap_size_h1_featured') ? : '32';
	$asap_link_as_btn_bg    			= get_theme_mod('asap_link_as_btn_bg') ? : '#206592';
	$asap_link_as_btn_bg_hover         	= get_theme_mod('asap_link_as_btn_bg_hover') ? : '#1c5a82';	
	$asap_btn_color_no_hash    			= ltrim($asap_btn_color, '#');
	$design_type 						= get_theme_mod('asap_home_design_type', 1);
	$columns 							= intval(get_theme_mod('asap_columns', 3));

	$header_design = '';

	$hero = is_single() 
		? get_theme_mod('asap_hero_post', 'normal')
		: ( is_page() 
			? get_theme_mod('asap_hero_page', 'normal')
			: ( is_category() 
				? get_theme_mod('asap_hero_cat', 'normal') 
				: '' ) );

	if ( is_single() || is_page() ) 
	{
		$header_design = get_post_meta(get_the_ID(), 'asap_header_design', true) ?: $hero;
	} elseif ( is_category() ) {
		$header_design = get_term_meta(get_queried_object()->term_id, 'asap_header_design', true) ?: $hero;
	}

	if ($options_fonts == 3) 
	{
		$asap_text_font 				= 'Maven Pro';
		$asap_text_weight 				= '400';	
		$asap_title_font 				= 'Maven Pro';
		$asap_title_weight 				= '600';
		$asap_loop_font 				= 'Maven Pro';
		$asap_loop_weight 				= '400';
		$asap_loop_news_font 			= 'Maven Pro';
		$asap_loop_news_weight 			= '400';	
		$asap_loop_news_featured_font 	= 'Maven Pro';
		$asap_loop_news_featured_weight	= '600';			
	} 
	else 
	{
		$text 							= explode('.', get_theme_mod('asap_font_text', 'Poppins.300'));
		$asap_text_font 				= $text[0];
		$asap_text_weight 				= $text[1];

		$head 							= explode('.', get_theme_mod('asap_font_title', 'Poppins.400'));
		$asap_title_font 				= $head[0];
		$asap_title_weight 				= $head[1];

		$loop 							= explode('.', get_theme_mod('asap_font_loop', 'Poppins.300'));
		$asap_loop_font 				= $loop[0];
		$asap_loop_weight 				= $loop[1];

		$loop_news						= explode('.', get_theme_mod('asap_font_loop_news', 'Poppins.300'));
		$asap_loop_news_font 			= $loop_news[0];
		$asap_loop_news_weight 			= $loop_news[1];		

		$loop_news_featured				= explode('.', get_theme_mod('asap_font_loop_news_featured', 'Poppins.700'));
		$asap_loop_news_featured_font 	= $loop_news_featured[0];
		$asap_loop_news_featured_weight	= $loop_news_featured[1];		
	}

	$custom_css = "";

	/*
	 * General data
	 */
	$custom_css .= "
	body {
		font-family: '{$asap_text_font}', sans-serif !important;	
		background: {$asap_body_background};
		font-weight: {$asap_text_weight} !important;
	}

	h1,h2,h3,h4,h5,h6 {
		font-family: '{$asap_title_font}', sans-serif !important;			
		font-weight: {$asap_title_weight};
		line-height: 1.3;
	}
		
	h1 {
		color:{$asap_h1_color}
	}
		
	h2,h5,h6	{
		color:{$asap_h2_color}
	}
	
	h3	{
		color:{$asap_h3_color}
	}
	
	h4	{
		color:{$asap_h4_color}
	}

	.home-categories .article-loop:hover h3,
	.home-categories .article-loop:hover p
	 {
		color:{$asap_h4_color} !important;
	}

	.grid-container .grid-item h2 {
		font-family: '{$asap_loop_news_featured_font}', sans-serif !important;			
		font-weight: {$asap_loop_news_featured_weight};
		font-size: {$asap_size_loop_news_featured}px !important;
		line-height: 1.3;		
	}

	.design-3 .grid-container .grid-item h2,
	.design-3 .grid-container .grid-item h2 {
		font-size: {$asap_size_loop_news_featured}px !important;	
	}

	.home-categories h2 {
		font-family: '{$asap_loop_news_featured_font}', sans-serif !important;			
		font-weight: {$asap_loop_news_featured_weight};
		font-size: calc({$asap_size_loop_news_featured}px - 4px) !important;
		line-height: 1.3;			
	}

	.home-categories .featured-post h3 {
		font-family: '{$asap_loop_news_featured_font}', sans-serif !important;			
		font-weight: {$asap_loop_news_featured_weight} !important;
		font-size: {$asap_size_loop_news_featured}px !important;
		line-height: 1.3;		
	}


	.home-categories .article-loop h3,
	.home-categories .regular-post h3
	 {
		font-family: '{$asap_loop_news_font}', sans-serif !important;			
		font-weight: {$asap_loop_news_weight} !important;
	}

	.home-categories .regular-post h3 {
		font-size: calc({$asap_size_text}px - 1px) !important;
	}

	.home-categories .article-loop h3,
	.design-3 .home-categories .regular-post h3 {
		font-size: {$asap_size_loop_news}px !important;
	}
	
	.article-loop p,
	.article-loop h2,
	.article-loop h3,
	.article-loop h4,
	.article-loop span.entry-title, 		
	.related-posts p,
	.last-post-sidebar p,
	.woocommerce-loop-product__title {
		font-family: '{$asap_loop_font}', sans-serif !important;							
		font-size: {$asap_size_loop}px !important;
		font-weight: {$asap_loop_weight} !important;		
	}

	.article-loop-featured p,
	.article-loop-featured h2,
	.article-loop-featured h3 {
		font-family: '{$asap_loop_font}', sans-serif !important;							
		font-size: {$asap_size_loop}px !important;
		font-weight: bold !important;	
	}

	.article-loop .show-extract p,
	.article-loop .show-extract span {
		font-family: '{$asap_text_font}', sans-serif !important;								
		font-weight: {$asap_text_weight} !important;
	}

	.home-categories .content-area .show-extract p {
		font-size: calc({$asap_size_text}px - 2px) !important;		
	}
		
	a {
		color: {$asap_link_color};
	}
	
	.the-content .post-index span,
	.des-category .post-index span {
	  font-size:{$asap_size_text}px;
	}

	.the-content .post-index li,
	.the-content .post-index a,
	.des-category .post-index li,
	.des-category .post-index a,
	.comment-respond > p > span > a,
	.asap-pros-cons-title span,
	.asap-pros-cons ul li span,
	.woocommerce #reviews #comments ol.commentlist li .comment-text p,
	.woocommerce #review_form #respond p,
	.woocommerce .comment-reply-title,
	.woocommerce form .form-row label, .woocommerce-page form .form-row label {
	  font-size: calc({$asap_size_text}px - 2px);
	}

	.content-tags a,
	.tagcloud a {
		border:1px solid {$asap_link_color};
	}

	.content-tags a:hover,
	.tagcloud a:hover {
		color: {$asap_link_color}99;
	}

	p,
	.the-content ul li,
	.the-content ol li,
	.content-wc ul li
	.content-wc ol li
	 {
		color: {$asap_font_color};
		font-size: {$asap_size_text}px;
		line-height: 1.6;
	}

	.comment-author cite,
	.primary-sidebar ul li a,
	.woocommerce ul.products li.product .price,
	span.asap-author,
	.content-cluster .show-extract span,
	.home-categories h2 a {
		color: {$asap_font_color};
	}

	.comment-body p,
	#commentform input,
	#commentform textarea
	{
		font-size: calc({$asap_size_text}px - 2px);
	}
		

	.social-title,
	.primary-sidebar ul li a {
		font-size: calc({$asap_size_text}px - 3px);		
	}

	.breadcrumb a,
	.breadcrumb span,
	.woocommerce .woocommerce-breadcrumb {
		font-size: calc({$asap_size_text}px - 5px);
	}
		
	.content-footer p,
	.content-footer li,
	.content-footer .widget-bottom-area,
	.search-header input:not([type=submit]):not([type=radio]):not([type=checkbox]):not([type=file]) {
		font-size: calc({$asap_size_text}px - 4px) !important;
	}
	
	.search-header input:not([type=submit]):not([type=radio]):not([type=checkbox]):not([type=file]) {
		border:1px solid {$asap_header_color}26 !important;
	}
		
	h1 {
		font-size: {$asap_size_h1}px;
	}

	.archive .content-loop h1 {
		font-size: calc({$asap_size_h1}px - 2px);		
	}

	.asap-hero h1 {
		font-size: {$asap_size_h1_featured}px;
	}

	h2 {
		font-size: {$asap_size_h2}px;
	}

	h3 {
		font-size: {$asap_size_h3}px;
	}

	h4 {
		font-size: {$asap_size_h4}px;
	}

	.site-header,
	#cookiesbox {
		background: {$asap_header_background};
	}

	.site-header-wc a span.count-number {
		border:1px solid {$asap_header_color};

	}
	
	.content-footer {
		background: {$asap_footer_background};		
	}
		
	.comment-respond > p,
	.area-comentarios ol > p,
	.error404 .content-loop p + p,
	.search .content-loop .search-home + p
 	{
		border-bottom:1px solid {$asap_btn_background}
	}

	.home-categories h2:after,
	.toc-rapida__item.is-active::before {
	  background: {$asap_btn_background}
	}

	.pagination a,
	.nav-links a,
	.woocommerce #respond input#submit,
	.woocommerce a.button, 
	.woocommerce button.button,
	.woocommerce input.button,
	.woocommerce #respond input#submit.alt,
	.woocommerce a.button.alt,
	.woocommerce button.button.alt,
	.woocommerce input.button.alt,
	.wpcf7-form input.wpcf7-submit,
	.woocommerce-pagination .page-numbers a,
	.woocommerce-pagination .page-numbers span {
		background: {$asap_btn_background};
		color: {$asap_btn_color} !important;
	}

	.woocommerce div.product .woocommerce-tabs ul.tabs li.active {
		border-bottom: 2px solid {$asap_btn_background};
	}
		
	.pagination a:hover,
	.nav-links a:hover,
	.woocommerce-pagination .page-numbers a:hover,
	.woocommerce-pagination .page-numbers span:hover {
		background: {$asap_btn_background}B3;			
	}		
	
	.woocommerce-pagination .page-numbers .current {
		background: {$asap_body_background};
		color: {$asap_font_color} !important;
	}

	.article-loop a span.entry-title
	{
		color:{$asap_font_color} !important;
	}

	.article-loop a:hover p,
	.article-loop a:hover h2,
	.article-loop a:hover h3,
	.article-loop a:hover span.entry-title,
	.home-categories-h2 h2 a:hover {
		color: {$asap_link_color} !important;
	}


	.article-loop.custom-links a:hover span.entry-title,
	.asap-loop-horizontal .article-loop a:hover span.entry-title {
		color: {$asap_font_color} !important;
	}
		
	#commentform input,
	#commentform textarea {
		border: 2px solid {$asap_btn_background};
		font-weight: {$asap_text_weight} !important;
	}

	.content-loop,
	.content-loop-design {
		max-width: {$asap_width_loop}px;
	}

	.site-header-content,
	.site-header-content-top {
		max-width: {$asap_width_header}px;
	}
		
	.content-footer {
		max-width: calc({$asap_width_header}px - 32px); 			
	}
		
	.content-footer-social {
		background: {$asap_footer_background}1A;
	}

	.content-single {
		max-width: {$asap_width_single}px;
	}
		
	.content-page {
		max-width: {$asap_width_page}px;
	}
	
	.content-wc {
		max-width: {$asap_width_wc}px;
	}

	.reply a,
	.go-top {
		background: {$asap_btn_background};
		color: {$asap_btn_color};
	}

	.reply a {
		border: 2px solid {$asap_btn_background};
	}

	#commentform input[type=submit] {
		background: {$asap_btn_background};
		color: {$asap_btn_color};
	}

	.site-header a,
	header,
	header label,
	.site-name h1
	 {
		color: {$asap_header_color};
	}
	
	.content-footer a,
	.content-footer p,
	.content-footer .widget-area,
	.content-footer .widget-content-footer-bottom {
		color: {$asap_footer_color};		
	}
		
	header .line {
		background: {$asap_header_color};
	}

	.site-logo img {
		max-width: {$asap_width_logo}px;
		width:100%;
	}

	.content-footer .logo-footer img {
		max-width: {$asap_width_logo_footer}px;
	}

	.search-header {
		margin-left: {$asap_search_margin}px;
	}
		
	.primary-sidebar {
		width:{$asap_width_sidebar}px;
	}
		
	p.sidebar-title {
		font-size:calc({$asap_size_text}px + 1px);
	}

	.comment-respond > p,
	.area-comentarios ol > p,
	.asap-subtitle,
	.asap-subtitle p {
		font-size:calc({$asap_size_text}px + 2px);
	}	
		
	.popular-post-sidebar ol a {
		color:{$asap_font_color};
		font-size:calc({$asap_size_text}px - 2px);
	}
		
	.popular-post-sidebar ol li:before,
	.primary-sidebar div ul li:before {
		border-color: {$asap_btn_background};
	}
		
	.search-form input[type=submit] {
		background:{$asap_header_background};
	}
		
	.search-form {
		border:2px solid {$asap_btn_background};
	}
		
	.sidebar-title:after {
		background:{$asap_btn_background};
	}	

	.single-nav .nav-prev a:before, 
	.single-nav .nav-next a:before {
		border-color:{$asap_btn_background};
	}

	.single-nav a {
		color:{$asap_font_color};
		font-size:calc({$asap_size_text}px - 3px);	
	}
		
	.the-content .post-index {
		border-top:2px solid {$asap_btn_background};
	}
		
	.the-content .post-index #show-table {
		color:{$asap_link_color};
		font-size: calc({$asap_size_text}px - 3px);
		font-weight: {$asap_text_weight};
	}

	.the-content .post-index .btn-show {
		font-size: calc({$asap_size_text}px - 3px) !important;
	}
		
	.search-header form {
	  width:{$asap_search_header_width}px;
	}	

	.site-header .site-header-wc svg {
		stroke:{$asap_header_color};
	}

	.item-featured {
		color:{$asap_featured_color};
		background:{$asap_featured_background};
	}

	.item-new {
		color:{$asap_new_color};
		background:{$asap_new_background};
	}

	.asap-style1.asap-popular ol li:before {
		border:1px solid {$asap_font_color};
	}
	
	.asap-style2.asap-popular ol li:before {
		border:2px solid {$asap_btn_background};
	}

	.category-filters a.checked .checkbox {
	    background-color: {$asap_btn_background}; 
	    border-color: {$asap_btn_background}; 
       background-image: url('data:image/svg+xml;charset=UTF-8,<svg viewBox=\"0 0 16 16\" fill=\"%23{$asap_btn_color_no_hash}\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M5.5 11.793l-3.646-3.647.708-.707L5.5 10.379l6.646-6.647.708.707-7.354 7.354z\"/></svg>');
	}

	.category-filters a:hover .checkbox {
	    border-color: {$asap_btn_background};
	}

	.design-2 .content-area.latest-post-container,
	.design-3 .regular-posts {
		grid-template-columns: repeat({$columns}, 1fr);
	}

	";

	

	$custom_css .= "
	.checkbox .check-table svg 
	{
		stroke:{$asap_btn_background};
	}
	";


	/*
	 * Enable scroll smoth
	 */
	if ( get_theme_mod('asap_scroll_smooth') ) :

	$custom_css .= "
		html{scroll-behavior:smooth;}
	";
		
	endif;


	/*
	 * Margin Featured Posts
	 */
	if ( $enable_featured_posts ) : 

		$custom_css .= "

		.content-area .article-loop-featured:nth-child({$last_last_row}),
		.content-cluster .article-loop-featured:nth-child({$last_last_row})  
		{
			margin-bottom:3rem;
		}

		";

	endif;


	/*
	 * Enable border radius
	 */
	if ( $rounded_borders || $design_box ) : 

	$custom_css .= "	
		.article-content,
		#commentform input, 
		#commentform textarea,
		.reply a,
		.woocommerce #respond input#submit, 
		.woocommerce #respond input#submit.alt,
		.woocommerce-address-fields__field-wrapper input,
		.woocommerce-EditAccountForm input,
		.wpcf7-form input,
		.wpcf7-form textarea,
		.wpcf7-form input.wpcf7-submit,
		.grid-container .grid-item,
		.design-1 .featured-post-img,
		.design-1 .regular-post-img,
		.design-1 .lastest-post-img,
		.design-2 .featured-post-img,
		.design-2 .regular-post-img,
		.design-2 .lastest-post-img,
		.design-2 .grid-item,
		.design-2 .grid-item .grid-image-container,
		.design-2 .regular-post,
		.home.design-2.asap-box-design .article-loop,
		.design-3 .featured-post-img,
		.design-3 .regular-post-img,
		.design-3 .lastest-post-img {
		  border-radius: {$borders_radius}px !important;
		}


		.pagination a, .pagination span, .nav-links a {
			border-radius:50%;
			min-width:2.5rem;
		}
		
		.reply a {
			padding:6px 8px !important;
		}
		.asap-icon,
		.asap-icon-single {
			border-radius:50%;
		}
		.asap-icon{
			margin-right:1px;
			padding:.6rem !important;
		}
		.content-footer-social {
			border-top-left-radius:{$borders_radius}px;
			border-top-right-radius:{$borders_radius}px;
		}
		
		.item-featured,
		.item-new,
		.average-rating-loop {
			border-radius:2px;
		}

		.content-item-category > span {
		    border-top-left-radius: 3px;
		    border-top-right-radius: 3px;
		}

		.woocommerce span.onsale,
		.woocommerce a.button,
		.woocommerce button.button, 
		.woocommerce input.button, 
		.woocommerce a.button.alt, 
		.woocommerce button.button.alt, 
		.woocommerce input.button.alt,
		.product-gallery-summary .quantity input,
		#add_payment_method table.cart input,
		.woocommerce-cart table.cart input, 
		.woocommerce-checkout table.cart input,
		.woocommerce div.product form.cart .variations select {
			border-radius:2rem !important;
		}
			

		.search-home input {
				border-radius:2rem !important;
			    padding: 0.875rem 1.25rem !important;
				
		}
			
		.search-home button.s-btn {
				margin-right:1.25rem !important;
		}


		#cookiesbox p,
		#cookiesbox a {
			color: {$asap_header_color};	
		}

		#cookiesbox button {
			background: {$asap_header_color};
			color:{$asap_header_background};
		}
			
			
		@media (max-width: 1050px) and (min-width:481px) {

			.article-loop-featured .article-image-featured
			 {
				border-radius: {$borders_radius}px !important;

			}

		}
		

		@media (min-width:1050px) { 

			#autocomplete-results {
			border-radius:3px;
		}

			ul.sub-menu,
			ul.sub-menu li {
				border-radius:{$borders_radius}px;

			}
			
			.search-header input {
				border-radius:2rem !important;
				padding: 0 3 0 .85rem !important;

			}
			

			.search-header button.s-btn {
				width:2.65rem !important;
			}

			.site-header .asap-icon svg {
				   stroke: {$asap_header_color} !important;
			}
		
			";

			/*
			 *  Featured Posts
			 */

			if ( ! get_theme_mod('asap_show_search_index') ) : 
	
				$custom_css .= "

				.home .content-loop {
						    padding-top: 1.75rem;
						  }
				";

			endif;

			if ( $enable_featured_posts && $rounded_borders ) : 

				$custom_css .= "

				.article-loop-featured:first-child .article-image-featured {
					border-top-left-radius:{$borders_radius}px !important;
				}
				
				.article-loop-featured:nth-child({$first_last_row}) .article-image-featured {
					border-bottom-left-radius:{$borders_radius}px !important;
				}
				
				.article-loop-featured:nth-child({$last_first_row}) .article-image-featured {
					border-top-right-radius:{$borders_radius}px !important;
				}
				
				.article-loop-featured:nth-child({$last_last_row}) .article-image-featured {
					border-bottom-right-radius:{$borders_radius}px !important;
				}
				
				
				";

			endif;


			if ( $enable_featured_posts ) : 

				$custom_css .= "

				.primary-sidebar .article-image-featured {
					border-radius:{$borders_radius}px !important;
				}

				";

			endif;

		$custom_css .= "

		}

		";

		if ( ( ! is_active_sidebar( 'social' ) ) &&  ( $rounded_borders ) ) : 


			$custom_css .= "

			@media (min-width:1050px) {
				.content-footer {
					border-top-left-radius:{$borders_radius}px;
					border-top-right-radius:{$borders_radius}px;
				}
			}

			";

		endif;

	
	endif;

	

	if ( ! $deactivate_background ) : 


		$custom_css .= "
		.article-content {
			height:196px;
		}
		.content-thin .content-cluster .article-content {
			height:160px !important;
		}
		.last-post-sidebar .article-content {
		    height: 140px;
		    margin-bottom: 8px
		}
		.related-posts .article-content {
		    height: 120px;
		}
		.asap-box-design .related-posts .article-content {
			min-height:120px !important;
		}
		.asap-box-design .content-thin .content-cluster .article-content {
			heigth:160px !important;
			min-height:160px !important;
		}

		@media (max-width:1050px) { 
			.last-post-sidebar .article-content,
		    .related-posts .article-content {
		        height: 150px !important
		    }
		}
		@media (max-width: 480px) {
			.article-content {
		        height: 180px
		    }
		}
		
		@media (min-width:480px)  {
			.asap-box-design .article-content:not(.asap-box-design .last-post-sidebar .article-content) {
				min-height:196px;
			}

			.asap-loop-horizontal .content-thin .asap-columns-1 .content-cluster .article-image,
			.asap-loop-horizontal .content-thin .asap-columns-1 .content-cluster .article-content {
				height:100% !important;
			}	

			.asap-loop-horizontal .asap-columns-1 .article-image:not(.asap-loop-horizontal .last-post-sidebar .asap-columns-1 .article-image), 
			.asap-loop-horizontal .asap-columns-1 .article-content:not(.asap-loop-horizontal .last-post-sidebar .asap-columns-1 .article-content) {
				height:100% !important;
			}
			
			.asap-loop-horizontal .asap-columns-2 .article-image,
			.asap-loop-horizontal .asap-columns-2 .article-content,
			.asap-loop-horizontal .content-thin .asap-columns-2 .content-cluster .article-image,
			.asap-loop-horizontal .content-thin .asap-columns-2 .content-cluster .article-content {
				min-height:140px !important;
				height:100% !important;
			}

			.asap-loop-horizontal .asap-columns-3 .article-image,
			.asap-loop-horizontal .asap-columns-3 .article-content,
			.asap-loop-horizontal .content-thin .asap-columns-3 .content-cluster .article-image,
			.asap-loop-horizontal .content-thin .asap-columns-3 .content-cluster .article-content {
				min-height:120px !important;	
				height:100% !important;
			}	

			.asap-loop-horizontal .asap-columns-4 .article-image,
			.asap-loop-horizontal .asap-columns-4 .article-content,
			.asap-loop-horizontal .content-thin .asap-columns-4 .content-cluster .article-image,
			.asap-loop-horizontal .content-thin .asap-columns-4.content-cluster .article-content {
				min-height:100px !important;	
				height:100% !important;
			}		

			.asap-loop-horizontal .asap-columns-5 .article-image,
			.asap-loop-horizontal .asap-columns-5 .article-content,
			.asap-loop-horizontal .content-thin .asap-columns-5 .content-cluster .article-image,
			.asap-loop-horizontal .content-thin .asap-columns-5 .content-cluster .article-content {
				min-height:90px !important;		
				height:100% !important;
			}
			

		}
		";

	else:
	
		/*
		 * Keep aspect ratio images
		 */

		if ( ! $loop_horizontal && $design_box ) :
			$custom_css .= "
			.asap-box-design .article-content {
				overflow:visible;
			}
			";
		endif;

	endif;


	/*
	 * Transitions
	 */

	if ( $enable_transitions ) :

		$custom_css .= "
			.article-loop .article-image,
			.article-loop a p,
			.article-loop img,
			.article-image-featured,
			input,
			textarea,
			a { 
				transition:all .2s; 
			}
			
			.article-loop:hover .article-image,
			.article-loop:hover img{
				transform:scale(1.05) 
			}

		";

	endif;
		
	/*
	 * Table of contents
	 */
	if (get_theme_mod('asap_hide_index') && (get_theme_mod('asap_user_hide_index'))): 

		$custom_css .= "
		.the-content .post-index #index-table
		{
			display:none;
		}
		";

	endif;

	if ($asap_index_list == 3): 

		$custom_css .= "
		.the-content .post-index ul,
		.the-content .post-index ol {
			list-style: none;
		}
		.the-content .post-index li {
			margin-left: 14px !important;
		}
		.the-content .post-index .classh3, 
			{
			margin-left:36px !important;
		}
		";

	endif;

	if ( $toc_sticky ):

		$custom_css .= "
			@media(max-width:1050px) {
				.the-content .post-index 
				{
					position:sticky;
					margin-top: 0 !important;
					box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
					z-index:9;
				}

				.asap-box-design .the-content .post-index.width100vw {
					box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;		
				}
				
				.the-content .post-index li
				{
					margin-left:2px !important;
				}
				#index-table
				{
					display:none;
				}
				.check-table svg {
					transform: rotateX(180deg);
				}
				.asap-content-box {
					overflow:visible !important;
				}
				.asap-box-design .post-thumbnail {
					margin:1rem 0 !important;
					border-top-right-radius: 0.5rem;
					border-top-left-radius: 0.5rem;
					overflow: hidden;	
				}
				.asap-back {
					z-index:9 !important;
				}
			}
		";
	
		if ( ! get_theme_mod('asap_user_hide_index') ) :

		$custom_css .= "
			@media(min-width:1050px) {
			.the-content .post-index .btn-show {
				display:none !important;
			}
			}
		";

		endif;

		if ( get_theme_mod('asap_no_sticky_header') ):
		
			$custom_css .= "
				@media(max-width:1050px) {
				.the-content .post-index 
				{
					top:0;
				}
				.the-content h2:before,
				.the-content h3:before,
				.the-content h2 span:before,
				.the-content h3 span:before {
					margin-top: -90px !important;
					height: 90px !important;
			  	}	
				}
			";

		else:
			if ( get_theme_mod('asap_toc_overlay') ) :
				$custom_css .= "
					@media(max-width:1050px) {
					.the-content .post-index 
					{
						top:0px;
						z-index:99999;
					}
					}
				";
			else:
				$custom_css .= "
					@media(max-width:1050px) {
					.the-content .post-index 
					{
						top:60px;
					}
					.the-content h2:before,
					.the-content h3:before,
					.the-content h2 span:before,
					.the-content h3 span:before {
						margin-top: -150px !important;
						height: 150px !important;
			  		}	
					}
				";
			endif;

		endif;

	endif;
	
	if ( $asap_index_design ):
		
		if ( $asap_index_design == 2 ):
			
			$custom_css .= "

			.the-content .post-index {
				background:#fff !important;
				border-left:none;
				border-bottom:none;
				border-right:none;
	    		box-shadow: 0 10px 26px rgba(0, 0, 0, 0.125);
				margin-top:26px !important;
			}

			.author-box {
	    		box-shadow: 0 10px 26px rgba(0, 0, 0, 0.125);				
			}

			";

		elseif ( $asap_index_design == 1 ):

			$custom_css .= "
				
			.the-content .post-index {
				background:{$asap_btn_background}1A !important;
				border:1px solid {$asap_btn_background} !important;
			}
			
			.the-content .post-index .checkbox .check-table svg {
				stroke-width:1 !important;
			}
			
			";

			if ( $toc_sticky ) :

				$custom_css .= "
					@media(max-width:1050px) {
						.the-content .post-index 
						{
							background:#fff !important;
						}
					}
				";

			endif;

		endif;
		
	endif;



	// Luego, empezamos a añadir las reglas CSS
	$custom_css .= "
	@media(max-width: 480px) {
	    h1, .archive .content-loop h1 {
	        font-size: calc({$asap_size_h1}px - 8px);
	    }
	    .asap-hero h1 {
	        font-size: calc({$asap_size_h1_featured}px - 8px);
	    }	    
	    h2 {
	        font-size: calc({$asap_size_h2}px - 4px);
	    }
	    h3 {
	        font-size: calc({$asap_size_h3}px - 4px);
	    }";

	    // Aquí, verificamos si $rounded_borders es verdadero
	    if ( $rounded_borders ) {
	        $custom_css .= "
	        .article-loop-featured .article-image-featured {
	            border-radius: {$borders_radius}px !important;
	        }";
	    }

	    // Cerramos el bloque de media query
	    $custom_css .= "
	}";
	
		
	$custom_css .= "	
	@media(min-width:1050px) {
		.content-thin {
			width: calc(95% - {$asap_width_sidebar}px);
		}
		#menu>ul {
			font-size: calc({$asap_size_text}px - 2px);
		}
		 #menu ul .menu-item-has-children:after {
			  border: solid {$asap_header_color};
			  border-width: 0 2px 2px 0;
    	}
	}
	@media(max-width:1050px) {
		#menu ul li .sub-menu li a:hover { 
			color:{$asap_link_color} !important;
		}
		#menu ul>li ul>li>a{
			font-size: calc({$asap_size_text}px - 2px);		
		}
	}
	";


	/*
	 * Sidebar options
	 */
	if ( ( get_theme_mod('asap_sidebar_image') ||  ( $loop_horizontal && $design_box ) )  && ! $enable_featured_posts ):  

		$custom_css .= "
		.last-post-sidebar {
			padding:0;
			margin-bottom:2rem !important;
		}
		.last-post-sidebar .article-loop a {
			display:flex !important;
			align-items: center;
		}
		.last-post-sidebar .article-loop p {
			width:100%;
			text-align:left !important;
			margin-bottom:0;
			font-size: calc({$asap_size_loop}px - 2px) !important;
		}
		.last-post-sidebar .article-content {
			margin-bottom:0 !important;
			margin-right:.5rem;
			min-width:120px;
		}
		.last-post-sidebar .article-image {
			height:90px !important;
			min-width:120px;

		}
		.last-post-sidebar article {
			margin-bottom:1.5rem !important;
		}	
		
		.asap-box-design .last-post-sidebar .article-loop a {
			flex-direction: row;
		}
		
		.asap-box-design .last-post-sidebar .asap-box-container p {
			margin-bottom:0 !important;
			padding:0 10px 0 10px !important;
		}
				
		 .asap-box-design .last-post-sidebar article:last-child {
			 margin-bottom:3.5rem !important;
		}
		
		";

		if ( ! $deactivate_background ) :
				
			$custom_css .= "
			.last-post-sidebar .article-content {
				height:90px !important;
			}
			";

		endif;

	else:

		$custom_css .= "
		 .asap-box-design .last-post-sidebar .article-content {
			 min-height:140px;
		}
			
		.asap-box-design .last-post-sidebar .article-loop {
			margin-bottom:.85rem !important;
		}
		

		 .asap-box-design .last-post-sidebar article:last-child {
			 margin-bottom:2rem !important;
		}		

		";

	endif;






	/*
	 * Layout list design
	 */
	if ( get_theme_mod('asap_enable_layout_lists')  ) :  

		$custom_css .= "
		.the-content ul:not(#index-table) li::marker {
			color: {$asap_btn_background};
		}

		.the-content ol:not(.post-index ol) > li::before {
		    content: counter(li);
		    counter-increment: li;
		    left: -1.5em;
		    top: 65%;
		    color:{$asap_btn_color};
		    background: {$asap_btn_background};
		    height: 1.4em;
		    min-width: 1.22em;
		    padding: 1px 1px 1px 2px;
		    border-radius: 6px;
		    border: 1px solid {$asap_btn_background};
		    line-height: 1.5em;
		    font-size: 22px;
		    text-align: center;
		    font-weight: normal;
		    float: left !important;
		    margin-right: 14px;
		    margin-top: 8px;
		}

		.the-content ol:not(.post-index ol) {
		    counter-reset: li;
		    list-style: none;
		    padding: 0;
		    margin-bottom: 2rem;
		    text-shadow: 0 1px 0 rgb(255 255 255 / 50%);
		}

		.the-content ol:not(.post-index ol) > li {
		    position: relative;
		    display: block;
		    padding: 0.5rem 0 0;
		    margin: 0.5rem 0 1rem !important;
		    border-radius: 10px;
		    text-decoration: none;
		    margin-left: 2px;
		}
		";

	endif;


	/*
	 * Show post extract in loop
	 */
	if (get_theme_mod('asap_show_post_extract')): 

		$custom_css .= "
		.asap-date-loop {
			font-size: calc({$asap_size_text}px - 5px) !important;
			text-align:left;
		}
				
		.asap-box-design .asap-box-container,
		.asap-loop-horizontal .asap-box-container
		{
			flex-direction:column;
			justify-content:center;
			align-items: flex-start !important;
		}
		
		.asap-box-design .article-loop .show-extract p {
			margin-top:6px;
			margin-bottom:0 !important;
		}
		
		.asap-box-design .article-loop .show-extract span.asap-read-more {
			margin-top:14px !important;
			margin-bottom:0 !important;
			display:block;
		}
	
		
		";

		if ( ! $loop_horizontal ) :

			$custom_css .= "
				.asap-box-design .asap-box-container
				{
					justify-content: flex-start !important;
				}
				.asap-box-design .related-posts .asap-box-container
				{
					justify-content:center !important;
				}
				.asap-box-design .asap-box-container p,
				.asap-box-design .asap-box-container>span,
				.asap-box-design .asap-box-container h2,
				.asap-box-design .asap-box-container h3 
				{
					padding-bottom:.9rem !important;
				}
			";

		else :

			$custom_css .= "
				.asap-box-design.asap-loop-horizontal .asap-box-container>span.asap-date-loop {
					padding:0 0 1rem 0 !important;
				}
			";
		
		endif;

	else: 

		$custom_css .= "
		.asap-date-loop {
			font-size: calc({$asap_size_text}px - 5px) !important;
			text-align:center;
		}
		
		";

		if ( get_theme_mod('asap_show_date_loop') ) :

			$custom_css .= "
			.asap-loop-horizontal .asap-date-loop 
			{
				font-size: calc({$asap_size_text}px - 5px) !important;
				text-align:left;
			}
			.asap-box-design .asap-box-container
			{
				flex-direction:column;
				justify-content:center;
				align-items: flex-start !important;
			}
			.asap-box-design .asap-date-loop:not(.asap-loop-horizontal .asap-date-loop)
			{
				padding:1.25rem 0 0 0 !important;
				text-align:center !important;
				margin-top: 0;
			}
			";

			if ( $loop_horizontal ) :
					
				$custom_css .= "

				.asap-box-design.asap-loop-horizontal .asap-box-container>span.asap-date-loop
				{
					padding:1.25rem 1.25rem 0 1.25rem !important;
				}

				";

			endif;

		endif;

	endif;


	if ( get_theme_mod('asap_show_date_loop') ) :
		
		if ( ! $loop_horizontal ) :

		$custom_css .= "
		
		 .asap-box-design .asap-box-container p,
		 .asap-box-design .asap-box-container>span,
		 .asap-box-design .asap-box-container h2,
		 .asap-box-design .asap-box-container h3 {

				padding-top:.5rem !important;

		}

		";	
		
		endif;
		
	endif;

		
	if (get_theme_mod('asap_show_post_extract') ): 
		
		$custom_css .= "
		.article-loop p:not(.last-post-sidebar .article-loop p),
		.article-loop h2,
		.article-loop h3,
		.article-loop h4,
		.article-loop .entry-title:not(.last-post-sidebar .article-loop .entry-title) {
			text-align: left !important;
			margin-bottom:6px !important;
			padding:0 10px 0 0 !important;
		}
		
		.article-loop .show-extract p,
		.featured-post a+p {
			font-size: calc({$asap_size_text}px - 2px) !important;
		}
		
		@media (min-width:800px) {
			
			.article-loop {
				margin-bottom:1rem !important;
			}
				
			.related-posts .article-loop {
				margin-bottom:0 !important;
			}
			
		}
		";

	elseif (get_theme_mod('asap_show_cluster_extract')): 

		$custom_css .= "
		.content-cluster .article-loop p,
		.content-cluster .article-loop h2,
		.content-cluster .article-loop h3,
		.content-cluster .article-loop h4,
		.content-cluster .article-loop span.entry-title {
			text-align: left !important;
			margin-bottom:8px !important;
			padding:0 10px 0 0 !important;
		}
		
		.content-cluster .article-loop .show-extract p {
			font-size: calc({$asap_size_text}px - 2px) !important;
		}
	
		@media (min-width:800px) {
			
			.content-cluster .article-loop {
				margin-bottom:1rem !important;
			}

		}
		";

	endif;
		

	if ( get_theme_mod('asap_show_post_extract') ): 

		if ( get_theme_mod('asap_loop_design' ) ) :

			$custom_css .= "
				.asap-box-design .asap-box-container:not(.asap-box-design .last-post-sidebar .asap-box-container) {
					padding:1.5rem 0;
				}
			";
			
		else:
			$custom_css .= "
				.asap-box-design .asap-box-container:not(.asap-box-design .last-post-sidebar .asap-box-container) {
					padding:1.5rem;
				}
			";

		endif;

	endif;

		
	/*
	 * Header sticky
	 */
	if (get_theme_mod('asap_no_sticky_header')): 
		
		$custom_css .= "
		.sticky {
			top: 22px !important;
		}
				
		.the-content h2:before,
		.the-content h2 span:before{
			margin-top: -20px;
			height: 20px;
		  }
		
		header {
			position:relative !important;
		}
		
		@media (max-width: 1050px) {
			.content-single,
			.content-page {
				padding-top: 0 !important;
			}
			.content-loop {
				padding: 2rem;
			}
			.author .content-loop, .category .content-loop {
				padding: 1rem 2rem 2rem 2rem;
			}
		
		}
		";
		
	else: 
		
		$custom_css .= "
		.the-content h2:before,
		.the-content h2 span:before {
			margin-top: -70px;
			height: 70px;
		 }
		 
		 ";
		
	endif;


	/*
	 * Hero design
	 */
	if ( isset( $header_design ) && !empty( $header_design ) && $header_design != 'normal' ) :

	$custom_css .= "

    	.asap-hero-content h1,
    	.asap-hero-content p {
    		color:{$hero_text} !important;
    	}

    	@media (max-width: 1050px) {
			.content-page,
			.content-single {
				padding-top: 0 !important;
			}
		}
		";

		if ( ! get_theme_mod('asap_no_sticky_header') ) : 

			$custom_css .= "
			@media (max-width: 1050px) {
				.asap-hero {
					height: 360px;
				}
				.category .asap-hero {
					height: 300px !important;
				}
				.asap-hero-content {
				    padding-top: 3.5rem !important;
				}
			}
			";

		else:
			
			$custom_css .= "
			@media (max-width: 1050px) {
				.asap-hero {
					height: 300px;
				    margin-top:-10px;
				}
			}
			";

		endif;

		if ( ! get_theme_mod('asap_disable_highlight', false) ) : 

			$custom_css .= "
			.asap-hero:after {
				background: radial-gradient(ellipse at center, {$hero_background} 18%,#000000d1 100%);
			}
		";

		endif;

    endif;
		
	
	if ( $dropdown_right ) :

	$custom_css .= "	
	
	@media(min-width:1050px) 
	{
		#menu ul>li ul {
		left:0 !important;
		}
		#menu ul>li>ul>li>ul {
		left: 15.5rem!important;
		}
	}
	";
	endif;

	/*
	 * Menu
	 */
	if (get_theme_mod('asap_menu_columns') == '2'): 
		
		$custom_css .= "
			@media (min-width: 1050px) {
				#menu ul>li ul {
					width: 26rem !important;
				}
				#menu>ul>li ul>li {
					width:50% !important;
				}
			}
		";

		if ( $dropdown_right ) {

			$custom_css .= "
			@media (min-width: 1050px) {
				#menu ul>li>ul>li>ul {
					left: .25rem !important;
				}
			}
			
			";
		}
		else 
		{
			$custom_css .= "
			@media (min-width: 1050px) {
				#menu ul>li>ul>li>ul {
					left: -16.25rem !important;
				}
			}
			";
		}
	
	endif;

	if (get_theme_mod('asap_menu_columns') == '3'): 

		$custom_css .= "
		@media (min-width: 1050px) {
			#menu ul>li ul {
				width: 36rem !important;
			}
			#menu>ul>li ul>li {
				width:33.333333% !important;
			}
		}
		";

		if ( $dropdown_right ) {

			$custom_css .= "
			@media (min-width: 1050px) {
				#menu ul>li>ul>li>ul {
					left: .25rem !important;	
				}
			}
			";
		}
		else 
		{
			$custom_css .= "
			@media (min-width: 1050px) {
				#menu ul>li>ul>li>ul {
					left: -36.2rem !important;	
				}
			}
			";
		}

	endif;		
		

	/*
	 * Logo above menu
	 */
	if ( $asap_header_top ):
	$custom_css .= "
		.asap-content-logo-top 
		{
	background: {$asap_top_header_background};
	}
	.site-header-content
	{
		justify-content:center;
	}
	
			@media (max-width: 1050px)
			{
				.site-logo img
				{
					max-height:36px;
				}
				#menu {
					 margin-top: 60px;
				}
				.asap-content-logo-top 
				{
					padding: 0 1rem !important;
					z-index: 9999;
					width: 100%;
					height:60px;
				}
			}
			";

		if ( $asap_search_header_left ):

		$custom_css .= "
			@media (min-width: 1050px)
			{
				.site-header-content-top {
					padding-left:1rem;
					padding-rigth:1rem;
					justify-content:space-between;
				}		
			}
		";

		else:

		$custom_css .= "
			.asap-content-logo-top {
				padding:1rem;
			}

			.site-header-content-top {
				flex-direction:column;
				justify-content:center;
			}
		";

		endif;

		if (get_theme_mod('asap_no_sticky_header')): 
			$custom_css .= "		
			@media (max-width: 1050px)
			{
			.asap-content-logo-top 
			{
				position:absolute;
			}
			}
			";
		else:
			$custom_css .= "		
			@media (max-width: 1050px)
			{
			.asap-content-logo-top 
			{
				position:fixed;
			}
			}
			";
		endif;

	endif;

	if ( get_theme_mod('asap_shadow_menu') ) :

		if ( $asap_header_top ) {
			
			$custom_css .= "
			
				@media(min-width:1050px) 
				{
					.site-header {
						box-shadow: 0 1px 12px rgb(0 0 0 / 30%);
					}
					.asap-content-logo-top {
						position: relative;
						z-index:9999;
						overflow:hidden;
					}
				}
				@media(max-width:1050px) 
				{
					.asap-content-logo-top {
						box-shadow: 0 1px 12px rgb(0 0 0 / 30%);
					}
				}				
			";
			
		} else {

			$custom_css .= "
				.site-header {
					box-shadow: 0 1px 12px rgb(0 0 0 / 30%);
				}
			";

		}

	endif;

	/*
	 * Show total footer
	 */
	if (get_theme_mod('asap_total_footer')): 	

		$custom_css .= "
		.content-footer .widget-area {
			padding-right:2rem;
		}
	
		footer {
			background: {$asap_footer_background};
		}		

		.content-footer {
			padding:20px;
		}
		
		.content-footer p.widget-title {
			margin-bottom:10px;
		}	
		
		.content-footer .logo-footer {
			width:100%;
			align-items:flex-start;
		}

		.content-footer-social {
			width: 100%;			
		}
		
		.content-single,
		content-page {
			margin-bottom:2rem;
		}

		.content-footer-social > div {
			max-width: calc({$asap_width_header}px - 32px);	
			margin:0 auto;	
		}

		.content-footer .widget-bottom-area {
			margin-top:1.25rem;
		}

		.content-footer .widget-bottom-title {
			display: none;
		}
		
		@media (min-width:1050px) {
			
			.content-footer {
				padding:30px 0;
			}

			.content-footer .logo-footer {
				margin:0 1rem 0 0 !important;
				padding-right:2rem !important;
			}
		
		}
		
		@media (max-width:1050px) {
			
			.content-footer .logo-footer {
				margin:0 0 1rem 0 !important;
			}

			.content-footer .widget-area {
				margin-top:2rem !important;
			}
		
		}
		";
		
	else: 
		
		$custom_css .= "
		.content-footer {
			padding:0;
		}

		.content-footer p {
			margin-bottom:0 !important;
		}

		.content-footer .widget-area,
		.content-footer .widget-bottom-area {
			margin-bottom: 0rem;
			padding:1rem;
		}

		.content-footer li:first-child:before {
			content: '';
			padding: 0;
		}

		.content-footer li:before {
			content: '|';
			padding: 0 7px 0 5px;
			color: #fff;
			opacity: .4;
		}

		.content-footer li {
			list-style-type: none;
			display: inline;
			font-size: 15px;
		}

		.content-footer .widget-title,
		.content-footer .widget-bottom-title {
			display: none;
		}

		.content-footer {
			background: {$asap_footer_background};
		}		
		
		.content-footer-social {
			max-width: calc({$asap_width_header}px - 32px);			
		}
		";


	endif;		


	/*
	 * Social buttons
	 */
	if ((is_single()) && ($asap_show_social_buttons_bottom) && (($asap_social_post_types == '1') || ($asap_social_post_types == '2'))): 
		
		$custom_css .= "
		@media (max-width:1050px) {
		
			.content-footer {
				padding-bottom:44px;
			}
			
		}
		";
		
	endif;
	
	if ((is_page()) && ($asap_show_social_buttons_bottom) && (($asap_social_post_types == '1') || ($asap_social_post_types == '3'))): 
		
		$custom_css .= "
		@media (max-width:1050px) {
		
			.content-footer {
				padding-bottom:44px;
			}
			
		}
		";
		
	endif;

	/*
	 * Body background design box
	 */
	if ( $design_box ) :

		$custom_css .= "
			 .asap-box-design .the-content .post-index
			 {
				 background:{$asap_body_background} !important;
			}
	
			.asap-box-design .asap-user-comment-text {
				background:{$asap_body_background};
			}

			.asap-box-design .asap-user-comment-text:before {
				border-bottom: 8px solid {$asap_body_background};
			}		

			@media(max-width:1050px){
				.content-cluster {
					padding: 0.5rem 0 0 !important;
				}
			}

		";	

		if ( $loop_horizontal ): 

			$custom_css .= "
				.article-content {
					margin-bottom:0 !important;
				}
			";

		else:

			$custom_css .= "
				.asap-box-design .asap-show-desc .asap-box-container {
					flex-direction:column;
					justify-content:flex-start !important;
					align-items: flex-start !important;
					padding:1.5rem;
				}
				
				.asap-box-design .asap-show-desc .asap-box-container .show-extract span {
					margin-top:6px;
					margin-bottom:0 !important;
				}
				.asap-box-design .article-loop.asap-show-desc span.entry-title {
					text-align:left !important;
					padding:0 !important;
					margin-bottom: 8px !important;
					padding: 0 10px 0 0 !important;
				}
		
			";

		endif;

		if ( is_single() || is_page() ) :

			$postid = get_the_ID();			

			$hideimg = get_post_meta($postid, 'hide_image_post', true) || get_post_meta($postid, 'hide_image_page', true);
	
			$has_thumbnail = has_post_thumbnail($postid);

			if ( ! get_theme_mod('asap_hide_image_featured') && ! $hideimg && $header_design == 'normal' && $has_thumbnail ) :
				
				$custom_css .= "
				.asap-box-design .asap-content-box .the-content {
					padding-top: 0 !important;
				}
				";
				
				if ( ! get_theme_mod('asap_margin_thumbnail') ) :
					
					$custom_css .= "
					.asap-box-design .asap-content-box .post-thumbnail {
						margin-bottom: 1.5rem !important;
					}	
					";					
				endif;
				
				
			endif;
				
			if ( get_post_meta( $postid, 'asap_disable_box_design', true ) ) :

				$type = is_single() ? 'postid' : 'page-id';

				$custom_css .= "
					.{$type}-{$postid}.asap-box-design .the-content .post-index
					{
						border-radius:0 !important;
						border-top:1px solid #ddd;
						border-bottom:1px solid #ddd;
					}

					.{$type}-{$postid}.asap-box-design .asap-content-box
					{
						background:transparent !important;
						box-shadow:none !important;
						border-radius:0 !important;
					}

					.{$type}-{$postid}.asap-box-design .asap-content-box .the-content
					{
						padding:0 !important;
					}
				";	

			endif;
			
		endif;

	endif;


	/*
	 * Menu Flotante (o cabecera doble en mobile)
	 */
	if ( $float_menu || $asap_header_top ): 
	
		$custom_css .= "
		@media (max-width: 1050px) {
			header label {
				width: 64px;
				height: 64px;		
				position: fixed;
				padding:0;
				right: 1.5rem;
    			bottom: 6rem;
				border-radius: 50%;
				-webkit-box-shadow: 0px 4px 8px 0px rgba(0,0,0,0.5);
				box-shadow: 0px 4px 8px 0px rgba(0,0,0,0.5);
				background-color: #fff;
				-webkit-transition: 300ms ease all;
				transition: 300ms ease all;
				z-index:101;
			display: flex;
  				align-items: center;
		}
		
		.site-header-content {
			justify-content: center;
		}
			
			.line {
				background:#282828 !important;
			}
			
			.circle {
				margin:0 auto;
				width: 24px;
				height: 24px;
			}
			";
			
			if (get_theme_mod('asap_no_sticky_header')): 
			
				$custom_css .= "
				#menu {
					top:0;
					margin-top:0;
				}
				";
			
			else: 

				if ( ! $asap_header_top ) :
			
				$custom_css .= "
				#menu {
					margin-top:30px;
				}			
				";

				endif;
			
			endif;		
			
		$custom_css .= "	
		}
		";
		
		// ✅ Si hay cabecera doble, ajustar margin-top del menú en mobile
		if ( $asap_header_top ) :
			$custom_css .= "
			@media (max-width: 1050px) {
				#menu {
					margin-top: 30px !important;
				}
			}
			";
		endif;

	else:

	endif;		


	/*
	 * Add scrolling to menu
	 */
	if ( get_theme_mod('asap_scroll_menu') ) :

		$menu_height = get_theme_mod('asap_height_menu', 300);

		$custom_css .= "
			@media(min-width:1050px){
				#menu ul>li ul
					{
						max-height:{$menu_height}px !important;
						overflow-y:auto;
					}
			}
		";


	endif;


	/*
	 * Clústers 2 columns in mobile
	 */	
	if ( get_theme_mod('asap_two_columns') ) :

		$custom_css .= "
			@media (max-width: 480px) {
				.content-area
				{
					margin-left: -0.75rem;
					margin-right: -0.75rem;
				}
				
				.content-area .article-loop-featured {
				    margin-left: .75rem;
				    margin-right: .75rem;
				}

				.related-posts {
					padding: 1.5rem .75rem !important;
				}

				.article-loop.asap-columns-2,
				.article-loop.asap-columns-3,
				.article-loop.asap-columns-4,
				.article-loop.asap-columns-5 {
					flex-basis: 50%;
					max-width: 50%;
					padding: 0 0.75rem 0.75rem 0.75rem !important;
				}

				.related-posts .article-loop {
					padding: 0 0.75rem 0.75rem 0.75rem !important;
				}

				.article-content,
				.article-image,
				.content-thin .content-cluster .article-content,
				.content-thin .content-cluster .article-image {
					height: 106px !important;
				}
				
				.related-posts .article-content,
				.related-posts .article-image {
					height: 106px !important;
				}
				.article-loop p,
				.article-loop h2,
				.article-loop h3,
				.article-loop h4,
				.article-loop span.entry-title,
				.related-posts p {
					font-size: calc({$asap_size_loop}px - 1px) !important;
					line-height:1.3 !important;
				}
			}

		";

	endif;


	if ($search_background) {
		$custom_css .= "
			.search-header input {
				background:{$search_background} !important;
			}
			.search-header button.s-btn,
			.search-header input::placeholder {
				color: {$search_color} !important;
				opacity:1 !important;
			}
			.search-header button.s-btn:hover {
				opacity:.7 !important;
			}
			.search-header input:not([type=submit]):not([type=radio]):not([type=checkbox]):not([type=file]) {
				border-color: {$search_background} !important;
			}
		";
	}

		
	/*
	 * Featured image small
	 */
	if ( get_theme_mod('asap_show_featured_small')  ) :  

		$custom_css .= "
			@media (min-width: 768px) {
				.content-single .post-thumbnail {
				    float: left;
				    max-width: 300px;
				}
				.asap-box-design .content-single .asap-content-box .post-thumbnail {
					margin-top:0 !important;
					margin-left:0 !important;
					margin-bottom: 0 !important;
				}

			}
		";

		if (!$design_box):
			$custom_css .= "
			@media (min-width: 768px) {
				.content-single .post-thumbnail {
				    margin: 0.75rem 1rem 0.5rem 0 !important;
				}
			}
			";
		else:
			$custom_css .= "
			@media (min-width: 768px) {
				.content-single .post-thumbnail {
				    margin: 1.5rem !important;
				}
				.asap-box-design .asap-content-box .the-content {
					padding-top:1.25rem !important;
				}
			}
			";
		endif;

	endif;

	if ( get_theme_mod('asap_show_featured_small_page')  ) :  

		$custom_css .= "
		@media (min-width: 768px) {
			.content-page .post-thumbnail {
			    float: left;
			    margin: 0.75rem 1rem 0.5rem 0 !important;
			    max-width: 300px;
			}
		}
		";

	endif;

	if (in_array(get_theme_mod('asap_menu_desktop_design'), ['design2', 'design3'])) :

		$custom_css .= "
		@media (min-width: 1050px) {
			#menu>ul>li ul li a {
				transition: all .15s;
			}
			#menu>ul>li {
				margin:0;
				background: {$asap_link_as_btn_bg};
		    	padding: 0 15px;
			}		
			#menu>ul>li>a{
				display: inline-block;
			}
			#menu>ul>li:hover {
		    	background: {$asap_link_as_btn_bg_hover};	
			}
		}
		";

		if (get_theme_mod('asap_menu_desktop_design') == 'design3') {

			$custom_css .= "
			@media (min-width: 1050px) {
				#menu>ul>li {
					line-height:40px;
					border-radius:8px;
					margin-left:6px;
					padding: 0 12px;
				}
			}
			";

		}

	endif;

	if ($design_type == 1) {
		$custom_css .= "    
		    @media(min-width:768px) {
				.design-1 .grid-container .grid-item.item-2 h2,
				.design-1 .grid-container .grid-item.item-4 h2 {
				    font-size: calc({$asap_size_loop_news_featured}px - 8px) !important;
				}
		    }
		";
	}

	if ($design_type == 2) {
		$custom_css .= " 
		    @media(min-width:768px) {
		    	.design-2 .grid-container .grid-item:first-child a .main-h2-container h2 {
				    font-size: {$asap_size_loop_news_featured} !important;		    		
		    	}
				.design-2 .grid-container .grid-item.item-2 h2,
				.design-2 .grid-container .grid-item.item-3 h2,
				.design-2 .grid-container .grid-item.item-4 h2,
				.design-2 .grid-container .grid-item.item-5 h2,
				.design-2 .grid-container .grid-item.item-6 h2 {
				    font-size: calc({$asap_size_loop_news}px + 2px) !important;
				}
				.design-2 .grid-container .grid-item:first-child,
			    .design-2 .category-posts .regular-posts .regular-post:first-child {
			    	grid-column: span {$columns};	
				}
				.design-2 .regular-posts .regular-post:first-child h3 {
					font-size: {$asap_size_loop_news_featured}px !important;
					line-height: 1.3;		
				}				
		    }
		    @media(max-width:768px) {
				.design-2 .grid-container .grid-item h2 {
				    font-size: calc({$asap_size_loop_news_featured}px - 8px) !important;
				}
		    }
			.design-2 .grid-container,
			.design-2 .regular-posts {
				grid-template-columns: repeat({$columns}, 1fr);
			}

			.design-2 .latest-post-container .asap-columns-1, 
			.design-2 .latest-post-container .asap-columns-2, 
			.design-2 .latest-post-container .asap-columns-3, 
			.design-2 .latest-post-container .asap-columns-4, 
			.design-2 .latest-post-container .asap-columns-5 {
				width: 100%;
			    flex-basis: 100%;
			    max-width: 100%;
			}
		";
	}

	if ($design_type == 3) {
		$custom_css .= " 
		    @media(max-width:768px) {
				.design-3 .grid-container .grid-item h2 {
				    font-size: calc({$asap_size_loop_news_featured}px - 4px) !important;
				}
			}
		";
	}

	if ($design_type == 2 || $design_type == 3) {
		$custom_css .= "    
			.home-categories .show-extract p {
				font-size: calc({$asap_size_text}px - 2px) !important;
			}
		";
	}

	$product_width = get_theme_mod('asap_wc_mobile_two_cols', true) ? '50%' : '100%';

	$custom_css .= "
	    @media (max-width: 768px) {
	        .woocommerce ul.products[class*=\"columns-\"] li.product,
	        .woocommerce-page ul.products[class*=\"columns-\"] li.product {
	            width: {$product_width} !important;
	        }
	    }
	";

	/*
	 * Megamenu Dropdown Compact - Ancho dinámico del CONTAINER
	 * Restar 2rem (padding left + right de site-header-content)
	 */
	$custom_css .= "
		.asap-megamenu-overlay.asap-megamenu-dropdown .asap-megamenu-container {
			width: 100%;
			padding: 0;
		}
		@media (min-width: 1050px) {
			.asap-megamenu-overlay.asap-megamenu-dropdown .asap-megamenu-container {
				max-width: calc({$asap_width_header}px - 2rem) !important;
				width: calc({$asap_width_header}px - 2rem) !important;
				margin: 0 auto !important;
			}
		}
		.asap-megamenu-overlay.asap-megamenu-dropdown .asap-megamenu-header,
		.asap-megamenu-overlay.asap-megamenu-dropdown .asap-megamenu-content {
			padding-left: 1rem;
			padding-right: 1rem;
		}
	";

	/*
	 * Minify CSS
	 */
	$custom_css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $custom_css);
	$custom_css = preg_replace('/^[\s\t]*[\r\n]+/m', '', $custom_css);


	/*
	 * Add inline styles
	 */
	wp_add_inline_style( 'asap-style', $custom_css );