<?php
/**
 * Settings Manager
 * 
 * Centraliza el guardado de todas las opciones del sistema.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Settings
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Settings_Manager {
    
    /**
     * @var object Instancia principal
     */
    private $instance;
    
    /**
     * Constructor
     * 
     * @param object $instance Instancia de ASAP_Manual_IA_Rewriter
     */
    public function __construct($instance) {
        $this->instance = $instance;
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
     * Guarda opciones por defecto de generación
     */
    public function save_defaults() {
        update_option('asap_ia_default_status', sanitize_text_field($_POST['asap_ia_default_status'] ?? 'draft'));
        update_option('asap_ia_default_post_type', sanitize_text_field($_POST['asap_ia_default_post_type'] ?? 'post'));
        update_option('asap_ia_default_author', absint($_POST['asap_ia_default_author'] ?? 0));
        update_option('asap_ia_default_lang', sanitize_text_field($_POST['asap_ia_default_lang'] ?? 'es'));
        update_option('asap_ia_default_style', sanitize_text_field($_POST['asap_ia_default_style'] ?? 'informativo'));
        update_option('asap_ia_default_length', max(500, min(8000, intval($_POST['asap_ia_default_length'] ?? 3000))));
    }
    
    /**
     * Guarda API keys
     */
    public function save_config() {
        // Proveedor de IA
        if (isset($_POST['asap_ia_provider'])) {
            $provider = sanitize_text_field($_POST['asap_ia_provider']);
            if (in_array($provider, ['openai', 'gemini'], true)) {
                update_option('asap_ia_provider', $provider);
            }
        }
        
        // OpenAI API Key
        if (isset($_POST['asap_ia_openai_api_key'])) {
            $val = trim(wp_unslash($_POST['asap_ia_openai_api_key']));
            if ($val === '') {
                update_option('asap_ia_openai_api_key', '');
            } elseif ($val !== '********') {
                update_option('asap_ia_openai_api_key', sanitize_text_field($val));
            }
        }
        
        // OpenAI Model
        if (isset($_POST['asap_ia_openai_model'])) {
            $valid_models = ['gpt-4o-mini', 'gpt-4o', 'gpt-4-turbo', 'gpt-4.1-mini', 'gpt-4.1-nano', 'o1-mini', 'o1'];
            $model = sanitize_text_field($_POST['asap_ia_openai_model']);
            if (in_array($model, $valid_models, true)) {
                update_option('asap_ia_openai_model', $model);
            }
        }
        
        // Google Gemini API Key
        if (isset($_POST['asap_ia_gemini_api_key'])) {
            $val = trim(wp_unslash($_POST['asap_ia_gemini_api_key']));
            if ($val === '') {
                update_option('asap_ia_gemini_api_key', '');
            } elseif ($val !== '********') {
                update_option('asap_ia_gemini_api_key', sanitize_text_field($val));
            }
        }
        
        // Gemini Model
        if (isset($_POST['asap_ia_gemini_model'])) {
            $valid_models = ['gemini-2.5-flash', 'gemini-2.5-flash-lite', 'gemini-2.5-pro', 'gemini-2.0-flash-exp'];
            $model = sanitize_text_field($_POST['asap_ia_gemini_model']);
            if (in_array($model, $valid_models, true)) {
                update_option('asap_ia_gemini_model', $model);
            }
        }
        
        // ValueSERP API Key
        if (isset($_POST['asap_ia_valueserp_api_key'])) {
            $val = trim(wp_unslash($_POST['asap_ia_valueserp_api_key']));
            if ($val === '') {
                update_option('asap_ia_valueserp_api_key', '');
            } elseif ($val !== '********') {
                update_option('asap_ia_valueserp_api_key', sanitize_text_field($val));
            }
        }
        
        // Replicate API Token
        if (isset($_POST['asap_ia_replicate_api_token'])) {
            $val = trim(wp_unslash($_POST['asap_ia_replicate_api_token']));
            if ($val === '') {
                update_option('asap_ia_replicate_api_token', '');
            } elseif ($val !== '********') {
                update_option('asap_ia_replicate_api_token', sanitize_text_field($val));
            }
        }
    }
    
    /**
     * Guarda configuración de metaetiquetas
     */
    public function save_meta_settings() {
        update_option('asap_meta_enable', isset($_POST['asap_meta_enable']) ? '1' : '0');
        
        $target = sanitize_text_field($_POST['asap_meta_target'] ?? 'auto');
        if (!in_array($target, ['auto','yoast','rankmath','none'], true)) {
            $target = 'auto';
        }
        update_option('asap_meta_target', $target);
        
        $when = sanitize_text_field($_POST['asap_meta_when'] ?? 'publish');
        if (!in_array($when, ['publish','save'], true)) {
            $when = 'publish';
        }
        update_option('asap_meta_when', $when);
        
        update_option('asap_meta_only_if_empty', isset($_POST['asap_meta_only_if_empty']) ? '1' : '0');
        
        $lang = sanitize_text_field($_POST['asap_meta_lang'] ?? 'inherit');
        if (!in_array($lang, ['inherit','es','en','pt'], true)) {
            $lang = 'inherit';
        }
        update_option('asap_meta_lang', $lang);
        
        update_option('asap_meta_title_len', max(20, min(80, intval($_POST['asap_meta_title_len'] ?? 60))));
        update_option('asap_meta_desc_len', max(60, min(200, intval($_POST['asap_meta_desc_len'] ?? 155))));
        update_option('asap_meta_title_prompt', wp_unslash($_POST['asap_meta_title_prompt'] ?? ''));
        update_option('asap_meta_desc_prompt', wp_unslash($_POST['asap_meta_desc_prompt'] ?? ''));
    }
    
    /**
     * Guarda configuración de imágenes
     */
    public function save_images_settings() {
        $rep_models = $this->call_instance_method('get_replicate_model_catalog');
        $S = $this->call_instance_method('get_image_settings');
        
        $S['enable'] = isset($_POST['asap_img_enable']) ? '1' : '0';
        
        $post_types = isset($_POST['asap_img_post_types']) && is_array($_POST['asap_img_post_types']) 
                      ? array_map('sanitize_text_field', $_POST['asap_img_post_types']) 
                      : ['post'];
        
        // Validar post types públicos
        $public_pts = array_keys(get_post_types(['public'=>true], 'names') ?: []);
        $S['post_types'] = array_values(array_intersect($post_types, $public_pts));
        if (empty($S['post_types'])) {
            $S['post_types'] = ['post'];
        }
        
        $S['only_if_empty'] = isset($_POST['asap_img_only_if_empty']) ? '1' : '0';
        
        $lang_value = $_POST['asap_img_lang'] ?? 'inherit';
        $S['lang'] = in_array($lang_value, ['inherit','es','en','pt'], true) ? $lang_value : 'inherit';
        
        // Cuándo generar imágenes
        $when_value = $_POST['asap_img_when'] ?? 'publish';
        update_option('asap_img_when', in_array($when_value, ['publish','save','publish_update'], true) ? $when_value : 'publish');
        
        $provider_value = $_POST['asap_img_provider'] ?? 'openai';
        $S['provider'] = in_array($provider_value, ['openai','gemini','replicate'], true) ? $provider_value : 'openai';
        
        $fallback_value = $_POST['asap_img_fallback'] ?? 'none';
        $S['fallback'] = in_array($fallback_value, ['none','openai','gemini','replicate'], true) ? $fallback_value : 'none';
        
        // OpenAI (solo dall-e-3)
        $openai_size_value = $_POST['asap_img_openai_size'] ?? '1024x1024';
        $S['openai_size'] = in_array($openai_size_value, ['1024x1024','1024x1792','1792x1024'], true) ? $openai_size_value : '1024x1024';
        
        $openai_quality_value = $_POST['asap_img_openai_quality'] ?? 'standard';
        $S['openai_quality'] = in_array($openai_quality_value, ['standard','hd'], true) ? $openai_quality_value : 'standard';
        
        $S['openai_n'] = max(1, min(4, intval($_POST['asap_img_openai_n'] ?? 1)));
        
        // Google Gemini (Nano Banana)
        $valid_gemini_ratios = ['1:1','2:3','3:2','3:4','4:3','4:5','5:4','9:16','16:9','21:9'];
        $gemini_aspect_value = $_POST['asap_img_gemini_aspect_ratio'] ?? '1:1';
        $S['gemini_aspect_ratio'] = in_array($gemini_aspect_value, $valid_gemini_ratios, true) ? $gemini_aspect_value : '1:1';
        
        // Replicate
        $replicate_model_value = $_POST['asap_img_replicate_model'] ?? 'flux-schnell';
        $S['replicate_model'] = array_key_exists($replicate_model_value, $rep_models) ? $replicate_model_value : 'flux-schnell';
        
        $replicate_aspect_value = $_POST['asap_img_replicate_aspect'] ?? '1:1';
        $S['replicate_aspect'] = in_array($replicate_aspect_value, ['1:1','16:9','9:16','4:3','3:4','21:9'], true) ? $replicate_aspect_value : '1:1';
        
        $S['replicate_n'] = max(1, min(4, intval($_POST['asap_img_replicate_n'] ?? 1)));
        $S['negative_prompt'] = sanitize_text_field($_POST['asap_img_negative_prompt'] ?? '');
        
        // Prompt/ALT
        $S['prompt_template'] = wp_unslash($_POST['asap_img_prompt_template'] ?? '');
        if ($S['prompt_template'] === '') {
            $S['prompt_template'] = 'Imagen destacada para el artículo titulado "{title}". Estilo fotográfico realista, composición limpia, alta calidad. IMPORTANTE: NO incluir texto, letras, palabras ni tipografía en la imagen. Solo elementos visuales. Idioma visual: {lang}.';
        }
        
        $alt_mode_value = $_POST['asap_img_alt_mode'] ?? 'template';
        $S['alt_mode'] = in_array($alt_mode_value, ['template', 'ai'], true) ? $alt_mode_value : 'template';
        
        $S['alt_template'] = sanitize_text_field($_POST['asap_img_alt_template'] ?? '{title}');
        
        $S['content_enable'] = isset($_POST['asap_img_content_enable']) ? '1' : '0';
        $S['content_quantity'] = max(1, min(6, intval($_POST['asap_img_content_quantity'] ?? 3)));
        
        $content_strategy_value = $_POST['asap_img_content_strategy'] ?? 'auto';
        $S['content_strategy'] = in_array($content_strategy_value, ['auto', 'each_h2', 'every_n'], true) ? $content_strategy_value : 'auto';
        
        $S['content_every_n'] = max(1, min(10, intval($_POST['asap_img_content_every_n'] ?? 2)));
        
        $content_position_value = $_POST['asap_img_content_position'] ?? 'after_h2';
        $S['content_position'] = in_array($content_position_value, ['after_h2', 'before_content', 'after_first_p'], true) ? $content_position_value : 'after_h2';
        
        $content_img_style_value = $_POST['asap_img_content_img_style'] ?? 'centered';
        $S['content_img_style'] = in_array($content_img_style_value, ['centered', 'full_width', 'float_left', 'float_right'], true) ? $content_img_style_value : 'centered';
        
        $S['content_prompt_template'] = wp_unslash($_POST['asap_img_content_prompt_template'] ?? '');
        
        // Tarifas
        $S['openai_price_per_mpx'] = 0.04; // Fixed para dall-e-3
        $S['openai_hd_mult'] = 2.0;
        
        $rates = isset($_POST['asap_img_replicate_rates']) && is_array($_POST['asap_img_replicate_rates']) 
                 ? $_POST['asap_img_replicate_rates'] : [];
        foreach ($rep_models as $slug => $meta) {
            $S['replicate_rates'][$slug] = isset($rates[$slug]) 
                                            ? max(0, floatval($rates[$slug])) 
                                            : (isset($S['replicate_rates'][$slug]) ? $S['replicate_rates'][$slug] : 0.006);
        }
        
        update_option('asap_img_settings', $S);
    }
}


