<?php
/**
 * UI Helper
 * 
 * Funciones auxiliares para la interfaz de administración.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Helpers
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Helpers_UI_Helper {
    
    /**
     * Muestra un mensaje de éxito en el admin
     * 
     * @param string $msg Mensaje a mostrar
     */
    public static function admin_notice_success($msg) {
        echo '<div class="notice notice-success is-dismissible"><p>'. esc_html($msg) .'</p></div>';
    }
    
    /**
     * Muestra un mensaje de error en el admin
     * 
     * @param string $msg Mensaje a mostrar
     */
    public static function admin_notice_error($msg) {
        echo '<div class="notice notice-error is-dismissible"><p>'. esc_html($msg) .'</p></div>';
    }
    
    /**
     * Muestra un mensaje de advertencia en el admin
     * 
     * @param string $msg Mensaje a mostrar
     */
    public static function admin_notice_warning($msg) {
        echo '<div class="notice notice-warning is-dismissible"><p>'. esc_html($msg) .'</p></div>';
    }
    
    /**
     * Muestra un mensaje informativo en el admin
     * 
     * @param string $msg Mensaje a mostrar
     */
    public static function admin_notice_info($msg) {
        echo '<div class="notice notice-info is-dismissible"><p>'. esc_html($msg) .'</p></div>';
    }
    
}



