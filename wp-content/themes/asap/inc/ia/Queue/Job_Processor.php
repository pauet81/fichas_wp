<?php
/**
 * Procesador de Trabajos de la Cola
 * 
 * Procesa trabajos de la cola (artículos, metas, imágenes).
 * 
 * @package ASAP_Theme
 * @subpackage IA\Queue
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Queue_Job_Processor {
    
    /**
     * @var ASAP_IA_Generators_Article_Generator
     */
    private $article_generator;
    
    /**
     * @var ASAP_IA_Queue_Queue_Manager
     */
    private $queue_manager;
    
    /**
     * Constructor
     */
    public function __construct($article_generator, $queue_manager) {
        $this->article_generator = $article_generator;
        $this->queue_manager = $queue_manager;
    }
    
    /**
     * Alias para compatibilidad - Delega a process()
     */
    public function process_job_by_id($job_id) {
        return $this->process($job_id);
    }

    /**
     * Cron: Procesa toda la cola automáticamente
     */
    public function cron_process_queue() {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        // Verificar si el auto-procesamiento está habilitado
        if (get_option('asap_auto_process_queue', '0') !== '1') {
            return; // No registrar nada si está deshabilitado
        }
        
        // Verificar si hay trabajos pendientes ANTES de registrar
        $has_pending = $this->queue_manager->get_next_job();
        if (!$has_pending) {
            return; // No hay nada que procesar, salir silenciosamente
        }
        
        // Solo registrar si HAY trabajos pendientes
        $logger->info($session_id, 'queue', 'Iniciando procesamiento automático de cola');

        // Procesar hasta 5 jobs por cron
        $processed = 0;
        $max_jobs = 5;
        $start_time = microtime(true);

        while ($processed < $max_jobs) {
            $job = $this->queue_manager->get_next_job();
            
            if (!$job) {
                // No hay más jobs pendientes
                $duration = microtime(true) - $start_time;
                $logger->success($session_id, 'queue', "Cola procesada: {$processed} trabajos completados", null, null, null, $duration);
                $this->queue_manager->maybe_send_completion_email();
                break;
            }

            $logger->info($session_id, 'queue', "Procesando job #{$job->id}: {$job->type}");
            
            $result = $this->process($job->id);
            $processed++;

            // Si hay error, marcar como failed
            if (is_wp_error($result)) {
                $logger->error($session_id, 'queue', "Error en job #{$job->id}: " . $result->get_error_message());
                $this->queue_manager->update_status($job->id, 'failed', [
                    'error_message' => $result->get_error_message()
                ]);
            } else {
                $logger->success($session_id, 'queue', "Job #{$job->id} completado exitosamente");
            }
        }
    }
    
    /**
     * Procesa un trabajo de la cola
     * 
     * @param int $job_id ID del trabajo
     * @return int|WP_Error ID del post creado o WP_Error
     */
    public function process($job_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'asap_ia_queue';
        
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        $logger->info($session_id, 'queue', "Iniciando procesamiento de job #{$job_id}");
        
        // Obtener el job
        $job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $job_id));
        if (!$job) {
            $logger->error($session_id, 'queue', "Job #{$job_id} no encontrado en la base de datos");
            return new WP_Error('job_not_found', 'Job no encontrado.');
        }

        // Parsear parámetros
        $params = json_decode($job->params, true);
        if (!is_array($params)) {
            $logger->error($session_id, 'queue', "Parámetros inválidos para job #{$job_id}");
            return new WP_Error('invalid_params', 'Parámetros inválidos.');
        }
        
        $h1 = $params['h1'] ?? 'Sin título';
        $logger->info($session_id, 'queue', "Job #{$job_id}: Generando artículo '{$h1}'");

        // Procesar según tipo
        if ($job->type === 'article' || ($params['type'] ?? '') === 'article') {
            // ⭐ Agregar configuración de imágenes para que pueda generar imágenes de contenido
            $params['image_settings'] = get_option('asap_img_settings', []);
            // ⭐ AGREGAR API KEY DE REPLICATE
            $params['image_settings']['replicate_api_key'] = get_option('asap_ia_replicate_api_token', '');
            
            // Generación por secciones con contexto
            $start_time = microtime(true);
            $result = $this->article_generator->generate_by_sections($params);
            $duration = microtime(true) - $start_time;
            
            if (is_wp_error($result)) {
                $logger->error($session_id, 'queue', "Error generando job #{$job_id}: " . $result->get_error_message(), null, null, null, $duration);
                return $result;
            }
            
            $post_id = $result['post_id'] ?? $result;
            $cost = $result['cost_usd'] ?? 0;

            // Marcar como completado
            $this->queue_manager->update_status($job_id, 'completed', ['post_id' => $post_id]);
            
            $logger->success($session_id, 'queue', "Job #{$job_id} completado: Post #{$post_id} creado", ['post_id' => $post_id, 'h1' => $h1], $post_id, null, $duration, $cost);
            
            return $post_id;
        }

        $logger->error($session_id, 'queue', "Tipo de job desconocido para job #{$job_id}: {$job->type}");
        return new WP_Error('unknown_type', 'Tipo de job desconocido.');
    }
    
    /**
     * Procesa el siguiente job de la cola
     * 
     * @return int|WP_Error|null ID del post creado, WP_Error si falla, o null si no hay jobs
     */
    public function process_next() {
        // Verificar límite de 5/hora
        if (!$this->queue_manager->can_process()) {
            return new WP_Error('rate_limit', 'Límite de 5 artículos por hora alcanzado. Espera un momento e intenta nuevamente.');
        }
        
        // Obtener siguiente job
        $job = $this->queue_manager->get_next_job();
        if (!$job) {
            return null; // No hay jobs pendientes
        }
        
        // Marcar como en procesamiento
        $this->queue_manager->update_status($job->id, 'processing', ['attempts' => intval($job->attempts) + 1]);
        
        // Procesar
        $result = $this->process($job->id);
        
        if (is_wp_error($result)) {
            // Marcar como fallido
            $this->queue_manager->update_status($job->id, 'failed', [
                'error_message' => $result->get_error_message(),
                'attempts' => intval($job->attempts) + 1
            ]);
            
            return $result;
        }
        
        return $result;
    }
}



