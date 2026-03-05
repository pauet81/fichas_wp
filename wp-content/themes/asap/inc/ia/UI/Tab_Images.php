<?php
/**
 * Tab: Configuración de Imágenes
 * 
 * Renderiza el formulario de configuración de generación automática de imágenes.
 * 
 * @package ASAP_Theme
 * @subpackage IA\UI
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_UI_Tab_Images {
    
    /**
     * Renderiza el tab completo
     * 
     * @param object $instance Instancia de ASAP_Manual_IA_Rewriter
     */
    public static function render($instance) {
        // Usar reflexión para obtener settings
        $reflection = new ReflectionMethod($instance, 'get_image_settings');
        $reflection->setAccessible(true);
        $S = $reflection->invoke($instance);
        
        // Usar reflexión para obtener catálogo de modelos Replicate
        $reflection2 = new ReflectionMethod($instance, 'get_replicate_model_catalog');
        $reflection2->setAccessible(true);
        $repM = $reflection2->invoke($instance);
        
        $pts   = get_post_types(['public'=>true], 'objects');
        ?>
        <section id="asap-options" class="asap-options section-content active">
            <style>
                .wrapper-asap-options select{width:300px}
                /* Prevenir scroll horizontal */
                .asap-options{overflow-x:hidden;max-width:100%}
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
                .asap-row{display:flex;align-items:center;gap:10px}
                .asap-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
                @media (max-width:1024px){.asap-grid{grid-template-columns:1fr}}
                .notice-inline{margin:10px 0}
                .model-selector-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:15px;margin-top:10px}
                @media(max-width:1200px){.model-selector-grid{grid-template-columns:repeat(2,1fr)}}
                @media(max-width:768px){.model-selector-grid{grid-template-columns:1fr}}
                .model-card{border:2px solid #ddd;border-radius:8px;padding:12px;cursor:pointer;transition:all 0.2s;background:#fff}
                .model-card:hover{border-color:#2271b1;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
                .model-card.selected{border-color:#2271b1;background:#f0f6fc}
                .model-card-image{width:100%;height:140px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#999;margin-bottom:8px;overflow:hidden}
                .model-card-image img{width:100%;height:100%;object-fit:cover}
                .model-card-title{font-weight:600;font-size:13px;margin-bottom:4px;color:#1d2327}
                .model-card-desc{font-size:11px;color:#646970;line-height:1.4}
            </style>

            <style>.small-muted{color:#6d6d6d;font-size:12px}</style>
            
            <form method="post" id="asap-images-settings" class="form-table">
                <?php wp_nonce_field('asap_images_settings_action','asap_images_settings_nonce'); ?>
                <h2>IA — Generación de imágenes
                    <span class="asap-tooltip">?<span class="tooltiptext">
                        Genera y asigna la imagen destacada automáticamente al publicar.
                    </span></span>
                </h2>



                <table class="form-table"><tbody>

            <div style="background:#e8f8f5;padding:12px;border-radius:3px; margin-top: 12px;">
                                <p style="margin:0;font-size:14px;color:#333;">
                                    Las imágenes se generarán automáticamente cuando un artículo pase al estado “Publicado”.
                                </p>
    
                            </div>
                                            
                    <tr>
                        <th><label for="asap_img_enable">Activar generación de imágenes</label><span class="asap-tooltip">?<span class="tooltiptext">Activa la generación automática de imágenes destacadas con IA cada vez que publiques o actualices un post. La imagen se genera y asigna automáticamente.</span></span></th>
                        <td><label><input type="checkbox" name="asap_img_enable" id="asap_img_enable" value="1" <?php checked('1',$S['enable']); ?>></label></td>
                    </tr>
                    <tr>
                        <th><label>Tipos de contenido</label><span class="asap-tooltip">?<span class="tooltiptext">Selecciona en qué tipos de contenido deseas que se generen imágenes automáticamente (Entradas, Páginas, Custom Post Types, etc.). Por defecto se aplica a "Entrada".</span></span></th>
                        <td>
                            <?php foreach ($pts as $name=>$obj): 
                                // Excluir 'attachment' (Medios) ya que no tiene sentido generar imagen destacada para imágenes
                                if ($name === 'attachment') continue;
                            ?>
                                <label style="display:block;margin-bottom:8px;">
                                    <input type="checkbox" name="asap_img_post_types[]" value="<?php echo esc_attr($name); ?>" <?php checked(in_array($name,$S['post_types'],true)); ?>>
                                    <?php echo esc_html($obj->labels->singular_name); ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="asap_img_only_if_empty">Sobrescritura de imágenes</label><span class="asap-tooltip">?<span class="tooltiptext">Si está marcado, solo generará imagen cuando el post NO tenga imagen destacada. Si está desmarcado, reemplazará la imagen destacada existente cada vez.</span></span></th>
                        <td><label><input type="checkbox" name="asap_img_only_if_empty" id="asap_img_only_if_empty" value="1" <?php checked('1',$S['only_if_empty']); ?>></label></td>
                    </tr>
                    

                    <tr><th><hr></th><td><hr></td></tr>

                    <tr>
                        <th><label for="asap_img_provider">Proveedor</label><span class="asap-tooltip">?<span class="tooltiptext">Selecciona qué servicio de IA usar para generar imágenes: DALL-E 3 (OpenAI), Gemini Nano Banana o modelos de Replicate.</span></span></th>
                        <td>
                            <select name="asap_img_provider" id="asap_img_provider" style="width:350px;">
                                <option value="openai"   <?php selected($S['provider'],'openai'); ?>>OpenAI (DALL-E 3)</option>
                                <option value="gemini"   <?php selected($S['provider'],'gemini'); ?>>Google Gemini (Nano Banana)</option>
                                <option value="replicate"<?php selected($S['provider'],'replicate'); ?>>Replicate</option>
                            </select>
                        </td>
                    </tr>

                    <!-- OPENAI -->
                    <tr class="row-openai">
                        <th><label for="asap_img_openai_size">Tamaño</label><span class="asap-tooltip">?<span class="tooltiptext">Configura las dimensiones de la imagen destacada con DALL-E 3. Por defecto usa 1792×1024 (horizontal ancho) ideal para imágenes destacadas de blog.</span></span></th>
                        <td>
                            <select name="asap_img_openai_size" id="asap_img_openai_size" style="width:250px;">
                                <option value="1792x1024"  <?php selected($S['openai_size'] ?? '1792x1024','1792x1024'); ?>>1792×1024 (Horizontal) - Recomendado</option>
                                <option value="1024x1024"  <?php selected($S['openai_size'] ?? '1792x1024','1024x1024'); ?>>1024×1024 (Cuadrado)</option>
                                <option value="1024x1792"  <?php selected($S['openai_size'] ?? '1792x1024','1024x1792'); ?>>1024×1792 (Vertical)</option>
                            </select>
                            <input type="hidden" name="asap_img_openai_quality" value="hd">
                            <input type="hidden" name="asap_img_openai_n" value="1">
                        </td>
                    </tr>

                    <!-- GEMINI -->
                    <tr class="row-gemini" style="display:none">
                        <th><label for="asap_img_gemini_aspect_ratio">Relación de aspecto</label><span class="asap-tooltip">?<span class="tooltiptext">Selecciona la relación de aspecto para las imágenes generadas con Gemini. Por defecto usa 16:9 (horizontal ancho) ideal para imágenes destacadas de blog. Costo: ~$0.04 por imagen.</span></span></th>
                        <td>
                            <select name="asap_img_gemini_aspect_ratio" id="asap_img_gemini_aspect_ratio" style="width:350px;">
                                <?php $gemini_ar = $S['gemini_aspect_ratio'] ?? '16:9'; ?>
                                <option value="16:9"  <?php selected($gemini_ar,'16:9'); ?>>16:9 (Horizontal - 1344x768) - Recomendado</option>
                                <option value="1:1"   <?php selected($gemini_ar,'1:1'); ?>>1:1 (Cuadrado - 1024x1024)</option>
                                <option value="2:3"   <?php selected($gemini_ar,'2:3'); ?>>2:3 (Vertical - 832x1248)</option>
                                <option value="3:2"   <?php selected($gemini_ar,'3:2'); ?>>3:2 (Horizontal - 1248x832)</option>
                                <option value="3:4"   <?php selected($gemini_ar,'3:4'); ?>>3:4 (Vertical - 864x1184)</option>
                                <option value="4:3"   <?php selected($gemini_ar,'4:3'); ?>>4:3 (Horizontal - 1184x864)</option>
                                <option value="4:5"   <?php selected($gemini_ar,'4:5'); ?>>4:5 (Vertical - 896x1152)</option>
                                <option value="5:4"   <?php selected($gemini_ar,'5:4'); ?>>5:4 (Horizontal - 1152x896)</option>
                                <option value="9:16"  <?php selected($gemini_ar,'9:16'); ?>>9:16 (Vertical - 768x1344)</option>
                                <option value="21:9"  <?php selected($gemini_ar,'21:9'); ?>>21:9 (Ultra wide - 1536x672)</option>
                            </select>
                        </td>
                    </tr>

                    <!-- REPLICATE - SIMPLIFICADO: Solo FLUX Schnell -->
                    <!-- 
                    ⚠️ CÓDIGO COMENTADO PARA FUTURO: Selector de múltiples modelos
                    <tr class="row-replicate" style="display:none">
                        <th><label>Modelo</label><span class="asap-tooltip">?<span class="tooltiptext">Selecciona el modelo de Replicate según el estilo visual que necesites: Flux para realismo, SDXL para versatilidad, etc.</span></span></th>
                        <td>
                            <input type="hidden" name="asap_img_replicate_model" id="asap_img_replicate_model" value="<?php echo esc_attr($S['replicate_model']); ?>">
                            <div class="model-selector-grid">
                                <?php foreach ($repM as $slug=>$meta): 
                                    $isSelected = $S['replicate_model'] === $slug;
                                ?>
                                    <div class="model-card <?php echo $isSelected ? 'selected' : ''; ?>" data-model="<?php echo esc_attr($slug); ?>">
                                        <div class="model-card-image">
                                            <span>Ejemplo: <?php echo esc_html($slug); ?></span>
                                        </div>
                                        <div class="model-card-title"><?php echo esc_html($meta['label']); ?></div>
                                        <div class="model-card-desc"><?php echo esc_html($meta['desc']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    -->
                    
                    <!-- REPLICATE: Siempre usa FLUX Schnell (hardcoded, sin mostrar selector) -->
                    <input type="hidden" name="asap_img_replicate_model" id="asap_img_replicate_model" value="flux-schnell">
                    
                    <tr class="row-replicate" style="display:none">
                        <th><label for="asap_img_replicate_aspect">Aspect Ratio</label><span class="asap-tooltip">?<span class="tooltiptext">Define la proporción de aspecto de la imagen destacada. Por defecto usa 16:9 (horizontal ancho) que es ideal para imágenes destacadas de blog.</span></span></th>
                        <td>
                            <select name="asap_img_replicate_aspect" id="asap_img_replicate_aspect" style="width:250px;">
                                <option value="16:9"  <?php selected($S['replicate_aspect'] ?? '16:9','16:9'); ?>>16:9 (Horizontal ancho) - Recomendado</option>
                                <option value="1:1"   <?php selected($S['replicate_aspect'] ?? '16:9','1:1'); ?>>1:1 (Cuadrado)</option>
                                <option value="9:16"  <?php selected($S['replicate_aspect'] ?? '16:9','9:16'); ?>>9:16 (Vertical alto)</option>
                                <option value="4:3"   <?php selected($S['replicate_aspect'] ?? '16:9','4:3'); ?>>4:3 (Horizontal clásico)</option>
                                <option value="3:4"   <?php selected($S['replicate_aspect'] ?? '16:9','3:4'); ?>>3:4 (Vertical clásico)</option>
                                <option value="21:9"  <?php selected($S['replicate_aspect'] ?? '16:9','21:9'); ?>>21:9 (Ultra-wide)</option>
                            </select>
                            <input type="hidden" name="asap_img_replicate_n" value="1">
                        </td>
                    </tr>
                    <tr class="row-replicate" style="display:none">
                        <th><label for="asap_img_negative_prompt">Negative prompt</label><span class="asap-tooltip">?<span class="tooltiptext">Elementos que NO quieres en la imagen. Ejemplo: "sin texto, sin watermark, sin manos deformes". Ayuda a refinar el resultado.</span></span></th>
                        <td><input type="text" name="asap_img_negative_prompt" id="asap_img_negative_prompt" style="width:520px" value="<?php echo esc_attr($S['negative_prompt']); ?>" placeholder="sin texto, sin watermark, sin manos deformes"></td>
                    </tr>

                    <tr><th><hr></th><td><hr></td></tr>

                    <tr>
                        <th><label for="asap_img_prompt_template">Prompt base</label><span class="asap-tooltip">?<span class="tooltiptext">Plantilla de instrucciones para generar la imagen. Usa placeholders: {title}, {excerpt}, {content}, {site_name}, {categories}, {tags}, {lang} para personalizar el prompt según cada artículo.</span></span></th>
                        <td>
                            <textarea name="asap_img_prompt_template" id="asap_img_prompt_template" rows="4" style="width:520px"><?php echo esc_textarea($S['prompt_template']); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="asap_img_alt_mode">Atributo alt</label><span class="asap-tooltip">?<span class="tooltiptext">Define cómo generar los textos ALT y Title de las imágenes. Puedes usar una plantilla estática o generar descripciones SEO automáticamente con IA. Costo adicional con IA: ~$0.00002 por imagen (muy económico).</span></span></th>
                        <td>
                            <select name="asap_img_alt_mode" id="asap_img_alt_mode" style="width:350px;">
                                <option value="template" <?php selected($S['alt_mode'] ?? 'template', 'template'); ?>>Usar plantilla estática</option>
                                <option value="ai" <?php selected($S['alt_mode'] ?? 'template', 'ai'); ?>>Generar con IA (descriptivo SEO)</option>
                            </select>
                            
                            <div id="alt_template_section" style="margin-top:10px;">
                                <input type="text" name="asap_img_alt_template" id="asap_img_alt_template" style="width:520px" value="<?php echo esc_attr($S['alt_template']); ?>" placeholder="{title}">
                                <p class="small-muted">Placeholders: <code>{title}</code>, <code>{site_name}</code>, <code>{lang}</code></p>
                            </div>
                            
                            <div id="alt_ai_section" style="margin-top:10px;display:none;">
                                <p class="description">
                                    La IA generará automáticamente un ALT descriptivo y un Title basados en el título del artículo, contexto del contenido, keyword principal y categorías.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr><th><hr></th><td><hr></td></tr>


                    <tr>
                        <th><label for="asap_img_content_enable">Generar imágenes dentro</label><span class="asap-tooltip">?<span class="tooltiptext">Genera e inserta automáticamente imágenes contextuales dentro del contenido del artículo para ilustrar cada sección de forma visual.</span></span></th>
                        <td><label><input type="checkbox" name="asap_img_content_enable" id="asap_img_content_enable" value="1" <?php checked('1',$S['content_enable'] ?? '0'); ?>></label></td>
                    </tr>
                    <tr id="content_image_settings" style="<?php echo ($S['content_enable'] ?? '0') === '1' ? '' : 'display:none'; ?>">
                        <th><label for="asap_img_content_quantity">Cantidad</label><span class="asap-tooltip">?<span class="tooltiptext">Define cuántas imágenes se insertarán dentro del contenido del artículo. Se distribuyen según la estrategia seleccionada.</span></span></th>
                        <td>
                            <select name="asap_img_content_quantity" id="asap_img_content_quantity" style="width:200px;">
                                <option value="1" <?php selected($S['content_quantity'] ?? 3, 1); ?>>1 imagen</option>
                                <option value="2" <?php selected($S['content_quantity'] ?? 3, 2); ?>>2 imágenes</option>
                                <option value="3" <?php selected($S['content_quantity'] ?? 3, 3); ?>>3 imágenes</option>
                                <option value="4" <?php selected($S['content_quantity'] ?? 3, 4); ?>>4 imágenes</option>
                                <option value="5" <?php selected($S['content_quantity'] ?? 3, 5); ?>>5 imágenes</option>
                                <option value="6" <?php selected($S['content_quantity'] ?? 3, 6); ?>>6 imágenes</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="content_strategy_row" style="<?php echo ($S['content_enable'] ?? '0') === '1' ? '' : 'display:none'; ?>">
                        <th><label for="asap_img_content_strategy">Estrategia de distribución</label><span class="asap-tooltip">?<span class="tooltiptext">Define cómo se distribuyen las imágenes: uniformemente, al inicio de cada H2, o cada N secciones. Afecta la ubicación de las imágenes en el artículo.</span></span></th>
                        <td>
                            <select name="asap_img_content_strategy" id="asap_img_content_strategy" style="width:350px;">
                                <option value="auto" <?php selected($S['content_strategy'] ?? 'auto', 'auto'); ?>>Automática (distribuir uniformemente)</option>
                                <option value="each_h2" <?php selected($S['content_strategy'] ?? 'auto', 'each_h2'); ?>>Al inicio de cada H2 (hasta la cantidad máx.)</option>
                                <option value="every_n" <?php selected($S['content_strategy'] ?? 'auto', 'every_n'); ?>>Cada N secciones</option>
                            </select>
                            
                            <div id="every_n_input" style="margin-top:10px;<?php echo ($S['content_strategy'] ?? 'auto') === 'every_n' ? '' : 'display:none;'; ?>">
                                Insertar imagen cada <input type="number" min="1" max="10" name="asap_img_content_every_n" id="asap_img_content_every_n" value="<?php echo esc_attr($S['content_every_n'] ?? 2); ?>" style="width:70px"> secciones
                            </div>
                        </td>
                    </tr>
                    <tr id="content_position_row" style="<?php echo ($S['content_enable'] ?? '0') === '1' ? '' : 'display:none'; ?>">
                        <th><label for="asap_img_content_position">Posición de inserción</label><span class="asap-tooltip">?<span class="tooltiptext">Define exactamente dónde se insertará la imagen dentro de cada sección: después del H2, al inicio de la sección, o después del primer párrafo.</span></span></th>
                        <td>
                            <select name="asap_img_content_position" id="asap_img_content_position" style="width:350px;">
                                <option value="after_h2" <?php selected($S['content_position'] ?? 'after_h2', 'after_h2'); ?>>Después del encabezado H2</option>
                                <option value="before_content" <?php selected($S['content_position'] ?? 'after_h2', 'before_content'); ?>>Al inicio de la sección</option>
                                <option value="after_first_p" <?php selected($S['content_position'] ?? 'after_h2', 'after_first_p'); ?>>Después del primer párrafo</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="content_style_row" style="<?php echo ($S['content_enable'] ?? '0') === '1' ? '' : 'display:none'; ?>">
                        <th><label for="asap_img_content_img_style">Estilo visual</label><span class="asap-tooltip">?<span class="tooltiptext">Controla cómo se muestra la imagen: centrada, ancho completo, o flotante a un lado. Afecta el diseño visual del artículo.</span></span></th>
                        <td>
                            <select name="asap_img_content_img_style" id="asap_img_content_img_style" style="width:350px;">
                                <option value="centered" <?php selected($S['content_img_style'] ?? 'centered', 'centered'); ?>>Centrado (80% ancho máx.)</option>
                                <option value="full_width" <?php selected($S['content_img_style'] ?? 'centered', 'full_width'); ?>>Ancho completo (100%)</option>
                                <option value="float_left" <?php selected($S['content_img_style'] ?? 'centered', 'float_left'); ?>>Flotante a la izquierda</option>
                                <option value="float_right" <?php selected($S['content_img_style'] ?? 'centered', 'float_right'); ?>>Flotante a la derecha</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="content_prompt_row" style="<?php echo ($S['content_enable'] ?? '0') === '1' ? '' : 'display:none'; ?>">
                        <th><label for="asap_img_content_prompt_template">Prompt base — Imágenes de contenido</label><span class="asap-tooltip">?<span class="tooltiptext">Personaliza las instrucciones para generar imágenes de contenido. Usa placeholders: {h1}, {h2}, {context}, {lang}. Si lo dejas vacío, se usa el prompt por defecto.</span></span></th>
                        <td>
                            <?php $default_content_prompt = "Genera una imagen profesional y fotorrealista para ilustrar un artículo.\n\nArtículo principal: \"{h1}\"\nSección específica que ilustra: \"{h2}\"\n\nContexto de la sección:\n{context}\n\nRequisitos:\n- Estilo fotográfico profesional, realista, limpio\n- Composición clara y atractiva\n- Sin texto, sin marcas de agua\n- Alta calidad visual\n- Idioma visual: {lang}"; ?>
                            <textarea name="asap_img_content_prompt_template" id="asap_img_content_prompt_template" rows="6" style="width:520px" placeholder="<?php echo esc_attr($default_content_prompt); ?>"><?php echo esc_textarea($S['content_prompt_template'] ?? ''); ?></textarea>
                            <p class="small-muted">
                                Variables: {h1}, {h2}, {context}, {lang}
                            </p>
                        </td>
                    </tr>

                    <tr><th><hr></th><td><hr></td></tr>

                    <!-- Costo estimado (AJAX) -->
                    <tr>
                        <th>Costo estimado<span class="asap-tooltip">?<span class="tooltiptext">Calcula el costo aproximado según el proveedor, modelo, tamaño y cantidad seleccionados arriba.</span></span></th>
                        <td>
                            <button type="button" id="btn_calc_cost" class="button">Calcular costo</button>
                            <span id="img_spinner_cost" class="spinner" style="float:none;visibility:hidden;margin-left:10px;"></span>
                            <div id="cost_result" style="margin-top:10px;"></div>
                        </td>
                    </tr>

                    <tr><th><p class="submit"><input type="submit" class="button button-primary" value="Guardar ajustes"></p></th><td></td></tr>
                </tbody></table>
            </form>
        </section>

        <script>
        jQuery(function($){
            var catalog = <?php echo wp_json_encode($repM); ?>;
            function toggleProviderUI(){
                var p = $('#asap_img_provider').val();
                $('.row-openai, .row-gemini, .row-replicate').hide();
                if(p==='openai'){
                    $('.row-openai').show();
                } else if(p==='gemini'){
                    $('.row-gemini').show();
                } else if(p==='replicate'){
                    $('.row-replicate').show();
                }
            }
            toggleProviderUI();
            $('#asap_img_provider').on('change', toggleProviderUI);
            
            // Manejo de selección de modelos con tarjetas visuales
            $('.model-card').on('click', function(){
                var $card = $(this);
                var model = $card.data('model');
                
                // Actualizar visual
                $('.model-card').removeClass('selected');
                $card.addClass('selected');
                
                // Actualizar campo hidden
                $('#asap_img_replicate_model').val(model);
            });

            function disableCost(dis){
                $('#btn_calc_cost').prop('disabled',dis);
                var $spinner = $('#img_spinner_cost');
                if(dis) {
                    $spinner.css('visibility', 'visible').addClass('is-active');
                } else {
                    $spinner.removeClass('is-active').css('visibility', 'hidden');
                }
            }

            $('#btn_calc_cost').on('click', function(){
                var provider = $('#asap_img_provider').val();
                var payload = { 
                    action:'asap_calc_image_cost', 
                    nonce: $('#asap_images_settings_nonce').val(), // ← FIX: nonce correcto
                    provider: provider 
                };

                if(provider==='openai'){
                    payload.size    = $('#asap_img_openai_size').val();
                    payload.quality = $('#asap_img_openai_quality').val();
                    payload.qty     = parseInt($('#asap_img_openai_n').val()||'1',10);
                } else {
                    payload.model  = $('#asap_img_replicate_model').val();
                    payload.aspect = $('#asap_img_replicate_aspect').val();
                    payload.qty    = parseInt($('#asap_img_replicate_n').val()||'1',10);
                }

                disableCost(true);
                $('#cost_result').html('');
                $.post(ASAP_IA.ajax, payload, function(resp){
                    if(resp && resp.success){
                        var html = '<div style="padding:10px;background:#f0f6fc;border-left:4px solid #2271b1;border-radius:4px;">';
                        html += '<strong style="color:#2271b1;font-size:16px;">≈ $' + resp.data.estimated_usd + ' USD</strong>';
                        if(resp.data.note) html += '<br><span style="color:#646970;font-size:13px;">' + resp.data.note + '</span>';
                        html += '<br><span style="color:#646970;font-size:12px;margin-top:4px;display:inline-block;">Cantidad: ' + payload.qty + ' imagen(es)</span>';
                        html += '</div>';
                        $('#cost_result').html(html);
                    } else {
                        var msg = resp && resp.data && resp.data.message ? resp.data.message : 'Error al calcular.';
                        $('#cost_result').html('<div style="color:#dc3232;">' + msg + '</div>');
                    }
                }).always(function(){ disableCost(false); });
            });

            // Toggle ALT mode
            function toggleAltMode(){
                var mode = $('#asap_img_alt_mode').val();
                if(mode === 'ai'){
                    $('#alt_template_section').hide();
                    $('#alt_ai_section').show();
                } else {
                    $('#alt_template_section').show();
                    $('#alt_ai_section').hide();
                }
            }
            toggleAltMode();
            $('#asap_img_alt_mode').on('change', toggleAltMode);

            // ⭐ Toggle imágenes de contenido
            function toggleContentImages(){
                var enabled = $('#asap_img_content_enable').is(':checked');
                if(enabled){
                    $('#content_image_settings, #content_strategy_row, #content_position_row, #content_style_row, #content_prompt_row').show();
                } else {
                    $('#content_image_settings, #content_strategy_row, #content_position_row, #content_style_row, #content_prompt_row').hide();
                }
            }
            toggleContentImages();
            $('#asap_img_content_enable').on('change', toggleContentImages);

            // ⭐ Toggle "cada N" input
            function toggleEveryNInput(){
                var strategy = $('#asap_img_content_strategy').val();
                if(strategy === 'every_n'){
                    $('#every_n_input').show();
                } else {
                    $('#every_n_input').hide();
                }
            }
            toggleEveryNInput();
            $('#asap_img_content_strategy').on('change', toggleEveryNInput);

            // Inicializar switchify para checkboxes
            if(typeof jQuery.fn.switchify !== 'undefined'){
                $('#asap_img_enable, #asap_img_only_if_empty, #asap_img_content_enable, input[name="asap_img_post_types[]"]').switchify();
            }
        });
        </script>
        <?php
    }
}

