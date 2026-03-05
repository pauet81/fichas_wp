<?php
/**
 * AJAX Handler
 * 
 * Centraliza todos los endpoints AJAX del sistema de IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\AJAX
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_AJAX_Handler {
    
    /**
     * @var object Instancia principal (para compatibilidad y acceso a métodos privados)
     */
    private $instance;
    
    /**
     * @var ASAP_IA_Core_Token_Calculator
     */
    private $token_calculator;
    
    /**
     * @var ASAP_IA_Generators_Outline_Generator
     */
    private $outline_generator;
    
    /**
     * @var ASAP_IA_Queue_Queue_Manager
     */
    private $queue_manager;
    
    /**
     * @var ASAP_IA_Queue_Job_Processor
     */
    private $job_processor;
    
    /**
     * @var ASAP_IA_Database_Logger
     */
    private $logger;
    
    /**
     * @var ASAP_IA_Research_Briefing_Builder
     */
    private $briefing_builder;
    
    /**
     * @var ASAP_IA_Research_PAA_Processor
     */
    private $paa_processor;
    
    /**
     * Constructor
     * 
     * @param object $instance Instancia de ASAP_Manual_IA_Rewriter
     */
    public function __construct($instance) {
        $this->instance = $instance;
        
        // Obtener dependencias de la instancia usando reflexión
        $this->token_calculator = $this->get_instance_property('token_calculator');
        $this->outline_generator = $this->get_instance_property('outline_generator');
        $this->queue_manager = $this->get_instance_property('queue_manager');
        $this->job_processor = $this->get_instance_property('job_processor');
        $this->logger = $this->get_instance_property('logger');
        
        // Inicializar módulos de Research
        $this->briefing_builder = new ASAP_IA_Research_Briefing_Builder();
        $this->paa_processor = new ASAP_IA_Research_PAA_Processor(
            $this->get_instance_property('openai_client'),
            $this->token_calculator
        );
    }
    
    /**
     * Registra todos los hooks AJAX
     */
    public function register_hooks() {
        // Generación y outline
        add_action('wp_ajax_asap_manual_rewrite', [$this, 'manual_rewrite']);
        add_action('wp_ajax_asap_suggest_outline', [$this, 'suggest_outline']);
        add_action('wp_ajax_asap_extract_competitor_structure', [$this, 'extract_competitor_structure']);
        add_action('wp_ajax_asap_get_related_keywords', [$this, 'get_related_keywords']);
        add_action('wp_ajax_asap_save_ia_defaults', [$this, 'save_ia_defaults']);
        add_action('wp_ajax_asap_get_generation_progress', [$this, 'get_generation_progress']);
        
        // Validación y costos
        add_action('wp_ajax_asap_test_openai_key', [$this, 'test_openai_key']);
        add_action('wp_ajax_asap_test_gemini_key', [$this, 'test_gemini_key']);
        add_action('wp_ajax_asap_test_replicate_token', [$this, 'test_replicate_token']);
        add_action('wp_ajax_asap_test_valueserp_key', [$this, 'test_valueserp_key']);
        add_action('wp_ajax_asap_calc_article_cost', [$this, 'calc_article_cost']);
        add_action('wp_ajax_asap_calc_meta_cost', [$this, 'calc_meta_cost']);
        add_action('wp_ajax_asap_calc_image_cost', [$this, 'calc_image_cost']);
        
        // Cola de generación
        add_action('wp_ajax_asap_add_to_queue', [$this, 'add_to_queue']);
        add_action('wp_ajax_asap_process_next_job', [$this, 'process_next_job']);
        add_action('wp_ajax_asap_process_queue_item', [$this, 'process_queue_item']);
        add_action('wp_ajax_asap_delete_queue_item', [$this, 'delete_queue_item']);
        add_action('wp_ajax_asap_retry_queue_item', [$this, 'retry_queue_item']);
        add_action('wp_ajax_asap_clear_completed_queue', [$this, 'clear_completed_queue']);
        add_action('wp_ajax_asap_clear_all_queue', [$this, 'clear_all_queue']);
        add_action('wp_ajax_asap_toggle_auto_queue', [$this, 'toggle_auto_queue']);
        add_action('wp_ajax_asap_toggle_queue_email', [$this, 'toggle_queue_email']);
        add_action('wp_ajax_asap_save_auto_publish_config', [$this, 'save_auto_publish_config']);
        add_action('wp_ajax_asap_import_csv_to_queue', [$this, 'import_csv_to_queue']);
        
        // SERP Analysis & Research (NUEVO)
        add_action('wp_ajax_asap_analyze_serp', [$this, 'analyze_serp']);
        add_action('wp_ajax_asap_create_briefing', [$this, 'create_briefing']);
        add_action('wp_ajax_asap_suggest_h2_from_serp', [$this, 'suggest_h2_from_serp']);
        add_action('wp_ajax_asap_extract_entities', [$this, 'extract_entities']);
        
        // Background Tasks con Polling (evita timeouts)
        add_action('wp_ajax_asap_start_background_task', [$this, 'start_background_task']);
        add_action('wp_ajax_asap_poll_task_status', [$this, 'poll_task_status']);
        add_action('wp_ajax_asap_pause_task', [$this, 'pause_task']);
        add_action('wp_ajax_asap_resume_task', [$this, 'resume_task']);
        add_action('wp_ajax_asap_cancel_task', [$this, 'cancel_task']);
        add_action('wp_ajax_asap_restart_task', [$this, 'restart_task']);
        add_action('wp_ajax_asap_retry_task', [$this, 'retry_task']);
        
        // Generation Logs (logs detallados en tiempo real)
        add_action('wp_ajax_asap_get_generation_logs', [$this, 'get_generation_logs']);
        add_action('wp_ajax_asap_get_recent_logs', [$this, 'get_recent_logs']);
        add_action('wp_ajax_asap_clean_old_logs', [$this, 'clean_old_logs']);
    }
    
    /**
     * Llama un método de la instancia usando reflexión
     */
    private function call_instance_method($method, ...$args) {
        $reflection = new ReflectionMethod($this->instance, $method);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($this->instance, $args);
    }
    
    /**
     * Obtiene una propiedad privada de la instancia
     */
    private function get_instance_property($property) {
        $reflection = new ReflectionProperty($this->instance, $property);
        $reflection->setAccessible(true);
        return $reflection->getValue($this->instance);
    }
    
    // =========================================================================
    // ENDPOINTS AJAX
    // =========================================================================
    
    /**
     * AJAX: Generar artículo completo
     */
    public function manual_rewrite() {
        check_ajax_referer('asap_manual_rewrite_action', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }
        
        // ✅ Aumentar límites de tiempo para artículos largos
        set_time_limit(300); // 5 minutos
        ini_set('max_execution_time', '300');
        
        // Delegar al método original
        $this->call_instance_method('ajax_manual_rewrite');
    }
    
    /**
     * AJAX: Sugerir outline H2
     */
    public function suggest_outline() {
        check_ajax_referer('asap_outline_action', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }
        
        $this->call_instance_method('ajax_suggest_outline');
    }
    
    /**
     * AJAX: Extraer estructura de competidor
     */
    public function extract_competitor_structure() {
        check_ajax_referer('asap_competitor_action', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }
        
        $this->call_instance_method('ajax_extract_competitor_structure');
    }
    
    /**
     * AJAX: Obtener keywords relacionadas
     */
    public function get_related_keywords() {
        check_ajax_referer('asap_related_keywords', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }
        
        $this->call_instance_method('ajax_get_related_keywords');
    }
    
    /**
     * AJAX: Test OpenAI API Key
     */
    public function test_openai_key() {
        check_ajax_referer('asap_test_openai_key', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        
        if (empty($api_key)) {
            wp_send_json_error(['message' => 'La API Key está vacía.']);
        }

        if (strlen($api_key) < 20) {
            wp_send_json_error(['message' => 'La API Key parece inválida (muy corta).']);
        }

        // Hacer una llamada de prueba simple a OpenAI
        $endpoint = 'https://api.openai.com/v1/models';
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 15,
        ];

        $response = wp_remote_get($endpoint, $args);
        
        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => 'Error de conexión: ' . $response->get_error_message()
            ]);
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($code === 200) {
            wp_send_json_success([
                'message' => 'Conexión exitosa! Tu API Key funciona correctamente.'
            ]);
        } elseif ($code === 401) {
            wp_send_json_error([
                'message' => 'API Key inválida. Verifica tu clave en OpenAI: <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com/api-keys</a>'
            ]);
        } elseif ($code === 429) {
            wp_send_json_error([
                'message' => 'Límite de tasa excedido. Verifica tu plan en OpenAI: <a href="https://platform.openai.com/account/billing" target="_blank">Ver facturación</a>'
            ]);
        } else {
            $error_msg = isset($data['error']['message']) ? $data['error']['message'] : 'Error desconocido';
            wp_send_json_error([
                'message' => 'Error de OpenAI: ' . esc_html($error_msg)
            ]);
        }
    }
    
    /**
     * AJAX: Test Gemini API Key
     */
    public function test_gemini_key() {
        check_ajax_referer('asap_test_gemini_key', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        
        if (empty($api_key)) {
            wp_send_json_error(['message' => 'La API Key está vacía.']);
        }

        if (strlen($api_key) < 20) {
            wp_send_json_error(['message' => 'La API Key parece inválida (muy corta).']);
        }

        // Hacer una llamada de prueba simple a Gemini
        $model = 'gemini-2.5-flash'; // ✅ Modelo actualizado
        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $api_key
        );
        
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Responde con una sola palabra: OK']
                    ]
                ]
            ]
        ];
        
        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
            'body' => wp_json_encode($body),
        ];

        $response = wp_remote_post($endpoint, $args);
        
        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => 'Error de conexión: ' . $response->get_error_message()
            ]);
        }

        $code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        if ($code === 200 && !empty($data['candidates'])) {
            wp_send_json_success([
                'message' => 'Conexión exitosa! Tu API Key de Gemini funciona correctamente.'
            ]);
        } elseif ($code === 400) {
            $error_msg = isset($data['error']['message']) ? $data['error']['message'] : 'Solicitud inválida';
            wp_send_json_error([
                'message' => 'Error en la solicitud: ' . esc_html($error_msg)
            ]);
        } elseif ($code === 403) {
            wp_send_json_error([
                'message' => 'API Key inválida o sin permisos. Verifica tu clave en: <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>'
            ]);
        } elseif ($code === 429) {
            wp_send_json_error([
                'message' => 'Límite de tasa excedido. Verifica tu cuota en Google Cloud.'
            ]);
        } else {
            $error_msg = isset($data['error']['message']) ? $data['error']['message'] : 'Error desconocido';
            wp_send_json_error([
                'message' => 'Error de Gemini: ' . esc_html($error_msg)
            ]);
        }
    }
    
    /**
     * AJAX: Test Replicate API Token
     */
    public function test_replicate_token() {
        check_ajax_referer('asap_test_replicate_token', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        $api_token = isset($_POST['api_token']) ? sanitize_text_field($_POST['api_token']) : '';
        
        if (empty($api_token)) {
            wp_send_json_error(['message' => 'El Token está vacío.']);
        }

        if (strlen($api_token) < 10) {
            wp_send_json_error(['message' => 'El Token parece inválido (muy corto).']);
        }

        // Hacer una llamada de prueba a Replicate API (verificar cuenta)
        $endpoint = 'https://api.replicate.com/v1/models';
        
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
        ];

        $response = wp_remote_get($endpoint, $args);
        
        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => 'Error de conexión: ' . $response->get_error_message()
            ]);
        }

        $code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($code === 200) {
            wp_send_json_success([
                'message' => 'Conexión exitosa! Tu Token de Replicate funciona correctamente.'
            ]);
        } elseif ($code === 401) {
            wp_send_json_error([
                'message' => 'Token inválido o expirado. Verifica tu token en: <a href="https://replicate.com/account/api-tokens" target="_blank">Replicate</a>'
            ]);
        } elseif ($code === 403) {
            wp_send_json_error([
                'message' => 'Token sin permisos suficientes. Crea un nuevo token en Replicate.'
            ]);
        } elseif ($code === 429) {
            wp_send_json_error([
                'message' => 'Límite de tasa excedido, pero el token es válido.'
            ]);
        } else {
            $data = json_decode($response_body, true);
            $error_msg = isset($data['detail']) ? $data['detail'] : 'Error desconocido';
            wp_send_json_error([
                'message' => 'Error de Replicate: ' . esc_html($error_msg)
            ]);
        }
    }
    
    /**
     * AJAX: Test ValueSERP API Key
     */
    public function test_valueserp_key() {
        check_ajax_referer('asap_test_valueserp_key', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        
        if (empty($api_key)) {
            wp_send_json_error(['message' => 'La API Key está vacía.']);
        }

        if (strlen($api_key) < 10) {
            wp_send_json_error(['message' => 'La API Key parece inválida (muy corta).']);
        }

        // Hacer una llamada de prueba a ValueSERP (búsqueda simple)
        $test_query = 'test';
        $endpoint = 'https://api.valueserp.com/search?' . http_build_query([
            'api_key' => $api_key,
            'q' => $test_query,
            'location' => 'United States',
            'gl' => 'us',
            'hl' => 'en',
            'num' => 1,
        ]);
        
        $args = [
            'timeout' => 25, // Aumentado de 10 a 25 segundos
            'sslverify' => false, // Evitar problemas SSL
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $response = wp_remote_get($endpoint, $args);
        
        if (is_wp_error($response)) {
            $error_msg = $response->get_error_message();
            
            // Mensaje específico para timeout
            if (strpos($error_msg, 'cURL error 28') !== false || strpos($error_msg, 'timeout') !== false) {
                wp_send_json_error([
                    'message' => '⏱️ Timeout: ValueSERP tardó más de 25 segundos en responder. Intenta de nuevo en unos momentos.'
                ]);
            }
            
            wp_send_json_error([
                'message' => 'Error de conexión: ' . $error_msg
            ]);
        }

        $code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        if ($code === 200 && isset($data['search_information'])) {
            wp_send_json_success([
                'message' => 'Conexión exitosa! Tu API Key de ValueSERP funciona correctamente.'
            ]);
        } elseif ($code === 401 || $code === 403) {
            wp_send_json_error([
                'message' => 'API Key inválida o sin permisos. Verifica tu clave en: <a href="https://www.valueserp.com/dashboard" target="_blank">ValueSERP Dashboard</a>'
            ]);
        } elseif ($code === 429) {
            wp_send_json_error([
                'message' => 'Límite de búsquedas excedido. Verifica tu plan en ValueSERP.'
            ]);
        } else {
            $error_msg = isset($data['error']) ? $data['error'] : 'Error desconocido';
            wp_send_json_error([
                'message' => 'Error de ValueSERP: ' . esc_html($error_msg)
            ]);
        }
    }
    
    /**
     * AJAX: Calcular costo de artículo
     */
    public function calc_article_cost() {
        check_ajax_referer('asap_calc_cost_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $target_len = max(500, min(8000, intval($_POST['target_len'] ?? 3000)));
        $h2_count = max(1, intval($_POST['h2_count'] ?? 5));
        $has_faqs = isset($_POST['has_faqs']) && $_POST['has_faqs'] === '1';
        $has_intro = isset($_POST['has_intro']) && $_POST['has_intro'] === '1';
        $has_conclusion = isset($_POST['has_conclusion']) && $_POST['has_conclusion'] === '1';

        // Obtener modelo actual
        $provider = get_option('asap_ia_provider', 'openai');
        $model = $provider === 'openai' 
            ? get_option('asap_ia_openai_model', 'gpt-4o-mini')
            : get_option('asap_ia_gemini_model', 'gemini-2.5-flash-lite');

        // CÁLCULO REAL considerando TODAS las llamadas con contexto acumulado
        // Un artículo típico hace estas llamadas:
        // 1. Intro (prompt + keywords + estructura)
        // 2. Sección 1 (prompt + intro + contexto)
        // 3. Sección 2 (prompt + intro + sección 1)
        // 4. Sección 3 (prompt + intro + sección 1 + sección 2)
        // ... y así sucesivamente
        // El contexto se acumula en cada llamada
        
        // TOKENS DE ENTRADA (más realista):
        // - Prompt base por llamada: ~800 tokens (instrucciones + keywords + estructura)
        // - Contexto acumulado: crece con cada sección
        $tokens_input = 0;
        $accumulated_context = 0;
        
        // 1. Introducción
        if ($has_intro) {
            $tokens_input += 800; // Prompt completo
            $accumulated_context += 300; // La intro generada
        }
        
        // 2. Secciones (cada una incluye TODO el contexto anterior)
        $words_per_section = intval($target_len / $h2_count);
        for ($i = 0; $i < $h2_count; $i++) {
            $tokens_input += 800; // Prompt base
            $tokens_input += $accumulated_context; // Contexto acumulado
            $accumulated_context += intval($words_per_section * 1.3); // Agregar nueva sección al contexto
        }
        
        // 3. Conclusión (incluye TODO el artículo generado hasta ahora)
        if ($has_conclusion) {
            $tokens_input += 600; // Prompt
            $tokens_input += $accumulated_context; // Todo el artículo
        }
        
        // 4. FAQs (incluye el artículo completo como contexto)
        if ($has_faqs) {
            $tokens_input += 700; // Prompt
            $tokens_input += intval($accumulated_context * 0.5); // Contexto parcial
        }
        
        // TOKENS DE SALIDA:
        // Palabras a tokens (1 palabra ≈ 1.3 tokens)
        $tokens_output = intval($target_len * 1.3);
        
        // Agregar tokens de intro, conclusión y FAQs
        if ($has_intro) $tokens_output += 200;
        if ($has_conclusion) $tokens_output += 150;
        if ($has_faqs) $tokens_output += 400;
        
        $cost = $this->token_calculator->calculate_cost($model, $tokens_input, $tokens_output);
        
        wp_send_json_success([
            'cost_usd' => number_format($cost, 4),
            'tokens_input' => number_format($tokens_input),
            'tokens_output' => number_format($tokens_output),
            'tokens_total' => number_format($tokens_input + $tokens_output),
            'model' => $model
        ]);
    }
    
    /**
     * AJAX: Calcular costo de meta
     */
    public function calc_meta_cost() {
        check_ajax_referer('asap_calc_cost_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        // Meta tags son muy económicos
        $tokens_input = 130;  // Total para title + desc
        $tokens_output = 45;  // Total para title + desc
        
        $cost = $this->token_calculator->calculate_cost('gpt-4.1-mini', $tokens_input, $tokens_output);
        
        wp_send_json_success([
            'cost_usd' => number_format($cost, 6),
            'tokens_input' => $tokens_input,
            'tokens_output' => $tokens_output,
            'tokens_total' => $tokens_input + $tokens_output,
            'note' => 'Por artículo (título + descripción)'
        ]);
    }
    
    /**
     * AJAX: Calcular costo de imagen
     */
    public function calc_image_cost() {
        check_ajax_referer('asap_calc_cost_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        // Delegar al método original (depende de muchas opciones específicas)
        $this->call_instance_method('ajax_calc_image_cost');
    }
    
    /**
     * AJAX: Agregar a cola
     */
    public function add_to_queue() {
        check_ajax_referer('asap_manual_rewrite_action', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        // Recopilar todos los parámetros
        // Decodificar outline si viene como JSON string
        $outline_raw = isset($_POST['outline']) ? wp_unslash($_POST['outline']) : '[]';
        $outline = json_decode($outline_raw, true);
        if (!is_array($outline)) {
            $outline = [];
        }
        
        $params = [
            'type' => 'article',
            'h1' => sanitize_text_field($_POST['h1'] ?? ''),
            'keyword' => sanitize_text_field($_POST['keyword'] ?? ''),
            'secondary_keywords' => isset($_POST['secondary_keywords']) ? array_map('sanitize_text_field', (array) $_POST['secondary_keywords']) : [],
            'target_len' => max(500, min(8000, intval($_POST['target_len'] ?? 3000))),
            'style' => sanitize_text_field($_POST['style'] ?? 'informativo'),
            'status' => sanitize_text_field($_POST['status'] ?? 'draft'),
            'post_type' => sanitize_text_field($_POST['post_type'] ?? 'post'),
            'author' => absint($_POST['author'] ?? 0),
            'lang' => sanitize_text_field($_POST['lang'] ?? 'es'),
            'extra' => sanitize_textarea_field($_POST['extra'] ?? ''),
            'outline' => $outline,
            'intro_enable' => isset($_POST['intro_enable']) && $_POST['intro_enable'] === '1',
            'intro_custom' => sanitize_textarea_field($_POST['intro_custom'] ?? ''),
            'faqs_enable' => isset($_POST['faqs_enable']) && $_POST['faqs_enable'] === '1',
            'faqs_count' => max(1, min(15, intval($_POST['faqs_count'] ?? 5))),
            'conclusion_enable' => isset($_POST['conclusion_enable']) && $_POST['conclusion_enable'] === '1',
            'include_references' => isset($_POST['include_references']) && $_POST['include_references'] === '1',
            'custom_references' => sanitize_textarea_field($_POST['custom_references'] ?? ''),
        ];

        if (empty($params['h1'])) {
            wp_send_json_error(['message' => 'El H1 es obligatorio.']);
        }

        $result = $this->queue_manager->add_to_queue($params);
        
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success([
            'queue_id' => $result,
            'message' => '✅ Artículo agregado a la cola correctamente.',
        ]);
    }
    
    /**
     * AJAX: Procesar siguiente job
     */
    public function process_queue_item() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
        if (!$job_id) {
            wp_send_json_error(['message' => 'ID de trabajo no válido.']);
        }

        // Procesar el job específico
        $result = $this->job_processor->process_job_by_id($job_id);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success([
            'message' => 'Artículo generado exitosamente.',
            'post_id' => $result
        ]);
    }
    
    public function process_next_job() {
        check_ajax_referer('asap_process_queue', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }

        // Verificar límite de tasa ANTES de obtener el job
        if (!$this->queue_manager->can_process()) {
            wp_send_json_success([
                'completed' => false,
                'limit_reached' => true,
                'message' => '⚠️ Límite alcanzado (máx. 5 por hora)'
            ]);
            return;
        }

        $job = $this->queue_manager->get_next_job();
        
        if (!$job) {
            // No hay más jobs pendientes - Verificar si enviar email
            $this->queue_manager->maybe_send_completion_email();
            
            wp_send_json_success([
                'completed' => true,
                'message' => 'Cola procesada completamente.',
            ]);
            return;
        }

        // Procesar el job usando Job_Processor
        $result = $this->job_processor->process_next();

        $params = json_decode($job->params, true);
        $current_title = isset($params['h1']) ? $params['h1'] : 'Artículo';

        // Calcular progreso DESPUÉS de procesar
        $stats = $this->queue_manager->get_stats();
        $total_processed = $stats['completed_today'] + $stats['failed'];
        $total_pending = $stats['pending'];
        $total = $total_processed + $total_pending;
        $progress = $total > 0 ? round(($total_processed / $total) * 100) : 0;
        
        // Si hubo error pero queremos continuar con los demás
        $error_msg = is_wp_error($result) ? $result->get_error_message() : null;
        
        wp_send_json_success([
            'completed' => false,
            'progress' => $progress,
            'processed' => $total_processed,
            'total' => $total,
            'current_title' => $current_title,
            'post_id' => is_wp_error($result) ? null : $result,
            'error' => $error_msg,
            'message' => $error_msg ? "❌ Error en '{$current_title}': {$error_msg}" : "✓ '{$current_title}' generado exitosamente"
        ]);
    }
    
    /**
     * AJAX: Eliminar item de cola
     */
    public function delete_queue_item() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $job_id = absint($_POST['job_id'] ?? 0);
        if (!$job_id) {
            wp_send_json_error(['message' => 'ID de trabajo inválido.']);
        }

        $this->queue_manager->delete_item($job_id);

        wp_send_json_success(['message' => 'Trabajo eliminado correctamente.']);
    }
    
    /**
     * AJAX: Reintentar item de cola
     */
    public function retry_queue_item() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $job_id = absint($_POST['job_id'] ?? 0);
        if (!$job_id) {
            wp_send_json_error(['message' => 'ID de trabajo inválido.']);
        }

        $updated = $this->queue_manager->update_status($job_id, 'pending', ['error_message' => '']);
        
        if (!$updated) {
            wp_send_json_error(['message' => 'No se pudo actualizar el trabajo.']);
        }

        wp_send_json_success(['message' => 'Trabajo reiniciado. Será procesado nuevamente.']);
    }
    
    /**
     * AJAX: Limpiar cola completada
     */
    public function clear_completed_queue() {
        check_ajax_referer('asap_clear_queue', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_ia_queue';
        $deleted = $wpdb->delete($table_name, ['status' => 'completed'], ['%s']);
        
        wp_send_json_success(['message' => "Eliminados {$deleted} trabajos completados."]);
    }
    
    /**
     * AJAX: Vaciar toda la cola (pendientes y fallidos)
     */
    public function clear_all_queue() {
        check_ajax_referer('asap_clear_queue', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $deleted = $this->queue_manager->clear_all_queue();
        
        wp_send_json_success(['message' => "Cola vaciada: {$deleted} trabajos eliminados."]);
    }
    
    /**
     * AJAX: Toggle auto procesamiento
     */
    public function toggle_auto_queue() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $enabled = sanitize_text_field($_POST['enabled'] ?? '0');
        update_option('asap_auto_process_queue', $enabled);
        
        wp_send_json_success(['message' => 'Configuración actualizada.']);
    }
    
    /**
     * AJAX: Toggle notificaciones email
     */
    public function toggle_queue_email() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $enabled = sanitize_text_field($_POST['enabled'] ?? '0');
        update_option('asap_queue_email_notifications', $enabled);
        
        wp_send_json_success(['message' => 'Notificaciones por email ' . ($enabled === '1' ? 'activadas' : 'desactivadas') . '.']);
    }
    
    /**
     * AJAX: Guardar configuración de publicación automática
     */
    public function save_auto_publish_config() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }
        
        $enabled = sanitize_text_field($_POST['enabled'] ?? '0');
        $interval = max(1, min(168, intval($_POST['interval'] ?? 24)));
        
        update_option('asap_queue_auto_publish_enabled', $enabled);
        update_option('asap_queue_auto_publish_interval', $interval);
        
        // Si se activa, programar el cron
        if ($enabled === '1') {
            if (!wp_next_scheduled('asap_auto_publish_posts')) {
                wp_schedule_event(time(), 'hourly', 'asap_auto_publish_posts');
            }
        } else {
            // Si se desactiva, limpiar el cron
            $timestamp = wp_next_scheduled('asap_auto_publish_posts');
            if ($timestamp) {
                wp_unschedule_event($timestamp, 'asap_auto_publish_posts');
            }
        }
        
        wp_send_json_success([
            'message' => 'Configuración guardada. ' . ($enabled === '1' 
                ? "Los posts se publicarán cada {$interval} horas." 
                : 'Publicación automática desactivada.')
        ]);
    }
    
    /**
     * AJAX: Importar CSV a cola
     */
    public function import_csv_to_queue() {
        check_ajax_referer('asap_queue_actions', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $items_json = isset($_POST['items']) ? wp_unslash($_POST['items']) : '[]';
        $items = json_decode($items_json, true);
        
        if (!is_array($items) || empty($items)) {
            $logger->error($session_id, 'csv_import', 'Intento de importar CSV sin artículos');
            wp_send_json_error(['message' => 'No hay artículos para importar.']);
        }
        
        $total_items = count($items);
        $logger->info($session_id, 'csv_import', "Iniciando importación de CSV: {$total_items} artículos");

        $added = 0;
        $errors = 0;
        $start_time = microtime(true);
        
        // Obtener configuración de publicación automática
        $auto_publish_enabled = get_option('asap_queue_auto_publish_enabled', '0') === '1';
        $publish_interval_hours = intval(get_option('asap_queue_auto_publish_interval', 24));
        
        foreach ($items as $index => $item) {
            $h1 = $item['h1'] ?? 'Sin título';
            
            // Convertir H2s a formato outline
            $outline = [];
            if (isset($item['h2s']) && is_array($item['h2s'])) {
                foreach ($item['h2s'] as $h2) {
                    $outline[] = ['h2' => $h2, 'h3' => []];
                }
            }
            
            // Procesar secondary_keywords (formato: "keyword1;keyword2;keyword3")
            $secondary_keywords = [];
            if (!empty($item['secondary_keywords'])) {
                $secondary_keywords = array_map('trim', explode(';', $item['secondary_keywords']));
                $secondary_keywords = array_filter($secondary_keywords); // Remover vacíos
            }
            
            // Procesar competition_urls (formato: "url1;url2;url3")
            $competition_urls = [];
            if (!empty($item['competition_urls'])) {
                $competition_urls = array_map('trim', explode(';', $item['competition_urls']));
                $competition_urls = array_filter($competition_urls); // Remover vacíos
            }

            // Calcular scheduled_at si la publicación automática está habilitada
            $scheduled_at = null;
            if ($auto_publish_enabled && $publish_interval_hours > 0) {
                // Programar cada artículo con un intervalo de N horas
                // Usar current_time() de WordPress para ser consistente con la zona horaria del sitio
                $hours_delay = $index * $publish_interval_hours;
                $scheduled_timestamp = strtotime("+{$hours_delay} hours", strtotime(current_time('mysql')));
                $scheduled_at = date('Y-m-d H:i:s', $scheduled_timestamp);
            }
            
            $params = [
                'type' => 'article',
                'h1' => $h1,
                'keyword' => $item['keyword'] ?? '',
                'secondary_keywords' => $secondary_keywords,
                'target_len' => intval($item['target_len'] ?? 3000),
                'style' => $item['style'] ?? 'informativo',
                'lang' => $item['lang'] ?? 'es',
                'status' => $item['status'] ?? 'draft',
                'post_type' => $item['post_type'] ?? 'post',
                'author' => intval($item['author'] ?? 0),
                'extra' => '',
                'outline' => wp_json_encode($outline),
                'intro_enable' => true,  // Siempre habilitada ahora
                'intro_custom' => '',
                'faqs_enable' => isset($item['faqs_enable']) ? (bool)$item['faqs_enable'] : false,
                'faqs_count' => intval($item['faqs_count'] ?? 5),
                'conclusion_enable' => isset($item['conclusion_enable']) ? (bool)$item['conclusion_enable'] : false,
                'competition_urls' => $competition_urls,
                'categories' => [], // ⭐ Agregar categorías (vacío por defecto)
                'scheduled_at' => $scheduled_at, // ✅ Programar según intervalo configurado
            ];

            // ✅ DEBUG: Loguear los parámetros que se están enviando
            $scheduled_info = $scheduled_at ? " (programado: {$scheduled_at})" : " (inmediato)";
            $logger->info($session_id, 'csv_import', "Intentando agregar artículo " . ($index + 1) . ": H1='{$h1}', Keyword='{$params['keyword']}', H2s=" . count($outline) . $scheduled_info);

            $result = $this->queue_manager->add_to_queue($params);
            if (!is_wp_error($result)) {
                $added++;
                $logger->success($session_id, 'csv_import', "✅ Artículo " . ($index + 1) . "/{$total_items} agregado: '{$h1}'");
            } else {
                $errors++;
                $error_msg = $result->get_error_message();
                $logger->error($session_id, 'csv_import', "❌ Error artículo " . ($index + 1) . "/{$total_items} ('{$h1}'): {$error_msg}");
            }
        }
        
        $duration = microtime(true) - $start_time;
        
        // ✅ Mensaje personalizado según el resultado
        if ($errors > 0 && $added > 0) {
            // Importación parcial (probablemente por límite de cola)
            $logger->warning($session_id, 'csv_import', "Importación parcial: {$added} artículos agregados, {$errors} no pudieron agregarse", [
                'total' => $total_items,
                'added' => $added,
                'errors' => $errors
            ], null, null, $duration);
            
            wp_send_json_success([
                'message' => "⚠️ {$added} artículos agregados. {$errors} no pudieron agregarse (cola llena). Procesa algunos trabajos y vuelve a importar los restantes.",
                'added' => $added,
                'errors' => $errors,
                'partial' => true
            ]);
        } elseif ($errors > 0 && $added === 0) {
            // Todos fallaron
            $logger->error($session_id, 'csv_import', "Importación fallida: 0 artículos agregados, {$errors} errores", [
                'total' => $total_items,
                'added' => $added,
                'errors' => $errors
            ], null, null, $duration);
            
            wp_send_json_error([
                'message' => "❌ No se pudo agregar ningún artículo. La cola puede estar llena (máximo 100 trabajos).",
                'added' => 0,
                'errors' => $errors
            ]);
        } else {
            // Todo OK
            $logger->success($session_id, 'csv_import', "Importación completada: {$added} artículos agregados", [
                'total' => $total_items,
                'added' => $added,
                'errors' => $errors
            ], null, null, $duration);
            
            wp_send_json_success([
                'message' => "✅ {$added} artículos agregados a la cola.",
                'added' => $added,
            ]);
        }
    }
    
    // =========================================================================
    // NUEVOS ENDPOINTS: SERP ANALYSIS & RESEARCH
    // =========================================================================
    
    /**
     * AJAX: Analizar SERPs para una keyword
     * 
     * IMPORTANTE: Este proceso puede tomar 10-20 segundos
     * Se recomienda llamarlo en background o con loading state
     */
    public function analyze_serp() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        
        if (empty($keyword)) {
            $logger->error($session_id, 'serp_analysis', 'Intento de analizar SERP sin keyword');
            wp_send_json_error(['message' => 'Keyword es obligatoria.']);
        }
        
        $logger->info($session_id, 'serp_analysis', "Iniciando análisis SERP para: '{$keyword}'");
        
        // Verificar si hay API key de ValueSERP
        $serp_analyzer = new ASAP_IA_Research_SERP_Analyzer();
        if (!$serp_analyzer->has_api_key()) {
            $logger->error($session_id, 'serp_analysis', 'ValueSERP API Key no configurada');
            wp_send_json_error([
                'message' => '⚠ Configura tu ValueSERP API Key en la pestaña Configuración. <a href="https://www.valueserp.com/" target="_blank">Obtener API Key →</a>'
            ]);
        }
        
        // Verificar cache primero
        $cached = $serp_analyzer->get_cached_serp_data($keyword);
        if ($cached) {
            $logger->info($session_id, 'serp_analysis', "Análisis SERP obtenido desde cache: '{$keyword}'");
            wp_send_json_success([
                'message' => '✅ Análisis SERP obtenido (desde cache)',
                'serp_data' => $cached,
                'from_cache' => true,
            ]);
            return;
        }
        
        // Analizar (puede tomar tiempo)
        $options = [
            'location' => sanitize_text_field($_POST['location'] ?? 'Argentina'),
            'language' => sanitize_text_field($_POST['language'] ?? 'es'),
            'num_results' => 10,
        ];
        
        $start_time = microtime(true);
        $serp_data = $serp_analyzer->analyze($keyword, $options);
        $duration = microtime(true) - $start_time;
        
        if (is_wp_error($serp_data)) {
            $logger->error($session_id, 'serp_analysis', "Error analizando SERP para '{$keyword}': " . $serp_data->get_error_message(), null, null, $keyword, $duration);
            wp_send_json_error(['message' => $serp_data->get_error_message()]);
        }
        
        $result_count = isset($serp_data['organic_results']) ? count($serp_data['organic_results']) : 0;
        $logger->success($session_id, 'serp_analysis', "Análisis SERP completado para '{$keyword}': {$result_count} resultados", [
            'results' => $result_count,
            'location' => $options['location'],
            'language' => $options['language']
        ], null, $keyword, $duration);
        
        wp_send_json_success([
            'message' => '✅ Análisis SERP completado',
            'serp_data' => $serp_data,
            'from_cache' => false,
        ]);
    }
    
    /**
     * AJAX: Crear briefing completo con análisis de competidores
     * 
     * IMPORTANTE: Proceso largo (20-30 segundos) - scraping de top 3
     * Mostrar loading state al usuario
     */
    public function create_briefing() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        
        if (empty($keyword)) {
            wp_send_json_error(['message' => 'Keyword es obligatoria.']);
        }
        
        // Verificar cache
        $cached = $this->briefing_builder->get_cached_briefing($keyword);
        if ($cached) {
            $summary = $this->briefing_builder->generate_summary($cached);
            
            wp_send_json_success([
                'message' => '✅ Briefing obtenido (desde cache)',
                'briefing' => $cached,
                'summary' => $summary,
                'from_cache' => true,
            ]);
            return;
        }
        
        // Crear briefing completo (proceso largo)
        $options = [
            'location' => sanitize_text_field($_POST['location'] ?? 'Argentina'),
            'language' => sanitize_text_field($_POST['language'] ?? 'es'),
            'use_cache' => true,
        ];
        
        $briefing = $this->briefing_builder->create_from_keyword($keyword, $options);
        
        if (is_wp_error($briefing)) {
            wp_send_json_error(['message' => $briefing->get_error_message()]);
        }
        
        // Generar resumen en texto
        $summary = $this->briefing_builder->generate_summary($briefing);
        
        wp_send_json_success([
            'message' => '✅ Briefing creado exitosamente',
            'briefing' => $briefing,
            'summary' => $summary,
            'from_cache' => false,
            'stats' => [
                'analyzed_urls' => $briefing['competition']['analyzed_urls'],
                'paa_count' => $briefing['paa_count'],
                'must_cover_count' => count($briefing['must_cover_topics']),
                'suggested_h2_count' => count($briefing['suggested_h2']),
            ],
        ]);
    }
    
    /**
     * AJAX: Sugerir H2 basados en análisis SERP
     * 
     * Versión mejorada de suggest_outline que usa datos de competidores
     */
    public function suggest_h2_from_serp() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        $h1 = sanitize_text_field($_POST['h1'] ?? '');
        
        if (empty($keyword)) {
            wp_send_json_error(['message' => 'Keyword es obligatoria.']);
        }
        
        // Obtener o crear briefing
        $briefing = $this->briefing_builder->get_cached_briefing($keyword);
        
        if (!$briefing) {
            // Crear briefing rápido (solo SERP, sin scraping)
            $serp_analyzer = new ASAP_IA_Research_SERP_Analyzer();
            $serp_data = $serp_analyzer->analyze($keyword);
            
            if (is_wp_error($serp_data)) {
                // Fallback a sugerencia con IA normal
                $this->call_instance_method('ajax_suggest_outline');
                return;
            }
            
            // Sugerencias basadas solo en títulos de SERPs
            $suggested_h2 = $this->extract_h2_from_serp_titles($serp_data);
            
            wp_send_json_success([
                'suggestions' => $suggested_h2,
                'source' => 'serp_titles',
                'message' => 'Sugerencias basadas en títulos de Google (análisis rápido)',
            ]);
            return;
        }
        
        // Tenemos briefing completo - usar H2 de competidores
        $suggested_h2 = array_column($briefing['suggested_h2'] ?? [], 'h2');
        
        // Limitar a 12 sugerencias
        $suggested_h2 = array_slice($suggested_h2, 0, 12);
        
        wp_send_json_success([
            'suggestions' => $suggested_h2,
            'source' => 'competitors',
            'message' => 'Sugerencias basadas en análisis de top 10 competidores',
            'briefing_available' => true,
            'briefing' => $briefing,
        ]);
    }
    
    /**
     * Extrae H2 potenciales de títulos de SERP (análisis rápido)
     */
    private function extract_h2_from_serp_titles($serp_data) {
        $h2_suggestions = [];
        
        foreach ($serp_data['organic_results'] ?? [] as $result) {
            $title = $result['title'] ?? '';
            
            // Convertir título a posible H2
            // Ej: "Los 10 Mejores..." → "Mejores opciones", "Comparativa", etc.
            $h2_variations = $this->title_to_h2_variations($title);
            $h2_suggestions = array_merge($h2_suggestions, $h2_variations);
        }
        
        // Eliminar duplicados
        $h2_suggestions = array_unique($h2_suggestions);
        
        return array_slice($h2_suggestions, 0, 12);
    }
    
    /**
     * Convierte título a variaciones de H2
     */
    private function title_to_h2_variations($title) {
        $variations = [];
        
        // Patrones comunes
        if (stripos($title, 'qué es') !== false) {
            $variations[] = '¿Qué es y para qué sirve?';
            $variations[] = 'Definición y conceptos básicos';
        }
        if (stripos($title, 'cómo') !== false) {
            $variations[] = 'Paso a paso';
            $variations[] = 'Guía práctica';
        }
        if (stripos($title, 'mejor') !== false || stripos($title, 'top') !== false) {
            $variations[] = 'Mejores opciones';
            $variations[] = 'Comparativa';
            $variations[] = 'Ventajas y desventajas';
        }
        
        return $variations;
    }
    
    /**
     * AJAX: Extraer entidades de texto
     * 
     * Útil para preview rápido de entidades sin briefing completo
     */
    public function extract_entities() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $keyword = sanitize_text_field($_POST['keyword'] ?? '');
        
        // Opción 1: Extraer de briefing existente
        if ($keyword) {
            $briefing = $this->briefing_builder->get_cached_briefing($keyword);
            
            if ($briefing && !empty($briefing['entities'])) {
                $summary = $this->briefing_builder->entity_extractor->generate_summary($briefing['entities']);
                
                wp_send_json_success([
                    'message' => '✅ Entidades obtenidas del briefing',
                    'entities' => $briefing['entities'],
                    'summary' => $summary,
                    'from_cache' => true,
                ]);
                return;
            }
        }
        
        // Opción 2: Extraer de texto directo (para testing)
        $text = isset($_POST['text']) ? wp_unslash($_POST['text']) : '';
        
        if (!empty($text)) {
            $entity_extractor = new ASAP_IA_Research_Entity_Extractor();
            $result = $entity_extractor->extract_from_text($text, $keyword);
            
            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            }
            
            wp_send_json_success([
                'message' => '✅ Entidades extraídas',
                'entities' => $result['entities'],
                'cost' => number_format($result['cost'], 5),
                'tokens' => $result['tokens'],
            ]);
            return;
        }
        
        wp_send_json_error(['message' => 'Se requiere keyword (con briefing) o texto para analizar.']);
    }
    
    /**
     * AJAX: Iniciar tarea en background
     * 
     * Retorna task_id para hacer polling
     */
    public function start_background_task() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_type = sanitize_text_field($_POST['task_type'] ?? '');
        $task_data = $_POST['task_data'] ?? [];
        
        if (empty($task_type)) {
            wp_send_json_error(['message' => 'Tipo de tarea requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        $task_id = $task_manager->create_task($task_type, $task_data);
        
        wp_send_json_success([
            'message' => '✅ Tarea creada',
            'task_id' => $task_id,
        ]);
    }
    
    /**
     * AJAX: Hacer polling del estado de una tarea
     * 
     * Se llama cada 2 segundos desde el frontend
     * Procesa UNA etapa (< 30 seg) y retorna el estado
     */
    public function poll_task_status() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_id = sanitize_text_field($_POST['task_id'] ?? '');
        
        if (empty($task_id)) {
            wp_send_json_error(['message' => 'Task ID requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        
        // Procesar siguiente etapa
        $task = $task_manager->process_next_step($task_id);
        
        if (!$task) {
            wp_send_json_error(['message' => 'Tarea no encontrada.']);
        }
        
        // Retornar estado
        wp_send_json_success([
            'task' => $task,
            'status' => $task['status'],
            'progress' => $task['progress'],
            'result' => $task['result'] ?? null,
            'error' => $task['error'] ?? null,
        ]);
    }
    
    /**
     * AJAX: Pausar tarea en ejecución
     */
    public function pause_task() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_id = sanitize_text_field($_POST['task_id'] ?? '');
        
        if (empty($task_id)) {
            wp_send_json_error(['message' => 'Task ID requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        $task = $task_manager->pause_task($task_id);
        
        if (is_wp_error($task)) {
            wp_send_json_error(['message' => $task->get_error_message()]);
        }
        
        wp_send_json_success([
            'message' => '⏸️ Tarea pausada',
            'task' => $task,
        ]);
    }
    
    /**
     * AJAX: Reanudar tarea pausada
     */
    public function resume_task() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_id = sanitize_text_field($_POST['task_id'] ?? '');
        
        if (empty($task_id)) {
            wp_send_json_error(['message' => 'Task ID requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        $task = $task_manager->resume_task($task_id);
        
        if (is_wp_error($task)) {
            wp_send_json_error(['message' => $task->get_error_message()]);
        }
        
        wp_send_json_success([
            'message' => '▶️ Tarea reanudada',
            'task' => $task,
        ]);
    }
    
    /**
     * AJAX: Cancelar tarea
     */
    public function cancel_task() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_id = sanitize_text_field($_POST['task_id'] ?? '');
        
        if (empty($task_id)) {
            wp_send_json_error(['message' => 'Task ID requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        $task = $task_manager->cancel_task($task_id);
        
        if (is_wp_error($task)) {
            wp_send_json_error(['message' => $task->get_error_message()]);
        }
        
        wp_send_json_success([
            'message' => '❌ Tarea cancelada',
            'task' => $task,
        ]);
    }
    
    /**
     * AJAX: Reiniciar tarea desde el principio
     */
    public function restart_task() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_id = sanitize_text_field($_POST['task_id'] ?? '');
        
        if (empty($task_id)) {
            wp_send_json_error(['message' => 'Task ID requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        $task = $task_manager->restart_task($task_id);
        
        if (is_wp_error($task)) {
            wp_send_json_error(['message' => $task->get_error_message()]);
        }
        
        // Iniciar polling automático después de restart
        wp_send_json_success([
            'message' => '🔄 Tarea reiniciada',
            'task' => $task,
            'auto_resume' => true, // Frontend debe iniciar polling
        ]);
    }
    
    /**
     * AJAX: Reintentar tarea fallida desde último checkpoint
     */
    public function retry_task() {
        check_ajax_referer('asap_serp_analysis', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $task_id = sanitize_text_field($_POST['task_id'] ?? '');
        
        if (empty($task_id)) {
            wp_send_json_error(['message' => 'Task ID requerido.']);
        }
        
        $task_manager = new ASAP_IA_Background_Task_Manager();
        $task = $task_manager->retry_task($task_id);
        
        if (is_wp_error($task)) {
            wp_send_json_error(['message' => $task->get_error_message()]);
        }
        
        // Iniciar polling automático después de retry
        wp_send_json_success([
            'message' => '🔄 Reintentando tarea',
            'task' => $task,
            'auto_resume' => true, // Frontend debe iniciar polling
        ]);
    }
    
    /**
     * Obtener logs de generación en tiempo real
     * 
     * Devuelve los logs de una sesión específica para mostrar en UI
     */
    public function get_generation_logs() {
        check_ajax_referer('asap_generation_logs', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        if (empty($session_id)) {
            wp_send_json_error(['message' => 'Session ID requerido']);
        }
        
        $logger = new ASAP_IA_Database_Generation_Logger();
        $logs = $logger->get_logs_by_session($session_id);
        $stats = $logger->get_session_stats($session_id);
        
        // Detectar si terminó (último log es success de post_creation)
        $finished = false;
        if (!empty($logs)) {
            $last_log = end($logs);
            $finished = (
                ($last_log['category'] === ASAP_IA_Database_Generation_Logger::CAT_POST && $last_log['type'] === ASAP_IA_Database_Generation_Logger::TYPE_SUCCESS)
                || $last_log['type'] === ASAP_IA_Database_Generation_Logger::TYPE_ERROR
            );
        }
        
        wp_send_json_success([
            'logs' => $logs,
            'stats' => $stats,
            'finished' => $finished,
        ]);
    }
    
    /**
     * Obtener logs recientes (para Tab_Config)
     */
    public function get_recent_logs() {
        check_ajax_referer('asap_recent_logs', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 100;
        $logger = new ASAP_IA_Database_Generation_Logger();
        $logs = $logger->get_recent_logs($limit);
        
        // Estadísticas globales
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_ia_generation_logs';
        $stats_query = $wpdb->prepare(
            "SELECT 
                COUNT(DISTINCT session_id) as total_sessions,
                SUM(cost_usd) as total_cost
            FROM {$table_name}
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            LIMIT %d",
            $limit
        );
        $stats = $wpdb->get_row($stats_query, ARRAY_A);
        
        wp_send_json_success([
            'logs' => $logs,
            'stats' => $stats,
        ]);
    }
    
    /**
     * Limpiar logs antiguos
     */
    public function clean_old_logs() {
        check_ajax_referer('asap_clean_logs', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permitido.']);
        }
        
        $logger = new ASAP_IA_Database_Generation_Logger();
        $deleted = $logger->clean_old_logs(30);
        
        wp_send_json_success([
            'message' => "Se eliminaron {$deleted} logs antiguos.",
            'deleted' => $deleted,
        ]);
    }
    
    /**
     * AJAX: Guardar configuración por defecto de IA
     */
    public function save_ia_defaults() {
        check_ajax_referer('asap_manual_rewrite_action', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No tienes permisos suficientes.']);
        }
        
        // Guardar valores por defecto (TODOS los campos configurables)
        update_option('asap_ia_default_lang', sanitize_text_field($_POST['lang'] ?? 'es'));
        update_option('asap_ia_default_style', sanitize_text_field($_POST['style'] ?? 'informativo'));
        update_option('asap_ia_default_status', sanitize_text_field($_POST['status'] ?? 'draft'));
        update_option('asap_ia_default_post_type', sanitize_text_field($_POST['post_type'] ?? 'post'));
        update_option('asap_ia_default_author', absint($_POST['author'] ?? 0));
        
        // ⭐ NUEVOS CAMPOS: Longitud, instrucciones, extras
        update_option('asap_ia_default_target_len', absint($_POST['target_len'] ?? 3000));
        update_option('asap_ia_default_extra', sanitize_textarea_field($_POST['extra'] ?? ''));
        update_option('asap_ia_default_intro_enable', sanitize_text_field($_POST['intro_enable'] ?? '1'));
        update_option('asap_ia_default_faqs_enable', sanitize_text_field($_POST['faqs_enable'] ?? '0'));
        update_option('asap_ia_default_faqs_count', absint($_POST['faqs_count'] ?? 5));
        update_option('asap_ia_default_conclusion_enable', sanitize_text_field($_POST['conclusion_enable'] ?? '1'));
        
        // ⭐ REFERENCIAS
        update_option('asap_ia_default_include_references', sanitize_text_field($_POST['include_references'] ?? '0'));
        update_option('asap_ia_default_custom_references', sanitize_textarea_field($_POST['custom_references'] ?? ''));
        
        // ⭐ CATEGORÍAS
        $categories = isset($_POST['categories']) && is_array($_POST['categories']) 
            ? array_map('absint', $_POST['categories']) 
            : [];
        update_option('asap_ia_default_categories', $categories);
        
        wp_send_json_success([
            'message' => 'Configuración guardada correctamente.'
        ]);
    }
    
    /**
     * ✅ AJAX: Obtener progreso de generación desde transients
     */
    public function get_generation_progress() {
        check_ajax_referer('asap_manual_rewrite_action', 'nonce');
        
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        if (empty($session_id)) {
            wp_send_json_error(['message' => 'Session ID requerido']);
        }
        
        // Leer progreso desde transient
        $progress_data = get_transient('asap_gen_progress_' . $session_id);
        
        if ($progress_data) {
            wp_send_json_success($progress_data);
        } else {
            wp_send_json_success([
                'progress' => 0,
                'message' => '⏳ Preparando generación...'
            ]);
        }
    }
}


