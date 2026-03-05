<?php
/**
 * ASAP IA - Remote Configuration Validator
 * Validates remote configuration settings
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Core_Remote_Validator {
    
    private static $instance = null;
    private $logger;
    private static $config = null;
    
    /**
     * Cargar configuración
     */
    private static function get_config() {
        if (self::$config === null) {
            $config_file = get_template_directory() . '/inc/ia/config.php';
            if (file_exists($config_file)) {
                self::$config = require $config_file;
            } else {
                // Valores por defecto si no existe config
                self::$config = [
                    'edd_item_ids' => [12345, 12346],
                    'license_cache_ttl' => 604800,
                    'edd_base_url' => 'https://asaptheme.com',
                ];
            }
        }
        return self::$config;
    }
    
    // Ofuscado: slug del theme
    private static function get_slug() {
        return get_template(); // 'asap'
    }
    
    // Ofuscado: IDs de productos en EDD
    private static function get_item_ids() {
        $config = self::get_config();
        return $config['edd_item_ids'];
    }
    
    // Ofuscado: Duración del cache
    private static function get_ttl() {
        $config = self::get_config();
        return $config['license_cache_ttl'];
    }
    
    // Ofuscado: URL base de EDD
    private static function get_base_url() {
        $config = self::get_config();
        return $config['edd_base_url'];
    }
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->logger = new ASAP_IA_Database_Generation_Logger();
    }
    
    /**
     * ✅ Verificación principal (usa la licencia del theme)
     * 
     * @param bool $force Forzar verificación (ignorar cache)
     * @param string|null $session_id ID de sesión para logging
     * @return bool|WP_Error true si válida, WP_Error si no
     */
    public function verify($force = false, $session_id = null) {
        $config = self::get_config();
        
        // ⚠️ SI LA VALIDACIÓN ESTÁ DESHABILITADA, SIEMPRE RETORNAR TRUE
        if (isset($config['validate_license']) && $config['validate_license'] === false) {
            return true; // Sin log
        }
        
        $slug = self::get_slug();
        
        // Obtener licencia del theme (ya configurada en asap-license)
        $license_key = trim(get_option($slug . '_license_key', ''));
        $license_status = get_option($slug . '_license_key_status', false);
        
        // Si no hay licencia configurada
        if (empty($license_key)) {
            $this->log('error', '❌ Licencia no configurada. El usuario debe activar su licencia en admin.php?page=asap-license', $session_id);
            return $this->error('no_license');
        }
        
        // Cache check (si no se fuerza)
        if (!$force) {
            $cached = get_transient('asap_ia_' . 'validation');
            if ($cached === 'ok' && $license_status === 'valid') {
                $this->log('info', '✅ Licencia válida (desde cache)', $session_id);
                return true;
            }
        }
        
        // Verificar estado de licencia del theme
        if ($license_status === 'valid') {
            set_transient('asap_ia_' . 'validation', 'ok', self::get_ttl());
            $this->log('success', '✅ Licencia verificada correctamente. Estado: VALID', $session_id);
            return true;
        }
        
        // Si no es válida, retornar error según el estado
        $this->log('error', "❌ Licencia inválida. Estado: " . strtoupper($license_status ?: 'INVALID'), $session_id);
        return $this->error($license_status ?: 'invalid');
    }
    
    /**
     * Obtener información de la licencia del theme
     */
    public function get_info() {
        $slug = self::get_slug();
        $license_key = get_option($slug . '_license_key', '');
        $license_status = get_option($slug . '_license_key_status', '');
        
        if (empty($license_key)) {
            return [];
        }
        
        return [
            'status' => $license_status,
            'key' => substr($license_key, 0, 10) . '...',
            'checked' => time(),
        ];
    }
    
    /**
     * Error handler
     */
    private function error($code) {
        delete_transient('asap_ia_' . 'validation');
        
        $license_url = admin_url('admin.php?page=asap-license');
        
        $messages = [
            'no_license' => '⚠️ No has activado tu licencia de Asap Theme. <a href="' . $license_url . '"><strong>Activar licencia →</strong></a>',
            'expired' => '⚠️ Tu licencia de Asap Theme ha <strong>expirado</strong>. <a href="' . $license_url . '">Renovar →</a>',
            'disabled' => '⚠️ Tu licencia de Asap Theme está <strong>deshabilitada</strong>. <a href="https://asaptheme.com/contacto" target="_blank">Contactar soporte →</a>',
            'invalid' => '⚠️ Tu licencia de Asap Theme no es válida. <a href="' . $license_url . '">Verificar →</a>',
            'inactive' => '⚠️ Tu licencia de Asap Theme está inactiva. <a href="' . $license_url . '">Activar →</a>',
            'site_inactive' => '⚠️ Tu licencia de Asap Theme no está activa para este sitio. <a href="' . $license_url . '">Activar →</a>',
            'deactivated' => '⚠️ Tu licencia de Asap Theme fue desactivada. <a href="' . $license_url . '">Reactivar →</a>',
        ];
        
        $msg = $messages[$code] ?? $messages['invalid'];
        $this->log('warning', "License validation failed: {$code}");
        
        return new WP_Error('license_invalid', $msg);
    }
    
    /**
     * Logger
     */
    private function log($level, $message, $session_id = null) {
        $sid = $session_id ?: 'license_' . time();
        
        if ($level === 'error' || $level === 'warning') {
            $this->logger->error($sid, 'license', $message);
        } elseif ($level === 'success') {
            $this->logger->success($sid, 'license', $message);
        } else {
            $this->logger->info($sid, 'license', $message);
        }
    }
}

