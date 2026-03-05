<?php
/**
 * Gestor de Cola de Trabajos IA
 * 
 * Maneja la cola de trabajos pendientes, procesamiento y estadísticas.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Queue
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Queue_Queue_Manager {
    
    /**
     * @var string Nombre de la tabla de cola
     */
    private $table_name;
    
    /**
     * @var string Nombre de la tabla de logs
     */
    private $logs_table;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'asap_ia_queue';
        $this->logs_table = $wpdb->prefix . 'asap_ia_logs';
    }
    
    /**
     * Agrega un trabajo a la cola
     * 
     * @param array $params Parámetros del trabajo
     * @return int|WP_Error ID del trabajo o WP_Error si falla
     */
    public function add_to_queue($params) {
        global $wpdb;
        
        // ✅ Sin límite de cola (permite importaciones masivas de 500+ artículos)
        // Nota: El límite anterior de 100 impedía importaciones grandes.
        // Ahora el único límite es la capacidad del servidor/base de datos.
        
        $data = [
            'user_id' => get_current_user_id(),
            'status' => 'pending',
            'type' => $params['type'] ?? 'article',
            'params' => wp_json_encode($params),
            'scheduled_at' => $params['scheduled_at'] ?? null,
            'publish_immediately' => isset($params['publish_immediately']) ? intval($params['publish_immediately']) : 1,
        ];
        
        $wpdb->insert($this->table_name, $data);
        
        return $wpdb->insert_id;
    }
    
    /**
     * Obtiene el siguiente trabajo de la cola
     * 
     * @return object|null Objeto del trabajo o null si no hay
     */
    public function get_next_job() {
        global $wpdb;
        
        // Limpiar jobs atascados primero
        $this->cleanup_stuck_jobs();
        
        // Obtener el siguiente job pendiente que:
        // - Esté en estado 'pending'
        // - Y no tenga scheduled_at (publicar inmediatamente)
        // - O tenga scheduled_at <= current_time() (ya llegó su hora de publicación)
        // Usar current_time() de WordPress para respetar la zona horaria del sitio
        $current_wp_time = current_time('mysql');
        $job = $wpdb->get_row($wpdb->prepare("
            SELECT * FROM {$this->table_name} 
            WHERE status = 'pending' 
            AND (
                scheduled_at IS NULL 
                OR scheduled_at <= %s
            )
            ORDER BY id ASC 
            LIMIT 1
        ", $current_wp_time));
        
        return $job;
    }
    
    /**
     * Obtiene estadísticas de la cola
     * 
     * @return array Estadísticas
     */
    public function get_stats() {
        global $wpdb;
        
        // Usar current_time() para respetar la zona horaria de WordPress
        $today = date('Y-m-d', strtotime(current_time('mysql')));
        
        $pending = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'pending'");
        $processing = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'processing'");
        $completed = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'completed' AND DATE(processed_at) = %s",
            $today
        ));
        $failed = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'failed'");
        
        return [
            'pending' => intval($pending),
            'processing' => intval($processing),
            'completed_today' => intval($completed),
            'failed' => intval($failed),
        ];
    }
    
    /**
     * Obtiene items de la cola
     * 
     * @param string $status Estado de los items ('all', 'pending', 'completed', 'failed')
     * @param int $limit Límite de resultados
     * @return array Lista de items
     */
    public function get_items($status = 'all', $limit = 20) {
        global $wpdb;
        
        $where = '';
        if ($status !== 'all') {
            $where = $wpdb->prepare("WHERE status = %s", $status);
        }
        
        $items = $wpdb->get_results("SELECT * FROM {$this->table_name} $where ORDER BY id DESC LIMIT " . intval($limit));
        
        return $items;
    }
    
    /**
     * Actualiza el estado de un trabajo
     * 
     * @param int $job_id ID del trabajo
     * @param string $status Nuevo estado
     * @param array $data Datos adicionales
     */
    public function update_status($job_id, $status, $data = []) {
        global $wpdb;
        
        $update = ['status' => $status];
        
        // Timestamp para cuando empieza a procesarse
        if ($status === 'processing') {
            $update['started_at'] = current_time('mysql');
        }
        
        // Timestamp para cuando termina (éxito o fallo)
        if ($status === 'completed' || $status === 'failed') {
            $update['processed_at'] = current_time('mysql');
        }
        
        // Si vuelve a pending (retry), limpiar started_at
        if ($status === 'pending') {
            $update['started_at'] = null;
        }
        
        if (isset($data['post_id'])) {
            $update['post_id'] = intval($data['post_id']);
        }
        
        if (isset($data['error_message'])) {
            $update['error_message'] = $data['error_message'];
        }
        
        if (isset($data['attempts'])) {
            $update['attempts'] = intval($data['attempts']);
        }
        
        $wpdb->update($this->table_name, $update, ['id' => $job_id]);
    }
    
    /**
     * Verifica si se puede procesar la cola (límite de 5/hora)
     * 
     * @return bool
     */
    public function can_process() {
        global $wpdb;
        
        // Usar current_time() para respetar la zona horaria de WordPress
        // Calcular hace 1 hora desde el tiempo actual de WordPress
        $one_hour_ago_timestamp = strtotime('-1 hour', strtotime(current_time('mysql')));
        $one_hour_ago = date('Y-m-d H:i:s', $one_hour_ago_timestamp);
        
        // Contar cuántos artículos se procesaron en la última hora (incluyendo cola y manual)
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->logs_table} 
            WHERE type = 'article' 
            AND action IN ('generate_article', 'generate_article_from_queue') 
            AND created_at >= %s",
            $one_hour_ago
        ));
        
        return intval($count) < 5; // Máximo 5 por hora
    }
    
    /**
     * Elimina un trabajo de la cola
     * 
     * @param int $job_id ID del trabajo
     */
    public function delete_item($job_id) {
        global $wpdb;
        $wpdb->delete($this->table_name, ['id' => $job_id], ['%d']);
    }
    
    /**
     * Limpia trabajos atascados en "processing"
     */
    public function cleanup_stuck_jobs() {
        global $wpdb;
        
        // Obtener jobs en processing que llevan más de 5 minutos
        // Usar current_time() para respetar la zona horaria de WordPress
        $timeout_minutes = 5;
        $timeout_timestamp = strtotime("-{$timeout_minutes} minutes", strtotime(current_time('mysql')));
        $timeout_time = date('Y-m-d H:i:s', $timeout_timestamp);
        
        $stuck_jobs = $wpdb->get_results($wpdb->prepare("
            SELECT id, attempts, started_at 
            FROM {$this->table_name}
            WHERE status = 'processing'
            AND (
                started_at IS NULL 
                OR started_at < %s
            )
        ", $timeout_time));
        
        foreach ($stuck_jobs as $job) {
            $max_attempts = 3;
            
            if ($job->attempts < $max_attempts) {
                // Resetear a pending para retry
                $wpdb->update(
                    $this->table_name,
                    [
                        'status' => 'pending',
                        'started_at' => null,
                        'error_message' => 'Job atascado en processing (>5min), reintentando. Intento ' . ($job->attempts + 1) . '/' . $max_attempts,
                        'attempts' => intval($job->attempts) + 1,
                    ],
                    ['id' => $job->id]
                );
            } else {
                // Si alcanzó el límite, marcar como failed
                $wpdb->update(
                    $this->table_name,
                    [
                        'status' => 'failed',
                        'processed_at' => current_time('mysql'),
                        'error_message' => 'Job atascado después de ' . $max_attempts . ' intentos. Timeout: ' . $timeout_minutes . ' minutos.',
                    ],
                    ['id' => $job->id]
                );
            }
        }
    }
    
    /**
     * Limpia trabajos completados (mantiene solo últimos N)
     * 
     * @param int $keep_last Número de trabajos a mantener
     */
    public function cleanup_completed($keep_last = 50) {
        global $wpdb;
        
        // Eliminar trabajos completados excepto los últimos N
        $wpdb->query($wpdb->prepare("
            DELETE FROM {$this->table_name}
            WHERE status = 'completed'
            AND id NOT IN (
                SELECT id FROM (
                    SELECT id FROM {$this->table_name}
                    WHERE status = 'completed'
                    ORDER BY processed_at DESC
                    LIMIT %d
                ) AS keep_jobs
            )
        ", $keep_last));
    }
    
    /**
     * Verifica si la cola está vacía
     * 
     * @return bool
     */
    public function is_empty() {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'pending'");
        return intval($count) === 0;
    }
    
    /**
     * Verifica si hay trabajos en procesamiento
     * 
     * @return bool
     */
    public function has_processing_jobs() {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'processing'");
        return intval($count) > 0;
    }

    /**
     * Vacía toda la cola (elimina todos los items pendientes y fallidos)
     * 
     * @return int Número de items eliminados
     */
    public function clear_all_queue() {
        global $wpdb;
        
        // Eliminar pendientes y fallidos (mantener completed para historial)
        $deleted = $wpdb->query("
            DELETE FROM {$this->table_name}
            WHERE status IN ('pending', 'failed')
        ");
        
        return intval($deleted);
    }
    
    /**
     * Envía email de notificación cuando la cola se completa
     */
    public function maybe_send_completion_email() {
        // Verificar si las notificaciones están habilitadas
        if (get_option('asap_queue_email_notifications', '0') !== '1') {
            return;
        }

        // Verificar si hay pending
        $stats = $this->get_stats();
        if ($stats['pending'] > 0) {
            return; // Aún hay trabajos pendientes
        }

        // Verificar si ya enviamos email recientemente (último minuto)
        $last_sent = get_transient('asap_queue_completion_email_sent');
        if ($last_sent) {
            return;
        }

        // Enviar email
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $subject = "[{$site_name}] Cola de IA completada";
        
        $message = "Hola,\n\n";
        $message .= "La cola de generación de artículos con IA se ha completado.\n\n";
        $message .= "Estadísticas:\n";
        $message .= "- Completados hoy: {$stats['completed_today']}\n";
        $message .= "- Fallidos: {$stats['failed']}\n\n";
        $message .= "Ver dashboard: " . admin_url('admin.php?page=asap-menu-ia&tab=ia_dashboard') . "\n\n";
        $message .= "Saludos,\n";
        $message .= "Sistema de IA de {$site_name}";

        wp_mail($admin_email, $subject, $message);

        // Marcar como enviado (durante 1 minuto)
        set_transient('asap_queue_completion_email_sent', true, MINUTE_IN_SECONDS);
    }
}
