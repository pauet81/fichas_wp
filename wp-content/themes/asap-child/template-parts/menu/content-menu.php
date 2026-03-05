<?php 

$asap_search_text = get_theme_mod('asap_search_text') ?: esc_html( __( "Search", "asap" ) );
$float_design = get_theme_mod('asap_float_design');

$blog_id = (int) get_option('page_for_posts');
$blog_url = $blog_id ? get_permalink($blog_id) : home_url('/blog/');

$mega_fallback_infantil = get_stylesheet_directory_uri() . '/images/mega-fallback-infantil.svg';
$mega_fallback_primaria = get_stylesheet_directory_uri() . '/images/mega-fallback-primaria.svg';

if (!function_exists('fichas_get_hub_bg')) {
	function fichas_get_hub_bg($path, $fallback) {
		$path = trim((string) $path, '/');
		if ($path !== '') {
			$page = get_page_by_path($path);
			if ($page instanceof WP_Post) {
				$thumb_id = get_post_thumbnail_id($page->ID);
				if ($thumb_id) {
					$url = wp_get_attachment_image_url($thumb_id, 'medium_large');
					if ($url) {
						return $url;
					}
				}
			}
		}
		return $fallback;
	}
}
?>

<?php if ( is_active_sidebar( 'hsocial' ) ) : ?>

<div class="social-desktop">

	<?php dynamic_sidebar( 'hsocial' );	?>

</div>

<?php endif; ?>

<div class="asap-menu-container-flex">

	<div class="fichas-progress-badge" id="fichas-progress-badge" aria-live="polite">
		<div class="progress-level">Nivel <span class="level-value">1</span></div>
		<div class="progress-bar"><span class="progress-fill" style="width: 0%;"></span></div>
		<div class="progress-points"><span class="points-value">0</span> pts</div>
	</div>

	<button id="nav-icon" class="nav-toggle" type="button" aria-label="Abrir menú">
		<div class="circle nav-icon">
			<span class="line top"></span>
			<span class="line middle"></span>
			<span class="line bottom"></span>
		</div>
	</button>

	<nav id="menu" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement" role="navigation" <?php if ( $float_design ) { echo ' class="asap-float" '; } ?> >

		<ul class="header-menu mega-menu">
			<li class="menu-item has-mega mega-infantil">
				<a href="<?php echo esc_url( home_url('/infantil/') ); ?>">Infantil</a>
				<div class="mega-panel">
					<div class="mega-grid mega-grid-4">
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/infantil/3-anos/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('infantil/3-anos', $mega_fallback_infantil) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">3 a&ntilde;os</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/infantil/4-anos/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('infantil/4-anos', $mega_fallback_infantil) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">4 a&ntilde;os</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/infantil/5-anos/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('infantil/5-anos', $mega_fallback_infantil) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">5 a&ntilde;os</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/infantil/tematicas/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('infantil/tematicas', $mega_fallback_infantil) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">Tem&aacute;ticas</h4>
								
							</div>
						</a>
					</div>
				</div>
			</li>

			<li class="menu-item has-mega mega-primaria">
				<a href="<?php echo esc_url( home_url('/primaria/') ); ?>">Primaria</a>
				<div class="mega-panel">
					<div class="mega-grid mega-grid-3">
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/primaria/1-primaria/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('primaria/1-primaria', $mega_fallback_primaria) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">1&ordm; Primaria</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/primaria/2-primaria/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('primaria/2-primaria', $mega_fallback_primaria) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">2&ordm; Primaria</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/primaria/3-primaria/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('primaria/3-primaria', $mega_fallback_primaria) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">3&ordm; Primaria</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/primaria/4-primaria/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('primaria/4-primaria', $mega_fallback_primaria) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">4&ordm; Primaria</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/primaria/5-primaria/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('primaria/5-primaria', $mega_fallback_primaria) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">5&ordm; Primaria</h4>
							</div>
						</a>
						<a class="mega-col has-bg mega-card" href="<?php echo esc_url( home_url('/primaria/6-primaria/') ); ?>" style="--mega-bg-image: url('<?php echo esc_url( fichas_get_hub_bg('primaria/6-primaria', $mega_fallback_primaria) ); ?>');">
							<div class="mega-col-inner">
								<h4 class="mega-title-link">6&ordm; Primaria</h4>
								
							</div>
						</a>
					</div>
				</div>
			</li>

			<li class="menu-item"><a href="<?php echo esc_url( $blog_url ); ?>">Blog</a></li>
		</ul>

		<?php if (get_theme_mod('asap_show_search_menu')) : ?>

		<div class="search-responsive">

			<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">

				<input autocomplete="off" id="search-menu" placeholder="<?php echo $asap_search_text; ?>" value="<?php echo get_search_query() ?>" name="s" required>

				<?php if (function_exists('is_woocommerce')): ?>

				<input type="hidden" value="product" name="post_type">

				<?php endif;?>

				<button class="s-btn" type="submit" aria-label="<?php echo esc_html( __( "Search", "asap" ) ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
						<circle cx="11" cy="11" r="8"></circle>
						<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
					</svg>
				</button>

			</form>

		</div>

		<?php endif; ?>

		<?php if ( is_active_sidebar( 'msocial' ) ) : ?>

			<div class="social-mobile social-buttons">

				<?php dynamic_sidebar( 'msocial' );	?>

			</div>

		<?php endif; ?>

	</nav>

</div>

<div id="mobile-offcanvas-overlay" class="mobile-offcanvas-overlay" aria-hidden="true"></div>
<aside id="mobile-offcanvas" class="mobile-offcanvas" aria-hidden="true">
	<button type="button" class="mobile-offcanvas-close" aria-label="Cerrar menú">×</button>
	<nav class="mobile-offcanvas-nav" aria-label="Menú móvil"></nav>
</aside>

