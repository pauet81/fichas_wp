<?php
/**
 * Competitor Scraper
 * 
 * Scrapea inteligentemente los primeros 3 URLs del top 10 que sean accesibles.
 * Implementa retry logic para evitar bloqueos de Cloudflare.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Research
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Research_Competitor_Scraper {
    
    /**
     * @var int Timeout por request (segundos)
     */
    private $timeout = 8;
    
    /**
     * @var int Máximo de URLs a intentar
     */
    private $max_attempts = 10;
    
    /**
     * @var int Target de URLs exitosas
     */
    private $target_success = 3;
    
    /**
     * Scrapea los primeros N competidores accesibles
     * 
     * @param array $urls Lista de URLs a scrapear
     * @param int $target Cantidad objetivo de URLs (default: 3)
     * @return array Array con datos scrapeados exitosos
     */
    public function scrape_top_competitors($urls, $target = 3) {
        if (empty($urls)) {
            return ['success' => [], 'failed' => [], 'errors' => []];
        }
        
        $this->target_success = $target;
        $successful = [];
        $failed = [];
        $errors = [];
        $attempted = 0;
        
        foreach ($urls as $url) {
            // Límite de intentos
            if ($attempted >= $this->max_attempts) {
                break;
            }
            
            // Ya tenemos suficientes exitosos
            if (count($successful) >= $this->target_success) {
                break;
            }
            
            $attempted++;
            
            // Intentar scraping
            $result = $this->scrape_single_url($url);
            
            if (is_wp_error($result)) {
                $failed[] = $url;
                $errors[$url] = $result->get_error_message();
                continue;
            }
            
            $successful[] = $result;
        }
        
        return [
            'success' => $successful,
            'failed' => $failed,
            'errors' => $errors,
            'success_rate' => $attempted > 0 ? round((count($successful) / $attempted) * 100) : 0,
        ];
    }
    
    /**
     * Scrapea una URL individual
     * 
     * @param string $url URL a scrapear
     * @return array|WP_Error Datos extraídos o error
     */
    public function scrape_single_url($url) {
        if (empty($url)) {
            return new WP_Error('invalid_url', 'URL inválida.');
        }
        
        // User agent realista para evitar bloqueos
        $user_agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];
        $user_agent = $user_agents[array_rand($user_agents)];
        
        $args = [
            'timeout' => $this->timeout,
            'redirection' => 3,
            'httpversion' => '1.1',
            'user-agent' => $user_agent,
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ];
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            return new WP_Error('scrape_failed', 'Error: ' . $response->get_error_message());
        }
        
        $code = wp_remote_retrieve_response_code($response);
        
        // Verificar si fue bloqueado
        if ($code === 403 || $code === 429 || $code === 503) {
            return new WP_Error('blocked', "Bloqueado (HTTP {$code}) - Cloudflare/WAF");
        }
        
        if ($code !== 200) {
            return new WP_Error('http_error', "HTTP {$code}");
        }
        
        $html = wp_remote_retrieve_body($response);
        
        // Verificar si es una página de Cloudflare challenge
        if (stripos($html, 'cloudflare') !== false && stripos($html, 'challenge') !== false) {
            return new WP_Error('cloudflare', 'Bloqueado por Cloudflare Challenge');
        }
        
        // Analizar contenido
        $analysis = $this->analyze_html($html, $url);
        
        if (is_wp_error($analysis)) {
            return $analysis;
        }
        
        return [
            'url' => $url,
            'scraped_at' => current_time('mysql'),
            'data' => $analysis,
        ];
    }
    
    /**
     * Analiza HTML y extrae estructura
     * 
     * @param string $html HTML del sitio
     * @param string $url URL original
     * @return array|WP_Error Análisis o error
     */
    private function analyze_html($html, $url) {
        if (empty($html) || strlen($html) < 100) {
            return new WP_Error('empty_content', 'Contenido vacío o muy corto.');
        }
        
        // Usar HTML_Parser existente
        $structure = ASAP_IA_Helpers_HTML_Parser::parse_html_structure($html);
        
        // Extraer contenido principal
        $main_content = ASAP_IA_Helpers_HTML_Parser::extract_main_content($html, $url);
        
        // Análisis adicional
        $analysis = [
            'title' => $structure['title'] ?? '',
            'h1' => $structure['h1'] ?? '',
            'h2_list' => $structure['h2'] ?? [],
            'h2_count' => count($structure['h2'] ?? []),
            'h3_count' => $this->count_headings($html, 'h3'),
            'word_count' => $structure['word_count'] ?? 0,
            'paragraph_count' => $this->count_elements($html, 'p'),
            'list_count' => $this->count_elements($html, 'ul') + $this->count_elements($html, 'ol'),
            'image_count' => $this->count_elements($html, 'img'),
            'table_count' => $this->count_elements($html, 'table'),
            'has_faq' => $this->has_faq_schema($html),
            'has_toc' => $this->has_table_of_contents($html),
            'main_content_excerpt' => mb_substr(wp_strip_all_tags($main_content), 0, 500),
        ];
        
        return $analysis;
    }
    
    /**
     * Cuenta elementos HTML
     */
    private function count_elements($html, $tag) {
        return substr_count(strtolower($html), "<{$tag}");
    }
    
    /**
     * Cuenta headings específicos
     */
    private function count_headings($html, $tag) {
        preg_match_all('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', $html, $matches);
        return count($matches[0]);
    }
    
    /**
     * Detecta si tiene FAQ schema
     */
    private function has_faq_schema($html) {
        return stripos($html, 'FAQPage') !== false || 
               stripos($html, 'Question') !== false && stripos($html, 'Answer') !== false;
    }
    
    /**
     * Detecta si tiene tabla de contenidos
     */
    private function has_table_of_contents($html) {
        $indicators = ['table-of-contents', 'toc', 'índice', 'contenido'];
        
        foreach ($indicators as $indicator) {
            if (stripos($html, $indicator) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Calcula métricas promedio de competidores
     * 
     * @param array $competitors Lista de competidores analizados
     * @return array Métricas promedio
     */
    public function calculate_competitor_averages($competitors) {
        if (empty($competitors)) {
            return [
                'avg_word_count' => 2500,
                'avg_h2_count' => 8,
                'avg_h3_count' => 15,
                'avg_images' => 5,
                'avg_lists' => 8,
                'avg_tables' => 1,
                'faq_usage_percent' => 0,
                'toc_usage_percent' => 0,
            ];
        }
        
        $metrics = [
            'word_counts' => [],
            'h2_counts' => [],
            'h3_counts' => [],
            'image_counts' => [],
            'list_counts' => [],
            'table_counts' => [],
            'has_faq' => 0,
            'has_toc' => 0,
        ];
        
        foreach ($competitors as $comp) {
            $data = $comp['data'] ?? [];
            
            if (!empty($data['word_count'])) $metrics['word_counts'][] = $data['word_count'];
            if (!empty($data['h2_count'])) $metrics['h2_counts'][] = $data['h2_count'];
            if (!empty($data['h3_count'])) $metrics['h3_counts'][] = $data['h3_count'];
            if (isset($data['image_count'])) $metrics['image_counts'][] = $data['image_count'];
            if (isset($data['list_count'])) $metrics['list_counts'][] = $data['list_count'];
            if (isset($data['table_count'])) $metrics['table_counts'][] = $data['table_count'];
            if (!empty($data['has_faq'])) $metrics['has_faq']++;
            if (!empty($data['has_toc'])) $metrics['has_toc']++;
        }
        
        $total = count($competitors);
        
        return [
            'avg_word_count' => !empty($metrics['word_counts']) ? round(array_sum($metrics['word_counts']) / count($metrics['word_counts'])) : 2500,
            'avg_h2_count' => !empty($metrics['h2_counts']) ? round(array_sum($metrics['h2_counts']) / count($metrics['h2_counts'])) : 8,
            'avg_h3_count' => !empty($metrics['h3_counts']) ? round(array_sum($metrics['h3_counts']) / count($metrics['h3_counts'])) : 15,
            'avg_images' => !empty($metrics['image_counts']) ? round(array_sum($metrics['image_counts']) / count($metrics['image_counts'])) : 5,
            'avg_lists' => !empty($metrics['list_counts']) ? round(array_sum($metrics['list_counts']) / count($metrics['list_counts'])) : 8,
            'avg_tables' => !empty($metrics['table_counts']) ? round(array_sum($metrics['table_counts']) / count($metrics['table_counts'])) : 1,
            'faq_usage_percent' => $total > 0 ? round(($metrics['has_faq'] / $total) * 100) : 0,
            'toc_usage_percent' => $total > 0 ? round(($metrics['has_toc'] / $total) * 100) : 0,
        ];
    }
}



