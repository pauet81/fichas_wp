<?php
/**
 * Cliente para la API de Google Gemini
 * 
 * Maneja todas las comunicaciones con la API de Google Gemini,
 * incluyendo chat completions, gestión de API keys y manejo de errores.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Core
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Core_Gemini_Client {
    
    /**
     * Modelos disponibles de Gemini
     */
    const MODELS = [
        'gemini-2.5-flash' => 'Gemini 2.5 Flash',
        'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash Lite',
        'gemini-2.5-pro' => 'Gemini 2.5 Pro',
        'gemini-2.0-flash-exp' => 'Gemini 2.0 Flash (Experimental)',
    ];
    
    /**
     * Realiza una llamada a la API de Google Gemini
     * 
     * @param string $api_key API key de Google Gemini
     * @param string $model Modelo a usar (ej: 'gemini-1.5-flash')
     * @param float $temperature Temperatura (0-2 para Gemini)
     * @param string $system Instrucciones del sistema
     * @param string $user_content Contenido del usuario
     * @param int $max_tokens Máximo de tokens de respuesta
     * @return array|WP_Error Array con 'content', 'usage', 'model' o WP_Error si falla
     */
    public function chat($api_key, $model, $temperature, $system, $user_content, $max_tokens = 1800) {
        // Endpoint de Gemini
        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $api_key
        );
        
        // Combinar system y user en un solo prompt (Gemini no tiene role system separado)
        $combined_prompt = $system . "\n\n" . $user_content;
        
        // Construir body según formato de Gemini
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $combined_prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => max(0, min(2, floatval($temperature))),
                'maxOutputTokens' => intval($max_tokens),
            ]
        ];
        
        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 29,
            'body' => wp_json_encode($body),
        ];
        
        $res = wp_remote_post($endpoint, $args);
        
        if (is_wp_error($res)) {
            $error_msg = $res->get_error_message();
            
            if (strpos($error_msg, 'timed out') !== false || strpos($error_msg, 'timeout') !== false) {
                return new WP_Error('gemini_timeout', 'Timeout de Gemini (> 30 seg). Se reintentará automáticamente.');
            }
            
            return new WP_Error('gemini_connection_error', 'Error de conexión con Gemini: ' . $error_msg);
        }
        
        $code = wp_remote_retrieve_response_code($res);
        $json = json_decode(wp_remote_retrieve_body($res), true);
        
        // Verificar respuesta exitosa
        if (200 !== (int)$code || empty($json['candidates'][0]['content']['parts'][0]['text'])) {
            $error_msg = !empty($json['error']['message']) ? $json['error']['message'] : 'Error desconocido';
            
            // Mensajes personalizados según el código de error
            if ($code === 400) {
                $msg = 'Solicitud inválida a Gemini. ' . $error_msg;
            } elseif ($code === 403) {
                $msg = 'API Key inválida o sin permisos. Verifica tu clave en <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>.';
            } elseif ($code === 429) {
                $msg = 'Límite de tasa excedido en Gemini. Verifica tu cuota. Error: ' . $error_msg;
            } elseif ($code === 500 || $code === 503) {
                $msg = 'Gemini está experimentando problemas temporales. Intenta nuevamente en unos momentos. Error: ' . $error_msg;
            } else {
                $msg = 'Error de Gemini (código ' . $code . '): ' . $error_msg;
            }
            
            return new WP_Error('gemini_error', $msg);
        }
        
        // Extraer contenido
        $content = $json['candidates'][0]['content']['parts'][0]['text'];
        
        // Extraer información de tokens si está disponible
        $prompt_tokens = isset($json['usageMetadata']['promptTokenCount']) ? $json['usageMetadata']['promptTokenCount'] : 0;
        $completion_tokens = isset($json['usageMetadata']['candidatesTokenCount']) ? $json['usageMetadata']['candidatesTokenCount'] : 0;
        $total_tokens = isset($json['usageMetadata']['totalTokenCount']) ? $json['usageMetadata']['totalTokenCount'] : 0;
        
        // Retornar en formato compatible con OpenAI
        $result = [
            'content' => $content,
            'usage' => [
                'prompt_tokens' => $prompt_tokens,
                'completion_tokens' => $completion_tokens,
                'total_tokens' => $total_tokens,
            ],
            'model' => $model,
        ];
        
        return $result;
    }
    
    /**
     * Obtiene la API key de Gemini desde diferentes fuentes
     * 
     * Orden de búsqueda:
     * 1. Filtro 'asap/ia/gemini_api_key'
     * 2. Constante ASAP_GEMINI_API_KEY
     * 3. Constante GEMINI_API_KEY
     * 4. Variable de entorno GEMINI_API_KEY
     * 5. Opción de WordPress 'asap_ia_gemini_api_key'
     * 
     * @return string API key o string vacío si no se encuentra
     */
    public function get_api_key() {
        $key = apply_filters('asap/ia/gemini_api_key', '');
        if (!$key && defined('ASAP_GEMINI_API_KEY')) $key = ASAP_GEMINI_API_KEY;
        if (!$key && defined('GEMINI_API_KEY')) $key = GEMINI_API_KEY;
        if (!$key && getenv('GEMINI_API_KEY')) $key = getenv('GEMINI_API_KEY');
        if (!$key) $key = get_option('asap_ia_gemini_api_key', '');
        return trim($key);
    }
    
    /**
     * Obtiene el modelo configurado o el por defecto
     * 
     * @return string Nombre del modelo
     */
    public function get_model() {
        $model = get_option('asap_ia_gemini_model', 'gemini-2.5-flash-lite');
        return $model;
    }
}

