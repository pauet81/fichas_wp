<?php
/**
 * Tab: Cola de Generación
 * 
 * Renderiza la interfaz completa para gestionar la cola de artículos.
 * 
 * @package ASAP_Theme
 * @subpackage IA\UI
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_UI_Tab_Queue {
    
    /**
     * @var object Instancia de ASAP_IA_New para acceder a métodos
     */
    private static $instance;
    
    /**
     * Renderiza el tab completo
     * 
     * @param object $ia_instance Instancia de ASAP_IA_New
     */
    public static function render($ia_instance) {
        self::$instance = $ia_instance;
        
        global $wpdb;
        $queue_table = $wpdb->prefix . 'asap_ia_queue';
        
        // Verificar que la tabla exista
        if ($wpdb->get_var("SHOW TABLES LIKE '$queue_table'") != $queue_table) {
            echo '<div class="notice notice-warning inline"><p>⚠ La tabla de cola aún no se ha creado. Recarga la página para inicializarla.</p></div>';
            return;
        }
        
        // Obtener datos usando métodos de la instancia
        $stats = self::call_method('get_queue_stats');
        $pending_items = self::call_method('get_queue_items', ['pending', 50]);
        $processing_items = self::call_method('get_queue_items', ['processing', 10]);
        $completed_items = self::call_method('get_queue_items', ['completed', 20]);
        $failed_items = self::call_method('get_queue_items', ['failed', 10]);
        
        ?>
        <section id="asap-options" class="asap-options section-content active">
            <?php self::render_styles(); ?>
            <?php self::render_header(); ?>
            <?php self::render_stats($stats); ?>
            <?php self::render_control_panel($ia_instance); ?>
            <?php self::render_csv_import(); ?>
            <?php self::render_pending_table($pending_items); ?>
            <?php self::render_completed_table($completed_items); ?>
            <?php self::render_failed_table($failed_items); ?>
            <?php self::render_javascript(); ?>
        </section>
        <?php
    }
    
    /**
     * Llama un método de la instancia
     */
    private static function call_method($method, $args = []) {
        $reflection = new ReflectionMethod(self::$instance, $method);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs(self::$instance, $args);
    }
    
    /**
     * Renderiza estilos CSS
     */
    private static function render_styles() {
        ?>
        <style>
            /* Prevenir scroll horizontal */
            .asap-options{overflow-x:hidden;max-width:100%}
            /* Fix para h2 y h3 con tooltips */
            .asap-options h2,
            .asap-options h3{display:flex;align-items:center;gap:6px}
            /* Unificar ancho de primera columna */
            .form-table th{width:200px}
            /* Tooltips - CSS base original */
            .asap-tooltip{display:inline-block;flex-shrink:0;float:right;margin-right:-10px;margin-top:2px;height:15px;width:15px;vertical-align:top;text-align:center;line-height:15px;font-size:12px;background:#F0F0F1;color:#777;border-radius:50%;text-decoration:none;cursor:help;position:relative}
            .asap-tooltip .tooltiptext{visibility:hidden;width:250px;background-color:#202225;color:#fff;text-align:left;cursor:default;padding:10px 12px;border-radius:4px;position:absolute;line-height:1.3;z-index:1;font-weight:300;font-size:13px;top:100%;left:50%;margin-top:14px;margin-left:-30px;opacity:0;transition:opacity .15s}
            .asap-tooltip .tooltiptext::after{content:" ";position:absolute;bottom:100%;left:25px;border-width:5px;border-style:solid;border-color:transparent transparent #282828 transparent}
            .asap-tooltip:hover .tooltiptext{opacity:1;visibility:visible}
            /* Tooltips en H2/H3 - solo ajustes específicos sin romper base */
            .asap-options h2 span.asap-tooltip,
            .asap-options h3 span.asap-tooltip{background:#202225;color:#fff;margin-left:6px;margin-right:0;margin-top:0;float:none}
            .queue-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin:20px 0}
            @media(max-width:1024px){.queue-stats{grid-template-columns:repeat(2,1fr)}}
            .stat-card{background:#fff;border:1px solid #e5e5e5;border-radius:4px;padding:20px;text-align:center;border-left:3px solid #1abc9c}
            .stat-card-number{font-size:36px;font-weight:700;color:#1d2327;margin-bottom:8px;line-height:1}
            .stat-card-label{font-size:12px;color:#7e8993;text-transform:uppercase;letter-spacing:0.3px}
            .queue-table{width:100%;border-collapse:collapse;margin-top:20px;background:#fff;border:1px solid #e5e5e5;border-radius:4px;overflow:hidden}
            .queue-table th{background:#fafafa;padding:12px 15px;text-align:left;border-bottom:1px solid #e5e5e5;font-weight:600;font-size:12px;color:#646970;text-transform:uppercase;letter-spacing:0.3px}
            .queue-table td{padding:12px 15px;border-bottom:1px solid #f0f0f1;font-size:13px}
            .queue-table tr:last-child td{border-bottom:none}
            .queue-table tr:hover{background:#fafafa}
            .badge{display:inline-block;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:600}
            .badge-pending{background:#fef8e7;color:#d4a106}
            .badge-processing{background:#e8f5f2;color:#1abc9c}
            .badge-completed{background:#e8f5f2;color:#1abc9c}
            .badge-failed{background:#ffe7e7;color:#c0392b}
            .btn-group{display:flex;gap:10px;margin:20px 0}
            .control-panel{background:#fff;border:1px solid #e5e5e5;border-radius:4px;padding:25px;margin:20px 0}
            .import-csv-zone{border:2px dashed #1abc9c;border-radius:4px;padding:30px;text-align:center;background:#fafafa;cursor:pointer;transition:all 0.2s}
            .import-csv-zone:hover{background:#e8f5f2}
            .import-csv-zone.drag-over{border-color:#16a085;background:#e8f5f2}
            .small-muted{color:#7e8993;font-size:12px}
        </style>
        <?php
    }
    
    /**
     * Renderiza el encabezado
     */
    private static function render_header() {
        ?>
        <h2>IA — Cola de generación
            <span class="asap-tooltip">?<span class="tooltiptext">Gestiona tus artículos en cola. Máximo 50 en cola, procesa hasta 5 por hora automáticamente.</span></span>
        </h2>
        <?php
    }
    
    /**
     * Renderiza estadísticas
     */
    private static function render_stats($stats) {
        ?>
        <div class="queue-stats">
            <div class="stat-card stat-pending">
                <div class="stat-card-number"><?php echo $stats['pending']; ?></div>
                <div class="stat-card-label">⏸ En Cola</div>
            </div>
            <div class="stat-card stat-processing">
                <div class="stat-card-number"><?php echo $stats['processing']; ?></div>
                <div class="stat-card-label">⏳ Procesando</div>
            </div>
            <div class="stat-card stat-completed">
                <div class="stat-card-number"><?php echo $stats['completed_today']; ?></div>
                <div class="stat-card-label">✅ Completados Hoy</div>
            </div>
            <div class="stat-card stat-failed">
                <div class="stat-card-number"><?php echo $stats['failed']; ?></div>
                <div class="stat-card-label">❌ Fallidos</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderiza panel de control
     */
    private static function render_control_panel($ia_instance) {
        $can_process = self::call_method('can_process_queue');
        ?>
        <div class="control-panel">
            <h3 style="margin-top:0;">Panel de Control
                <span class="asap-tooltip">?<span class="tooltiptext">Controla el procesamiento de la cola: inicia manualmente, pausa el proceso, o limpia artículos completados para mantener la lista ordenada.</span></span>
            </h3>
            
            <div class="btn-group">
                <button type="button" id="btn_process_queue" class="button button-primary button-large">
                    ▶ Procesar Cola Ahora
                </button>
                <button type="button" id="btn_pause_queue" class="button button-large" disabled>
                    ⏸ Pausar
                </button>
                <button type="button" id="btn_clear_completed" class="button button-large">
                    🗑 Limpiar Completados
                </button>
                <button type="button" id="btn_clear_all_queue" class="button button-large" style="color:#b32d2e;">
                    🗑 Vaciar Cola
                </button>
            </div>

            <div style="margin-top:15px;">
                <div id="queue_progress_wrap" style="display:none;">
                    <div style="background:#f0f0f1;border-radius:6px;height:24px;overflow:hidden;margin-bottom:10px;">
                        <div id="queue_progress_bar" style="background:#2271b1;height:100%;width:0;transition:width 0.3s;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:600;"></div>
                    </div>
                    <div id="queue_progress_text" style="font-size:13px;color:#646970;"></div>
                </div>
            </div>

            <div style="margin-top:20px;padding-top:20px;border-top:1px solid #ddd;">
                <h4>Publicación automática programada
                    <span class="asap-tooltip">?<span class="tooltiptext">Los artículos generados se guardarán como borrador y se publicarán automáticamente cada N horas. Ideal para mantener un flujo constante de contenido sin tener que publicar manualmente.</span></span>
                </h4>
                <label style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                    <input type="checkbox" id="queue_auto_publish_enabled" <?php checked(get_option('asap_queue_auto_publish_enabled', '0'), '1'); ?>>
                    <span>Activar publicación automática</span>
                </label>
                <div id="queue_auto_publish_settings" style="margin-left:26px;<?php echo get_option('asap_queue_auto_publish_enabled', '0') === '1' ? '' : 'display:none;'; ?>">
                    <label style="display:flex;align-items:center;gap:10px;">
                        <span>Publicar cada</span>
                        <input type="number" id="queue_auto_publish_interval" value="<?php echo esc_attr(get_option('asap_queue_auto_publish_interval', '24')); ?>" min="1" max="168" style="width:80px;">
                        <span>horas</span>
                    </label>
                    <p class="description" style="margin-top:10px;">
                        Los artículos en borrador se publicarán automáticamente cada <strong id="interval_display"><?php echo esc_html(get_option('asap_queue_auto_publish_interval', '24')); ?></strong> horas. 
                        El sistema usa WP Cron para revisar cada 5 minutos y publicar los posts programados.
                    </p>
                    <button type="button" id="btn_save_auto_publish" class="button" style="margin-top:10px;">
                        Guardar configuración
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderiza importador CSV
     */
    private static function render_csv_import() {
        ?>
        <div class="control-panel">
            <h3 style="margin-top:0;">Importar desde CSV
                <span class="asap-tooltip">?<span class="tooltiptext">Importa múltiples artículos de una vez usando un archivo CSV. Ideal para agregar decenas de artículos a la cola sin llenar formularios uno por uno.</span></span>
            </h3>
            <p class="description">Sube un archivo CSV con tus artículos para agregarlos todos a la cola de una vez.</p>
            
            <div style="margin-bottom:15px;">
                <button type="button" id="btn_download_csv_example" class="button button-secondary">
                    Descargar ejemplo de CSV
                </button>
                <span style="margin-left:10px;color:#666;font-size:12px;">← Descarga un archivo de ejemplo para ver el formato correcto</span>
            </div>
            
            <div class="import-csv-zone" id="csv_drop_zone">
                <p style="font-size:16px;margin-bottom:10px;">Arrastra tu archivo CSV aquí o haz click para seleccionar</p>
                <p style="font-size:12px;color:#646970;margin:0;">Formato completo: h1, keyword, secondary_keywords, longitud, estilo, idioma, status, post_type, author, faqs, faqs_count, conclusion, competition_urls, h2_1, h2_2...</p>
                <input type="file" id="csv_file_input" accept=".csv" style="display:none;">
            </div>
            
            <div id="csv_preview" style="display:none;margin-top:20px;">
                <h4>Vista previa del CSV:</h4>
                <div id="csv_preview_content" style="max-height:300px;overflow-y:auto;border:1px solid #ddd;padding:10px;background:#fff;"></div>
                <div style="margin-top:15px;">
                    <button type="button" id="btn_import_csv" class="button button-primary">
                        ➕ Agregar <span id="csv_count">0</span> artículos a la cola
                    </button>
                    <button type="button" id="btn_cancel_csv" class="button">Cancelar</button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderiza tabla de pendientes
     */
    private static function render_pending_table($items) {
        if (empty($items)) {
            ?>
            <div class="notice notice-info inline">
                <p>No hay artículos pendientes en la cola. Ve a la pestaña <a href="<?php echo admin_url('admin.php?page=asap-menu-ia&tab=ia_new'); ?>">Nuevo</a> y usa "Agregar a cola".</p>
            </div>
            <?php
            return;
        }
        ?>
        <h3>⏸ Pendientes (<?php echo count($items); ?>)</h3>
        <table class="queue-table">
            <thead>
                <tr>
                    <th style="width:40px;">ID</th>
                    <th style="width:35%;">Título (H1)</th>
                    <th style="width:25%;">Keyword</th>
                    <th style="width:90px;">Longitud</th>
                    <th style="width:100px;">Estado</th>
                    <th style="width:135px;">Creado</th>
                    <th style="width:110px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $params = json_decode($item->params, true);
                    $h1 = isset($params['h1']) ? $params['h1'] : '—';
                    $keyword = isset($params['keyword']) ? $params['keyword'] : '—';
                    $target_len = isset($params['target_len']) ? $params['target_len'] : '—';
                ?>
                <tr data-job-id="<?php echo $item->id; ?>">
                    <td><?php echo $item->id; ?></td>
                    <td><strong><?php echo esc_html($h1); ?></strong></td>
                    <td><?php echo esc_html($keyword); ?></td>
                    <td><?php echo esc_html($target_len); ?> palabras</td>
                    <td><span class="badge badge-pending">Pendiente</span></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($item->created_at)); ?></td>
                    <td>
                        <button type="button" class="button button-primary btn-process-now" data-id="<?php echo $item->id; ?>" title="Generar ahora">▶️</button>
                        <button type="button" class="button btn-delete-queue" data-id="<?php echo $item->id; ?>" title="Eliminar">🗑</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Renderiza tabla de completados
     */
    private static function render_completed_table($items) {
        if (empty($items)) return;
        ?>
        <h3 style="margin-top:40px;">✅ Completados (<?php echo count($items); ?>)</h3>
        <table class="queue-table">
            <thead>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Título</th>
                    <th>Post</th>
                    <th style="width:100px;">Estado</th>
                    <th style="width:150px;">Procesado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $params = json_decode($item->params, true);
                    $h1 = isset($params['h1']) ? $params['h1'] : '—';
                ?>
                <tr>
                    <td><?php echo $item->id; ?></td>
                    <td><?php echo esc_html($h1); ?></td>
                    <td>
                        <?php if ($item->post_id): ?>
                            <a href="<?php echo get_edit_post_link($item->post_id); ?>" target="_blank">
                                Editar #<?php echo $item->post_id; ?>
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-completed">Completado</span></td>
                    <td><?php echo $item->processed_at ? date('d/m/Y H:i', strtotime($item->processed_at)) : '—'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Renderiza tabla de fallidos
     */
    private static function render_failed_table($items) {
        if (empty($items)) return;
        ?>
        <h3 style="margin-top:40px;">❌ Fallidos (<?php echo count($items); ?>)</h3>
        <table class="queue-table">
            <thead>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Título</th>
                    <th>Error</th>
                    <th style="width:100px;">Intentos</th>
                    <th style="width:100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $params = json_decode($item->params, true);
                    $h1 = isset($params['h1']) ? $params['h1'] : '—';
                ?>
                <tr>
                    <td><?php echo $item->id; ?></td>
                    <td><?php echo esc_html($h1); ?></td>
                    <td style="color:#dc3232;font-size:12px;"><?php echo esc_html(substr($item->error_message, 0, 100)); ?></td>
                    <td><?php echo $item->attempts; ?>/3</td>
                    <td>
                        <button type="button" class="button btn-retry-queue" data-id="<?php echo $item->id; ?>">🔄 Reintentar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Renderiza JavaScript
     */
    private static function render_javascript() {
        $process_nonce = wp_create_nonce('asap_process_queue');
        $clear_nonce = wp_create_nonce('asap_clear_queue');
        $actions_nonce = wp_create_nonce('asap_queue_actions');
        $related_nonce = wp_create_nonce('asap_related_keywords');
        ?>
        <script>
        jQuery(function($){
            'use strict';
            
            var params = new URLSearchParams(window.location.search);
            if (params.get('page') !== 'asap-menu-ia') return;
            // Si no hay tab especificado, asumimos que estamos en ia_dashboard (primera pestaña)
            // Solo ejecutar si estamos explícitamente en ia_queue
            var currentTab = params.get('tab') || 'ia_dashboard';
            if (currentTab !== 'ia_queue') return;
            
            var processNonce = '<?php echo $process_nonce; ?>';
            var clearNonce = '<?php echo $clear_nonce; ?>';
            var actionsNonce = '<?php echo $actions_nonce; ?>';
            var isProcessing = false;
            var csvData = [];
            
            // ========================================================================
            // PROCESAMIENTO DE COLA
            // ========================================================================
            
            $('#btn_process_queue').on('click', function() {
                if (isProcessing) return;
                
                var $btn = $(this);
                var $progressWrap = $('#queue_progress_wrap');
                var $progressBar = $('#queue_progress_bar');
                var $progressText = $('#queue_progress_text');
                
                isProcessing = true;
                $btn.prop('disabled', true).text('⏳ Procesando...');
                $('#btn_pause_queue').prop('disabled', false);
                $progressWrap.show();
                $progressBar.css('width', '0%').text('');
                $progressText.text('Iniciando procesamiento...');
                
                processNext();
                
                function processNext() {
                    if (!isProcessing) {
                        finalize('⏸ Pausado por el usuario');
                        return;
                    }
                    
                    $.ajax({
                        url: ASAP_IA.ajax,
                        type: 'POST',
                        data: {
                            action: 'asap_process_next_job',
                            nonce: processNonce
                        },
                        success: function(resp) {
                            if (resp && resp.success) {
                                if (resp.data.completed) {
                                    $progressBar.css('width', '100%').text('100%');
                                    $progressText.html('<strong style="color:#46b450;">✓ Cola completada. Recargando...</strong>');
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500);
                                } else if (resp.data.limit_reached) {
                                    finalize('⚠ Límite alcanzado (máx. 5 por hora)');
                                } else if (resp.data.message) {
                                    var progress = resp.data.progress || 50;
                                    $progressBar.css('width', progress + '%').text(progress + '%');
                                    $progressText.html(resp.data.message);
                                    
                                    // Continuar con el siguiente después de 2 segundos
                                    setTimeout(processNext, 2000);
                                }
                            } else {
                                var errorMsg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error desconocido';
                                finalize('❌ Error: ' + errorMsg);
                            }
                        },
                        error: function() {
                            finalize('❌ Error de conexión');
                        }
                    });
                }
                
                function finalize(message) {
                    isProcessing = false;
                    $btn.prop('disabled', false).text('▶ Procesar Cola Ahora');
                    $('#btn_pause_queue').prop('disabled', true);
                    $progressText.text(message);
                    
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            });
            
            $('#btn_pause_queue').on('click', function() {
                isProcessing = false;
                $(this).prop('disabled', true);
            });
            
            // ========================================================================
            // LIMPIAR COMPLETADOS
            // ========================================================================
            
            $('#btn_clear_completed').on('click', function() {
                if (!confirm('¿Estás seguro de eliminar todos los trabajos completados de la cola?')) {
                    return;
                }
                
                var $btn = $(this);
                $btn.prop('disabled', true).text('🗑 Limpiando...');
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_clear_completed_queue',
                        nonce: clearNonce
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            alert(resp.data.message || 'Completados eliminados');
                            location.reload();
                        } else {
                            alert('Error: ' + (resp.data.message || 'Error desconocido'));
                        }
                    },
                    error: function() {
                        alert('Error de conexión');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('🗑 Limpiar Completados');
                    }
                });
            });
            
            // ========================================================================
            // VACIAR TODA LA COLA
            // ========================================================================
            
            $('#btn_clear_all_queue').on('click', function() {
                if (!confirm('⚠️ ATENCIÓN: Esto eliminará TODOS los trabajos pendientes y fallidos de la cola.\n\n¿Estás seguro de que deseas vaciar la cola completamente?')) {
                    return;
                }
                
                var $btn = $(this);
                $btn.prop('disabled', true).text('🗑 Vaciando...');
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_clear_all_queue',
                        nonce: clearNonce
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            alert(resp.data.message || 'Cola vaciada correctamente');
                            location.reload();
                        } else {
                            alert('Error: ' + (resp.data.message || 'Error desconocido'));
                        }
                    },
                    error: function() {
                        alert('Error de conexión');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('🗑 Vaciar Cola');
                    }
                });
            });
            
            // ========================================================================
            // PROCESAR AHORA, ELIMINAR Y REINTENTAR ITEMS
            // ========================================================================
            
            // Botón "Generar ahora"
            $('.btn-process-now').on('click', function() {
                if (!confirm('¿Generar este artículo ahora? Se procesará inmediatamente.')) return;
                
                var jobId = $(this).data('id');
                var $btn = $(this);
                var $row = $(this).closest('tr');
                
                // Deshabilitar botón y mostrar estado
                $btn.prop('disabled', true).text('⏳');
                $row.find('.badge').removeClass('badge-pending').addClass('badge-processing').text('Procesando...');
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_process_queue_item',
                        nonce: actionsNonce,
                        job_id: jobId
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });
                            alert('✅ Artículo generado exitosamente. Recarga la página para ver los resultados.');
                            location.reload();
                        } else {
                            alert('❌ Error: ' + (resp.data && resp.data.message ? resp.data.message : 'Error desconocido'));
                            $btn.prop('disabled', false).text('▶️');
                            $row.find('.badge').removeClass('badge-processing').addClass('badge-pending').text('Pendiente');
                        }
                    },
                    error: function() {
                        alert('❌ Error de conexión al procesar el artículo.');
                        $btn.prop('disabled', false).text('▶️');
                        $row.find('.badge').removeClass('badge-processing').addClass('badge-pending').text('Pendiente');
                    }
                });
            });
            
            $('.btn-delete-queue').on('click', function() {
                if (!confirm('¿Eliminar este artículo de la cola?')) return;
                
                var jobId = $(this).data('id');
                var $row = $(this).closest('tr');
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_delete_queue_item',
                        nonce: actionsNonce,
                        job_id: jobId
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('Error: ' + (resp.data.message || 'Error desconocido'));
                        }
                    },
                    error: function() {
                        alert('Error de conexión');
                    }
                });
            });
            
            $('.btn-retry-queue').on('click', function() {
                var jobId = $(this).data('id');
                var $btn = $(this);
                
                $btn.prop('disabled', true).text('🔄 Reintentando...');
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_retry_queue_item',
                        nonce: actionsNonce,
                        job_id: jobId
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            alert(resp.data.message || 'Reintentando...');
                            location.reload();
                        } else {
                            alert('Error: ' + (resp.data.message || 'Error desconocido'));
                        }
                    },
                    error: function() {
                        alert('Error de conexión');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('🔄 Reintentar');
                    }
                });
            });
            
            // ========================================================================
            // CONFIGURACIÓN
            // ========================================================================
            
            $('#auto_process_queue').on('change', function() {
                var enabled = $(this).is(':checked') ? '1' : '0';
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_toggle_auto_queue',
                        nonce: actionsNonce,
                        enabled: enabled
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            console.log('Auto-procesamiento: ' + (enabled === '1' ? 'activado' : 'desactivado'));
                        }
                    }
                });
            });
            
            $('#queue_email_notifications').on('change', function() {
                var enabled = $(this).is(':checked') ? '1' : '0';
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_toggle_queue_email',
                        nonce: actionsNonce,
                        enabled: enabled
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            console.log('Email notificaciones: ' + (enabled === '1' ? 'activado' : 'desactivado'));
                        }
                    }
                });
            });
            
            // ========================================================================
            // PUBLICACIÓN AUTOMÁTICA PROGRAMADA
            // ========================================================================
            
            // Toggle de publicación automática
            $('#queue_auto_publish_enabled').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#queue_auto_publish_settings').slideDown(200);
                } else {
                    $('#queue_auto_publish_settings').slideUp(200);
                }
            });
            
            // Actualizar el display del intervalo
            $('#queue_auto_publish_interval').on('input', function() {
                $('#interval_display').text($(this).val());
            });
            
            // Guardar configuración de publicación automática
            $('#btn_save_auto_publish').on('click', function() {
                var enabled = $('#queue_auto_publish_enabled').is(':checked') ? '1' : '0';
                var interval = parseInt($('#queue_auto_publish_interval').val()) || 24;
                
                // Validar rango
                if (interval < 1) interval = 1;
                if (interval > 168) interval = 168;
                $('#queue_auto_publish_interval').val(interval);
                
                var $btn = $(this);
                $btn.prop('disabled', true).text('Guardando...');
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_save_auto_publish_config',
                        nonce: actionsNonce,
                        enabled: enabled,
                        interval: interval
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            alert('✅ ' + (resp.data.message || 'Configuración guardada correctamente.'));
                        } else {
                            alert('❌ Error: ' + (resp.data && resp.data.message ? resp.data.message : 'No se pudo guardar la configuración'));
                        }
                    },
                    error: function() {
                        alert('❌ Error de conexión al guardar la configuración');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('💾 Guardar configuración');
                    }
                });
            });
            
            // ========================================================================
            // IMPORTACIÓN CSV
            // ========================================================================
            
            // Descargar ejemplo de CSV
            $('#btn_download_csv_example').on('click', function(){
                // FORMATO COMPLETO:
                // h1, keyword, secondary_keywords, longitud, estilo, idioma, status, post_type, author, faqs, faqs_count, conclusion, competition_urls, h2_1, h2_2, h2_3...
                var csvContent = "h1,keyword,secondary_keywords,longitud,estilo,idioma,status,post_type,author,faqs,faqs_count,conclusion,competition_urls,h2_1,h2_2,h2_3,h2_4,h2_5,h2_6\n";
                
                // Ejemplo 1: Programación
                csvContent += '"Cómo aprender a programar desde cero","aprender programar","programación para principiantes;tutoriales programación;aprender coding",3000,informativo,es,draft,post,0,1,5,1,"","Qué es la programación","Lenguajes recomendados para principiantes","Recursos gratuitos para aprender","Errores comunes al empezar","Cómo crear tu primer proyecto","Consejos para mantener la motivación"\n';
                
                // Ejemplo 2: Cocina
                csvContent += '"Las mejores recetas de postres fáciles","postres fáciles","recetas dulces;postres caseros;repostería fácil",2500,conversacional,es,publish,post,0,0,5,1,"","Ingredientes básicos que siempre debes tener","Receta 1: Brownies de chocolate","Receta 2: Mousse de limón","Receta 3: Galletas de avena","Tips para decorar tus postres","Cómo conservar tus postres caseros"\n';
                
                // Ejemplo 3: Marketing con análisis de competencia
                csvContent += '"Guía completa de marketing digital 2025","marketing digital 2025","estrategias marketing;marketing online;publicidad digital",4000,profesional,es,draft,post,0,1,8,1,"https://example.com/competidor1;https://example.com/competidor2","Qué es el marketing digital","Estrategias de SEO actualizadas","Marketing en redes sociales","Email marketing efectivo","Publicidad pagada (SEM)","Analítica y medición de resultados"\n';
                
                // Ejemplo 4: Yoga
                csvContent += '"Beneficios del yoga para principiantes","yoga principiantes","yoga para empezar;práctica yoga;posturas yoga",2000,informativo,es,draft,post,0,1,6,0,"","Qué es el yoga y sus orígenes","Beneficios físicos del yoga","Beneficios mentales y emocionales","Tipos de yoga para comenzar","Posturas básicas para principiantes","Cómo crear una rutina diaria"\n';
                
                // Ejemplo 5: Finanzas
                csvContent += '"Cómo ahorrar dinero en el supermercado","ahorrar dinero supermercado","trucos ahorro;comprar barato;economía doméstica",1800,conversacional,es,publish,post,0,0,5,1,"","Planifica tus compras con anticipación","Compara precios y usa apps","Aprovecha ofertas y cupones","Compra productos de temporada","Evita comprar con hambre","Lista de productos económicos y nutritivos"\n';
                
                // Crear blob y descargar
                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                var url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'ejemplo-importacion-asap.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
            
            var $dropZone = $('#csv_drop_zone');
            var $fileInput = $('#csv_file_input');
            var $preview = $('#csv_preview');
            var $previewContent = $('#csv_preview_content');
            
            // Click en zona de drop abre selector de archivos
            $dropZone.on('click', function(e) {
                // Evitar que el clic en el input vuelva a disparar este evento
                if (e.target.id === 'csv_file_input') return;
                $fileInput.click();
            });
            
            // Prevenir comportamiento por defecto del navegador
            $dropZone.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('drag-over');
            });
            
            $dropZone.on('dragleave dragend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
            });
            
            // Manejar drop de archivo
            $dropZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('drag-over');
                
                var files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    handleFile(files[0]);
                }
            });
            
            // Manejar selección de archivo
            $fileInput.on('change', function() {
                if (this.files.length > 0) {
                    handleFile(this.files[0]);
                }
            });
            
            function handleFile(file) {
                if (!file.name.endsWith('.csv')) {
                    alert('Por favor selecciona un archivo CSV válido');
                    return;
                }
                
                var reader = new FileReader();
                reader.onload = function(e) {
                    parseCSV(e.target.result);
                };
                reader.readAsText(file);
            }
            
            function parseCSV(csvText) {
                var lines = csvText.split(/\r\n|\r|\n/);
                csvData = [];
                
                // ✅ IGNORAR LA PRIMERA LÍNEA (CABECERA)
                for (var i = 1; i < lines.length; i++) {
                    var line = lines[i].trim();
                    if (!line) continue;
                    
                    // Parseo mejorado que respeta comillas
                    var cols = parseCSVLine(line);
                    
                    if (cols.length < 3) continue; // mínimo: h1, keyword, longitud
                    
                    // FORMATO NUEVO:
                    // 0=h1, 1=keyword, 2=secondary_keywords, 3=longitud, 4=estilo, 5=idioma,
                    // 6=status, 7=post_type, 8=author, 9=faqs, 10=faqs_count, 11=conclusion,
                    // 12=competition_urls, 13+=h2s
                    
                    var item = {
                        h1: cols[0] || 'Sin título',
                        keyword: cols[1] || '',
                        secondary_keywords: cols[2] || '',  // Formato: "keyword1;keyword2;keyword3"
                        target_len: parseInt(cols[3]) || 3000,
                        style: cols[4] || 'informativo',
                        lang: cols[5] || 'es',
                        status: cols[6] || 'draft',
                        post_type: cols[7] || 'post',
                        author: parseInt(cols[8]) || 0,
                        faqs_enable: (cols[9] === '1' || cols[9] === 'true' || cols[9] === 'yes'),
                        faqs_count: parseInt(cols[10]) || 5,
                        conclusion_enable: (cols[11] === '1' || cols[11] === 'true' || cols[11] === 'yes'),
                        competition_urls: cols[12] || '',  // Formato: "url1;url2;url3"
                        h2s: []
                    };
                    
                    // H2s adicionales (comienzan en columna 13)
                    for (var j = 13; j < cols.length; j++) {
                        if (cols[j]) {
                            item.h2s.push(cols[j]);
                        }
                    }
                    
                    csvData.push(item);
                }
                
                // Helper: Parsea una línea CSV respetando comillas
                function parseCSVLine(line) {
                    var result = [];
                    var current = '';
                    var inQuotes = false;
                    
                    for (var i = 0; i < line.length; i++) {
                        var char = line[i];
                        
                        if (char === '"') {
                            inQuotes = !inQuotes;
                        } else if (char === ',' && !inQuotes) {
                            result.push(current.trim());
                            current = '';
                        } else {
                            current += char;
                        }
                    }
                    result.push(current.trim());
                    return result;
                }
                
                if (csvData.length === 0) {
                    alert('No se encontraron datos válidos en el CSV');
                    return;
                }
                
                // Mostrar preview
                var html = '<table class="queue-table">';
                html += '<thead><tr><th>H1</th><th>Keyword</th><th>Longitud</th><th>Status</th><th>FAQs</th><th>H2s</th></tr></thead><tbody>';
                
                csvData.slice(0, 10).forEach(function(item) {
                    html += '<tr>';
                    html += '<td>' + $('<div>').text(item.h1).html() + '</td>';
                    html += '<td>' + $('<div>').text(item.keyword).html() + '</td>';
                    html += '<td>' + item.target_len + '</td>';
                    html += '<td>' + (item.status || 'draft') + '</td>';
                    html += '<td>' + (item.faqs_enable ? 'Sí (' + item.faqs_count + ')' : 'No') + '</td>';
                    html += '<td>' + item.h2s.length + ' H2s</td>';
                    html += '</tr>';
                });
                
                if (csvData.length > 10) {
                    html += '<tr><td colspan="6"><em>... y ' + (csvData.length - 10) + ' más</em></td></tr>';
                }
                
                html += '</tbody></table>';
                
                $previewContent.html(html);
                $('#csv_count').text(csvData.length);
                $preview.slideDown();
            }
            
            $('#btn_import_csv').on('click', function() {
                if (csvData.length === 0) {
                    alert('No hay datos para importar');
                    return;
                }
                
                var $btn = $(this);
                $btn.prop('disabled', true);
                
                // ✅ IMPORTACIÓN POR LOTES (evita timeout)
                var totalItems = csvData.length;
                var batchSize = 10; // Procesar 10 artículos a la vez
                var currentBatch = 0;
                var totalAdded = 0;
                var totalErrors = 0;
                
                function importBatch() {
                    var start = currentBatch * batchSize;
                    var end = Math.min(start + batchSize, totalItems);
                    var batch = csvData.slice(start, end);
                    
                    if (batch.length === 0) {
                        // ✅ Importación completada
                        $btn.prop('disabled', false).html('➕ Agregar <span id="csv_count">' + csvData.length + '</span> artículos a la cola');
                        alert('✅ Importación completada: ' + totalAdded + ' artículos agregados' + (totalErrors > 0 ? ', ' + totalErrors + ' errores' : ''));
                        location.reload();
                        return;
                    }
                    
                    // Actualizar progreso
                    var progress = Math.round((end / totalItems) * 100);
                    $btn.html('➕ Importando... ' + end + '/' + totalItems + ' (' + progress + '%)');
                    
                    $.ajax({
                        url: ASAP_IA.ajax,
                        type: 'POST',
                        timeout: 60000, // 60 segundos por lote
                        data: {
                            action: 'asap_import_csv_to_queue',
                            nonce: actionsNonce,
                            items: JSON.stringify(batch)
                        },
                        success: function(resp) {
                            if (resp && resp.success) {
                                totalAdded += (resp.data.added || batch.length);
                            } else {
                                totalErrors += batch.length;
                                console.error('Error en lote:', resp.data.message);
                            }
                            
                            // Procesar siguiente lote
                            currentBatch++;
                            setTimeout(importBatch, 500); // Pequeña pausa entre lotes
                        },
                        error: function(xhr, status, error) {
                            totalErrors += batch.length;
                            console.error('Error de conexión en lote ' + currentBatch + ':', error);
                            
                            // Continuar con el siguiente lote aunque falle uno
                            currentBatch++;
                            setTimeout(importBatch, 1000);
                        }
                    });
                }
                
                // Iniciar importación
                importBatch();
            });
            
            $('#btn_cancel_csv').on('click', function() {
                csvData = [];
                $preview.slideUp();
                $fileInput.val('');
            });
        });
        </script>
        <?php
    }
}



