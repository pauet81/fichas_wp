<?php
/**
 * Utilities Helper
 * 
 * Funciones auxiliares generales (slugify, tokens, costos, iconos).
 * 
 * @package ASAP_Theme
 * @subpackage IA\Helpers
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Helpers_Utils {
    
    /**
     * Convertir texto a slug para nombres de archivo
     */
    public static function slugify_filename($text) {
        $text = remove_accents($text);
        $text = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $text));
        $text = trim($text, '-');
        return $text ?: 'image';
    }
    
    /**
     * Asegurar extensión correcta en nombre de archivo
     */
    public static function ensure_ext($filename, $format) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext !== $format) {
            $filename .= '.' . $format;
        }
        return $filename;
    }
    
    /**
     * Estimar tokens de un texto
     */
    public static function estimate_tokens($text) {
        // Aproximación: ~4 caracteres por token en español
        $chars = mb_strlen($text);
        return (int)ceil($chars / 4);
    }
    
    /**
     * Calcular costo según modelo y tokens
     */
    public static function calculate_cost($model, $tokens_input, $tokens_output) {
        // Precios por millón de tokens (actualizado 2024)
        $prices = [
            'gpt-4o' => ['input' => 5.00, 'output' => 15.00],
            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
            'gpt-4' => ['input' => 30.00, 'output' => 60.00],
            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
            'gpt-4.1-mini' => ['input' => 0.15, 'output' => 0.60],
        ];
        
        if (!isset($prices[$model])) {
            // Default a gpt-4o-mini si el modelo no está en la lista
            $model = 'gpt-4o-mini';
        }
        
        $cost_input = ($tokens_input / 1000000) * $prices[$model]['input'];
        $cost_output = ($tokens_output / 1000000) * $prices[$model]['output'];
        
        return $cost_input + $cost_output;
    }
    
    /**
     * Iconos SVG para el menú
     */
    public static function icon_pen() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>';
    }
    
    public static function icon_settings() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6m6-6h-6m6 0h6m-6 0a6 6 0 0 1-6 6m0 0a6 6 0 0 1-6-6m0 0h6m0 0V7m0 0a6 6 0 0 1 6 6"/></svg>';
    }
    
    public static function icon_meta() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h12"/></svg>';
    }
    
    public static function icon_image() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>';
    }
    
    public static function icon_queue() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>';
    }
    
    public static function icon_dashboard() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>';
    }
}




