<?php
/**
 * Task Manager - Sistema de Background Processing con Polling
 * 
 * Divide procesos largos en etapas de < 30 segundos cada una
 * para evitar timeouts del servidor.
 * 
 * Sistema similar a:
 * - WordPress background processing
 * - WooCommerce importación por lotes
 * - Plugins de migración por etapas
 * 
 * FLUJO:
 * 1. Frontend: Inicia tarea → Recibe task_id
 * 2. Frontend: Hace polling cada 2 segundos
 * 3. Backend: Procesa UNA etapa (< 30 seg) → Actualiza estado
 * 4. Backend: Si hay más etapas, espera siguiente poll
 * 5. Backend: Si terminó, marca completado
 * 
 * @package ASAP_Theme
 * @subpackage IA\Background
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Background_Task_Manager {
    
    /**
     * Estados de tareas
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PAUSED = 'paused';        // Usuario pausó
    const STATUS_CANCELLED = 'cancelled';  // Usuario canceló
    const STATUS_COMPLETED = 'completed';
    const STATUS_ERROR = 'error';
    const STATUS_TIMEOUT = 'timeout';      // Etapa se colgó
    
    /**
     * Tipos de tareas
     */
    const TASK_CREATE_BRIEFING = 'create_briefing';
    const TASK_GENERATE_ARTICLE = 'generate_article';
    const TASK_SCRAPE_COMPETITORS = 'scrape_competitors';
    
    /**
     * Prefijo para opciones
     */
    const OPTION_PREFIX = 'asap_ia_task_';
    
    /**
     * Tiempo máximo por etapa (segundos)
     */
    const MAX_STEP_TIME = 25; // 25 seg para dejar margen
    
    /**
     * Máximo de reintentos por etapa
     */
    const MAX_RETRIES = 3;
    
    /**
     * Timeout para detectar si etapa se colgó (segundos)
     */
    const STEP_TIMEOUT = 60; // Si una etapa tarda > 60 seg, se considera colgada
    
    /**
     * Crear nueva tarea
     * 
     * @param string $type Tipo de tarea
     * @param array $data Datos de la tarea
     * @return string Task ID
     */
    public function create_task($type, $data = []) {
        $task_id = uniqid('task_' . $type . '_', true);
        
        $task = [
            'id' => $task_id,
            'type' => $type,
            'status' => self::STATUS_PENDING,
            'data' => $data,
            'result' => null,
            'error' => null,
            'progress' => [
                'current_step' => 0,
                'total_steps' => $this->calculate_steps($type, $data),
                'percentage' => 0,
                'message' => 'Iniciando...',
            ],
            'retries' => [],              // Registro de reintentos por etapa
            'current_retry' => 0,          // Reintentos de la etapa actual
            'step_started_at' => null,     // Timestamp inicio de etapa actual
            'can_resume' => true,          // Si se puede reanudar
            'started_at' => null,
            'completed_at' => null,
            'created_at' => time(),
            'last_poll_at' => time(),      // Último poll (para detectar abandono)
        ];
        
        $this->save_task($task);
        
        return $task_id;
    }
    
    /**
     * Procesar siguiente etapa de una tarea
     * 
     * Esta función se llama desde AJAX polling
     * Procesa UNA etapa y retorna el estado
     * 
     * @param string $task_id Task ID
     * @return array Estado actualizado
     */
    public function process_next_step($task_id) {
        $task = $this->get_task($task_id);
        
        if (!$task) {
            return [
                'status' => self::STATUS_ERROR,
                'error' => 'Tarea no encontrada',
            ];
        }
        
        // Actualizar último poll (para detectar abandono)
        $task['last_poll_at'] = time();
        
        // Si ya está completada, retornar
        if ($task['status'] === self::STATUS_COMPLETED) {
            return $task;
        }
        
        // Si está pausada, retornar sin procesar
        if ($task['status'] === self::STATUS_PAUSED) {
            return $task;
        }
        
        // Si está cancelada, retornar
        if ($task['status'] === self::STATUS_CANCELLED) {
            return $task;
        }
        
        // Si está en error y no tiene reintentos disponibles, retornar
        if ($task['status'] === self::STATUS_ERROR && $task['current_retry'] >= self::MAX_RETRIES) {
            return $task;
        }
        
        // Verificar si la etapa actual se colgó (timeout)
        if ($task['status'] === self::STATUS_PROCESSING && $task['step_started_at']) {
            $elapsed = time() - $task['step_started_at'];
            if ($elapsed > self::STEP_TIMEOUT) {
                // Etapa se colgó, intentar reintentar
                $task['progress']['message'] = '⚠️ Timeout detectado, reintentando...';
                $task['current_retry']++;
                
                if ($task['current_retry'] >= self::MAX_RETRIES) {
                    $task['status'] = self::STATUS_TIMEOUT;
                    $task['error'] = 'La etapa ' . $task['progress']['current_step'] . ' excedió el tiempo límite después de ' . self::MAX_RETRIES . ' reintentos.';
                    $this->save_task($task);
                    return $task;
                }
                
                // Registrar reintento
                $task['retries'][] = [
                    'step' => $task['progress']['current_step'],
                    'reason' => 'timeout',
                    'timestamp' => time(),
                ];
            }
        }
        
        // Marcar como procesando si está pending o reanudando
        if ($task['status'] === self::STATUS_PENDING || $task['status'] === self::STATUS_ERROR) {
            $task['status'] = self::STATUS_PROCESSING;
            if (!$task['started_at']) {
                $task['started_at'] = time();
            }
            $task['step_started_at'] = time();
            $this->save_task($task);
        }
        
        // Procesar según tipo (con manejo de errores y reintentos)
        try {
            $task['step_started_at'] = time();
            $task = $this->process_task_step($task);
            
            // Si la etapa completó exitosamente, resetear contador de reintentos
            $task['current_retry'] = 0;
            
        } catch (Exception $e) {
            // Error en la etapa
            $task['current_retry']++;
            
            // Registrar reintento
            $task['retries'][] = [
                'step' => $task['progress']['current_step'],
                'reason' => 'exception',
                'error' => $e->getMessage(),
                'timestamp' => time(),
            ];
            
            if ($task['current_retry'] >= self::MAX_RETRIES) {
                // Agotar reintentos
                $task['status'] = self::STATUS_ERROR;
                $task['error'] = 'Error en etapa ' . $task['progress']['current_step'] . ' después de ' . self::MAX_RETRIES . ' reintentos: ' . $e->getMessage();
                $task['completed_at'] = time();
            } else {
                // Reintentar en el siguiente poll
                $task['status'] = self::STATUS_ERROR; // Temporal, siguiente poll reintentará
                $task['progress']['message'] = '⚠️ Error, reintentando... (' . $task['current_retry'] . '/' . self::MAX_RETRIES . ')';
            }
            
            $this->save_task($task);
            return $task;
        }
        
        // Guardar y retornar
        $this->save_task($task);
        
        return $task;
    }
    
    /**
     * Procesar una etapa según el tipo de tarea
     * 
     * @param array $task Tarea
     * @return array Tarea actualizada
     */
    private function process_task_step($task) {
        $type = $task['type'];
        $step = $task['progress']['current_step'];
        
        switch ($type) {
            case self::TASK_CREATE_BRIEFING:
                $task = $this->process_briefing_step($task, $step);
                break;
                
            case self::TASK_GENERATE_ARTICLE:
                $task = $this->process_article_step($task, $step);
                break;
                
            case self::TASK_SCRAPE_COMPETITORS:
                $task = $this->process_scraping_step($task, $step);
                break;
                
            default:
                throw new Exception('Tipo de tarea desconocido: ' . $type);
        }
        
        // Actualizar progreso
        $current = $task['progress']['current_step'];
        $total = $task['progress']['total_steps'];
        $task['progress']['percentage'] = $total > 0 ? round(($current / $total) * 100) : 0;
        
        // Si terminó todas las etapas
        if ($current >= $total) {
            $task['status'] = self::STATUS_COMPLETED;
            $task['completed_at'] = time();
            $task['progress']['message'] = '✅ Completado';
            $task['progress']['percentage'] = 100;
        }
        
        return $task;
    }
    
    /**
     * Procesar etapa de creación de briefing
     * 
     * ETAPAS:
     * 1. Análisis SERP (5 seg)
     * 2. Scraping URL 1 (8 seg)
     * 3. Scraping URL 2 (8 seg)
     * 4. Scraping URL 3 (8 seg)
     * 5. Entity extraction (6 seg)
     * 6. Build briefing (2 seg)
     */
    private function process_briefing_step($task, $step) {
        $keyword = $task['data']['keyword'] ?? '';
        $options = $task['data']['options'] ?? [];
        
        // Inicializar resultado si no existe
        if (!isset($task['result'])) {
            $task['result'] = [
                'serp_data' => null,
                'scraped_urls' => [],
                'extracted_entities' => [], // Entidades individuales por competidor
                'entities' => null,          // Entidades consolidadas
                'briefing' => null,
            ];
        }
        
        switch ($step) {
            case 0:
                // ETAPA 1: Análisis SERP (~8 seg)
                $task['progress']['message'] = '🔍 Analizando resultados de Google...';
                
                $serp_analyzer = new ASAP_IA_Research_SERP_Analyzer();
                $serp_data = $serp_analyzer->analyze($keyword, $options);
                
                if (is_wp_error($serp_data)) {
                    throw new Exception($serp_data->get_error_message());
                }
                
                $task['result']['serp_data'] = $serp_data;
                $task['progress']['current_step'] = 1;
                break;
                
            case 1:
            case 2:
            case 3:
                // ETAPAS 2-4: Scraping URLs (~8 seg cada uno)
                $url_index = $step - 1; // 0, 1, 2
                $task['progress']['message'] = "📄 Scraping competidor " . ($url_index + 1) . " de 3...";
                
                $serp_data = $task['result']['serp_data'];
                $top_urls = array_slice($serp_data['organic_results'] ?? [], 0, 10);
                
                if (isset($top_urls[$url_index])) {
                    $scraper = new ASAP_IA_Research_Competitor_Scraper();
                    $url_data = $top_urls[$url_index];
                    
                    $scraped = $scraper->scrape_url($url_data['link']);
                    
                    if (!is_wp_error($scraped)) {
                        $task['result']['scraped_urls'][] = [
                            'url' => $url_data['link'],
                            'title' => $url_data['title'] ?? '',
                            'position' => $url_index + 1,
                            'data' => $scraped,
                        ];
                    }
                }
                
                $task['progress']['current_step'] = $step + 1;
                break;
                
            case 4:
            case 5:
            case 6:
                // ETAPAS 5-7: Entity extraction por competidor (~20 seg cada uno)
                $comp_index = $step - 4; // 0, 1, 2
                $total_scraped = count($task['result']['scraped_urls']);
                
                if ($comp_index < $total_scraped) {
                    $task['progress']['message'] = "🏷️ Extrayendo entidades del competidor " . ($comp_index + 1) . "...";
                    
                    $competitor = $task['result']['scraped_urls'][$comp_index];
                    $extractor = new ASAP_IA_Research_Entity_Extractor();
                    
                    // Extraer de UNO solo (no todos de golpe)
                    $content = $competitor['data']['main_content_excerpt'] ?? '';
                    $title = $competitor['title'] ?? '';
                    $combined_text = $title . "\n\n" . mb_substr($content, 0, 1500);
                    
                    $result = $extractor->extract_from_text($combined_text, $keyword);
                    
                    if (!is_wp_error($result)) {
                        $task['result']['extracted_entities'][] = [
                            'url' => $competitor['url'],
                            'entities' => $result['entities'] ?? [],
                            'cost' => $result['cost'] ?? 0,
                        ];
                    }
                } else {
                    // Si no hay más competidores, solo avanzar
                    $task['progress']['message'] = "⏭️ Saltando extracción (no hay más competidores)...";
                }
                
                $task['progress']['current_step'] = $step + 1;
                break;
                
            case 7:
                // ETAPA 8: Consolidar entidades (~2 seg)
                $task['progress']['message'] = '🏷️ Consolidando entidades...';
                
                // Consolidar entidades extraídas individualmente
                $all_entities = [];
                $total_cost = 0;
                
                foreach ($task['result']['extracted_entities'] as $extracted) {
                    foreach ($extracted['entities'] as $entity) {
                        $key = strtolower($entity['name']);
                        
                        if (!isset($all_entities[$key])) {
                            $all_entities[$key] = [
                                'name' => $entity['name'],
                                'type' => $entity['type'],
                                'context' => $entity['context'] ?? '',
                                'count' => 1,
                                'urls' => [$extracted['url']],
                            ];
                        } else {
                            $all_entities[$key]['count']++;
                            $all_entities[$key]['urls'][] = $extracted['url'];
                        }
                    }
                    $total_cost += $extracted['cost'] ?? 0;
                }
                
                // Rankear y calcular must_mention
                uasort($all_entities, function($a, $b) {
                    return $b['count'] <=> $a['count'];
                });
                
                $total_competitors = count($task['result']['scraped_urls']);
                $must_mention = [];
                foreach ($all_entities as $entity) {
                    $percentage = ($entity['count'] / max(1, $total_competitors)) * 100;
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
                
                $task['result']['entities'] = [
                    'entities' => array_values($all_entities),
                    'by_type' => $by_type,
                    'must_mention' => $must_mention,
                    'total_unique' => count($all_entities),
                    'cost' => $total_cost,
                ];
                
                $task['progress']['current_step'] = 8;
                break;
                
            case 8:
                // ETAPA 9: Construir briefing (~2 seg)
                $task['progress']['message'] = '📊 Generando briefing final...';
                
                $builder = new ASAP_IA_Research_Briefing_Builder();
                
                // Construir briefing con datos ya procesados
                $competitors_data = [
                    'success' => $task['result']['scraped_urls'],
                    'failed' => [],
                    'total_time' => 0,
                ];
                
                $briefing = $builder->build_briefing_from_data(
                    $keyword,
                    $task['result']['serp_data'],
                    $competitors_data,
                    $task['result']['entities']
                );
                
                // Cachear briefing
                $builder->cache_briefing($keyword, $briefing);
                
                $task['result']['briefing'] = $briefing;
                $task['progress']['current_step'] = 9;
                $task['progress']['message'] = '✅ Briefing completado';
                break;
        }
        
        return $task;
    }
    
    /**
     * Procesar etapa de generación de artículo
     * 
     * ETAPAS:
     * 1. Generar introducción (15 seg)
     * 2. Generar sección 1 (20 seg)
     * 3. Generar sección 2 (20 seg)
     * ... una etapa por cada 2 secciones
     * N. Generar conclusión (10 seg)
     */
    private function process_article_step($task, $step) {
        $params = $task['data']['params'] ?? [];
        $outline = $params['outline'] ?? [];
        
        // Inicializar resultado si no existe
        if (!isset($task['result'])) {
            $task['result'] = [
                'sections' => [],
                'post_id' => null,
            ];
        }
        
        $generator = new ASAP_IA_Generators_Article_Generator();
        
        // ETAPA 0: Introducción
        if ($step === 0) {
            $task['progress']['message'] = '✍️ Escribiendo introducción...';
            
            $intro = $generator->generate_introduction($params);
            $task['result']['sections']['intro'] = $intro;
            $task['progress']['current_step'] = 1;
            return $task;
        }
        
        // ETAPAS 1-N: Secciones (2 por etapa)
        $total_sections = count($outline);
        $sections_per_step = 2;
        $section_steps = ceil($total_sections / $sections_per_step);
        
        if ($step <= $section_steps) {
            $start_idx = ($step - 1) * $sections_per_step;
            $end_idx = min($start_idx + $sections_per_step, $total_sections);
            
            // ✅ Mensaje más específico con nombre del H2
            if ($start_idx === $end_idx - 1 && isset($outline[$start_idx]['h2'])) {
                // Solo 1 sección en este paso
                $h2_title = $outline[$start_idx]['h2'];
                $task['progress']['message'] = "✍️ Escribiendo sección " . ($start_idx + 1) . " de {$total_sections}: {$h2_title}";
            } else {
                // 2 secciones en este paso
                $task['progress']['message'] = "✍️ Escribiendo secciones " . ($start_idx + 1) . "-{$end_idx} de {$total_sections}";
            }
            
            for ($i = $start_idx; $i < $end_idx; $i++) {
                if (isset($outline[$i])) {
                    $section_content = $generator->generate_section($params, $outline[$i], $i);
                    $task['result']['sections']["section_{$i}"] = $section_content;
                }
            }
            
            $task['progress']['current_step'] = $step + 1;
            return $task;
        }
        
        // ÚLTIMA ETAPA: Conclusión + Crear post
        if ($step === ($section_steps + 1)) {
            $task['progress']['message'] = '✍️ Finalizando artículo...';
            
            $conclusion = $generator->generate_conclusion($params);
            $task['result']['sections']['conclusion'] = $conclusion;
            
            // Ensamblar y crear post
            $full_content = $generator->assemble_article($task['result']['sections']);
            $post_id = $generator->create_post($params['h1'], $full_content, $params);
            
            $task['result']['post_id'] = $post_id;
            $task['progress']['current_step'] = $step + 1;
            $task['progress']['message'] = '✅ Artículo creado';
        }
        
        return $task;
    }
    
    /**
     * Procesar etapa de scraping
     */
    private function process_scraping_step($task, $step) {
        // Similar a briefing pero solo scraping
        // ... implementación según necesidad
        return $task;
    }
    
    /**
     * Calcular número de etapas según tipo de tarea
     */
    private function calculate_steps($type, $data) {
        switch ($type) {
            case self::TASK_CREATE_BRIEFING:
                return 9; // SERP + 3 URLs scraping + 3 URLs entities + consolidar + build
                
            case self::TASK_GENERATE_ARTICLE:
                $outline = $data['params']['outline'] ?? [];
                $total_sections = count($outline);
                $sections_per_step = 2;
                $section_steps = ceil($total_sections / $sections_per_step);
                return 1 + $section_steps + 1; // intro + secciones + conclusión
                
            case self::TASK_SCRAPE_COMPETITORS:
                return 3; // 3 URLs
                
            default:
                return 1;
        }
    }
    
    /**
     * Obtener tarea
     */
    public function get_task($task_id) {
        $option_name = self::OPTION_PREFIX . $task_id;
        $task = get_option($option_name, false);
        return $task;
    }
    
    /**
     * Guardar tarea
     */
    private function save_task($task) {
        $option_name = self::OPTION_PREFIX . $task['id'];
        update_option($option_name, $task, false); // no autoload
    }
    
    /**
     * Pausar tarea
     * 
     * @param string $task_id Task ID
     * @return array|WP_Error Tarea actualizada o error
     */
    public function pause_task($task_id) {
        $task = $this->get_task($task_id);
        
        if (!$task) {
            return new WP_Error('task_not_found', 'Tarea no encontrada.');
        }
        
        // Solo se puede pausar si está processing o pending
        if (!in_array($task['status'], [self::STATUS_PROCESSING, self::STATUS_PENDING], true)) {
            return new WP_Error('invalid_status', 'Solo se pueden pausar tareas en progreso.');
        }
        
        $task['status'] = self::STATUS_PAUSED;
        $task['progress']['message'] = '⏸️ Pausado por el usuario';
        $this->save_task($task);
        
        return $task;
    }
    
    /**
     * Reanudar tarea pausada
     * 
     * @param string $task_id Task ID
     * @return array|WP_Error Tarea actualizada o error
     */
    public function resume_task($task_id) {
        $task = $this->get_task($task_id);
        
        if (!$task) {
            return new WP_Error('task_not_found', 'Tarea no encontrada.');
        }
        
        if ($task['status'] !== self::STATUS_PAUSED) {
            return new WP_Error('invalid_status', 'Solo se pueden reanudar tareas pausadas.');
        }
        
        if (!$task['can_resume']) {
            return new WP_Error('cannot_resume', 'Esta tarea no se puede reanudar.');
        }
        
        $task['status'] = self::STATUS_PROCESSING;
        $task['progress']['message'] = '▶️ Reanudando...';
        $this->save_task($task);
        
        return $task;
    }
    
    /**
     * Cancelar tarea
     * 
     * @param string $task_id Task ID
     * @return array|WP_Error Tarea actualizada o error
     */
    public function cancel_task($task_id) {
        $task = $this->get_task($task_id);
        
        if (!$task) {
            return new WP_Error('task_not_found', 'Tarea no encontrada.');
        }
        
        // Solo se puede cancelar si no está completada
        if ($task['status'] === self::STATUS_COMPLETED) {
            return new WP_Error('already_completed', 'No se puede cancelar una tarea completada.');
        }
        
        $task['status'] = self::STATUS_CANCELLED;
        $task['progress']['message'] = '❌ Cancelado por el usuario';
        $task['completed_at'] = time();
        $this->save_task($task);
        
        return $task;
    }
    
    /**
     * Reiniciar tarea desde el principio
     * 
     * @param string $task_id Task ID
     * @return array|WP_Error Tarea actualizada o error
     */
    public function restart_task($task_id) {
        $task = $this->get_task($task_id);
        
        if (!$task) {
            return new WP_Error('task_not_found', 'Tarea no encontrada.');
        }
        
        // Resetear todo a estado inicial
        $task['status'] = self::STATUS_PENDING;
        $task['progress']['current_step'] = 0;
        $task['progress']['percentage'] = 0;
        $task['progress']['message'] = '🔄 Reiniciando...';
        $task['result'] = null;
        $task['error'] = null;
        $task['retries'] = [];
        $task['current_retry'] = 0;
        $task['started_at'] = null;
        $task['completed_at'] = null;
        $task['step_started_at'] = null;
        
        $this->save_task($task);
        
        return $task;
    }
    
    /**
     * Reintentar tarea fallida desde el último checkpoint
     * 
     * @param string $task_id Task ID
     * @return array|WP_Error Tarea actualizada o error
     */
    public function retry_task($task_id) {
        $task = $this->get_task($task_id);
        
        if (!$task) {
            return new WP_Error('task_not_found', 'Tarea no encontrada.');
        }
        
        // Solo se puede reintentar si está en error o timeout
        if (!in_array($task['status'], [self::STATUS_ERROR, self::STATUS_TIMEOUT], true)) {
            return new WP_Error('invalid_status', 'Solo se pueden reintentar tareas con error.');
        }
        
        // Resetear contador de reintentos y status
        $task['status'] = self::STATUS_PROCESSING;
        $task['current_retry'] = 0;
        $task['error'] = null;
        $task['progress']['message'] = '🔄 Reintentando desde etapa ' . $task['progress']['current_step'] . '...';
        
        $this->save_task($task);
        
        return $task;
    }
    
    /**
     * Eliminar tarea (cleanup)
     */
    public function delete_task($task_id) {
        $option_name = self::OPTION_PREFIX . $task_id;
        delete_option($option_name);
    }
    
    /**
     * Limpiar tareas antiguas (> 24 horas)
     */
    public function cleanup_old_tasks() {
        global $wpdb;
        
        $prefix = self::OPTION_PREFIX;
        $cutoff = time() - DAY_IN_SECONDS;
        
        // Buscar opciones con prefijo
        $options = $wpdb->get_results($wpdb->prepare(
            "SELECT option_name, option_value FROM {$wpdb->options} 
             WHERE option_name LIKE %s",
            $wpdb->esc_like($prefix) . '%'
        ));
        
        foreach ($options as $option) {
            $task = maybe_unserialize($option->option_value);
            if (isset($task['created_at']) && $task['created_at'] < $cutoff) {
                delete_option($option->option_name);
            }
        }
    }
}

