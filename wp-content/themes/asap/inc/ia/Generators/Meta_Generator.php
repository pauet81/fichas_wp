<?php
/**
 * Generador de Meta Tags (título y descripción)
 * 
 * Genera meta títulos y descripciones optimizados para SEO usando IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Generators
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Generators_Meta_Generator {
    
    /**
     * @var ASAP_IA_Core_OpenAI_Client Cliente de OpenAI
     */
    private $openai_client;
    
    /**
     * @var ASAP_IA_Core_Gemini_Client Cliente de Gemini
     */
    private $gemini_client;
    
    /**
     * @var ASAP_IA_Core_Token_Calculator Calculadora de tokens
     */
    private $token_calculator;
    
    /**
     * Constructor
     * 
     * @param ASAP_IA_Core_OpenAI_Client $openai_client Cliente de OpenAI
     * @param ASAP_IA_Core_Token_Calculator $token_calculator Calculadora de tokens
     */
    public function __construct($openai_client, $token_calculator) {
        $this->openai_client = $openai_client;
        $this->gemini_client = new ASAP_IA_Core_Gemini_Client();
        $this->token_calculator = $token_calculator;
    }
    
    /**
     * Método wrapper que llama a la IA configurada (OpenAI o Gemini)
     * 
     * @param string $system Mensaje de sistema
     * @param string $user_content Contenido del usuario
     * @param int $max_tokens Máximo de tokens
     * @return array|WP_Error Respuesta de la IA
     */
    private function call_ai($system, $user_content, $max_tokens = 200) {
        $provider = get_option('asap_ia_provider', 'openai');
        
        if ($provider === 'gemini') {
            $api_key = $this->gemini_client->get_api_key();
            if (empty($api_key)) {
                return new WP_Error('no_api_key', 'Falta configurar Google Gemini API Key.');
            }
            $model = get_option('asap_ia_gemini_model', 'gemini-2.5-flash-lite');
            $temperature = floatval(get_option('asap_ia_temperature', 0.7));
            
            return $this->gemini_client->chat($api_key, $model, $temperature, $system, $user_content, $max_tokens);
        } else {
            // OpenAI por defecto
            $api_key = $this->openai_client->get_api_key();
            if (empty($api_key)) {
                return new WP_Error('no_api_key', 'Falta configurar OpenAI API Key.');
            }
            $model = get_option('asap_ia_openai_model', 'gpt-4o-mini');
            $temperature = floatval(get_option('asap_ia_temperature', 0.7));
            
            return $this->openai_client->chat($api_key, $model, $temperature, $system, $user_content, $max_tokens);
        }
    }
    
    /**
     * Verifica si debe generar meta tags y los genera si es necesario
     */
    public function maybe_generate_meta_for_post($post_id, $force = false) {
        $target = get_option('asap_meta_target', 'auto');
        
        if ($target === 'none') {
            return null;
        }
        
        // Obtener plugin SEO activo
        $active_plugin = $this->get_active_seo_plugin();
        
        if (!$active_plugin) {
            return null;
        }
        
        // Si el target es específico, verificar que coincida
        if ($target !== 'auto' && $target !== $active_plugin['slug']) {
            return null;
        }

        $only_empty = get_option('asap_meta_only_if_empty', '1') === '1';
        
        // Obtener metaetiquetas existentes usando las keys del plugin activo
        $existing_title = get_post_meta($post_id, $active_plugin['title_key'], true);
        $existing_desc = get_post_meta($post_id, $active_plugin['desc_key'], true);
        
        if ($only_empty && $existing_title && $existing_desc && !$force) {
            return null;
        }

        $opts = [
            'need_title' => $only_empty ? empty($existing_title) : true,
            'need_desc' => $only_empty ? empty($existing_desc) : true,
        ];
        
        $generated = $this->generate_for_post($post_id, $opts);
        
        if (!$generated) {
            return null;
        }
        
        // Guardar en el plugin SEO usando las keys correspondientes
        if ($generated['title']) {
            update_post_meta($post_id, $active_plugin['title_key'], $generated['title']);
        }
        if ($generated['description']) {
            update_post_meta($post_id, $active_plugin['desc_key'], $generated['description']);
        }
        
        return $generated;
    }

    /**
     * Detecta plugins SEO instalados
     * 
     * @return array Array con plugins detectados y sus meta keys
     */
    public function detect_seo_plugins() {
        $plugins = [
            'yoast' => [
                'active' => defined('WPSEO_VERSION') || class_exists('WPSEO_Meta'),
                'name' => 'Yoast SEO',
                'title_key' => '_yoast_wpseo_title',
                'desc_key' => '_yoast_wpseo_metadesc',
                'priority' => 1
            ],
            'rankmath' => [
                'active' => defined('RANK_MATH_VERSION') || function_exists('rank_math'),
                'name' => 'Rank Math',
                'title_key' => 'rank_math_title',
                'desc_key' => 'rank_math_description',
                'priority' => 2
            ],
            'aioseo' => [
                'active' => defined('AIOSEO_VERSION') || class_exists('AIOSEO\Plugin\AIOSEO'),
                'name' => 'All in One SEO',
                'title_key' => '_aioseo_title',
                'desc_key' => '_aioseo_description',
                'priority' => 3
            ],
            'seopress' => [
                'active' => defined('SEOPRESS_VERSION') || function_exists('seopress_init'),
                'name' => 'SEOPress',
                'title_key' => '_seopress_titles_title',
                'desc_key' => '_seopress_titles_desc',
                'priority' => 4
            ],
            'seoframework' => [
                'active' => defined('THE_SEO_FRAMEWORK_VERSION') || function_exists('the_seo_framework'),
                'name' => 'The SEO Framework',
                'title_key' => '_genesis_title',
                'desc_key' => '_genesis_description',
                'priority' => 5
            ],
        ];
        
        return $plugins;
    }
    
    /**
     * Obtiene el plugin SEO activo con mayor prioridad
     * 
     * @return array|null ['slug' => 'yoast', 'name' => 'Yoast SEO', 'title_key' => '...', 'desc_key' => '...'] o null
     */
    private function get_active_seo_plugin() {
        $plugins = $this->detect_seo_plugins();
        $active = null;
        
        foreach ($plugins as $slug => $data) {
            if ($data['active']) {
                if (!$active || $data['priority'] < $active['priority']) {
                    $active = array_merge(['slug' => $slug], $data);
                }
            }
        }
        
        return $active;
    }

    /**
     * Genera meta tags para un post
     * 
     * @param int $post_id ID del post
     * @param array $opts Opciones: 'need_title', 'need_desc'
     * @return array|null ['title' => '...', 'description' => '...', 'cost' => 0.00, 'tokens' => 0] o null si falla
     */
    public function generate_for_post($post_id, array $opts) {
        $post = get_post($post_id);
        if (!$post) return null;

        // Inicializar logger
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $logger->info($session_id, 'meta_generation', "Generando metaetiquetas para post {$post_id}");

        // Recopilar datos del post
        $data = $this->gather_post_data($post_id);
        
        // Configuración
        $max_title = intval(get_option('asap_meta_title_len', 60));
        $max_desc = intval(get_option('asap_meta_desc_len', 155));
        
        // Preparar prompts
        $prompts = $this->prepare_prompts($data, $max_title, $max_desc);
        
        $out_title = '';
        $out_desc = '';
        $total_tokens_input = 0;
        $total_tokens_output = 0;
        $model = '';
        $start_time = microtime(true);

        // Generar título
        if (!empty($opts['need_title'])) {
            $logger->info($session_id, 'meta_generation', "Generando meta título...");
            $sys = "Eres un experto en SEO y copywriting. Genera títulos de metaetiquetas IRRESISTIBLES que inviten al clic. REGLAS CRÍTICAS:\n1. Usa mayúsculas SOLO en 1-2 palabras clave importantes (no más)\n2. Usa lenguaje persuasivo y emocional\n3. Crea urgencia o curiosidad\n4. Devuelve SOLO el título, sin comillas ni explicaciones\n5. Máximo {$max_title} caracteres\n6. NO abuses de las mayúsculas, solo resalta lo más importante";
            $response = $this->call_ai($sys, $prompts['title_prompt'], 64);
            if (!is_wp_error($response)) {
                $out_title = $this->single_line_limit(wp_strip_all_tags($response['content']), $max_title);
                $total_tokens_input += $response['usage']['prompt_tokens'];
                $total_tokens_output += $response['usage']['completion_tokens'];
                $model = $response['model'];
                $logger->success($session_id, 'meta_generation', "Meta título generado: {$out_title}");
            } else {
                $logger->error($session_id, 'meta_generation', "Error generando título: " . $response->get_error_message());
            }
        }
        
        // Generar descripción
        if (!empty($opts['need_desc'])) {
            $logger->info($session_id, 'meta_generation', "Generando meta descripción...");
            $sys = "Eres un experto en SEO y copywriting. Redacta meta descripciones PERSUASIVAS que conviertan. REGLAS CRÍTICAS:\n1. Usa 1-2 emojis relevantes al inicio o final para destacar\n2. Incluye un beneficio claro o promesa de valor\n3. Crea curiosidad o urgencia\n4. Usa lenguaje emocional y directo\n5. Devuelve SOLO la descripción, sin comillas ni explicaciones\n6. Máximo {$max_desc} caracteres";
            $response = $this->call_ai($sys, $prompts['desc_prompt'], 120);
            if (!is_wp_error($response)) {
                $out_desc = $this->single_line_limit(wp_strip_all_tags($response['content']), $max_desc);
                $total_tokens_input += $response['usage']['prompt_tokens'];
                $total_tokens_output += $response['usage']['completion_tokens'];
                if (empty($model)) $model = $response['model'];
                $logger->success($session_id, 'meta_generation', "Meta descripción generada: " . mb_substr($out_desc, 0, 50) . "...");
            } else {
                $logger->error($session_id, 'meta_generation', "Error generando descripción: " . $response->get_error_message());
            }
        }

        $duration = microtime(true) - $start_time;

        if (!$out_title && !$out_desc) {
            $logger->error($session_id, 'meta_generation', "No se generaron metaetiquetas", null, null, null, $duration);
            return null;
        }
        
        $cost = $this->token_calculator->calculate_cost($model, $total_tokens_input, $total_tokens_output);
        $total_tokens = $total_tokens_input + $total_tokens_output;
        
        $logger->success($session_id, 'meta_generation', "Metaetiquetas generadas exitosamente", [
            'title' => $out_title,
            'description' => mb_substr($out_desc, 0, 100),
            'tokens' => $total_tokens,
            'model' => $model
        ], null, null, $duration, $cost);

        return [
            'title' => $out_title,
            'description' => $out_desc,
            'cost' => $cost,
            'tokens' => $total_tokens,
            'tokens_input' => $total_tokens_input,
            'tokens_output' => $total_tokens_output,
            'model' => $model
        ];
    }
    
    /**
     * Estima el costo de generar metas
     * 
     * @param int $post_id ID del post
     * @param bool $need_title Generar título
     * @param bool $need_desc Generar descripción
     * @return array ['cost' => 0.00, 'tokens' => 0]
     */
    public function estimate_cost($post_id, $need_title, $need_desc) {
        $post = get_post($post_id);
        if (!$post) return ['cost' => 0, 'tokens' => 0];
        
        $data = $this->gather_post_data($post_id);
        $max_title = intval(get_option('asap_meta_title_len', 60));
        $max_desc = intval(get_option('asap_meta_desc_len', 155));
        $prompts = $this->prepare_prompts($data, $max_title, $max_desc);
        
        $total_input = 0;
        $total_output = 0;
        
        if ($need_title) {
            $total_input += $this->token_calculator->estimate_tokens($prompts['title_prompt']);
            $total_output += 64; // max_tokens para título
        }
        
        if ($need_desc) {
            $total_input += $this->token_calculator->estimate_tokens($prompts['desc_prompt']);
            $total_output += 120; // max_tokens para descripción
        }
        
        return [
            'cost' => $this->token_calculator->calculate_cost('gpt-4o-mini', $total_input, $total_output),
            'tokens' => $total_input + $total_output
        ];
    }
    
    /**
     * Recopila datos del post
     */
    private function gather_post_data($post_id) {
        $post = get_post($post_id);
        $site_name = get_bloginfo('name');
        $title = get_the_title($post_id);
        $excerpt = trim(get_the_excerpt($post_id));
        $content = trim(wp_strip_all_tags($post->post_content));
        if (mb_strlen($content) > 5000) $content = mb_substr($content, 0, 5000).'…';
        
        $cats = $tags = '';
        $tax_cats = get_the_terms($post_id, 'category');
        if (!is_wp_error($tax_cats) && !empty($tax_cats)) $cats = implode(', ', wp_list_pluck($tax_cats, 'name'));
        $tax_tags = get_the_terms($post_id, 'post_tag');
        if (!is_wp_error($tax_tags) && !empty($tax_tags)) $tags = implode(', ', wp_list_pluck($tax_tags, 'name'));
        
        $lang = get_option('asap_meta_lang','inherit');
        if ($lang === 'inherit') $lang = get_option('asap_ia_default_lang','es');
        
        return compact('site_name', 'title', 'excerpt', 'content', 'cats', 'tags', 'lang');
    }
    
    /**
     * Prepara los prompts
     */
    private function prepare_prompts($data, $max_title, $max_desc) {
        $prompt_title = trim(get_option('asap_meta_title_prompt',''));
        $prompt_desc = trim(get_option('asap_meta_desc_prompt',''));
        
        if ($prompt_title === '') {
            $prompt_title = "Crea un meta título IRRESISTIBLE en {lang} que invite al clic. Pon las palabras MÁS IMPORTANTES en MAYÚSCULAS (2-4 palabras clave). Usa lenguaje persuasivo y emocional. Máx {max_title} caracteres. Devuelve SOLO el título sin comillas.";
        }
        if ($prompt_desc === '') {
            $prompt_desc = "Crea una meta descripción PERSUASIVA en {lang} que convierta. Usa 1-2 emojis relevantes. Incluye un beneficio claro y crea curiosidad. Máx {max_description} caracteres. Devuelve SOLO la descripción sin comillas.";
        }
        
        $repl = [
            '{title}' => $data['title'],
            '{excerpt}' => $data['excerpt'],
            '{content}' => $data['content'],
            '{site_name}' => $data['site_name'],
            '{categories}' => $data['cats'],
            '{tags}' => $data['tags'],
            '{lang}' => $data['lang'],
            '{max_title}' => $max_title,
            '{max_description}' => $max_desc,
        ];
        
        $p_title = strtr($prompt_title, $repl);
        $p_desc = strtr($prompt_desc, $repl);
        
        $context = "Contexto:\nTÍTULO: {$data['title']}\nCATEGORÍAS: {$data['cats']}\nETIQUETAS: {$data['tags']}\nEXTRACTO: {$data['excerpt']}\n";
        
        return [
            'title_prompt' => $p_title . "\n\n" . $context,
            'desc_prompt' => $p_desc . "\n\n" . $context
        ];
    }
    
    /**
     * Limita texto a una línea y longitud máxima
     * Preserva emojis y mayúsculas
     */
    private function single_line_limit($text, $max_chars) {
        // Normalizar espacios en blanco pero preservar emojis
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        // Truncar si excede el límite
        if (mb_strlen($text) > $max_chars) {
            $text = mb_substr($text, 0, $max_chars);
            // Eliminar palabra incompleta al final
            $text = preg_replace('/\s+\S*$/u', '', $text);
            $text .= '…';
        }
        
        // Eliminar comillas al inicio/final pero NO emojis
        $text = preg_replace('/^["""\'\s]+|["""\'\s]+$/u', '', $text);
        
        return $text;
    }
}




