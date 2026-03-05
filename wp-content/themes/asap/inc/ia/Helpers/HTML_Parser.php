<?php
/**
 * HTML Parser Helper
 * 
 * Funciones auxiliares para parsear y extraer contenido HTML.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Helpers
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Helpers_HTML_Parser {
    
    /**
     * Parsear estructura HTML y extraer headings
     */
    public static function parse_html_structure($html) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $result = [
            'title' => '',
            'h1' => '',
            'h2' => [],
            'h3' => [],
            'h2_with_h3' => [],
            'word_count' => 0,
            'all_headings' => [],
        ];

        // Extraer título
        $title_nodes = $dom->getElementsByTagName('title');
        if ($title_nodes->length > 0) {
            $result['title'] = trim($title_nodes->item(0)->textContent);
        }

        // Extraer H1
        $h1_nodes = $dom->getElementsByTagName('h1');
        if ($h1_nodes->length > 0) {
            $result['h1'] = trim($h1_nodes->item(0)->textContent);
        }

        // Contar palabras del contenido
        $body_nodes = $dom->getElementsByTagName('body');
        if ($body_nodes->length > 0) {
            $body_text = $body_nodes->item(0)->textContent;
            $words = preg_split('/\s+/u', trim($body_text), -1, PREG_SPLIT_NO_EMPTY);
            $result['word_count'] = count($words);
        }

        // Extraer todos los headings en orden usando XPath
        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h2 | //h3');
        
        $current_h2_index = -1;
        
        foreach ($headings as $node) {
            $tag = strtolower($node->nodeName);
            
            // Clonar el nodo para manipularlo sin afectar el original
            $clone = $node->cloneNode(true);
            
            // Eliminar spans que suelen ser TOC o decorativos
            $spans_to_remove = $xpath->query('.//span', $clone);
            foreach ($spans_to_remove as $span) {
                // Solo remover si es de TOC o similar
                $class = $span->getAttribute('class');
                if (strpos($class, 'toc') !== false || strpos($class, 'index') !== false) {
                    $span->parentNode->removeChild($span);
                }
            }
            
            // Obtener texto limpio
            $text = trim($clone->textContent);
            
            if (empty($text)) continue;
            
            // Limpiar texto (quitar números, bullets, emojis al inicio)
            $text = preg_replace('/^\s*[\d\.\)\-\•\x{1F300}-\x{1F9FF}]+\s*/u', '', $text);
            $text = trim($text);
            
            if (empty($text)) continue;
            
            $result['all_headings'][] = ['tag' => $tag, 'text' => $text];
            
            if ($tag === 'h2') {
                $result['h2'][] = $text;
                $current_h2_index++;
                $result['h2_with_h3'][$current_h2_index] = [
                    'h2' => $text,
                    'h3' => []
                ];
            } elseif ($tag === 'h3' && $current_h2_index >= 0) {
                $result['h3'][] = $text;
                $result['h2_with_h3'][$current_h2_index]['h3'][] = $text;
            }
        }

        return $result;
    }
    
    /**
     * Hacer request HTTP a una URL
     */
    public static function fetch_url_html($url) {
        $args = [
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
            ],
            'redirection' => 5,
            'sslverify' => true,
        ];
        
        $res = wp_remote_get($url, $args);
        
        if (is_wp_error($res)) {
            $error_message = $res->get_error_message();
            return new WP_Error('connection_error', "Error de conexión: {$error_message}");
        }
        
        $code = wp_remote_retrieve_response_code($res);
        if (200 !== (int)$code) {
            // Mensajes más descriptivos según el código HTTP
            $error_messages = [
                403 => 'Acceso prohibido (403). El sitio bloquea el scraping.',
                404 => 'Página no encontrada (404).',
                500 => 'Error del servidor (500).',
                503 => 'Servicio no disponible (503).',
            ];
            
            $message = $error_messages[$code] ?? "HTTP {$code} al traer la URL.";
            return new WP_Error('bad_http', $message);
        }
        
        $body = wp_remote_retrieve_body($res);
        if (!$body || strlen($body) < 100) {
            return new WP_Error('empty', 'Respuesta vacía o muy corta.');
        }
        
        return $body;
    }
    
    /**
     * Extraer contenido principal de HTML
     */
    public static function extract_main_content($html, $url) {
        $title = '';
        $content_html = '';
        $images = [];
        
        // Intentar con Readability si está disponible
        if (class_exists('\fivefilters\Readability\Readability')) {
            try {
                // Configuración de Readability
                $config = new \fivefilters\Readability\Configuration();
                $config->setOriginalURL($url);
                
                // Parsear con Readability (v3.3.3+ acepta HTML string directamente)
                $readability = new \fivefilters\Readability\Readability($config);
                $readability->parse($html);
                
                // Obtener resultados
                $title = $readability->getTitle();
                $content_html = $readability->getContent();
                
            } catch (\Throwable $e) {
                // Fallback manual si Readability falla
            }
        }
        
        // Fallback manual (Readability no está instalada)
        if (empty($content_html)) {
            // Limpiar scripts, styles, nav, footer, header, aside, etc.
            $clean = preg_replace('#<(script|style|nav|footer|header|aside|noscript|iframe)[^>]*>.*?</\\1>#si', '', $html);
            
            // Extraer título
            if (preg_match('#<title[^>]*>(.*?)</title>#si', $html, $m)) {
                $title = wp_strip_all_tags(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5));
            }
            
            // Extraer imágenes
            if (preg_match_all('#<img[^>]+src=["\']([^"\']+)["\'][^>]*>#i', $clean, $im)) {
                $images = array_values(array_unique($im[1]));
            }
            
            // Estrategia 1: Buscar article o main
            if (preg_match('#<(article|main)[^>]*>(.*?)</\\1>#si', $clean, $m)) {
                $best = $m[2];
            }
            // Estrategia 2: Buscar divs con clases de contenido
            elseif (preg_match('#<div[^>]*class=["\'][^"\']*(?:post-content|entry-content|article-content|content|main-content|post_content)[^"\']*["\'][^>]*>(.*?)</div>#si', $clean, $m)) {
                $best = $m[1];
            }
            // Estrategia 3: Bloque con más párrafos
            else {
                $blocks = preg_split('#</?div[^>]*>|</?section[^>]*>|</?article[^>]*>#i', $clean);
                $best = '';
                $best_score = 0;
                
                foreach ($blocks as $block) {
                    // Contar párrafos y headings (indicadores de contenido real)
                    $p_count = substr_count(strtolower($block), '<p');
                    $h_count = substr_count(strtolower($block), '<h2') + substr_count(strtolower($block), '<h3');
                    $text = wp_strip_all_tags($block);
                    $text_len = mb_strlen($text);
                    
                    // Score ponderado: párrafos + headings + longitud
                    $score = ($p_count * 100) + ($h_count * 50) + ($text_len / 10);
                    
                    // Evitar bloques muy cortos o muy largos
                    if ($text_len > 200 && $text_len < 50000 && $score > $best_score) {
                        $best_score = $score;
                        $best = $block;
                    }
                }
            }
            
            if ($best) {
                $content_html = wp_kses(force_balance_tags($best), [
                    'p' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'ul' => [], 'ol' => [], 'li' => [],
                    'strong' => [], 'b' => [], 'em' => [], 'i' => [],
                    'a' => ['href' => [], 'title' => [], 'rel' => [], 'target' => []],
                    'blockquote' => [], 'code' => [], 'pre' => [], 'br' => []
                ]);
                
                if (empty($content_html)) {
                    $content_html = wpautop(esc_html(wp_strip_all_tags($best)));
                }
            }
        }
        
        // Open Graph fallbacks
        if (empty($title) && preg_match('#<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)#i', $html, $m)) {
            $title = wp_strip_all_tags(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5));
        }
        
        if (empty($images) && preg_match('#<meta\s+property=["\']og:image["\']\s+content=["\']([^"\']+)#i', $html, $m2)) {
            $images = [$m2[1]];
        }
        
        return [
            'title' => $title,
            'content' => $content_html,
            'images' => $images
        ];
    }
    
    /**
     * Encontrar primera imagen en contenido
     */
    public static function find_first_image_in_content($content) {
        if (preg_match('#<img[^>]+src=["\']([^"\']+)["\']#i', $content, $m)) {
            return $m[1];
        }
        return '';
    }
}


