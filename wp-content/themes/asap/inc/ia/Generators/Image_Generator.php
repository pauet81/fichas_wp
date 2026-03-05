<?php
/**
 * Generador de Imágenes con IA
 * 
 * Genera imágenes destacadas usando DALL-E 3 (OpenAI) o Replicate.
 * Incluye generación de ALT text automático con IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Generators
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Generators_Image_Generator {
    
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
     * @var callable Función de logging
     */
    private $logger;
    
    /**
     * Constructor
     */
    public function __construct($openai_client, $token_calculator, $logger = null) {
        $this->openai_client = $openai_client;
        $this->gemini_client = new ASAP_IA_Core_Gemini_Client();
        $this->token_calculator = $token_calculator;
        $this->logger = $logger;
    }
    
    /**
     * Verifica si debe generar imagen destacada y la genera si es necesario
     */
    public function maybe_generate_featured_image_for_post($post_id, $context = 'publish') {
        $settings = get_option('asap_img_settings', []);
        $post = get_post($post_id);
        
        if (!$post) return;

        // Verificar post types
        $post_types = $settings['post_types'] ?? ['post'];
        if (!in_array($post->post_type, $post_types, true)) return;
        
        // Verificar si solo cuando está vacío
        if (($settings['only_if_empty'] ?? '1') === '1' && has_post_thumbnail($post_id)) return;

        // Evitar bucles: si ya generamos recientemente
        if (get_post_meta($post_id, '_asap_img_generated', true) === '1') return;

        // ⭐ AGREGAR API KEY DE REPLICATE AL ARRAY DE SETTINGS
        $settings['replicate_api_key'] = get_option('asap_ia_replicate_api_token', '');

        // Generar con proveedor seleccionado (con fallback)
        $provider = $settings['provider'] ?? 'openai';
        $ok = $this->generate_and_assign_featured($post_id, $provider, $settings);
        
        if (!$ok && isset($settings['fallback']) && $settings['fallback'] !== 'none') {
            $this->generate_and_assign_featured($post_id, $settings['fallback'], $settings);
        }
    }

    /**
     * Genera y asigna imagen destacada a un post
     * 
     * @param int $post_id ID del post
     * @param string $provider 'openai' o 'replicate'
     * @param array $settings Configuración de generación
     * @return bool true si tuvo éxito
     */
    public function generate_and_assign_featured($post_id, $provider, array $settings) {
        $prompt = $this->build_image_prompt($post_id, $settings);
        $alt = $this->build_alt_text($post_id, $settings);

        $attachments = [];
        $cost = 0;
        $metadata = ['prompt' => mb_substr($prompt, 0, 200), 'provider' => $provider];
        
        if ($provider === 'openai') {
            $attachments = $this->generate_images_openai($prompt, $settings);
            // Calcular costo para OpenAI
            if (!empty($attachments)) {
                $size = $settings['openai_size'];
                $quality = $settings['openai_quality'];
                $n = count($attachments);
                
                if ($size === '1024x1024') {
                    $cost_per_image = $quality === 'hd' ? 0.080 : 0.040;
                } elseif ($size === '1024x1792' || $size === '1792x1024') {
                    $cost_per_image = $quality === 'hd' ? 0.120 : 0.080;
                } else {
                    $cost_per_image = 0.040;
                }
                $cost = $cost_per_image * $n;
                $metadata['model'] = $settings['openai_model'] ?? 'dall-e-3';
                $metadata['size'] = $size;
                $metadata['quality'] = $quality;
                $metadata['quantity'] = $n;
            }
        } elseif ($provider === 'gemini') {
            $attachments = $this->generate_images_gemini($prompt, $settings);
            // Costo de Gemini 2.5 Flash Image (Nano Banana): 1290 tokens por imagen
            if (!empty($attachments)) {
                $n = count($attachments);
                // Precio aproximado: $0.04 por imagen (basado en 1290 tokens)
                $cost = 0.04 * $n;
                $metadata['model'] = 'gemini-2.5-flash-image';
                $metadata['aspect_ratio'] = $settings['gemini_aspect_ratio'] ?? '1:1';
                $metadata['quantity'] = $n;
            }
        } elseif ($provider === 'replicate') {
            $attachments = $this->generate_images_replicate($prompt, $settings);
            // Calcular costo estimado para Replicate
            if (!empty($attachments)) {
                // ⚠️ FORZAR flux-schnell (ignorar configuración guardada)
                $model = 'flux-schnell';
                $rate = isset($settings['replicate_rates'][$model]) ? floatval($settings['replicate_rates'][$model]) : 0.006;
                $width = intval($settings['replicate_width'] ?? 1024);
                $height = intval($settings['replicate_height'] ?? 1024);
                $mpx = ($width * $height) / 1000000;
                $n = count($attachments);
                $cost = $rate * $mpx * $n;
                $metadata['model'] = $model;
                $metadata['dimensions'] = "{$width}x{$height}";
                $metadata['quantity'] = $n;
            }
        }
        
        if (empty($attachments)) {
            // Log de error
            if ($this->logger) {
                call_user_func($this->logger, [
                    'type' => 'image',
                    'action' => 'generate_featured_image',
                    'post_id' => $post_id,
                    'model' => $provider,
                    'status' => 'error',
                    'error_message' => 'No se generaron imágenes',
                    'metadata' => $metadata,
                ]);
            }
            return false;
        }

        // Guardar y asignar
        $first_id = 0;
        foreach ($attachments as $i => $item) {
            if (isset($item['b64'])) {
                $filename = $this->slugify_filename(mb_substr($prompt, 0, 60)) . '-' . date('Ymd-His') . '-' . ($i + 1) . '.png';
                $aid = $this->save_base64_image_to_media($item['b64'], $filename, $post_id, $settings['format'] ?? 'png', $settings['watermark'] ?? '', $alt);
                if ($aid) {
                    if (!$first_id) $first_id = $aid;
                }
            } elseif (isset($item['url'])) {
                $aid = $this->sideload_image_from_url($item['url'], $post_id, $settings['format'] ?? 'png', $settings['watermark'] ?? '', $alt);
                if ($aid) {
                    if (!$first_id) $first_id = $aid;
                }
            }
        }
        
        if ($first_id) {
            set_post_thumbnail($post_id, $first_id);
            update_post_meta($post_id, '_asap_img_generated', '1');
            
            // Log exitoso
            if ($this->logger) {
                call_user_func($this->logger, [
                    'type' => 'image',
                    'action' => 'generate_featured_image',
                    'post_id' => $post_id,
                    'model' => $provider,
                    'cost_usd' => $cost,
                    'status' => 'success',
                    'metadata' => $metadata,
                ]);
            }
            
            return true;
        }
        return false;
    }
    
    /**
     * Construye el prompt para generar imagen
     */
    private function build_image_prompt($post_id, array $settings) {
        $post = get_post($post_id);
        $site = get_bloginfo('name');
        $title = get_the_title($post_id);
        $excerpt = trim(get_the_excerpt($post_id));
        $content = trim(wp_strip_all_tags($post->post_content));
        if (mb_strlen($content) > 1200) $content = mb_substr($content, 0, 1200) . '…';
        $cats = $tags = '';
        $tax_cats = get_the_terms($post_id, 'category');
        if (!is_wp_error($tax_cats) && !empty($tax_cats)) $cats = implode(', ', wp_list_pluck($tax_cats, 'name'));
        $tax_tags = get_the_terms($post_id, 'post_tag');
        if (!is_wp_error($tax_tags) && !empty($tax_tags)) $tags = implode(', ', wp_list_pluck($tax_tags, 'name'));

        $lang = $settings['lang'] === 'inherit' ? get_option('asap_ia_default_lang', 'es') : $settings['lang'];
        $rep = [
            '{title}' => $title,
            '{excerpt}' => $excerpt,
            '{content}' => $content,
            '{site_name}' => $site,
            '{categories}' => $cats,
            '{tags}' => $tags,
            '{lang}' => $lang,
        ];
        return strtr($settings['prompt_template'], $rep);
    }
    
    /**
     * Construye el ALT text para la imagen
     */
    private function build_alt_text($post_id, array $settings) {
        $site = get_bloginfo('name');
        $title = get_the_title($post_id);
        $lang = $settings['lang'] === 'inherit' ? get_option('asap_ia_default_lang', 'es') : $settings['lang'];
        
        // Si está en modo IA, generar con OpenAI
        if (isset($settings['alt_mode']) && $settings['alt_mode'] === 'ai') {
            $api_key = $this->openai_client->get_api_key();
            if ($api_key) {
                $post = get_post($post_id);
                $excerpt = trim(get_the_excerpt($post_id));
                $cats = '';
                $tax_cats = get_the_terms($post_id, 'category');
                if (!is_wp_error($tax_cats) && !empty($tax_cats)) {
                    $cats = implode(', ', wp_list_pluck($tax_cats, 'name'));
                }
                
                $context = "Título: {$title}\nCategorías: {$cats}\nResumen: {$excerpt}";
                $system = "Eres experto en SEO y accesibilidad web. Genera textos ALT descriptivos para imágenes destacadas.";
                $user = "Genera un texto ALT descriptivo (máximo 125 caracteres) para la imagen destacada de este artículo en {$lang}. Debe ser conciso, descriptivo y optimizado para SEO.\n\n{$context}\n\nDevuelve SOLO el texto ALT, sin comillas ni explicaciones.";
                
                $response = $this->openai_client->chat($api_key, 'gpt-4.1-mini', 0.7, $system, $user, 50);
                
                if (!is_wp_error($response) && isset($response['content'])) {
                    $alt = trim(wp_strip_all_tags($response['content']));
                    $alt = trim($alt, '"\''); // Quitar comillas si las agregó
                    
                    // Log del costo
                    if (isset($response['usage']) && $this->logger) {
                        $cost = $this->token_calculator->calculate_cost('gpt-4.1-mini', $response['usage']['prompt_tokens'], $response['usage']['completion_tokens']);
                        call_user_func($this->logger, [
                            'type' => 'alt_text',
                            'action' => 'generate_alt_text',
                            'post_id' => $post_id,
                            'model' => 'gpt-4.1-mini',
                            'tokens_input' => $response['usage']['prompt_tokens'],
                            'tokens_output' => $response['usage']['completion_tokens'],
                            'tokens_total' => $response['usage']['total_tokens'],
                            'cost_usd' => $cost,
                            'status' => 'success',
                            'metadata' => ['alt_text' => $alt],
                        ]);
                    }
                    
                    return $alt ?: $title;
                }
            }
        }
        
        // Modo template (default)
        $rep = ['{title}' => $title, '{site_name}' => $site, '{lang}' => $lang];
        $alt = strtr($settings['alt_template'], $rep);
        $alt = trim(preg_replace('/\s+/', ' ', $alt));
        return $alt ?: $title;
    }
    
    /**
     * Genera imágenes con OpenAI (DALL-E)
     */
    private function generate_images_openai($prompt, array $settings) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $api_key = $this->openai_client->get_api_key();
        if (!$api_key) {
            $logger->error($session_id, 'image_generation', 'OpenAI API Key no configurada');
            return [];
        }
        
        $model = $settings['openai_model'] ?? 'dall-e-3';
        $size = $settings['openai_size'] ?? '1024x1024';
        $quality = $settings['openai_quality'] ?? 'standard';
        $n = intval($settings['openai_n'] ?? 1);
        
        $logger->info($session_id, 'image_generation', "Generando imagen con OpenAI: {$model} ({$size}, {$quality})");

        $endpoint = 'https://api.openai.com/v1/images/generations';
        $body = [
            'model' => $model,
            'prompt' => $prompt,
            'n' => $n,
            'size' => $size,
            'quality' => $quality,
            'response_format' => 'b64_json',
        ];
        
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 60,
            'body' => wp_json_encode($body),
        ];
        
        $start_time = microtime(true);
        $res = wp_remote_post($endpoint, $args);
        $duration = microtime(true) - $start_time;
        
        if (is_wp_error($res)) {
            $logger->error($session_id, 'image_generation', "Error OpenAI: " . $res->get_error_message(), null, null, null, $duration);
            return [];
        }
        
        $code = wp_remote_retrieve_response_code($res);
        if (200 !== (int)$code) {
            $body_text = wp_remote_retrieve_body($res);
            $error_data = json_decode($body_text, true);
            $error_msg = isset($error_data['error']['message']) ? $error_data['error']['message'] : "HTTP {$code}";
            $logger->error($session_id, 'image_generation', "Error OpenAI: {$error_msg}", null, null, null, $duration);
            return [];
        }
        
        $json = json_decode(wp_remote_retrieve_body($res), true);
        if (empty($json['data'])) {
            $logger->error($session_id, 'image_generation', "OpenAI: Sin datos en respuesta", null, null, null, $duration);
            return [];
        }
        
        $attachments = [];
        foreach ($json['data'] as $item) {
            if (!empty($item['b64_json'])) {
                $attachments[] = ['b64' => $item['b64_json']];
            }
        }
        
        $count = count($attachments);
        $logger->success($session_id, 'image_generation', "OpenAI: {$count} imagen(es) generada(s) con {$model}", [
            'model' => $model,
            'count' => $count,
            'size' => $size,
            'quality' => $quality
        ], null, null, $duration);
        
        return $attachments;
    }
    
    /**
     * Genera imágenes con Google Gemini 2.5 Flash Image (Nano Banana)
     */
    private function generate_images_gemini($prompt, array $settings) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $api_key = $this->gemini_client->get_api_key();
        if (!$api_key) {
            $logger->error($session_id, 'image_generation', 'Gemini API Key no configurada');
            return [];
        }
        
        $aspect_ratio = $settings['gemini_aspect_ratio'] ?? '1:1';
        
        $logger->info($session_id, 'image_generation', "Generando imagen con Gemini Nano Banana ({$aspect_ratio})");
        
        // Endpoint de Gemini para generación de imágenes
        $model = 'gemini-2.5-flash-image';
        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $api_key
        );
        
        // Construir body con configuración de imagen
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseModalities' => ['IMAGE'],
                'imageConfig' => [
                    'aspectRatio' => $aspect_ratio
                ]
            ]
        ];
        
        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 90, // Mayor timeout para generación de imágenes
            'body' => wp_json_encode($body),
        ];
        
        $start_time = microtime(true);
        $res = wp_remote_post($endpoint, $args);
        $duration = microtime(true) - $start_time;
        
        if (is_wp_error($res)) {
            $logger->error($session_id, 'image_generation', "Error Gemini: " . $res->get_error_message(), null, null, null, $duration);
            return [];
        }
        
        $code = wp_remote_retrieve_response_code($res);
        if (200 !== (int)$code) {
            $body_text = wp_remote_retrieve_body($res);
            $error_data = json_decode($body_text, true);
            $error_msg = isset($error_data['error']['message']) ? $error_data['error']['message'] : "HTTP {$code}";
            $logger->error($session_id, 'image_generation', "Error Gemini: {$error_msg}", null, null, null, $duration);
            return [];
        }
        
        $json = json_decode(wp_remote_retrieve_body($res), true);
        
        // Extraer imágenes del response
        $attachments = [];
        if (isset($json['candidates'][0]['content']['parts'])) {
            foreach ($json['candidates'][0]['content']['parts'] as $part) {
                if (isset($part['inlineData']['data'])) {
                    // La imagen viene en base64 en inlineData
                    $attachments[] = ['b64' => $part['inlineData']['data']];
                }
            }
        }
        
        if (empty($attachments)) {
            $logger->error($session_id, 'image_generation', "Gemini: Sin imágenes en respuesta", null, null, null, $duration);
            return [];
        }
        
        $count = count($attachments);
        $logger->success($session_id, 'image_generation', "Gemini Nano Banana: {$count} imagen(es) generada(s) ({$aspect_ratio})", [
            'model' => $model,
            'count' => $count,
            'aspect_ratio' => $aspect_ratio
        ], null, null, $duration);
        
        return $attachments;
    }
    
    /**
     * Genera imágenes con Replicate (forma simple: solo modelo + API key)
     */
    private function generate_images_replicate($prompt, array $settings) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $api_key = $settings['replicate_api_key'] ?? '';
        if (!$api_key) {
            $logger->error($session_id, 'image_generation', 'Replicate API Key no configurada');
            return [];
        }
        
        // ⚠️ FORZAR flux-schnell (ignorar configuración guardada por si tienen playground-v2 u otro modelo viejo)
        $model_slug = 'flux-schnell';
        $aspect_ratio = $settings['replicate_aspect'] ?? '16:9';
        $n = intval($settings['replicate_n'] ?? 1);
        
        // Obtener model_id del catálogo
        $catalog = $this->get_replicate_model_catalog();
        if (!isset($catalog[$model_slug])) {
            $model_slug = 'flux-schnell'; // Fallback
        }
        $model_id = $catalog[$model_slug]['model_id'];
        
        $logger->info($session_id, 'image_generation', "Generando imagen con Replicate: {$model_slug} ({$aspect_ratio})");
        
        // Endpoint directo del modelo (forma simple)
        $endpoint = "https://api.replicate.com/v1/models/{$model_id}/predictions";
        
        // Configurar input según el modelo
        $input = $this->build_replicate_input($model_slug, $prompt, $aspect_ratio, $n);
        
        $body = ['input' => $input];
        
        $args = [
            'headers' => [
                'Authorization' => 'Token ' . $api_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'wait', // ⭐ Respuesta sincrónica (sin polling)
            ],
            'timeout' => 60,
            'body' => wp_json_encode($body),
        ];
        
        $start_time = microtime(true);
        $res = wp_remote_post($endpoint, $args);
        $duration = microtime(true) - $start_time;
        
        if (is_wp_error($res)) {
            $logger->error($session_id, 'image_generation', "Error Replicate: " . $res->get_error_message(), null, null, null, $duration);
            return [];
        }
        
        $json = json_decode(wp_remote_retrieve_body($res), true);
        
        // Con "Prefer: wait", la respuesta ya contiene el output
        if (empty($json['output'])) {
            $error_msg = isset($json['error']) ? $json['error'] : 'Sin output en respuesta';
            
            // Log detallado para debugging
            $debug_info = [
                'model' => $model_slug,
                'status' => $json['status'] ?? 'unknown',
                'error' => $json['error'] ?? null,
                'logs' => $json['logs'] ?? null,
                'response_keys' => array_keys($json)
            ];
            
            $logger->error($session_id, 'image_generation', "Error Replicate ({$model_slug}): {$error_msg}", $debug_info, null, null, $duration);
            
            return [];
        }
        
        $attachments = [];
        $output = is_array($json['output']) ? $json['output'] : [$json['output']];
        
        foreach ($output as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $attachments[] = ['url' => $url];
            }
        }
        
        $count = count($attachments);
        $logger->success($session_id, 'image_generation', "Replicate: {$count} imagen(es) generada(s) con {$model_slug}", [
            'model' => $model_slug,
            'count' => $count,
            'aspect_ratio' => $aspect_ratio
        ], null, null, $duration);
        
        return $attachments;
    }
    
    /**
     * Calcula aspect ratio a partir de width/height
     */
    private function calculate_aspect_ratio($width, $height) {
        $ratio = $width / $height;
        
        // Mapear a aspect ratios comunes
        if (abs($ratio - 1) < 0.1) return '1:1';
        if (abs($ratio - 16/9) < 0.1) return '16:9';
        if (abs($ratio - 9/16) < 0.1) return '9:16';
        if (abs($ratio - 4/3) < 0.1) return '4:3';
        if (abs($ratio - 3/4) < 0.1) return '3:4';
        if (abs($ratio - 21/9) < 0.1) return '21:9';
        
        return '1:1'; // Default
    }
    
    /**
     * Construye input según el modelo de Replicate
     * Parámetros verificados según documentación oficial Nov 2024
     */
    private function build_replicate_input($model_slug, $prompt, $aspect_ratio, $n) {
        switch ($model_slug) {
            // FLUX Schnell - Muy rápido
            case 'flux-schnell':
                return [
                    'prompt'         => $prompt,
                    'go_fast'        => true,
                    'num_outputs'    => $n,
                    'aspect_ratio'   => $aspect_ratio,
                    'output_format'  => 'webp',
                    'output_quality' => 80
                ];
            
            // FLUX Dev - Más calidad
            case 'flux-dev':
                return [
                    'prompt'         => $prompt,
                    'num_outputs'    => $n,
                    'aspect_ratio'   => $aspect_ratio,
                    'output_format'  => 'webp',
                    'output_quality' => 90,
                    'num_inference_steps' => 28
                ];
            
            // FLUX Pro - Máxima calidad
            case 'flux-pro':
                return [
                    'prompt'         => $prompt,
                    'aspect_ratio'   => $aspect_ratio,
                    'output_format'  => 'webp',
                    'output_quality' => 95,
                    'safety_tolerance' => 2
                ];
                
            // Stable Diffusion 3 Medium
            case 'sd3-medium':
                return [
                    'cfg'              => 3.5,
                    'steps'            => 28,
                    'prompt'           => $prompt,
                    'aspect_ratio'     => $aspect_ratio,
                    'output_format'    => 'webp',
                    'output_quality'   => 90,
                    'negative_prompt'  => '',
                    'prompt_strength'  => 0.85
                ];
            
            // Stable Diffusion XL
            case 'sdxl':
                return [
                    'prompt'           => $prompt,
                    'width'            => 1024,
                    'height'           => 1024,
                    'num_outputs'      => $n,
                    'scheduler'        => 'K_EULER',
                    'num_inference_steps' => 25,
                    'guidance_scale'   => 7.5,
                    'negative_prompt'  => 'ugly, blurry, low quality',
                    'refine'           => 'expert_ensemble_refiner'
                ];
                
            // Google Imagen 3 Fast
            case 'imagen':
                return [
                    'prompt'              => $prompt,
                    'aspect_ratio'        => $aspect_ratio,
                    'output_format'       => 'jpeg',
                    'safety_filter_level' => 'block_some',
                    'person_generation'   => 'allow_adult',
                    'number_of_images'    => $n
                ];
                
            // Ideogram v2 Turbo
            case 'ideogram':
                return [
                    'prompt'              => $prompt,
                    'aspect_ratio'        => $aspect_ratio,
                    'magic_prompt_option' => 'AUTO',
                    'model'               => 'V_2_TURBO'
                ];
            
            // Playground v2.5
            case 'playground-v2':
                return [
                    'prompt'           => $prompt,
                    'width'            => 1024,
                    'height'           => 1024,
                    'scheduler'        => 'K_EULER_ANCESTRAL',
                    'num_inference_steps' => 25,
                    'guidance_scale'   => 3.0
                ];
                
            default:
                // Fallback genérico con aspect_ratio
                return [
                    'prompt'         => $prompt,
                    'aspect_ratio'   => $aspect_ratio,
                    'num_outputs'    => $n,
                    'output_format'  => 'webp',
                    'output_quality' => 85
                ];
        }
    }
    
    /**
     * Obtiene el catálogo de modelos Replicate
     * Actualizado Nov 2024 con endpoints verificados
     */
    private function get_replicate_model_catalog() {
        return [
            // FLUX Models (Black Forest Labs) - Los más rápidos y versátiles
            'flux-schnell'   => ['model_id' => 'black-forest-labs/flux-schnell'],      // ✅ MUY RÁPIDO
            'flux-dev'       => ['model_id' => 'black-forest-labs/flux-dev'],          // ✅ Más calidad
            'flux-pro'       => ['model_id' => 'black-forest-labs/flux-1.1-pro'],      // ✅ Máxima calidad
            
            // Stable Diffusion Models (Stability AI)
            'sd3-medium'     => ['model_id' => 'stability-ai/stable-diffusion-3-medium'], // ✅ Versátil
            'sdxl'           => ['model_id' => 'stability-ai/sdxl'],                    // ✅ Stable Diffusion XL
            
            // Google Imagen
            'imagen'         => ['model_id' => 'google-deepmind/imagen-3-fast'],       // ✅ Google Imagen 3 (fast)
            
            // Ideogram (Excelente para texto en imágenes)
            'ideogram'       => ['model_id' => 'ideogram-ai/ideogram-v2-turbo'],       // ✅ Ideogram v2 Turbo
            
            // Playground (Fotografía y realismo)
            'playground-v2'  => ['model_id' => 'playgroundai/playground-v2.5-1024px-aesthetic'], // ✅ Playground v2.5
        ];
    }
    
    /**
     * Guarda imagen base64 en biblioteca de medios
     */
    private function save_base64_image_to_media($b64, $filename, $post_id, $format, $watermark, $alt) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $upload_dir = wp_upload_dir();
        $decoded = base64_decode($b64);
        if (!$decoded) {
            $logger->error($session_id, 'image', "Error decodificando base64 para imagen destacada (OpenAI/Gemini)", ['filename' => $filename], $post_id);
            return false;
        }
        
        // Crear imagen desde string
        $image = imagecreatefromstring($decoded);
        if (!$image) {
            $logger->error($session_id, 'image', "Error creando imagen desde string base64 (OpenAI/Gemini)", ['filename' => $filename], $post_id);
            return false;
        }
        
        // Aplicar marca de agua si está habilitado
        if ($watermark) {
            $image = $this->apply_watermark($image);
        }
        
        // Determinar formato final y aplicar conversión optimizada
        $final_format = $format;
        $quality = 80; // Calidad por defecto
        
        // Si el formato es webp y está soportado, usar webp
        if ($format === 'webp' && function_exists('imagewebp')) {
            $filename = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $filename);
            $quality = 90;
        } 
        // Si no es webp, convertir PNG a JPG optimizado
        elseif (strpos(strtolower($filename), '.png') !== false) {
            // Convertir PNG a JPG para mejor compresión
            $filename = preg_replace('/\.png$/i', '.jpg', $filename);
            $final_format = 'jpg';
            $quality = 80; // Calidad 0.8 como solicitaste
            
            // Si la imagen tiene transparencia, crear fondo blanco
            if (imagecolortransparent($image) >= 0 || imageistruecolor($image)) {
                $width = imagesx($image);
                $height = imagesy($image);
                $new_image = imagecreatetruecolor($width, $height);
                
                // Fondo blanco
                $white = imagecolorallocate($new_image, 255, 255, 255);
                imagefill($new_image, 0, 0, $white);
                
                // Copiar imagen original sobre el fondo blanco
                imagecopy($new_image, $image, 0, 0, 0, 0, $width, $height);
                imagedestroy($image);
                $image = $new_image;
            }
        }
        
        $file_path = $upload_dir['path'] . '/' . $filename;
        
        // Guardar según formato con compresión optimizada
        if (strpos($filename, '.webp') !== false && function_exists('imagewebp')) {
            imagewebp($image, $file_path, $quality);
        } elseif (strpos($filename, '.jpg') !== false || strpos($filename, '.jpeg') !== false) {
            // JPG con compresión optimizada
            imagejpeg($image, $file_path, $quality);
        } else {
            // PNG como fallback (sin compresión, más pesado)
            imagepng($image, $file_path);
        }
        
        imagedestroy($image);
        
        // Crear attachment
        $filetype = wp_check_filetype($filename, null);
        $attachment = [
            'guid' => $upload_dir['url'] . '/' . basename($file_path),
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        
        $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);
        
        if (!$attach_id || is_wp_error($attach_id)) {
            $error_msg = is_wp_error($attach_id) ? $attach_id->get_error_message() : 'ID inválido';
            $logger->error($session_id, 'image', "Error insertando attachment (OpenAI/Gemini): " . $error_msg, ['filename' => $filename], $post_id);
            return false;
        }
        
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        update_post_meta($attach_id, '_wp_attachment_image_alt', $alt);
        
        $logger->success($session_id, 'image', "Imagen destacada subida exitosamente (OpenAI/Gemini)", [
            'attachment_id' => $attach_id,
            'filename' => $filename,
            'format' => $format
        ], $post_id);
        
        return $attach_id;
    }
    
    /**
     * Descarga imagen desde URL y la guarda en medios
     */
    private function sideload_image_from_url($url, $post_id, $format, $watermark, $alt) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        // Validar URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $logger->error($session_id, 'image', "URL inválida para imagen destacada (Replicate)", ['url' => $url], $post_id);
            return false;
        }
        
        // Descargar imagen temporal
        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            $logger->error($session_id, 'image', "Error descargando imagen destacada (Replicate): " . $tmp->get_error_message(), ['url' => $url], $post_id);
            return false;
        }
        
        // Cargar imagen para procesarla
        $image = @imagecreatefromstring(file_get_contents($tmp));
        if (!$image) {
            $logger->error($session_id, 'image', "Error creando imagen desde URL descargada (Replicate)", ['url' => $url], $post_id);
            @unlink($tmp);
            return false;
        }
        
        // Aplicar marca de agua si está habilitado
        if ($watermark) {
            $image = $this->apply_watermark($image);
        }
        
        $upload_dir = wp_upload_dir();
        $filename = basename($url);
        
        // Determinar formato final y aplicar conversión optimizada
        $quality = 80;
        
        // Si el formato es webp y está soportado, usar webp
        if ($format === 'webp' && function_exists('imagewebp')) {
            $filename = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $filename);
            $quality = 90;
        } 
        // Si no es webp, convertir PNG a JPG optimizado
        elseif (strpos(strtolower($filename), '.png') !== false) {
            // Convertir PNG a JPG para mejor compresión
            $filename = preg_replace('/\.png$/i', '.jpg', $filename);
            $quality = 80;
            
            // Si la imagen tiene transparencia, crear fondo blanco
            if (imagecolortransparent($image) >= 0 || imageistruecolor($image)) {
                $width = imagesx($image);
                $height = imagesy($image);
                $new_image = imagecreatetruecolor($width, $height);
                
                // Fondo blanco
                $white = imagecolorallocate($new_image, 255, 255, 255);
                imagefill($new_image, 0, 0, $white);
                
                // Copiar imagen original sobre el fondo blanco
                imagecopy($new_image, $image, 0, 0, 0, 0, $width, $height);
                imagedestroy($image);
                $image = $new_image;
            }
        }
        
        $file_path = $upload_dir['path'] . '/' . $filename;
        
        // Guardar según formato con compresión optimizada
        if (strpos($filename, '.webp') !== false && function_exists('imagewebp')) {
            imagewebp($image, $file_path, $quality);
        } elseif (strpos($filename, '.jpg') !== false || strpos($filename, '.jpeg') !== false) {
            // JPG con compresión optimizada
            imagejpeg($image, $file_path, $quality);
        } else {
            // PNG como fallback
            imagepng($image, $file_path);
        }
        
        imagedestroy($image);
        @unlink($tmp);
        
        // Crear attachment
        $filetype = wp_check_filetype($filename, null);
        $attachment = [
            'guid' => $upload_dir['url'] . '/' . basename($file_path),
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        
        $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);
        
        if (!$attach_id || is_wp_error($attach_id)) {
            $error_msg = is_wp_error($attach_id) ? $attach_id->get_error_message() : 'ID inválido';
            $logger->error($session_id, 'image', "Error insertando attachment desde URL (Replicate)", ['url' => $url], $post_id);
            return false;
        }
        
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        update_post_meta($attach_id, '_wp_attachment_image_alt', $alt);
        
        $logger->success($session_id, 'image', "Imagen destacada subida exitosamente desde URL (Replicate)", [
            'attachment_id' => $attach_id,
            'url' => $url,
            'format' => $format
        ], $post_id);
        
        return $attach_id;
    }
    
    /**
     * Aplica marca de agua a la imagen
     */
    private function apply_watermark($image) {
        // TODO: Implementar lógica de marca de agua
        return $image;
    }
    
    /**
     * Convierte texto a slug válido para nombre de archivo
     */
    private function slugify_filename($text) {
        $text = remove_accents($text);
        $text = preg_replace('/[^a-z0-9\s-]/i', '', $text);
        $text = preg_replace('/\s+/', '-', $text);
        $text = trim($text, '-');
        return strtolower($text);
    }
}



