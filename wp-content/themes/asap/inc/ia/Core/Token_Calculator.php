<?php
/**
 * Calculadora de Tokens y Costos
 * 
 * Estima tokens y calcula costos de la API de OpenAI
 * basándose en los precios actualizados.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Core
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Core_Token_Calculator {
    
    /**
     * Estima la cantidad de tokens en un texto
     * 
     * Nota: Esta es una aproximación. OpenAI usa tiktoken que es más preciso.
     * Aproximación: 1 token ≈ 4 caracteres para inglés/español
     * 
     * @param string $text Texto a estimar
     * @return int Cantidad estimada de tokens
     */
    public function estimate_tokens($text) {
        // Estimación aproximada: 1 token ≈ 4 caracteres para inglés/español
        return (int) ceil(strlen($text) / 4);
    }
    
    /**
     * Calcula el costo de una llamada a OpenAI o Gemini
     * 
     * @param string $model Modelo usado
     * @param int $tokens_input Tokens de entrada (prompt)
     * @param int $tokens_output Tokens de salida (completion)
     * @return float Costo en USD
     */
    public function calculate_cost($model, $tokens_input, $tokens_output) {
        // Precios actualizados (2025)
        // OpenAI: https://openai.com/pricing
        // Gemini: https://ai.google.dev/pricing
        $prices = [
            // OpenAI GPT-4 familia
            'gpt-4o' => [
                'input' => 2.50 / 1000000,   // $2.50 per 1M tokens
                'output' => 10.00 / 1000000, // $10.00 per 1M tokens
            ],
            'gpt-4o-mini' => [
                'input' => 0.150 / 1000000,  // $0.150 per 1M tokens
                'output' => 0.600 / 1000000, // $0.600 per 1M tokens
            ],
            'gpt-4-turbo' => [
                'input' => 10.00 / 1000000,
                'output' => 30.00 / 1000000,
            ],
            'gpt-4.1-mini' => [
                'input' => 0.150 / 1000000,
                'output' => 0.600 / 1000000,
            ],
            'gpt-4.1-nano' => [
                'input' => 0.100 / 1000000,
                'output' => 0.400 / 1000000,
            ],
            'gpt-4' => [
                'input' => 30.00 / 1000000,
                'output' => 60.00 / 1000000,
            ],
            'o1-mini' => [
                'input' => 3.00 / 1000000,
                'output' => 12.00 / 1000000,
            ],
            'o1' => [
                'input' => 15.00 / 1000000,
                'output' => 60.00 / 1000000,
            ],
            
            // Google Gemini familia
            'gemini-2.5-flash' => [
                'input' => 0.075 / 1000000,  // $0.075 per 1M tokens
                'output' => 0.30 / 1000000,  // $0.30 per 1M tokens
            ],
            'gemini-2.5-flash-lite' => [
                'input' => 0.075 / 1000000,  // Mismo precio que flash
                'output' => 0.30 / 1000000,
            ],
            'gemini-2.5-pro' => [
                'input' => 1.25 / 1000000,   // $1.25 per 1M tokens
                'output' => 5.00 / 1000000,  // $5.00 per 1M tokens
            ],
            'gemini-2.0-flash-exp' => [
                'input' => 0.075 / 1000000,
                'output' => 0.30 / 1000000,
            ],
            
            // Imágenes
            'dall-e-3' => [
                'standard_1024' => 0.040,
                'standard_1792' => 0.080,
                'hd_1024' => 0.080,
                'hd_1792' => 0.120,
            ],
        ];
        
        if (!isset($prices[$model])) {
            // Si el modelo no está en la lista, devolver 0
            return 0.0;
        }
        
        $price_data = $prices[$model];
        
        // Si es un modelo de imágenes, devolver precio fijo
        if ($model === 'dall-e-3') {
            return 0.0; // El costo de imágenes se calcula aparte
        }
        
        $cost = ($tokens_input * $price_data['input']) + ($tokens_output * $price_data['output']);
        
        return round($cost, 6);
    }
    
    /**
     * Obtiene los precios de todos los modelos
     * 
     * @return array Array asociativo con precios por modelo
     */
    public function get_prices() {
        return [
            'gpt-4.1-mini' => [
                'input' => 0.000150 / 1000,
                'output' => 0.000600 / 1000,
            ],
            'gpt-4' => [
                'input' => 0.03 / 1000,
                'output' => 0.06 / 1000,
            ],
            'gpt-4-turbo' => [
                'input' => 0.01 / 1000,
                'output' => 0.03 / 1000,
            ],
        ];
    }
}





