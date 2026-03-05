<?php
/**
 * Briefing Builder
 * 
 * Construye briefings inteligentes basados en análisis SERP y competidores.
 * Identifica: search intent, must-cover topics, content gaps, target metrics.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Research
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Research_Briefing_Builder {
    
    /**
     * @var ASAP_IA_Research_SERP_Analyzer
     */
    private $serp_analyzer;
    
    /**
     * @var ASAP_IA_Research_Competitor_Scraper
     */
    private $competitor_scraper;
    
    /**
     * @var ASAP_IA_Research_Entity_Extractor
     */
    private $entity_extractor;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->serp_analyzer = new ASAP_IA_Research_SERP_Analyzer();
        $this->competitor_scraper = new ASAP_IA_Research_Competitor_Scraper();
        $this->entity_extractor = new ASAP_IA_Research_Entity_Extractor();
    }
    
    /**
     * Crea briefing completo desde una keyword
     * 
     * @param string $keyword Keyword a analizar
     * @param array $options Opciones adicionales
     * @return array|WP_Error Briefing completo o error
     */
    public function create_from_keyword($keyword, $options = []) {
        if (empty($keyword)) {
            return new WP_Error('missing_keyword', 'Keyword es obligatoria.');
        }
        
        // 1. Verificar cache (24 horas)
        $cached = $this->serp_analyzer->get_cached_serp_data($keyword);
        if ($cached && !empty($options['use_cache'])) {
            return $this->build_from_cached($cached);
        }
        
        // 2. Analizar SERPs
        $serp_data = $this->serp_analyzer->analyze($keyword, $options);
        
        if (is_wp_error($serp_data)) {
            return $serp_data;
        }
        
        // Cachear resultados SERP
        $this->serp_analyzer->cache_serp_data($keyword, $serp_data);
        
        // 3. Extraer top URLs
        $top_urls = $this->serp_analyzer->extract_top_urls($serp_data, 10);
        
        // 4. Scrapear competidores (solo primeros 3 accesibles)
        // IMPORTANTE: Esto puede tomar ~10-20 segundos
        $competitors = $this->competitor_scraper->scrape_top_competitors($top_urls, 3);
        
        // 5. Extraer entidades de competidores (NUEVO)
        $entities_data = $this->entity_extractor->extract_from_competitors(
            $competitors['success'] ?? [],
            $keyword
        );
        
        // 6. Construir briefing
        $briefing = $this->build_briefing($keyword, $serp_data, $competitors, $entities_data);
        
        // Cachear briefing completo (24 horas)
        $this->cache_briefing($keyword, $briefing);
        
        return $briefing;
    }
    
    /**
     * Construye briefing desde datos ya procesados (para background tasks)
     * 
     * @param string $keyword Keyword
     * @param array $serp_data Datos de SERP
     * @param array $competitors Competidores scrapeados
     * @param array $entities_data Entidades extraídas
     * @return array Briefing estructurado
     */
    public function build_briefing_from_data($keyword, $serp_data, $competitors, $entities_data = []) {
        return $this->build_briefing($keyword, $serp_data, $competitors, $entities_data);
    }
    
    /**
     * Construye briefing a partir de datos SERP y competidores
     * 
     * @param string $keyword Keyword
     * @param array $serp_data Datos de SERP
     * @param array $competitors Competidores scrapeados
     * @param array $entities_data Entidades extraídas (NUEVO)
     * @return array Briefing estructurado
     */
    private function build_briefing($keyword, $serp_data, $competitors, $entities_data = []) {
        $successful_comps = $competitors['success'] ?? [];
        
        // Calcular métricas promedio
        $averages = $this->competitor_scraper->calculate_competitor_averages($successful_comps);
        
        // Detectar search intent
        $search_intent = $this->detect_search_intent($keyword, $serp_data);
        
        // Extraer temas comunes (must-cover)
        $must_cover_topics = $this->extract_must_cover_topics($successful_comps);
        
        // Extraer H2 más comunes
        $common_h2 = $this->extract_common_h2($successful_comps);
        
        // Identificar content gaps
        $content_gaps = $this->identify_content_gaps($successful_comps, $common_h2);
        
        // People Also Ask
        $paa = $this->serp_analyzer->extract_paa($serp_data);
        
        // Related Searches
        $related_searches = $this->serp_analyzer->extract_related_searches($serp_data);
        
        // Construir briefing
        $briefing = [
            'keyword' => $keyword,
            'created_at' => current_time('mysql'),
            
            // Métricas objetivo
            'target_metrics' => [
                'word_count' => max(2000, $averages['avg_word_count']),
                'h2_count' => max(6, $averages['avg_h2_count']),
                'h3_count' => $averages['avg_h3_count'],
                'images_count' => $averages['avg_images'],
                'lists_count' => $averages['avg_lists'],
                'tables_count' => $averages['avg_tables'],
                'should_include_faq' => $averages['faq_usage_percent'] > 30,
                'should_include_toc' => $averages['toc_usage_percent'] > 50,
            ],
            
            // Análisis de competencia
            'competition' => [
                'total_results' => $serp_data['total_results'] ?? 0,
                'analyzed_urls' => count($successful_comps),
                'failed_urls' => count($competitors['failed'] ?? []),
                'top_domains' => $this->extract_top_domains($serp_data),
            ],
            
            // Intent y tono
            'search_intent' => $search_intent,
            'recommended_tone' => $this->detect_tone($successful_comps),
            
            // Contenido
            'must_cover_topics' => $must_cover_topics,
            'suggested_h2' => $common_h2,
            'content_gaps' => $content_gaps,
            
            // Preguntas
            'people_also_ask' => $paa,
            'paa_count' => count($paa),
            
            // Keywords relacionadas
            'related_searches' => $related_searches,
            
            // Featured snippet
            'featured_snippet' => $serp_data['featured_snippet'] ?? null,
            
            // 🆕 ENTIDADES (marcas, personas, conceptos)
            'entities' => $entities_data,
            'entities_summary' => !empty($entities_data) ? $this->entity_extractor->generate_summary($entities_data) : '',
            
            // Competidores (datos completos)
            'competitors_data' => $successful_comps,
            
            // Métricas de calidad
            'quality_benchmarks' => [
                'min_word_count' => round($averages['avg_word_count'] * 0.8),
                'target_word_count' => $averages['avg_word_count'],
                'optimal_word_count' => round($averages['avg_word_count'] * 1.2),
                'min_h2' => max(5, $averages['avg_h2_count'] - 2),
                'optimal_h2' => max(8, $averages['avg_h2_count'] + 1),
            ],
        ];
        
        return $briefing;
    }
    
    /**
     * Detecta search intent
     */
    private function detect_search_intent($keyword, $serp_data) {
        $keyword_lower = strtolower($keyword);
        
        // Patrones de intent
        $informational = ['qué es', 'cómo', 'guía', 'tutorial', 'aprende', 'definición'];
        $commercial = ['mejor', 'top', 'comparativa', 'vs', 'opinión', 'review'];
        $transactional = ['comprar', 'precio', 'descuento', 'oferta', 'barato'];
        $navigational = ['login', 'acceso', 'descargar', 'sitio oficial'];
        
        foreach ($informational as $pattern) {
            if (strpos($keyword_lower, $pattern) !== false) return 'informational';
        }
        foreach ($commercial as $pattern) {
            if (strpos($keyword_lower, $pattern) !== false) return 'commercial';
        }
        foreach ($transactional as $pattern) {
            if (strpos($keyword_lower, $pattern) !== false) return 'transactional';
        }
        foreach ($navigational as $pattern) {
            if (strpos($keyword_lower, $pattern) !== false) return 'navigational';
        }
        
        // Analizar títulos de top 5
        $titles = array_slice($serp_data['organic_results'] ?? [], 0, 5);
        $titles_text = implode(' ', array_column($titles, 'title'));
        $titles_lower = strtolower($titles_text);
        
        if (strpos($titles_lower, 'comprar') !== false || strpos($titles_lower, 'precio') !== false) {
            return 'transactional';
        }
        if (strpos($titles_lower, 'mejor') !== false || strpos($titles_lower, 'top') !== false) {
            return 'commercial';
        }
        
        return 'informational'; // Default
    }
    
    /**
     * Extrae temas que DEBEN cubrirse (presentes en 50%+ de top)
     */
    private function extract_must_cover_topics($competitors) {
        if (empty($competitors)) {
            return [];
        }
        
        $all_h2 = [];
        
        // Recopilar todos los H2
        foreach ($competitors as $comp) {
            $h2_list = $comp['data']['h2_list'] ?? [];
            foreach ($h2_list as $h2) {
                $h2_clean = $this->normalize_heading($h2);
                if (!empty($h2_clean)) {
                    $all_h2[] = $h2_clean;
                }
            }
        }
        
        // Contar frecuencias
        $h2_freq = array_count_values($all_h2);
        arsort($h2_freq);
        
        $total_competitors = count($competitors);
        $threshold = $total_competitors * 0.5; // 50% o más
        
        $must_cover = [];
        foreach ($h2_freq as $h2 => $count) {
            if ($count >= $threshold) {
                $must_cover[] = [
                    'topic' => $h2,
                    'frequency' => $count,
                    'percentage' => round(($count / $total_competitors) * 100),
                ];
            }
        }
        
        return array_slice($must_cover, 0, 10); // Top 10 temas obligatorios
    }
    
    /**
     * Extrae H2 más comunes para sugerencias
     */
    private function extract_common_h2($competitors) {
        if (empty($competitors)) {
            return [];
        }
        
        $all_h2 = [];
        
        foreach ($competitors as $comp) {
            $h2_list = $comp['data']['h2_list'] ?? [];
            foreach ($h2_list as $h2) {
                $h2_clean = $this->normalize_heading($h2);
                if (!empty($h2_clean)) {
                    $all_h2[] = $h2_clean;
                }
            }
        }
        
        // Contar y ordenar
        $h2_freq = array_count_values($all_h2);
        arsort($h2_freq);
        
        // Tomar top 12 más comunes
        $common = [];
        $count = 0;
        foreach ($h2_freq as $h2 => $freq) {
            $common[] = [
                'h2' => $h2,
                'usage_count' => $freq,
                'percentage' => round(($freq / count($competitors)) * 100),
            ];
            if (++$count >= 12) break;
        }
        
        return $common;
    }
    
    /**
     * Identifica content gaps (temas únicos para destacar)
     */
    private function identify_content_gaps($competitors, $common_h2) {
        // Por ahora, sugerir temas genéricos de valor agregado
        // En el futuro, podría usar IA para identificar gaps reales
        
        $gap_suggestions = [
            'Tabla comparativa detallada',
            'Ejemplos prácticos paso a paso',
            'Casos de uso reales',
            'Errores comunes a evitar',
            'Mejores prácticas actualizadas 2025',
            'Recursos y herramientas adicionales',
        ];
        
        return array_slice($gap_suggestions, 0, 3);
    }
    
    /**
     * Detecta tono predominante en competidores
     */
    private function detect_tone($competitors) {
        // Análisis simple basado en palabras
        // En el futuro podría usar IA para análisis más sofisticado
        
        $formal_indicators = ['usted', 'debe', 'es importante', 'se recomienda'];
        $casual_indicators = ['tú', 'puedes', 'vamos', 'fácil', 'simple'];
        
        $formal_score = 0;
        $casual_score = 0;
        
        foreach ($competitors as $comp) {
            $excerpt = strtolower($comp['data']['main_content_excerpt'] ?? '');
            
            foreach ($formal_indicators as $indicator) {
                if (strpos($excerpt, $indicator) !== false) $formal_score++;
            }
            foreach ($casual_indicators as $indicator) {
                if (strpos($excerpt, $indicator) !== false) $casual_score++;
            }
        }
        
        if ($formal_score > $casual_score) {
            return 'formal';
        } elseif ($casual_score > $formal_score) {
            return 'casual';
        }
        
        return 'balanced';
    }
    
    /**
     * Normaliza heading para comparación
     */
    private function normalize_heading($heading) {
        // Limpiar
        $clean = trim($heading);
        $clean = wp_strip_all_tags($clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = preg_replace('/^\d+[\.\)]\s*/', '', $clean); // Quitar numeración
        
        // Normalizar a minúsculas para comparación
        return mb_strtolower($clean);
    }
    
    /**
     * Extrae dominios top
     */
    private function extract_top_domains($serp_data) {
        $domains = [];
        
        foreach ($serp_data['organic_results'] ?? [] as $result) {
            if (!empty($result['domain'])) {
                $domains[] = $result['domain'];
            }
        }
        
        return array_slice(array_unique($domains), 0, 5);
    }
    
    /**
     * Construye briefing desde cache
     */
    private function build_from_cached($cached_serp) {
        // Reconstruir briefing desde SERP cacheado
        // (sin volver a scrapear competidores)
        return $cached_serp;
    }
    
    /**
     * Cachea briefing completo - ahora público para background tasks
     */
    public function cache_briefing($keyword, $briefing) {
        $cache_key = 'asap_briefing_' . md5($keyword);
        set_transient($cache_key, $briefing, DAY_IN_SECONDS);
    }
    
    /**
     * Obtiene briefing cacheado
     */
    public function get_cached_briefing($keyword) {
        $cache_key = 'asap_briefing_' . md5($keyword);
        return get_transient($cache_key);
    }
    
    /**
     * Genera resumen en texto para mostrar al usuario
     */
    public function generate_summary($briefing) {
        $summary = [];
        
        $summary[] = "📊 **Análisis SERP para:** {$briefing['keyword']}";
        $summary[] = "";
        $summary[] = "**🎯 Search Intent:** " . ucfirst($briefing['search_intent']);
        $summary[] = "**📏 Longitud objetivo:** {$briefing['target_metrics']['word_count']} palabras (promedio top 10)";
        $summary[] = "**📋 H2 recomendados:** {$briefing['target_metrics']['h2_count']} secciones";
        $summary[] = "";
        
        // Must cover topics
        if (!empty($briefing['must_cover_topics'])) {
            $summary[] = "**✅ Temas obligatorios a cubrir:**";
            foreach (array_slice($briefing['must_cover_topics'], 0, 5) as $topic) {
                $summary[] = "  • {$topic['topic']} ({$topic['percentage']}% de top 10)";
            }
            $summary[] = "";
        }
        
        // PAA
        if (!empty($briefing['people_also_ask'])) {
            $summary[] = "**❓ People Also Ask ({$briefing['paa_count']} preguntas):**";
            foreach (array_slice($briefing['people_also_ask'], 0, 5) as $paa) {
                $summary[] = "  • {$paa['question']}";
            }
            $summary[] = "";
        }
        
        // Sugerencias H2
        if (!empty($briefing['suggested_h2'])) {
            $summary[] = "**💡 H2 sugeridos (basados en top 10):**";
            foreach (array_slice($briefing['suggested_h2'], 0, 8) as $h2) {
                $summary[] = "  • {$h2['h2']} ({$h2['percentage']}%)";
            }
            $summary[] = "";
        }
        
        // Content gaps
        if (!empty($briefing['content_gaps'])) {
            $summary[] = "**🎯 Oportunidades únicas (content gaps):**";
            foreach ($briefing['content_gaps'] as $gap) {
                $summary[] = "  • {$gap}";
            }
            $summary[] = "";
        }
        
        // 🆕 Entidades (NUEVO)
        if (!empty($briefing['entities']['must_mention'])) {
            $summary[] = "**🏷️ ENTIDADES CLAVE** (mencionar en el artículo):";
            foreach (array_slice($briefing['entities']['must_mention'], 0, 8) as $entity) {
                $type_emoji = [
                    'brands' => '🏢',
                    'concepts' => '💡',
                    'products' => '📦',
                    'people' => '👤',
                    'organizations' => '🏛️',
                    'places' => '📍',
                ];
                $emoji = $type_emoji[$entity['type']] ?? '•';
                $summary[] = "  {$emoji} {$entity['name']} ({$entity['percentage']}%)";
            }
            $summary[] = "";
        }
        
        // Competencia
        $summary[] = "**🏆 Competencia analizada:**";
        $summary[] = "  • URLs analizadas: {$briefing['competition']['analyzed_urls']} de 10";
        $summary[] = "  • Tasa de éxito: {$briefing['competition']['analyzed_urls']}/10";
        
        return implode("\n", $summary);
    }
    
    /**
     * Genera prompt mejorado para Article_Generator
     * 
     * @param array $briefing Briefing completo
     * @param string $h2_title Título H2 actual a generar
     * @return string Prompt enriquecido
     */
    public function enhance_prompt_with_briefing($briefing, $h2_title = '') {
        $prompt_additions = [];
        
        // Contexto de competencia
        $prompt_additions[] = "ANÁLISIS DE COMPETENCIA (Top {$briefing['competition']['analyzed_urls']} URLs):";
        $prompt_additions[] = "- Longitud promedio: {$briefing['target_metrics']['word_count']} palabras";
        $prompt_additions[] = "- H2 promedio: {$briefing['target_metrics']['h2_count']}";
        $prompt_additions[] = "- Search Intent: {$briefing['search_intent']}";
        $prompt_additions[] = "";
        
        // Must cover topics
        if (!empty($briefing['must_cover_topics'])) {
            $topics = array_slice($briefing['must_cover_topics'], 0, 5);
            $topic_list = implode(', ', array_column($topics, 'topic'));
            $prompt_additions[] = "TEMAS OBLIGATORIOS A CUBRIR: {$topic_list}";
            $prompt_additions[] = "";
        }
        
        // PAA relevante a esta sección
        if (!empty($briefing['people_also_ask']) && $h2_title) {
            $relevant_paa = $this->find_relevant_paa($briefing['people_also_ask'], $h2_title);
            if (!empty($relevant_paa)) {
                $prompt_additions[] = "PREGUNTAS QUE LA GENTE BUSCA (responde naturalmente):";
                foreach ($relevant_paa as $paa) {
                    $prompt_additions[] = "- {$paa['question']}";
                }
                $prompt_additions[] = "";
            }
        }
        
        return implode("\n", $prompt_additions);
    }
    
    /**
     * Encuentra PAA relevantes para un H2
     */
    private function find_relevant_paa($paa_list, $h2_title) {
        $relevant = [];
        $h2_lower = strtolower($h2_title);
        
        foreach ($paa_list as $paa) {
            $question_lower = strtolower($paa['question']);
            
            // Buscar palabras clave del H2 en la pregunta
            $h2_words = explode(' ', $h2_lower);
            $matches = 0;
            
            foreach ($h2_words as $word) {
                if (strlen($word) > 3 && strpos($question_lower, $word) !== false) {
                    $matches++;
                }
            }
            
            // Si hay 2+ palabras coincidentes, es relevante
            if ($matches >= 2) {
                $relevant[] = $paa;
            }
        }
        
        return array_slice($relevant, 0, 2); // Máx 2 por sección
    }
}

