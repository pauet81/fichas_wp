<?php
/**
 * ASAP IA - Configuración centralizada
 * 
 */

if (!defined('ABSPATH')) exit;

return [
    
    /**
     * ═══════════════════════════════════════════════════════════════
     * 🔧 CONFIGURACIÓN DE LICENCIA EDD
     * ═══════════════════════════════════════════════════════════════
     */
    
    'validate_license' => false,
    
    'edd_item_ids' => [
        460,
        18339,
    ],
    'license_cache_ttl' => 604800,
    'edd_base_url' => 'https://asaptheme.com',
    
    
    /**
     * ═══════════════════════════════════════════════════════════════
     * 🔒 CONFIGURACIÓN DE SEGURIDAD
     * ═══════════════════════════════════════════════════════════════
     */
    
    // Probabilidad de verificación aleatoria durante generación (%)
    // 20 = 20% de probabilidad
    'random_check_probability' => 20,
    
    // Archivos críticos para verificación de integridad
    'critical_files' => [
        'inc/ia/Core/Remote_Validator.php',
        'inc/ia/Generators/Article_Generator.php',
        'inc/ia/Generators/Image_Generator.php',
        'inc/ia/Generators/Meta_Generator.php',
        'inc/ia/Core/Integrity_Check.php',
    ],
    
    
    /**
     * ═══════════════════════════════════════════════════════════════
     * 📊 CONFIGURACIÓN DE LOGGING
     * ═══════════════════════════════════════════════════════════════
     */
    
    // Habilitar logging detallado de validación de licencia
    'enable_license_logging' => true,
    
    // Habilitar logging de verificaciones de integridad
    'enable_security_logging' => true,
    
];

