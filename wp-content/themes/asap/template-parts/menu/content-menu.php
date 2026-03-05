<?php 

$asap_search_text = get_theme_mod('asap_search_text') ?: esc_html( __( "Search", "asap" ) );
$float_design = get_theme_mod('asap_float_design');
$megamenu_enabled = get_option('asap_megamenu_enabled', '0') === '1';
?>

<?php if ( is_active_sidebar( 'hsocial' ) ) : ?>

<div class="social-desktop">

	<?php dynamic_sidebar( 'hsocial' );	?>

</div>

<?php endif; ?>

<div<?php if ($megamenu_enabled) echo ' class="asap-menu-container-flex"'; ?>>
	
	<?php if ( has_nav_menu( 'header-menu' ) && !$megamenu_enabled ) : ?>
	
	<input type="checkbox" id="btn-menu" />
	
	<label id="nav-icon" for="btn-menu">

		<div class="circle nav-icon">

			<span class="line top"></span>
			<span class="line middle"></span>
			<span class="line bottom"></span>

		</div>
		
	</label>
	
	<?php endif; ?>
	
	<?php if ($megamenu_enabled) : 
		// Obtener colores personalizados
		$bg_color = get_option('asap_megamenu_bg_color', '#667eea');
		$bg_color_2 = get_option('asap_megamenu_bg_color_2', '#764ba2');
		$card_bg = get_option('asap_megamenu_card_bg', '#ffffff');
		$text_color = get_option('asap_megamenu_text_color', '#333333');
		$header_text_color = get_option('asap_megamenu_header_text_color', '#ffffff');
		$hover_color = get_option('asap_megamenu_hover_color', '#667eea');
		$column_bg = get_option('asap_megamenu_column_bg', '#f8f9fa');
		$link_hover_color = get_option('asap_megamenu_link_hover_color', '#135e96');
		$header_color = get_theme_mod('asap_header_color') ?: '#FFFFFF';
		
		// Obtener efectos visuales
		$enable_zoom = get_option('asap_megamenu_enable_zoom', '0') === '1';
		$enable_shadow = get_option('asap_megamenu_enable_shadow', '1') === '1';
	?>
	
	<!-- MEGAMENU BUTTON -->
	<style>
		/* Variables de color personalizadas */
		:root {
			--megamenu-bg-1: <?php echo esc_attr($bg_color); ?>;
			--megamenu-bg-2: <?php echo esc_attr($bg_color_2); ?>;
			--megamenu-card-bg: <?php echo esc_attr($card_bg); ?>;
			--megamenu-text: <?php echo esc_attr($text_color); ?>;
			--megamenu-header-text: <?php echo esc_attr($header_text_color); ?>;
			--megamenu-hover: <?php echo esc_attr($hover_color); ?>;
			--megamenu-column-bg: <?php echo esc_attr($column_bg); ?>;
			--megamenu-link-hover: <?php echo esc_attr($link_hover_color); ?>;
			--megamenu-trigger-color: <?php echo esc_attr($header_color); ?>;
		}
		
		/* Efectos visuales condicionales */
		<?php if ($enable_zoom): ?>
		.asap-megamenu-column:hover {
			transform: scale(1.02);
		}
		<?php endif; ?>
		
		<?php if (!$enable_shadow): ?>
		.asap-megamenu-column {
			box-shadow: none !important;
		}
		<?php endif; ?>
		
		/* Todos los estilos del megamenu están en assets/css/megamenu.css */
	</style>
	<div class="asap-megamenu-trigger">
		<button type="button" class="asap-megamenu-toggle" aria-label="Abrir menú">
			<span class="asap-megamenu-icon">
				<span></span>
				<span></span>
				<span></span>
			</span>
		</button>
	</div>
	
	<?php endif; ?>

	<nav id="menu" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement" role="navigation" <?php if ( $float_design ) { echo ' class="asap-float" '; } ?> >
		
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

		<?php 
		// Mostrar menú normal SOLO si el megamenu NO está activo
		if (!$megamenu_enabled) {
			wp_nav_menu( array(
				'menu' 				=> 'main-menu-web',
				'theme_location' 	=> 'header-menu',
				'container'         => 'ul',
				'menu_class'        => 'header-menu',
				'fallback_cb' 		=> false
			));
		}
		?>
		
		<?php if ( is_active_sidebar( 'msocial' ) ) : ?>

			<div class="social-mobile social-buttons">

				<?php dynamic_sidebar( 'msocial' );	?>

			</div>

		<?php endif; ?>
		
	</nav> 
	
</div>

<?php if ($megamenu_enabled) : ?>
<!-- MEGAMENU OVERLAY PROFESIONAL -->
<?php 
$megamenu_style = get_option('asap_megamenu_style', 'fullscreen');
// Clase directa según el estilo: asap-megamenu-fullscreen, asap-megamenu-dropdown, asap-megamenu-sidebar
$overlay_class = 'asap-megamenu-overlay asap-megamenu-' . esc_attr($megamenu_style);
?>
<?php 
$logo_text = get_option('asap_megamenu_logo_text', 'Menú');
$hide_logo = get_option('asap_megamenu_hide_logo', '0') === '1';
?>
<div class="<?php echo $overlay_class; ?>" style="display: none;">
	<div class="asap-megamenu-container">
		<div class="asap-megamenu-header">
			<?php if (!$hide_logo) : ?>
			<div class="asap-megamenu-logo">
				<h2><?php echo esc_html($logo_text); ?></h2>
			</div>
			<?php endif; ?>
			<?php 
			// ✅ Solo mostrar botón X en fullscreen y sidebar, NO en dropdown
			if ($megamenu_style === 'fullscreen' || $megamenu_style === 'sidebar') : 
			?>
			<button type="button" class="asap-megamenu-close" aria-label="Cerrar menú">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<line x1="18" y1="6" x2="6" y2="18"></line>
					<line x1="6" y1="6" x2="18" y2="18"></line>
				</svg>
			</button>
			<?php endif; ?>
		</div>
		<div class="asap-megamenu-content">
			<?php
			// Cargar desde opciones globales
			$megamenu_data = get_option('asap_megamenu_global_content', []);
			
			if (!empty($megamenu_data) && !empty($megamenu_data['content'])) {
				$content = json_decode($megamenu_data['content'], true);
				
				if (!empty($content) && is_array($content)) {
					// Usar la función centralizada que aplica las clases correctas
					$settings = [
						'layout' => $megamenu_data['layout'] ?? 'grid',
						'columns' => $megamenu_data['columns'] ?? 4,
					];
					
					// Obtener la instancia del megamenu y usar su método de renderizado
					$megamenu_instance = ASAP_Megamenu::instance();
					echo $megamenu_instance->render_megamenu_from_data($content, $settings);
				} else {
					echo '<p style="text-align:center;padding:40px;color:#fff;">No hay contenido en el megamenu. Ve a Settings > ASAP > Megamenu para configurarlo.</p>';
				}
			} else {
				echo '<p style="text-align:center;padding:40px;color:#fff;">No hay megamenu configurado. Ve a Settings > ASAP > Megamenu para crear uno.</p>';
			}
			?>
		</div>
	</div>
</div>

<script>
(function() {
	'use strict';
	
	// Variables globales
	let isAnimating = false;
	let overlay = null;
	let toggleButton = null;
	let closeButton = null;
	let container = null;
	let isDropdown = false;
	
	// Inicializar cuando el DOM esté listo
	function init() {
		overlay = document.querySelector('.asap-megamenu-overlay');
		toggleButton = document.querySelector('.asap-megamenu-toggle');
		closeButton = document.querySelector('.asap-megamenu-close');
		container = document.querySelector('.asap-megamenu-container');
		
		if (!overlay || !toggleButton) return;
		
		isDropdown = overlay.classList.contains('asap-megamenu-dropdown');
		
		console.log('🚀 Megamenu inicializado:', isDropdown ? 'Dropdown' : 'Fullscreen/Sidebar');
		
		// Event listeners
		setupEventListeners();
	}
	
	// Configurar event listeners
	function setupEventListeners() {
		// Toggle button
		toggleButton.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			toggle();
		});
		
		// Close button
		if (closeButton) {
			closeButton.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				close();
			});
		}
		
		// ESC key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && isOpen()) {
				close();
			}
		});
		
		// Click fuera (solo para dropdown)
		if (isDropdown) {
			document.addEventListener('click', function(e) {
				if (!isOpen() || isAnimating) return;
				
				const clickedToggle = toggleButton.contains(e.target);
				const clickedInside = container && container.contains(e.target);
				
				if (!clickedToggle && !clickedInside) {
					close();
				}
			});
		}
	}
	
	// Verificar si está abierto
	function isOpen() {
		return overlay && overlay.classList.contains('active');
	}
	
	// Toggle
	function toggle() {
		if (isAnimating) return;
		
		if (isOpen()) {
			close();
		} else {
			open();
		}
	}
	
	// Abrir
	function open() {
		if (isAnimating || isOpen()) return;
		
		isAnimating = true;
		console.log('📂 Abriendo megamenu...');
		
		// Mostrar overlay
	overlay.style.display = 'flex';
		
		// Bloquear scroll (excepto dropdown)
		if (!isDropdown) {
	document.body.style.overflow = 'hidden';
		}
	
		// Forzar reflow
		overlay.offsetHeight;
		
		// Activar animación
		requestAnimationFrame(() => {
			overlay.classList.add('active');
			toggleButton.classList.add('active');
			
			// Liberar después de la animación
	setTimeout(() => {
				isAnimating = false;
				console.log('✅ Megamenu abierto');
			}, 300);
		});
	}
	
	// Cerrar
	function close() {
		if (isAnimating || !isOpen()) return;
		
		isAnimating = true;
		console.log('📁 Cerrando megamenu...');
		
		// Desactivar
	overlay.classList.remove('active');
		toggleButton.classList.remove('active');
	document.body.style.overflow = '';
	
		// Ocultar después de la animación
	setTimeout(() => {
			if (!overlay.classList.contains('active')) {
				overlay.style.display = 'none';
			}
			isAnimating = false;
			console.log('✅ Megamenu cerrado');
		}, 300);
	}
	
	// Exponer funciones globales para onclick inline
	window.toggleMegamenu = toggle;
	window.closeMegamenu = close;
	
	// Inicializar
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
</script>

<?php endif; ?>	