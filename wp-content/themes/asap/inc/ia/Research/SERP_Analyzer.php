<?php
/**
 * SERP Analyzer
 * 
 * Analiza resultados de búsqueda de Google usando ValueSERP API.
 * Extrae: top 10 URLs, People Also Ask, Related Searches, Featured Snippets.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Research
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Research_SERP_Analyzer {
    
    /**
     * @var string API key de ValueSERP
     */
    private $api_key;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = get_option('asap_ia_valueserp_api_key', '');
    }
    
    /**
     * Analiza SERPs para una keyword
     * 
     * @param string $keyword Palabra clave a analizar
     * @param array $options Opciones: location, language, num_results
     * @return array|WP_Error Datos de SERP o error
     */
    public function analyze($keyword, $options = []) {
        if (empty($keyword)) {
            return new WP_Error('missing_keyword', 'Keyword es obligatoria.');
        }
        
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Configura ValueSERP API Key en Configuración.');
        }
        
        // Configuración
        $defaults = [
            'location' => 'Argentina',
            'language' => 'es',
            'num_results' => 10,
            'include_paa' => true,
            'include_related' => true,
        ];
        $options = wp_parse_args($options, $defaults);
        
        // Construir URL de ValueSERP
        $params = [
            'api_key' => $this->api_key,
            'q' => $keyword,
            'location' => $options['location'],
            'google_domain' => 'google.com.ar', // Ajustar según location
            'gl' => 'ar', // Country code
            'hl' => $options['language'],
            'num' => $options['num_results'],
            'include_html' => 'false', // No necesitamos HTML completo
        ];
        
        $url = 'https://api.valueserp.com/search?' . http_build_query($params);
        
        // Hacer request con timeout generoso (ValueSERP hace búsquedas reales)
        $response = wp_remote_get($url, [
            'timeout' => 25, // 25 seg - ValueSERP puede tardar en responder
            'sslverify' => false, // Evitar problemas SSL en algunos servers
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        
        if (is_wp_error($response)) {
            $error_msg = $response->get_error_message();
            
            // Error específico para timeout
            if (strpos($error_msg, 'cURL error 28') !== false) {
                return new WP_Error('serp_timeout', 'La búsqueda en ValueSERP tardó demasiado (>25s). Intenta de nuevo o verifica tu conexión.');
            }
            
            return new WP_Error('serp_error', 'Error al conectar con ValueSERP: ' . $error_msg);
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($code !== 200) {
            $error_msg = isset($data['error']) ? $data['error'] : 'Error desconocido';
            return new WP_Error('serp_api_error', 'ValueSERP error: ' . $error_msg);
        }
        
        // Procesar y estructurar datos
        return $this->process_serp_data($data, $keyword);
    }
    
    /**
     * Procesa datos crudos de ValueSERP
     * 
     * @param array $data Datos de la API
     * @param string $keyword Keyword original
     * @return array Datos estructurados
     */
    private function process_serp_data($data, $keyword) {
        $processed = [
            'keyword' => $keyword,
            'total_results' => $data['search_information']['total_results'] ?? 0,
            'organic_results' => [],
            'people_also_ask' => [],
            'related_searches' => [],
            'featured_snippet' => null,
            'knowledge_graph' => null,
        ];
        
        // Procesar resultados orgánicos (top 10)
        if (isset($data['organic_results']) && is_array($data['organic_results'])) {
            foreach ($data['organic_results'] as $result) {
                $processed['organic_results'][] = [
                    'position' => $result['position'] ?? 0,
                    'title' => $result['title'] ?? '',
                    'url' => $result['link'] ?? '',
                    'domain' => $result['domain'] ?? '',
                    'description' => $result['snippet'] ?? '',
                    'date' => $result['date'] ?? null,
                ];
            }
        }
        
        // People Also Ask
        if (isset($data['related_questions']) && is_array($data['related_questions'])) {
            foreach ($data['related_questions'] as $paa) {
                $processed['people_also_ask'][] = [
                    'question' => $paa['question'] ?? '',
                    'snippet' => $paa['snippet'] ?? '',
                    'title' => $paa['title'] ?? '',
                    'link' => $paa['link'] ?? '',
                ];
            }
        }
        
        // Related Searches
        if (isset($data['related_searches']) && is_array($data['related_searches'])) {
            foreach ($data['related_searches'] as $related) {
                $processed['related_searches'][] = $related['query'] ?? '';
            }
        }
        
        // Featured Snippet
        if (isset($data['answer_box'])) {
            $processed['featured_snippet'] = [
                'type' => $data['answer_box']['type'] ?? 'paragraph',
                'snippet' => $data['answer_box']['snippet'] ?? '',
                'title' => $data['answer_box']['title'] ?? '',
                'url' => $data['answer_box']['link'] ?? '',
            ];
        }
        
        // Knowledge Graph
        if (isset($data['knowledge_graph'])) {
            $processed['knowledge_graph'] = [
                'title' => $data['knowledge_graph']['title'] ?? '',
                'type' => $data['knowledge_graph']['type'] ?? '',
                'description' => $data['knowledge_graph']['description'] ?? '',
            ];
        }
        
        return $processed;
    }
    
    /**
     * Extrae solo las URLs del top 10
     * 
     * @param array $serp_data Datos de SERP procesados
     * @param int $limit Cantidad de URLs (default: 10)
     * @return array Lista de URLs
     */
    public function extract_top_urls($serp_data, $limit = 10) {
        $urls = [];
        
        if (isset($serp_data['organic_results'])) {
            foreach ($serp_data['organic_results'] as $result) {
                if (!empty($result['url'])) {
                    $urls[] = $result['url'];
                }
                
                if (count($urls) >= $limit) {
                    break;
                }
            }
        }
        
        return $urls;
    }
    
    /**
     * Extrae People Also Ask
     * 
     * @param array $serp_data Datos de SERP
     * @return array Lista de preguntas
     */
    public function extract_paa($serp_data) {
        return $serp_data['people_also_ask'] ?? [];
    }
    
    /**
     * Extrae Related Searches
     * 
     * @param array $serp_data Datos de SERP
     * @return array Lista de búsquedas relacionadas
     */
    public function extract_related_searches($serp_data) {
        return $serp_data['related_searches'] ?? [];
    }
    
    /**
     * Calcula métricas promedio de top N
     * 
     * @param array $serp_data Datos de SERP
     * @param int $top_n Cantidad de resultados a analizar (default: 5)
     * @return array Métricas calculadas
     */
    public function calculate_average_metrics($serp_data, $top_n = 5) {
        $results = array_slice($serp_data['organic_results'] ?? [], 0, $top_n);
        
        if (empty($results)) {
            return [
                'avg_title_length' => 60,
                'avg_description_length' => 155,
                'total_results' => 0,
            ];
        }
        
        $title_lengths = [];
        $desc_lengths = [];
        
        foreach ($results as $result) {
            if (!empty($result['title'])) {
                $title_lengths[] = mb_strlen($result['title']);
            }
            if (!empty($result['description'])) {
                $desc_lengths[] = mb_strlen($result['description']);
            }
        }
        
        return [
            'avg_title_length' => !empty($title_lengths) ? round(array_sum($title_lengths) / count($title_lengths)) : 60,
            'avg_description_length' => !empty($desc_lengths) ? round(array_sum($desc_lengths) / count($desc_lengths)) : 155,
            'total_results' => count($results),
        ];
    }
    
    /**
     * Guarda análisis SERP en cache
     * 
     * @param string $keyword Keyword
     * @param array $data Datos a cachear
     * @param int $expiration Expiración en segundos (default: 24 horas)
     */
    public function cache_serp_data($keyword, $data, $expiration = DAY_IN_SECONDS) {
        $cache_key = 'asap_serp_' . md5($keyword);
        set_transient($cache_key, $data, $expiration);
    }
    
    /**
     * Obtiene análisis SERP del cache
     * 
     * @param string $keyword Keyword
     * @return array|null Datos cacheados o null
     */
    public function get_cached_serp_data($keyword) {
        $cache_key = 'asap_serp_' . md5($keyword);
        return get_transient($cache_key);
    }
    
    /**
     * Verifica si hay API key configurada
     * 
     * @return bool
     */
    public function has_api_key() {
        return !empty($this->api_key);
    }
}

