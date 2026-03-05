<?php
/**
 * Hooks Manager
 * 
 * Gestiona todos los hooks de WordPress para el sistema de IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Hooks
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Hooks_Manager {
    
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
     * Registra todos los hooks
     */
    public function register_hooks() {
        add_action('transition_post_status', [$this, 'on_transition_post_status'], 10, 3);
        add_action('save_post', [$this, 'on_save_post'], 20, 3);
        
        // Hook para generación de imágenes en background
        add_action('asap_ia_generate_featured_image', [$this, 'background_generate_featured_image'], 10, 1);
        
        // Hook para generación de meta tags en background
        add_action('asap_ia_generate_meta_tags', [$this, 'background_generate_meta_tags'], 10, 1);
        
        // Hook para inyectar FAQ Schema en el head
        add_action('wp_head', [$this, 'inject_faq_schema'], 10);
        
        // Hook para publicación automática programada
        add_action('asap_auto_publish_posts', [$this, 'auto_publish_scheduled_posts']);
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
     * Hook: transition_post_status
     * Se ejecuta cuando un post cambia de estado
     */
    public function on_transition_post_status($new_status, $old_status, $post) {
        // Prevenir ejecuciones duplicadas (WordPress puede llamar este hook múltiples veces)
        static $processed_posts = [];
        $post_key = $post->ID . '_' . $new_status . '_' . $old_status;
        
        if (isset($processed_posts[$post_key])) {
            return; // Ya procesamos este post en esta transición
        }
        $processed_posts[$post_key] = true;
        
        // Metaetiquetas según configuración
        $meta_enabled = $this->is_meta_enabled();
        
        if ($meta_enabled) {
            $meta_when = get_option('asap_meta_when', 'publish');
            $should_generate_meta = false;
            
            // Determinar si debemos generar según la configuración
            if ($meta_when === 'publish' && $new_status === 'publish' && $old_status !== 'publish') {
                $should_generate_meta = true;
            } elseif ($meta_when === 'save' && ($new_status === 'publish' || $new_status === 'draft')) {
                $should_generate_meta = true;
            }
            
            if ($should_generate_meta) {
                // Programar generación en background (después de que Yoast/RankMath terminen)
                // Delay de 3 segundos para que los plugins SEO terminen primero
                wp_schedule_single_event(time() + 3, 'asap_ia_generate_meta_tags', [$post->ID]);
            }
        }
        
        // Imágenes destacadas según configuración
        $images_enabled = $this->is_images_enabled();
        
        if ($images_enabled) {
            $img_when = get_option('asap_img_when', 'publish');
            $should_generate = false;
            
            // Determinar si debemos generar según la configuración
            if ($img_when === 'publish' && $new_status === 'publish' && $old_status !== 'publish') {
                $should_generate = true;
            } elseif ($img_when === 'save' && ($new_status === 'publish' || $new_status === 'draft')) {
                $should_generate = true;
            } elseif ($img_when === 'publish_update' && $new_status === 'publish') {
                $should_generate = true;
            }
            
            if ($should_generate && !has_post_thumbnail($post->ID)) {
                // Programar generación en background (no bloquea la publicación)
                // Delay de 10 segundos para evitar conflictos con meta tags (que se ejecutan a los 3s)
                // Esto asegura que no se pisen entre sí al usar la API
                wp_schedule_single_event(time() + 10, 'asap_ia_generate_featured_image', [$post->ID]);
            }
        }
    }
    
    /**
     * Hook: save_post
     * Se ejecuta al guardar un post (incluyendo actualizaciones)
     * 
     * NOTA: Ya no se usa para metaetiquetas ni imágenes.
     * Todo se maneja en transition_post_status con las opciones "publish" o "save"
     */
    public function on_save_post($post_id, $post = null, $update = false) {
        // Este hook se mantiene por compatibilidad pero ya no hace nada
        // La lógica se movió completamente a transition_post_status
    }
    
    /**
     * Verifica si las metaetiquetas están habilitadas
     */
    private function is_meta_enabled() {
        // Verificar si está habilitado
        if (get_option('asap_meta_enable', '0') !== '1') {
            return false;
        }
        
        // Verificar que haya API key según el proveedor
        $provider = get_option('asap_ia_provider', 'openai');
        
        if ($provider === 'gemini') {
            $gemini_key = get_option('asap_ia_gemini_key', '');
            return !empty($gemini_key);
        } else {
        $openai_key = $this->call_instance_method('get_openai_key');
            return !empty($openai_key);
        }
    }
    
    /**
     * Genera imagen destacada en background
     * 
     * @param int $post_id ID del post
     */
    public function background_generate_featured_image($post_id) {
        // Verificar que el post existe
        $post = get_post($post_id);
        if (!$post) {
            return;
        }
        
        // Verificar que no tenga imagen destacada ya
        if (has_post_thumbnail($post_id)) {
            return;
        }
        
        // Generar imagen
        try {
            $this->call_instance_method('maybe_generate_featured_image_for_post', $post_id, 'publish');
        } catch (Exception $e) {
            // Silenciar error
        }
    }
    
    /**
     * Genera meta tags en background (asíncrono)
     * 
     * @param int $post_id ID del post
     */
    public function background_generate_meta_tags($post_id) {
        // Verificar que el post existe
        $post = get_post($post_id);
        if (!$post) {
            return;
        }
        
        // Generar meta tags
        try {
            $this->call_instance_method('maybe_generate_meta_for_post', $post_id, false);
        } catch (Exception $e) {
            // Silenciar error
        }
    }
    
    /**
     * Verifica si las imágenes están habilitadas
     */
    private function is_images_enabled() {
        $settings = $this->call_instance_method('get_image_settings');
        
        if ($settings['enable'] !== '1') {
            return false;
        }
        
        // Verificar API keys según el proveedor
        $provider = $settings['provider'] ?? 'openai';
        
        if ($provider === 'openai') {
            $openai_key = get_option('asap_ia_openai_api_key', '');
            if (empty($openai_key)) {
                return false;
            }
        } elseif ($provider === 'gemini') {
            $gemini_key = get_option('asap_ia_gemini_api_key', '');
            if (empty($gemini_key)) {
            return false;
        }
        } elseif ($provider === 'replicate') {
            $replicate_key = get_option('asap_ia_replicate_api_token', '');
            if (empty($replicate_key)) {
            return false;
            }
        }
        
        return true;
    }
    
    /**
     * Inyecta el Schema de FAQs en el <head> si el post lo tiene
     */
    public function inject_faq_schema() {
        if (!is_singular()) {
            return;
        }
        
        $post_id = get_the_ID();
        if (!$post_id) {
            return;
        }
        
        $faq_schema = get_post_meta($post_id, '_asap_faq_schema', true);
        if (empty($faq_schema)) {
            return;
        }
        
        // El schema ya viene como JSON puro, solo agregar el script tag
        echo '<script type="application/ld+json">' . "\n" . $faq_schema . "\n" . '</script>' . "\n";
    }
    
    /**
     * Publica automáticamente posts en borrador según el intervalo configurado
     * Se ejecuta cada hora via WP Cron
     */
    public function auto_publish_scheduled_posts() {
        // Verificar si está habilitado
        if (get_option('asap_queue_auto_publish_enabled', '0') !== '1') {
            return;
        }
        
        $interval_hours = intval(get_option('asap_queue_auto_publish_interval', 24));
        
        // Buscar posts en borrador que fueron creados por la IA y estén programados para publicar
        $args = [
            'post_type' => 'any',
            'post_status' => 'draft',
            'posts_per_page' => 10, // Publicar máximo 10 por ejecución
            'meta_query' => [
                [
                    'key' => '_asap_ia_generated',
                    'value' => '1',
                    'compare' => '='
                ]
            ],
            'orderby' => 'date',
            'order' => 'ASC'
        ];
        
        $drafts = get_posts($args);
        
        if (empty($drafts)) {
            return;
        }
        
        // Obtener el timestamp del último post publicado automáticamente
        $last_published = get_option('asap_last_auto_published', 0);
        $now = time();
        $interval_seconds = $interval_hours * HOUR_IN_SECONDS;
        
        // Verificar si ya pasó el tiempo necesario
        if (($now - $last_published) < $interval_seconds) {
            return; // Aún no es tiempo de publicar
        }
        
        // Publicar el primer borrador de la lista
        $post_to_publish = $drafts[0];
        
        wp_update_post([
            'ID' => $post_to_publish->ID,
            'post_status' => 'publish'
        ]);
        
        // Actualizar el timestamp del último post publicado
        update_option('asap_last_auto_published', $now);
    }
}


