<?php
if ( ! defined('ABSPATH') ) exit;

// El autoloader se carga desde functions.php

class ASAP_Manual_IA_Rewriter {

    const NS = 'asap_ia';
    const META_SOURCE_URL   = '_asap_source_url';
    const META_SOURCE_TITLE = '_asap_source_title';
    
    /**
     * @var ASAP_IA_Core_OpenAI_Client Cliente de OpenAI
     */
    private $openai_client;
    
    /**
     * @var ASAP_IA_Core_Token_Calculator Calculadora de tokens y costos
     */
    private $token_calculator;
    
    /**
     * @var ASAP_IA_Generators_Article_Generator Generador de artículos
     */
    private $article_generator;
    
    /**
     * @var ASAP_IA_Generators_Outline_Generator Generador de outline (H2)
     */
    private $outline_generator;
    
    /**
     * @var ASAP_IA_Generators_Meta_Generator Generador de meta tags
     */
    private $meta_generator;
    
    /**
     * @var ASAP_IA_Generators_Image_Generator Generador de imágenes
     */
    private $image_generator;
    
    /**
     * @var ASAP_IA_Queue_Queue_Manager Gestor de cola de trabajos
     */
    private $queue_manager;
    
    /**
     * @var ASAP_IA_Queue_Job_Processor Procesador de trabajos de cola
     */
    private $job_processor;
    
    /**
     * @var ASAP_IA_Database_Logger Gestor de logs
     */
    private $logger;
    
    /**
     * @var ASAP_IA_Settings_Manager Gestor de configuraciones
     */
    private $settings_manager;

    public function __construct() {
        try {
            // Verificar que el autoloader haya cargado
            $required_classes = [
                'ASAP_IA_Core_OpenAI_Client',
                'ASAP_IA_Core_Token_Calculator',
                'ASAP_IA_Queue_Queue_Manager',
                'ASAP_IA_Database_Logger',
                'ASAP_IA_Generators_Article_Generator',
                'ASAP_IA_Generators_Outline_Generator',
                'ASAP_IA_Generators_Meta_Generator',
                'ASAP_IA_Generators_Image_Generator',
                'ASAP_IA_Queue_Job_Processor',
                'ASAP_IA_AJAX_Handler',
                'ASAP_IA_Hooks_Manager',
                'ASAP_IA_Settings_Manager',
            ];
            
            foreach ($required_classes as $class) {
                if (!class_exists($class)) {
                    // Intentar cargar manualmente
                    $this->manual_require_class($class);
                    
                    // Si aún no existe, lanzar error descriptivo
                    if (!class_exists($class)) {
                        throw new Exception("ERROR CRÍTICO: No se pudo cargar la clase {$class}. Verifica que el autoloader esté cargado correctamente en functions.php");
                    }
                }
            }
            
            // Inicializar clases del Core
            $this->openai_client = new ASAP_IA_Core_OpenAI_Client();
            $this->token_calculator = new ASAP_IA_Core_Token_Calculator();
            $this->queue_manager = new ASAP_IA_Queue_Queue_Manager();
            $this->logger = new ASAP_IA_Database_Logger();
            
            // NOTA: Job_Processor se inicializa después porque depende de otros generadores
            
            // ✅ Inicializar image_generator PRIMERO (necesario para article_generator)
            $this->image_generator = new ASAP_IA_Generators_Image_Generator(
                $this->openai_client,
                $this->token_calculator,
                [$this, 'log_generation']
            );
            
            // Inicializar generadores
            $this->article_generator = new ASAP_IA_Generators_Article_Generator(
                $this->openai_client,
                $this->token_calculator,
                [$this, 'log_generation'],
                $this->image_generator // ← Pasar image_generator para imágenes de contenido
            );
            $this->outline_generator = new ASAP_IA_Generators_Outline_Generator(
                $this->openai_client,
                $this->token_calculator
            );
            $this->meta_generator = new ASAP_IA_Generators_Meta_Generator(
                $this->openai_client,
                $this->token_calculator
            );
            
            // Inicializar Job Processor (después de los generadores)
            $this->job_processor = new ASAP_IA_Queue_Job_Processor(
                $this->article_generator,
                $this->queue_manager
            );
            
            // Inicializar managers auxiliares
            $ajax_handler = new ASAP_IA_AJAX_Handler($this);
            $hooks_manager = new ASAP_IA_Hooks_Manager($this);
            $this->settings_manager = new ASAP_IA_Settings_Manager($this);
        } catch (Exception $e) {
            // Mostrar error amigable al administrador
            if (is_admin()) {
                add_action('admin_notices', function() use ($e) {
                    echo '<div class="notice notice-error"><p>';
                    echo '<strong>Error crítico en ASAP IA:</strong> ' . esc_html($e->getMessage());
                    echo '</p><p style="font-size:11px;font-family:monospace;background:#f0f0f0;padding:10px;overflow-x:auto;">';
                    echo esc_html($e->getTraceAsString());
                    echo '</p></div>';
                });
            }
            
            // Re-lanzar el error para que WordPress lo maneje
            throw $e;
        }
        
        // Cargar Action Scheduler
        $this->load_action_scheduler();
        
        // Crear tablas si no existen
        add_action('admin_init', [$this->logger, 'maybe_create_table']);
        add_action('admin_init', [$this, 'maybe_create_queue_table']);
        
        // Hooks de WordPress (admin)
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
        // Registrar AJAX handlers (delegados al AJAX_Handler)
        $ajax_handler->register_hooks();
        
        // Registrar hooks de WordPress (delegados al Hooks_Manager)
        $hooks_manager->register_hooks();
        
        // WP Cron para auto-procesamiento
        add_filter('cron_schedules', [$this, 'add_cron_intervals']);
        add_action('asap_auto_process_queue_cron', [$this, 'cron_process_queue']);
        if (!wp_next_scheduled('asap_auto_process_queue_cron')) {
            wp_schedule_event(time(), 'five_minutes', 'asap_auto_process_queue_cron');
        }
    }
    
    /**
     * Carga manualmente una clase si el autoloader falla
     * 
     * @param string $class Nombre de la clase a cargar
     */
    private function manual_require_class($class) {
        // Solo cargar clases que empiecen con ASAP_IA_
        if (strpos($class, 'ASAP_IA_') !== 0) {
            return;
        }
        
        // Mapeo manual de clases a archivos
        $class_map = [
            // Core
            'ASAP_IA_Core_OpenAI_Client' => 'Core/OpenAI_Client.php',
            'ASAP_IA_Core_Token_Calculator' => 'Core/Token_Calculator.php',
            'ASAP_IA_Core_Context_Manager' => 'Core/Context_Manager.php',
            
            // Database
            'ASAP_IA_Database_Logger' => 'Database/Logger.php',
            'ASAP_IA_Database_Generation_Logger' => 'Database/Generation_Logger.php',
            
            // Generators
            'ASAP_IA_Generators_Article_Generator' => 'Generators/Article_Generator.php',
            'ASAP_IA_Generators_Outline_Generator' => 'Generators/Outline_Generator.php',
            'ASAP_IA_Generators_Meta_Generator' => 'Generators/Meta_Generator.php',
            'ASAP_IA_Generators_Image_Generator' => 'Generators/Image_Generator.php',
            
            // Queue
            'ASAP_IA_Queue_Queue_Manager' => 'Queue/Queue_Manager.php',
            'ASAP_IA_Queue_Job_Processor' => 'Queue/Job_Processor.php',
            
            // AJAX
            'ASAP_IA_AJAX_Handler' => 'AJAX/Handler.php',
            
            // Hooks
            'ASAP_IA_Hooks_Manager' => 'Hooks/Manager.php',
            
            // Settings
            'ASAP_IA_Settings_Manager' => 'Settings/Manager.php',
            
            // UI
            'ASAP_IA_UI_Tab_Config' => 'UI/Tab_Config.php',
            'ASAP_IA_UI_Tab_Dashboard' => 'UI/Tab_Dashboard.php',
            'ASAP_IA_UI_Tab_New_Article' => 'UI/Tab_New_Article.php',
            'ASAP_IA_UI_Tab_Queue' => 'UI/Tab_Queue.php',
            'ASAP_IA_UI_Tab_Meta' => 'UI/Tab_Meta.php',
            'ASAP_IA_UI_Tab_Images' => 'UI/Tab_Images.php',
            
            // Helpers
            'ASAP_IA_Helpers_HTML_Parser' => 'Helpers/HTML_Parser.php',
            'ASAP_IA_Helpers_Image_Processor' => 'Helpers/Image_Processor.php',
            'ASAP_IA_Helpers_Utils' => 'Helpers/Utils.php',
            
            // Research
            'ASAP_IA_Research_SERP_Analyzer' => 'Research/SERP_Analyzer.php',
            'ASAP_IA_Research_Competitor_Scraper' => 'Research/Competitor_Scraper.php',
            'ASAP_IA_Research_PAA_Processor' => 'Research/PAA_Processor.php',
            'ASAP_IA_Research_Briefing_Builder' => 'Research/Briefing_Builder.php',
            'ASAP_IA_Research_Entity_Extractor' => 'Research/Entity_Extractor.php',
            
            // Background
            'ASAP_IA_Background_Task_Manager' => 'Background/Task_Manager.php',
        ];
        
        if (isset($class_map[$class])) {
            $file_path = __DIR__ . '/' . $class_map[$class];
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    public function add_cron_intervals($schedules) {
        $schedules['five_minutes'] = [
            'interval' => 300, // 5 minutos en segundos
            'display'  => __('Cada 5 minutos', 'asap'),
        ];
        return $schedules;
    }

    /* -------------------------------------------------------------------------
     * Cargar Action Scheduler
     * ------------------------------------------------------------------------- */
    private function load_action_scheduler() {
        // Intentar cargar Action Scheduler standalone si no existe
        if (!class_exists('ActionScheduler')) {
            $as_path = get_template_directory() . '/inc/action-scheduler/action-scheduler.php';
            if (file_exists($as_path)) {
                require_once $as_path;
            }
        }
        
        // Registrar acciones de procesamiento
        if (class_exists('ActionScheduler')) {
            add_action('asap_process_queue_job', [$this, 'process_queue_job'], 10, 1);
            add_action('asap_process_article_section', [$this, 'process_article_section'], 10, 1);
        } else {
            // Fallback a WP Cron
            add_action('asap_process_queue_job', [$this, 'process_queue_job'], 10, 1);
        }
    }

    /* -------------------------------------------------------------------------
     * Crear tabla de logs (solo una vez)
     * ------------------------------------------------------------------------- */
    // Wrapper para mantener compatibilidad - Delega a Logger
    public function maybe_create_logs_table() {
        $this->logger->maybe_create_table();
    }

    /* -------------------------------------------------------------------------
     * Crear tabla de cola (solo una vez)
     * ------------------------------------------------------------------------- */
    public function maybe_create_queue_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_ia_queue';
        $charset_collate = $wpdb->get_charset_collate();
        
        // Verificar versión de la tabla
        $current_version = get_option('asap_ia_queue_table_version', '0');
        
        // Verificar si ya existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        
        if (!$table_exists) {
            // Crear tabla nueva con started_at
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) NOT NULL,
                status varchar(20) DEFAULT 'pending',
                type varchar(50) DEFAULT 'article',
                params longtext NOT NULL,
                scheduled_at datetime DEFAULT NULL,
                publish_immediately tinyint(1) DEFAULT 1,
                post_id bigint(20) DEFAULT NULL,
                error_message text DEFAULT NULL,
                attempts int(11) DEFAULT 0,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                started_at datetime DEFAULT NULL,
                processed_at datetime DEFAULT NULL,
                PRIMARY KEY  (id),
                KEY user_id (user_id),
                KEY status (status),
                KEY type (type),
                KEY created_at (created_at),
                KEY started_at (started_at)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            
            update_option('asap_ia_queue_table_version', '1.1');
        } elseif ($current_version < '1.1') {
            // Actualizar tabla existente: agregar started_at
            $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'started_at'");
            if (empty($column_exists)) {
                $wpdb->query("ALTER TABLE $table_name ADD COLUMN started_at datetime DEFAULT NULL AFTER attempts");
                $wpdb->query("ALTER TABLE $table_name ADD KEY started_at (started_at)");
            }
            update_option('asap_ia_queue_table_version', '1.1');
        }
    }

    /* -------------------------------------------------------------------------
     * Menú: submenú "IA" colgando de 'asap-menu'
     * ------------------------------------------------------------------------- */
    public function register_menus() {
        $cap = 'manage_options';
        add_submenu_page(
            'asap-menu',
            'IA',
            'IA',
            $cap,
            'asap-menu-ia',
            [$this, 'render_ia_page']
        );
    }

    /* -------------------------------------------------------------------------
     * Assets solo para la página IA
     * ------------------------------------------------------------------------- */
    public function enqueue_admin_assets($hook) {
        $is_ia_page = isset($_GET['page']) && $_GET['page'] === 'asap-menu-ia';
        if ( ! $is_ia_page ) return;

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable'); // Para drag & drop

        // Objeto AJAX global
        wp_enqueue_script(
            'asap-ia-manual',
            get_template_directory_uri() . '/assets/js/asap-ia-manual.js',
            ['jquery', 'jquery-ui-sortable'],
            '1.6.0',
            true
        );
        wp_localize_script('asap-ia-manual', 'ASAP_IA', [
            'ajax' => admin_url('admin-ajax.php'),
        ]);

        // Estilos mínimos
        wp_add_inline_style('wp-admin', '.spinner{visibility:visible;}');
    }

    /* -------------------------------------------------------------------------
     * Página IA con tabs
     * ------------------------------------------------------------------------- */
    public function render_ia_page() {
        if ( ! current_user_can('manage_options') ) {
            echo '<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p>No tienes permisos suficientes.</p></div>';
            return;
        }

        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'ia_dashboard';
        if ( ! in_array($active_tab, ['ia_dashboard','ia_new','ia_meta','ia_images','ia_queue','ia_config'], true) ) $active_tab = 'ia_dashboard';

        // Guardados (POST) - Delegados al Settings_Manager
        if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
            if ( isset($_POST['asap_ia_defaults_nonce']) && wp_verify_nonce($_POST['asap_ia_defaults_nonce'], 'asap_ia_defaults_action') ) {
                $this->settings_manager->save_defaults();
                $this->admin_notice_success('Ajustes guardados.');
            }
            if ( isset($_POST['asap_ia_config_nonce']) && wp_verify_nonce($_POST['asap_ia_config_nonce'], 'asap_ia_config_action') ) {
                $this->settings_manager->save_config();
                $this->admin_notice_success('Configuración guardada.');
            }
            if ( isset($_POST['asap_meta_nonce']) && wp_verify_nonce($_POST['asap_meta_nonce'], 'asap_meta_action') ) {
                $this->settings_manager->save_meta_settings();
                $this->admin_notice_success('Metaetiquetas: ajustes guardados.');
            }
            if ( isset($_POST['asap_images_settings_nonce']) && wp_verify_nonce($_POST['asap_images_settings_nonce'], 'asap_images_settings_action') ) {
                $this->settings_manager->save_images_settings();
                $this->admin_notice_success('Imágenes: ajustes guardados.');
            }
        }

        $defaults = $this->get_defaults_for_render();

        ?>
        <div class="wrap wrapper-asap-options">
            <div class="nav-tab-wrapper">
                <div style="text-align:center;">
                    <img class="asap-logo" src="https://demo.asaptheme.com/wp-content/themes/asap/assets/img/logo.png" width="200" height="54" alt="ASAP">
                </div>
                <a href="<?php echo esc_url( add_query_arg(['page'=>'asap-menu-ia','tab'=>'ia_dashboard'], admin_url('admin.php')) ); ?>" class="nav-tab <?php echo $active_tab==='ia_dashboard'?'nav-tab-active':''; ?>">
                    <?php echo $this->icon_dashboard(); ?><span><?php _e('Dashboard', 'asap'); ?></span>
                </a>
                <a href="<?php echo esc_url( add_query_arg(['page'=>'asap-menu-ia','tab'=>'ia_new'], admin_url('admin.php')) ); ?>" class="nav-tab <?php echo $active_tab==='ia_new'?'nav-tab-active':''; ?>">
                    <?php echo $this->icon_pen(); ?><span><?php _e('Artículos', 'asap'); ?></span>
                </a>
                <a href="<?php echo esc_url( add_query_arg(['page'=>'asap-menu-ia','tab'=>'ia_images'], admin_url('admin.php')) ); ?>" class="nav-tab <?php echo $active_tab==='ia_images'?'nav-tab-active':''; ?>">
                    <?php echo $this->icon_image(); ?><span><?php _e('Imágenes', 'asap'); ?></span>
                </a>
                <a href="<?php echo esc_url( add_query_arg(['page'=>'asap-menu-ia','tab'=>'ia_meta'], admin_url('admin.php')) ); ?>" class="nav-tab <?php echo $active_tab==='ia_meta'?'nav-tab-active':''; ?>">
                    <?php echo $this->icon_meta(); ?><span><?php _e('Metaetiquetas', 'asap'); ?></span>
                </a>

                <a href="<?php echo esc_url( add_query_arg(['page'=>'asap-menu-ia','tab'=>'ia_queue'], admin_url('admin.php')) ); ?>" class="nav-tab <?php echo $active_tab==='ia_queue'?'nav-tab-active':''; ?>">
                    <?php echo $this->icon_queue(); ?><span><?php _e('Cola de generación', 'asap'); ?></span>
                </a>
                <a href="<?php echo esc_url( add_query_arg(['page'=>'asap-menu-ia','tab'=>'ia_config'], admin_url('admin.php')) ); ?>" class="nav-tab <?php echo $active_tab==='ia_config'?'nav-tab-active':''; ?>">
                    <?php echo $this->icon_settings(); ?><span><?php _e('Configuración', 'asap'); ?></span>
                </a>
            </div>
        <?php
            if ( $active_tab === 'ia_config' ) {
                $this->render_tab_config();
            } elseif ( $active_tab === 'ia_meta' ) {
                $this->render_tab_meta();
            } elseif ( $active_tab === 'ia_images' ) {
                $this->render_tab_images();
            } elseif ( $active_tab === 'ia_queue' ) {
                $this->render_tab_queue();
            } elseif ( $active_tab === 'ia_new' ) {
                $this->render_tab_new_single_form($defaults);
            } else {
                $this->render_tab_dashboard();
            }
        ?>
        </div>
        <?php
    }

    /* -------------------------------------------------------------------------
     * TAB: Dashboard - Estadísticas y métricas
     * ------------------------------------------------------------------------- */
    private function render_tab_dashboard() {
        // Delegar a Tab_Dashboard
        ASAP_IA_UI_Tab_Dashboard::render($this);
    }

    /* -------------------------------------------------------------------------
     * TAB: Configuración (API keys)
     * ------------------------------------------------------------------------- */
    private function render_tab_config() {
        // Delegar a Tab_Config
        ASAP_IA_UI_Tab_Config::render();
    }

    /* -------------------------------------------------------------------------
     * TAB: Metaetiquetas (auto)
     *  - Removida opción de temperatura: siempre 0.7
     * ------------------------------------------------------------------------- */
    private function render_tab_meta() {
        // Delegar a Tab_Meta
        ASAP_IA_UI_Tab_Meta::render($this);
    }

    /* -------------------------------------------------------------------------
     * TAB: Imágenes (auto)
     * ------------------------------------------------------------------------- */
    private function render_tab_images() {
        // Delegar a Tab_Images
        ASAP_IA_UI_Tab_Images::render($this);
    }

    /* -------------------------------------------------------------------------
     * TAB: Cola de Generación
     * ------------------------------------------------------------------------- */
    private function render_tab_queue() {
        // Delegar a Tab_Queue
        ASAP_IA_UI_Tab_Queue::render($this);
    }

    /* -------------------------------------------------------------------------
     * TAB: Nuevo (Generador premium y muy personalizable)
     * ------------------------------------------------------------------------- */
    private function render_tab_new_single_form(array $d) {
        // Delegar a Tab_New_Article
        ASAP_IA_UI_Tab_New_Article::render($d);
    }

    /* -------------------------------------------------------------------------
     * Helpers de configuración
     * ------------------------------------------------------------------------- */
    private function get_image_settings() {
        $rep_models = $this->get_replicate_model_catalog();
        $defaults = [
            'enable' => '0',
            'post_types' => ['post'],
            'only_if_empty' => '1',
            'lang' => 'inherit',

            'provider' => 'openai',
            'fallback' => 'none',

            // OpenAI (solo dall-e-3)
            'openai_size'    => '1024x1024',
            'openai_quality' => 'standard',
            'openai_n'       => 1,

            // Replicate
            'replicate_model'  => 'flux-schnell',
            'replicate_aspect' => '1:1',
            'replicate_n'      => 1,
            'negative_prompt'  => '',

            // Prompt/ALT
            'prompt_template' => 'Imagen destacada para el artículo titulado "{title}". Estilo fotográfico realista, composición limpia, alta calidad. IMPORTANTE: NO incluir texto, letras, palabras ni tipografía en la imagen. Solo elementos visuales. Idioma visual: {lang}.',
            'alt_mode'        => 'template',
            'alt_template'    => '{title}',

            // ⭐ IMÁGENES DE CONTENIDO (NUEVO)
            'content_enable'   => '0',
            'content_quantity' => 3,
            'content_strategy' => 'auto', // auto | each_h2 | every_n
            'content_every_n'  => 2,      // Para strategy 'every_n'
            'content_position' => 'after_h2', // after_h2 | before_content | after_first_p
            'content_img_style' => 'centered', // centered | full_width | float_left | float_right
            'content_prompt_template' => '', // Prompt personalizado (vacío = usar default)

            // Tarifas estimadas
            'openai_price_per_mpx' => 0.04,
            'openai_hd_mult'       => 2.0,
            'replicate_rates'    => array_fill_keys(array_keys($rep_models), 0.006),
        ];
        $cfg = get_option('asap_img_settings', []);
        $cfg = wp_parse_args($cfg, $defaults);
        foreach ($defaults as $k=>$v) if (!isset($cfg[$k])) $cfg[$k]=$v;
        return $cfg;
    }

    private function get_replicate_model_catalog() {
        // Catálogo curado (slug => label/desc + model_id)
        return [
            'flux-schnell'        => [
                'label'    => 'FLUX.1 schnell (BFL)', 
                'desc'     => 'Imágenes realistas, MUY rápido (ideal para iterar).',
                'model_id' => 'black-forest-labs/flux-schnell'
            ],
            'flux-dev'            => [
                'label'    => 'FLUX.1 dev (BFL)',     
                'desc'     => 'Más calidad que schnell; algo más lento.',
                'model_id' => 'black-forest-labs/flux-dev'
            ],
            'sd3-medium'          => [
                'label'    => 'Stable Diffusion 3 (medium)', 
                'desc'     => 'Versátil; buena coherencia.',
                'model_id' => 'stability-ai/stable-diffusion-3-medium'
            ],
            'imagen'              => [
                'label'    => 'Google Imagen 3',
                'desc'     => 'Modelo de Google, alta calidad.',
                'model_id' => 'google-deepmind/imagen-3'
            ],
            'ideogram'            => [
                'label'    => 'Ideogram v2 Turbo',
                'desc'     => 'Excelente para texto en imágenes.',
                'model_id' => 'ideogram-ai/ideogram-v2'
            ],
            'sdxl'                => [
                'label'    => 'Stable Diffusion XL',
                'desc'     => 'Versátil, buena calidad general.',
                'model_id' => 'stability-ai/sdxl'
            ],
            'playground-v2'       => [
                'label'    => 'Playground v2.5',
                'desc'     => 'Excelente para fotografía y realismo.',
                'model_id' => 'playgroundai/playground-v2.5-1024px-aesthetic'
            ],
        ];
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Helpers - Delegación a clases especializadas
     * ------------------------------------------------------------------------- */
    private function parse_html_structure($html) {
        return ASAP_IA_Helpers_HTML_Parser::parse_html_structure($html);
    }

    private function fetch_url_html($url) {
        return ASAP_IA_Helpers_HTML_Parser::fetch_url_html($url);
    }

    private function extract_main_content($html, $url) {
        return ASAP_IA_Helpers_HTML_Parser::extract_main_content($html, $url);
    }

    private function find_first_image_in_content($content) {
        return ASAP_IA_Helpers_HTML_Parser::find_first_image_in_content($content);
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Settings/Meta/Imágenes - Delegación a clases especializadas
     * ------------------------------------------------------------------------- */
    private function meta_is_enabled() {
        return $this->settings_manager->meta_is_enabled();
    }

    private function img_is_enabled() {
        return $this->settings_manager->img_is_enabled();
    }

    private function is_public_post_type($pt) {
        return $this->settings_manager->is_public_post_type($pt);
    }

    private function maybe_generate_meta_for_post($post_id, $force=false) {
        return $this->meta_generator->maybe_generate_meta_for_post($post_id, $force);
    }

    private function detect_seo_plugins() {
        return $this->meta_generator->detect_seo_plugins();
    }

    private function generate_meta_for_post($post_id, array $opts) {
        return $this->meta_generator->generate_meta_for_post($post_id, $opts);
    }

    private function maybe_generate_featured_image_for_post($post_id, $context='publish') {
        return $this->image_generator->maybe_generate_featured_image_for_post($post_id, $context);
    }

    private function generate_and_assign_featured($post_id, $provider, array $S) {
        return $this->image_generator->generate_and_assign_featured($post_id, $provider, $S);
    }

    private function build_image_prompt($post_id, array $S) {
        return $this->image_generator->build_image_prompt($post_id, $S);
    }

    private function build_alt_text($post_id, array $S) {
        return $this->image_generator->build_alt_text($post_id, $S);
    }

    private function generate_images_openai($prompt, array $S) {
        return $this->image_generator->generate_images_openai($prompt, $S);
    }

    private function generate_images_replicate($prompt, array $S) {
        return $this->image_generator->generate_images_replicate($prompt, $S);
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Image Processor - Delegación a Helpers
     * ------------------------------------------------------------------------- */
    private function save_base64_image_to_media($b64, $filename, $post_id=0, $format='png', $watermark='', $alt='') {
        return ASAP_IA_Helpers_Image_Processor::save_base64_image_to_media($b64, $filename, $post_id, $format, $watermark, $alt);
    }

    private function sideload_image_from_url($url, $post_id=0, $format='png', $watermark='', $alt='') {
        return ASAP_IA_Helpers_Image_Processor::sideload_image_from_url($url, $post_id, $format, $watermark, $alt);
    }

    private function process_image_file($file, $format='png', $watermark='') {
        return ASAP_IA_Helpers_Image_Processor::process_image_file($file, $format, $watermark);
    }

    private function apply_text_watermark($editor, $text) {
        return ASAP_IA_Helpers_Image_Processor::apply_text_watermark($editor, $text);
    }

    private function ensure_ext($filename, $format) {
        return ASAP_IA_Helpers_Utils::ensure_ext($filename, $format);
    }

    private function slugify_filename($text) {
        return ASAP_IA_Helpers_Utils::slugify_filename($text);
    }

    /* -------------------------------------------------------------------------
     * Wrappers para OpenAI Client
     * ------------------------------------------------------------------------- */
    private function openai_chat($api_key, $model, $temperature, $system, $user_content, $max_tokens = 1800) {
        return $this->openai_client->chat($api_key, $model, $temperature, $system, $user_content, $max_tokens);
    }

    private function get_openai_key() {
        return $this->openai_client->get_api_key();
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Queue Management
     * ------------------------------------------------------------------------- */
    private function add_to_queue($params) {
        return $this->queue_manager->add_to_queue($params);
    }

    private function get_next_queue_job() {
        return $this->queue_manager->get_next_job();
    }

    private function get_queue_stats() {
        return $this->queue_manager->get_stats();
    }

    private function get_queue_items($status = 'all', $limit = 20) {
        return $this->queue_manager->get_items($status, $limit);
    }

    private function update_queue_status($job_id, $status, $data = []) {
        $this->queue_manager->update_status($job_id, $status, $data);
    }

    private function can_process_queue() {
        return $this->queue_manager->can_process();
    }

    private function delete_queue_item($job_id) {
        return $this->queue_manager->delete_item($job_id);
    }

    private function cleanup_stuck_jobs() {
        $this->queue_manager->cleanup_stuck_jobs();
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Job Processing
     * ------------------------------------------------------------------------- */
    public function process_queue_job($job_id) {
        return $this->job_processor->process_job_by_id($job_id);
    }

    private function generate_article_by_sections($params) {
        return $this->article_generator->generate_by_sections($params);
    }

    private function generate_article_from_params($params) {
        return $this->article_generator->generate_from_params($params);
    }

    public function cron_process_queue() {
        $this->job_processor->cron_process_queue();
    }

    private function maybe_send_queue_completion_email() {
        $this->queue_manager->maybe_send_completion_email();
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Logger y Utils
     * ------------------------------------------------------------------------- */
    public function log_generation($data) {
        $this->logger->log($data);
    }

    private function estimate_tokens($text) {
        return ASAP_IA_Helpers_Utils::estimate_tokens($text);
    }

    private function calculate_cost($model, $tokens_input, $tokens_output) {
        return ASAP_IA_Helpers_Utils::calculate_cost($model, $tokens_input, $tokens_output);
    }

    /* -------------------------------------------------------------------------
     * Wrappers para Iconos - Delegación a Helpers/Icons.php
     * ------------------------------------------------------------------------- */
    private function icon_pen() {
        return ASAP_IA_Helpers_Icons::pen();
    }
    private function icon_settings() {
        return ASAP_IA_Helpers_Icons::settings();
    }
    private function icon_meta() {
        return ASAP_IA_Helpers_Icons::meta();
    }
    private function icon_image() {
        return ASAP_IA_Helpers_Icons::image();
    }
    private function icon_queue() {
        return ASAP_IA_Helpers_Icons::queue();
    }
    private function icon_dashboard() {
        return ASAP_IA_Helpers_Icons::dashboard();
    }

    /* -------------------------------------------------------------------------
     * Wrappers para UI Helper
     * ------------------------------------------------------------------------- */
    private function admin_notice_success($msg) {
        ASAP_IA_Helpers_UI_Helper::admin_notice_success($msg);
    }

    /* -------------------------------------------------------------------------
     * Helper para defaults de renderizado
     * ------------------------------------------------------------------------- */
    private function get_defaults_for_render() {
        return [
            'h1' => '',
            'keyword' => '',
            'target_len' => get_option('asap_ia_default_target_len', 3000),
            'style' => get_option('asap_ia_default_style', 'informativo'),
            'lang' => get_option('asap_ia_default_lang', 'es'),
            'status' => get_option('asap_ia_default_status', 'draft'),
            'post_type' => get_option('asap_ia_default_post_type', 'post'),
            'author' => get_option('asap_ia_default_author', 0),
            // ⭐ NUEVOS CAMPOS
            'extra' => get_option('asap_ia_default_extra', ''),
            'intro_enable' => get_option('asap_ia_default_intro_enable', '1') === '1',
            'faqs_enable' => get_option('asap_ia_default_faqs_enable', '0') === '1',
            'faqs_count' => get_option('asap_ia_default_faqs_count', 5),
            'conclusion_enable' => get_option('asap_ia_default_conclusion_enable', '1') === '1',
            // ⭐ REFERENCIAS
            'include_references' => get_option('asap_ia_default_include_references', '0') === '1',
            'custom_references' => get_option('asap_ia_default_custom_references', ''),
            // ⭐ CATEGORÍAS
            'categories' => get_option('asap_ia_default_categories', []),
        ];
    }

    /* =========================================================================
     * FUNCIONES AJAX - Implementaciones para compatibilidad con AJAX/Handler
     * ========================================================================= */

    /**
     * AJAX: Sugerir outline (H2)
     */
    public function ajax_suggest_outline() {
        $h1 = sanitize_text_field($_POST['h1'] ?? '');
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        $existing = isset($_POST['existing']) && is_array($_POST['existing']) ? array_map('sanitize_text_field', $_POST['existing']) : [];

        // Delegar a Outline_Generator
        $result = $this->outline_generator->suggest_h2($h1, $keyword, $existing);
        
        if (is_wp_error($result)) {
            // Log del error
            $this->log_generation([
                'type' => 'outline',
                'action' => 'suggest_h2',
                'model' => 'gpt-4.1-mini',
                'status' => 'error',
                'error_message' => $result->get_error_message(),
                'metadata' => ['h1' => $h1, 'keyword' => $keyword],
            ]);
            
            // Mensaje personalizado según el error
            if ($result->get_error_code() === 'no_api_key') {
                $config_url = admin_url('admin.php?page=asap-menu-ia&tab=ia_config');
                wp_send_json_error(['message'=>'⚠ Configura tu OpenAI API Key en la pestaña <a href="'.$config_url.'" style="color:#2271b1;">Configuración</a>. <a href="https://platform.openai.com/api-keys" target="_blank" style="color:#2271b1;">Obtener API Key →</a>']);
            }
            
            wp_send_json_error(['message'=> $result->get_error_message()]);
        }

        // Log exitoso
        $this->log_generation([
            'type' => 'outline',
            'action' => 'suggest_h2',
            'model' => $result['model'],
            'tokens_input' => $result['usage']['prompt_tokens'],
            'tokens_output' => $result['usage']['completion_tokens'],
            'tokens_total' => $result['tokens'],
            'cost_usd' => $result['cost'],
            'status' => 'success',
            'metadata' => ['h1' => $h1, 'keyword' => $keyword, 'suggestions_count' => count($result['suggestions'])],
        ]);

        wp_send_json_success([
            'suggestions' => $result['suggestions'],
            'usage' => $result['usage'],
            'cost' => number_format($result['cost'], 5),
        ]);
    }

    /**
     * AJAX: Extraer estructura de competidor
     */
    public function ajax_extract_competitor_structure() {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        try {
            $url = esc_url_raw($_POST['url'] ?? '');
            
            if (empty($url)) {
                $logger->error($session_id, 'competitor_analysis', 'Intento de extraer estructura sin URL');
                wp_send_json_error(['message' => 'URL es obligatoria.']);
            }
            
            $logger->info($session_id, 'competitor_analysis', "📝 Extrayendo estructura de: {$url}");
            $start_time = microtime(true);

            $html = $this->fetch_url_html($url);
            
            if (is_wp_error($html)) {
                $duration = microtime(true) - $start_time;
                $error_message = $html->get_error_message();
                $logger->error($session_id, 'competitor_analysis', "❌ Error obteniendo HTML: {$error_message}", [
                    'url' => $url,
                    'error_code' => $html->get_error_code()
                ], null, null, $duration);
                wp_send_json_error(['message' => $error_message]);
                return;
            }

            $logger->info($session_id, 'competitor_analysis', "✓ HTML obtenido (" . strlen($html) . " bytes), parseando estructura...");
            
            $structure = $this->parse_html_structure($html);
            
            if (empty($structure) || !isset($structure['h2'])) {
                $duration = microtime(true) - $start_time;
                $logger->error($session_id, 'competitor_analysis', "❌ Error parseando HTML: estructura vacía o inválida", [
                    'url' => $url,
                    'html_length' => strlen($html)
                ], null, null, $duration);
                wp_send_json_error(['message' => 'No se pudo extraer la estructura del HTML. El sitio podría tener un formato no estándar.']);
                return;
            }
            
            $duration = microtime(true) - $start_time;
            
            $logger->success($session_id, 'competitor_analysis', "✅ Estructura extraída exitosamente", [
                'url' => $url,
                'h2_count' => count($structure['h2']),
                'h3_count' => count($structure['h3'] ?? []),
                'word_count' => $structure['word_count'],
                'title' => $structure['title']
            ], null, null, $duration);
            
            wp_send_json_success([
                'title' => $structure['title'],
                'h1' => $structure['h1'],
                'h2' => $structure['h2'],
                'h3' => $structure['h3'] ?? [],
                'word_count' => $structure['word_count'],
                'h2_count' => count($structure['h2']),
                'h3_count' => count($structure['h3'] ?? []),
                'structure' => $structure['h2_with_h3'] ?? []
            ]);
            
        } catch (Exception $e) {
            $duration = isset($start_time) ? microtime(true) - $start_time : 0;
            $error_msg = "Excepción capturada: " . $e->getMessage();
            $logger->error($session_id, 'competitor_analysis', "❌ {$error_msg}", [
                'url' => $url ?? 'N/A',
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], null, null, $duration);
            wp_send_json_error(['message' => 'Error inesperado: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Obtener keywords relacionadas usando ValueSERP
     * 
     * Usa PAA (People Also Ask) y Related Searches de ValueSERP
     * para obtener keywords relacionadas reales desde Google.
     */
    public function ajax_get_related_keywords() {
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        
        if (empty($keyword)) {
            wp_send_json_error(['message' => 'Keyword es obligatoria.']);
        }

        // Verificar si ValueSERP está configurado
        $serp_analyzer = new ASAP_IA_Research_SERP_Analyzer();
        
        if (!$serp_analyzer->has_api_key()) {
            wp_send_json_error([
                'message' => 'ValueSERP API Key no configurada. Ve a Configuración para agregarla.'
            ]);
            return;
        }

        // Intentar obtener del cache primero
        $cached = $serp_analyzer->get_cached_serp_data($keyword);
        
        if ($cached) {
            // Usar datos cacheados
            $keywords = $this->extract_keywords_from_serp($cached);
            wp_send_json_success([
                'keywords' => $keywords,
                'source' => 'ValueSERP (cache)',
                'count' => count($keywords)
            ]);
            return;
        }

        // Hacer análisis SERP
        $serp_data = $serp_analyzer->analyze($keyword, [
            'location' => 'Argentina',
            'language' => 'es',
            'num_results' => 10,
            'include_paa' => true,
            'include_related' => true
        ]);

        if (is_wp_error($serp_data)) {
            wp_send_json_error([
                'message' => 'Error al consultar ValueSERP: ' . $serp_data->get_error_message()
            ]);
            return;
        }

        // Cachear resultados por 24 horas
        $serp_analyzer->cache_serp_data($keyword, $serp_data, DAY_IN_SECONDS);

        // Extraer keywords de PAA y Related Searches
        $keywords = $this->extract_keywords_from_serp($serp_data);

        wp_send_json_success([
            'keywords' => $keywords,
            'source' => 'ValueSERP',
            'count' => count($keywords)
        ]);
    }

    /**
     * Extrae keywords desde datos de SERP
     * Combina PAA y Related Searches
     */
    private function extract_keywords_from_serp($serp_data) {
        $keywords = [];

        // Extraer de People Also Ask (PAA)
        if (!empty($serp_data['people_also_ask'])) {
            foreach ($serp_data['people_also_ask'] as $paa) {
                $question = $paa['question'] ?? '';
                if (!empty($question)) {
                    $keywords[] = $question;
                }
            }
        }

        // Extraer de Related Searches
        if (!empty($serp_data['related_searches'])) {
            foreach ($serp_data['related_searches'] as $related) {
                if (!empty($related)) {
                    $keywords[] = $related;
                }
            }
        }

        // Limitar a 15 keywords max y remover duplicados
        $keywords = array_unique($keywords);
        return array_slice($keywords, 0, 15);
    }

    /**
     * AJAX: Generar artículo desde keyword + outline
     * 
     * Sistema de generación:
     * 1. Usuario ingresa keyword (obligatoria)
     * 2. Usuario define estructura H2/H3 (puede ser manual o extraída de competidor)
     * 3. Opcionalmente puede crear briefing desde SERPs (análisis ValueSERP)
     * 4. Sistema genera artículo por secciones
     */
    public function ajax_manual_rewrite() {
        $h1 = sanitize_text_field($_POST['h1'] ?? '');
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        $outline = isset($_POST['outline']) ? json_decode(stripslashes($_POST['outline']), true) : [];
        
        // Validar datos obligatorios
        if (empty($h1)) {
            wp_send_json_error(['message' => 'El H1 es obligatorio.']);
        }
        
        // NO validar outline aquí - se generará automáticamente si está vacío
        // if (empty($outline) || !is_array($outline) || count($outline) === 0) {
        //     wp_send_json_error(['message' => 'Debes definir al menos un H2 en la estructura.']);
        // }
        
        // Parámetros de generación
        $params = [
            'h1' => $h1,
            'keyword' => $keyword, // Opcional pero recomendado
            'target_len' => absint($_POST['target_len'] ?? 3000),
            'style' => sanitize_text_field($_POST['style'] ?? 'informativo'),
            'lang' => sanitize_text_field($_POST['lang'] ?? 'es'),
            'extra_prompt' => sanitize_textarea_field($_POST['extra'] ?? ''),
            'outline' => $outline,
            'intro_enable' => filter_var($_POST['intro_enable'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'intro_custom' => sanitize_textarea_field($_POST['intro_custom'] ?? ''),
            'faqs_enable' => filter_var($_POST['faqs_enable'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'faqs_count' => absint($_POST['faqs_count'] ?? 5),
            'conclusion_enable' => filter_var($_POST['conclusion_enable'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'include_references' => filter_var($_POST['include_references'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'custom_references' => sanitize_textarea_field($_POST['custom_references'] ?? ''),
            // ⭐ PARÁMETROS DE WORDPRESS (CRÍTICOS)
            'status' => sanitize_text_field($_POST['status'] ?? 'draft'),
            'post_type' => sanitize_text_field($_POST['post_type'] ?? 'post'),
            'author' => absint($_POST['author'] ?? 0),
            // ⭐ CATEGORÍAS
            'categories' => isset($_POST['categories']) && is_array($_POST['categories']) 
                ? array_map('absint', $_POST['categories']) 
                : [],
        ];
        
        // Si hay briefing cacheado para esta keyword, usarlo
        if (!empty($keyword)) {
            $briefing = get_transient('asap_briefing_' . md5($keyword));
            if ($briefing && is_array($briefing)) {
                $params['briefing'] = $briefing;
            }
        }
        
        // ⭐ Agregar configuración de imágenes para que pueda generar imágenes de contenido
        $params['image_settings'] = $this->get_image_settings();
        // ⭐ AGREGAR API KEY DE REPLICATE
        $params['image_settings']['replicate_api_key'] = get_option('asap_ia_replicate_api_token', '');
        
        // Generar artículo por secciones (ahora devuelve array con post_id, session_id, etc.)
        $result = $this->article_generator->generate_by_sections($params);
        
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }
        
        // El post ya fue creado por Article_Generator, obtener ID
        $post_id = $result['post_id'];
        $session_id = $result['session_id'];
        
        // Guardar metadata
        if (!empty($keyword)) {
            update_post_meta($post_id, '_asap_keyword', $keyword);
        }
        
        // Log de post creado
        $gen_logger = new ASAP_IA_Database_Generation_Logger();
        $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_POST, 'Post publicado exitosamente', [
            'post_id' => $post_id,
            'title' => $h1,
            'status' => get_post_status($post_id),
        ], $post_id, $keyword);
        
        wp_send_json_success([
            'message' => 'Artículo generado exitosamente',
            'post_id' => $post_id,
            'session_id' => $session_id, // ← Para polling de logs
            'edit_url' => get_edit_post_link($post_id, 'raw') ?: admin_url('post.php?action=edit&post=' . $post_id),
            'view_url' => get_permalink($post_id) ?: get_site_url() . '/?p=' . $post_id,
            'cost_usd' => $result['cost_usd'] ?? 0,
        ]);
    }

    /**
     * AJAX: Calcular costo de imágenes
     */
    public function ajax_calc_image_cost() {
        check_ajax_referer('asap_images_settings_action', 'nonce'); // ← FIX: Verificar nonce correcto
        
        $provider = sanitize_text_field($_POST['provider'] ?? 'openai');
        $qty = max(1, intval($_POST['qty'] ?? 1));
        $S = $this->get_image_settings();

        if ($provider === 'openai') {
            $size = sanitize_text_field($_POST['size'] ?? '1024x1024');
            $quality = ($_POST['quality'] ?? 'standard') === 'hd' ? 'hd' : 'standard';
            list($w, $h) = array_map('intval', explode('x', $size));
            if ($w <= 0 || $h <= 0) $w = $h = 1024;
            $mpx = ($w * $h) / 1048576.0;
            $base = (float)$S['openai_price_per_mpx'];
            $mult = $quality === 'hd' ? (float)$S['openai_hd_mult'] : 1.0;
            $cost = $qty * $mpx * $base * $mult;
            $cost = number_format($cost, 4, '.', '');
            wp_send_json_success(['estimated_usd' => $cost, 'note' => "DALL-E 3, {$size}, {$quality}"]);
        } elseif ($provider === 'replicate') {
            $model = sanitize_text_field($_POST['model'] ?? 'flux-schnell');
            $aspect = sanitize_text_field($_POST['aspect'] ?? '1:1');
            
            // Estimar megapixels según aspect ratio común (≈1MP para todos)
            $mpx = 1.0; // Simplificado: la mayoría de Replicate usan ~1MP
            
            $rate = isset($S['replicate_rates'][$model]) ? (float)$S['replicate_rates'][$model] : 0.006;
            $cost = $qty * $mpx * $rate;
            $cost = number_format($cost, 4, '.', '');
            wp_send_json_success(['estimated_usd' => $cost, 'note' => "Replicate, {$model}, {$aspect}"]);
        } else {
            wp_send_json_error(['message' => 'Proveedor no soportado.']);
        }
    }
}

new ASAP_Manual_IA_Rewriter();
