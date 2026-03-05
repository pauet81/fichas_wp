<?php
/**
 * PAA Processor (People Also Ask)
 * 
 * Procesa preguntas de "People Also Ask" y las integra en artículos.
 * Genera respuestas optimizadas para featured snippets.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Research
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Research_PAA_Processor {
    
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
     * Genera sección FAQ desde People Also Ask
     * 
     * @param array $paa_questions Lista de preguntas PAA
     * @param string $keyword Keyword principal
     * @param int $max_questions Máximo de preguntas (default: 5)
     * @return array|WP_Error ['html' => '...', 'cost' => 0.00, 'tokens' => 0]
     */
    public function generate_faq_section($paa_questions, $keyword = '', $max_questions = 5) {
        if (empty($paa_questions)) {
            return ['html' => '', 'cost' => 0, 'tokens' => 0];
        }
        
        $api_key = $this->openai_client->get_api_key();
        if (empty($api_key)) {
            return new WP_Error('no_api_key', 'Falta API Key de OpenAI.');
        }
        
        // Tomar solo las primeras N preguntas
        $questions_to_answer = array_slice($paa_questions, 0, $max_questions);
        
        $faq_html = '<h2>Preguntas Frecuentes</h2>' . "\n";
        $total_cost = 0;
        $total_tokens = 0;
        
        foreach ($questions_to_answer as $paa) {
            $question = $paa['question'] ?? '';
            if (empty($question)) continue;
            
            // Generar respuesta optimizada para snippet
            $answer_result = $this->generate_answer($question, $keyword, $api_key);
            
            if (is_wp_error($answer_result)) {
                continue; // Saltar si falla
            }
            
            $faq_html .= '<div class="faq-item">' . "\n";
            $faq_html .= '<h3>' . esc_html($question) . '</h3>' . "\n";
            $faq_html .= '<div class="faq-answer">' . "\n";
            $faq_html .= $answer_result['answer'] . "\n";
            $faq_html .= '</div>' . "\n";
            $faq_html .= '</div>' . "\n\n";
            
            $total_cost += $answer_result['cost'];
            $total_tokens += $answer_result['tokens'];
        }
        
        return [
            'html' => $faq_html,
            'cost' => $total_cost,
            'tokens' => $total_tokens,
            'questions_answered' => count($questions_to_answer),
        ];
    }
    
    /**
     * Genera respuesta a una pregunta específica
     * 
     * @param string $question Pregunta a responder
     * @param string $keyword Keyword de contexto
     * @param string $api_key API Key de OpenAI
     * @return array|WP_Error ['answer' => '...', 'cost' => 0.00, 'tokens' => 0]
     */
    private function generate_answer($question, $keyword, $api_key) {
        $model = 'gpt-4o-mini';
        $temperature = 0.5; // Más preciso para FAQs
        
        $system = "Eres experto en SEO y contenido optimizado para featured snippets. 
                   Genera respuestas concisas, precisas y directas.
                   Usa HTML básico: <p>, <ul>, <ol>, <li>, <strong>.
                   Longitud ideal: 40-60 palabras (para featured snippet).";
        
        $user = "Pregunta: {$question}\n";
        if ($keyword) {
            $user .= "Contexto/Keyword: {$keyword}\n";
        }
        $user .= "\nGenera una respuesta directa, clara y concisa (40-60 palabras). 
                  Optimizada para aparecer en featured snippets de Google.
                  Devuelve SOLO el HTML de la respuesta.";
        
        $response = $this->openai_client->chat($api_key, $model, $temperature, $system, $user, 200);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $cost = $this->token_calculator->calculate_cost(
            $model,
            $response['usage']['prompt_tokens'],
            $response['usage']['completion_tokens']
        );
        
        return [
            'answer' => trim($response['content']),
            'cost' => $cost,
            'tokens' => $response['usage']['total_tokens'],
        ];
    }
    
    /**
     * Integra PAA en secciones existentes (en lugar de FAQ separado)
     * 
     * @param array $paa_questions Lista de preguntas
     * @param array $h2_structure Estructura H2 del artículo
     * @return array Mapeo de preguntas a secciones
     */
    public function map_paa_to_sections($paa_questions, $h2_structure) {
        $mapping = [];
        
        foreach ($h2_structure as $index => $section) {
            $h2_title = $section['h2'] ?? '';
            $mapping[$index] = [
                'h2' => $h2_title,
                'relevant_paa' => [],
            ];
            
            // Buscar PAA relevantes para este H2
            foreach ($paa_questions as $paa) {
                $question = $paa['question'] ?? '';
                
                if ($this->is_relevant_to_section($question, $h2_title)) {
                    $mapping[$index]['relevant_paa'][] = $paa;
                }
            }
        }
        
        return $mapping;
    }
    
    /**
     * Verifica si una pregunta es relevante para una sección
     */
    private function is_relevant_to_section($question, $h2_title) {
        $question_lower = strtolower($question);
        $h2_lower = strtolower($h2_title);
        
        // Extraer palabras clave del H2
        $h2_words = preg_split('/\s+/', $h2_lower);
        $matches = 0;
        
        foreach ($h2_words as $word) {
            // Ignorar palabras cortas/comunes
            if (strlen($word) <= 3) continue;
            if (in_array($word, ['qué', 'cómo', 'para', 'por', 'con', 'una', 'los', 'las', 'del', 'que'])) continue;
            
            if (strpos($question_lower, $word) !== false) {
                $matches++;
            }
        }
        
        // Relevante si hay 2+ palabras coincidentes
        return $matches >= 2;
    }
    
    /**
     * Genera schema markup para FAQ
     * 
     * @param array $faq_items Items de FAQ [{question: '', answer: ''}]
     * @return string JSON-LD schema
     */
    public function generate_faq_schema($faq_items) {
        if (empty($faq_items)) {
            return '';
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];
        
        foreach ($faq_items as $item) {
            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($item['answer']),
                ],
            ];
        }
        
        return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}



