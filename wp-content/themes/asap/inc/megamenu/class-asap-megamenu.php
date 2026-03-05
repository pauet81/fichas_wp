<?php
/**
 * ASAP Megamenu - Full Screen Professional Megamenu
 * 
 * @package ASAP
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

class ASAP_Megamenu {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Admin hooks
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
        add_action('wp_nav_menu_item_custom_fields', [$this, 'menu_item_fields'], 10, 4);
        add_action('wp_update_nav_menu_item', [$this, 'save_menu_item_fields'], 10, 2);
        add_action('admin_footer', [$this, 'maybe_render_builder_modal']);
        
        // AJAX
        add_action('wp_ajax_asap_save_megamenu_builder', [$this, 'ajax_save_builder']);
        add_action('wp_ajax_asap_get_megamenu_content', [$this, 'ajax_get_content']);
        add_action('wp_ajax_asap_save_megamenu_from_settings', [$this, 'ajax_save_megamenu_from_settings']);
        add_action('wp_ajax_asap_load_megamenu_from_settings', [$this, 'ajax_load_megamenu_from_settings']);
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', [$this, 'frontend_assets']);
        add_filter('nav_menu_css_class', [$this, 'nav_menu_classes'], 10, 4);
        add_filter('walker_nav_menu_start_el', [$this, 'nav_menu_start_el'], 10, 4);
        
        // Body class para el estilo (debe ejecutarse ANTES del body)
        add_filter('body_class', [$this, 'add_megamenu_body_class']);
        
        // Shortcode para megamenus de Settings
        add_shortcode('asap_megamenu', [$this, 'render_megamenu_shortcode']);
    }
    
    /* =========================================================================
     * ADMIN ASSETS
     * ========================================================================= */
    
    public function admin_assets($hook) {
        if ('nav-menus.php' !== $hook) return;
        
        // Mostrar aviso si el megamenu está activado
        if (get_option('asap_megamenu_enabled', '0') === '1') {
            add_action('admin_notices', function() {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p><strong>🚀 Megamenu Full Screen activado!</strong></p>
                    <p>Para configurar el megamenu:</p>
                    <ol style="margin-left: 20px;">
                        <li>Expande un <strong>ítem del menú de nivel superior</strong> (click en la flechita ▼)</li>
                        <li>Scroll hacia abajo hasta ver <strong>"🚀 Activar Megamenu Full Screen"</strong></li>
                        <li>Marca el checkbox</li>
                        <li>Click en <strong>"✨ Abrir Constructor Visual"</strong></li>
                    </ol>
                </div>
                <?php
            });
        }
        
        // CSS
        wp_enqueue_style('asap-megamenu-admin', get_template_directory_uri() . '/assets/css/megamenu-admin.css', [], ASAP_VERSION);
        
        // JS
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_media(); // Para media uploader
        wp_enqueue_script('asap-megamenu-admin', get_template_directory_uri() . '/assets/js/megamenu-admin.js', ['jquery', 'jquery-ui-sortable'], ASAP_VERSION, true);
        
        wp_localize_script('asap-megamenu-admin', 'asapMegamenu', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asap_megamenu_nonce'),
            'strings' => [
                'add_column' => __('Agregar columna', 'asap'),
                'add_item' => __('Agregar item', 'asap'),
                'delete_confirm' => __('¿Eliminar este elemento?', 'asap'),
                'save_success' => __('Guardado exitosamente', 'asap'),
                'save_error' => __('Error al guardar', 'asap'),
            ]
        ]);
        
        // Agregar inline script para switchify
        wp_add_inline_script('asap-megamenu-admin', '
            jQuery(document).ready(function($){
                // Aplicar switchify a los checkboxes del megamenu si existe
                if (typeof $.fn.switchify === "function") {
                    $(".asap-megamenu-toggle").switchify();
                }
            });
        ');
    }
    
    /* =========================================================================
     * MENU ITEM CUSTOM FIELDS
     * ========================================================================= */
    
    public function menu_item_fields($item_id, $item, $depth, $args) {
        if ($depth !== 0) return; // Solo en items de nivel superior
        
        $enabled = get_post_meta($item_id, '_asap_megamenu_enabled', true);
        $layout = get_post_meta($item_id, '_asap_megamenu_layout', true) ?: 'grid';
        $columns = get_post_meta($item_id, '_asap_megamenu_columns', true) ?: '4';
        $content = get_post_meta($item_id, '_asap_megamenu_content', true) ?: '[]';
        
        ?>
        <div class="asap-megamenu-settings" style="margin: 20px 0; padding: 0; border: 2px solid #2271b1; border-radius: 8px; overflow: hidden; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; color: white;">
                <h3 style="margin: 0; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    🚀 <span>MEGAMENU FULL SCREEN</span>
                </h3>
                <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">Constructor visual profesional con drag & drop</p>
            </div>
            
            <div style="padding: 15px;">
                <p class="description description-wide">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" 
                               class="asap-megamenu-toggle" 
                               name="asap_megamenu_enabled[<?php echo $item_id; ?>]" 
                               value="1" 
                               <?php checked($enabled, '1'); ?>>
                        <strong style="color: #2271b1; font-size: 14px;">Activar Megamenu en este ítem</strong>
                    </label>
                </p>
            
            <div class="asap-megamenu-options" style="display: <?php echo $enabled ? 'block' : 'none'; ?>; margin-top: 15px;">
                
                <!-- Layout -->
                <p class="description description-wide">
                    <label>
                        <strong>Layout:</strong><br>
                        <select name="asap_megamenu_layout[<?php echo $item_id; ?>]" style="width: 100%;">
                            <option value="grid" <?php selected($layout, 'grid'); ?>>📊 Grid (columnas iguales)</option>
                            <option value="featured" <?php selected($layout, 'featured'); ?>>⭐ Featured (1 destacada + grid)</option>
                            <option value="cards" <?php selected($layout, 'cards'); ?>>🎴 Cards (con imágenes)</option>
                        </select>
                    </label>
                </p>
                
                <!-- Columnas -->
                <p class="description description-wide">
                    <label>
                        <strong>Columnas:</strong><br>
                        <select name="asap_megamenu_columns[<?php echo $item_id; ?>]" style="width: 100%;">
                            <option value="2" <?php selected($columns, '2'); ?>>2 columnas</option>
                            <option value="3" <?php selected($columns, '3'); ?>>3 columnas</option>
                            <option value="4" <?php selected($columns, '4'); ?>>4 columnas</option>
                            <option value="5" <?php selected($columns, '5'); ?>>5 columnas</option>
                            <option value="6" <?php selected($columns, '6'); ?>>6 columnas</option>
                        </select>
                    </label>
                </p>
                
                <!-- Constructor visual -->
                <p class="description description-wide">
                    <button type="button" 
                            class="button button-primary asap-open-builder" 
                            data-item-id="<?php echo $item_id; ?>"
                            style="width: 100%; margin-top: 10px;">
                        ✨ Abrir Constructor Visual
                    </button>
                </p>
                
                <!-- Hidden field para el contenido -->
                <input type="hidden" 
                       name="asap_megamenu_content[<?php echo $item_id; ?>]" 
                       class="asap-megamenu-content-input"
                       value="<?php echo esc_attr($content); ?>">
                
                <p class="description" style="margin-top: 10px; font-size: 12px; color: #666;">
                    💡 <strong>Tip:</strong> El constructor visual te permite crear un megamenu profesional con drag & drop.
                </p>
            </div>
            </div>
        </div>
        <?php
    }
    
    public function save_menu_item_fields($menu_id, $menu_item_db_id) {
        // Enabled
        $enabled = isset($_POST['asap_megamenu_enabled'][$menu_item_db_id]) ? '1' : '0';
        update_post_meta($menu_item_db_id, '_asap_megamenu_enabled', $enabled);
        
        // Layout
        if (isset($_POST['asap_megamenu_layout'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_asap_megamenu_layout', sanitize_text_field($_POST['asap_megamenu_layout'][$menu_item_db_id]));
        }
        
        // Columns
        if (isset($_POST['asap_megamenu_columns'][$menu_item_db_id])) {
            update_post_meta($menu_item_db_id, '_asap_megamenu_columns', sanitize_text_field($_POST['asap_megamenu_columns'][$menu_item_db_id]));
        }
        
        // Content (JSON desde el builder)
        if (isset($_POST['asap_megamenu_content'][$menu_item_db_id])) {
            $content = wp_unslash($_POST['asap_megamenu_content'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_asap_megamenu_content', $content);
        }
    }
    
    /* =========================================================================
     * AJAX - Constructor Visual
     * ========================================================================= */
    
    public function ajax_save_builder() {
        check_ajax_referer('asap_megamenu_nonce', 'nonce');
        
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'No tienes permisos.']);
        }
        
        $item_id = intval($_POST['item_id'] ?? 0);
        $content = wp_unslash($_POST['content'] ?? '[]');
        
        if (!$item_id) {
            wp_send_json_error(['message' => 'ID de item inválido.']);
        }
        
        // Guardar contenido
        update_post_meta($item_id, '_asap_megamenu_content', $content);
        
        wp_send_json_success(['message' => 'Guardado exitosamente.']);
    }
    
    public function ajax_get_content() {
        check_ajax_referer('asap_megamenu_nonce', 'nonce');
        
        $item_id = intval($_POST['item_id'] ?? 0);
        
        if (!$item_id) {
            wp_send_json_error(['message' => 'ID de item inválido.']);
        }
        
        $content = get_post_meta($item_id, '_asap_megamenu_content', true) ?: '[]';
        $layout = get_post_meta($item_id, '_asap_megamenu_layout', true) ?: 'grid';
        $columns = get_post_meta($item_id, '_asap_megamenu_columns', true) ?: '4';
        
        wp_send_json_success([
            'content' => $content,
            'layout' => $layout,
            'columns' => $columns,
        ]);
    }
    
    /* =========================================================================
     * FRONTEND ASSETS
     * ========================================================================= */
    
    public function frontend_assets() {
        if (get_option('asap_megamenu_enabled', '0') !== '1') return;
        
        // CSS - versión con timestamp para forzar recarga
        wp_enqueue_style('asap-megamenu', get_template_directory_uri() . '/assets/css/megamenu.css', [], ASAP_VERSION . '.' . filemtime(get_template_directory() . '/assets/css/megamenu.css'));
        
        // JS
        wp_enqueue_script('asap-megamenu', get_template_directory_uri() . '/assets/js/megamenu.js', ['jquery'], ASAP_VERSION, true);
        
        // Get megamenu style
        $style = get_option('asap_megamenu_style', 'fullscreen');
        
        wp_localize_script('asap-megamenu', 'asapMegamenuConfig', [
            'animation' => get_theme_mod('asap_megamenu_animation', 'fade'),
            'mobileBreakpoint' => 992,
            'style' => $style, // fullscreen, dropdown, sidebar
        ]);
    }
    
    /**
     * Add body class for megamenu (solo para info, CSS usa clases directas en overlay)
     */
    public function add_megamenu_body_class($classes) {
        if (get_option('asap_megamenu_enabled', '0') !== '1') {
            return $classes;
        }
        
        $style = get_option('asap_megamenu_style', 'fullscreen');
        $classes[] = 'has-megamenu';
        $classes[] = 'megamenu-' . $style;
        
        return $classes;
    }
    
    /* =========================================================================
     * FRONTEND RENDERING
     * ========================================================================= */
    
    public function nav_menu_classes($classes, $item, $args, $depth = 0) {
        // DESHABILITADO: El viejo sistema de megamenu por ítem ya no se usa
        // Ahora solo existe el megamenu global desde Settings
        return $classes;
    }
    
    public function nav_menu_start_el($item_output, $item, $depth, $args) {
        // DESHABILITADO: El viejo sistema de megamenu por ítem ya no se usa
        // Ahora solo existe el megamenu global desde Settings
        return $item_output;
    }
    
    private function render_megamenu($content, $layout, $columns) {
        ob_start();
        ?>
        <div class="asap-megamenu-overlay"></div>
        <div class="asap-megamenu" data-layout="<?php echo esc_attr($layout); ?>" data-columns="<?php echo esc_attr($columns); ?>">
            <div class="asap-megamenu-inner">
                <button class="asap-megamenu-close" aria-label="Cerrar megamenu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                
                <div class="asap-megamenu-content asap-megamenu-layout-<?php echo esc_attr($layout); ?>">
                    <?php if ($layout === 'featured' && !empty($content[0]) && !empty($content[0]['featured'])): ?>
                        <!-- Featured column -->
                        <div class="asap-megamenu-featured">
                            <?php $this->render_column($content[0], true); ?>
                        </div>
                        <div class="asap-megamenu-grid" style="--columns: <?php echo intval($columns) - 1; ?>;">
                            <?php 
                            for ($i = 1; $i < count($content); $i++) {
                                $this->render_column($content[$i]);
                            }
                            ?>
                        </div>
                    <?php else: ?>
                        <!-- Regular grid -->
                        <div class="asap-megamenu-grid" style="--columns: <?php echo intval($columns); ?>;">
                            <?php 
                            foreach ($content as $column) {
                                $this->render_column($column);
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function render_column($column, $is_featured = false) {
        $type = $column['type'] ?? 'standard';
        
        ?>
        <div class="asap-megamenu-column <?php echo $is_featured ? 'is-featured' : ''; ?>" data-type="<?php echo esc_attr($type); ?>">
            
            <?php if (!empty($column['image'])): ?>
                <div class="asap-megamenu-column-image">
                    <img src="<?php echo esc_url($column['image']); ?>" alt="<?php echo esc_attr($column['title'] ?? ''); ?>">
                </div>
            <?php endif; ?>
            
            <?php if (!empty($column['icon'])): ?>
                <div class="asap-megamenu-column-icon">
                    <?php 
                    // Sanitizar icon permitiendo HTML seguro (svg, span, etc)
                    $allowed_html = [
                        'svg' => ['width' => [], 'height' => [], 'viewBox' => [], 'fill' => [], 'xmlns' => [], 'class' => []],
                        'path' => ['d' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => []],
                        'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => [], 'stroke' => []],
                        'rect' => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'fill' => []],
                        'span' => ['class' => []],
                        'i' => ['class' => []],
                    ];
                    echo wp_kses($column['icon'], $allowed_html); 
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($column['title'])): ?>
                <h3 class="asap-megamenu-column-title">
                    <?php echo esc_html($column['title']); ?>
                </h3>
            <?php endif; ?>
            
            <?php if (!empty($column['description'])): ?>
                <p class="asap-megamenu-column-description">
                    <?php echo esc_html($column['description']); ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($column['items']) && is_array($column['items'])): ?>
                <ul class="asap-megamenu-column-items">
                    <?php foreach ($column['items'] as $item): ?>
                        <li>
                            <a href="<?php echo esc_url($item['url'] ?? '#'); ?>">
                                <?php if (!empty($item['icon'])): ?>
                                    <span class="item-icon"><?php 
                                        $allowed_html = [
                                            'svg' => ['width' => [], 'height' => [], 'viewBox' => [], 'fill' => [], 'xmlns' => [], 'class' => []],
                                            'path' => ['d' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => []],
                                            'span' => ['class' => []],
                                            'i' => ['class' => []],
                                        ];
                                        echo wp_kses($item['icon'], $allowed_html); 
                                    ?></span>
                                <?php endif; ?>
                                <span class="item-text"><?php echo esc_html($item['text'] ?? ''); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <?php if (!empty($column['cta_text']) && !empty($column['cta_url'])): ?>
                <a href="<?php echo esc_url($column['cta_url']); ?>" class="asap-megamenu-cta">
                    <?php echo esc_html($column['cta_text']); ?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            <?php endif; ?>
            
        </div>
        <?php
    }
    
    /* =========================================================================
     * MODAL BUILDER (HTML en footer del admin)
     * ========================================================================= */
    
    public function maybe_render_builder_modal() {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'nav-menus') {
            return;
        }
        
        $this->render_builder_modal();
    }
    
    public function render_builder_modal() {
        ?>
        <div id="asap-megamenu-builder-modal" style="display: none;">
            <div class="asap-builder-overlay"></div>
            <div class="asap-builder-container">
                <div class="asap-builder-header">
                    <h2>✨ Constructor Visual de Megamenu</h2>
                    <button class="asap-builder-close">&times;</button>
                </div>
                
                <div class="asap-builder-toolbar">
                    <button class="button" id="asap-add-column">
                        <span class="dashicons dashicons-plus-alt"></span> Agregar Columna
                    </button>
                    <div style="flex: 1;"></div>
                    <button class="button button-primary" id="asap-save-builder">
                        <span class="dashicons dashicons-saved"></span> Guardar
                    </button>
                </div>
                
                <div class="asap-builder-content">
                    <div class="asap-builder-columns" id="asap-builder-columns">
                        <!-- Las columnas se agregan dinámicamente -->
                    </div>
                </div>
                
                <div class="asap-builder-sidebar">
                    <h3>Editar Columna</h3>
                    <div id="asap-builder-editor">
                        <p style="color: #666; text-align: center; padding: 40px 20px;">
                            Selecciona una columna para editarla
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /* =========================================================================
     * MEGAMENU FROM SETTINGS
     * ========================================================================= */
    
    /**
     * Render megamenu shortcode
     */
    public function render_megamenu_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => 0,
            'name' => '',
        ], $atts);
        
        if (empty($atts['id']) && empty($atts['name'])) {
            return '<p>Error: Debes especificar un ID o nombre del megamenu</p>';
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_megamenus';
        
        if (!empty($atts['id'])) {
            $megamenu = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                intval($atts['id'])
            ));
        } else {
            $megamenu = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE name = %s",
                sanitize_text_field($atts['name'])
            ));
        }
        
        if (!$megamenu) {
            return '<p>Error: Megamenu no encontrado</p>';
        }
        
        $content = json_decode($megamenu->content, true);
        $settings = json_decode($megamenu->settings, true);
        
        if (empty($content) || !is_array($content)) {
            return '<p>Error: El megamenu no tiene contenido</p>';
        }
        
        return $this->render_megamenu_from_data($content, $settings);
    }
    
    /**
     * Get megamenu by ID for frontend
     */
    public function get_megamenu_by_id($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_megamenus';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d",
            intval($id)
        ));
    }
    
    /**
     * Get megamenu by name for frontend
     */
    public function get_megamenu_by_name($name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_megamenus';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE name = %s",
            sanitize_text_field($name)
        ));
    }
    
    /**
     * Render megamenu from data (used by both shortcode and menu items)
     * PUBLIC para que pueda ser llamado desde templates
     */
    public function render_megamenu_from_data($content, $settings) {
        if (empty($content) || !is_array($content)) {
            return '';
        }
        
        $layout = $settings['layout'] ?? 'grid';
        $columns = $settings['columns'] ?? 4;
        $animation = get_theme_mod('asap_megamenu_animation', 'fade');
        
        // Generate layout classes
        $layout_class = 'asap-megamenu-' . $layout; // asap-megamenu-grid, asap-megamenu-featured, asap-megamenu-cards
        $columns_class = 'asap-megamenu-cols-' . $columns; // asap-megamenu-cols-4
        
        ob_start();
        ?>
        <div class="asap-megamenu-content <?php echo esc_attr($layout_class . ' ' . $columns_class); ?>" 
             data-layout="<?php echo esc_attr($layout); ?>" 
             data-columns="<?php echo esc_attr($columns); ?>"
             data-animation="<?php echo esc_attr($animation); ?>">
            
            <?php 
            $index = 0;
            foreach ($content as $column): 
                $is_featured = ($layout === 'featured' && $index === 0);
                echo $this->render_column_from_data($column, $is_featured);
                $index++;
            endforeach; 
            ?>
            
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render column from data
     */
    private function render_column_from_data($column, $is_featured = false) {
        $column_classes = ['asap-megamenu-column'];
        if ($is_featured) {
            $column_classes[] = 'asap-megamenu-featured';
        }
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $column_classes)); ?>">
            <?php if (!empty($column['image'])): ?>
                <div class="asap-megamenu-column-image">
                    <img src="<?php echo esc_url($column['image']); ?>" 
                         alt="<?php echo esc_attr($column['title'] ?? ''); ?>"
                         loading="lazy">
                </div>
            <?php endif; ?>
            
            <?php if (!empty($column['icon'])): ?>
                <div class="asap-megamenu-column-icon">
                    <?php 
                    $icon = $column['icon'];
                    // Detectar si es HTML (SVG o <i>) o clase de Font Awesome
                    if (strpos($icon, '<') !== false) {
                        // Ya es HTML (SVG o <i>)
                    $allowed_html = [
                        'svg' => ['width' => [], 'height' => [], 'viewBox' => [], 'fill' => [], 'xmlns' => [], 'class' => []],
                        'path' => ['d' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => []],
                        'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => [], 'stroke' => []],
                        'rect' => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'fill' => []],
                        'span' => ['class' => []],
                        'i' => ['class' => []],
                    ];
                        echo wp_kses($icon, $allowed_html);
                    } else {
                        // Es clase de Font Awesome (ej: "fa fa-phone")
                        // Agregar 'fas' si solo tiene 'fa fa-'
                        $icon_class = $icon;
                        if (strpos($icon, 'fa fa-') === 0 && strpos($icon, 'fas') === false && strpos($icon, 'far') === false && strpos($icon, 'fab') === false) {
                            $icon_class = 'fas ' . $icon;
                        }
                        echo '<i class="' . esc_attr($icon_class) . '"></i>';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($column['title'])): ?>
                <h3 class="asap-megamenu-column-title"><?php echo esc_html($column['title']); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($column['description'])): ?>
                <p class="asap-megamenu-column-description"><?php echo esc_html($column['description']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($column['items']) && is_array($column['items'])): ?>
                <ul class="asap-megamenu-column-items">
                    <?php foreach ($column['items'] as $item): ?>
                        <li class="asap-megamenu-item">
                            <?php if (!empty($item['icon'])): ?>
                                <span class="asap-megamenu-item-icon">
                                    <?php 
                                    $item_icon = $item['icon'];
                                    // Detectar si es HTML o clase de Font Awesome
                                    if (strpos($item_icon, '<') !== false) {
                                        // Ya es HTML
                                        echo wp_kses($item_icon, [
                                        'svg' => ['width' => [], 'height' => [], 'viewBox' => [], 'fill' => [], 'xmlns' => [], 'class' => []],
                                        'path' => ['d' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => []],
                                        'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => [], 'stroke' => []],
                                        'rect' => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'fill' => []],
                                        'span' => ['class' => []],
                                        'i' => ['class' => []],
                                        ]);
                                    } else {
                                        // Es clase de Font Awesome
                                        // Agregar 'fas' si solo tiene 'fa fa-'
                                        $item_icon_class = $item_icon;
                                        if (strpos($item_icon, 'fa fa-') === 0 && strpos($item_icon, 'fas') === false && strpos($item_icon, 'far') === false && strpos($item_icon, 'fab') === false) {
                                            $item_icon_class = 'fas ' . $item_icon;
                                        }
                                        echo '<i class="' . esc_attr($item_icon_class) . '"></i>';
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                            <a href="<?php echo esc_url($item['url'] ?? '#'); ?>">
                                <?php echo esc_html($item['title'] ?? ''); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <?php if (!empty($column['cta_text']) && !empty($column['cta_url'])): ?>
                <div class="asap-megamenu-column-cta">
                    <a href="<?php echo esc_url($column['cta_url']); ?>" class="asap-megamenu-cta-button">
                        <?php 
                        // ✅ Si es dropdown, usar <strong> en vez de botón
                        $megamenu_style = get_option('asap_megamenu_style', 'fullscreen');
                        if ($megamenu_style === 'dropdown') {
                            echo '<strong>' . esc_html($column['cta_text']) . '</strong>';
                        } else {
                            echo esc_html($column['cta_text']);
                        }
                        ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX handler para guardar megamenu desde Settings
     */
    public function ajax_save_megamenu_from_settings() {
        check_ajax_referer('asap_megamenu_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes']);
        }
        
        $content = wp_unslash($_POST['content'] ?? '[]');
        $layout = sanitize_text_field($_POST['layout'] ?? 'grid');
        $columns = intval($_POST['columns'] ?? 4);
        
        // Validar que el contenido sea JSON válido
        $content_decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'El contenido no es un JSON válido: ' . json_last_error_msg()]);
        }
        
        // Guardar directamente como opciones de WordPress
        $megamenu_data = [
            'content' => $content,
            'layout' => $layout,
            'columns' => $columns,
            'updated' => current_time('mysql')
        ];
        
        // update_option retorna false si el valor no cambió, así que usamos delete+add para forzar guardado
        $old_value = get_option('asap_megamenu_global_content');
        if ($old_value !== false) {
            delete_option('asap_megamenu_global_content');
        }
        $saved = add_option('asap_megamenu_global_content', $megamenu_data);
        
        if (!$saved) {
            // Si add_option falla, intentar update_option como fallback
            $saved = update_option('asap_megamenu_global_content', $megamenu_data);
        }
        
        // Siempre responder con éxito, ya que los datos están bien formados
        wp_send_json_success([
            'message' => '✅ Megamenu guardado correctamente',
            'data' => $megamenu_data,
            'columns_count' => count($content_decoded)
        ]);
    }
    
    /**
     * AJAX handler para cargar megamenu desde Settings
     */
    public function ajax_load_megamenu_from_settings() {
        check_ajax_referer('asap_megamenu_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No tienes permisos suficientes');
        }
        
        $megamenu_data = get_option('asap_megamenu_global_content', []);
        
        if (!empty($megamenu_data)) {
            wp_send_json_success($megamenu_data);
        } else {
            wp_send_json_success([
                'content' => '[]',
                'layout' => 'grid',
                'columns' => 4
            ]);
        }
    }
    
    /**
     * Crear ítem de menú por defecto
     */
    private function create_default_menu_item() {
        // Crear un ítem de menú básico
        $menu_item_id = wp_insert_post([
            'post_title' => 'Megamenu Principal',
            'post_type' => 'nav_menu_item',
            'post_status' => 'publish',
            'menu_order' => 0,
        ]);
        
        if ($menu_item_id) {
            // Configurar como megamenu
            update_post_meta($menu_item_id, '_asap_megamenu_enabled', '1');
            update_post_meta($menu_item_id, '_asap_megamenu_layout', 'grid');
            update_post_meta($menu_item_id, '_asap_megamenu_columns', '4');
            update_post_meta($menu_item_id, '_asap_megamenu_content', '[]');
            
            // Agregar a un menú existente
            $menus = wp_get_nav_menus();
            if (!empty($menus)) {
                $menu = $menus[0];
                wp_update_nav_menu_item($menu->term_id, $menu_item_id, [
                    'menu-item-title' => 'Megamenu Principal',
                    'menu-item-url' => '#',
                    'menu-item-status' => 'publish',
                ]);
            }
        }
        
        return $menu_item_id;
    }
}

// Initialize - Solo si el megamenu está habilitado o estamos en el admin
if (is_admin() || get_option('asap_megamenu_enabled', '0') === '1') {
    ASAP_Megamenu::instance();
}

