<?php
/**
 * Autoloader para clases de IA
 * 
 * Carga automáticamente las clases refactorizadas siguiendo PSR-4
 * 
 * @package ASAP_Theme
 * @subpackage IA
 */

if (!defined('ABSPATH')) exit;

spl_autoload_register(function ($class) {
    // Solo cargar clases que empiecen con ASAP_IA_
    if (strpos($class, 'ASAP_IA_') !== 0) {
        return;
    }
    
    // Remover el prefijo ASAP_IA_
    // Ejemplo: ASAP_IA_Core_OpenAI_Client -> Core_OpenAI_Client
    $class_name = str_replace('ASAP_IA_', '', $class);
    
    // Dividir por underscores
    // Ejemplo: Core_OpenAI_Client -> ['Core', 'OpenAI_Client']
    $parts = explode('_', $class_name);
    
    // El primer elemento es el directorio (Core, Generators, etc.)
    // El resto forma el nombre del archivo (OpenAI_Client)
    if (count($parts) === 1) {
        // Solo hay un segmento, es el archivo en la raíz
        $file_path = $parts[0];
    } else {
        // Primer elemento es el directorio
        $directory = array_shift($parts);
        // El resto es el nombre del archivo (con underscores preservados)
        $filename = implode('_', $parts);
        $file_path = $directory . '/' . $filename;
    }
    
    // Construir ruta completa
    $full_path = __DIR__ . '/' . $file_path . '.php';
    
    // Cargar el archivo si existe
    if (file_exists($full_path)) {
        require_once $full_path;
    }
});



