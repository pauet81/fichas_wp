<?php
/**
 * Generation Logger - Sistema de logs detallados para generación de artículos
 * 
 * Registra cada paso del proceso:
 * - Análisis SERP
 * - Scraping de competidores
 * - Extracción de entidades
 * - Generación de secciones
 * - Errores y reintentos
 * 
 * @package ASAP_Theme
 * @subpackage IA\Database
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Database_Generation_Logger {
    
    /**
     * Nombre de la tabla de logs
     */
    const TABLE_NAME = 'asap_ia_generation_logs';
    
    /**
     * Tipos de log
     */
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_DEBUG = 'debug';
    
    /**
     * Categorías de log
     */
    const CAT_SERP = 'serp_analysis';
    const CAT_SCRAPING = 'scraping';
    const CAT_ENTITIES = 'entities';
    const CAT_BRIEFING = 'briefing';
    const CAT_GENERATION = 'generation';
    const CAT_SECTION = 'section';
    const CAT_INTRO = 'intro';
    const CAT_CONCLUSION = 'conclusion';
    const CAT_FAQS = 'faqs';
    const CAT_POST = 'post_creation';
    const CAT_IMAGE = 'image';
    const CAT_META = 'meta';
    const CAT_SYSTEM = 'system';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Crear tabla si no existe
        $this->maybe_create_table();
    }
    
    /**
     * Crear tabla de logs si no existe
     */
    private function maybe_create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Verificar si la tabla ya existe
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
            return;
        }
        
        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            session_id VARCHAR(50) NOT NULL,
            post_id BIGINT(20) UNSIGNED NULL,
            keyword VARCHAR(255) NULL,
            type VARCHAR(20) NOT NULL DEFAULT 'info',
            category VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            data LONGTEXT NULL,
            duration FLOAT NULL,
            cost_usd DECIMAL(10,6) NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            INDEX session_id_idx (session_id),
            INDEX post_id_idx (post_id),
            INDEX type_idx (type),
            INDEX category_idx (category),
            INDEX created_at_idx (created_at)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Registrar log
     * 
     * @param string $session_id ID de sesión (para agrupar logs)
     * @param string $type Tipo de log (info, success, warning, error)
     * @param string $category Categoría (serp, scraping, generation, etc.)
     * @param string $message Mensaje descriptivo
     * @param array $data Datos adicionales (opcional)
     * @param int|null $post_id ID del post (opcional)
     * @param string|null $keyword Keyword (opcional)
     * @param float|null $duration Duración en segundos (opcional)
     * @param float|null $cost_usd Costo en USD (opcional)
     * @return int|false ID del log o false si error
     */
    public function log($session_id, $type, $category, $message, $data = null, $post_id = null, $keyword = null, $duration = null, $cost_usd = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $insert_data = [
            'session_id' => $session_id,
            'post_id' => $post_id,
            'keyword' => $keyword,
            'type' => $type,
            'category' => $category,
            'message' => $message,
            'data' => !empty($data) ? wp_json_encode($data) : null,
            'duration' => $duration,
            'cost_usd' => $cost_usd,
            'created_at' => current_time('mysql'),
        ];
        
        $result = $wpdb->insert($table_name, $insert_data);
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Helpers para tipos específicos
     */
    
    public function info($session_id, $category, $message, $data = null, $post_id = null, $keyword = null) {
        return $this->log($session_id, self::TYPE_INFO, $category, $message, $data, $post_id, $keyword);
    }
    
    public function success($session_id, $category, $message, $data = null, $post_id = null, $keyword = null, $duration = null, $cost_usd = null) {
        return $this->log($session_id, self::TYPE_SUCCESS, $category, $message, $data, $post_id, $keyword, $duration, $cost_usd);
    }
    
    public function warning($session_id, $category, $message, $data = null, $post_id = null, $keyword = null) {
        return $this->log($session_id, self::TYPE_WARNING, $category, $message, $data, $post_id, $keyword);
    }
    
    public function error($session_id, $category, $message, $data = null, $post_id = null, $keyword = null) {
        return $this->log($session_id, self::TYPE_ERROR, $category, $message, $data, $post_id, $keyword);
    }
    
    public function debug($session_id, $category, $message, $data = null, $post_id = null, $keyword = null) {
        return $this->log($session_id, self::TYPE_DEBUG, $category, $message, $data, $post_id, $keyword);
    }
    
    /**
     * Obtener logs por sesión
     * 
     * @param string $session_id ID de sesión
     * @param int $limit Límite de registros
     * @return array Logs
     */
    public function get_logs_by_session($session_id, $limit = 1000) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE session_id = %s ORDER BY id ASC LIMIT %d",
            $session_id,
            $limit
        );
        
        return $wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Obtener logs por post
     * 
     * @param int $post_id ID del post
     * @param int $limit Límite de registros
     * @return array Logs
     */
    public function get_logs_by_post($post_id, $limit = 1000) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE post_id = %d ORDER BY id ASC LIMIT %d",
            $post_id,
            $limit
        );
        
        return $wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Obtener logs recientes
     * 
     * @param int $limit Límite de registros
     * @param string|null $type Filtrar por tipo
     * @param string|null $category Filtrar por categoría
     * @return array Logs
     */
    public function get_recent_logs($limit = 100, $type = null, $category = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $where = [];
        if ($type) {
            $where[] = $wpdb->prepare("type = %s", $type);
        }
        if ($category) {
            $where[] = $wpdb->prepare("category = %s", $category);
        }
        
        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT * FROM {$table_name} {$where_clause} ORDER BY id DESC LIMIT " . absint($limit);
        
        return $wpdb->get_results($sql, ARRAY_A);
    }
    
    /**
     * Obtener estadísticas de una sesión
     * 
     * @param string $session_id ID de sesión
     * @return array Estadísticas
     */
    public function get_session_stats($session_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $sql = $wpdb->prepare(
            "SELECT 
                COUNT(*) as total_logs,
                SUM(CASE WHEN type = 'error' THEN 1 ELSE 0 END) as errors,
                SUM(CASE WHEN type = 'warning' THEN 1 ELSE 0 END) as warnings,
                SUM(CASE WHEN type = 'success' THEN 1 ELSE 0 END) as successes,
                SUM(duration) as total_duration,
                SUM(cost_usd) as total_cost,
                MIN(created_at) as started_at,
                MAX(created_at) as finished_at
            FROM {$table_name}
            WHERE session_id = %s",
            $session_id
        );
        
        return $wpdb->get_row($sql, ARRAY_A);
    }
    
    /**
     * Limpiar logs antiguos
     * 
     * @param int $days Días de antigüedad
     * @return int Número de registros eliminados
     */
    public function clean_old_logs($days = 30) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        
        $sql = $wpdb->prepare(
            "DELETE FROM {$table_name} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        );
        
        return $wpdb->query($sql);
    }
    
    /**
     * Generar ID de sesión único
     * 
     * @return string Session ID
     */
    public static function generate_session_id() {
        return uniqid('gen_', true);
    }
}



