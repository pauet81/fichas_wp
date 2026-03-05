<?php
/**
 * Content Image Injector
 * 
 * Genera e inyecta imágenes dentro del contenido del artículo,
 * distribuidas según la estrategia configurada.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Generators
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Generators_Content_Image_Injector {
    
    private $image_generator;
    private $openai_client;
    
    public function __construct($openai_client, $image_generator) {
        $this->openai_client = $openai_client;
        $this->image_generator = $image_generator;
    }
    
    /**
     * Determina si debe generar imagen para esta sección
     * 
     * @param int $section_index Índice de la sección (0-based)
     * @param int $total_sections Total de secciones
     * @param array $settings Configuración de imágenes
     * @return bool
     */
    public function should_inject_image_here($section_index, $total_sections, $settings) {
        if (($settings['content_enable'] ?? '0') !== '1') {
            return false;
        }
        
        $quantity = intval($settings['content_quantity'] ?? 3);
        $strategy = $settings['content_strategy'] ?? 'auto';
        
        switch ($strategy) {
            case 'each_h2':
                // Una imagen en cada H2 (máximo $quantity)
                return $section_index < $quantity;
                
            case 'every_n':
                // Cada N secciones
                $n = intval($settings['content_every_n'] ?? 2);
                return ($section_index % $n) === 0 && ($section_index / $n) < $quantity;
                
            case 'auto':
            default:
                // Distribución uniforme automática
                return in_array($section_index, $this->calculate_uniform_distribution($total_sections, $quantity), true);
        }
    }
    
    /**
     * Calcula posiciones para distribución uniforme
     * 
     * @param int $total Total de elementos
     * @param int $quantity Cantidad a distribuir
     * @return array Índices donde insertar
     */
    private function calculate_uniform_distribution($total, $quantity) {
        if ($quantity >= $total) {
            // Si queremos más imágenes que secciones, poner en todas
            return range(0, $total - 1);
        }
        
        if ($quantity <= 0) {
            return [];
        }
        
        $step = $total / $quantity;
        $positions = [];
        
        for ($i = 0; $i < $quantity; $i++) {
            $positions[] = (int) floor($i * $step);
        }
        
        return $positions;
    }
    
    /**
     * Genera una imagen para una sección específica
     * 
     * @param string $h1 Título del artículo
     * @param string $h2 Título de la sección
     * @param string $section_content Contenido de la sección
     * @param array $settings Configuración de imágenes
     * @param string $session_id ID de sesión para logging
     * @return array|null ['url' => '...', 'attachment_id' => 123] o null si falla
     */
    public function generate_image_for_section($h1, $h2, $section_content, $settings, $session_id) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        
        // Construir prompt contextual
        $prompt = $this->build_content_image_prompt($h1, $h2, $section_content, $settings);
        
        $logger->info($session_id, 'content_image', "Generando imagen para sección: {$h2}");
        
        $provider = $settings['provider'] ?? 'openai';
        $start_time = microtime(true);
        
        // Generar imagen
        if ($provider === 'openai') {
            $attachments = $this->generate_single_openai($prompt, $settings);
        } elseif ($provider === 'replicate') {
            $attachments = $this->generate_single_replicate($prompt, $settings);
        } else {
            $logger->error($session_id, 'content_image', "Proveedor desconocido: {$provider}");
            return null;
        }
        
        $duration = microtime(true) - $start_time;
        
        if (empty($attachments)) {
            $logger->error($session_id, 'content_image', "Error generando imagen para: {$h2}", null, null, null, $duration);
            return null;
        }
        
        $logger->success($session_id, 'content_image', "Imagen generada para: {$h2}", [
            'provider' => $provider,
            'h2' => $h2
        ], null, null, $duration);
        
        return $attachments[0] ?? null;
    }
    
    /**
     * Construye prompt contextual para imagen de contenido
     */
    private function build_content_image_prompt($h1, $h2, $section_content, $settings) {
        $lang = $settings['lang'] ?? 'es';
        $lang_map = ['es' => 'español', 'en' => 'English', 'pt' => 'português'];
        $lang_full = $lang_map[$lang] ?? 'español';
        
        // Extraer primeras 200 palabras del contenido para contexto
        $words = str_word_count($section_content, 2);
        $context_words = array_slice($words, 0, 200, true);
        $context = implode(' ', $context_words);
        
        // Usar template personalizado si existe
        $template = $settings['content_prompt_template'] ?? '';
        
        if (empty($template)) {
            $template = "Genera una imagen profesional y fotorrealista para ilustrar un artículo.

Artículo principal: \"{h1}\"
Sección específica que ilustra: \"{h2}\"

Contexto de la sección:
{context}

Requisitos:
- Estilo fotográfico profesional, realista, limpio
- Composición clara y atractiva
- IMPORTANTE: NO incluir texto, letras, palabras ni tipografía en la imagen
- Sin marcas de agua
- Alta calidad visual
- Idioma visual: {lang}";
        }
        
        // Reemplazar placeholders
        $prompt = str_replace(['{h1}', '{h2}', '{context}', '{lang}'], 
                              [$h1, $h2, $context, $lang_full], 
                              $template);
        
        return $prompt;
    }
    
    /**
     * Genera una sola imagen con OpenAI
     */
    private function generate_single_openai($prompt, $settings) {
        $api_key = $this->openai_client->get_api_key();
        if (!$api_key) return [];
        
        $size = $settings['openai_size'] ?? '1024x1024';
        $quality = $settings['openai_quality'] ?? 'standard';
        
        $endpoint = 'https://api.openai.com/v1/images/generations';
        
        $body = [
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1, // Solo 1 imagen para contenido
            'size' => $size,
            'quality' => $quality,
            'response_format' => 'url'
        ];
        
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 60,
            'body' => wp_json_encode($body),
        ];
        
        $res = wp_remote_post($endpoint, $args);
        
        if (is_wp_error($res)) return [];
        
        $json = json_decode(wp_remote_retrieve_body($res), true);
        
        if (empty($json['data'])) return [];
        
        $attachments = [];
        foreach ($json['data'] as $item) {
            if (!empty($item['url'])) {
                $attachments[] = ['url' => $item['url']];
            }
        }
        
        return $attachments;
    }
    
    /**
     * Genera una sola imagen con Replicate
     */
    private function generate_single_replicate($prompt, $settings) {
        $api_key = $settings['replicate_api_key'] ?? '';
        if (!$api_key) return [];
        
        $model_slug = $settings['replicate_model'] ?? 'flux-schnell';
        $aspect_ratio = $settings['replicate_aspect'] ?? '1:1';
        
        // Obtener model_id del catálogo
        $catalog = $this->get_replicate_model_catalog();
        if (!isset($catalog[$model_slug])) {
            $model_slug = 'flux-schnell';
        }
        $model_id = $catalog[$model_slug]['model_id'];
        
        $endpoint = "https://api.replicate.com/v1/models/{$model_id}/predictions";
        
        // Input básico según el modelo
        $input = [
            'prompt' => $prompt,
            'aspect_ratio' => $aspect_ratio,
            'go_fast' => true,
            'num_outputs' => 1,
            'output_format' => 'webp',
            'output_quality' => 80
        ];
        
        $body = ['input' => $input];
        
        $args = [
            'headers' => [
                'Authorization' => 'Token ' . $api_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'wait',
            ],
            'timeout' => 60,
            'body' => wp_json_encode($body),
        ];
        
        $res = wp_remote_post($endpoint, $args);
        
        if (is_wp_error($res)) return [];
        
        $json = json_decode(wp_remote_retrieve_body($res), true);
        
        if (empty($json['output'])) return [];
        
        $attachments = [];
        $output = is_array($json['output']) ? $json['output'] : [$json['output']];
        
        foreach ($output as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $attachments[] = ['url' => $url];
            }
        }
        
        return $attachments;
    }
    
    /**
     * Obtiene catálogo de modelos Replicate
     */
    private function get_replicate_model_catalog() {
        return [
            'flux-schnell' => [
                'label' => 'FLUX.1 schnell (BFL)', 
                'desc' => 'Imágenes realistas, MUY rápido (ideal para iterar).',
                'model_id' => 'black-forest-labs/flux-schnell'
            ],
            'flux-dev' => [
                'label' => 'FLUX.1 dev (BFL)',
                'desc' => 'Más calidad que schnell; algo más lento.',
                'model_id' => 'black-forest-labs/flux-dev'
            ],
            'sd3-medium' => [
                'label' => 'Stable Diffusion 3 (medium)',
                'desc' => 'Versátil; buena coherencia.',
                'model_id' => 'stability-ai/stable-diffusion-3-medium'
            ],
            'imagen' => [
                'label' => 'Google Imagen 3',
                'desc' => 'Modelo de Google, alta calidad.',
                'model_id' => 'google-deepmind/imagen-3'
            ],
            'ideogram' => [
                'label' => 'Ideogram v2 Turbo',
                'desc' => 'Excelente para texto en imágenes.',
                'model_id' => 'ideogram-ai/ideogram-v2'
            ],
            'sdxl' => [
                'label' => 'Stable Diffusion XL',
                'desc' => 'Versátil, buena calidad general.',
                'model_id' => 'stability-ai/sdxl'
            ],
            'playground-v2' => [
                'label' => 'Playground v2.5',
                'desc' => 'Excelente para fotografía y realismo.',
                'model_id' => 'playgroundai/playground-v2.5-1024px-aesthetic'
            ],
        ];
    }
    
    /**
     * Inyecta imagen HTML al inicio del contenido de la sección
     * 
     * @param string $section_html HTML de la sección
     * @param string $image_url URL de la imagen
     * @param string $alt Texto alternativo
     * @param array $settings Configuración
     * @return string HTML con imagen inyectada
     */
    public function inject_image_into_html($section_html, $image_url, $alt, $settings) {
        $position = $settings['content_position'] ?? 'after_h2';
        
        // Construir tag de imagen
        $img_class = 'asap-content-generated-image';
        $img_style = $this->get_image_style($settings);
        
        $img_tag = sprintf(
            '<img src="%s" alt="%s" class="%s" style="%s" loading="lazy">',
            esc_url($image_url),
            esc_attr($alt),
            esc_attr($img_class),
            esc_attr($img_style)
        );
        
        switch ($position) {
            case 'after_h2':
                // Insertar justo después del H2
                return preg_replace(
                    '/(<h2[^>]*>.*?<\/h2>)/i',
                    '$1' . "\n" . $img_tag . "\n",
                    $section_html,
                    1
                );
                
            case 'before_content':
                // Al principio de todo
                return $img_tag . "\n" . $section_html;
                
            case 'after_first_p':
                // Después del primer párrafo
                return preg_replace(
                    '/(<p[^>]*>.*?<\/p>)/is',
                    '$1' . "\n" . $img_tag . "\n",
                    $section_html,
                    1
                );
                
            default:
                return $img_tag . "\n" . $section_html;
        }
    }
    
    /**
     * Genera estilos CSS inline para la imagen
     */
    private function get_image_style($settings) {
        $style = $settings['content_img_style'] ?? 'centered';
        
        switch ($style) {
            case 'full_width':
                return 'width:100%; height:auto; margin:20px 0;';
                
            case 'float_left':
                return 'float:left; max-width:45%; height:auto; margin:10px 20px 10px 0;';
                
            case 'float_right':
                return 'float:right; max-width:45%; height:auto; margin:10px 0 10px 20px;';
                
            case 'centered':
            default:
                return 'display:block; max-width:80%; height:auto; margin:20px auto;';
        }
    }
}



