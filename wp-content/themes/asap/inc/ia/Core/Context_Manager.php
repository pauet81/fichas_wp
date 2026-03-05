<?php
/**
 * Gestor de Contexto para Generación por Secciones
 * 
 * Mantiene el contexto acumulado durante la generación de artículos
 * por secciones para evitar repeticiones y mejorar coherencia.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Core
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Core_Context_Manager {
    
    /**
     * @var string Resumen del contexto acumulado
     */
    private $context_summary = '';
    
    /**
     * @var array Keywords secundarias ya usadas
     */
    private $used_secondary_keywords = [];
    
    /**
     * @var int Máximo de secciones a mantener en contexto
     */
    private $max_sections = 3;
    
    /**
     * @var int Máximo de caracteres por sección en el resumen
     */
    private $max_chars_per_section = 200;
    
    /**
     * Constructor
     * 
     * @param int $max_sections Máximo de secciones a mantener (default: 3)
     * @param int $max_chars_per_section Máximo de caracteres por sección (default: 200)
     */
    public function __construct($max_sections = 3, $max_chars_per_section = 200) {
        $this->max_sections = $max_sections;
        $this->max_chars_per_section = $max_chars_per_section;
    }
    
    /**
     * Agrega una sección al contexto
     * 
     * @param string $section_title Título de la sección (H2)
     * @param string $section_html HTML de la sección
     * @param array $secondary_keywords Lista completa de keywords secundarias (para detectar cuáles se usaron)
     * @return void
     */
    public function add_section($section_title, $section_html, $secondary_keywords = []) {
        // Extraer texto plano
        $section_text = wp_strip_all_tags($section_html);
        
        // Detectar keywords secundarias usadas en esta sección
        if (!empty($secondary_keywords)) {
            $section_lower = mb_strtolower($section_text);
            foreach ($secondary_keywords as $kw) {
                $kw_lower = mb_strtolower($kw);
                if (strpos($section_lower, $kw_lower) !== false) {
                    if (!in_array($kw, $this->used_secondary_keywords)) {
                        $this->used_secondary_keywords[] = $kw;
                    }
                }
            }
        }
        
        // Contar palabras
        $section_words = str_word_count($section_text);
        
        // Crear resumen de la sección
        $section_excerpt = substr($section_text, 0, $this->max_chars_per_section);
        
        // Agregar al contexto
        $this->context_summary .= sprintf(
            "Sección '%s' (%d palabras): %s... ",
            $section_title,
            $section_words,
            $section_excerpt
        );
        
        // Limitar contexto a las últimas N secciones
        $this->trim_context();
    }
    
    /**
     * Limita el contexto a las últimas N secciones
     * 
     * @return void
     */
    private function trim_context() {
        $context_parts = explode('Sección ', $this->context_summary);
        
        if (count($context_parts) > ($this->max_sections + 1)) {
            // Mantener solo las últimas N secciones
            $context_parts = array_slice($context_parts, -$this->max_sections);
            $this->context_summary = 'Sección ' . implode('Sección ', $context_parts);
        }
    }
    
    /**
     * Obtiene el resumen del contexto actual
     * 
     * @return string Resumen del contexto
     */
    public function get_summary() {
        return $this->context_summary;
    }
    
    /**
     * Reinicia el contexto
     * 
     * @return void
     */
    public function reset() {
        $this->context_summary = '';
    }
    
    /**
     * Verifica si hay contexto disponible
     * 
     * @return bool True si hay contexto, false si está vacío
     */
    public function has_context() {
        return !empty($this->context_summary);
    }
    
    /**
     * Agrega texto inicial al contexto (ej: "Introducción completada")
     * 
     * @param string $text Texto a agregar
     * @return void
     */
    public function add_note($text) {
        $this->context_summary .= $text . ' ';
    }
    
    /**
     * Obtiene las keywords secundarias ya usadas
     * 
     * @return array Keywords usadas
     */
    public function get_used_keywords() {
        return $this->used_secondary_keywords;
    }
    
    /**
     * Obtiene las keywords secundarias aún NO usadas
     * 
     * @param array $all_keywords Lista completa de keywords secundarias
     * @return array Keywords disponibles
     */
    public function get_available_keywords($all_keywords) {
        return array_diff($all_keywords, $this->used_secondary_keywords);
    }
    
    /**
     * Registra manualmente una keyword como usada (para intro/faqs/conclusión)
     * 
     * @param string $keyword Keyword a registrar
     * @return void
     */
    public function mark_keyword_used($keyword) {
        if (!in_array($keyword, $this->used_secondary_keywords)) {
            $this->used_secondary_keywords[] = $keyword;
        }
    }
}





