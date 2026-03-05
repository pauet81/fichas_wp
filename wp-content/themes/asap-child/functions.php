<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        if ( !file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/css/main.min.css' ) ):
            wp_deregister_style( 'asap-style' );
            wp_register_style( 'asap-style', trailingslashit( get_template_directory_uri() ) . 'assets/css/main.min.css' );
        endif;
        $child_style_path = trailingslashit( get_stylesheet_directory() ) . 'style.css';
        $child_style_ver = file_exists( $child_style_path ) ? filemtime( $child_style_path ) : null;
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'asap-style' ), $child_style_ver );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// Ensure megamenu styles are loaded when enabled.
function fichas_megamenu_styles() {
    if ( get_option( 'asap_megamenu_enabled', '0' ) !== '1' ) {
        return;
    }

    if ( wp_style_is( 'asap-megamenu', 'enqueued' ) || wp_style_is( 'asap-megamenu', 'registered' ) ) {
        return;
    }

    $megamenu_css_path = get_template_directory() . '/assets/css/megamenu.css';
    $megamenu_css_ver = file_exists( $megamenu_css_path ) ? filemtime( $megamenu_css_path ) : null;
    wp_enqueue_style(
        'asap-megamenu',
        get_template_directory_uri() . '/assets/css/megamenu.css',
        array( 'asap-style' ),
        $megamenu_css_ver
    );
}
add_action( 'wp_enqueue_scripts', 'fichas_megamenu_styles', 15 );

// END ENQUEUE PARENT ACTION
// ============================================================================
// CÓDIGO PERSONALIZADO PARA FICHAS - AÑADIR AL FINAL DEL FUNCTIONS.PHP
// ============================================================================

// Encolar CSS personalizado para fichas
function fichas_custom_styles() {
    $template_slug = is_page() ? get_page_template_slug() : '';
    $is_hub = (
        is_page_template('page-hub-generic.php') ||
        is_page_template('page-hub-nivel-infantil.php') ||
        is_page_template('page-hub-nivel-primaria.php') ||
        is_page_template('page-hub-curso.php')
    );
    if (!$is_hub && $template_slug) {
        $is_hub = in_array($template_slug, array(
            'page-hub-generic.php',
            'page-hub-nivel-infantil.php',
            'page-hub-nivel-primaria.php',
            'page-hub-curso.php'
        ), true);
    }
    $is_single_ficha = is_singular('ficha');

    // Evitar duplicados con WPCode (handles: fichas-css / front-page-css / fichas-base)
    if (!wp_style_is('fichas-css', 'enqueued') && !wp_style_is('fichas-css', 'registered')) {
        $fichas_css_path = get_stylesheet_directory() . '/css/fichas.css';
        $fichas_css_ver = file_exists( $fichas_css_path ) ? filemtime( $fichas_css_path ) : '1.0.0';
        wp_enqueue_style( 
            'fichas-custom-style',
            get_stylesheet_directory_uri() . '/css/fichas.css',
            array(),
            $fichas_css_ver
        );
    }

    if (is_front_page()) {
        if (!wp_style_is('front-page-css', 'enqueued') && !wp_style_is('front-page-css', 'registered')) {
            $front_css_path = get_stylesheet_directory() . '/css/front-page.css';
            $front_css_ver = file_exists( $front_css_path ) ? filemtime( $front_css_path ) : '1.0.0';
            wp_enqueue_style(
                'front-page-custom-style',
                get_stylesheet_directory_uri() . '/css/front-page.css',
                array('fichas-custom-style'),
                $front_css_ver
            );
        }
    }

    $post_slug = '';
    $parent_slug = '';
    if (is_page()) {
        global $post;
        if ($post) {
            $post_slug = get_post_field('post_name', $post->ID);
            $parent_id = wp_get_post_parent_id($post->ID);
            $parent_slug = $parent_id ? get_post_field('post_name', $parent_id) : '';
        }
    }

    if ($is_hub || is_page() || ($post_slug === 'tematicas' && $parent_slug === 'infantil')) {
        $hub_css_path = get_stylesheet_directory() . '/css/hub-nivel.css';
        $hub_css_ver = file_exists( $hub_css_path ) ? filemtime( $hub_css_path ) : '1.0.0';
        wp_enqueue_style(
            'hub-nivel-custom-style',
            get_stylesheet_directory_uri() . '/css/hub-nivel.css',
            array('fichas-custom-style'),
            $hub_css_ver
        );
    }

    if (!wp_script_is('fichas-base', 'enqueued') && !wp_script_is('fichas-base', 'registered')) {
        $fichas_js_path = get_stylesheet_directory() . '/js/fichas.js';
        $fichas_js_ver = file_exists( $fichas_js_path ) ? filemtime( $fichas_js_path ) : '1.0.0';
        wp_enqueue_script(
            'fichas-custom-js',
            get_stylesheet_directory_uri() . '/js/fichas.js',
            array(),
            $fichas_js_ver,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'fichas_custom_styles', 20 );

// Desactivar megamenu del tema (CSS/JS) para usar el nuestro
function fichas_disable_theme_megamenu_assets() {
    if (!is_admin()) {
        wp_dequeue_style('asap-megamenu');
        wp_deregister_style('asap-megamenu');
        wp_dequeue_script('asap-megamenu');
        wp_deregister_script('asap-megamenu');
    }
}
add_action('wp_enqueue_scripts', 'fichas_disable_theme_megamenu_assets', 100);

// Quitar filtros del megamenu del tema para evitar HTML inyectado
function fichas_disable_theme_megamenu_filters() {
    if (class_exists('ASAP_Megamenu')) {
        $instance = ASAP_Megamenu::instance();
        remove_filter('nav_menu_css_class', [$instance, 'nav_menu_classes'], 10);
        remove_filter('walker_nav_menu_start_el', [$instance, 'nav_menu_start_el'], 10);
        remove_filter('body_class', [$instance, 'add_megamenu_body_class']);
    }
}
add_action('after_setup_theme', 'fichas_disable_theme_megamenu_filters', 20);

// Eliminar cualquier HTML del megamenu del tema inyectado en frontend
function fichas_strip_theme_megamenu_html($html) {
    if (is_admin()) return $html;
    $patterns = [
        '/<div[^>]*class="[^"]*asap-megamenu-overlay[^"]*"[^>]*>.*?<\\/div>/s',
        '/<div[^>]*class="[^"]*asap-megamenu-trigger[^"]*"[^>]*>.*?<\\/div>/s',
        '/<div[^>]*class="[^"]*asap-megamenu-container[^"]*"[^>]*>.*?<\\/div>/s'
    ];
    return preg_replace($patterns, '', $html);
}

function fichas_start_buffer_megamenu_strip() {
    if (!is_admin()) {
        ob_start('fichas_strip_theme_megamenu_html');
    }
}

function fichas_end_buffer_megamenu_strip() {
    if (!is_admin() && ob_get_length()) {
        ob_end_flush();
    }
}

add_action('init', 'fichas_start_buffer_megamenu_strip', 1);
add_action('shutdown', 'fichas_end_buffer_megamenu_strip', 0);

// Permitir HTML y JavaScript sin filtros en campos ACF
// Necesario para el campo contenido_ficha_html
add_filter( 'acf/the_field/allow_unsafe_html', '__return_true' );

// Fin del código personalizado para Fichas

// ============================================================================
// SISTEMA DE ACTIVIDAD DE USUARIOS (ADMIN)
// ============================================================================

function fichas_activity_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'fichas_activity';
}

function fichas_activity_install() {
    global $wpdb;
    $table = fichas_activity_table_name();
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
        action VARCHAR(50) NOT NULL,
        object_type VARCHAR(50) NOT NULL,
        object_id BIGINT UNSIGNED NULL,
        object_title TEXT NULL,
        details LONGTEXT NULL,
        severity VARCHAR(20) NOT NULL DEFAULT 'info',
        ip VARCHAR(45) NULL,
        user_agent TEXT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY action (action),
        KEY object_type (object_type),
        KEY severity (severity),
        KEY created_at (created_at)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('fichas_activity_db_version', '1.0');
}

add_action('admin_init', 'fichas_activity_install');

function fichas_activity_get_ip() {
    $keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR');
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = explode(',', $_SERVER[$key])[0];
            return trim($ip);
        }
    }
    return '';
}

function fichas_log_activity($action, $object_type, $object_id = null, $object_title = '', $details = array(), $severity = 'info') {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($object_id)) return;

    global $wpdb;
    $table = fichas_activity_table_name();
    $user = wp_get_current_user();
    $user_id = $user && $user->ID ? (int) $user->ID : 0;
    $ip = fichas_activity_get_ip();
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 250) : '';

    $wpdb->insert(
        $table,
        array(
            'user_id' => $user_id,
            'action' => sanitize_text_field($action),
            'object_type' => sanitize_text_field($object_type),
            'object_id' => $object_id ? (int) $object_id : null,
            'object_title' => $object_title ? wp_strip_all_tags($object_title) : '',
            'details' => !empty($details) ? wp_json_encode($details) : null,
            'severity' => sanitize_text_field($severity),
            'ip' => $ip,
            'user_agent' => $ua,
            'created_at' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
    );
}

// Posts/Pages/Fichas create/update/delete
function fichas_activity_on_save_post($post_id, $post, $update) {
    if (!$post || $post->post_status === 'auto-draft') return;
    if (wp_is_post_revision($post_id)) return;

    $allowed = array('post', 'page', 'ficha');
    if (!in_array($post->post_type, $allowed, true)) return;

    $action = $update ? 'update' : 'create';
    $template = $post->post_type === 'page' ? get_post_meta($post_id, '_wp_page_template', true) : '';
    $is_hub = in_array($template, array(
        'page-hub-generic.php',
        'page-hub-nivel-infantil.php',
        'page-hub-nivel-primaria.php',
        'page-hub-curso.php'
    ), true);
    $severity = $is_hub ? 'warn' : 'info';
    fichas_log_activity($action, $post->post_type, $post_id, $post->post_title, array(
        'status' => $post->post_status,
        'template' => $template
    ), $severity);
}
add_action('save_post', 'fichas_activity_on_save_post', 10, 3);

function fichas_activity_on_delete_post($post_id) {
    $post = get_post($post_id);
    if (!$post) return;
    $allowed = array('post', 'page', 'ficha');
    if (!in_array($post->post_type, $allowed, true)) return;
    fichas_log_activity('delete', $post->post_type, $post_id, $post->post_title, array(), 'critical');
}
add_action('before_delete_post', 'fichas_activity_on_delete_post');

// Media upload
function fichas_activity_on_add_attachment($post_id) {
    $post = get_post($post_id);
    if (!$post) return;
    fichas_log_activity('upload', 'media', $post_id, $post->post_title);
}
add_action('add_attachment', 'fichas_activity_on_add_attachment');

// Login / Logout
function fichas_activity_on_login($user_login, $user) {
    if (!$user) return;
    fichas_log_activity('login', 'user', $user->ID, $user_login);
}
add_action('wp_login', 'fichas_activity_on_login', 10, 2);

function fichas_activity_on_logout() {
    $user = wp_get_current_user();
    if ($user && $user->ID) {
        fichas_log_activity('logout', 'user', $user->ID, $user->user_login);
    }
}
add_action('wp_logout', 'fichas_activity_on_logout');

// Users
function fichas_activity_on_user_register($user_id) {
    $user = get_user_by('id', $user_id);
    if ($user) {
        fichas_log_activity('user_create', 'user', $user_id, $user->user_login, array(), 'warn');
    }
}
add_action('user_register', 'fichas_activity_on_user_register');

function fichas_activity_on_profile_update($user_id, $old_user_data) {
    $user = get_user_by('id', $user_id);
    if ($user) {
        fichas_log_activity('user_update', 'user', $user_id, $user->user_login, array(), 'info');
    }
}
add_action('profile_update', 'fichas_activity_on_profile_update', 10, 2);

function fichas_activity_on_delete_user($user_id) {
    $user = get_user_by('id', $user_id);
    $name = $user ? $user->user_login : 'user';
    fichas_log_activity('user_delete', 'user', $user_id, $name, array(), 'critical');
}
add_action('delete_user', 'fichas_activity_on_delete_user');

// Menus
function fichas_activity_on_menu_update($menu_id) {
    $menu = wp_get_nav_menu_object($menu_id);
    $title = $menu ? $menu->name : 'Menu';
    fichas_log_activity('menu_update', 'menu', $menu_id, $title, array(), 'warn');
}
add_action('wp_update_nav_menu', 'fichas_activity_on_menu_update');

// Plugins
function fichas_activity_on_plugin_change($plugin) {
    $action = current_filter() === 'activated_plugin' ? 'plugin_activate' : 'plugin_deactivate';
    fichas_log_activity($action, 'plugin', null, $plugin, array(), 'critical');
}
add_action('activated_plugin', 'fichas_activity_on_plugin_change');
add_action('deactivated_plugin', 'fichas_activity_on_plugin_change');

// Post status transitions
function fichas_activity_on_status_transition($new_status, $old_status, $post) {
    if (!$post || $new_status === $old_status) return;
    $allowed = array('post', 'page', 'ficha');
    if (!in_array($post->post_type, $allowed, true)) return;
    fichas_log_activity('status_change', $post->post_type, $post->ID, $post->post_title, array(
        'from' => $old_status,
        'to' => $new_status
    ), 'info');
}
add_action('transition_post_status', 'fichas_activity_on_status_transition', 10, 3);

// Comments
function fichas_activity_on_comment_insert($comment_id, $comment) {
    if (!$comment) return;
    fichas_log_activity('comment_create', 'comment', $comment_id, '', array(
        'post_id' => $comment->comment_post_ID,
        'status' => $comment->comment_approved
    ), 'info');
}
add_action('wp_insert_comment', 'fichas_activity_on_comment_insert', 10, 2);

function fichas_activity_on_comment_status($comment_id, $comment_status) {
    $comment = get_comment($comment_id);
    if (!$comment) return;
    fichas_log_activity('comment_status', 'comment', $comment_id, '', array(
        'post_id' => $comment->comment_post_ID,
        'status' => $comment_status
    ), 'warn');
}
add_action('wp_set_comment_status', 'fichas_activity_on_comment_status', 10, 2);

function fichas_activity_on_comment_delete($comment_id) {
    $comment = get_comment($comment_id);
    if (!$comment) return;
    fichas_log_activity('comment_delete', 'comment', $comment_id, '', array(
        'post_id' => $comment->comment_post_ID
    ), 'critical');
}
add_action('delete_comment', 'fichas_activity_on_comment_delete');

// Taxonomies (categories, tags, custom)
function fichas_activity_on_term_create($term_id, $tt_id, $taxonomy) {
    $term = get_term($term_id, $taxonomy);
    if (!$term || is_wp_error($term)) return;
    fichas_log_activity('term_create', 'term', $term_id, $term->name, array(
        'taxonomy' => $taxonomy
    ), 'info');
}
add_action('created_term', 'fichas_activity_on_term_create', 10, 3);

function fichas_activity_on_term_edit($term_id, $tt_id, $taxonomy) {
    $term = get_term($term_id, $taxonomy);
    if (!$term || is_wp_error($term)) return;
    fichas_log_activity('term_update', 'term', $term_id, $term->name, array(
        'taxonomy' => $taxonomy
    ), 'info');
}
add_action('edited_term', 'fichas_activity_on_term_edit', 10, 3);

function fichas_activity_on_term_delete($term, $tt_id, $taxonomy, $deleted_term) {
    if (!$deleted_term || is_wp_error($deleted_term)) return;
    $name = isset($deleted_term->name) ? $deleted_term->name : '';
    fichas_log_activity('term_delete', 'term', (int) $term, $name, array(
        'taxonomy' => $taxonomy
    ), 'warn');
}
add_action('delete_term', 'fichas_activity_on_term_delete', 10, 4);

// Media delete
function fichas_activity_on_delete_attachment($post_id) {
    $post = get_post($post_id);
    if (!$post) return;
    fichas_log_activity('upload_delete', 'media', $post_id, $post->post_title, array(), 'warn');
}
add_action('delete_attachment', 'fichas_activity_on_delete_attachment');

// ACF saves
function fichas_activity_on_acf_save($post_id) {
    $title = '';
    if (is_numeric($post_id)) {
        $post = get_post((int) $post_id);
        $title = $post ? $post->post_title : '';
    }
    fichas_log_activity('acf_save', 'acf', is_numeric($post_id) ? (int) $post_id : null, $title, array(
        'acf_post_id' => $post_id
    ), 'info');
}
add_action('acf/save_post', 'fichas_activity_on_acf_save', 20);

// WP Import
function fichas_activity_on_import_start() {
    fichas_log_activity('import_start', 'import', null, 'WP Import', array(), 'warn');
}
add_action('import_start', 'fichas_activity_on_import_start');

function fichas_activity_on_import_end() {
    fichas_log_activity('import_end', 'import', null, 'WP Import', array(), 'warn');
}
add_action('import_end', 'fichas_activity_on_import_end');

// Dashboard widgets
function fichas_register_activity_widgets() {
    wp_add_dashboard_widget('fichas_activity_overview', 'Actividad reciente', 'fichas_render_activity_overview');
}
add_action('wp_dashboard_setup', 'fichas_register_activity_widgets');

function fichas_render_activity_overview() {
    global $wpdb;
    $table = fichas_activity_table_name();

    $today = date('Y-m-d');
    $count_today = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table} WHERE DATE(created_at) = %s",
        $today
    ));
    $users_today = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT user_id) FROM {$table} WHERE DATE(created_at) = %s",
        $today
    ));

    $top_users = $wpdb->get_results(
        "SELECT user_id, COUNT(*) as total
         FROM {$table}
         WHERE created_at >= (NOW() - INTERVAL 7 DAY)
         GROUP BY user_id
         ORDER BY total DESC
         LIMIT 5"
    );

    $latest = $wpdb->get_results(
        "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 6"
    );
    $alerts = $wpdb->get_results(
        "SELECT * FROM {$table} WHERE severity IN ('critical','warn') ORDER BY created_at DESC LIMIT 6"
    );

    echo '<div class="fichas-activity-cards">';
    echo '<div class="fichas-activity-card">';
    echo '<span class="label">Actividad hoy</span>';
    echo '<strong>' . esc_html($count_today) . ' acciones</strong>';
    echo '<span class="muted">' . esc_html($users_today) . ' usuarios</span>';
    echo '</div>';

    echo '<div class="fichas-activity-card">';
    echo '<span class="label">Usuarios mas activos (7 dias)</span>';
    if ($top_users) {
        foreach ($top_users as $row) {
            $user = get_user_by('id', $row->user_id);
            $name = $user ? $user->display_name : 'Desconocido';
            echo '<div class="mini-row"><span>' . esc_html($name) . '</span><span class="badge">' . esc_html($row->total) . '</span></div>';
        }
    } else {
        echo '<span class="muted">Sin datos</span>';
    }
    echo '</div>';

    echo '<div class="fichas-activity-card full">';
    echo '<span class="label">Ultimas acciones</span>';
    if ($latest) {
        echo '<ul class="fichas-activity-list">';
        foreach ($latest as $row) {
            $user = $row->user_id ? get_user_by('id', $row->user_id) : null;
            $name = $user ? $user->display_name : 'Sistema';
            $time = mysql2date('H:i', $row->created_at);
            $title = $row->object_title ? $row->object_title : $row->object_type;
            echo '<li><span class="pill action-' . esc_attr($row->action) . '">' . esc_html($row->action) . '</span> ';
            echo esc_html($name) . ' · ' . esc_html($title) . ' · ' . esc_html($time) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<span class="muted">Sin actividad registrada todavia.</span>';
    }
    echo '</div>';

    echo '<div class="fichas-activity-card full">';
    echo '<span class="label">Alertas recientes</span>';
    if ($alerts) {
        echo '<ul class="fichas-activity-list">';
        foreach ($alerts as $row) {
            $user = $row->user_id ? get_user_by('id', $row->user_id) : null;
            $name = $user ? $user->display_name : 'Sistema';
            $time = mysql2date('H:i', $row->created_at);
            $title = $row->object_title ? $row->object_title : $row->object_type;
            echo '<li><span class="pill sev-' . esc_attr($row->severity) . '">' . esc_html($row->severity) . '</span> ';
            echo esc_html($name) . ' · ' . esc_html($row->action) . ' · ' . esc_html($title) . ' · ' . esc_html($time) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<span class="muted">Sin alertas.</span>';
    }
    echo '</div>';
    echo '</div>';
}

// Admin page: Actividad
function fichas_register_activity_page() {
    add_menu_page(
        'Actividad',
        'Actividad',
        'manage_options',
        'fichas-activity',
        'fichas_render_activity_page',
        'dashicons-activity',
        3
    );
}
add_action('admin_menu', 'fichas_register_activity_page');

function fichas_render_activity_page() {
    global $wpdb;
    $table = fichas_activity_table_name();

    $user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
    $action = isset($_GET['action_type']) ? sanitize_text_field($_GET['action_type']) : '';
    $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

    $where = "WHERE 1=1";
    $params = array();
    if ($user_id) {
        $where .= " AND user_id = %d";
        $params[] = $user_id;
    }
    if ($action) {
        $where .= " AND action = %s";
        $params[] = $action;
    }
    if ($date_from) {
        $where .= " AND DATE(created_at) >= %s";
        $params[] = $date_from;
    }
    if ($date_to) {
        $where .= " AND DATE(created_at) <= %s";
        $params[] = $date_to;
    }

    if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
        $from = $date_from ? $date_from : date('Y-m-d', strtotime('-7 days'));
        $to = $date_to ? $date_to : date('Y-m-d');
        $where_pdf = "WHERE DATE(created_at) BETWEEN %s AND %s";
        $params_pdf = array($from, $to);
        $user_filter = '';
        if ($user_id) {
            $where_pdf .= " AND user_id = %d";
            $params_pdf[] = $user_id;
        }
        if ($action) {
            $where_pdf .= " AND action = %s";
            $params_pdf[] = $action;
        }

        $sql = "SELECT * FROM {$table} {$where_pdf} ORDER BY created_at DESC";
        $report_rows = $wpdb->get_results($wpdb->prepare($sql, $params_pdf));

        $summary = $wpdb->get_results($wpdb->prepare(
            "SELECT user_id, action, COUNT(*) as total
             FROM {$table}
             {$where_pdf}
             GROUP BY user_id, action
             ORDER BY user_id ASC",
            $params_pdf
        ));

        $summary_by_user = array();
        foreach ($summary as $row) {
            if (!isset($summary_by_user[$row->user_id])) $summary_by_user[$row->user_id] = array();
            $summary_by_user[$row->user_id][$row->action] = (int) $row->total;
        }

        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Reporte semanal</title>';
        echo '<style>
        body{font-family:Arial,sans-serif;color:#0f172a;margin:24px;}
        h1{margin:0 0 6px;font-size:24px;}
        h2{margin:24px 0 8px;font-size:18px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;}
        .meta{color:#64748b;font-size:12px;margin-bottom:16px;}
        table{width:100%;border-collapse:collapse;font-size:12px;}
        th,td{border:1px solid #e2e8f0;padding:6px 8px;text-align:left;vertical-align:top;}
        th{background:#f8fafc;}
        .pill{display:inline-block;padding:2px 6px;border-radius:999px;background:#e2e8f0;font-size:10px;text-transform:uppercase;letter-spacing:.06em;}
        .small{font-size:11px;color:#475569;}
        </style></head><body>';
        echo '<h1>Reporte semanal de actividad</h1>';
        echo '<div class="meta">Periodo: ' . esc_html($from) . ' a ' . esc_html($to) . '</div>';

        echo '<h2>Resumen por usuario</h2>';
        echo '<table><thead><tr><th>Usuario</th><th>Acciones</th><th>Total</th></tr></thead><tbody>';
        if ($summary_by_user) {
            foreach ($summary_by_user as $uid => $actions_arr) {
                $user = $uid ? get_user_by('id', $uid) : null;
                $name = $user ? $user->display_name : 'Sistema';
                $total = array_sum($actions_arr);
                $parts = array();
                foreach ($actions_arr as $act => $cnt) {
                    $parts[] = $act . ': ' . $cnt;
                }
                echo '<tr><td>' . esc_html($name) . '</td><td>' . esc_html(implode(' | ', $parts)) . '</td><td>' . esc_html($total) . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="3">Sin datos</td></tr>';
        }
        echo '</tbody></table>';

        echo '<h2>Detalle de acciones</h2>';
        echo '<table><thead><tr><th>Fecha</th><th>Usuario</th><th>Accion</th><th>Objeto</th><th>Detalle</th></tr></thead><tbody>';
        if ($report_rows) {
            foreach ($report_rows as $row) {
                $user = $row->user_id ? get_user_by('id', $row->user_id) : null;
                $name = $user ? $user->display_name : 'Sistema';
                $detail = $row->object_title ? $row->object_title : $row->object_type;
                echo '<tr>';
                echo '<td>' . esc_html(mysql2date('d/m/Y H:i', $row->created_at)) . '</td>';
                echo '<td>' . esc_html($name) . '</td>';
                echo '<td><span class="pill">' . esc_html($row->action) . '</span></td>';
                echo '<td>' . esc_html($row->object_type) . '</td>';
                echo '<td>' . esc_html($detail) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">Sin datos</td></tr>';
        }
        echo '</tbody></table>';
        echo '<script>window.print();</script></body></html>';
        exit;
    }

    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        $sql = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT 2000";
        $export_rows = $params ? $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A) : $wpdb->get_results($sql, ARRAY_A);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=actividad-fichas.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, array('fecha', 'usuario_id', 'accion', 'objeto', 'objeto_id', 'titulo', 'severidad', 'ip'));
        foreach ($export_rows as $row) {
            fputcsv($out, array(
                $row['created_at'],
                $row['user_id'],
                $row['action'],
                $row['object_type'],
                $row['object_id'],
                $row['object_title'],
                $row['severity'],
                $row['ip']
            ));
        }
        fclose($out);
        exit;
    }

    $sql = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT 200";
    $rows = $params ? $wpdb->get_results($wpdb->prepare($sql, $params)) : $wpdb->get_results($sql);

    $users = get_users(array('fields' => array('ID', 'display_name')));
    $actions = $wpdb->get_col("SELECT DISTINCT action FROM {$table} ORDER BY action ASC");

    $actions_by_user = $wpdb->get_results(
        "SELECT user_id, COUNT(*) as total
         FROM {$table}
         WHERE created_at >= (NOW() - INTERVAL 7 DAY)
         GROUP BY user_id
         ORDER BY total DESC
         LIMIT 8"
    );
    $actions_by_type = $wpdb->get_results(
        "SELECT action, COUNT(*) as total
         FROM {$table}
         WHERE created_at >= (NOW() - INTERVAL 7 DAY)
         GROUP BY action
         ORDER BY total DESC
         LIMIT 8"
    );
    $actions_by_day = $wpdb->get_results(
        "SELECT DATE(created_at) as day, COUNT(*) as total
         FROM {$table}
         WHERE created_at >= (NOW() - INTERVAL 14 DAY)
         GROUP BY day
         ORDER BY day ASC"
    );
    $latest_by_user = $wpdb->get_results(
        "SELECT t1.*
         FROM {$table} t1
         INNER JOIN (
             SELECT user_id, MAX(created_at) AS max_date
             FROM {$table}
             WHERE user_id > 0
             GROUP BY user_id
         ) t2 ON t1.user_id = t2.user_id AND t1.created_at = t2.max_date
         ORDER BY t1.created_at DESC
         LIMIT 50"
    );

    echo '<div class="wrap"><h1>Actividad</h1>';
    echo '<form class="fichas-activity-filters" method="get">';
    echo '<input type="hidden" name="page" value="fichas-activity" />';
    echo '<select name="user_id">';
    echo '<option value="0">Todos los usuarios</option>';
    foreach ($users as $u) {
        printf('<option value="%d"%s>%s</option>', $u->ID, selected($user_id, $u->ID, false), esc_html($u->display_name));
    }
    echo '</select>';
    echo '<select name="action_type">';
    echo '<option value="">Todas las acciones</option>';
    foreach ($actions as $act) {
        printf('<option value="%s"%s>%s</option>', esc_attr($act), selected($action, $act, false), esc_html($act));
    }
    echo '</select>';
    echo '<input type="date" name="date_from" value="' . esc_attr($date_from) . '" />';
    echo '<input type="date" name="date_to" value="' . esc_attr($date_to) . '" />';
    echo '<button class="button button-primary">Aplicar</button>';
    echo '<a class="button" href="' . esc_url(add_query_arg('export','csv')) . '">Exportar CSV</a>';
    echo '<a class="button" href="' . esc_url(add_query_arg('export','pdf')) . '">Reporte semanal PDF</a>';
    echo '</form>';

    if ($user_id) {
        $user_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT action, COUNT(*) as total FROM {$table} WHERE user_id = %d GROUP BY action ORDER BY total DESC LIMIT 8",
            $user_id
        ));
        $user_total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE user_id = %d",
            $user_id
        ));
        $user_info = get_user_by('id', $user_id);
        echo '<div class="fichas-activity-card full fichas-user-summary">';
        echo '<span class="label">Resumen de usuario</span>';
        if ($user_info) {
            echo '<strong>' . esc_html($user_info->display_name) . '</strong>';
        }
        echo '<span class="muted">Total acciones: ' . esc_html($user_total) . '</span>';
        if ($user_stats) {
            echo '<div class="fichas-bar-chart">';
            $max_stat = 0;
            foreach ($user_stats as $r) { if ((int)$r->total > $max_stat) $max_stat = (int)$r->total; }
            if ($max_stat < 1) $max_stat = 1;
            foreach ($user_stats as $r) {
                $width = (int) round(((int) $r->total / $max_stat) * 100);
                echo '<div class="bar-row"><span class="bar-label">' . esc_html($r->action) . '</span>';
                echo '<span class="bar-track"><span class="bar-fill" style="width:' . esc_attr($width) . '%"></span></span>';
                echo '<span class="bar-value">' . esc_html($r->total) . '</span></div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    $max_user = 0;
    foreach ($actions_by_user as $row) {
        if ((int) $row->total > $max_user) $max_user = (int) $row->total;
    }
    $max_type = 0;
    foreach ($actions_by_type as $row) {
        if ((int) $row->total > $max_type) $max_type = (int) $row->total;
    }
    $max_day = 0;
    foreach ($actions_by_day as $row) {
        if ((int) $row->total > $max_day) $max_day = (int) $row->total;
    }
    if ($max_user < 1) $max_user = 1;
    if ($max_type < 1) $max_type = 1;
    if ($max_day < 1) $max_day = 1;

    echo '<div class="fichas-activity-charts">';
    echo '<div class="fichas-activity-card">';
    echo '<span class="label">Usuarios mas activos (7 dias)</span>';
    if ($actions_by_user) {
        echo '<div class="fichas-bar-chart">';
        foreach ($actions_by_user as $row) {
            $user = $row->user_id ? get_user_by('id', $row->user_id) : null;
            $name = $user ? $user->display_name : 'Sistema';
            $width = (int) round(((int) $row->total / $max_user) * 100);
            echo '<div class="bar-row"><span class="bar-label">' . esc_html($name) . '</span>';
            echo '<span class="bar-track"><span class="bar-fill" style="width:' . esc_attr($width) . '%"></span></span>';
            echo '<span class="bar-value">' . esc_html($row->total) . '</span></div>';
        }
        echo '</div>';
    } else {
        echo '<span class="muted">Sin datos</span>';
    }
    echo '</div>';

    echo '<div class="fichas-activity-card full">';
    echo '<span class="label">Ultima accion por usuario</span>';
    if ($latest_by_user) {
        echo '<ul class="fichas-activity-list">';
        foreach ($latest_by_user as $row) {
            $user = $row->user_id ? get_user_by('id', $row->user_id) : null;
            $name = $user ? $user->display_name : 'Sistema';
            $time = mysql2date('d/m H:i', $row->created_at);
            $title = $row->object_title ? $row->object_title : $row->object_type;
            echo '<li><strong>' . esc_html($name) . '</strong> · ';
            echo '<span class="pill action-' . esc_attr($row->action) . '">' . esc_html($row->action) . '</span> ';
            echo esc_html($title) . ' · ' . esc_html($time) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<span class="muted">Sin datos</span>';
    }
    echo '</div>';

    echo '<div class="fichas-activity-card">';
    echo '<span class="label">Acciones mas frecuentes (7 dias)</span>';
    if ($actions_by_type) {
        echo '<div class="fichas-bar-chart">';
        foreach ($actions_by_type as $row) {
            $width = (int) round(((int) $row->total / $max_type) * 100);
            echo '<div class="bar-row"><span class="bar-label">' . esc_html($row->action) . '</span>';
            echo '<span class="bar-track"><span class="bar-fill is-accent" style="width:' . esc_attr($width) . '%"></span></span>';
            echo '<span class="bar-value">' . esc_html($row->total) . '</span></div>';
        }
        echo '</div>';
    } else {
        echo '<span class="muted">Sin datos</span>';
    }
    echo '</div>';

    echo '<div class="fichas-activity-card full">';
    echo '<span class="label">Actividad por dia (ultimos 14 dias)</span>';
    if ($actions_by_day) {
        echo '<div class="fichas-sparkline">';
        foreach ($actions_by_day as $row) {
            $height = (int) round(((int) $row->total / $max_day) * 100);
            $day = esc_html(mysql2date('d/m', $row->day));
            echo '<div class="spark-col" title="' . esc_attr($day . ' · ' . $row->total) . '">';
            echo '<span class="spark-bar" style="height:' . esc_attr($height) . '%"></span>';
            echo '<span class="spark-label">' . $day . '</span>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<span class="muted">Sin datos</span>';
    }
    echo '</div>';
    echo '</div>';

    echo '<table class="widefat fixed striped fichas-activity-table">';
    echo '<thead><tr><th>Fecha</th><th>Usuario</th><th>Accion</th><th>Objeto</th><th>Detalle</th></tr></thead><tbody>';
    if ($rows) {
        foreach ($rows as $row) {
            $user = $row->user_id ? get_user_by('id', $row->user_id) : null;
            $name = $user ? $user->display_name : 'Sistema';
            $detail = $row->object_title ? $row->object_title : $row->object_type;
            echo '<tr>';
            echo '<td>' . esc_html(mysql2date('d/m/Y H:i', $row->created_at)) . '</td>';
            echo '<td>' . esc_html($name) . '</td>';
            echo '<td><span class="pill action-' . esc_attr($row->action) . '">' . esc_html($row->action) . '</span></td>';
            echo '<td>' . esc_html($row->object_type) . '</td>';
            echo '<td>' . esc_html($detail) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">Sin actividad registrada.</td></tr>';
    }
    echo '</tbody></table></div>';
}

function fichas_activity_admin_styles($hook) {
    if ($hook !== 'index.php' && $hook !== 'toplevel_page_fichas-activity') return;
    $css = '
    .fichas-activity-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin:10px 0 0;}
    .fichas-activity-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;box-shadow:0 6px 18px rgba(15,23,42,.06);}
    .fichas-activity-card.full{grid-column:1 / -1;}
    .fichas-activity-card .label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;}
    .fichas-activity-card strong{display:block;font-size:22px;margin:6px 0;color:#0f172a;}
    .fichas-activity-card .muted{color:#64748b;font-size:12px;}
    .fichas-activity-card .mini-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;margin-top:6px;}
    .fichas-activity-card .badge{background:#e2e8f0;color:#0f172a;border-radius:10px;padding:2px 8px;font-weight:700;}
    .fichas-activity-list{margin:8px 0 0;padding:0;list-style:none;display:grid;gap:8px;}
    .fichas-activity-list li{font-size:13px;color:#1f2937;}
    .pill{display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;text-transform:uppercase;letter-spacing:.06em;background:#e2e8f0;color:#0f172a;margin-right:6px;}
    .pill.action-create{background:#dcfce7;color:#166534;}
    .pill.action-update{background:#dbeafe;color:#1e40af;}
    .pill.action-delete{background:#fee2e2;color:#991b1b;}
    .pill.action-upload{background:#ede9fe;color:#5b21b6;}
    .pill.action-login{background:#cffafe;color:#155e75;}
    .pill.action-logout{background:#fef3c7;color:#92400e;}
    .pill.action-menu_update{background:#e0e7ff;color:#3730a3;}
    .pill.action-user_create,.pill.action-user_update{background:#fce7f3;color:#9d174d;}
    .pill.action-plugin_activate,.pill.action-plugin_deactivate{background:#f1f5f9;color:#0f172a;}
    .pill.sev-critical{background:#fee2e2;color:#991b1b;}
    .pill.sev-warn{background:#fef3c7;color:#92400e;}
    .pill.sev-info{background:#dbeafe;color:#1e40af;}
    .fichas-activity-filters{display:flex;gap:8px;align-items:center;margin:16px 0;}
    .fichas-activity-filters select,.fichas-activity-filters input[type=date]{min-width:160px;}
    .fichas-user-summary strong{display:block;margin-top:6px;}
    .fichas-activity-charts{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin:12px 0 18px;}
    .fichas-bar-chart{display:grid;gap:8px;margin-top:8px;}
    .bar-row{display:grid;grid-template-columns:120px 1fr 40px;gap:8px;align-items:center;font-size:12px;color:#1f2937;}
    .bar-label{font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .bar-track{background:#f1f5f9;border-radius:999px;height:8px;position:relative;overflow:hidden;}
    .bar-fill{display:block;height:100%;background:linear-gradient(90deg,#3b82f6,#38bdf8);border-radius:999px;}
    .bar-fill.is-accent{background:linear-gradient(90deg,#f97316,#f59e0b);}
    .bar-value{font-weight:700;text-align:right;}
    .fichas-sparkline{display:grid;grid-template-columns:repeat(auto-fit,minmax(16px,1fr));gap:6px;align-items:end;height:140px;margin-top:10px;}
    .spark-col{display:grid;gap:4px;align-items:end;justify-items:center;}
    .spark-bar{width:100%;max-width:18px;background:linear-gradient(180deg,#22c55e,#16a34a);border-radius:8px 8px 4px 4px;min-height:8px;}
    .spark-label{font-size:10px;color:#64748b;transform:rotate(-20deg);white-space:nowrap;}
    ';
    if (!wp_style_is('fichas-activity-admin', 'enqueued')) {
        wp_register_style('fichas-activity-admin', false);
        wp_enqueue_style('fichas-activity-admin');
    }
    wp_add_inline_style('fichas-activity-admin', $css);
}
add_action('admin_enqueue_scripts', 'fichas_activity_admin_styles');


