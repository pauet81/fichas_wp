<?php
/**
 * Generador de Artículos por Secciones
 * 
 * Genera artículos completos divididos en secciones con contexto acumulado
 * para evitar repeticiones y mejorar coherencia.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Generators
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Generators_Article_Generator {
    
    /**
     * @var ASAP_IA_Core_OpenAI_Client Cliente de OpenAI
     */
    private $openai_client;
    
    /**
     * @var ASAP_IA_Core_Gemini_Client Cliente de Gemini
     */
    private $gemini_client;
    
    /**
     * @var ASAP_IA_Core_Token_Calculator Calculadora de tokens
     */
    private $token_calculator;
    
    /**
     * @var callable Función de logging
     */
    private $logger;
    
    /**
     * @var ASAP_IA_Generators_Image_Generator|null Generador de imágenes
     */
    private $image_generator;
    
    /**
     * @var ASAP_IA_Generators_Content_Image_Injector|null Inyector de imágenes de contenido
     */
    private $content_image_injector;
    
    /**
     * Constructor
     * 
     * @param ASAP_IA_Core_OpenAI_Client $openai_client Cliente de OpenAI
     * @param ASAP_IA_Core_Token_Calculator $token_calculator Calculadora de tokens
     * @param callable $logger Función de logging
     * @param ASAP_IA_Generators_Image_Generator|null $image_generator Generador de imágenes (opcional)
     */
    public function __construct($openai_client, $token_calculator, $logger = null, $image_generator = null) {
        $this->openai_client = $openai_client;
        $this->gemini_client = new ASAP_IA_Core_Gemini_Client();
        $this->token_calculator = $token_calculator;
        $this->logger = $logger;
        $this->image_generator = $image_generator;
        
        // Instanciar Content_Image_Injector si tenemos image_generator
        if ($image_generator) {
            $this->content_image_injector = new ASAP_IA_Generators_Content_Image_Injector(
                $openai_client,
                $image_generator
            );
        }
    }
    
    /**
     * Método wrapper que llama a la IA configurada (OpenAI o Gemini)
     * 
     * @param string $system Mensaje de sistema
     * @param string $user_content Contenido del usuario
     * @param int $max_tokens Máximo de tokens
     * @return array|WP_Error Respuesta de la IA
     */
    private function call_ai($system, $user_content, $max_tokens = 3000) {
        $provider = get_option('asap_ia_provider', 'openai');
        
        if ($provider === 'gemini') {
            $api_key = $this->gemini_client->get_api_key();
            if (empty($api_key)) {
                return new WP_Error('no_api_key', 'Falta configurar Google Gemini API Key.');
            }
            $model = get_option('asap_ia_gemini_model', 'gemini-2.5-flash-lite');
            $temperature = floatval(get_option('asap_ia_temperature', 0.7));
            
            return $this->gemini_client->chat($api_key, $model, $temperature, $system, $user_content, $max_tokens);
        } else {
            // OpenAI por defecto
            $api_key = $this->openai_client->get_api_key();
            if (empty($api_key)) {
                return new WP_Error('no_api_key', 'Falta configurar OpenAI API Key.');
            }
            $model = get_option('asap_ia_openai_model', 'gpt-4o-mini');
            $temperature = floatval(get_option('asap_ia_temperature', 0.7));
            
            return $this->openai_client->chat($api_key, $model, $temperature, $system, $user_content, $max_tokens);
        }
    }
    
    /**
     * Obtiene el nombre del proveedor y modelo actual
     * 
     * @return string Nombre del proveedor y modelo
     */
    private function get_provider_name() {
        $provider = get_option('asap_ia_provider', 'openai');
        
        if ($provider === 'gemini') {
            $model = get_option('asap_ia_gemini_model', 'gemini-2.5-flash-lite');
            return "Google Gemini ({$model})";
        } else {
            $model = get_option('asap_ia_openai_model', 'gpt-4o-mini');
            return "OpenAI ({$model})";
        }
    }
    
    /**
     * Alias para compatibilidad - Delega a generate_by_sections
     */
    public function generate_from_params($params) {
        return $this->generate_by_sections($params);
    }
    
    /**
     * Genera un artículo completo por secciones con contexto acumulado
     * 
     * @param array $params Parámetros de generación
     * @return int|WP_Error ID del post creado o WP_Error si falla
     */
    public function generate_by_sections($params) {
        // Aumentar límite de tiempo
        @set_time_limit(300); // 5 minutos
        
        // 🆕 INICIALIZAR LOGGER
        $session_id = $params['session_id'] ?? ASAP_IA_Database_Generation_Logger::generate_session_id();
        $gen_logger = new ASAP_IA_Database_Generation_Logger();
        $start_time = microtime(true);
        
        // Validaciones básicas
        $h1 = $params['h1'] ?? '';
        if (empty($h1)) {
            $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Error: Falta el H1');
            return new WP_Error('missing_h1', 'Falta el H1.');
        }

        // Parsear y validar outline
        $outline = $this->parse_outline($params['outline'] ?? '[]');
        if (is_wp_error($outline)) {
            $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Error al parsear outline: ' . $outline->get_error_message());
            return $outline;
        }
        
        // Si no hay outline, generar H2s automáticamente
        if (empty($outline)) {
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'No hay estructura definida. Generando H2s automáticamente...');
            $outline = $this->generate_automatic_outline($h1, $params, $session_id, $gen_logger);
            if (is_wp_error($outline) || empty($outline)) {
                $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Error al generar estructura automática');
                return new WP_Error('outline_generation_failed', 'No se pudo generar la estructura automática.');
            }
            $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Estructura generada: ' . count($outline) . ' H2s');
        }

        // Configuración del proveedor
        $provider = get_option('asap_ia_provider', 'openai');
        $provider_name = $this->get_provider_name();
        
        // Verificar API key según el proveedor
        if ($provider === 'gemini') {
            $api_key = $this->gemini_client->get_api_key();
            if (empty($api_key)) {
                $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Error: Falta Google Gemini API Key');
                return new WP_Error('no_api_key', 'Falta configurar Google Gemini API Key.');
            }
        } else {
        $api_key = $this->openai_client->get_api_key();
        if (empty($api_key)) {
            $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Error: Falta OpenAI API Key');
            return new WP_Error('no_api_key', 'Falta configurar OpenAI API Key.');
            }
        }

        // Configuración
        $target_len = intval($params['target_len'] ?? 3000);
        $style = $params['style'] ?? 'informativo';
        $lang = $params['lang'] ?? 'es';
        $keyword = $params['keyword'] ?? '';
        
        // Log inicio con proveedor de IA
        $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, "🤖 Usando IA: {$provider_name}", [
            'provider' => $provider,
            'provider_name' => $provider_name,
        ], null, $keyword);
        
        $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Iniciando generación de artículo', [
            'h1' => $h1,
            'keyword' => $keyword,
            'sections' => count($outline),
            'target_len' => $target_len,
        ], null, $keyword);

        // 🆕 OBTENER BRIEFING SI EXISTE (mejora calidad significativamente)
        $briefing = null;
        if (!empty($keyword)) {
            $briefing_builder = new ASAP_IA_Research_Briefing_Builder();
            $briefing = $briefing_builder->get_cached_briefing($keyword);
            
            // Si hay briefing, ajustar target_len basado en competencia
            if ($briefing && isset($briefing['target_metrics']['word_count'])) {
                $target_len = max($target_len, $briefing['target_metrics']['word_count']);
            }
        }

        // Calcular palabras por sección
        $h2_count = count($outline);
        $words_per_h2 = round($target_len / $h2_count);

        // Contexto acumulado (mejorado si hay briefing)
        $context_manager = new ASAP_IA_Core_Context_Manager();
        $generated_content = [];
        $total_cost = 0;
        $total_tokens = 0;

        // Keywords secundarias
        $secondary_keywords = !empty($params['secondary_keywords']) ? (array) $params['secondary_keywords'] : [];

        // Sistema base para prompts (MEJORADO con briefing y secondary keywords)
        $system_base = $this->build_system_prompt($lang, $style, $briefing, $secondary_keywords);
        
        // ✅ Guardar progreso inicial en transient
        $this->update_progress_transient($session_id, 5, '⏳ Generando introducción...');
        
        // 1. Generar introducción
        $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_INTRO, 'Generando introducción...');
        $intro_start = microtime(true);
        
        $intro_result = $this->generate_intro($params, $h1, $keyword, $outline, $system_base, $context_manager);
        if (!empty($intro_result['content'])) {
            $intro_duration = microtime(true) - $intro_start;
            $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_INTRO, 'Introducción generada', null, null, $keyword, $intro_duration, $intro_result['cost']);
            
            $generated_content[] = $intro_result['content'];
            $total_cost += $intro_result['cost'];
            $total_tokens += $intro_result['tokens'];
            
            // ✅ Actualizar progreso
            $this->update_progress_transient($session_id, 15, '✅ Introducción generada');
        }

        // 2. Generar cada sección H2
        foreach ($outline as $index => $section) {
            $section_h2 = $section['h2'] ?? "Sección " . ($index + 1);
            
            // ✅ Calcular progreso basado en secciones
            $progress_percent = 15 + (($index + 1) / $h2_count) * 70; // 15% intro + 70% secciones
            $this->update_progress_transient($session_id, $progress_percent, "✍️ Generando sección " . ($index + 1) . " de {$h2_count}: {$section_h2}");
            
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_SECTION, "Generando sección " . ($index + 1) . ": {$section_h2}");
            $section_start = microtime(true);
            
            $section_result = $this->generate_section(
                $section,
                $index,
                $h1,
                $keyword,
                $words_per_h2,
                $system_base,
                $context_manager,
                $secondary_keywords
            );
            
            $section_duration = microtime(true) - $section_start;
            $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_SECTION, "Sección " . ($index + 1) . " completada", null, null, $keyword, $section_duration, $section_result['cost']);
            
            // ⭐ GENERAR IMAGEN DE CONTENIDO (si está habilitado)
            $section_content = $section_result['content'];
            if ($this->content_image_injector && isset($params['image_settings'])) {
                $img_settings = $params['image_settings'];
                $total_sections = count($outline);
                
                if ($this->content_image_injector->should_inject_image_here($index, $total_sections, $img_settings)) {
                    $gen_logger->info($session_id, 'content_image', "Generando imagen para: {$section_h2}...");
                    $img_start = microtime(true);
                    
                    $image_data = $this->content_image_injector->generate_image_for_section(
                        $h1,
                        $section_h2,
                        $section_content,
                        $img_settings,
                        $session_id
                    );
                    
                    if ($image_data && !empty($image_data['url'])) {
                        // Inyectar imagen en el HTML
                        $alt_text = $section_h2; // Usar H2 como ALT
                        $section_content = $this->content_image_injector->inject_image_into_html(
                            $section_content,
                            $image_data['url'],
                            $alt_text,
                            $img_settings
                        );
                        
                        $img_duration = microtime(true) - $img_start;
                        $gen_logger->success($session_id, 'content_image', "Imagen inyectada para: {$section_h2}", null, null, $keyword, $img_duration);
                    } else {
                        $img_duration = microtime(true) - $img_start;
                        $gen_logger->error($session_id, 'content_image', "Error al generar imagen para: {$section_h2}", null, null, $keyword, $img_duration);
                    }
                }
            }
            
            $generated_content[] = $section_content;
            $total_cost += $section_result['cost'];
            $total_tokens += $section_result['tokens'];
        }

        // 3. Generar FAQs
        $faq_schema = '';
        if (!empty($params['faqs_enable'])) {
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_FAQS, 'Generando preguntas frecuentes...');
            $faqs_start = microtime(true);
            
            $faqs_result = $this->generate_faqs($params, $h1, $system_base, $context_manager);
            if (!empty($faqs_result['content'])) {
                $faqs_duration = microtime(true) - $faqs_start;
                $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_FAQS, 'FAQs generadas', null, null, $keyword, $faqs_duration, $faqs_result['cost']);
                
                $generated_content[] = $faqs_result['content'];
                $total_cost += $faqs_result['cost'];
                $total_tokens += $faqs_result['tokens'];
                
                // Guardar el schema para agregarlo al final
                if (!empty($faqs_result['schema'])) {
                    $faq_schema = $faqs_result['schema'];
                }
            }
        }

        // 4. Generar conclusión
        if (!empty($params['conclusion_enable'])) {
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_CONCLUSION, 'Generando conclusión...');
            $conclusion_start = microtime(true);
            
            $conclusion_result = $this->generate_conclusion($h1, $system_base, $context_manager);
            if (!empty($conclusion_result['content'])) {
                $conclusion_duration = microtime(true) - $conclusion_start;
                $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_CONCLUSION, 'Conclusión generada', null, null, $keyword, $conclusion_duration, $conclusion_result['cost']);
                
                $generated_content[] = $conclusion_result['content'];
                $total_cost += $conclusion_result['cost'];
                $total_tokens += $conclusion_result['tokens'];
            }
        }

        // 5. Generar referencias
        if (!empty($params['include_references'])) {
            $refs_result = $this->generate_references($params, $h1, $keyword, $system_base);
            if (!empty($refs_result['content'])) {
                $generated_content[] = $refs_result['content'];
                $total_cost += $refs_result['cost'];
                $total_tokens += $refs_result['tokens'];
            }
        }

        // Ensamblar contenido final
        $final_html = implode("\n\n", $generated_content);
        
        // Post-procesamiento: Limpiar bloques de código y errores
        $final_html = $this->clean_generated_content($final_html);

        // Crear post
        $post_id = $this->create_post($h1, $final_html, $params);
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Guardar Schema de FAQs como post meta si existe
        if (!empty($faq_schema)) {
            update_post_meta($post_id, '_asap_faq_schema', $faq_schema);
        }

        // Log de la generación
        if ($this->logger) {
            call_user_func($this->logger, [
                'type' => 'article',
                'action' => 'generate_article_by_sections',
                'post_id' => $post_id,
                'model' => $this->get_provider_name(),
                'tokens_input' => 0,
                'tokens_output' => 0,
                'tokens_total' => $total_tokens,
                'cost_usd' => $total_cost,
                'status' => 'success',
                'metadata' => [
                    'h1' => $h1,
                    'keyword' => $keyword,
                    'secondary_keywords' => $secondary_keywords,
                    'target_len' => $target_len,
                    'sections_count' => count($outline),
                    'generation_method' => 'by_sections_with_context',
                ],
            ]);
        }
        
        // Log final de duración total
        $total_duration = microtime(true) - $start_time;
        $gen_logger->success($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Artículo completo generado', [
            'post_id' => $post_id,
            'words' => str_word_count(strip_tags($final_html)),
            'sections' => count($outline),
        ], $post_id, $keyword, $total_duration, $total_cost);

        return [
            'post_id' => $post_id,
            'content' => $final_html,
            'cost_usd' => $total_cost,
            'tokens' => $total_tokens,
            'session_id' => $session_id,
        ];
    }
    
    /**
     * Construye prompt del sistema enriquecido con briefing
     * 
     * @param string $lang Idioma
     * @param string $style Estilo
     * @param array|null $briefing Briefing de SERP (opcional)
     * @return string Prompt del sistema
     */
    private function build_system_prompt($lang, $style, $briefing = null, $secondary_keywords = []) {
        // ✅ Mapear código de idioma a nombre EN ESPAÑOL (para que el prompt sea consistente)
        $lang_map = [
            // Idiomas principales
            'es' => 'español', 'en' => 'inglés', 'pt' => 'portugués', 'fr' => 'francés',
            'de' => 'alemán', 'it' => 'italiano', 'ru' => 'ruso', 'ja' => 'japonés',
            'zh' => 'chino', 'ko' => 'coreano', 'ar' => 'árabe', 'hi' => 'hindi',
            'bn' => 'bengalí', 'tr' => 'turco', 'id' => 'indonesio', 'ms' => 'malayo',
            'vi' => 'vietnamita', 'th' => 'tailandés', 'tl' => 'filipino', 'fa' => 'persa',
            'ur' => 'urdu',
            // Europa Occidental
            'nl' => 'holandés', 'sv' => 'sueco', 'no' => 'noruego', 'da' => 'danés',
            'fi' => 'finlandés', 'is' => 'islandés',
            // Europa del Este
            'pl' => 'polaco', 'uk' => 'ucraniano', 'cs' => 'checo', 'sk' => 'eslovaco',
            'hu' => 'húngaro', 'ro' => 'rumano', 'bg' => 'búlgaro', 'hr' => 'croata',
            'sr' => 'serbio', 'sl' => 'esloveno', 'mk' => 'macedonio', 'sq' => 'albanés',
            'et' => 'estonio', 'lv' => 'letón', 'lt' => 'lituano',
            // Lenguas regionales Europa
            'ca' => 'catalán', 'eu' => 'vasco', 'gl' => 'gallego', 'cy' => 'galés',
            'ga' => 'irlandés', 'mt' => 'maltés',
            // Mediterráneo y Medio Oriente
            'el' => 'griego', 'he' => 'hebreo',
            // Subcontinente Indio
            'ta' => 'tamil', 'te' => 'telugu', 'mr' => 'marathi', 'gu' => 'gujarati',
            'kn' => 'kannada', 'ml' => 'malayalam', 'pa' => 'punjabi', 'si' => 'cingalés',
            'ne' => 'nepalí',
            // Sudeste Asiático
            'my' => 'birmano', 'km' => 'camboyano', 'lo' => 'laosiano',
            // Asia Central y del Este
            'mn' => 'mongol',
            // África
            'sw' => 'suajili', 'am' => 'amhárico', 'ha' => 'hausa', 'yo' => 'yoruba',
            'ig' => 'igbo', 'zu' => 'zulú', 'xh' => 'xhosa', 'af' => 'afrikáans',
            'so' => 'somalí',
            // América Latina (variantes)
            'es-mx' => 'español mexicano', 'es-ar' => 'español argentino', 'es-co' => 'español colombiano',
            'es-cl' => 'español chileno', 'es-pe' => 'español peruano', 'es-ve' => 'español venezolano',
            'pt-br' => 'portugués brasileño',
            // Otros
            'eo' => 'esperanto', 'la' => 'latín'
        ];
        $lang_full = $lang_map[$lang] ?? 'español';
        
        $base = "Eres un redactor senior SEO y copywriter experto.\n\n";
        
        $base .= "🌍 IDIOMA OBLIGATORIO: **{$lang_full}**\n";
        $base .= "TODO el contenido (títulos, subtítulos, párrafos, listas, tablas) DEBE estar 100% en {$lang_full}.\n";
        $base .= "NUNCA mezcles idiomas. Si el idioma es English, TODO en inglés. Si es español, TODO en español.\n\n";
        
        $base .= "🎭 TONO Y ESTILO OBLIGATORIO: **{$style}**\n";
        $base .= "Este es el tono que DEBES mantener en TODO el artículo. Es tu guía principal de redacción.\n";
        $base .= "Si es 'informativo': objetivo, educativo, con datos.\n";
        $base .= "Si es 'conversacional': cercano, tú/usted, como hablando con un amigo.\n";
        $base .= "Si es 'persuasivo': enfocado en beneficios, CTAs, acción.\n";
        $base .= "Si es 'profesional/técnico': preciso, formal, con terminología específica.\n\n";
        
        // 🆕 KEYWORDS SECUNDARIAS (solo en System Prompt base, NO se pasan en cada sección)
        if (!empty($secondary_keywords)) {
            $keywords_list = implode(', ', array_slice($secondary_keywords, 0, 10));
            $base .= "🔑 KEYWORDS SECUNDARIAS DEL ARTÍCULO:\n";
            $base .= "{$keywords_list}\n\n";
            $base .= "📌 INSTRUCCIONES:\n";
            $base .= "- Te indicaré en cada sección qué keywords YA fueron usadas y cuáles están disponibles\n";
            $base .= "- SOLO usa keywords disponibles Y relevantes para el H2 actual\n";
            $base .= "- Si ninguna keyword disponible encaja con el tema del H2, NO uses ninguna\n";
            $base .= "- Máximo 1-2 keywords por sección, integradas de forma natural\n";
            $base .= "- Calidad del contenido > Incluir keywords\n\n";
        }
        
        $base .= "📝 REGLAS DE FORMATO (aplica con criterio):\n";
        $base .= "1. **Párrafos CORTOS**: 2-4 líneas por párrafo (40-60 palabras). Lectura cómoda en móviles.\n";
        $base .= "2. **Negritas moderadas**: Usa <strong> en 1-2 conceptos clave cada 2-3 párrafos. No exageres.\n";
        $base .= "3. **Variedad de formato**: Alterna entre párrafos narrativos, listas, tablas, ejemplos. NO repitas la misma estructura.\n";
        $base .= "4. **Listas selectivas**: Usa <ul><li> cuando realmente ayude (pasos, características, comparaciones).\n";
        $base .= "5. **Tablas cuando compares**: <table> para comparar opciones, precios, características similares.\n";
        $base .= "6. **HTML LIMPIO Y VÁLIDO**: Solo <p>, <h2>, <h3>, <ul>, <ol>, <li>, <table>, <tr>, <td>, <th>, <strong>, <em>. NUNCA uses bloques ```html.\n";
        $base .= "   ⚠️ CRÍTICO: SIEMPRE cierra TODAS las etiquetas. Si abres <strong>, DEBES cerrar </strong>. Si abres <li>, DEBES cerrar </li>.\n";
        $base .= "   ⚠️ NUNCA dejes párrafos o listas a la mitad. Completa TODA la oración antes de pasar al siguiente H2.\n";
        $base .= "   ⚠️ NUNCA cortes palabras a la mitad (ej: 'advert' debe ser 'advertencia'). Completa TODAS las palabras.\n";
        $base .= "   🔴 PROHIBIDO USAR MARKDOWN: NUNCA uses **texto** o __texto__. USA <strong>texto</strong> y <em>texto</em>.\n";
        $base .= "   ✅ CORRECTO: <strong>transformación y el crecimiento personal</strong>\n";
        $base .= "   ❌ INCORRECTO: **transformación y el crecimiento personal**\n";
        $base .= "   ✅ CORRECTO: <em>psyche</em>\n";
        $base .= "   ❌ INCORRECTO: *psyche*\n";
        $base .= "7. **Ejemplos prácticos**: Casos reales, números, estadísticas específicas.\n\n";
        
        $base .= "✅ OBJETIVO: Contenido profesional, escaneable, VARIADO entre secciones.\n\n";
        
        $base .= "❌ PROHIBIDO:\n";
        $base .= "- Párrafos de más de 60 palabras\n";
        $base .= "- Bloques ```html o código\n";
        $base .= "- Repetir el mismo formato en cada H2\n";
        $base .= "- Exceso de negritas (solo lo esencial)\n";
        $base .= "- Cortar palabras a la mitad por falta de tokens\n";
        $base .= "- Dejar etiquetas HTML abiertas (SIEMPRE cierra <strong>, <em>, <p>, <li>)\n";
        $base .= "- Usar sintaxis Markdown (**negrita** o *cursiva*). Usa HTML: <strong> y <em>\n\n";
        
        // 🆕 ENRIQUECER con datos de briefing
        if ($briefing) {
            $base .= "\n\n📊 ANÁLISIS DE COMPETENCIA (Top 10 Google):";
            $base .= "\n- Search Intent: {$briefing['search_intent']}";
            $base .= "\n- Longitud promedio top 5: {$briefing['target_metrics']['word_count']} palabras";
            $base .= "\n- H2 promedio: {$briefing['target_metrics']['h2_count']}";
            $base .= "\n- Tono recomendado: {$briefing['recommended_tone']}";
            
            // Temas obligatorios
            if (!empty($briefing['must_cover_topics'])) {
                $topics = array_slice($briefing['must_cover_topics'], 0, 5);
                $topic_list = implode(', ', array_column($topics, 'topic'));
                $base .= "\n\n✅ TEMAS OBLIGATORIOS A CUBRIR: {$topic_list}";
            }
            
            // People Also Ask (primeras 3)
            if (!empty($briefing['people_also_ask'])) {
                $paa_questions = array_slice($briefing['people_also_ask'], 0, 3);
                $paa_list = implode("\n", array_column($paa_questions, 'question'));
                $base .= "\n\n❓ PREGUNTAS QUE LA GENTE BUSCA (responde naturalmente en el contenido):\n{$paa_list}";
            }
            
            // Content gaps (oportunidades)
            if (!empty($briefing['content_gaps'])) {
                $gaps = implode(', ', array_slice($briefing['content_gaps'], 0, 3));
                $base .= "\n\n🎯 AGREGA VALOR ÚNICO con: {$gaps}";
            }
            
            // 🆕 ENTIDADES (marcas, conceptos, productos)
            if (!empty($briefing['entities']['must_mention'])) {
                $entity_context = $this->build_entities_context($briefing['entities']);
                if ($entity_context) {
                    $base .= "\n\n" . $entity_context;
                }
            }
            
            $base .= "\n\nEscribe mejor que los competidores: más completo, más claro, con ejemplos reales.";
        }
        
        return $base;
    }
    
    /**
     * Construye contexto de entidades para prompts
     * 
     * @param array $entities_data Datos de entidades
     * @return string Contexto formateado
     */
    private function build_entities_context($entities_data) {
        $must_mention = $entities_data['must_mention'] ?? [];
        
        if (empty($must_mention)) {
            return '';
        }
        
        // Agrupar por tipo
        $by_type = [];
        foreach (array_slice($must_mention, 0, 10) as $entity) {
            $type = $entity['type'] ?? 'concepts';
            if (!isset($by_type[$type])) {
                $by_type[$type] = [];
            }
            $by_type[$type][] = $entity['name'];
        }
        
        $lines = [];
        $lines[] = "🏷️ ENTIDADES CLAVE (mencionar naturalmente cuando sean relevantes):";
        
        $type_labels = [
            'brands' => 'Marcas',
            'products' => 'Productos',
            'concepts' => 'Conceptos técnicos',
            'people' => 'Expertos/Referencias',
        ];
        
        foreach ($by_type as $type => $names) {
            $label = $type_labels[$type] ?? ucfirst($type);
            $names_str = implode(', ', $names);
            $lines[] = "- {$label}: {$names_str}";
        }
        
        return implode("\n", $lines);
    }
    
    /**
     * Parsea y valida el outline
     */
    private function parse_outline($outline_json) {
        if (is_string($outline_json)) {
            $outline_arr = json_decode($outline_json, true);
        } else {
            $outline_arr = $outline_json;
        }
        
        // NO validar aquí si está vacío, porque generate_by_sections ya lo maneja
        // y genera automáticamente si es necesario
        if (!is_array($outline_arr)) {
            $outline_arr = [];
        }

        // Sanitizar outline
        $outline = [];
        foreach ($outline_arr as $row) {
            $h2 = isset($row['h2']) ? sanitize_text_field($row['h2']) : '';
            if (!$h2) continue;
            $h3s = [];
            if (isset($row['h3']) && is_array($row['h3'])) {
                foreach ($row['h3'] as $h3) {
                    $h3 = sanitize_text_field($h3);
                    if ($h3) $h3s[] = $h3;
                }
            }
            $outline[] = ['h2' => $h2, 'h3' => $h3s];
        }
        
        // NO validar si está vacío aquí - el método principal ya lo maneja
        // y genera H2s automáticamente si es necesario
        return $outline;
    }
    
    /**
     * Genera la introducción
     */
    private function generate_intro($params, $h1, $keyword, $outline, $system_base, $context_manager) {
        // Si hay intro personalizada, usarla
        if (!empty($params['intro_custom'])) {
            return [
                'content' => wp_kses_post($params['intro_custom']),
                'cost' => 0,
                'tokens' => 0
            ];
        }
        
        // SIEMPRE generar introducción (1-3 párrafos cortos obligatorios)
        $h2_list = array_column($outline, 'h2');
        $h2_preview = implode(', ', array_slice($h2_list, 0, 3));
        
        $intro_prompt = "Escribe una **introducción profesional y concisa** para el artículo: '{$h1}'\n\n";
        if ($keyword) $intro_prompt .= "Keyword principal: {$keyword}\n\n";
        
        $intro_prompt .= "📝 ESTRUCTURA OBLIGATORIA:\n";
        $intro_prompt .= "**1-3 párrafos cortos** (100-180 palabras máximo)\n\n";
        
        $intro_prompt .= "**Distribución recomendada:**\n";
        $intro_prompt .= "- **Párrafo 1** (50-60 palabras): Hook inicial - presenta el problema/pregunta que el lector tiene\n";
        $intro_prompt .= "- **Párrafo 2** (50-60 palabras): Promesa - qué aprenderá y por qué es valioso\n";
        $intro_prompt .= "- **Párrafo 3** (40-50 palabras, OPCIONAL): Preview breve de los temas principales\n\n";
        
        $intro_prompt .= "Temas principales del artículo: {$h2_preview}\n\n";
        
        $intro_prompt .= "✅ DEBE INCLUIR:\n";
        $intro_prompt .= "- Hook que conecte emocionalmente (pregunta, dato sorprendente, o problema común)\n";
        $intro_prompt .= "- Valor claro: qué problema resuelve este artículo\n";
        $intro_prompt .= "- OBLIGATORIO: Usa <strong> en 2-4 conceptos/palabras clave para énfasis visual (NO abuses, solo términos importantes)\n";
        $intro_prompt .= "- Párrafos cortos: máximo 50-60 palabras cada uno\n";
        $intro_prompt .= "- Tono directo y profesional\n\n";
        
        $intro_prompt .= "❌ EVITA:\n";
        $intro_prompt .= "- Bloques de código ```html\n";
        $intro_prompt .= "- Encabezados H2/H3 (solo párrafos <p>)\n";
        $intro_prompt .= "- Frases genéricas: 'En este artículo veremos...', 'A continuación te explicamos...'\n";
        $intro_prompt .= "- Párrafos largos (más de 60 palabras)\n";
        $intro_prompt .= "- Introducciones extensas (máximo 180 palabras)\n\n";
        
        $intro_prompt .= "💡 EJEMPLO DE USO DE <strong>:\n";
        $intro_prompt .= "❌ MAL: Sin negritas o todo en negrita\n";
        $intro_prompt .= "✅ BIEN: '¿Alguna vez te has preguntado cómo <strong>recuperar a tu ex</strong>? La <strong>comunicación efectiva</strong> y el <strong>crecimiento personal</strong> son claves fundamentales...'\n";
        
        $intro_response = $this->call_ai($system_base, $intro_prompt, 500);
        
        if (is_wp_error($intro_response)) {
            return ['content' => '', 'cost' => 0, 'tokens' => 0];
        }
        
        $context_manager->add_note("Introducción completada.");
        
        return [
            'content' => trim($intro_response['content']),
            'cost' => $this->token_calculator->calculate_cost($intro_response['model'], $intro_response['usage']['prompt_tokens'], $intro_response['usage']['completion_tokens']),
            'tokens' => $intro_response['usage']['total_tokens']
        ];
    }
    
    /**
     * Genera una sección H2
     */
    private function generate_section($section, $index, $h1, $keyword, $words_per_h2, $system_base, $context_manager, $secondary_keywords = []) {
        $h2_title = $section['h2'];
        $h3_list = $section['h3'];
        
        // Construir prompt con contexto
        $section_prompt = "CONTEXTO PREVIO:\n";
        $section_prompt .= "Título del artículo: {$h1}\n";
        if ($keyword) $section_prompt .= "Keyword principal: {$keyword}\n";
        
        // 🆕 KEYWORDS TRACKING
        if (!empty($secondary_keywords)) {
            $used_kw = $context_manager->get_used_keywords();
            $available_kw = $context_manager->get_available_keywords($secondary_keywords);
            
            if (!empty($used_kw)) {
                $section_prompt .= "✅ Keywords secundarias YA usadas: " . implode(', ', $used_kw) . "\n";
            }
            if (!empty($available_kw)) {
                $section_prompt .= "🔓 Keywords secundarias DISPONIBLES: " . implode(', ', $available_kw) . "\n";
                $section_prompt .= "(Usa 1-2 si son relevantes para este H2, o ninguna si no encajan)\n";
            } else {
                $section_prompt .= "ℹ️ Todas las keywords secundarias ya fueron usadas.\n";
            }
            $section_prompt .= "\n";
        }
        
        if ($context_manager->has_context()) {
            $section_prompt .= "Contenido previo:\n{$context_manager->get_summary()}\n";
        }
        
        $section_prompt .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $section_prompt .= "ESCRIBE LA SIGUIENTE SECCIÓN:\n";
        $section_prompt .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $section_prompt .= "📌 H2: {$h2_title}\n";
        
        if (!empty($h3_list)) {
            $section_prompt .= "📌 Subsecciones H3: " . implode(', ', $h3_list) . "\n";
        }
        
        $section_prompt .= "\n📏 EXTENSIÓN: {$words_per_h2} palabras (±20%)\n";
        
        // Sugerencias de estilo rotativas (NO obligatorias, solo para variedad)
        $style_suggestions = [
            'Sugerencia: Enfoque narrativo con ejemplos concretos y casos de uso reales',
            'Sugerencia: Estructura con listas de viñetas para mayor claridad',
            'Sugerencia: Si aplica, tabla comparativa (solo si tiene sentido)',
            'Sugerencia: Formato educativo paso a paso si el tema lo permite',
            'Sugerencia: Incluye datos, porcentajes o estadísticas si es relevante',
        ];
        
        $current_suggestion = $style_suggestions[$index % count($style_suggestions)];
        
        $section_prompt .= "\n✍️ INSTRUCCIONES DE REDACCIÓN:\n";
        $section_prompt .= "1. Empieza DIRECTAMENTE con <h2>{$h2_title}</h2>\n";
        $section_prompt .= "   ⚠️ IMPORTANTE: Usa el H2 EXACTAMENTE como está escrito arriba, sin modificar mayúsculas/minúsculas.\n\n";
        
        $section_prompt .= "2. **VARIEDAD** (esta es la sección #{$index}):\n";
        $section_prompt .= "   {$current_suggestion}\n";
        $section_prompt .= "   IMPORTANTE: Esto es solo una sugerencia. Si no encaja con el H2, ignórala y elige el formato más apropiado.\n\n";
        
        $section_prompt .= "3. **FORMATO GENERAL**:\n";
        $section_prompt .= "   - Párrafos CORTOS de 2-4 líneas (40-60 palabras)\n";
        $section_prompt .= "   - Negritas <strong> MODERADAS: solo 1-2 conceptos clave cada 2-3 párrafos\n";
        $section_prompt .= "   - Ejemplos CONCRETOS con números, nombres, fechas\n\n";
        
        if (!empty($h3_list)) {
            $section_prompt .= "4. **ESTRUCTURA CON H3**:\n";
            $section_prompt .= "   - Para cada H3: 1 párrafo intro + contenido variado (lista O ejemplo O comparación)\n";
            $section_prompt .= "   - NO uses el mismo formato para todos los H3\n";
            $section_prompt .= "   - Varía: un H3 con lista, otro con ejemplo narrativo, otro con comparación\n\n";
        } else {
            $section_prompt .= "4. **ESTRUCTURA SIN H3**:\n";
            $section_prompt .= "   - 3-4 bloques de contenido variado\n";
            $section_prompt .= "   - Alterna formato: párrafo narrativo, luego lista, luego ejemplo, etc.\n\n";
        }
        
        $section_prompt .= "5. **DIVERSIDAD DE FORMATO**:\n";
        $section_prompt .= "   - NO todas las secciones deben tener el mismo formato\n";
        $section_prompt .= "   - Alterna entre enfoques: narrativo, listas, comparaciones, ejemplos\n";
        $section_prompt .= "   - Adapta el formato al contenido, no uses fórmulas repetitivas\n\n";
        
        $section_prompt .= "❌ EVITA:\n";
        $section_prompt .= "- Párrafos de más de 60 palabras\n";
        $section_prompt .= "- Bloques ```html\n";
        $section_prompt .= "- Repetir el mismo formato que otras secciones\n";
        $section_prompt .= "- Exceso de negritas (solo lo esencial)\n";
        $section_prompt .= "- Frases de relleno genéricas\n";
        
        if (!empty($h3_list)) {
            $section_prompt .= "\n💡 CÓMO ESTRUCTURAR LOS H3:\n";
            $section_prompt .= "- NO los presentes como '1. Primer H3, 2. Segundo H3, 3. Tercer H3'\n";
            $section_prompt .= "- ALTERNATIVA 1: Escribe párrafos introductorios, luego cada H3 con su desarrollo completo\n";
            $section_prompt .= "- ALTERNATIVA 2: Integra los H3 como subtemas naturales dentro de un flujo narrativo\n";
            $section_prompt .= "- ALTERNATIVA 3: Usa bullet points (<ul>) SOLO cuando sea lógico (listas de pasos, checklist)\n";
            $section_prompt .= "- Cada H3 debe tener 150-250 palabras de desarrollo, con ejemplos CONCRETOS\n";
        }
        
        $section_prompt .= "\n🎯 CALIDAD:\n";
        $section_prompt .= "- Escribe como un experto que explica a un colega, no como un robot\n";
        $section_prompt .= "- Usa lenguaje cercano pero profesional\n";
        $section_prompt .= "- Aporta valor real, no relleno\n";
        $section_prompt .= "- Si mencionas algo, DESARRÓLLALO con ejemplos\n";
        
        if ($index === 0 && $keyword) {
            $section_prompt .= "\n🔑 SEO: Menciona naturalmente '{$keyword}' 1-2 veces (no fuerces)\n";
        }
        
        $section_prompt .= "\n📦 OUTPUT: SOLO el HTML de esta sección (sin texto introductorio, sin explicaciones, sin conclusión general)\n";

        // ⚠️ CRÍTICO: MÍNIMO 1500 tokens por sección para evitar cortes
        // Aunque pidas 1000 palabras total, cada sección necesita espacio para completarse
        $calculated_tokens = intval($words_per_h2 * 2.5);
        $min_tokens = 1500; // MÍNIMO absoluto
        $max_tokens = 3500; // MÁXIMO para artículos largos
        $section_tokens = max($min_tokens, min($max_tokens, $calculated_tokens));
        
        $section_response = $this->call_ai($system_base, $section_prompt, $section_tokens);
        
        if (is_wp_error($section_response)) {
            // NO mostrar errores de timeout en el contenido - mejor omitir la sección
            // El log ya registró el error
            return [
                'content' => '', // Sección vacía - no mostrar mensaje de error
                'cost' => 0,
                'tokens' => 0
            ];
        }
        
        $section_html = trim($section_response['content']);
        
        // Actualizar contexto (detecta automáticamente keywords usadas)
        $context_manager->add_section($h2_title, $section_html, $secondary_keywords);
        
        return [
            'content' => $section_html,
            'cost' => $this->token_calculator->calculate_cost($section_response['model'], $section_response['usage']['prompt_tokens'], $section_response['usage']['completion_tokens']),
            'tokens' => $section_response['usage']['total_tokens']
        ];
    }
    
    /**
     * Genera FAQs
     */
    private function generate_faqs($params, $h1, $system_base, $context_manager) {
        $faqs_count = intval($params['faqs_count'] ?? 5);
        
        $faqs_prompt = "Genera {$faqs_count} preguntas frecuentes (FAQs) para el artículo '{$h1}'.\n\n";
        
        $faqs_prompt .= "📖 CONTENIDO YA CUBIERTO EN EL ARTÍCULO:\n";
        $faqs_prompt .= "{$context_manager->get_summary()}\n\n";
        
        $faqs_prompt .= "📝 FORMATO OBLIGATORIO:\n";
        $faqs_prompt .= "<h2>Preguntas frecuentes</h2>\n\n";
        $faqs_prompt .= "<h3>¿Pregunta directa aquí?</h3>\n";
        $faqs_prompt .= "<p>Respuesta corta y directa en 40-60 palabras con <strong>conceptos clave</strong> resaltados.</p>\n\n";
        
        $faqs_prompt .= "✅ REQUISITOS:\n";
        $faqs_prompt .= "1. **NO escribas 'Pregunta 1:', 'Pregunta 2:'** - Solo la pregunta directa en H3\n";
        $faqs_prompt .= "2. Preguntas naturales que la gente realmente buscaría\n";
        $faqs_prompt .= "3. Respuestas de 40-60 palabras (2-3 oraciones máximo)\n";
        $faqs_prompt .= "4. Usa <strong> en 1-2 palabras clave por respuesta (moderado)\n";
        $faqs_prompt .= "5. Si una respuesta tiene puntos, usa lista <ul><li>\n\n";
        
        $faqs_prompt .= "❌ NO REPITAS:\n";
        $faqs_prompt .= "- Información ya cubierta en las secciones anteriores\n";
        $faqs_prompt .= "- Responde dudas complementarias, no resúmenes de lo ya dicho\n\n";
        
        $faqs_prompt .= "💡 Ejemplos de preguntas útiles:\n";
        $faqs_prompt .= "- ¿Cuánto tiempo tarda en...?\n";
        $faqs_prompt .= "- ¿Es mejor X o Y?\n";
        $faqs_prompt .= "- ¿Qué pasa si...?\n";
        $faqs_prompt .= "- ¿Dónde puedo...?\n";
        
        $faqs_response = $this->call_ai($system_base, $faqs_prompt, 1000);
        
        if (is_wp_error($faqs_response)) {
            return ['content' => '', 'cost' => 0, 'tokens' => 0, 'schema' => ''];
        }
        
        $faqs_html = trim($faqs_response['content']);
        
        // Extraer preguntas y respuestas para el schema
        $schema_data = $this->extract_faqs_for_schema($faqs_html);
        
        return [
            'content' => $faqs_html,
            'cost' => $this->token_calculator->calculate_cost($faqs_response['model'], $faqs_response['usage']['prompt_tokens'], $faqs_response['usage']['completion_tokens']),
            'tokens' => $faqs_response['usage']['total_tokens'],
            'schema' => $schema_data
        ];
    }
    
    /**
     * Extrae preguntas y respuestas del HTML para generar Schema.org FAQPage
     */
    private function extract_faqs_for_schema($html) {
        if (empty($html)) {
            return '';
        }
        
        // Crear DOMDocument para parsear el HTML
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $questions = [];
        $h3_elements = $dom->getElementsByTagName('h3');
        
        foreach ($h3_elements as $h3) {
            $question = trim($h3->textContent);
            
            // Buscar el siguiente elemento (debería ser la respuesta)
            $answer_html = '';
            $next = $h3->nextSibling;
            
            while ($next) {
                if ($next->nodeType === XML_ELEMENT_NODE) {
                    if ($next->nodeName === 'h3' || $next->nodeName === 'h2') {
                        // Llegamos a la siguiente pregunta o sección
                        break;
                    }
                    // Acumular contenido de la respuesta
                    $answer_html .= $dom->saveHTML($next);
                }
                $next = $next->nextSibling;
            }
            
            if (!empty($question) && !empty($answer_html)) {
                $questions[] = [
                    'name' => $question,
                    'text' => trim(strip_tags($answer_html))
                ];
            }
        }
        
        if (empty($questions)) {
            return '';
        }
        
        // Generar Schema.org JSON-LD (solo el JSON, sin el script tag)
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => []
        ];
        
        foreach ($questions as $qa) {
            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $qa['name'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $qa['text']
                ]
            ];
        }
        
        // Retornar solo el JSON, se agregará al head via wp_head
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Genera conclusión
     */
    private function generate_conclusion($h1, $system_base, $context_manager) {
        $conclusion_prompt = "Escribe una conclusión profesional para el artículo '{$h1}'.\n\n";
        
        $conclusion_prompt .= "📖 CONTENIDO DEL ARTÍCULO:\n";
        $conclusion_prompt .= "{$context_manager->get_summary()}\n\n";
        
        $conclusion_prompt .= "📝 ESTRUCTURA OBLIGATORIA:\n";
        $conclusion_prompt .= "1. **Párrafo 1** (50 palabras): Recapitula los 3 puntos más importantes del artículo\n";
        $conclusion_prompt .= "2. **Párrafo 2** (40 palabras): Beneficios concretos que el lector puede lograr aplicando esto\n";
        $conclusion_prompt .= "3. **Párrafo 3** (30 palabras): Call-to-action claro y específico (próximo paso a tomar)\n\n";
        
        $conclusion_prompt .= "✅ DEBE INCLUIR:\n";
        $conclusion_prompt .= "- <h2>Conclusión</h2>\n";
        $conclusion_prompt .= "- Párrafos CORTOS (máximo 50 palabras cada uno)\n";
        $conclusion_prompt .= "- <strong> en 1-2 conceptos clave solamente\n";
        $conclusion_prompt .= "- CTA motivador que invite a la acción\n\n";
        
        $conclusion_prompt .= "❌ PROHIBIDO:\n";
        $conclusion_prompt .= "- Bloques ```html\n";
        $conclusion_prompt .= "- Frases cliché como 'en resumen...', 'en conclusión...'\n";
        $conclusion_prompt .= "- Información nueva que no se haya mencionado antes\n";
        $conclusion_prompt .= "- Repetir textualmente párrafos anteriores\n";
        
        $conclusion_response = $this->call_ai($system_base, $conclusion_prompt, 500);
        
        if (is_wp_error($conclusion_response)) {
            return ['content' => '', 'cost' => 0, 'tokens' => 0];
        }
        
        return [
            'content' => trim($conclusion_response['content']),
            'cost' => $this->token_calculator->calculate_cost($conclusion_response['model'], $conclusion_response['usage']['prompt_tokens'], $conclusion_response['usage']['completion_tokens']),
            'tokens' => $conclusion_response['usage']['total_tokens']
        ];
    }
    
    /**
     * Genera referencias
     */
    private function generate_references($params, $h1, $keyword, $system_base) {
        $custom_refs = $params['custom_references'] ?? '';
        
        if (!empty($custom_refs)) {
            $refs_lines = preg_split('/\r\n|\r|\n/', trim($custom_refs));
            $refs_html = "<h2>Referencias y Fuentes</h2>\n<ul>";
            
            // ✅ Eliminar duplicados
            $unique_urls = [];
            foreach ($refs_lines as $url) {
                $url = trim($url);
                if (filter_var($url, FILTER_VALIDATE_URL) && !in_array($url, $unique_urls)) {
                    $unique_urls[] = $url;
                }
            }
            
            foreach ($unique_urls as $url) {
                $domain = parse_url($url, PHP_URL_HOST);
                // ✅ Solo mostrar el dominio como enlace, sin texto descriptivo
                $refs_html .= "<li><a href='" . esc_url($url) . "' target='_blank' rel='noopener nofollow'>" . esc_html($domain) . "</a></li>";
            }
            $refs_html .= "</ul>";
            return ['content' => $refs_html, 'cost' => 0, 'tokens' => 0];
        }
        
        // Buscar URLs reales y verificadas
        $lang = $params['lang'] ?? 'es';
        $real_urls = $this->search_real_references($keyword, $h1, $lang);
        
        if (!empty($real_urls)) {
            // ✅ Eliminar duplicados por URL
            $unique_urls = [];
            $seen_urls = [];
            
            foreach ($real_urls as $ref) {
                $url = $ref['url'];
                if (!in_array($url, $seen_urls)) {
                    $unique_urls[] = $ref;
                    $seen_urls[] = $url;
                }
            }
            
            // Usar URLs encontradas (ya verificadas)
            $refs_html = "<h2>Referencias y Fuentes</h2>\n<ul>";
            foreach ($unique_urls as $ref) {
                // ✅ Solo mostrar el título como enlace, sin descripción
                $refs_html .= sprintf(
                    "<li><a href='%s' target='_blank' rel='noopener nofollow'>%s</a></li>\n",
                    esc_url($ref['url']),
                    esc_html($ref['title'])
                );
            }
            $refs_html .= "</ul>";
            
            return ['content' => $refs_html, 'cost' => 0, 'tokens' => 0];
        }
        
        // Fallback: Solo mencionar que debe agregar referencias manualmente
        $refs_html = "<h2>Referencias y Fuentes</h2>\n";
        $refs_html .= "<p><em>Para mejorar la credibilidad del artículo, considera agregar referencias a fuentes confiables relacionadas con {$keyword}.</em></p>";
        
        return ['content' => $refs_html, 'cost' => 0, 'tokens' => 0];
    }
    
    /**
     * Busca URLs reales verificadas para referencias (SOLO AUTORIDAD, NO COMPETENCIA)
     * 
     * @param string $keyword Palabra clave
     * @param string $h1 Título del artículo
     * @return array URLs verificadas
     */
    private function search_real_references($keyword, $h1, $lang = 'es') {
        $verified_urls = [];
        $seen_domains = []; // ✅ Para evitar duplicados de Wikipedia
        
        // ✅ ESTRATEGIA NUEVA: Usar ValueSERP para buscar sitios de autoridad relacionados
        $valueserp_key = get_option('asap_ia_valueserp_api_key', '');
        
        if (!empty($valueserp_key)) {
            // 1. Buscar en sitios de autoridad usando ValueSERP
            $authority_results = $this->search_authority_with_valueserp($keyword, $valueserp_key);
            if (!empty($authority_results)) {
                $verified_urls = array_merge($verified_urls, $authority_results);
            }
        }
        
        // 2. Si no hay ValueSERP o no encontró suficientes, buscar en Wikipedia (solo idioma de generación)
        if (count($verified_urls) < 3) {
            $wiki_results = $this->search_wikipedia($keyword, $lang);
            if (!empty($wiki_results)) {
                foreach ($wiki_results as $result) {
                    $domain = parse_url($result['url'], PHP_URL_HOST);
                    if (!in_array($domain, $seen_domains)) {
                        $verified_urls[] = $result;
                        $seen_domains[] = $domain;
                    }
                }
            }
        }
        
        // 3. Fallback a URLs genéricas solo si no se encontró NADA
        if (empty($verified_urls)) {
            $generic_sources = $this->get_generic_trusted_sources($keyword);
            foreach ($generic_sources as $source) {
                if ($this->verify_url_exists($source['url'])) {
                    $verified_urls[] = $source;
                    if (count($verified_urls) >= 3) break;
                }
            }
        }
        
        // Limitar a máximo 5 referencias
        return array_slice($verified_urls, 0, 5);
    }
    
    /**
     * Busca en Wikipedia API (solo en el idioma especificado)
     */
    private function search_wikipedia($keyword, $lang = 'es') {
        $results = [];
        
        // ✅ Usar nueva función de limpieza
        $clean_keyword = $this->clean_keyword_for_search($keyword);
        
        // ✅ Solo buscar en el idioma de generación (no mezclar idiomas)
        $langs = [$lang]; // Solo el idioma especificado
        
        foreach ($langs as $current_lang) {
            if (count($results) >= 2) break;
            
            $endpoint = add_query_arg([
                'action' => 'opensearch',
                'search' => $clean_keyword,
                'limit' => 3,
                'namespace' => 0,
                'format' => 'json',
            ], "https://{$current_lang}.wikipedia.org/w/api.php");
            
            $response = wp_remote_get($endpoint, ['timeout' => 8]);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                
                if (!empty($data[1]) && !empty($data[2]) && !empty($data[3])) {
                    $titles = $data[1];
                    $descriptions = $data[2];
                    $urls = $data[3];
                    
                    for ($i = 0; $i < count($titles); $i++) {
                        if (isset($urls[$i]) && $this->verify_url_exists($urls[$i])) {
                            $results[] = [
                                'url' => $urls[$i],
                                'title' => $titles[$i] . ' - Wikipedia',
                                'description' => $descriptions[$i] ?? "Información sobre {$clean_keyword}"
                            ];
                            
                            if (count($results) >= 2) break 2; // Salir de ambos loops
                        }
                    }
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Busca en Wikidata
     */
    private function search_wikidata($keyword) {
        // Usar tema general para mejor match
        $general_topic = $this->extract_general_topic($keyword);
        
        $endpoint = add_query_arg([
            'action' => 'wbsearchentities',
            'search' => $general_topic,
            'language' => 'es',
            'limit' => 2,
            'format' => 'json',
        ], 'https://www.wikidata.org/w/api.php');
        
        $response = wp_remote_get($endpoint, ['timeout' => 8]);
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        $results = [];
        
        if (!empty($data['search'])) {
            foreach ($data['search'] as $item) {
                $id = $item['id'] ?? '';
                $label = $item['label'] ?? '';
                $description = $item['description'] ?? '';
                
                if ($id) {
                    $url = "https://www.wikidata.org/wiki/{$id}";
                    $results[] = [
                        'url' => $url,
                        'title' => $label . ' - Wikidata',
                        'description' => $description ?: "Datos estructurados sobre {$general_topic}"
                    ];
                    
                    if (count($results) >= 1) break;
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Busca SOLO en sitios de autoridad (.gov, .edu, .org específicos) - NO competencia
     */
    /**
     * ✅ NUEVA ESTRATEGIA: Buscar sitios de autoridad relacionados con el tema usando ValueSERP
     * NO incluye blogs ni sitios que puedan ser competencia directa
     */
    private function search_authority_with_valueserp($keyword, $valueserp_key) {
        $results = [];
        
        // ✅ Limpiar keyword para obtener el tema principal
        $clean_keyword = $this->clean_keyword_for_search($keyword);
        
        // ✅ Buscar en sitios de autoridad específicos (NO competencia)
        $authority_sites = [
            'wikipedia.org',
            '*.edu',
            '*.gov',
            'britannica.com',
            'rae.es',
            'microsoft.com',
            'apple.com',
            'github.com',
            'stackoverflow.com',
        ];
        
        // Hacer una búsqueda combinada con OR para ser más eficiente
        $site_query = implode(' OR ', array_map(function($site) {
            return "site:{$site}";
        }, $authority_sites));
        
        $query = "({$site_query}) {$clean_keyword}";
        
        $endpoint = add_query_arg([
            'api_key' => $valueserp_key,
            'q' => $query,
            'location' => 'Spain',
            'google_domain' => 'google.es',
            'gl' => 'es',
            'hl' => 'es',
            'num' => 10,
        ], 'https://api.valueserp.com/search');
        
        $response = wp_remote_get($endpoint, ['timeout' => 10]);
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $serp_results = $body['organic_results'] ?? [];
            
            foreach ($serp_results as $item) {
                $url = $item['link'] ?? '';
                $title = $item['title'] ?? '';
                $snippet = $item['snippet'] ?? '';
                
                if ($url && $title && $this->is_trusted_domain($url)) {
                    $results[] = [
                        'url' => $url,
                        'title' => $title,
                        'description' => $snippet ?: "Fuente de autoridad sobre {$clean_keyword}"
                    ];
                    
                    if (count($results) >= 5) break;
                }
            }
        }
        
        return $results;
    }
    
    /**
     * ✅ Limpia la keyword para búsqueda (quita palabras interrogativas pero mantiene el tema)
     * Ejemplo: "qué es windows 7" → "windows 7"
     */
    private function clean_keyword_for_search($keyword) {
        $clean = strtolower(trim($keyword));
        
        // Quitar SOLO palabras interrogativas al inicio
        $interrogatives = ['qué es', 'que es', 'cómo', 'como', 'cuál es', 'cual es', 'por qué', 'porque', 'dónde', 'donde', 'cuándo', 'cuando'];
        
        foreach ($interrogatives as $word) {
            if (strpos($clean, $word) === 0) {
                $clean = trim(substr($clean, strlen($word)));
                break;
            }
        }
        
        return $clean;
    }
    
    /**
     * Extrae el tema general de una keyword longtail
     * 
     * Ejemplo:
     * - "cómo recuperar a mi ex novio" → "relaciones pareja"
     * - "mejores zapatillas para correr marathon" → "running zapatillas deportivas"
     * - "recetas de postres sin azúcar para diabéticos" → "nutrición diabetes"
     */
    private function extract_general_topic($keyword) {
        // Normalizar
        $clean = strtolower(trim($keyword));
        
        // Quitar palabras comunes de longtail
        $stopwords = [
            'cómo', 'como', 'qué', 'que', 'cuál', 'cual', 'cuáles', 'cuales',
            'por qué', 'porque', 'dónde', 'donde', 'cuándo', 'cuando',
            'mejor', 'mejores', 'para', 'de', 'del', 'la', 'el', 'los', 'las',
            'mi', 'tu', 'su', 'más', 'muy', 'sin', 'con', 'en', 'a', 'y',
            'guía', 'completa', 'definitiva', 'paso', 'pasos', 'consejos',
            'tips', 'trucos', 'tutorial', 'aprende', 'aprender',
        ];
        
        // Separar palabras
        $words = preg_split('/\s+/', $clean);
        
        // Filtrar stopwords
        $filtered = array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && strlen($word) > 2;
        });
        
        // Tomar las 2-3 palabras más significativas
        $important_words = array_slice($filtered, 0, 3);
        
        // Si quedó muy corto, devolver la keyword original (primera mitad)
        if (count($important_words) < 2) {
            $half = array_slice($words, 0, ceil(count($words) / 2));
            return implode(' ', $half);
        }
        
        return implode(' ', $important_words);
    }
    
    /**
     * Verifica si un dominio es confiable
     */
    private function is_trusted_domain($url) {
        $domain = parse_url($url, PHP_URL_HOST);
        
        // Dominios educativos, gubernamentales, organizaciones reconocidas
        $trusted_patterns = [
            '\.edu$',
            '\.gov$',
            '\.org$',
            'wikipedia\.org',
            'britannica\.com',
            'nature\.com',
            'sciencedirect\.com',
            'ncbi\.nlm\.nih\.gov',
            'who\.int',
            'cdc\.gov',
            'harvard\.edu',
            'mit\.edu',
            'stanford\.edu',
        ];
        
        foreach ($trusted_patterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $domain)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verifica si una URL existe y responde correctamente
     */
    private function verify_url_exists($url) {
        $response = wp_remote_head($url, [
            'timeout' => 5,
            'redirection' => 5,
            'user-agent' => 'Mozilla/5.0 (compatible; WordPress)',
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        return ($code >= 200 && $code < 400);
    }
    
    /**
     * Obtiene fuentes genéricas confiables basadas en la keyword
     */
    private function get_generic_trusted_sources($keyword) {
        // Normalizar keyword
        $clean_keyword = strtolower(trim($keyword));
        
        // URLs genéricas pero confiables
        $sources = [];
        
        // Wikipedia en español (más probable que exista)
        $wiki_search = str_replace(' ', '_', ucwords($clean_keyword));
        $sources[] = [
            'url' => "https://es.wikipedia.org/wiki/{$wiki_search}",
            'title' => "Wikipedia - {$keyword}",
            'description' => "Información enciclopédica sobre {$keyword}"
        ];
        
        // Wikipedia en inglés (segunda opción)
        $sources[] = [
            'url' => "https://en.wikipedia.org/wiki/{$wiki_search}",
            'title' => "Wikipedia (EN) - {$keyword}",
            'description' => "Encyclopedia information about {$keyword}"
        ];
        
        // Britannica
        $brit_search = str_replace(' ', '-', $clean_keyword);
        $sources[] = [
            'url' => "https://www.britannica.com/topic/{$brit_search}",
            'title' => "Encyclopædia Britannica - {$keyword}",
            'description' => "Comprehensive information about {$keyword}"
        ];
        
        return $sources;
    }
    
    /**
     * Trunca texto a una longitud específica
     */
    private function truncate($text, $length) {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
    
    /**
     * Limpia el contenido generado de bloques de código y errores
     * 
     * @param string $content Contenido HTML
     * @return string Contenido limpio
     */
    private function clean_generated_content($content) {
        // 1. Quitar bloques ```html
        $content = preg_replace('/```html\s*/i', '', $content);
        $content = preg_replace('/```\s*$/m', '', $content);
        
        // 2. Quitar cualquier bloque ``` que haya quedado
        $content = preg_replace('/```[a-z]*\s*/i', '', $content);
        $content = str_replace('```', '', $content);
        
        // 3. Corregir HTML mal formado: &lt;/ y &gt; escapados incorrectamente
        $content = preg_replace('/&lt;\/\s*([a-z]+)?\s*$/i', '', $content); // Eliminar &lt;/ al final
        $content = preg_replace('/&lt;\/([a-z]+)&gt;/i', '</$1>', $content); // Convertir &lt;/tag&gt; a </tag>
        $content = preg_replace('/&lt;([a-z]+)&gt;/i', '<$1>', $content); // Convertir &lt;tag&gt; a <tag>
        
        // 4. Corregir etiquetas HTML rotas: <strong>texto&lt;/ → <strong>texto</strong>
        $content = preg_replace('/<(strong|em|b|i|u)>([^<]*?)&lt;\/\s*<\/(strong|em|b|i|u)>/i', '<$1>$2</$1>', $content);
        
        // 5. Eliminar fragmentos de etiquetas rotas al final de párrafos
        $content = preg_replace('/<\/p>\s*&lt;\/\s*$/m', '</p>', $content);
        
        // 6. Quitar espacios múltiples entre párrafos
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        
        // 7. Trim general
        $content = trim($content);
        
        return $content;
    }
    
    /**
     * Corrige HTML mal formado usando DOMDocument y regex
     * 
     * @param string $html HTML potencialmente mal formado
     * @return string HTML corregido
     */
    private function fix_broken_html($html) {
        // Si está vacío, retornar
        if (empty($html)) {
            return $html;
        }
        
        // 1. CORRECCIONES PRE-PROCESAMIENTO
        
        // Eliminar <br> seguido de <h2> o <h3> (no tiene sentido)
        $html = preg_replace('/<br\s*\/?>\s*<(h[2-6])/i', '<$1', $html);
        
        // ⚠️ CRÍTICO: Cerrar <strong> abiertos ANTES de <h2>/<h3>
        $html = preg_replace('/<strong>\s*(<h[2-6])/i', '</strong>$1', $html);
        
        // Cerrar párrafos incompletos antes de H2/H3
        $html = preg_replace('/(<p>[^<]*?)(<h[2-6])/i', '$1</p>$2', $html);
        
        // Cerrar listas incompletas antes de H2/H3
        $html = preg_replace('/(<li>[^<]*?)(<h[2-6])/i', '$1</li></ul>$2', $html);
        
        // Cerrar <strong>, <em>, <b>, <i> huérfanos dentro de párrafos
        $html = preg_replace('/<(strong|em|b|i|u)>([^<]*?)(<\/(?:p|li|h[1-6]|div)>)/i', '<$1>$2</$1>$3', $html);
        
        // Eliminar etiquetas de apertura huérfanas al final
        $html = preg_replace('/<(strong|em|b|i|u|p|li|ul|ol)>\s*$/i', '', $html);
        
        // ⚠️ CRÍTICO: Detectar y cerrar <strong> que abarcan múltiples H2s
        // Si hay un <strong> seguido de múltiples <h2>, cerrarlo antes del primer <h2>
        $html = preg_replace('/<strong>(\s*<\/p>)?\s*(<h[2-6])/i', '</strong>$1$2', $html);
        
        // Cerrar párrafos que quedaron abiertos al final
        if (substr_count($html, '<p>') > substr_count($html, '</p>')) {
            $html .= '</p>';
        }
        
        // Cerrar listas que quedaron abiertas
        if (substr_count($html, '<ul>') > substr_count($html, '</ul>')) {
            $html .= '</li></ul>';
        }
        if (substr_count($html, '<ol>') > substr_count($html, '</ol>')) {
            $html .= '</li></ol>';
        }
        
        // Cerrar <strong> huérfanos
        if (substr_count($html, '<strong>') > substr_count($html, '</strong>')) {
            $html .= '</strong>';
        }
        
        // 2. USAR DOMDocument PARA VALIDACIÓN FINAL
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        
        // Suprimir warnings de HTML mal formado
        libxml_use_internal_errors(true);
        
        // Cargar HTML con encoding UTF-8
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Limpiar errores
        libxml_clear_errors();
        
        // Guardar HTML corregido
        $fixed_html = $dom->saveHTML();
        
        // 3. POST-PROCESAMIENTO
        
        // Eliminar el XML encoding que agregamos
        $fixed_html = str_replace('<?xml encoding="UTF-8">', '', $fixed_html);
        
        // Eliminar DOCTYPE si se agregó
        $fixed_html = preg_replace('/<!DOCTYPE[^>]*>/i', '', $fixed_html);
        
        // Limpiar etiquetas html y body si se agregaron
        $fixed_html = preg_replace('/<\/?html[^>]*>/i', '', $fixed_html);
        $fixed_html = preg_replace('/<\/?body[^>]*>/i', '', $fixed_html);
        
        // Eliminar etiquetas vacías
        $fixed_html = preg_replace('/<(strong|em|b|i|u|p)>\s*<\/\1>/i', '', $fixed_html);
        
        // Limpiar espacios múltiples
        $fixed_html = preg_replace('/\n{3,}/', "\n\n", $fixed_html);
        
        return trim($fixed_html);
    }
    
    /**
     * Crea el post en WordPress
     */
    private function create_post($h1, $final_html, $params) {
        $author = intval($params['author'] ?? 0);
        $author_id = $author > 0 && get_user_by('id', $author) ? $author : get_current_user_id();
        
        // Asegurar que post_type sea válido (nunca attachment)
        $post_type = $params['post_type'] ?? 'post';
        if (empty($post_type) || $post_type === 'attachment' || !post_type_exists($post_type)) {
            $post_type = 'post';
        }
        
        $status_to_insert = $params['status'] ?? 'draft';
        
        // Validar y corregir HTML antes de insertar
        $final_html = $this->fix_broken_html($final_html);
        
        $post_id = wp_insert_post([
            'post_title'   => wp_strip_all_tags($h1),
            'post_content' => wp_kses_post($final_html),
            'post_status'  => $status_to_insert,
            'post_type'    => $post_type,
            'post_author'  => $author_id,
        ], true);
        
        // Marcar como generado por IA para el sistema de publicación automática
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_asap_ia_generated', '1');
            
            // ⭐ Asignar categorías si se especificaron
            if (!empty($params['categories']) && is_array($params['categories'])) {
                $categories = array_map('absint', $params['categories']);
                wp_set_post_categories($post_id, $categories);
            }
        }
        
        return $post_id;
    }
    
    /**
     * Genera estructura de H2s automáticamente usando IA
     */
    private function generate_automatic_outline($h1, $params, $session_id, $gen_logger) {
        $keyword = $params['keyword'] ?? '';
        $lang = $params['lang'] ?? 'es';
        $secondary_keywords = $params['secondary_keywords'] ?? [];
        
        try {
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, "Llamando a suggest_h2 con H1: '$h1', Keyword: '$keyword'");
            
            // Usar outline generator existente
            $outline_generator = new ASAP_IA_Generators_Outline_Generator(
                $this->openai_client,
                $this->token_calculator
            );
            
            // suggest_h2 acepta: ($h1, $keyword, $existing)
            $response = $outline_generator->suggest_h2($h1, $keyword, []);
            
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, "Respuesta de suggest_h2 recibida");
            
            if (is_wp_error($response)) {
                $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Error en suggest_h2: ' . $response->get_error_message());
                return $response;
            }
            
            if (!is_array($response) || empty($response['suggestions'])) {
                $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'suggest_h2 no devolvió sugerencias válidas. Response: ' . print_r($response, true));
                return new WP_Error('no_suggestions', 'No se generaron H2s.');
            }
            
            $gen_logger->info($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Sugerencias recibidas: ' . count($response['suggestions']) . ' H2s');
            
            // Convertir array de H2s a formato de outline
            $outline = [];
            foreach ($response['suggestions'] as $h2) {
                if (!empty($h2)) {
                    $outline[] = ['h2' => $h2, 'h3' => []];
                }
            }
            
            if (empty($outline)) {
                $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Todas las sugerencias estaban vacías');
                return new WP_Error('empty_suggestions', 'Todas las sugerencias de H2 estaban vacías.');
            }
            
            return $outline;
            
        } catch (Exception $e) {
            $gen_logger->error($session_id, ASAP_IA_Database_Generation_Logger::CAT_GENERATION, 'Excepción en generate_automatic_outline: ' . $e->getMessage());
            return new WP_Error('exception', 'Error inesperado al generar H2s: ' . $e->getMessage());
        }
    }
    
    /**
     * ✅ Actualiza el progreso en un transient para que el frontend pueda leerlo
     * 
     * @param string $session_id ID de sesión único
     * @param int $progress Porcentaje de progreso (0-100)
     * @param string $message Mensaje descriptivo
     */
    private function update_progress_transient($session_id, $progress, $message) {
        if (empty($session_id)) {
            return;
        }
        
        $transient_key = 'asap_gen_progress_' . $session_id;
        $data = [
            'progress' => min(100, max(0, intval($progress))),
            'message' => $message,
            'timestamp' => time()
        ];
        
        // Guardar por 5 minutos (suficiente para cualquier generación)
        set_transient($transient_key, $data, 300);
    }
}



