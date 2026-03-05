<?php
/**
 * Asap Theme - Dark Mode
 * Funcionalidad de modo oscuro integrada en el tema
 * 
 * @package AsapTheme
 */

defined('ABSPATH') or die('¡Sin acceso directo, por favor!');

/**
 * Enqueue Dark Mode Assets
 * Carga los archivos CSS y JS del modo oscuro si está habilitado
 */
function asap_dark_mode_enqueue_assets() {
    $enabled = get_theme_mod('asap_dark_mode_enabled', false);
    
    if ($enabled) {
        // Cargar CSS del modo oscuro
        wp_enqueue_style(
            'asap-dark-mode-style',
            ASAP_THEME_URL . '/assets/css/dark-mode.css',
            array('asap-style'),
            filemtime(ASAP_THEME_DIR . '/assets/css/dark-mode.css')
        );

        // Cargar JavaScript del modo oscuro
        wp_enqueue_script(
            'asap-dark-mode-script',
            ASAP_THEME_URL . '/assets/js/dark-mode.js',
            array('jquery'),
            filemtime(ASAP_THEME_DIR . '/assets/js/dark-mode.js'),
            true
        );

        // Pasar configuraciones al JavaScript
        wp_localize_script('asap-dark-mode-script', 'asapDarkMode', array(
            'buttonPosition' => get_theme_mod('asap_dark_mode_button_position', 'bottom-left'),
            'buttonText' => get_theme_mod('asap_dark_mode_button_text', '🌙'),
            'buttonTextActive' => get_theme_mod('asap_dark_mode_button_text_active', '☀️'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'asap_dark_mode_enqueue_assets');

/**
 * Add Dark Mode Inline Script to Prevent FOUC
 * Previene el "flash" de contenido cuando se cambia de página
 * Este script se ejecuta ANTES de que se renderice el HTML
 */
function asap_dark_mode_prevent_fouc() {
    if (!get_theme_mod('asap_dark_mode_enabled', false)) {
        return;
    }
    ?>
    <script>
    (function() {
        // Aplicar inmediatamente si el usuario tiene modo oscuro activado
        var darkMode = localStorage.getItem('asapDarkMode');
        if (darkMode === 'on') {
            document.documentElement.classList.add('asap-dark-mode');
            if (document.body) {
                document.body.classList.add('asap-dark-mode');
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    document.body.classList.add('asap-dark-mode');
                });
            }
        }
    })();
    </script>
    <?php
}
add_action('wp_head', 'asap_dark_mode_prevent_fouc', 1);

/**
 * Add Dark Mode Custom CSS
 * Agrega CSS personalizado para los colores del modo oscuro
 */
function asap_dark_mode_custom_css() {
    if (!get_theme_mod('asap_dark_mode_enabled', false)) {
        return;
    }

    // Obtener colores personalizados
    $bg_primary = get_theme_mod('asap_dark_mode_bg_primary', '#121212');
    $bg_secondary = get_theme_mod('asap_dark_mode_bg_secondary', '#1a1a1a');
    $bg_tertiary = get_theme_mod('asap_dark_mode_bg_tertiary', '#1e1e1e');
    
    $text_primary = get_theme_mod('asap_dark_mode_text_primary', '#f1f1f1');
    $text_secondary = get_theme_mod('asap_dark_mode_text_secondary', '#e0e0e0');
    
    $heading_h1 = get_theme_mod('asap_dark_mode_heading_h1', '#FDBE02');
    $heading_h2 = get_theme_mod('asap_dark_mode_heading_h2', '#FF8040');
    $heading_other = get_theme_mod('asap_dark_mode_heading_other', '#FDBE02');
    
    $link_color = get_theme_mod('asap_dark_mode_link_color', '#80cbc4');
    $link_hover = get_theme_mod('asap_dark_mode_link_hover', '#4dd0e1');
    
    $accent_color = get_theme_mod('asap_dark_mode_accent_color', '#80cbc4');
    $border_color = get_theme_mod('asap_dark_mode_border_color', '#444444');
    
    $button_bg = get_theme_mod('asap_dark_mode_button_bg', '#333333');
    $button_text = get_theme_mod('asap_dark_mode_button_text_color', '#ffffff');

    // Generar CSS personalizado
    $custom_css = "
    /* Asap Theme - Dark Mode Custom Colors */
    body.asap-dark-mode {
        --dark-bg-primary: {$bg_primary};
        --dark-bg-secondary: {$bg_secondary};
        --dark-bg-tertiary: {$bg_tertiary};
        --dark-text-primary: {$text_primary};
        --dark-text-secondary: {$text_secondary};
        --dark-heading-h1: {$heading_h1};
        --dark-heading-h2: {$heading_h2};
        --dark-heading-other: {$heading_other};
        --dark-link-color: {$link_color};
        --dark-link-hover: {$link_hover};
        --dark-accent-color: {$accent_color};
        --dark-border-color: {$border_color};
    }
    
    /* Aplicar variables CSS personalizadas */
    body.asap-dark-mode,
    body.asap-dark-mode .asap-content-box,
    body.asap-dark-mode .the-content,
    body.asap-dark-mode .site,
    body.asap-dark-mode .entry-content {
        background-color: var(--dark-bg-primary) !important;
        color: var(--dark-text-primary) !important;
    }
    
    body.asap-dark-mode h1 {
        color: var(--dark-heading-h1) !important;
    }
    
    body.asap-dark-mode h2 {
        color: var(--dark-heading-h2) !important;
    }
    
    body.asap-dark-mode h3,
    body.asap-dark-mode h4,
    body.asap-dark-mode h5,
    body.asap-dark-mode h6 {
        color: var(--dark-heading-other) !important;
    }
    
    body.asap-dark-mode a {
        color: var(--dark-link-color) !important;
    }
    
    body.asap-dark-mode a:hover {
        color: var(--dark-link-hover) !important;
    }
    
    body.asap-dark-mode p,
    body.asap-dark-mode span,
    body.asap-dark-mode div,
    body.asap-dark-mode li {
        color: var(--dark-text-secondary) !important;
    }
    
    /* Botón de toggle personalizado */
    #asap-dark-mode-toggle {
        background-color: {$button_bg} !important;
        color: {$button_text} !important;
    }
    ";

    wp_add_inline_style('asap-dark-mode-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'asap_dark_mode_custom_css', 20);

/**
 * Add Dark Mode Body Class
 * Agrega la clase al body si el usuario tiene el modo oscuro activado
 */
function asap_dark_mode_body_class($classes) {
    if (get_theme_mod('asap_dark_mode_enabled', false)) {
        // La clase se agrega via JavaScript basándose en localStorage
        // para recordar la preferencia del usuario
    }
    return $classes;
}
add_filter('body_class', 'asap_dark_mode_body_class');

/**
 * Register Dark Mode Customizer Settings
 * Registra todas las configuraciones del modo oscuro en el customizer
 */
function asap_dark_mode_customizer($wp_customize) {
    
    // Sección principal del Modo Oscuro
    $wp_customize->add_section('asap_dark_mode_section', array(
        'title'       => __('🌙 Modo Oscuro', 'asap'),
        'priority'    => 30,
        'description' => __('Configura el modo oscuro de tu sitio web. Tus visitantes podrán activarlo/desactivarlo según su preferencia.', 'asap'),
    ));

    // ============================================
    // Activar Modo Oscuro
    // ============================================
    $wp_customize->add_setting('asap_dark_mode_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'asap_sanitize_checkbox',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('asap_dark_mode_enabled', array(
        'label'       => __('Activar Modo Oscuro', 'asap'),
        'description' => __('Habilita la funcionalidad de modo oscuro en tu sitio.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'type'        => 'checkbox',
        'priority'    => 10,
    ));

    // ============================================
    // CONFIGURACIÓN DEL BOTÓN
    // ============================================
    
    // Separador
    $wp_customize->add_setting('asap_dark_mode_button_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_button_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Configuración del Botón', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 20,
    )));

    // Posición del botón
    $wp_customize->add_setting('asap_dark_mode_button_position', array(
        'default'           => 'bottom-left',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('asap_dark_mode_button_position', array(
        'label'       => __('Posición del Botón', 'asap'),
        'description' => __('Elige dónde aparecerá el botón flotante.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'type'        => 'select',
        'choices'     => array(
            'bottom-left'  => __('Abajo Izquierda', 'asap'),
            'bottom-right' => __('Abajo Derecha', 'asap'),
            'top-left'     => __('Arriba Izquierda', 'asap'),
            'top-right'    => __('Arriba Derecha', 'asap'),
        ),
        'priority'    => 21,
    ));

    // Texto del botón (modo claro)
    $wp_customize->add_setting('asap_dark_mode_button_text', array(
        'default'           => '🌙',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('asap_dark_mode_button_text', array(
        'label'       => __('Icono del Botón (Modo Claro)', 'asap'),
        'description' => __('Emoji o texto que se muestra cuando el modo oscuro está desactivado.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'type'        => 'text',
        'priority'    => 22,
    ));

    // Texto del botón (modo oscuro)
    $wp_customize->add_setting('asap_dark_mode_button_text_active', array(
        'default'           => '☀️',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('asap_dark_mode_button_text_active', array(
        'label'       => __('Icono del Botón (Modo Oscuro)', 'asap'),
        'description' => __('Emoji o texto que se muestra cuando el modo oscuro está activado.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'type'        => 'text',
        'priority'    => 23,
    ));

    // ============================================
    // COLORES DE FONDO
    // ============================================
    
    $wp_customize->add_setting('asap_dark_mode_colors_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_colors_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Colores de Fondo', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 30,
    )));

    // Fondo primario
    $wp_customize->add_setting('asap_dark_mode_bg_primary', array(
        'default'           => '#121212',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_bg_primary', array(
        'label'       => __('Fondo Principal', 'asap'),
        'description' => __('Color de fondo principal del sitio.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 31,
    )));

    // Fondo secundario
    $wp_customize->add_setting('asap_dark_mode_bg_secondary', array(
        'default'           => '#1a1a1a',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_bg_secondary', array(
        'label'       => __('Fondo Secundario', 'asap'),
        'description' => __('Para artículos, cajas y elementos secundarios.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 32,
    )));

    // Fondo terciario
    $wp_customize->add_setting('asap_dark_mode_bg_tertiary', array(
        'default'           => '#1e1e1e',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_bg_tertiary', array(
        'label'       => __('Fondo Terciario', 'asap'),
        'description' => __('Para formularios, inputs y elementos terciarios.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 33,
    )));

    // ============================================
    // COLORES DE TEXTO
    // ============================================
    
    $wp_customize->add_setting('asap_dark_mode_text_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_text_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Colores de Texto', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 40,
    )));

    // Texto primario
    $wp_customize->add_setting('asap_dark_mode_text_primary', array(
        'default'           => '#f1f1f1',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_text_primary', array(
        'label'       => __('Texto Principal', 'asap'),
        'description' => __('Color del texto principal.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 41,
    )));

    // Texto secundario
    $wp_customize->add_setting('asap_dark_mode_text_secondary', array(
        'default'           => '#e0e0e0',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_text_secondary', array(
        'label'       => __('Texto Secundario', 'asap'),
        'description' => __('Para párrafos, listas y texto secundario.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 42,
    )));

    // ============================================
    // COLORES DE ENCABEZADOS
    // ============================================
    
    $wp_customize->add_setting('asap_dark_mode_headings_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_headings_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Colores de Encabezados', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 50,
    )));

    // H1
    $wp_customize->add_setting('asap_dark_mode_heading_h1', array(
        'default'           => '#FDBE02',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_heading_h1', array(
        'label'       => __('Color H1', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 51,
    )));

    // H2
    $wp_customize->add_setting('asap_dark_mode_heading_h2', array(
        'default'           => '#FF8040',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_heading_h2', array(
        'label'       => __('Color H2', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 52,
    )));

    // H3-H6
    $wp_customize->add_setting('asap_dark_mode_heading_other', array(
        'default'           => '#FDBE02',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_heading_other', array(
        'label'       => __('Color H3-H6', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 53,
    )));

    // ============================================
    // COLORES DE ENLACES
    // ============================================
    
    $wp_customize->add_setting('asap_dark_mode_links_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_links_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Colores de Enlaces', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 60,
    )));

    // Color de enlace
    $wp_customize->add_setting('asap_dark_mode_link_color', array(
        'default'           => '#80cbc4',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_link_color', array(
        'label'       => __('Color de Enlaces', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 61,
    )));

    // Color de enlace hover
    $wp_customize->add_setting('asap_dark_mode_link_hover', array(
        'default'           => '#4dd0e1',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_link_hover', array(
        'label'       => __('Color de Enlaces (Hover)', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 62,
    )));

    // ============================================
    // COLORES ADICIONALES
    // ============================================
    
    $wp_customize->add_setting('asap_dark_mode_extra_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_extra_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Colores Adicionales', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 70,
    )));

    // Color de acento
    $wp_customize->add_setting('asap_dark_mode_accent_color', array(
        'default'           => '#80cbc4',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_accent_color', array(
        'label'       => __('Color de Acento', 'asap'),
        'description' => __('Para botones, elementos destacados, etc.', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 71,
    )));

    // Color de borde
    $wp_customize->add_setting('asap_dark_mode_border_color', array(
        'default'           => '#444444',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_border_color', array(
        'label'       => __('Color de Bordes', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 72,
    )));

    // ============================================
    // BOTÓN DE TOGGLE
    // ============================================
    
    $wp_customize->add_setting('asap_dark_mode_button_separator', array(
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'asap_dark_mode_button_separator', array(
        'label' => '<hr style="border-top: 2px solid #ddd; margin: 20px 0;"><h3 style="margin: 10px 0;">' . __('Estilo del Botón Flotante', 'asap') . '</h3>',
        'section' => 'asap_dark_mode_section',
        'type' => 'hidden',
        'priority' => 80,
    )));

    // Color de fondo del botón
    $wp_customize->add_setting('asap_dark_mode_button_bg', array(
        'default'           => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_button_bg', array(
        'label'       => __('Fondo del Botón', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 81,
    )));

    // Color de texto del botón
    $wp_customize->add_setting('asap_dark_mode_button_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asap_dark_mode_button_text_color', array(
        'label'       => __('Color de Texto del Botón', 'asap'),
        'section'     => 'asap_dark_mode_section',
        'priority'    => 82,
    )));
}
// TODO: Implementar en versión futura
// add_action('customize_register', 'asap_dark_mode_customizer');

