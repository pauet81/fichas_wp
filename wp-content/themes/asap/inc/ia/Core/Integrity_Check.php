<?php
/**
 * ASAP IA - File Integrity Checker
 * Verifies system file integrity
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Core_Integrity_Check {
    
    /**
     * Cargar configuración
     */
    private static function get_config() {
        $config_file = get_template_directory() . '/inc/ia/config.php';
        if (file_exists($config_file)) {
            return require $config_file;
        }
        
        // Valores por defecto
        return [
            'critical_files' => [
                'inc/ia/Core/Remote_Validator.php',
                'inc/ia/Generators/Article_Generator.php',
                'inc/ia/Generators/Image_Generator.php',
                'inc/ia/Generators/Meta_Generator.php',
            ],
            'random_check_probability' => 20,
        ];
    }
    
    /**
     * ✅ Verificación de integridad de archivos críticos
     * Detecta si alguien modificó el código
     * 
     * @param string|null $session_id ID de sesión para logging
     * @return bool true si integridad OK, false si comprometido
     */
    public static function verify_files($session_id = null) {
        $config = self::get_config();
        $critical_files = $config['critical_files'];
        
        $checksums = get_option('asap_file_' . 'checksums', []);
        
        // Primera ejecución: guardar checksums
        if (empty($checksums)) {
            self::save_checksums($critical_files);
            self::log('info', '✅ Checksums de archivos inicializados', $session_id);
            return true;
        }
        
        // Verificar checksums
        foreach ($critical_files as $file) {
            $path = get_template_directory() . '/' . $file;
            
            if (!file_exists($path)) {
                continue;
            }
            
            $current = md5_file($path);
            $stored = $checksums[$file] ?? '';
            
            // ⚠️ Archivo modificado
            if ($stored && $current !== $stored) {
                // Log de seguridad
                self::log('error', "⚠️ ALERTA DE SEGURIDAD: Archivo modificado detectado: {$file}", $session_id);
                
                // Fallo silencioso: no mostrar error obvio
                self::trigger_silent_failure();
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Guardar checksums de archivos
     */
    private static function save_checksums($files) {
        $checksums = [];
        
        foreach ($files as $file) {
            $path = get_template_directory() . '/' . $file;
            
            if (file_exists($path)) {
                $checksums[$file] = md5_file($path);
            }
        }
        
        update_option('asap_file_' . 'checksums', $checksums);
    }
    
    /**
     * ⚠️ Fallo silencioso
     * No muestra error obvio, simplemente no funciona
     */
    private static function trigger_silent_failure() {
        // Invalidar cache de validación
        delete_transient('asap_remote_' . 'status');
        
        // Marcar como comprometido (sin mensaje obvio)
        set_transient('asap_sys_' . 'check', 'fail', 86400);
    }
    
    /**
     * Verificar si el sistema está comprometido
     */
    public static function is_compromised() {
        return get_transient('asap_sys_' . 'check') === 'fail';
    }
    
    /**
     * ✅ Verificación aleatoria en runtime
     * Se ejecuta aleatoriamente durante la generación
     * 
     * @param string|null $session_id ID de sesión para logging
     * @return bool true si OK, false si falla
     */
    public static function random_check($session_id = null) {
        $config = self::get_config();
        $probability = $config['random_check_probability'];
        
        // Verificar según probabilidad configurada
        if (rand(1, 100) > $probability) {
            return true;
        }
        
        self::log('info', '🔄 Verificación aleatoria de licencia iniciada', $session_id);
        
        $validator = ASAP_IA_Core_Remote_Validator::get_instance();
        $check = $validator->verify(true, $session_id); // Forzar verificación remota
        
        if (is_wp_error($check)) {
            self::log('error', '❌ Verificación aleatoria falló: ' . $check->get_error_message(), $session_id);
            return false;
        }
        
        self::log('success', '✅ Verificación aleatoria exitosa', $session_id);
        return true;
    }
    
    /**
     * Logger helper
     */
    private static function log($level, $message, $session_id = null) {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $sid = $session_id ?: 'integrity_' . time();
        
        if ($level === 'error') {
            $logger->error($sid, 'security', $message);
        } elseif ($level === 'success') {
            $logger->success($sid, 'security', $message);
        } else {
            $logger->info($sid, 'security', $message);
        }
    }
}

