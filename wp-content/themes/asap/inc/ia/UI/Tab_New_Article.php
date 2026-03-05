<?php
/**
 * Tab: Nuevo Artículo
 * 
 * Renderiza el formulario completo para generar artículos con IA.
 * 
 * @package ASAP_Theme
 * @subpackage IA\UI
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_UI_Tab_New_Article {
    
    /**
     * Renderiza el tab completo
     * 
     * @param array $defaults Valores por defecto
     */
    public static function render($defaults) {
        ?>
        <section id="asap-options" class="asap-options section-content active">
            <?php self::render_styles(); ?>
            
            <form method="post" id="asap-ia-settings" class="form-table" novalidate onsubmit="return false;">
                <?php self::render_nonces(); ?>
                <?php self::render_header(); ?>
                
                <table class="form-table" id="asap-fieldset-one"><tbody>
                    <?php self::render_basic_fields($defaults); ?>
                    <?php self::render_competitor_analysis(); ?>
                    <?php self::render_outline_builder(); ?>
                    <?php self::render_extras($defaults); ?>
                    <?php self::render_wp_options($defaults); ?>
                    <?php self::render_references($defaults); ?>
                    <?php self::render_cost_section(); ?>
                    <?php self::render_generation_mode(); ?>
                    <?php self::render_actions(); ?>
                </tbody></table>
            </form>
        </section>
        
        <?php self::render_javascript(); ?>
        <?php
    }
    
    /**
     * Renderiza los estilos CSS
     */
    private static function render_styles() {
        ?>
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            #asap_manual_extra_prompt {
                line-height: 1.5 !important;
            }
            /* Prevenir scroll horizontal */
            .asap-options{max-width:100%}
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
            .crre-progress-container{width:460px;height:12px;background:#f0f0f1;border-radius:6px;overflow:hidden}
            .crre-progress-bar{width:0;height:100%;background:#1abc9c;transition:width .3s ease}
            .small-muted{color:#6d6d6d;font-size:12px}
            .outline-wrap{border:1px solid #ddd;padding:12px;border-radius:6px;max-width:780px;background:#fff}
            .h2-block{border:1px solid #e3e3e3;background:#fafafa;border-radius:6px;padding:10px;margin-bottom:10px}
            .h2-row{display:flex;gap:8px;align-items:center}
            .h2-row input[type="text"]{flex:1}
            .h3-list{margin-left:24px;margin-top:8px}
            .h3-row{display:flex;gap:8px;align-items:center;margin:6px 0}
            .h3-row input[type="text"]{flex:1}
            .btn-sm{padding:2px 8px;font-size:11px}
            .mt10{margin-top:10px}
            .mb6{margin-bottom:6px}
            .pill{display:inline-block;background:#baeae1;padding:4px 10px;border-radius:50px;margin:2px}
            .suggestions{padding:12px;border-radius:3px;background:#e8f8f5;display:none}
            .suggestions ul{margin:0;padding-left:0px; margin-top: 8px; margin-bottom: 16px;}
            .drag-handle{cursor:move;padding:4px 8px;color:#999;font-size:16px;user-select:none;display:inline-block}
            .drag-handle:hover{color:#2271b1}

            .pill:hover {
                background:#8cddcd !important;
            }
            .h2-block.ui-sortable-helper{opacity:0.8;box-shadow:0 4px 12px rgba(0,0,0,0.15)}
            .h3-row.ui-sortable-helper{opacity:0.8;box-shadow:0 2px 6px rgba(0,0,0,0.15)}
            .ui-sortable-placeholder{background:#e7f3ff !important;border:2px dashed #2271b1 !important;visibility:visible !important}
            #asap_outline.ui-sortable{min-height:50px}
            
            /* Estilos unificados para inputs y botones */
            .asap-input-standard{
                width:460px !important;
                height:36px !important;
                line-height:36px !important;
                padding:0 10px !important;
                font-size:14px !important;
                border:1px solid #8c8f94 !important;
                border-radius:4px !important;
                box-sizing:border-box !important;
            }
            .asap-input-standard:focus{
                border-color:#2271b1 !important;
                outline:none !important;
                box-shadow:0 0 0 1px #2271b1 !important;
            }
            .asap-button-standard{
                height:36px !important;
                line-height:34px !important;
                padding:0 16px !important;
                font-size:13px !important;
                white-space:nowrap !important;
                border-radius:4px !important;
                box-sizing:border-box !important;
            }
            /* Select estándar igual que inputs */
            select.asap-select-standard{
                width:460px !important;
                height:36px !important;
                line-height:36px !important;
                padding:0 10px !important;
                font-size:14px !important;
                border:1px solid #8c8f94 !important;
                border-radius:4px !important;
                box-sizing:border-box !important;
            }
            /* Todos los selects de WordPress también */
            .form-table select:not(.asap-lang-select2){
                height:36px !important;
                line-height:36px !important;
                padding:0 10px !important;
                font-size:14px !important;
                border:1px solid #8c8f94 !important;
                border-radius:4px !important;
            }
            /* Select2 personalizado para que coincida */
            .select2-container--default .select2-selection--single{
                height:36px !important;
                border:1px solid #8c8f94 !important;
                border-radius:4px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered{
                line-height:34px !important;
                padding-left:10px !important;
                font-size:14px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow{
                height:34px !important;
            }
            .select2-container{
                width:460px !important;
            }

            .asap-options .notice-success {
                border-left: none !important;
                border-radius: 3px !important;
                padding: 4px 42px 4px 12px !important;
                background: #e8f8f5 !important;
                color: #333 !important;
                margin-top: 14px;
            }

        </style>
        <?php
    }
    
    /**
     * Renderiza los nonces de seguridad
     */
    private static function render_nonces() {
        wp_nonce_field('asap_ia_defaults_action','asap_ia_defaults_nonce');
        wp_nonce_field('asap_manual_rewrite_action','asap_manual_rewrite_nonce');
        wp_nonce_field('asap_outline_action','asap_outline_nonce');
        wp_nonce_field('asap_calc_cost_nonce','asap_calc_cost_nonce');
        wp_nonce_field('asap_competitor_action','asap_competitor_nonce');
    }
    
    /**
     * Renderiza el encabezado
     */
    private static function render_header() {
        ?>
        <h2>IA — Generador de artículos
            <span class="asap-tooltip">?<span class="tooltiptext">
                Define H1, palabra clave, longitud y tono. Añade H2/H3 manualmente o pídele a la IA sugerencias.
                La IA respetará EXACTAMENTE la estructura que definas.
            </span></span>
        </h2>
        <?php
    }
    
    /**
     * Renderiza los campos básicos
     */
    private static function render_basic_fields($d) {
        ?>
        <!-- H1 -->
        <tr>
            <th><label for="asap_h1">H1</label><span class="asap-tooltip">?<span class="tooltiptext">El título principal del artículo. Debe ser claro, atractivo y contener la palabra clave. Este será el H1 del post.</span></span></th>
            <td><input type="text" id="asap_h1" class="asap-input-standard" placeholder="Ej: Guía completa de marketing de contenidos" /></td>
        </tr>
        
        <!-- Palabra clave principal (OPCIONAL) -->
        <tr>
            <th><label for="asap_keyword">Palabra clave objetivo</label><span class="asap-tooltip">?<span class="tooltiptext">OPCIONAL. Esta es tu palabra clave objetivo principal para posicionar en Google. Si no la defines, se usará el H1 como keyword principal.</span></span></th>
            <td>
                <div style="display:flex;gap:8px;align-items:stretch;">
                    <input type="text" id="asap_keyword" class="asap-input-standard" style="width:310px !important;" placeholder="Ej: marketing de contenidos">
                    <button type="button" class="button asap-button-standard" id="btn_get_related">Buscar relacionadas</button>
                    <span class="spinner" id="spinner_related" style="visibility:hidden;margin:0;flex-shrink:0;"></span>
                </div>
            </td>
        </tr>
        
        <!-- Palabras clave secundarias (OPCIONAL) -->
        <tr>
            <th><label for="asap_secondary_keywords">Palabras clave relacionadas</label><span class="asap-tooltip">?<span class="tooltiptext">OPCIONAL. Keywords relacionadas que la IA incluirá naturalmente en el contenido. Puedes agregarlas manualmente (escribe y presiona Enter) o hacer clic en las sugerencias de "Búsquedas relacionadas" arriba.</span></span></th>
            <td>
                <input type="text" id="asap_secondary_keywords_input" class="asap-input-standard" style="margin-bottom:8px;" placeholder="Escribe y presiona Enter para agregar">
                <div id="asap_secondary_keywords_list" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                    <!-- Las keywords se mostrarán aquí dinámicamente -->
                </div>
                <div id="related_keywords" style="display:none;margin-top:10px;padding:12px;background:#e8f8f5;border-radius:3px;">
                    <p style="margin:0 0 10px 0;font-weight:600;color:#333;">Búsquedas relacionadas</p>
                    <div id="related_keywords_list" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
                </div>
            </td>
        </tr>
        
        <!-- Longitud -->
        <tr>
            <th><label for="asap_target_len">Longitud</label><span class="asap-tooltip">?<span class="tooltiptext">Define aproximadamente cuántas palabras tendrá el artículo final. Los artículos más largos tienden a rankear mejor pero cuestan más en tokens.</span></span></th>
            <td>
                <select id="asap_target_len" name="asap_ia_default_length" class="asap-select-standard">
                    <option value="1000" <?php selected($d['target_len'], 1000); ?>>≈ 1.000 palabras</option>
                    <option value="1500" <?php selected($d['target_len'], 1500); ?>>≈ 1.500 palabras</option>
                    <option value="2000" <?php selected($d['target_len'], 2000); ?>>≈ 2.000 palabras</option>
                    <option value="2500" <?php selected($d['target_len'], 2500); ?>>≈ 2.500 palabras</option>
                    <option value="3000" <?php selected($d['target_len'], 3000); ?>>≈ 3.000 palabras</option>
                    <option value="3500" <?php selected($d['target_len'], 3500); ?>>≈ 3.500 palabras</option>
                    <option value="4000" <?php selected($d['target_len'], 4000); ?>>≈ 4.000 palabras</option>
                </select>
            </td>
        </tr>
        
        <!-- Tono/Estilo -->
        <tr>
            <th><label for="asap_ia_default_style">Tono</label><span class="asap-tooltip">?<span class="tooltiptext">El tono de escritura que usará la IA: informativo, persuasivo, conversacional, etc. Afecta directamente el estilo narrativo del artículo.</span></span></th>
            <td>
                <select id="asap_ia_default_style" name="asap_ia_default_style" class="asap-select-standard">
                    <option value="informativo"  <?php selected($d['style'],'informativo'); ?>>Informativo</option>
                    <option value="persuasivo"   <?php selected($d['style'],'persuasivo'); ?>>Persuasivo</option>
                    <option value="periodistico" <?php selected($d['style'],'periodistico'); ?>>Periodístico</option>
                    <option value="conversacional" <?php selected($d['style'],'conversacional'); ?>>Conversacional</option>
                    <option value="tecnico" <?php selected($d['style'],'tecnico'); ?>>Técnico</option>
                    <option value="storytelling" <?php selected($d['style'],'storytelling'); ?>>Storytelling</option>
                </select>
                
                <div id="style_preview" style="display:none;margin-top:10px;padding:12px;background:#e8f8f5;border-radius:3px;font-size:13px;line-height:1.6;color:#1d2327;">
                    <!-- Se llenará con JavaScript -->
                </div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza análisis de competencia
     */
    private static function render_competitor_analysis() {
        ?>
        <tr>
            <th>Análisis de competencia<span class="asap-tooltip">?<span class="tooltiptext">Pega la URL de un artículo competidor para copiar automáticamente su estructura H2/H3. Extrae automáticamente la estructura H2/H3 de cualquier artículo competidor. Útil para replicar estructuras exitosas y mejorarlas.</span></span></th>
            <td>
                    <div style="display:flex;align-items:stretch;gap:8px;margin-bottom:10px;">
                        <input type="text" id="competitor_url" name="competitor_url" class="asap-input-standard" style="width:310px !important;" placeholder="https://ejemplo.com/articulo-competidor" autocomplete="off">
                        <button type="button" class="button asap-button-standard" id="btn_extract_structure">Extraer estructura</button>
                        <span class="spinner" id="spinner_extract" style="visibility:hidden;margin:0;flex-shrink:0;"></span>
                    </div>
                <div id="competitor_result"></div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza el constructor de outline H2/H3
     */
    private static function render_outline_builder() {
        ?>
        <tr>
            <th>Estructura<span class="asap-tooltip">?<span class="tooltiptext">Define la estructura de encabezados del artículo. La IA respetará EXACTAMENTE esta estructura. Puedes reordenar arrastrando y soltar, y pedirle a la IA sugerencias.</span></span></th>
            <td>
                <div class="outline-wrap">
                    <div class="mb6 small-muted">
                        Añade tantos encabezados como quieras, la IA respetará exactamente esta estructura. O puedes dejarlo vacío para que se generen automáticamente.
                    </div>
                    <div id="asap_outline" style="display:none;"></div>
                    <div class="mt10">
                        <button type="button" class="button" id="btn_add_h2">+ Agregar H2</button>
                        <button type="button" class="button" id="btn_suggest_h2">Sugerir H2 (IA)</button>
                        <span class="spinner" id="spinner_suggest" style="float:none;visibility:hidden;"></span>
                    </div>
                    <div id="asap_outline_suggestions" class="suggestions mt10">
                        <strong>Sugerencias:</strong>
                        <ul id="asap_suggest_list"></ul>
                        <button type="button" class="button button-primary btn-sm" id="btn_add_selected_h2">Agregar seleccionados</button>
                        <button type="button" class="button btn-sm" id="btn_clear_suggestions">Ocultar</button>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza extras (FAQs, conclusión)
     */
    private static function render_extras($d) {
        ?>
        <tr>
            <th>Extras<span class="asap-tooltip">?<span class="tooltiptext">Secciones adicionales que enriquecen el artículo. Los artículos SIEMPRE incluyen una introducción de 1-3 párrafos cortos. Aquí puedes agregar FAQs con schema estructurado y conclusión.</span></span></th>
            <td>
                <div class="mt10">
                    <label><input type="checkbox" id="asap_faqs_enable" value="1" <?php checked($d['faqs_enable'], true); ?>> <strong>FAQs</strong></label>
                </div>
                <div id="faqs_count_wrapper" style="<?php echo $d['faqs_enable'] ? '' : 'display:none;'; ?>margin-top:8px;margin-left:24px;">
                    <label>
                        Cantidad de preguntas: 
                        <input type="number" min="1" max="15" id="asap_faqs_count" value="<?php echo esc_attr($d['faqs_count']); ?>" style="width:70px">
                    </label>
                </div>
                <div class="mt10">
                    <label><input type="checkbox" id="asap_conclusion_enable" value="1" <?php checked($d['conclusion_enable'], true); ?>> <strong>Conclusión</strong></label>
                </div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza opciones de WordPress
     */
    private static function render_wp_options($d) {
        ?>
        <tr><th><label for="asap_ia_default_status">Estado</label><span class="asap-tooltip">?<span class="tooltiptext">Define si el artículo se guardará como borrador (para revisar antes de publicar) o se publicará automáticamente.</span></span></th><td>
            <select name="asap_ia_default_status" id="asap_ia_default_status">
                <option value="draft"   <?php selected($d['status'],'draft'); ?>>Borrador</option>
                <option value="publish" <?php selected($d['status'],'publish'); ?>>Publicado</option>
            </select>
        </td></tr>
        <tr><th><label for="asap_ia_default_post_type">Tipo de contenido</label><span class="asap-tooltip">?<span class="tooltiptext">El tipo de contenido donde se creará el artículo: Entrada, Página, o cualquier Custom Post Type que tengas instalado.</span></span></th><td>
            <select name="asap_ia_default_post_type" id="asap_ia_default_post_type">
                <?php 
                foreach ( get_post_types(['public'=>true],'objects') as $obj ) : 
                    // Excluir 'attachment' (Medios) porque no tiene sentido para artículos
                    if ($obj->name === 'attachment') continue;
                ?>
                    <option value="<?php echo esc_attr($obj->name); ?>" <?php selected($d['post_type'],$obj->name); ?>><?php echo esc_html($obj->labels->singular_name); ?></option>
                <?php endforeach; ?>
            </select>
        </td></tr>
        <tr><th><label for="asap_ia_default_author">Autor</label><span class="asap-tooltip">?<span class="tooltiptext">El usuario que aparecerá como autor del artículo generado. Si seleccionas "Automático", usará el usuario actual.</span></span></th><td>
            <?php
            wp_dropdown_users([
                'name' => 'asap_ia_default_author',
                'id'   => 'asap_ia_default_author',
                'selected' => $d['author'],
                'who'  => 'authors',
                'show' => 'display_name',
                'show_option_none' => __('— Automático —','asap')
            ]);
            ?>
        </td></tr>
        
        <!-- Categorías -->
        <tr><th><label for="asap_ia_default_categories">Categorías</label><span class="asap-tooltip">?<span class="tooltiptext">Selecciona una o más categorías para asignar automáticamente al artículo generado. Puedes buscar escribiendo el nombre.</span></span></th><td>
            <?php
            $categories = get_categories(['hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC']);
            $selected_categories = !empty($d['categories']) ? (is_array($d['categories']) ? $d['categories'] : explode(',', $d['categories'])) : [];
            ?>
            <select name="asap_ia_default_categories[]" id="asap_ia_default_categories" multiple="multiple" style="width:100%;max-width:400px;">
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->term_id); ?>" <?php echo in_array($cat->term_id, $selected_categories) ? 'selected' : ''; ?>>
                        <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td></tr>
        <tr><th><label for="asap_ia_default_lang">Idioma</label><span class="asap-tooltip">?<span class="tooltiptext">El idioma en el que se generará todo el contenido del artículo. Afecta el texto, estilo narrativo y expresiones idiomáticas.</span></span></th><td>
            <select name="asap_ia_default_lang" id="asap_ia_default_lang" class="asap-lang-select2">
                <!-- Idiomas principales -->
                <option value="es" <?php selected($d['lang'],'es'); ?>>Español</option>
                <option value="en" <?php selected($d['lang'],'en'); ?>>Inglés</option>
                <option value="pt" <?php selected($d['lang'],'pt'); ?>>Portugués</option>
                <option value="fr" <?php selected($d['lang'],'fr'); ?>>Francés</option>
                <option value="de" <?php selected($d['lang'],'de'); ?>>Alemán</option>
                <option value="it" <?php selected($d['lang'],'it'); ?>>Italiano</option>
                <option value="ru" <?php selected($d['lang'],'ru'); ?>>Ruso</option>
                <option value="ja" <?php selected($d['lang'],'ja'); ?>>Japonés</option>
                <option value="zh" <?php selected($d['lang'],'zh'); ?>>Chino</option>
                <option value="ko" <?php selected($d['lang'],'ko'); ?>>Coreano</option>
                <option value="ar" <?php selected($d['lang'],'ar'); ?>>Árabe</option>
                <option value="hi" <?php selected($d['lang'],'hi'); ?>>Hindi</option>
                <option value="bn" <?php selected($d['lang'],'bn'); ?>>Bengali</option>
                <option value="tr" <?php selected($d['lang'],'tr'); ?>>Turco</option>
                <option value="id" <?php selected($d['lang'],'id'); ?>>Indonesio</option>
                <option value="ms" <?php selected($d['lang'],'ms'); ?>>Malayo</option>
                <option value="vi" <?php selected($d['lang'],'vi'); ?>>Vietnamita</option>
                <option value="th" <?php selected($d['lang'],'th'); ?>>Tailandés</option>
                <option value="tl" <?php selected($d['lang'],'tl'); ?>>Filipino</option>
                <option value="fa" <?php selected($d['lang'],'fa'); ?>>Persa</option>
                <option value="ur" <?php selected($d['lang'],'ur'); ?>>Urdu</option>
                
                <!-- Europa Occidental -->
                <option value="nl" <?php selected($d['lang'],'nl'); ?>>Holandés</option>
                <option value="sv" <?php selected($d['lang'],'sv'); ?>>Sueco</option>
                <option value="no" <?php selected($d['lang'],'no'); ?>>Noruego</option>
                <option value="da" <?php selected($d['lang'],'da'); ?>>Danés</option>
                <option value="fi" <?php selected($d['lang'],'fi'); ?>>Finlandés</option>
                <option value="is" <?php selected($d['lang'],'is'); ?>>Islandés</option>
                
                <!-- Europa del Este -->
                <option value="pl" <?php selected($d['lang'],'pl'); ?>>Polaco</option>
                <option value="uk" <?php selected($d['lang'],'uk'); ?>>Ucraniano</option>
                <option value="cs" <?php selected($d['lang'],'cs'); ?>>Checo</option>
                <option value="sk" <?php selected($d['lang'],'sk'); ?>>Eslovaco</option>
                <option value="hu" <?php selected($d['lang'],'hu'); ?>>Húngaro</option>
                <option value="ro" <?php selected($d['lang'],'ro'); ?>>Rumano</option>
                <option value="bg" <?php selected($d['lang'],'bg'); ?>>Búlgaro</option>
                <option value="hr" <?php selected($d['lang'],'hr'); ?>>Croata</option>
                <option value="sr" <?php selected($d['lang'],'sr'); ?>>Serbio</option>
                <option value="sl" <?php selected($d['lang'],'sl'); ?>>Esloveno</option>
                <option value="mk" <?php selected($d['lang'],'mk'); ?>>Macedonio</option>
                <option value="sq" <?php selected($d['lang'],'sq'); ?>>Albanés</option>
                <option value="et" <?php selected($d['lang'],'et'); ?>>Estonio</option>
                <option value="lv" <?php selected($d['lang'],'lv'); ?>>Letón</option>
                <option value="lt" <?php selected($d['lang'],'lt'); ?>>Lituano</option>
                
                <!-- Lenguas regionales Europa -->
                <option value="ca" <?php selected($d['lang'],'ca'); ?>>Catalán</option>
                <option value="eu" <?php selected($d['lang'],'eu'); ?>>Vasco</option>
                <option value="gl" <?php selected($d['lang'],'gl'); ?>>Gallego</option>
                <option value="cy" <?php selected($d['lang'],'cy'); ?>>Galés</option>
                <option value="ga" <?php selected($d['lang'],'ga'); ?>>Irlandés</option>
                <option value="mt" <?php selected($d['lang'],'mt'); ?>>Maltés</option>
                
                <!-- Mediterráneo y Medio Oriente -->
                <option value="el" <?php selected($d['lang'],'el'); ?>>Griego</option>
                <option value="he" <?php selected($d['lang'],'he'); ?>>Hebreo</option>
                
                <!-- Subcontinente Indio -->
                <option value="ta" <?php selected($d['lang'],'ta'); ?>>Tamil</option>
                <option value="te" <?php selected($d['lang'],'te'); ?>>Telugu</option>
                <option value="mr" <?php selected($d['lang'],'mr'); ?>>Marathi</option>
                <option value="gu" <?php selected($d['lang'],'gu'); ?>>Gujarati</option>
                <option value="kn" <?php selected($d['lang'],'kn'); ?>>Kannada</option>
                <option value="ml" <?php selected($d['lang'],'ml'); ?>>Malayalam</option>
                <option value="pa" <?php selected($d['lang'],'pa'); ?>>Punjabi</option>
                <option value="si" <?php selected($d['lang'],'si'); ?>>Sinhala</option>
                <option value="ne" <?php selected($d['lang'],'ne'); ?>>Nepalí</option>
                
                <!-- Sudeste Asiático -->
                <option value="my" <?php selected($d['lang'],'my'); ?>>Birmano</option>
                <option value="km" <?php selected($d['lang'],'km'); ?>>Camboyano</option>
                <option value="lo" <?php selected($d['lang'],'lo'); ?>>Laosiano</option>
                
                <!-- Asia Central y del Este -->
                <option value="mn" <?php selected($d['lang'],'mn'); ?>>Mongol</option>
                <option value="kk" <?php selected($d['lang'],'kk'); ?>>Kazajo</option>
                <option value="uz" <?php selected($d['lang'],'uz'); ?>>Uzbeko</option>
                
                <!-- África -->
                <option value="sw" <?php selected($d['lang'],'sw'); ?>>Swahili</option>
                <option value="af" <?php selected($d['lang'],'af'); ?>>Afrikaans</option>
                <option value="am" <?php selected($d['lang'],'am'); ?>>Amhárico</option>
                <option value="ha" <?php selected($d['lang'],'ha'); ?>>Hausa</option>
                <option value="yo" <?php selected($d['lang'],'yo'); ?>>Yoruba</option>
                <option value="zu" <?php selected($d['lang'],'zu'); ?>>Zulú</option>
            </select>
        </td></tr>
        <tr><th><label for="asap_manual_extra_prompt">Instrucciones extra</label><span class="asap-tooltip">?<span class="tooltiptext">Instrucciones adicionales personalizadas para la IA. Ejemplo: "usar más ejemplos", "evitar tecnicismos", "tono más cercano", etc.</span></span></th><td>
            <textarea id="asap_manual_extra_prompt" rows="3" class="asap-input-standard" style="height:auto !important;resize:vertical;" placeholder="Tono más conversacional, añadir ejemplos, casos reales, evitar jerga, etc."><?php echo esc_textarea($d['extra']); ?></textarea>
        </td></tr>
        <?php
    }
    
    /**
     * Renderiza sección de referencias E-E-A-T
     */
    private static function render_references($d) {
        ?>
        <tr>
            <th>Incluir referencias <span style="display:inline-block;background:#d1fae5;color:#065f46;font-size:10px;font-weight:600;padding:2px 6px;border-radius:3px;margin-left:4px;vertical-align:middle;">BETA</span><span class="asap-tooltip">?<span class="tooltiptext">Incluye una sección de referencias/fuentes al final del artículo para mejorar credibilidad y E-E-A-T (criterios de calidad de Google).</span></span></th>
            <td>
                <label>
                    <input type="checkbox" id="asap_include_references" value="1" <?php checked($d['include_references'], true); ?>>
                    Incluir sección de referencias al final
                </label>
                <div id="references_section" style="<?php echo $d['include_references'] ? '' : 'display:none;'; ?>margin-top:10px;">
                    <p class="description" style="margin-bottom:10px;">
                        La IA incluirá las fuentes consultadas al final del artículo para mejorar E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness).
                    </p>
                    <textarea id="asap_custom_references" rows="3" class="asap-input-standard" style="height:auto !important;resize:vertical;" placeholder="Opcional: Pega URLs específicas que quieras que se mencionen como fuentes (una por línea)&#10;https://ejemplo.com/fuente1&#10;https://ejemplo.com/fuente2"><?php echo esc_textarea($d['custom_references']); ?></textarea>
                    <p class="small-muted" style="margin-top:5px;">
                        Si dejas vacío, la IA generará referencias genéricas apropiadas para el tema.
                    </p>
                </div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza modo de generación
     */
    private static function render_generation_mode() {
        ?>
        <tr>
            <th>Modo de generación<span class="asap-tooltip">?<span class="tooltiptext">Elige si generar el artículo inmediatamente (1 artículo) o agregarlo a la cola para procesamiento en segundo plano (útil para múltiples artículos).</span></span></th>
            <td>
                <label style="margin-right:20px;">
                    <input type="radio" name="generation_mode" value="immediate" checked> 
                    Generar ahora
                </label>
                <label>
                    <input type="radio" name="generation_mode" value="queue"> 
                    Agregar a la cola
                </label>

            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza costo estimado y guardar configuración
     */
    private static function render_cost_section() {
        ?>
        <tr>
            <th>Costo estimado<span class="asap-tooltip">?<span class="tooltiptext">Calcula el costo aproximado según la longitud, estructura y extras seleccionados arriba.</span></span></th>
            <td>
                <button type="button" id="btn_calc_article_cost" class="button">Calcular costo</button>
                <span id="article_spinner_cost" class="spinner" style="float:none;visibility:hidden;margin-left:10px;"></span>
                <div id="article_cost_result" style="margin-top:10px;"></div>
            </td>
        </tr>
        <tr>
            <th>Guardar como predeterminado<span class="asap-tooltip">?<span class="tooltiptext">Guarda la configuración actual como valores predeterminados: <strong>longitud, tono, instrucciones extra, FAQs, conclusión, estado, tipo de contenido y autor</strong>. <strong>NO</strong> guarda el H1, keywords ni estructura (son únicos por artículo).</span></span></th>
            <td>
                <button type="button" id="btn_save_defaults" class="button">Guardar</button>
                <span id="spinner_save_defaults" class="spinner" style="float:none;visibility:hidden;margin-left:10px;"></span>
                <div id="result_save_defaults" style="margin-top:10px;"></div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza botones de acción
     */
    private static function render_actions() {
        ?>
        <tr>
            <th>Generar artículo<span class="asap-tooltip">?<span class="tooltiptext">Genera el artículo inmediatamente (puedes ver el progreso en tiempo real) o agrégalo a la cola para procesamiento en segundo plano.</span></span></th>
            <td>
                <!-- Botón dinámico según modo -->
                <button type="button" id="asap_manual_rewrite" class="button button-primary" style="margin-right:10px;">
                    Generar ahora
                </button>
                <span id="asap_manual_spinner" class="spinner" style="float:none;visibility:hidden;"></span>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <div id="asap_manual_progress" style="display:none; margin-top:1em;">
                    <div class="crre-progress-container"><div class="crre-progress-bar" id="asap_manual_progress_bar"></div></div>
                    <p id="asap_manual_progress_text" class="small-muted"></p>
                </div>
                <div id="asap_manual_result" style="margin-top:1em;"></div>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderiza todo el JavaScript
     */
    private static function render_javascript() {
        ?>
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
        jQuery(function($){
            'use strict';
            
            var params = new URLSearchParams(window.location.search);
            if (params.get('page') !== 'asap-menu-ia') return;
            if (params.get('tab') && params.get('tab') !== 'ia_new') return;

            var outlineData = []; // {h2:"texto", h3:["a","b"]}
            
            // ========================================================================
            // FUNCIONES DE UTILIDAD
            // ========================================================================
            
            function generateId() {
                return 'h2_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            }
            
            function sanitizeText(text) {
                return $('<div>').text(text).html();
            }
            
            function showToast(message, type) {
                type = type || 'info';
                var color = type === 'success' ? '#1abc9c' : type === 'error' ? '#dc3232' : '#2271b1';
                var $toast = $('<div style="position:fixed;top:20px;right:20px;z-index:999999;background:' + color + ';color:#fff;padding:12px 20px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,0.15);font-size:13px;min-width:250px;">' + message + '</div>');
                $('body').append($toast);
                setTimeout(function(){ $toast.fadeOut(300, function(){ $toast.remove(); }); }, 3000);
            }
            
            // ========================================================================
            // MOSTRAR/OCULTAR CANTIDAD DE FAQs
            // ========================================================================
            
            $('#asap_faqs_enable').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#faqs_count_wrapper').slideDown(200);
                } else {
                    $('#faqs_count_wrapper').slideUp(200);
                }
            });
            
            // ========================================================================
            // OUTLINE BUILDER - Gestión de H2/H3
            // ========================================================================
            
            function renderOutline() {
                var $container = $('#asap_outline');
                $container.empty();
                
                // Ocultar si no hay H2s, mostrar si hay
                if (outlineData.length === 0) {
                    $container.hide();
                    return;
                } else {
                    $container.show();
                }
                
                outlineData.forEach(function(h2Obj, idx) {
                    var h2Id = h2Obj.id || generateId();
                    if (!h2Obj.id) h2Obj.id = h2Id;
                    
                    var $h2Block = $('<div class="h2-block" data-id="' + h2Id + '"></div>');
                    
                    // Fila del H2
                    var $h2Row = $('<div class="h2-row"></div>');
                    $h2Row.append('<span class="drag-handle" title="Arrastra para reordenar">⠿</span>');
                    $h2Row.append('<input type="text" placeholder="H2" value="' + sanitizeText(h2Obj.h2) + '" data-type="h2">');
                    $h2Row.append('<button type="button" class="button btn-sm btn-add-h3">+ H3</button>');
                    $h2Row.append('<button type="button" class="button btn-sm btn-remove-h2" style="background:#dc3232;color:#fff;border:none;">✕</button>');
                    $h2Block.append($h2Row);
                    
                    // Lista de H3
                    if (h2Obj.h3 && h2Obj.h3.length > 0) {
                        var $h3List = $('<div class="h3-list"></div>');
                        h2Obj.h3.forEach(function(h3Text, h3Idx) {
                            var $h3Row = $('<div class="h3-row"></div>');
                            $h3Row.append('<span class="drag-handle" title="Arrastra para reordenar">⠿</span>');
                            $h3Row.append('<input type="text" placeholder="H3" value="' + sanitizeText(h3Text) + '" data-type="h3" data-h3-idx="' + h3Idx + '">');
                            $h3Row.append('<button type="button" class="button btn-sm btn-remove-h3" style="background:#dc3232;color:#fff;border:none;">✕</button>');
                            $h3List.append($h3Row);
                        });
                        $h2Block.append($h3List);
                    }
                    
                    $container.append($h2Block);
                });
                
                // Inicializar sortable para H2
                $container.sortable({
                    handle: '.drag-handle',
                    axis: 'y',
                    items: '> .h2-block',
                    placeholder: 'ui-sortable-placeholder',
                    update: function() {
                        syncOutlineFromDOM();
                    }
                });
                
                // Inicializar sortable para H3
                $('.h3-list').sortable({
                    handle: '.drag-handle',
                    axis: 'y',
                    items: '> .h3-row',
                    placeholder: 'ui-sortable-placeholder',
                    update: function() {
                        syncOutlineFromDOM();
                    }
                });
            }
            
            function syncOutlineFromDOM() {
                var newData = [];
                $('#asap_outline > .h2-block').each(function() {
                    var $block = $(this);
                    var h2Id = $block.data('id');
                    var h2Text = $block.find('input[data-type="h2"]').val().trim();
                    var h3List = [];
                    
                    $block.find('.h3-list .h3-row').each(function() {
                        var h3Text = $(this).find('input[data-type="h3"]').val().trim();
                        if (h3Text) h3List.push(h3Text);
                    });
                    
                    if (h2Text) {
                        newData.push({id: h2Id, h2: h2Text, h3: h3List});
                    }
                });
                outlineData = newData;
            }
            
            // ========================================================================
            // EVENTOS DEL OUTLINE
            // ========================================================================
            
            // Agregar H2 manualmente
            $('#btn_add_h2').on('click', function() {
                outlineData.push({id: generateId(), h2: '', h3: []});
                renderOutline();
                // Focus en el último H2 agregado
                setTimeout(function(){
                    $('#asap_outline .h2-block:last input[data-type="h2"]').focus();
                }, 100);
            });
            
            // Eventos delegados para botones dinámicos
            $('#asap_outline').on('click', '.btn-remove-h2', function() {
                var $block = $(this).closest('.h2-block');
                var h2Id = $block.data('id');
                outlineData = outlineData.filter(function(item) { return item.id !== h2Id; });
                renderOutline();
            });
            
            $('#asap_outline').on('click', '.btn-add-h3', function() {
                var $block = $(this).closest('.h2-block');
                var h2Id = $block.data('id');
                var h2Obj = outlineData.find(function(item) { return item.id === h2Id; });
                if (h2Obj) {
                    if (!h2Obj.h3) h2Obj.h3 = [];
                    h2Obj.h3.push('');
                    renderOutline();
                    // Focus en el último H3 agregado
                    setTimeout(function(){
                        $block.find('.h3-list .h3-row:last input[data-type="h3"]').focus();
                    }, 100);
                }
            });
            
            $('#asap_outline').on('click', '.btn-remove-h3', function() {
                var $row = $(this).closest('.h3-row');
                var $block = $row.closest('.h2-block');
                var h2Id = $block.data('id');
                var h3Idx = $row.index();
                var h2Obj = outlineData.find(function(item) { return item.id === h2Id; });
                if (h2Obj && h2Obj.h3) {
                    h2Obj.h3.splice(h3Idx, 1);
                    renderOutline();
                }
            });
            
            // Sincronizar cambios de texto en tiempo real
            $('#asap_outline').on('input', 'input[type="text"]', function() {
                syncOutlineFromDOM();
            });
            
            // ========================================================================
            // SUGERIR H2 CON IA
            // ========================================================================
            
            $('#btn_suggest_h2').on('click', function() {
                var h1 = $('#asap_h1').val().trim();
                var keyword = $('#asap_keyword').val().trim();
                
                if (!h1) {
                    alert('Por favor ingresa el H1 primero.');
                    return;
                }
                
                // Si no hay keyword, usar el H1 como keyword
                if (!keyword) {
                    keyword = h1;
                }
                
                var existing = outlineData.map(function(item) { return item.h2; });
                
                $('#spinner_suggest').css('visibility', 'visible');
                $(this).prop('disabled', true);
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_suggest_outline',
                        nonce: $('#asap_outline_nonce').val(),
                        h1: h1,
                        keyword: keyword,
                        existing: existing
                    },
                    success: function(resp) {
                        if (resp && resp.success && resp.data && resp.data.suggestions) {
                            var suggestions = resp.data.suggestions;
                            var $list = $('#asap_suggest_list').empty();
                            
                            if (suggestions.length === 0) {
                                $list.append('<li>No hay sugerencias disponibles.</li>');
                            } else {
                                suggestions.forEach(function(sug) {
                                    $list.append('<li><label><input type="checkbox" value="' + sanitizeText(sug) + '"> ' + sanitizeText(sug) + '</label></li>');
                                });
                            }
                            
                            $('#asap_outline_suggestions').slideDown();
                            showToast('✨ ' + suggestions.length + ' sugerencias generadas', 'success');
                        } else {
                            var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error al obtener sugerencias';
                            showToast(msg, 'error');
                        }
                    },
                    error: function() {
                        showToast('Error de conexión', 'error');
                    },
                    complete: function() {
                        $('#spinner_suggest').css('visibility', 'hidden');
                        $('#btn_suggest_h2').prop('disabled', false);
                    }
                });
            });
            
            // Agregar sugerencias seleccionadas
            $('#btn_add_selected_h2').on('click', function() {
                var selected = [];
                $('#asap_suggest_list input:checked').each(function() {
                    selected.push($(this).val());
                });
                
                if (selected.length === 0) {
                    alert('Selecciona al menos una sugerencia.');
                    return;
                }
                
                selected.forEach(function(h2Text) {
                    outlineData.push({id: generateId(), h2: h2Text, h3: []});
                });
                
                renderOutline();
                $('#asap_outline_suggestions').slideUp();
                showToast('✓ ' + selected.length + ' H2 agregados', 'success');
            });
            
            $('#btn_clear_suggestions').on('click', function() {
                $('#asap_outline_suggestions').slideUp();
            });
            
            // ========================================================================
            // ANÁLISIS DE COMPETENCIA
            // ========================================================================
            
            $('#btn_extract_structure').on('click', function(e) {
                e.preventDefault(); // Prevenir cualquier submit
                
                var url = $('#competitor_url').val().trim();
                
                // Si no hay URL, simplemente no hacer nada (sin alerta)
                if (!url) {
                    return;
                }
                
                $('#spinner_extract').css('visibility', 'visible');
                $(this).prop('disabled', true);
                $('#competitor_result').empty();
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    timeout: 35000, // 35 segundos
                    data: {
                        action: 'asap_extract_competitor_structure',
                        nonce: $('#asap_competitor_nonce').val(),
                        url: url
                    },
                    success: function(resp) {
                        if (resp && resp.success && resp.data) {
                            var data = resp.data;
                            var msg = '<div style="background:#e8f8f5;padding:12px;border-radius:3px;margin-top:10px;">';
                            msg += '<p style="margin:0 0 8px 0;"><strong style="color:#1abc9c;">✓ Estructura extraída:</strong></p>';
                            msg += '<p style="margin:0;font-size:12px;color:#666;">H1: <strong>' + sanitizeText(data.h1) + '</strong><br>';
                            msg += 'H2: ' + data.h2_count + ' | H3: ' + data.h3_count + '</p>';
                            
                            // Mostrar lista de H2s
                            if (data.h2 && data.h2.length > 0) {
                                msg += '<div style="margin-top:10px;padding:10px;background:#f9f9f9;border-radius:4px;max-height:200px;overflow-y:auto;">';
                                msg += '<p style="margin:0 0 5px 0;font-size:11px;color:#666;font-weight:600;">ENCABEZADOS H2 ENCONTRADOS:</p>';
                                msg += '<ol style="margin:0;padding-left:20px;font-size:12px;color:#333;">';
                                data.h2.forEach(function(h2) {
                                    msg += '<li style="margin:3px 0;">' + sanitizeText(h2) + '</li>';
                                });
                                msg += '</ol></div>';
                            }
                            
                            msg += '<button type="button" class="button button-small" id="btn_import_competitor_structure" style="margin-top:8px;">✓ Importar estructura</button>';
                            msg += '</div>';
                            
                            $('#competitor_result').html(msg).data('structure', data.structure);
                            showToast('✓ Estructura extraída exitosamente', 'success');
                        } else {
                            var errorMsg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error desconocido al extraer estructura';
                            var errorDiv = '<div style="background:#fff;padding:12px;border-left:3px solid #dc3232;border-radius:4px;margin-top:10px;">';
                            errorDiv += '<p style="margin:0;color:#dc3232;"><strong>✗ Error:</strong> ' + errorMsg + '</p>';
                            errorDiv += '<p style="margin:8px 0 0 0;font-size:12px;color:#666;">Verifica: 1) URL válida, 2) Sitio accesible, 3) No bloquea scraping</p>';
                            errorDiv += '</div>';
                            $('#competitor_result').html(errorDiv);
                            showToast('Error al extraer estructura', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMsg = 'Error de conexión';
                        if (status === 'timeout') {
                            errorMsg = 'Timeout: El sitio tardó demasiado en responder (>35 seg)';
                        } else if (xhr.responseText) {
                            try {
                                var resp = JSON.parse(xhr.responseText);
                                if (resp && resp.data && resp.data.message) {
                                    errorMsg = resp.data.message;
                                }
                            } catch(e) {}
                        }
                        
                        var errorDiv = '<div style="background:#fff;padding:12px;border-left:3px solid #dc3232;border-radius:4px;margin-top:10px;">';
                        errorDiv += '<p style="margin:0;color:#dc3232;"><strong>✗ Error:</strong> ' + errorMsg + '</p>';
                        errorDiv += '<p style="margin:8px 0 0 0;font-size:12px;color:#666;">Posibles causas: 1) Timeout, 2) Sitio bloquea bots, 3) Cloudflare/protección activa</p>';
                        errorDiv += '</div>';
                        $('#competitor_result').html(errorDiv);
                        showToast('Error de conexión', 'error');
                    },
                    complete: function() {
                        $('#spinner_extract').css('visibility', 'hidden');
                        $('#btn_extract_structure').prop('disabled', false);
                    }
                });
            });
            
            // Importar estructura extraída
            $(document).on('click', '#btn_import_competitor_structure', function() {
                var structure = $('#competitor_result').data('structure');
                if (structure && structure.length > 0) {
                    outlineData = structure.map(function(item) {
                        return {
                            id: generateId(),
                            h2: item.h2,
                            h3: item.h3 || []
                        };
                    });
                    renderOutline();
                    showToast('✓ Estructura importada (' + outlineData.length + ' H2)', 'success');
                    $('#competitor_result').slideUp();
                }
            });
            
            // ========================================================================
            // PALABRAS CLAVE SECUNDARIAS (TAGS)
            // ========================================================================
            
            var secondaryKeywords = [];
            
            function renderSecondaryKeywords() {
                var $list = $('#asap_secondary_keywords_list');
                $list.empty();
                
                // No mostrar ningún placeholder si no hay keywords
                if (secondaryKeywords.length === 0) {
                    return;
                }
                
                secondaryKeywords.forEach(function(kw, index) {
                    var $tag = $('<span class="secondary-keyword-tag" style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:#1abc9c;color:#fff;border-radius:3px;font-size:12px;">' + 
                        sanitizeText(kw) + 
                        '<span class="remove-tag" data-index="' + index + '" style="cursor:pointer;font-weight:bold;margin-left:3px;">×</span>' +
                        '</span>');
                    $list.append($tag);
                });
            }
            
            function addSecondaryKeyword(keyword) {
                keyword = keyword.trim();
                if (!keyword || secondaryKeywords.indexOf(keyword) !== -1) {
                    return;
                }
                secondaryKeywords.push(keyword);
                renderSecondaryKeywords();
            }
            
            // Agregar con Enter
            $('#asap_secondary_keywords_input').on('keypress', function(e) {
                if (e.which === 13) { // Enter
                    e.preventDefault();
                    var keyword = $(this).val().trim();
                    if (keyword) {
                        addSecondaryKeyword(keyword);
                        $(this).val('');
                        showToast('✓ Keyword secundaria agregada', 'success');
                    }
                }
            });
            
            // Eliminar tag
            $(document).on('click', '.remove-tag', function() {
                var index = $(this).data('index');
                secondaryKeywords.splice(index, 1);
                renderSecondaryKeywords();
                showToast('✓ Keyword eliminada', 'success');
            });
            
            // ========================================================================
            // BÚSQUEDAS RELACIONADAS (AGREGAN COMO TAGS SECUNDARIOS)
            // ========================================================================
            
            $('#btn_get_related').on('click', function() {
                var keyword = $('#asap_keyword').val().trim();
                var h1 = $('#asap_h1').val().trim();
                
                // Si no hay keyword, usar el H1
                if (!keyword && h1) {
                    keyword = h1;
                }
                
                if (!keyword) {
                    alert('Por favor ingresa el H1 o una palabra clave primero.');
                    return;
                }
                
                $('#spinner_related').css('visibility', 'visible');
                $(this).prop('disabled', true);
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_get_related_keywords',
                        nonce: '<?php echo wp_create_nonce('asap_related_keywords'); ?>',
                        keyword: keyword
                    },
                    success: function(resp) {
                        if (resp && resp.success && resp.data && resp.data.keywords) {
                            var keywords = resp.data.keywords;
                            var source = resp.data.source || 'IA';
                            var $list = $('#related_keywords_list').empty();
                            
                            if (keywords.length === 0) {
                                $list.html('<p style="color:#666;">No se encontraron keywords relacionadas.</p>');
                            } else {
                                keywords.forEach(function(kw) {
                                    var $pill = $('<span class="pill" style="cursor:pointer;transition:all 0.2s;">' + sanitizeText(kw) + '</span>');
                                    $pill.on('click', function() {
                                        addSecondaryKeyword(kw);
                                        showToast('✓ Agregada como keyword secundaria', 'success');
                                        // Desaparecer el pill al hacer clic
                                        $(this).fadeOut(300, function() {
                                            $(this).remove();
                                        });
                                    });

                                    $list.append($pill);
                                });
                                
                                $('#related_keywords p:first').html('Búsquedas relacionadas');
                            }
                            
                            $('#related_keywords').slideDown();
                        } else {
                            var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error al obtener keywords';
                            showToast(msg, 'error');
                        }
                    },
                    error: function() {
                        showToast('Error de conexión', 'error');
                    },
                    complete: function() {
                        $('#spinner_related').css('visibility', 'hidden');
                        $('#btn_get_related').prop('disabled', false);
                    }
                });
            });
            
            // ========================================================================
            // PREVIEW DEL ESTILO
            // ========================================================================
            
            var styleDescriptions = {
                'informativo': '<strong>Informativo:</strong> Tono objetivo, directo, basado en hechos. Ideal para artículos educativos, guías y tutoriales donde la claridad es prioridad.',
                'persuasivo': '<strong>Persuasivo:</strong> Orientado a la conversión. Usa argumentos convincentes, beneficios claros y CTAs. Perfecto para landings, reseñas de productos y páginas de ventas.',
                'periodistico': '<strong>Periodístico:</strong> Estilo de reportaje profesional. Estructura piramidal invertida, datos verificables, citas. Para noticias y artículos de actualidad.',
                'conversacional': '<strong>Conversacional:</strong> Cercano y amigable, como hablar con un amigo. Usa "tú" o "vos", ejemplos cotidianos. Ideal para blogs personales y lifestyle.',
                'tecnico': '<strong>Técnico:</strong> Preciso y especializado. Usa terminología específica, explicaciones detalladas. Para documentación, whitepapers y contenido B2B.',
                'storytelling': '<strong>Storytelling:</strong> Narrativo y emocional. Cuenta historias, usa personajes y conflictos. Excelente para conectar emocionalmente con la audiencia.',
                'seo': '<strong>SEO-Friendly:</strong> Optimizado para buscadores sin sacrificar legibilidad. Balance entre keywords naturales y contenido de valor.'
            };
            
            $('#asap_ia_default_style').on('change', function() {
                var style = $(this).val();
                var desc = styleDescriptions[style] || '';
                if (desc) {
                    $('#style_preview').html(desc).slideDown();
                }
            });
            
            // ========================================================================
            // REFERENCIAS E-E-A-T
            // ========================================================================
            
            $('#asap_include_references').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#references_section').slideDown();
                } else {
                    $('#references_section').slideUp();
                }
            });
            
            // ========================================================================
            // MODO DE GENERACIÓN
            // ========================================================================
            
            $('input[name="generation_mode"]').on('change', function() {
                var mode = $(this).val();
                if (mode === 'immediate') {
                    $('#asap_manual_rewrite').html('Generar ahora');
                } else {
                    $('#asap_manual_rewrite').html('Agregar a cola');
                }
            });
            
            // ========================================================================
            // CALCULAR COSTO
            // ========================================================================
            
            $('#btn_calc_article_cost').on('click', function() {
                var target_len = parseInt($('#asap_target_len').val()) || 3000;
                var outline_count = outlineData.length;
                
                // La introducción SIEMPRE está incluida
                var intro = true;
                var faqs = $('#asap_faqs_enable').is(':checked');
                var faqs_count = parseInt($('#asap_faqs_count').val()) || 5;
                var conclusion = $('#asap_conclusion_enable').is(':checked');
                
                var $spinner = $('#article_spinner_cost');
                var $result = $('#article_cost_result');
                
                $spinner.css('visibility', 'visible');
                $result.empty();
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: {
                        action: 'asap_calc_article_cost',
                        nonce: $('#asap_calc_cost_nonce').val(),
                        target_len: target_len,
                        outline_count: outline_count,
                        intro_enable: intro,
                        faqs_enable: faqs,
                        faqs_count: faqs_count,
                        conclusion_enable: conclusion
                    },
                    success: function(resp) {
                        if (resp && resp.success && resp.data) {
                            var html = '<div style="background:#e8f8f5;padding:12px;border-radius:3px;">';
                            html += '<p style="margin:0;font-size:14px;"><strong>Costo estimado:</strong> <span style="color:#1abc9c;font-size:16px;font-weight:600;">$' + resp.data.cost_usd + ' USD</span></p>';
                            if (resp.data.tokens_input) {
                                html += '<p style="margin:8px 0 0 0;font-size:12px;color:#666;">Tokens entrada: ' + resp.data.tokens_input.toLocaleString() + '</p>';
                            }
                            if (resp.data.tokens_output) {
                                html += '<p style="margin:4px 0 0 0;font-size:12px;color:#666;">Tokens salida: ' + resp.data.tokens_output.toLocaleString() + '</p>';
                            }
                            if (resp.data.note) {
                                html += '<p style="margin:8px 0 0 0;font-size:11px;color:#999;">' + resp.data.note + '</p>';
                            }
                            html += '</div>';
                            $result.html(html);
                        } else {
                            $result.html('<div style="background:#fff;padding:12px;border-left:3px solid #dc3232;border-radius:4px;"><p style="margin:0;color:#dc3232;">Error al calcular el costo</p></div>');
                        }
                    },
                    error: function() {
                        $result.html('<div style="background:#fff;padding:12px;border-left:3px solid #dc3232;border-radius:4px;"><p style="margin:0;color:#dc3232;">Error de conexión</p></div>');
                    },
                    complete: function() {
                        $spinner.css('visibility', 'hidden');
                    }
                });
            });
            
            // ========================================================================
            // GENERAR ARTÍCULO O AGREGAR A COLA
            // ========================================================================
            
            var isGeneratingStructure = false; // Bandera para evitar loops
            
            // DESACTIVAR VALIDACIÓN HTML5 - MODO ULTRA AGRESIVO
            
            // 1. Bloquear el form principal
            var mainForm = document.getElementById('asap-ia-settings');
            if (mainForm) {
                mainForm.setAttribute('novalidate', 'novalidate');
                mainForm.noValidate = true;
                mainForm.onsubmit = function() { return false; };
                
                // Múltiples listeners para asegurar que NUNCA se submitee
                mainForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, {capture: true, passive: false});
                
                // Bloquear TODOS los inputs dentro del form
                mainForm.querySelectorAll('input').forEach(function(input) {
                    input.removeAttribute('required');
                    input.removeAttribute('pattern');
                    // Si es type url, cambiarlo a text
                    if (input.type === 'url') {
                        input.setAttribute('type', 'text');
                    }
                });
            }
            
            // 2. Campo competitor_url específicamente
            var competitorInput = document.getElementById('competitor_url');
            if (competitorInput) {
                competitorInput.removeAttribute('pattern');
                competitorInput.removeAttribute('required');
                competitorInput.setAttribute('type', 'text');
                competitorInput.type = 'text'; // Doble seguridad
                
                competitorInput.addEventListener('invalid', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, {capture: true, passive: false});
            }
            
            // 3. Bloquear TODOS los forms de la página (por si otro plugin mete algo)
            document.querySelectorAll('form').forEach(function(form) {
                form.setAttribute('novalidate', 'novalidate');
                form.noValidate = true;
            });
            
            // 🔍 DEBUG: Listener GLOBAL para detectar CUALQUIER campo que dispare validación
            document.addEventListener('invalid', function(e) {
                console.error('🔴🔴🔴 EVENTO INVALID GLOBAL DISPARADO 🔴🔴🔴');
                console.error('Campo problemático:', {
                    tag: e.target.tagName,
                    type: e.target.type,
                    id: e.target.id,
                    name: e.target.name,
                    value: e.target.value,
                    required: e.target.required,
                    validationMessage: e.target.validationMessage,
                    form: e.target.form ? e.target.form.id : 'sin form'
                });
                console.error('Stack trace:', new Error().stack);
                e.preventDefault(); // Prevenir el mensaje del navegador
                e.stopPropagation(); // Detener propagación
                e.stopImmediatePropagation(); // Detener TODOS los listeners
                return false;
            }, true); // true = captura en fase de captura (antes que bubbling)
            
            $('#asap_manual_rewrite').on('click', function(e) {
                // CRÍTICO: Prevenir CUALQUIER comportamiento del formulario
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Desactivar validación de TODOS los forms de la página (por si acaso)
                document.querySelectorAll('form').forEach(function(f) {
                    f.setAttribute('novalidate', 'novalidate');
                });
                
                syncOutlineFromDOM();
                
                var h1 = $('#asap_h1').val().trim();
                var keyword = $('#asap_keyword').val().trim();
                
                // Si no hay keyword, usar el H1 como keyword principal
                if (!keyword && h1) {
                    keyword = h1;
                }
                
                var target_len = $('#asap_target_len').val();
                var style = $('#asap_ia_default_style').val();
                var lang = $('#asap_ia_default_lang').val();
                var extra = $('#asap_manual_extra_prompt').val().trim();
                
                var status = $('#asap_ia_default_status').val();
                var post_type = $('#asap_ia_default_post_type').val();
                var author = $('#asap_ia_default_author').val();
                
                // La introducción SIEMPRE está habilitada (1-3 párrafos cortos)
                var intro_enable = true;
                var intro_custom = ''; // Ya no hay campo personalizado
                var faqs_enable = $('#asap_faqs_enable').is(':checked');
                var faqs_count = $('#asap_faqs_count').val();
                var conclusion_enable = $('#asap_conclusion_enable').is(':checked');
                
                var include_references = $('#asap_include_references').is(':checked');
                var custom_references = $('#asap_custom_references').val().trim();
                
                // ⭐ Categorías
                var categories = $('#asap_ia_default_categories').val() || [];
                
                var generation_mode = $('input[name="generation_mode"]:checked').val();
                
                if (!h1) {
                    alert('Por favor ingresa el H1.');
                    return;
                }
                
                // Si no hay estructura, permitir continuar (se generará automáticamente en el backend)
                if (outlineData.length === 0) {
                    // Solo mostrar confirmación para "Generar ahora", no para "Agregar a cola"
                    if (generation_mode === 'immediate') {
                        if (!confirm('No has definido una estructura H2/H3. La IA generará automáticamente una estructura basada en el título y keywords durante la generación. ¿Continuar?')) {
                            return;
                        }
                    }
                    // Continuar con outline vacío - se generará automáticamente en el Article_Generator
                }
                
                var $btn = $(this);
                var $spinner = $('#asap_manual_spinner');
                var $progress = $('#asap_manual_progress');
                var $progressBar = $('#asap_manual_progress_bar');
                var $progressText = $('#asap_manual_progress_text');
                var $result = $('#asap_manual_result');
                
                $btn.prop('disabled', true);
                $spinner.css('visibility', 'visible');
                $result.empty();
                
                if (generation_mode === 'queue') {
                    // Preparar datos para la cola
                    var queueData = {
                        action: 'asap_add_to_queue',
                        nonce: $('#asap_manual_rewrite_nonce').val(),
                        h1: h1,
                        keyword: keyword,
                        target_len: target_len,
                        style: style,
                        lang: lang,
                        extra: extra,
                        outline: JSON.stringify(outlineData),
                        status: status,
                        post_type: post_type,
                        author: author,
                        intro_enable: intro_enable ? '1' : '0',
                        intro_custom: intro_custom || '',
                        faqs_enable: faqs_enable ? '1' : '0',
                        faqs_count: faqs_count,
                        conclusion_enable: conclusion_enable ? '1' : '0',
                        include_references: include_references ? '1' : '0',
                        custom_references: custom_references || '',
                        // ⭐ Categorías
                        'categories[]': categories
                    };
                    
                    // Agregar secondary_keywords como array
                    if (secondaryKeywords && secondaryKeywords.length > 0) {
                        secondaryKeywords.forEach(function(kw, index) {
                            queueData['secondary_keywords[' + index + ']'] = kw;
                        });
                    }
                    
                    // Agregar a cola
                    $.ajax({
                        url: ASAP_IA.ajax,
                        type: 'POST',
                        data: queueData,
                        success: function(resp) {
                            console.log('✅ Respuesta del servidor:', resp);
                            if (resp && resp.success) {
                                var html = '<div class="notice notice-success"><p><strong>' + (resp.data.message || '✓ Agregado a la cola') + '</strong></p>';
                                html += '<p><a class="button button-primary" href="<?php echo admin_url('admin.php?page=asap-menu-ia&tab=ia_queue'); ?>">Ver cola</a></p></div>';
                                $result.html(html);
                                showToast('✓ Artículo agregado a la cola', 'success');
                            } else {
                                console.error('❌ Error en respuesta:', resp);
                                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error al agregar a la cola';
                                $result.html('<div class="notice notice-error"><p><strong>Error:</strong> ' + msg + '</p></div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error AJAX:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                error: error,
                                responseText: xhr.responseText
                            });
                            
                            var errorMsg = 'Error de conexión';
                            
                            // Intentar parsear JSON
                            if (xhr.responseText) {
                                try {
                                    var resp = JSON.parse(xhr.responseText);
                                    if (resp && resp.data && resp.data.message) {
                                        errorMsg = resp.data.message;
                                    }
                                } catch(e) {
                                    // Si no es JSON, mostrar el error 500
                                    if (xhr.status === 500) {
                                        errorMsg = 'Error del servidor (500). Revisa la consola PHP o los logs de WordPress para más detalles.';
                                        // Mostrar los primeros 500 caracteres del error HTML en consola
                                        if (xhr.responseText.length > 0) {
                                            console.error('📄 Respuesta del servidor (primeros 1000 caracteres):', xhr.responseText.substring(0, 1000));
                                        }
                                    } else if (xhr.status === 403) {
                                        errorMsg = 'Error de permisos (403). Verifica que estés logueado como administrador.';
                                    } else if (xhr.status === 0) {
                                        errorMsg = 'No se pudo conectar al servidor. Verifica tu conexión a internet.';
                                    } else {
                                        errorMsg = 'Error HTTP ' + xhr.status + ': ' + xhr.statusText;
                                    }
                                }
                            }
                            
                            $result.html('<div class="notice notice-error"><p><strong>Error:</strong> ' + errorMsg + '</p></div>');
                        },
                        complete: function() {
                            $btn.prop('disabled', false);
                            $spinner.css('visibility', 'hidden');
                        }
                    });
                } else {
                    // ✅ Generar inmediatamente CON PROGRESO EN TIEMPO REAL
                    $progress.show();
                    $progressBar.css('width', '5%');
                    
                    // Mostrar mensaje inicial
                    // ✅ Mensaje simple y claro
                    $progressText.html('⏳ Generando artículo... esto demora aproximadamente 60-90 segundos');
                    $progressBar.css('width', '10%'); // Barra inicial
                    
                    $.ajax({
                        url: ASAP_IA.ajax,
                        type: 'POST',
                        timeout: 300000,
                        data: {
                            action: 'asap_manual_rewrite',
                            nonce: $('#asap_manual_rewrite_nonce').val(),
                            h1: h1,
                            keyword: keyword,
                            target_len: target_len,
                            style: style,
                            lang: lang,
                            extra: extra,
                            outline: JSON.stringify(outlineData),
                            status: status,
                            post_type: post_type,
                            author: author,
                            intro_enable: intro_enable,
                            intro_custom: intro_custom,
                            faqs_enable: faqs_enable,
                            faqs_count: faqs_count,
                            conclusion_enable: conclusion_enable,
                            include_references: include_references,
                            custom_references: custom_references,
                            // ⭐ Categorías
                            'categories[]': categories
                        },
                        success: function(resp) {
                            $progressBar.css('width', '100%');
                            $progressText.html('✅ Artículo generado exitosamente');
                            
                            if (resp && resp.success && resp.data) {
                                var d = resp.data;
                                var html = '<div class="notice notice-success"><p><strong>' + d.message + '</strong></p>';
                                html += '<p>';
                                html += '<a class="button button-primary" href="' + (d.edit_url || '') + '">Editar post</a> ';
                                html += '<a class="button" href="' + (d.view_url || '') + '" target="_blank">Ver post</a>';
                                html += '</p>';
                                if (d.cost_usd) {
                                    html += '<p style="color:#666;font-size:12px;">Costo: $' + d.cost_usd.toFixed(4) + ' USD</p>';
                                }
                                html += '</div>';
                                $result.html(html);
                                showToast('✓ Artículo generado exitosamente', 'success');
                            } else {
                                var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error al generar artículo';
                                $result.html('<div class="notice notice-error"><p><strong>Error:</strong> ' + msg + '</p></div>');
                            }
                        },
                        error: function() {
                            $progressBar.css('width', '0%');
                            $progressText.html('❌ Error de conexión');
                            $result.html('<div class="notice notice-error"><p><strong>Error de conexión o timeout.</strong> Intenta agregar a la cola para artículos largos.</p></div>');
                        },
                        complete: function() {
                            $btn.prop('disabled', false);
                            $spinner.css('visibility', 'hidden');
                            setTimeout(function() {
                                $progress.fadeOut();
                            }, 2000);
                        }
                    });
                }
            });
            
            // ========================================================================
            // INICIALIZACIÓN
            // ========================================================================
            
            renderOutline();
            
            // Inicializar Select2 para idiomas
            if (typeof $.fn.select2 !== 'undefined') {
                $('.asap-lang-select2').select2({
                    placeholder: 'Selecciona un idioma',
                    allowClear: false,
                    width: '350px'
                });
            }
            
            // Guardar configuración por defecto con AJAX
            $('#btn_save_defaults').on('click', function(e) {
                e.preventDefault();
                
                var $btn = $(this);
                var $spinner = $('#spinner_save_defaults');
                var $result = $('#result_save_defaults');
                
                // Obtener valores actuales (TODOS los campos configurables)
                var data = {
                    action: 'asap_save_ia_defaults',
                    nonce: $('#asap_manual_rewrite_nonce').val(),
                    lang: $('#asap_ia_default_lang').val(),
                    style: $('#asap_ia_default_style').val(),
                    status: $('#asap_ia_default_status').val(),
                    post_type: $('#asap_ia_default_post_type').val(),
                    author: $('#asap_ia_default_author').val(),
                    // ⭐ NUEVOS CAMPOS (IDs correctos)
                    target_len: $('#asap_target_len').val(),
                    extra: $('#asap_manual_extra_prompt').val(),
                    intro_enable: '1', // Siempre habilitado
                    faqs_enable: $('#asap_faqs_enable').is(':checked') ? '1' : '0',
                    faqs_count: $('#asap_faqs_count').val(),
                    conclusion_enable: $('#asap_conclusion_enable').is(':checked') ? '1' : '0',
                    // ⭐ REFERENCIAS
                    include_references: $('#asap_include_references').is(':checked') ? '1' : '0',
                    custom_references: $('#asap_custom_references').val(),
                    // ⭐ CATEGORÍAS
                    categories: $('#asap_ia_default_categories').val() || []
                };
                
                // Mostrar spinner
                $btn.prop('disabled', true);
                $spinner.css('visibility', 'visible');
                $result.empty();
                
                $.ajax({
                    url: ASAP_IA.ajax,
                    type: 'POST',
                    data: data,
                    success: function(resp) {
                        if (resp && resp.success) {
                            $result.html('<div class="notice notice-success inline" style="margin:0;padding:8px 12px;"><p style="margin:0;">✓ Configuración guardada correctamente.</p></div>');
                            showToast('✓ Configuración guardada', 'success');
                            
                            // Ocultar mensaje después de 3 segundos
                            setTimeout(function() {
                                $result.fadeOut(300, function() {
                                    $(this).empty().show();
                                });
                            }, 3000);
                        } else {
                            var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Error al guardar';
                            $result.html('<div class="notice notice-error inline" style="margin:0;padding:8px 12px;"><p style="margin:0;">✗ ' + msg + '</p></div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $result.html('<div class="notice notice-error inline" style="margin:0;padding:8px 12px;"><p style="margin:0;">✗ Error de conexión</p></div>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.css('visibility', 'hidden');
                    }
                });
            });
            
            // Prevenir submit del formulario (ya no es necesario para guardar defaults)
            $('#asap-ia-settings').on('submit', function(e) {
                e.preventDefault();
                return false;
            });
            
            // ========================================================================
            // INICIALIZAR SELECT2 PARA CATEGORÍAS
            // ========================================================================
            
            $('#asap_ia_default_categories').select2({
                placeholder: 'Selecciona una o más categorías',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return 'No se encontraron categorías';
                    },
                    searching: function() {
                        return 'Buscando...';
                    }
                }
            });
        });
        </script>
        <?php
    }
}



