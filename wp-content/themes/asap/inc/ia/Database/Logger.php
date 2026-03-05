<?php
/**
 * Gestor de Logs de IA
 * 
 * Maneja el registro de todas las operaciones de IA (generación, tokens, costos).
 * 
 * @package ASAP_Theme
 * @subpackage IA\Database
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Database_Logger {
    
    /**
     * @var string Nombre de la tabla de logs
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'asap_ia_logs';
    }
    
    /**
     * Crea la tabla de logs si no existe
     */
    public function maybe_create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Verificar si ya existe
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") == $this->table_name) {
            return;
        }
        
        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            type varchar(50) NOT NULL,
            action varchar(100) NOT NULL,
            post_id bigint(20) DEFAULT NULL,
            model varchar(100) DEFAULT NULL,
            tokens_input int(11) DEFAULT 0,
            tokens_output int(11) DEFAULT 0,
            tokens_total int(11) DEFAULT 0,
            cost_usd decimal(10,6) DEFAULT 0.000000,
            status varchar(20) DEFAULT 'success',
            error_message text DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY type (type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Marcar que la tabla ya fue creada
        update_option('asap_ia_logs_table_version', '1.0');
    }
    
    /**
     * Registra una operación de generación
     * 
     * @param array $data Datos del log
     * @return int|false ID del log insertado o false si falla
     */
    public function log($data) {
        global $wpdb;
        
        $defaults = [
            'user_id' => get_current_user_id(),
            'type' => 'unknown',
            'action' => '',
            'post_id' => null,
            'model' => 'gpt-4.1-mini',
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_total' => 0,
            'cost_usd' => 0.0,
            'status' => 'success',
            'error_message' => null,
            'metadata' => null,
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        // Serializar metadata si es array
        if (is_array($data['metadata'])) {
            $data['metadata'] = wp_json_encode($data['metadata']);
        }
        
        $wpdb->insert($this->table_name, $data);
        
        return $wpdb->insert_id;
    }
    
    /**
     * Obtiene estadísticas de uso
     * 
     * @param array $filters Filtros opcionales ['type', 'user_id', 'date_from', 'date_to']
     * @return array Estadísticas
     */
    public function get_stats($filters = []) {
        global $wpdb;
        
        $where = ['1=1'];
        
        if (!empty($filters['type'])) {
            $where[] = $wpdb->prepare("type = %s", $filters['type']);
        }
        
        if (!empty($filters['user_id'])) {
            $where[] = $wpdb->prepare("user_id = %d", $filters['user_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = $wpdb->prepare("created_at >= %s", $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = $wpdb->prepare("created_at <= %s", $filters['date_to']);
        }
        
        $where_clause = implode(' AND ', $where);
        
        $stats = $wpdb->get_row("
            SELECT 
                COUNT(*) as total_operations,
                SUM(tokens_total) as total_tokens,
                SUM(cost_usd) as total_cost,
                AVG(tokens_total) as avg_tokens,
                AVG(cost_usd) as avg_cost
            FROM {$this->table_name}
            WHERE {$where_clause} AND status = 'success'
        ", ARRAY_A);
        
        return [
            'total_operations' => intval($stats['total_operations'] ?? 0),
            'total_tokens' => intval($stats['total_tokens'] ?? 0),
            'total_cost' => floatval($stats['total_cost'] ?? 0),
            'avg_tokens' => floatval($stats['avg_tokens'] ?? 0),
            'avg_cost' => floatval($stats['avg_cost'] ?? 0),
        ];
    }
    
    /**
     * Obtiene logs recientes
     * 
     * @param int $limit Número de logs a obtener
     * @param array $filters Filtros opcionales
     * @return array Lista de logs
     */
    public function get_recent($limit = 20, $filters = []) {
        global $wpdb;
        
        $where = ['1=1'];
        
        if (!empty($filters['type'])) {
            $where[] = $wpdb->prepare("type = %s", $filters['type']);
        }
        
        if (!empty($filters['user_id'])) {
            $where[] = $wpdb->prepare("user_id = %d", $filters['user_id']);
        }
        
        if (!empty($filters['status'])) {
            $where[] = $wpdb->prepare("status = %s", $filters['status']);
        }
        
        $where_clause = implode(' AND ', $where);
        
        $logs = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$this->table_name}
            WHERE {$where_clause}
            ORDER BY created_at DESC
            LIMIT %d
        ", $limit), ARRAY_A);
        
        // Deserializar metadata
        foreach ($logs as &$log) {
            if (!empty($log['metadata'])) {
                $log['metadata'] = json_decode($log['metadata'], true);
            }
        }
        
        return $logs;
    }
    
    /**
     * Limpia logs antiguos
     * 
     * @param int $days Número de días a mantener
     * @return int Número de registros eliminados
     */
    public function cleanup_old($days = 90) {
        global $wpdb;
        
        $deleted = $wpdb->query($wpdb->prepare("
            DELETE FROM {$this->table_name}
            WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)
        ", $days));
        
        return $deleted;
    }
    
    /**
     * Verifica si la tabla existe
     * 
     * @return bool
     */
    public function table_exists() {
        global $wpdb;
        return $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") == $this->table_name;
    }
}





