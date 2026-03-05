<?php
/**
 * Tab: Dashboard de IA
 * 
 * Renderiza estadísticas, métricas y análisis del uso de IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\UI
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_UI_Tab_Dashboard {
    
    /**
     * @var object Instancia de ASAP_Manual_IA_Rewriter
     */
    private static $instance;
    
    /**
     * Renderiza el tab completo
     * 
     * @param object $ia_instance Instancia de ASAP_Manual_IA_Rewriter
     */
    public static function render($ia_instance) {
        self::$instance = $ia_instance;
        
        global $wpdb;
        $logs_table = $wpdb->prefix . 'asap_ia_logs';
        
        // Verificar que la tabla exista
        if ($wpdb->get_var("SHOW TABLES LIKE '$logs_table'") != $logs_table) {
            echo '<div class="notice notice-warning inline"><p>⚠ Las tablas de logs aún no se han creado. Genera tu primer artículo para ver estadísticas.</p></div>';
            return;
        }
        
        // Recopilar todas las estadísticas
        $stats = self::gather_stats($wpdb, $logs_table);
        
        ?>
        <section id="asap-options" class="asap-options section-content active">
            <?php self::render_styles(); ?>
            
            <h2 style="margin-top:0;">IA — Dashboard
                <span class="asap-tooltip">?<span class="tooltiptext">Panel de control con estadísticas completas de uso de IA: artículos generados, costos, tokens consumidos, y métricas de rendimiento.</span></span>
            </h2>
            
            <?php self::render_main_cards($stats); ?>
            <?php self::render_costs($stats); ?>
            <?php self::render_activity_chart($stats['daily_stats']); ?>
            <?php self::render_top_keywords($stats['top_keywords']); ?>
            <?php self::render_recent_generations($stats['recent']); ?>
            <?php self::render_additional_stats($stats, $ia_instance); ?>
            <?php self::render_quick_actions($stats); ?>
        </section>
        <?php
    }
    
    /**
     * Recopila todas las estadísticas necesarias
     */
    private static function gather_stats($wpdb, $logs_table) {
        return [
            'total_articles' => intval($wpdb->get_var("SELECT COUNT(*) FROM $logs_table WHERE type = 'article' AND status = 'success'")),
            'total_metas' => intval($wpdb->get_var("SELECT COUNT(*) FROM $logs_table WHERE type = 'meta' AND status = 'success'")),
            'total_images' => intval($wpdb->get_var("SELECT COUNT(*) FROM $logs_table WHERE type = 'image' AND status = 'success'")),
            'total_alt_texts' => intval($wpdb->get_var("SELECT COUNT(*) FROM $logs_table WHERE type = 'alt_text' AND status = 'success'")),
            
            'cost_today' => floatval($wpdb->get_var("SELECT SUM(cost_usd) FROM $logs_table WHERE DATE(created_at) = CURDATE()")),
            'cost_week' => floatval($wpdb->get_var("SELECT SUM(cost_usd) FROM $logs_table WHERE YEARWEEK(created_at) = YEARWEEK(NOW())")),
            'cost_month' => floatval($wpdb->get_var("SELECT SUM(cost_usd) FROM $logs_table WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())")),
            'cost_total' => floatval($wpdb->get_var("SELECT SUM(cost_usd) FROM $logs_table")),
            
            'tokens_today' => intval($wpdb->get_var("SELECT SUM(tokens_total) FROM $logs_table WHERE DATE(created_at) = CURDATE()")),
            'tokens_total' => intval($wpdb->get_var("SELECT SUM(tokens_total) FROM $logs_table")),
            
            'articles_today' => intval($wpdb->get_var("SELECT COUNT(*) FROM $logs_table WHERE type = 'article' AND DATE(created_at) = CURDATE()")),
            
            'recent' => $wpdb->get_results("SELECT * FROM $logs_table WHERE status = 'success' AND type != 'outline' ORDER BY created_at DESC LIMIT 10"),
            
            'top_keywords' => $wpdb->get_results("
                SELECT JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.keyword')) as keyword, COUNT(*) as count 
                FROM $logs_table 
                WHERE type = 'article' AND metadata IS NOT NULL 
                GROUP BY keyword 
                ORDER BY count DESC 
                LIMIT 5
            "),
            
            'daily_stats' => $wpdb->get_results("
                SELECT DATE(created_at) as date, 
                       COUNT(*) as count,
                       SUM(cost_usd) as cost
                FROM $logs_table 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            "),
        ];
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
            /* Tooltips - CSS base original */
            .asap-tooltip{display:inline-block;flex-shrink:0;float:right;margin-right:-10px;margin-top:2px;height:15px;width:15px;vertical-align:top;text-align:center;line-height:15px;font-size:12px;background:#F0F0F1;color:#777;border-radius:50%;text-decoration:none;cursor:help;position:relative}
            .asap-tooltip .tooltiptext{visibility:hidden;width:250px;background-color:#202225;color:#fff;text-align:left;cursor:default;padding:10px 12px;border-radius:4px;position:absolute;line-height:1.3;z-index:1;font-weight:300;font-size:13px;top:100%;left:50%;margin-top:14px;margin-left:-30px;opacity:0;transition:opacity .15s}
            .asap-tooltip .tooltiptext::after{content:" ";position:absolute;bottom:100%;left:25px;border-width:5px;border-style:solid;border-color:transparent transparent #282828 transparent}
            .asap-tooltip:hover .tooltiptext{opacity:1;visibility:visible}
            /* Tooltips en H2/H3 - solo ajustes específicos sin romper base */
            .asap-options h2 span.asap-tooltip,
            .asap-options h3 span.asap-tooltip{background:#202225;color:#fff;margin-left:6px;margin-right:0;margin-top:0;float:none}
            .dashboard-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin:20px 0}
            @media(max-width:1400px){.dashboard-grid{grid-template-columns:repeat(2,1fr)}}
            @media(max-width:768px){.dashboard-grid{grid-template-columns:1fr}}
            .dashboard-card{background:#fff;border:1px solid #e5e5e5;border-radius:4px;padding:20px;border-left:3px solid #1abc9c}
            .dashboard-card-header{font-size:12px;color:#646970;margin-bottom:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px}
            .dashboard-card-value{font-size:36px;font-weight:700;color:#1d2327;margin-bottom:8px;line-height:1}
            .dashboard-card-label{font-size:13px;color:#7e8993}
            .dashboard-card-accent{border-left:3px solid #1abc9c}
            .recent-table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #e5e5e5;border-radius:4px;overflow:hidden}
            .recent-table th{background:#fafafa;padding:12px 15px;text-align:left;font-weight:600;border-bottom:1px solid #e5e5e5;font-size:12px;color:#646970;text-transform:uppercase;letter-spacing:0.3px}
            .recent-table td{padding:12px 15px;border-bottom:1px solid #f0f0f1;font-size:13px}
            .recent-table tr:last-child td{border-bottom:none}
            .recent-table tr:hover{background:#fafafa}
            .cost-highlight{color:#1abc9c;font-weight:600}
            .section-header{display:flex;justify-content:space-between;align-items:center;margin:30px 0 15px 0}
            .section-header h2{margin:0}
            .chart-container{background:#fff;border:1px solid #e5e5e5;border-radius:4px;padding:25px;margin:20px 0}
            .bar-chart{display:flex;align-items:flex-end;height:200px;gap:10px;margin-top:20px}
            .bar{flex:1;background:#1abc9c;border-radius:2px 2px 0 0;position:relative;transition:all 0.3s}
            .bar:hover{background:#16a085}
            .bar-label{position:absolute;bottom:-25px;left:0;right:0;text-align:center;font-size:11px;color:#646970}
            .bar-value{position:absolute;top:-20px;left:0;right:0;text-align:center;font-size:11px;font-weight:600;color:#1d2327}
            .badge{display:inline-block;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:600}
            .badge-completed{background:#e8f5f2;color:#1abc9c}
            .small-muted{color:#7e8993;font-size:12px}
            .info-box{margin-top:20px;padding:15px 20px;background:#fafafa;border-radius:4px;border-left:3px solid #1abc9c}
            .stat-value{font-size:28px;font-weight:700;color:#1d2327;margin-bottom:5px;line-height:1}
            .stat-label{font-size:12px;color:#7e8993;text-transform:uppercase;letter-spacing:0.3px;margin-bottom:8px}
            .stat-sublabel{font-size:12px;color:#a0a5aa}
        </style>
        <?php
    }
    
    /**
     * Renderiza cards principales
     */
    private static function render_main_cards($stats) {
        ?>
        <div class="dashboard-grid">
            <div class="dashboard-card dashboard-card-accent green">
                <div class="dashboard-card-header">Artículos</div>
                <div class="dashboard-card-value"><?php echo number_format($stats['total_articles']); ?></div>
                <div class="dashboard-card-label">Total generados</div>
            </div>
            
            <div class="dashboard-card dashboard-card-accent blue">
                <div class="dashboard-card-header">Metaetiquetas</div>
                <div class="dashboard-card-value"><?php echo number_format($stats['total_metas']); ?></div>
                <div class="dashboard-card-label">Total generados</div>
            </div>
            
            <div class="dashboard-card dashboard-card-accent orange">
                <div class="dashboard-card-header">Imágenes</div>
                <div class="dashboard-card-value"><?php echo number_format($stats['total_images']); ?></div>
                <div class="dashboard-card-label">Total generadas</div>
            </div>
            
            <div class="dashboard-card dashboard-card-accent purple">
                <div class="dashboard-card-header">Tokens</div>
                <div class="dashboard-card-value"><?php echo number_format($stats['tokens_total']); ?></div>
                <div class="dashboard-card-label">Total usados</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderiza sección de costos
     */
    private static function render_costs($stats) {
        ?>
        <div class="chart-container">
            <h3 style="margin-top:0;">Total de gastos
                <span class="asap-tooltip">?<span class="tooltiptext">Resumen de gastos en API de IA desglosados por período: hoy, semana, mes y total histórico. Incluye consumo de tokens.</span></span>
            </h3>
            <div class="dashboard-grid">
                <div>
                    <div class="stat-label">Hoy</div>
                    <div class="stat-value">$<?php echo number_format($stats['cost_today'], 4); ?></div>
                </div>
                <div>
                    <div class="stat-label">Esta Semana</div>
                    <div class="stat-value">$<?php echo number_format($stats['cost_week'], 4); ?></div>
                </div>
                <div>
                    <div class="stat-label">Este Mes</div>
                    <div class="stat-value">$<?php echo number_format($stats['cost_month'], 4); ?></div>
                </div>
                <div>
                    <div class="stat-label">Total Histórico</div>
                    <div class="stat-value">$<?php echo number_format($stats['cost_total'], 4); ?></div>
                </div>
            </div>
            

        </div>
        <?php
    }
    
    /**
     * Renderiza gráfico de actividad
     */
    private static function render_activity_chart($daily_stats) {
        if (empty($daily_stats)) return;
        ?>
        <div class="chart-container">
            <h3 style="margin-top:0;">Actividad de los últimos 7 días
                <span class="asap-tooltip">?<span class="tooltiptext">Gráfico de barras mostrando cuántas generaciones (artículos, metas, imágenes) realizaste cada día en la última semana.</span></span>
            </h3>
            <div class="bar-chart">
                <?php 
                $counts = array_column($daily_stats, 'count');
                $max_count = !empty($counts) ? max($counts) : 1;
                foreach (array_reverse($daily_stats) as $stat): 
                    $height = $max_count > 0 ? ($stat->count / $max_count) * 100 : 0;
                    $date_label = date('d/m', strtotime($stat->date));
                ?>
                    <div class="bar" style="height:<?php echo $height; ?>%;">
                        <div class="bar-value"><?php echo $stat->count; ?></div>
                        <div class="bar-label"><?php echo $date_label; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="small-muted" style="text-align:center;margin-top:35px;">Generaciones por día</p>
        </div>
        <?php
    }
    
    /**
     * Renderiza top keywords
     */
    private static function render_top_keywords($top_keywords) {
        if (empty($top_keywords)) return;
        ?>
        <div class="chart-container">
            <h3 style="margin-top:0;">Top palabras clave utilizadas
                <span class="asap-tooltip">?<span class="tooltiptext">Las palabras clave más utilizadas en tus artículos generados. Útil para identificar tus temas más frecuentes.</span></span>
            </h3>
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th style="width:100px;text-align:center;">Usos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_keywords as $kw): 
                        $keyword = trim($kw->keyword, '"');
                        if ($keyword === 'null' || empty($keyword)) continue;
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($keyword); ?></strong></td>
                        <td style="text-align:center;"><span class="badge badge-completed"><?php echo $kw->count; ?>x</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Renderiza últimas generaciones
     */
    private static function render_recent_generations($recent) {
        if (empty($recent)) return;
        ?>
        <div class="section-header">
            <h2>Últimas generaciones
                <span class="asap-tooltip">?<span class="tooltiptext">Listado de las 10 últimas operaciones con IA: artículos, metas, imágenes, ALT text. Incluye tokens, costos y enlaces directos.</span></span>
            </h2>
        </div>
        <table class="recent-table">
            <thead>
                <tr>
                    <th style="width:100px;">Tipo</th>
                    <th>Detalle</th>
                    <th style="width:120px;">Tokens</th>
                    <th style="width:100px;">Costo</th>
                    <th style="width:150px;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $log): 
                    $metadata = json_decode($log->metadata, true);
                    $type_labels = [
                        'article' => 'Artículo',
                        'meta' => 'Meta',
                        'image' => 'Imagen',
                        'alt_text' => 'ALT Text',
                    ];
                    $type_label = isset($type_labels[$log->type]) ? $type_labels[$log->type] : ucfirst($log->type);
                    
                    $detail = '';
                    
                    // Artículo
                    if ($log->type === 'article' && isset($metadata['h1'])) {
                        $detail = $metadata['h1'];
                        if ($log->post_id) {
                            $detail = '<a href="' . get_edit_post_link($log->post_id) . '" target="_blank">' . esc_html($detail) . '</a>';
                        }
                    }
                    // Meta tags
                    elseif ($log->type === 'meta' && $log->post_id) {
                        $post_title = get_the_title($log->post_id);
                        $detail = $post_title ? '<a href="' . get_edit_post_link($log->post_id) . '" target="_blank">' . esc_html($post_title) . '</a>' : 'Post ID: ' . $log->post_id;
                    }
                    // Imagen
                    elseif ($log->type === 'image' && $log->post_id) {
                        $post_title = get_the_title($log->post_id);
                        $detail = $post_title ? '<a href="' . get_edit_post_link($log->post_id) . '" target="_blank">' . esc_html($post_title) . '</a>' : 'Post ID: ' . $log->post_id;
                    }
                    // ALT Text
                    elseif ($log->type === 'alt_text' && isset($metadata['images_count'])) {
                        $count = $metadata['images_count'];
                        $detail = $count . ' ' . ($count == 1 ? 'imagen' : 'imágenes');
                        if ($log->post_id) {
                            $post_title = get_the_title($log->post_id);
                            if ($post_title) {
                                $detail .= ' en <a href="' . get_edit_post_link($log->post_id) . '" target="_blank">' . esc_html($post_title) . '</a>';
                            }
                        }
                    }
                    // Fallback genérico
                    elseif (isset($metadata['post_title'])) {
                        $detail = $metadata['post_title'];
                    } elseif ($log->post_id) {
                        $detail = 'Post ID: ' . $log->post_id;
                    } else {
                        $detail = '—';
                    }
                ?>
                <tr>
                    <td><?php echo $type_label; ?></td>
                    <td><?php echo $detail ?: '—'; ?></td>
                    <td style="font-family:monospace;font-size:12px;"><?php echo number_format($log->tokens_total); ?></td>
                    <td class="cost-highlight">$<?php echo number_format($log->cost_usd, 5); ?></td>
                    <td style="font-size:12px;color:#646970;"><?php echo date('d/m/Y H:i', strtotime($log->created_at)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Renderiza estadísticas adicionales
     */
    private static function render_additional_stats($stats, $ia_instance) {
        // Usar reflexión para acceder al método privado
        $reflection = new ReflectionMethod($ia_instance, 'get_queue_stats');
        $reflection->setAccessible(true);
        $queue_stats = $reflection->invoke($ia_instance);
        
        ?>
        <div class="chart-container">
            <h3 style="margin-top:0;">Análisis de ahorro
                <span class="asap-tooltip">?<span class="tooltiptext">Estimación del tiempo ahorrado usando IA vs. creación manual, costo promedio por artículo, y estado actual de la cola de generación.</span></span>
            </h3>
            <div class="dashboard-grid">
                <div>
                    <div class="stat-label">Tiempo Ahorrado Estimado</div>
                    <div class="stat-value">
                        <?php 
                        // Estimación: 1 artículo = 2 horas, 1 meta = 10 min, 1 imagen = 30 min
                        $hours_saved = ($stats['total_articles'] * 2) + ($stats['total_metas'] * 0.16) + ($stats['total_images'] * 0.5);
                        echo round($hours_saved);
                        ?>
                        <span style="font-size:18px;font-weight:400;color:#7e8993;margin-left:0px;">horas</span>
                    </div>
                </div>
                <div>
                    <div class="stat-label">Artículos Hoy</div>
                    <div class="stat-value">
                        <?php echo $stats['articles_today']; ?>
                    </div>
                </div>
                <div>
                    <div class="stat-label">Costo Promedio/Artículo</div>
                    <div class="stat-value">
                        $<?php echo $stats['total_articles'] > 0 ? number_format($stats['cost_total'] / $stats['total_articles'], 4) : '0.0000'; ?>
                    </div>
                </div>
                <div>
                    <div class="stat-label">Estado de la Cola</div>
                    <div class="stat-value">
                        <?php echo $queue_stats['pending']; ?>
                    </div>
                    <div class="stat-sublabel">
                        <a href="<?php echo admin_url('admin.php?page=asap-menu-ia&tab=ia_queue'); ?>" style="color:#1abc9c;text-decoration:none;">Ver cola →</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renderiza acciones rápidas
     */
    private static function render_quick_actions($stats) {
        // Usar reflexión para acceder al método privado get_queue_stats
        $reflection = new ReflectionMethod(self::$instance, 'get_queue_stats');
        $reflection->setAccessible(true);
        $queue_stats = $reflection->invoke(self::$instance);
        
        ?>
        <!--<div style="margin-top:30px;text-align:center;">
            <a href="<?php //echo admin_url('admin.php?page=asap-menu-ia&tab=ia_new'); ?>" class="button button-primary button-large" style="margin-right:10px;">
                Generar Nuevo Artículo
            </a>
            <a href="<?php //echo admin_url('admin.php?page=asap-menu-ia&tab=ia_queue'); ?>" class="button button-large">
                Ver Cola (<?php //echo $queue_stats['pending']; ?> pendientes)
            </a>
        </div>-->
        <?php
    }
}




