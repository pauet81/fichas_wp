<?php
/**
 * Cliente para la API de OpenAI
 * 
 * Maneja todas las comunicaciones con la API de OpenAI,
 * incluyendo chat completions, gestión de API keys y manejo de errores.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Core
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Core_OpenAI_Client {
    
    /**
     * Realiza una llamada a la API de OpenAI Chat Completions
     * 
     * @param string $api_key API key de OpenAI
     * @param string $model Modelo a usar (ej: 'gpt-4.1-mini')
     * @param float $temperature Temperatura (0-1)
     * @param string $system Mensaje de sistema
     * @param string $user_content Contenido del usuario
     * @param int $max_tokens Máximo de tokens de respuesta
     * @return array|WP_Error Array con 'content', 'usage', 'model' o WP_Error si falla
     */
    public function chat($api_key, $model, $temperature, $system, $user_content, $max_tokens = 1800) {
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $body = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user_content],
            ],
            'temperature' => max(0, min(1, floatval($temperature))),
            'max_tokens'  => intval($max_tokens),
        ];
        $args = [
            'headers' => [
                'Authorization' => 'Bearer '.$api_key,
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 20, // 20 seg para evitar timeouts del servidor
            'body'    => wp_json_encode($body),
        ];
        $res  = wp_remote_post($endpoint, $args);
        if ( is_wp_error($res) ) {
            $error_msg = $res->get_error_message();
            
            // Si es timeout, mensaje específico
            if (strpos($error_msg, 'timed out') !== false || strpos($error_msg, 'timeout') !== false) {
                return new WP_Error('openai_timeout', 'Timeout de OpenAI (> 20 seg). Se reintentará automáticamente.');
            }
            
            return new WP_Error('openai_connection_error', 'Error de conexión con OpenAI: ' . $error_msg);
        }
        
        $code = wp_remote_retrieve_response_code($res);
        $json = json_decode( wp_remote_retrieve_body($res), true );

        if ( 200 !== (int)$code || empty($json['choices'][0]['message']['content']) ) {
            $error_msg = ! empty($json['error']['message']) ? $json['error']['message'] : 'Error desconocido';
            
            // Mensajes personalizados según el código de error
            if ($code === 401) {
                $msg = 'API Key inválida. Verifica tu clave en <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI</a>.';
            } elseif ($code === 429) {
                $msg = 'Límite de tasa excedido. Verifica tu plan y uso en <a href="https://platform.openai.com/account/limits" target="_blank">OpenAI</a>. Error: ' . $error_msg;
            } elseif ($code === 400) {
                $msg = 'Solicitud inválida. ' . $error_msg;
            } elseif ($code === 500 || $code === 503) {
                $msg = 'OpenAI está experimentando problemas temporales. Intenta nuevamente en unos momentos. Error: ' . $error_msg;
            } else {
                $msg = 'Error de OpenAI (código ' . $code . '): ' . $error_msg;
            }
            
            return new WP_Error('openai_error', $msg);
        }
        
        // Retornar contenido y datos de uso
        $result = [
            'content' => $json['choices'][0]['message']['content'],
            'usage' => [
                'prompt_tokens' => isset($json['usage']['prompt_tokens']) ? $json['usage']['prompt_tokens'] : 0,
                'completion_tokens' => isset($json['usage']['completion_tokens']) ? $json['usage']['completion_tokens'] : 0,
                'total_tokens' => isset($json['usage']['total_tokens']) ? $json['usage']['total_tokens'] : 0,
            ],
            'model' => $model,
        ];
        
        return $result;
    }
    
    /**
     * Obtiene la API key de OpenAI desde diferentes fuentes
     * 
     * Orden de búsqueda:
     * 1. Filtro 'asap/ia/openai_api_key'
     * 2. Constante ASAP_OPENAI_API_KEY
     * 3. Constante OPENAI_API_KEY
     * 4. Variable de entorno OPENAI_API_KEY
     * 5. Opción de WordPress 'asap_ia_openai_api_key'
     * 
     * @return string API key o string vacío si no se encuentra
     */
    public function get_api_key() {
        $key = apply_filters('asap/ia/openai_api_key', '');
        if ( ! $key && defined('ASAP_OPENAI_API_KEY') ) $key = ASAP_OPENAI_API_KEY;
        if ( ! $key && defined('OPENAI_API_KEY') )      $key = OPENAI_API_KEY;
        if ( ! $key && getenv('OPENAI_API_KEY') )       $key = getenv('OPENAI_API_KEY');
        if ( ! $key )                                   $key = get_option('asap_ia_openai_api_key', '');
        return trim($key);
    }
}



