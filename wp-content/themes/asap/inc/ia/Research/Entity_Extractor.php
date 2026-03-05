<?php
/**
 * Entity Extractor
 * 
 * Extrae entidades importantes del contenido de competidores:
 * - Personas (expertos, autores, celebridades)
 * - Marcas y productos
 * - Lugares y ubicaciones
 * - Organizaciones
 * - Conceptos técnicos
 * 
 * Usa OpenAI para extracción inteligente y contextual.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Research
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Research_Entity_Extractor {
    
    /**
     * @var ASAP_IA_Core_OpenAI_Client
     */
    private $openai_client;
    
    /**
     * @var ASAP_IA_Core_Token_Calculator
     */
    private $token_calculator;
    
    /**
     * Constructor
     */
    public function __construct($openai_client = null, $token_calculator = null) {
        $this->openai_client = $openai_client ?: new ASAP_IA_Core_OpenAI_Client();
        $this->token_calculator = $token_calculator ?: new ASAP_IA_Core_Token_Calculator();
    }
    
    /**
     * Extrae entidades de múltiples competidores
     * 
     * @param array $competitors Lista de competidores scrapeados
     * @param string $keyword Keyword de contexto
     * @return array Entidades consolidadas y rankeadas
     */
    public function extract_from_competitors($competitors, $keyword = '') {
        if (empty($competitors)) {
            return [
                'entities' => [],
                'by_type' => [],
                'must_mention' => [],
                'cost' => 0,
                'tokens' => 0,
            ];
        }
        
        $all_entities = [];
        $total_cost = 0;
        $total_tokens = 0;
        
        // Extraer entidades de cada competidor
        foreach ($competitors as $competitor) {
            $content = $competitor['data']['main_content_excerpt'] ?? '';
            $title = $competitor['data']['title'] ?? '';
            
            if (empty($content)) continue;
            
            // Limitar contenido para no exceder tokens
            $content_sample = mb_substr($content, 0, 1500);
            $combined_text = $title . "\n\n" . $content_sample;
            
            $result = $this->extract_from_text($combined_text, $keyword);
            
            if (!is_wp_error($result)) {
                // Consolidar entidades
                foreach ($result['entities'] as $entity) {
                    $key = strtolower($entity['name']);
                    
                    if (!isset($all_entities[$key])) {
                        $all_entities[$key] = [
                            'name' => $entity['name'],
                            'type' => $entity['type'],
                            'context' => $entity['context'] ?? '',
                            'count' => 1,
                            'urls' => [$competitor['url'] ?? ''],
                        ];
                    } else {
                        $all_entities[$key]['count']++;
                        $all_entities[$key]['urls'][] = $competitor['url'] ?? '';
                    }
                }
                
                $total_cost += $result['cost'];
                $total_tokens += $result['tokens'];
            }
        }
        
        // Rankear por frecuencia
        uasort($all_entities, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        $total_competitors = count($competitors);
        
        // Identificar entidades "obligatorias" (50%+ de competidores)
        $must_mention = [];
        foreach ($all_entities as $entity) {
            $percentage = ($entity['count'] / $total_competitors) * 100;
            $entity['percentage'] = round($percentage);
            
            if ($percentage >= 50) {
                $must_mention[] = $entity;
            }
        }
        
        // Agrupar por tipo
        $by_type = [
            'brands' => [],
            'people' => [],
            'places' => [],
            'organizations' => [],
            'concepts' => [],
            'products' => [],
        ];
        
        foreach ($all_entities as $entity) {
            $type = $entity['type'] ?? 'concepts';
            if (isset($by_type[$type])) {
                $by_type[$type][] = $entity;
            }
        }
        
        return [
            'entities' => array_values($all_entities),
            'by_type' => $by_type,
            'must_mention' => $must_mention,
            'total_unique' => count($all_entities),
            'cost' => $total_cost,
            'tokens' => $total_tokens,
        ];
    }
    
    /**
     * Extrae entidades de un texto usando OpenAI
     * 
     * @param string $text Texto a analizar
     * @param string $keyword Keyword de contexto
     * @return array|WP_Error Entidades encontradas
     */
    public function extract_from_text($text, $keyword = '') {
        if (empty($text)) {
            return new WP_Error('empty_text', 'Texto vacío.');
        }
        
        $api_key = $this->openai_client->get_api_key();
        if (empty($api_key)) {
            return new WP_Error('no_api_key', 'Falta API Key de OpenAI.');
        }
        
        $model = 'gpt-4o-mini';
        $temperature = 0.3; // Más preciso para extracción
        
        $system = "Eres un experto en NLP y extracción de entidades nombradas.
                   Identifica y clasifica entidades importantes en el texto.
                   
                   TIPOS DE ENTIDADES:
                   - brands: Marcas comerciales (Ej: WordPress, Bluehost, Google)
                   - people: Personas mencionadas (Ej: Matt Mullenweg, expertos)
                   - places: Lugares, países, ciudades
                   - organizations: Empresas, instituciones
                   - products: Productos específicos
                   - concepts: Conceptos técnicos importantes (Ej: SSL, CDN, cPanel)
                   
                   Devuelve SOLO un JSON válido sin markdown.";
        
        $user = "Extrae las entidades más importantes y relevantes del siguiente texto.";
        if ($keyword) {
            $user .= "\nKeyword de contexto: {$keyword}";
        }
        $user .= "\n\nTexto:\n{$text}\n\n";
        $user .= "Devuelve JSON en este formato exacto:\n";
        $user .= '{"entities": [{"name": "Nombre", "type": "brands|people|places|organizations|products|concepts", "context": "breve descripción"}]}';
        
        $response = $this->openai_client->chat($api_key, $model, $temperature, $system, $user, 800);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        // Parsear JSON de la respuesta
        $content = trim($response['content']);
        
        // Limpiar markdown si existe
        $content = preg_replace('/^```json\s*/i', '', $content);
        $content = preg_replace('/\s*```$/i', '', $content);
        $content = trim($content);
        
        $parsed = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_parse_error', 'Error al parsear JSON: ' . json_last_error_msg());
        }
        
        $cost = $this->token_calculator->calculate_cost(
            $model,
            $response['usage']['prompt_tokens'],
            $response['usage']['completion_tokens']
        );
        
        return [
            'entities' => $parsed['entities'] ?? [],
            'cost' => $cost,
            'tokens' => $response['usage']['total_tokens'],
        ];
    }
    
    /**
     * Filtra y rankea entidades por relevancia
     * 
     * @param array $entities Lista de entidades extraídas
     * @param int $min_frequency Frecuencia mínima (default: 2)
     * @return array Entidades filtradas y rankeadas
     */
    public function filter_and_rank($entities, $min_frequency = 2) {
        $filtered = [];
        
        foreach ($entities as $entity) {
            // Filtrar por frecuencia mínima
            if (($entity['count'] ?? 1) < $min_frequency) {
                continue;
            }
            
            $filtered[] = $entity;
        }
        
        // Rankear por count descendente
        usort($filtered, function($a, $b) {
            return ($b['count'] ?? 0) <=> ($a['count'] ?? 0);
        });
        
        return $filtered;
    }
    
    /**
     * Genera lista de entidades para inyectar en prompts
     * 
     * @param array $entities_data Datos de entidades del briefing
     * @param array $types Tipos a incluir (default: todos)
     * @return string Lista formateada para prompts
     */
    public function generate_prompt_text($entities_data, $types = ['brands', 'concepts', 'products']) {
        $must_mention = $entities_data['must_mention'] ?? [];
        
        if (empty($must_mention)) {
            return '';
        }
        
        // Filtrar por tipos
        $filtered = array_filter($must_mention, function($entity) use ($types) {
            return in_array($entity['type'] ?? '', $types, true);
        });
        
        if (empty($filtered)) {
            return '';
        }
        
        $prompt_lines = [];
        $prompt_lines[] = "🏷️ ENTIDADES CLAVE A MENCIONAR (presentes en {$must_mention[0]['percentage']}%+ de top 10):";
        
        // Agrupar por tipo
        $by_type = [];
        foreach ($filtered as $entity) {
            $type = $entity['type'] ?? 'concepts';
            if (!isset($by_type[$type])) {
                $by_type[$type] = [];
            }
            $by_type[$type][] = $entity['name'];
        }
        
        // Formatear por tipo
        $type_labels = [
            'brands' => 'Marcas',
            'products' => 'Productos',
            'concepts' => 'Conceptos técnicos',
            'people' => 'Expertos/Autores',
            'organizations' => 'Organizaciones',
        ];
        
        foreach ($by_type as $type => $names) {
            $label = $type_labels[$type] ?? ucfirst($type);
            $names_str = implode(', ', array_slice($names, 0, 5)); // Max 5 por tipo
            $prompt_lines[] = "- {$label}: {$names_str}";
        }
        
        $prompt_lines[] = "\nMenciona estas entidades naturalmente cuando sean relevantes.";
        
        return implode("\n", $prompt_lines);
    }
    
    /**
     * Extrae entidades usando método regex (fallback rápido sin IA)
     * 
     * @param string $text Texto a analizar
     * @return array Entidades encontradas (menos preciso que IA)
     */
    public function extract_with_regex($text) {
        $entities = [];
        
        // Marcas comunes (WordPress ecosystem)
        $brand_patterns = [
            'WordPress', 'WooCommerce', 'Elementor', 'Yoast', 'RankMath',
            'Bluehost', 'SiteGround', 'Hostinger', 'HostGator', 'WP Engine',
            'Cloudflare', 'Google', 'Amazon', 'Facebook', 'Twitter',
            'PHP', 'MySQL', 'Apache', 'Nginx', 'Redis',
        ];
        
        foreach ($brand_patterns as $brand) {
            if (stripos($text, $brand) !== false) {
                $entities[] = [
                    'name' => $brand,
                    'type' => 'brands',
                    'count' => substr_count(strtolower($text), strtolower($brand)),
                ];
            }
        }
        
        // Conceptos técnicos
        $concept_patterns = [
            'SSL', 'HTTPS', 'CDN', 'DNS', 'FTP', 'SSH', 'cPanel',
            'hosting compartido', 'VPS', 'servidor dedicado', 'cloud',
            'backup', 'migración', 'dominio', 'ancho de banda',
        ];
        
        foreach ($concept_patterns as $concept) {
            if (stripos($text, $concept) !== false) {
                $entities[] = [
                    'name' => $concept,
                    'type' => 'concepts',
                    'count' => substr_count(strtolower($text), strtolower($concept)),
                ];
            }
        }
        
        return $entities;
    }
    
    /**
     * Valida y limpia entidades extraídas
     * 
     * @param array $entities Entidades crudas
     * @return array Entidades limpias
     */
    private function validate_entities($entities) {
        $cleaned = [];
        
        // Lista negra de palabras comunes (no son entidades)
        $blacklist = ['el', 'la', 'los', 'las', 'un', 'una', 'este', 'esta', 'ese', 'esa'];
        
        foreach ($entities as $entity) {
            $name = trim($entity['name'] ?? '');
            $name_lower = strtolower($name);
            
            // Validaciones
            if (empty($name)) continue;
            if (strlen($name) < 2) continue;
            if (in_array($name_lower, $blacklist, true)) continue;
            if (is_numeric($name)) continue;
            
            $cleaned[] = $entity;
        }
        
        return $cleaned;
    }
    
    /**
     * Calcula score de importancia para una entidad
     * 
     * @param array $entity Entidad con metadata
     * @param int $total_competitors Total de competidores
     * @return float Score de 0-100
     */
    public function calculate_importance_score($entity, $total_competitors) {
        $frequency = $entity['count'] ?? 1;
        $percentage = ($frequency / $total_competitors) * 100;
        
        // Factores de score
        $frequency_score = min(100, $percentage * 1.5); // Frecuencia es importante
        
        // Bonus por tipo (algunas entidades son más importantes)
        $type_bonus = [
            'brands' => 20,      // Marcas son muy importantes
            'products' => 20,    // Productos también
            'concepts' => 15,    // Conceptos técnicos
            'people' => 10,      // Personas
            'organizations' => 10,
            'places' => 5,
        ];
        
        $bonus = $type_bonus[$entity['type'] ?? 'concepts'] ?? 0;
        
        $total_score = min(100, $frequency_score + $bonus);
        
        return round($total_score);
    }
    
    /**
     * Genera resumen de entidades para mostrar al usuario
     * 
     * @param array $entities_data Datos de entidades
     * @return string Resumen en texto
     */
    public function generate_summary($entities_data) {
        $by_type = $entities_data['by_type'] ?? [];
        $total = $entities_data['total_unique'] ?? 0;
        $must_mention = $entities_data['must_mention'] ?? [];
        
        $summary = [];
        $summary[] = "🏷️ **ENTIDADES DETECTADAS:** {$total} únicas";
        $summary[] = "";
        
        // Must mention (50%+)
        if (!empty($must_mention)) {
            $summary[] = "**✅ MENCIONAR OBLIGATORIAMENTE** (presentes en 50%+ de top)";
            foreach (array_slice($must_mention, 0, 10) as $entity) {
                $summary[] = "  • {$entity['name']} ({$entity['type']}) - {$entity['percentage']}%";
            }
            $summary[] = "";
        }
        
        // Por tipo
        $type_labels = [
            'brands' => '🏢 Marcas',
            'products' => '📦 Productos',
            'concepts' => '💡 Conceptos',
            'people' => '👤 Personas',
            'organizations' => '🏛️ Organizaciones',
            'places' => '📍 Lugares',
        ];
        
        foreach ($by_type as $type => $entities) {
            if (empty($entities)) continue;
            
            $label = $type_labels[$type] ?? ucfirst($type);
            $count = count($entities);
            
            if ($count > 0) {
                $top_5 = array_slice($entities, 0, 5);
                $names = implode(', ', array_column($top_5, 'name'));
                $summary[] = "**{$label}** ({$count}): {$names}";
            }
        }
        
        return implode("\n", $summary);
    }
    
    /**
     * Genera contexto de entidades para inyectar en prompts
     * 
     * @param array $entities_data Datos de entidades
     * @param int $max_entities Máximo a incluir (default: 10)
     * @return string Texto para prompts
     */
    public function generate_context_for_prompt($entities_data, $max_entities = 10) {
        $must_mention = $entities_data['must_mention'] ?? [];
        
        if (empty($must_mention)) {
            return '';
        }
        
        $context_lines = [];
        
        // Agrupar por tipo
        $by_type = [];
        foreach (array_slice($must_mention, 0, $max_entities) as $entity) {
            $type = $entity['type'] ?? 'concepts';
            if (!isset($by_type[$type])) {
                $by_type[$type] = [];
            }
            $by_type[$type][] = $entity['name'];
        }
        
        // Formatear
        $type_labels = [
            'brands' => 'Marcas mencionadas en top 10',
            'products' => 'Productos relevantes',
            'concepts' => 'Conceptos técnicos clave',
            'people' => 'Expertos/Autores citados',
            'organizations' => 'Organizaciones relevantes',
        ];
        
        foreach ($by_type as $type => $names) {
            $label = $type_labels[$type] ?? ucfirst($type);
            $names_str = implode(', ', $names);
            $context_lines[] = "- {$label}: {$names_str}";
        }
        
        if (empty($context_lines)) {
            return '';
        }
        
        $context = "🏷️ ENTIDADES CLAVE (mencionar cuando sean relevantes):\n";
        $context .= implode("\n", $context_lines);
        
        return $context;
    }
    
    /**
     * Verifica si un texto menciona las entidades obligatorias
     * 
     * @param string $content Contenido generado
     * @param array $must_mention Entidades obligatorias
     * @return array Stats de cobertura
     */
    public function validate_entity_coverage($content, $must_mention) {
        if (empty($must_mention)) {
            return [
                'coverage_percentage' => 100,
                'mentioned' => [],
                'missing' => [],
            ];
        }
        
        $content_lower = strtolower($content);
        $mentioned = [];
        $missing = [];
        
        foreach ($must_mention as $entity) {
            $name = $entity['name'];
            $name_lower = strtolower($name);
            
            if (strpos($content_lower, $name_lower) !== false) {
                $mentioned[] = $name;
            } else {
                $missing[] = $name;
            }
        }
        
        $total = count($must_mention);
        $mentioned_count = count($mentioned);
        $coverage = $total > 0 ? round(($mentioned_count / $total) * 100) : 100;
        
        return [
            'coverage_percentage' => $coverage,
            'mentioned' => $mentioned,
            'missing' => $missing,
            'total_required' => $total,
            'total_mentioned' => $mentioned_count,
        ];
    }
    
    /**
     * Genera sugerencias de mejora si faltan entidades
     * 
     * @param array $validation Resultado de validate_entity_coverage
     * @return array Sugerencias
     */
    public function generate_improvement_suggestions($validation) {
        $missing = $validation['missing'] ?? [];
        
        if (empty($missing)) {
            return [
                'status' => 'excellent',
                'message' => '✅ Todas las entidades clave están cubiertas',
                'suggestions' => [],
            ];
        }
        
        $suggestions = [];
        $coverage = $validation['coverage_percentage'] ?? 0;
        
        if ($coverage < 50) {
            $suggestions[] = "⚠️ Cobertura baja ({$coverage}%). Agrega mínimo: " . implode(', ', array_slice($missing, 0, 3));
        } elseif ($coverage < 80) {
            $suggestions[] = "⚡ Mejorar cobertura ({$coverage}%). Considera mencionar: " . implode(', ', array_slice($missing, 0, 2));
        } else {
            $suggestions[] = "✅ Buena cobertura ({$coverage}%). Opcional: " . implode(', ', array_slice($missing, 0, 1));
        }
        
        return [
            'status' => $coverage >= 80 ? 'good' : ($coverage >= 50 ? 'fair' : 'poor'),
            'message' => $coverage >= 80 ? '✅ Buena cobertura' : '⚠️ Mejorar cobertura',
            'suggestions' => $suggestions,
            'coverage' => $coverage,
        ];
    }
}



