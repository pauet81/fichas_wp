<?php
/**
 * Generador de Outline (H2)
 * 
 * Sugiere títulos H2 para estructurar artículos usando IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Generators
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Generators_Outline_Generator {
    
    /**
     * @var ASAP_IA_Core_OpenAI_Client Cliente de OpenAI
     */
    private $openai_client;
    
    /**
     * @var ASAP_IA_Core_Token_Calculator Calculadora de tokens
     */
    private $token_calculator;
    
    /**
     * Constructor
     * 
     * @param ASAP_IA_Core_OpenAI_Client $openai_client Cliente de OpenAI
     * @param ASAP_IA_Core_Token_Calculator $token_calculator Calculadora de tokens
     */
    public function __construct($openai_client, $token_calculator) {
        $this->openai_client = $openai_client;
        $this->token_calculator = $token_calculator;
    }
    
    /**
     * Sugiere títulos H2 basándose en H1 y keyword
     * 
     * @param string $h1 Título principal
     * @param string $keyword Palabra clave
     * @param array $existing H2 ya existentes (para no repetir)
     * @return array|WP_Error Array con 'suggestions', 'cost', 'tokens' o WP_Error
     */
    public function suggest_h2($h1, $keyword = '', $existing = []) {
        if (empty($h1)) {
            return new WP_Error('missing_h1', 'Por favor ingresa el H1 antes de solicitar sugerencias.');
        }
        
        $api_key = $this->openai_client->get_api_key();
        if (empty($api_key)) {
            return new WP_Error('no_api_key', 'Falta configurar OpenAI API Key.');
        }
        
        $model = 'gpt-4.1-mini';
        $temperature = 0.7;
        
        $system = "Eres un editor SEO senior. Propón entre 6 y 9 H2 potentes y no redundantes para un artículo. Devuelve SOLO una lista en texto plano, un H2 por línea, sin numeración ni emojis. No repitas H2 ya existentes. MÁXIMO 9 H2.\n\n⚠️ FORMATO DE ENCABEZADOS:\n- En inglés: Usa sentence case (solo primera palabra en mayúscula), NO Title Case. Ejemplo: 'How to recover your ex' (NO 'How To Recover Your Ex')\n- En español: Primera palabra en mayúscula. Ejemplo: 'Cómo recuperar a tu ex'\n- En otros idiomas: Sigue las convenciones naturales del idioma.";
        $user = "H1: {$h1}\nPalabra clave principal: ".($keyword ?: '—')."\nH2 ya añadidos: ".( $existing ? implode(' | ', $existing) : 'ninguno' )."\n\nDevuelve solo los H2 propuestos (uno por línea).";
        
        $response = $this->openai_client->chat($api_key, $model, $temperature, $system, $user, 400);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        // Parsear respuesta
        $raw = $response['content'];
        $usage = $response['usage'];
        
        $lines = preg_split('/\r\n|\r|\n/', trim($raw));
        $suggestions = [];
        foreach ($lines as $line) {
            $line = trim(preg_replace('/^\s*[-*\d\.\)]\s*/', '', $line));
            if ($line !== '' && !in_array($line, $existing, true)) {
                $suggestions[] = $line;
            }
        }
        
        // ⭐ LIMITAR A MÁXIMO 9 H2s (por si la IA no respeta)
        if (count($suggestions) > 9) {
            $suggestions = array_slice($suggestions, 0, 9);
        }
        
        return [
            'suggestions' => $suggestions,
            'cost' => $this->token_calculator->calculate_cost($model, $usage['prompt_tokens'], $usage['completion_tokens']),
            'tokens' => $usage['total_tokens'],
            'usage' => $usage,
            'model' => $model
        ];
    }
    
    /**
     * Auto-genera H2 si el outline está vacío
     * 
     * @param string $h1 Título principal
     * @param string $keyword Palabra clave
     * @param int $target_len Longitud objetivo del artículo
     * @return array|WP_Error Array de H2 generados o WP_Error
     */
    public function auto_generate_h2($h1, $keyword = '', $target_len = 3000) {
        $api_key = $this->openai_client->get_api_key();
        if (empty($api_key)) {
            return new WP_Error('no_api_key', 'Falta configurar OpenAI API Key para auto-generar estructura.');
        }
        
        $model = 'gpt-4.1-mini';
        $system = "Eres un experto en SEO y estructura de contenido. Genera únicamente títulos H2 para estructurar un artículo completo y equilibrado.\n\n⚠️ FORMATO DE ENCABEZADOS:\n- En inglés: Usa sentence case (solo primera palabra en mayúscula), NO Title Case. Ejemplo: 'How to recover your ex' (NO 'How To Recover Your Ex')\n- En español: Primera palabra en mayúscula. Ejemplo: 'Cómo recuperar a tu ex'\n- En otros idiomas: Sigue las convenciones naturales del idioma.";
        
        $user = "Tema del artículo (H1): {$h1}\n";
        if ($keyword) $user .= "Palabra clave: {$keyword}\n";
        $user .= "Longitud objetivo: {$target_len} palabras\n\n";
        $user .= "Genera 4-6 títulos H2 que estructuren el artículo de forma lógica y completa. Devuelve SOLO los títulos, uno por línea, sin números, bullets ni explicaciones adicionales.";
        
        $response = $this->openai_client->chat($api_key, $model, 0.7, $system, $user, 500);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        // Parsear respuesta
        $raw = $response['content'];
        $lines = preg_split('/\r\n|\r|\n/', trim($raw));
        $h2_list = [];
        foreach ($lines as $line) {
            $line = trim(preg_replace('/^\s*[-*\d\.\)]\s*/', '', $line));
            if (!empty($line)) {
                $h2_list[] = ['h2' => $line, 'h3' => []];
            }
        }
        
        if (empty($h2_list)) {
            return new WP_Error('empty_generation', 'No se pudo auto-generar la estructura.');
        }
        
        return [
            'outline' => $h2_list,
            'cost' => $this->token_calculator->calculate_cost($model, $response['usage']['prompt_tokens'], $response['usage']['completion_tokens']),
            'tokens' => $response['usage']['total_tokens'],
            'usage' => $response['usage'],
            'model' => $model
        ];
    }
}





