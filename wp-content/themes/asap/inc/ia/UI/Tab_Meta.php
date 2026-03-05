<?php
/**
 * Tab: Configuración de Metaetiquetas
 * 
 * Render

iza el formulario de configuración de metaetiquetas automáticas.
 * 
 * @package ASAP_Theme
 * @subpackage IA\UI
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_UI_Tab_Meta {
    
    /**
     * Renderiza el tab completo
     * 
     * @param object $instance Instancia de ASAP_Manual_IA_Rewriter
     */
    public static function render($instance) {
        $enabled      = get_option('asap_meta_enable', '0');
        $target       = get_option('asap_meta_target', 'auto');
        $when         = get_option('asap_meta_when', 'publish');
        $only_empty   = get_option('asap_meta_only_if_empty', '1');
        $lang_mode    = get_option('asap_meta_lang', 'es');
        $max_title    = get_option('asap_meta_title_len', 60);
        $max_desc     = get_option('asap_meta_desc_len', 155);
        $p_title      = get_option('asap_meta_title_prompt', '');
        $p_desc       = get_option('asap_meta_desc_prompt', '');
        
        // Usar reflexión para llamar al método detect_seo_plugins
        $reflection = new ReflectionMethod($instance, 'detect_seo_plugins');
        $reflection->setAccessible(true);
        $detected = $reflection->invoke($instance);
        
        // Detectar plugins activos
        $det_text = [];
        $detected_plugin = 'none';
        $detected_name = 'Ninguno';
        $has_plugin = false;
        
        foreach ($detected as $slug => $data) {
            if ($data['active']) {
                $det_text[] = $data['name'];
                $has_plugin = true;
                // El primero activo será el detectado (por prioridad)
                if ($detected_plugin === 'none') {
                    $detected_plugin = $slug;
                    $detected_name = $data['name'];
                }
            }
        }
        
        if (empty($det_text)) {
            $det_text[] = 'Ninguno detectado';
        }
        
        // Prompts por defecto (para mostrar como placeholder si está vacío)
        $default_title_prompt = "Escribe un meta título conciso y atractivo en {lang} para {site_name}. Máx {max_title} caracteres. Devuelve SOLO el título.";
        $default_desc_prompt = "Escribe una meta descripción persuasiva en {lang}. Máx {max_description} caracteres. Devuelve SOLO la descripción.";
        
        // Incluir Select2 CDN si no está disponible
        wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '4.1.0');
        wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], '4.1.0', true);
        ?>
        <section id="asap-options" class="asap-options section-content active">
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
                .small-muted{color:#6d6d6d;font-size:12px}
            </style>

            <form method="post" id="asap-meta-settings" class="form-table">
                <?php 
                wp_nonce_field('asap_meta_action','asap_meta_nonce'); 
                wp_nonce_field('asap_calc_cost_nonce','asap_calc_cost_nonce_meta');
                ?>
                <h2>IA — Generación de metaetiquetas
                    <span class="asap-tooltip">?<span class="tooltiptext">Plugins detectados: <?php echo esc_html(implode(', ', $det_text)); ?></span></span>
                </h2>

                <table class="form-table"><tbody>
            <div style="background:#e8f8f5;padding:12px;border-radius:3px; margin-top: 12px;">
                                <p style="margin:0;font-size:14px;color:#333;">
                                    Las metaetiquetas se generarán automáticamente cuando un artículo pase al estado “Publicado”.
                                </p>
    
                            </div>
                    <tr><th><label for="asap_meta_enable">Activar generación de metaetiquetas</label><span class="asap-tooltip">?<span class="tooltiptext">Activa la generación automática de meta títulos y descripciones con IA para todos los posts publicados. Mejora el SEO automáticamente.</span></span></th><td><label><input type="checkbox" name="asap_meta_enable" id="asap_meta_enable" value="1" <?php checked('1',$enabled); ?>></label></td></tr>
                    <tr><th><label for="asap_meta_target">Plugin SEO detectado</label><span class="asap-tooltip">?<span class="tooltiptext">Este campo detecta automáticamente el plugin SEO instalado. Plugins compatibles: Yoast SEO, Rank Math, All in One SEO, SEOPress y The SEO Framework. Las metaetiquetas se guardarán en el plugin detectado.</span></span></th><td>
                        <select name="asap_meta_target" id="asap_meta_target" disabled style="width:350px;opacity:0.7;background:#f5f5f5;">
                            <option value="<?php echo esc_attr($detected_plugin); ?>" selected><?php echo esc_html($detected_name); ?></option>
                        </select>
                        <input type="hidden" name="asap_meta_target" value="<?php echo esc_attr($detected_plugin); ?>" />
                        <?php if (!$has_plugin): ?>
                            <p class="description">
                                ⚠️ No se detectó ningún plugin SEO compatible. Instala uno de estos plugins para usar esta funcionalidad:<br>
                                • <a href="https://wordpress.org/plugins/wordpress-seo/" target="_blank">Yoast SEO</a> (5M+ instalaciones)<br>
                                • <a href="https://wordpress.org/plugins/seo-by-rank-math/" target="_blank">Rank Math</a> (2M+ instalaciones)<br>
                                • <a href="https://wordpress.org/plugins/all-in-one-seo-pack/" target="_blank">All in One SEO</a> (3M+ instalaciones)<br>
                                • <a href="https://wordpress.org/plugins/wp-seopress/" target="_blank">SEOPress</a> (300K+ instalaciones)<br>
                                • <a href="https://wordpress.org/plugins/autodescription/" target="_blank">The SEO Framework</a> (300K+ instalaciones)
                            </p>
                        <?php else: ?>
                            <p class="description">
                                ✅ Plugin detectado: <strong><?php echo esc_html($detected_name); ?></strong>. Las metas se guardarán automáticamente.
                                <?php if (count($det_text) > 1): ?>
                                    <br>📋 Plugins SEO instalados: <?php echo implode(', ', $det_text); ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </td></tr>

                    <tr><th><label for="asap_meta_only_if_empty">Sobrescritura</label><span class="asap-tooltip">?<span class="tooltiptext">Si está marcado, solo generará metas cuando el post NO tenga metas previas. Si está desmarcado, reemplazará las metas existentes.</span></span></th><td><label><input type="checkbox" name="asap_meta_only_if_empty" id="asap_meta_only_if_empty" value="1" <?php checked('1',$only_empty); ?>> No sobrescribir si ya existen</label></td></tr>
                    <tr><th><label for="asap_meta_lang">Idioma</label><span class="asap-tooltip">?<span class="tooltiptext">Idioma en el que se generarán los meta títulos y descripciones.</span></span></th><td>
                        <select name="asap_meta_lang" id="asap_meta_lang" class="asap-select-standard">
                            <!-- Idiomas principales -->
                            <option value="es" <?php selected($lang_mode,'es'); ?>>Español</option>
                            <option value="en" <?php selected($lang_mode,'en'); ?>>Inglés</option>
                            <option value="pt" <?php selected($lang_mode,'pt'); ?>>Portugués</option>
                            <option value="fr" <?php selected($lang_mode,'fr'); ?>>Francés</option>
                            <option value="de" <?php selected($lang_mode,'de'); ?>>Alemán</option>
                            <option value="it" <?php selected($lang_mode,'it'); ?>>Italiano</option>
                            <option value="ru" <?php selected($lang_mode,'ru'); ?>>Ruso</option>
                            <option value="ja" <?php selected($lang_mode,'ja'); ?>>Japonés</option>
                            <option value="zh" <?php selected($lang_mode,'zh'); ?>>Chino</option>
                            <option value="ko" <?php selected($lang_mode,'ko'); ?>>Coreano</option>
                            <option value="ar" <?php selected($lang_mode,'ar'); ?>>Árabe</option>
                            <option value="hi" <?php selected($lang_mode,'hi'); ?>>Hindi</option>
                            <option value="bn" <?php selected($lang_mode,'bn'); ?>>Bengali</option>
                            <option value="tr" <?php selected($lang_mode,'tr'); ?>>Turco</option>
                            <option value="id" <?php selected($lang_mode,'id'); ?>>Indonesio</option>
                            <option value="ms" <?php selected($lang_mode,'ms'); ?>>Malayo</option>
                            <option value="vi" <?php selected($lang_mode,'vi'); ?>>Vietnamita</option>
                            <option value="th" <?php selected($lang_mode,'th'); ?>>Tailandés</option>
                            <option value="tl" <?php selected($lang_mode,'tl'); ?>>Filipino</option>
                            <option value="fa" <?php selected($lang_mode,'fa'); ?>>Persa</option>
                            <option value="ur" <?php selected($lang_mode,'ur'); ?>>Urdu</option>
                            
                            <!-- Europa Occidental -->
                            <option value="nl" <?php selected($lang_mode,'nl'); ?>>Holandés</option>
                            <option value="sv" <?php selected($lang_mode,'sv'); ?>>Sueco</option>
                            <option value="no" <?php selected($lang_mode,'no'); ?>>Noruego</option>
                            <option value="da" <?php selected($lang_mode,'da'); ?>>Danés</option>
                            <option value="fi" <?php selected($lang_mode,'fi'); ?>>Finlandés</option>
                            <option value="is" <?php selected($lang_mode,'is'); ?>>Islandés</option>
                            
                            <!-- Europa del Este -->
                            <option value="pl" <?php selected($lang_mode,'pl'); ?>>Polaco</option>
                            <option value="uk" <?php selected($lang_mode,'uk'); ?>>Ucraniano</option>
                            <option value="cs" <?php selected($lang_mode,'cs'); ?>>Checo</option>
                            <option value="sk" <?php selected($lang_mode,'sk'); ?>>Eslovaco</option>
                            <option value="hu" <?php selected($lang_mode,'hu'); ?>>Húngaro</option>
                            <option value="ro" <?php selected($lang_mode,'ro'); ?>>Rumano</option>
                            <option value="bg" <?php selected($lang_mode,'bg'); ?>>Búlgaro</option>
                            <option value="hr" <?php selected($lang_mode,'hr'); ?>>Croata</option>
                            <option value="sr" <?php selected($lang_mode,'sr'); ?>>Serbio</option>
                            <option value="sl" <?php selected($lang_mode,'sl'); ?>>Esloveno</option>
                            <option value="mk" <?php selected($lang_mode,'mk'); ?>>Macedonio</option>
                            <option value="sq" <?php selected($lang_mode,'sq'); ?>>Albanés</option>
                            <option value="et" <?php selected($lang_mode,'et'); ?>>Estonio</option>
                            <option value="lv" <?php selected($lang_mode,'lv'); ?>>Letón</option>
                            <option value="lt" <?php selected($lang_mode,'lt'); ?>>Lituano</option>
                            
                            <!-- Lenguas regionales Europa -->
                            <option value="ca" <?php selected($lang_mode,'ca'); ?>>Catalán</option>
                            <option value="eu" <?php selected($lang_mode,'eu'); ?>>Vasco</option>
                            <option value="gl" <?php selected($lang_mode,'gl'); ?>>Gallego</option>
                            <option value="cy" <?php selected($lang_mode,'cy'); ?>>Galés</option>
                            <option value="ga" <?php selected($lang_mode,'ga'); ?>>Irlandés</option>
                            <option value="mt" <?php selected($lang_mode,'mt'); ?>>Maltés</option>
                            
                            <!-- Mediterráneo y Medio Oriente -->
                            <option value="el" <?php selected($lang_mode,'el'); ?>>Griego</option>
                            <option value="he" <?php selected($lang_mode,'he'); ?>>Hebreo</option>
                            
                            <!-- Subcontinente Indio -->
                            <option value="ta" <?php selected($lang_mode,'ta'); ?>>Tamil</option>
                            <option value="te" <?php selected($lang_mode,'te'); ?>>Telugu</option>
                            <option value="mr" <?php selected($lang_mode,'mr'); ?>>Marathi</option>
                            <option value="gu" <?php selected($lang_mode,'gu'); ?>>Gujarati</option>
                            <option value="kn" <?php selected($lang_mode,'kn'); ?>>Kannada</option>
                            <option value="ml" <?php selected($lang_mode,'ml'); ?>>Malayalam</option>
                            <option value="pa" <?php selected($lang_mode,'pa'); ?>>Punjabi</option>
                            <option value="si" <?php selected($lang_mode,'si'); ?>>Sinhala</option>
                            <option value="ne" <?php selected($lang_mode,'ne'); ?>>Nepalí</option>
                            
                            <!-- Sudeste Asiático -->
                            <option value="my" <?php selected($lang_mode,'my'); ?>>Birmano</option>
                            <option value="km" <?php selected($lang_mode,'km'); ?>>Camboyano</option>
                            <option value="lo" <?php selected($lang_mode,'lo'); ?>>Laosiano</option>
                            
                            <!-- Asia Central y del Este -->
                            <option value="mn" <?php selected($lang_mode,'mn'); ?>>Mongol</option>
                            <option value="kk" <?php selected($lang_mode,'kk'); ?>>Kazajo</option>
                            <option value="uz" <?php selected($lang_mode,'uz'); ?>>Uzbeko</option>
                            
                            <!-- África -->
                            <option value="sw" <?php selected($lang_mode,'sw'); ?>>Swahili</option>
                            <option value="af" <?php selected($lang_mode,'af'); ?>>Afrikaans</option>
                            <option value="am" <?php selected($lang_mode,'am'); ?>>Amhárico</option>
                            <option value="ha" <?php selected($lang_mode,'ha'); ?>>Hausa</option>
                            <option value="yo" <?php selected($lang_mode,'yo'); ?>>Yoruba</option>
                            <option value="zu" <?php selected($lang_mode,'zu'); ?>>Zulú</option>
                        </select>
                    </td></tr>
                    <tr><th><label for="asap_meta_title_len">Longitud máx. título</label><span class="asap-tooltip">?<span class="tooltiptext">Longitud máxima en caracteres del meta título. Google recomienda 50-60 caracteres. La IA respetará este límite.</span></span></th><td><input type="number" min="20" max="80" step="1" name="asap_meta_title_len" id="asap_meta_title_len" value="<?php echo esc_attr($max_title); ?>"></td></tr>
                    <tr><th><label for="asap_meta_desc_len">Longitud máx. descripción</label><span class="asap-tooltip">?<span class="tooltiptext">Longitud máxima en caracteres de la meta descripción. Google recomienda 150-160 caracteres. La IA respetará este límite.</span></span></th><td><input type="number" min="60" max="200" step="1" name="asap_meta_desc_len" id="asap_meta_desc_len" value="<?php echo esc_attr($max_desc); ?>"></td></tr>
                    <tr><th><label for="asap_meta_title_prompt">Prompt base — Título</label><span class="asap-tooltip">?<span class="tooltiptext">Plantilla de instrucciones para generar el meta título. Usa placeholders como {title}, {categories}, {site_name} para personalizar. Si lo dejas vacío, se usará el prompt por defecto.</span></span></th>
                        <td>
                            <textarea name="asap_meta_title_prompt" id="asap_meta_title_prompt" rows="3" style="width:520px;" placeholder="<?php echo esc_attr($default_title_prompt); ?>"><?php echo esc_textarea($p_title); ?></textarea>
                            <p class="small-muted">
                                Variables: {title}, {excerpt}, {content}, {site_name}, {categories}, {tags}, {lang}, {max_title}
                            </p>
                        </td></tr>
                    <tr><th><label for="asap_meta_desc_prompt">Prompt base — Descripción</label><span class="asap-tooltip">?<span class="tooltiptext">Plantilla de instrucciones para generar la meta descripción. Usa placeholders como {excerpt}, {content}, {tags} para personalizar el resultado. Si lo dejas vacío, se usará el prompt por defecto.</span></span></th>
                        <td>
                            <textarea name="asap_meta_desc_prompt" id="asap_meta_desc_prompt" rows="4" style="width:520px;" placeholder="<?php echo esc_attr($default_desc_prompt); ?>"><?php echo esc_textarea($p_desc); ?></textarea>
                            <p class="small-muted">
                                Variables: {title}, {excerpt}, {content}, {site_name}, {categories}, {tags}, {lang}, {max_description}
                            </p>
                        </td></tr>
                    <tr>
                        <th>Costo estimado<span class="asap-tooltip">?<span class="tooltiptext">Calcula el costo aproximado por artículo para generar título y descripción. Generalmente es muy económico: menos de $0.0001 por post.</span></span></th>
                        <td>
                            <button type="button" id="btn_calc_meta_cost" class="button">Calcular costo</button>
                            <span id="meta_cost_result" style="color:#2271b1;font-weight:600;margin-left:10px;"></span>
                        </td>
                    </tr>
                    <tr><th><p class="submit"><input type="submit" class="button button-primary" value="Guardar ajustes" /></p></th><td></td></tr>
                </tbody></table>
            </form>
            
            <script>
            jQuery(function($){
                // Inicializar Select2 para idiomas
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#asap_meta_lang').select2({
                        width: '300px',
                        placeholder: 'Selecciona un idioma',
                        allowClear: false
                    });
                }
                
                // Calcular costo de metas
                $('#btn_calc_meta_cost').on('click', function(){
                    $.post(ASAP_IA.ajax, {
                        action: 'asap_calc_meta_cost',
                        nonce: $('#asap_calc_cost_nonce_meta').val()
                    }, function(resp){
                        if(resp && resp.success){
                            var msg = '≈ $' + resp.data.cost_usd + ' USD';
                            if(resp.data.note) msg += ' - ' + resp.data.note;
                            $('#meta_cost_result').text(msg);
                        } else {
                            $('#meta_cost_result').text('Error al calcular');
                        }
                    });
                });

                // Inicializar switchify para checkboxes
                if(typeof jQuery.fn.switchify !== 'undefined'){
                    $('#asap_meta_enable, #asap_meta_only_if_empty').switchify();
                }
            });
            </script>
        </section>
        <?php
    }
}


