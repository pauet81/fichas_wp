<?php
/**
 * Tab: Configuración de API Keys
 * 
 * Renderiza el formulario de configuración de credenciales de APIs.
 * 
 * @package ASAP_Theme
 * @subpackage IA\UI
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_UI_Tab_Config {
    
    /**
     * Renderiza el tab completo
     */
    public static function render() {
        $openai_key = get_option('asap_ia_openai_api_key', '');
        $gemini_key = get_option('asap_ia_gemini_api_key', '');
        $valueserp_key = get_option('asap_ia_valueserp_api_key', '');
        $replicate_key = get_option('asap_ia_replicate_api_token', '');
        
        // Enmascarar keys para seguridad
        $masked_openai = !empty($openai_key) ? '********' : '';
        $masked_gemini = !empty($gemini_key) ? '********' : '';
        $masked_valueserp = !empty($valueserp_key) ? '********' : '';
        $masked_repl = !empty($replicate_key) ? '********' : '';
        
        ?>
        <section id="asap-options" class="asap-options section-content active">
            <?php self::render_styles(); ?>
            
            <form method="post" id="asap-ia-config" class="form-table">
                <?php wp_nonce_field('asap_ia_config_action','asap_ia_config_nonce'); ?>
                <h2>IA — Configuración
                    <span class="asap-tooltip">?<span class="tooltiptext">Configura las credenciales de los servicios de IA que utilizarás.</span></span>
                </h2>
                
                <table class="form-table" id="asap-fieldset-one"><tbody>
                    <tr>
                        <th scope="row"><label for="asap_ia_provider">Proveedor de IA para textos</label><span class="asap-tooltip">?<span class="tooltiptext">Selecciona qué servicio usar para generar textos (artículos, metas). Para imágenes, configúralo en la pestaña "Imágenes".</span></span></th>
                        <td>
                            <?php $provider = get_option('asap_ia_provider', 'openai'); ?>
                            <select name="asap_ia_provider" id="asap_ia_provider_config" style="width:350px;">
                                <option value="openai" <?php selected($provider, 'openai'); ?>>OpenAI (GPT)</option>
                                <option value="gemini" <?php selected($provider, 'gemini'); ?>>Google Gemini</option>
                            </select>

                        </td>
                    </tr>
                    
                    
                    <tr class="provider-settings openai-settings">
                        <th scope="row"><label for="asap_ia_openai_model">Modelo OpenAI</label><span class="asap-tooltip">?<span class="tooltiptext">Modelo recomendado: GPT-4o mini (excelente balance costo/calidad). GPT-4o es el más potente y equilibrado. GPT-4.1 Mini y Nano son ultrarrápidos. GPT-4 Turbo es el clásico potente.</span></span></th>
                        <td>
                            <?php $openai_model = get_option('asap_ia_openai_model', 'gpt-4o-mini'); ?>
                            <select name="asap_ia_openai_model" id="asap_ia_openai_model" style="width:350px;">
                                <option value="gpt-4o-mini" <?php selected($openai_model, 'gpt-4o-mini'); ?>>GPT-4o mini</option>
                                <option value="gpt-4o" <?php selected($openai_model, 'gpt-4o'); ?>>GPT-4o</option>
                                <option value="gpt-4-turbo" <?php selected($openai_model, 'gpt-4-turbo'); ?>>GPT-4 Turbo</option>
                                <option value="gpt-4.1-mini" <?php selected($openai_model, 'gpt-4.1-mini'); ?>>GPT-4.1 Mini</option>
                                <option value="gpt-4.1-nano" <?php selected($openai_model, 'gpt-4.1-nano'); ?>>GPT-4.1 Nano</option>
                                <option value="o1-mini" <?php selected($openai_model, 'o1-mini'); ?>>o1-mini (Razonamiento)</option>
                                <option value="o1" <?php selected($openai_model, 'o1'); ?>>o1 (Razonamiento Avanzado)</option>
                            </select>
                            <span id="openai_token_cost" style="margin-left:10px;color:#646970;font-size:12px;">~$0.003 cada 1000 palabras</span>
                        </td>
                    </tr>
                    

                    <tr class="provider-settings gemini-settings" style="display:none;">
                        <th scope="row"><label for="asap_ia_gemini_model">Modelo Gemini</label><span class="asap-tooltip">?<span class="tooltiptext">Modelo recomendado: Gemini 2.5 Flash Lite (ultrarrápido y económico). Gemini 2.5 Flash es más equilibrado. Pro es el más potente para tareas complejas. Los modelos 2.0 son experimentales.</span></span></th>
                        <td>
                            <?php $gemini_model = get_option('asap_ia_gemini_model', 'gemini-2.5-flash-lite'); ?>
                            <select name="asap_ia_gemini_model" id="asap_ia_gemini_model" style="width:350px;">
                                <option value="gemini-2.5-flash" <?php selected($gemini_model, 'gemini-2.5-flash'); ?>>Gemini 2.5 Flash</option>
                                <option value="gemini-2.5-flash-lite" <?php selected($gemini_model, 'gemini-2.5-flash-lite'); ?>>Gemini 2.5 Flash Lite</option>
                                <option value="gemini-2.5-pro" <?php selected($gemini_model, 'gemini-2.5-pro'); ?>>Gemini 2.5 Pro</option>
                                <option value="gemini-2.0-flash-exp" <?php selected($gemini_model, 'gemini-2.0-flash-exp'); ?>>Gemini 2.0 Flash Experimental</option>
                            </select>
                            <span id="gemini_token_cost" style="margin-left:10px;color:#646970;font-size:12px;">~$0.0005 cada 1000 palabras</span>
                        </td>
                    </tr>
                    

                    <!-- OpenAI Settings -->
                    <tr>
                        <th scope="row"><label for="asap_ia_openai_api_key">OpenAI API Key</label><span class="asap-tooltip">?<span class="tooltiptext">Para textos con GPT e imágenes con DALL-E. Obtén tu clave en platform.openai.com/api-keys</span></span></th>
                        <td>
                            <input type="password" style="width:350px" name="asap_ia_openai_api_key" id="asap_ia_openai_api_key_config" value="<?php echo esc_attr($masked_openai); ?>" autocomplete="off" placeholder="sk-..." />
                            <button type="button" id="asap_test_api_key_config" class="button" style="margin-left:10px;">Probar conexión</button>
                            <span id="asap_api_status_config" style="margin-left:10px;"></span>
                            <span class="spinner" id="asap_api_spinner_config" style="float:none;margin:0;"></span>
                            <p class="description">
                                <?php if ($openai_key): ?>
                                    <span style="color:#46b450;">✓ API Key configurada</span>
                                <?php else: ?>
                                    <a href="https://platform.openai.com/api-keys" target="_blank">Obtener API Key →</a>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Google Gemini Settings -->
                    <tr>
                        <th scope="row"><label for="asap_ia_gemini_api_key">Google Gemini API Key</label><span class="asap-tooltip">?<span class="tooltiptext">Para textos con Gemini 2.5 e imágenes con Nano Banana. Obtén tu clave en aistudio.google.com/app/apikey</span></span></th>
                        <td>
                            <input type="password" style="width:350px" name="asap_ia_gemini_api_key" id="asap_ia_gemini_api_key_config" value="<?php echo esc_attr($masked_gemini); ?>" autocomplete="off" placeholder="AIza..." />
                            <button type="button" id="asap_test_gemini_key_config" class="button" style="margin-left:10px;">Probar conexión</button>
                            <span id="asap_gemini_status_config" style="margin-left:10px;"></span>
                            <span class="spinner" id="asap_gemini_spinner_config" style="float:none;margin:0;"></span>
                            <p class="description">
                                <?php if ($gemini_key): ?>
                                    <span style="color:#46b450;">✓ API Key configurada</span>
                                <?php else: ?>
                                    <a href="https://aistudio.google.com/app/apikey" target="_blank">Obtener API Key →</a>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="asap_ia_replicate_api_token">Replicate API Token</label><span class="asap-tooltip">?<span class="tooltiptext">Token opcional para usar modelos de generación de imágenes de Replicate como Flux, Stable Diffusion y otros. Solo necesario si seleccionas Replicate como proveedor de imágenes.</span></span></th>
                        <td>
                            <input type="password" style="width:350px" name="asap_ia_replicate_api_token" id="asap_ia_replicate_api_token" value="<?php echo esc_attr($masked_repl); ?>" autocomplete="off" placeholder="r8_..." />
                            <button type="button" id="asap_test_replicate_token" class="button" style="margin-left:10px;">Probar conexión</button>
                            <span id="asap_replicate_status" style="margin-left:10px;"></span>
                            <span class="spinner" id="asap_replicate_spinner" style="float:none;margin:0;"></span>
                            <p class="description">
                                <?php if ($replicate_key): ?>
                                    <span style="color:#46b450;">✓ Token configurado</span>
                                <?php else: ?>
                                    <a href="https://replicate.com/account/api-tokens" target="_blank">Obtener Token →</a>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="asap_ia_valueserp_api_key">ValueSERP API Key</label><span class="asap-tooltip">?<span class="tooltiptext">API Key opcional para obtener palabras clave relacionadas de Google (PAA y Related Searches) y análisis SERP. Solo necesario si deseas usar la funcionalidad de investigación de palabras clave.</span></span></th>
                        <td>
                            <input type="password" style="width:350px" name="asap_ia_valueserp_api_key" id="asap_ia_valueserp_api_key" value="<?php echo esc_attr($masked_valueserp); ?>" autocomplete="off" placeholder="xxxxxx..." />
                            <button type="button" id="asap_test_valueserp_key" class="button" style="margin-left:10px;">Probar conexión</button>
                            <span id="asap_valueserp_status" style="margin-left:10px;"></span>
                            <span class="spinner" id="asap_valueserp_spinner" style="float:none;margin:0;"></span>
                            <p class="description">
                                <?php if ($valueserp_key): ?>
                                    <span style="color:#46b450;">✓ API Key configurada</span>
                                <?php else: ?>
                                    <a href="https://www.valueserp.com/" target="_blank">Obtener API Key →</a>
                                <?php endif; ?>
                                <?php if ($valueserp_key): ?> | <?php endif; ?>Si ya guardaste, verás <code>********</code>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><p class="submit"><input type="submit" class="button button-primary" value="Guardar configuración" /></p></th>
                        <td></td>
                    </tr>
                </tbody></table>
            </form>
            
            <!-- 📊 Panel de Logs de Generación -->
            <div id="generation_logs_panel" style="margin-top:40px;">
                <h2 style="margin-bottom:15px;display:flex;align-items:center;">
                    Registro de depuración
                    <span class="asap-tooltip">?<span class="tooltiptext">Historial completo de todas las generaciones de artículos con IA. Los logs se guardan durante 30 días.</span></span>
                </h2>
                
                <div style="background:#fff;border:1px solid #ccd0d4;border-radius:4px;padding:20px;box-shadow:0 1px 1px rgba(0,0,0,.04);">
                    <div style="margin-bottom:15px;display:flex;gap:10px;align-items:center;">
                        <input type="number" id="log_limit" value="100" min="10" max="1000" step="10" style="width:80px;" placeholder="100" />
                        <button type="button" id="btn_load_logs" class="button button-primary">🔄 Cargar últimos logs</button>
                        <button type="button" id="btn_clear_logs" class="button" style="margin-left:auto;">🗑️ Limpiar logs antiguos (>30 días)</button>
                        <span id="log_loader" class="spinner" style="float:none;margin:0;"></span>
                    </div>
                    
                    <div id="generation_logs_container" style="
                        background:#1e1e1e;
                        border:1px solid #333;
                        border-radius:4px;
                        padding:15px;
                        max-height:500px;
                        overflow-y:auto;
                        font-family:'Consolas','Monaco','Courier New',monospace;
                        font-size:13px;
                        line-height:1.8;
                        color:#d4d4d4;
                    ">
                        <div style="color:#888;text-align:center;padding:20px;">
                            👆 Haz clic en "Cargar últimos logs" para ver el historial de generaciones
                        </div>
                    </div>
                    
                    <div id="generation_stats" style="margin-top:10px;font-size:12px;color:#666;"></div>
                </div>
            </div>
            
            <?php self::render_javascript($openai_key, $gemini_key, $replicate_key, $valueserp_key); ?>
        </section>
        <?php
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
            /* Fix para spinners - solo mostrar cuando estén activos */
            .spinner{visibility:hidden;display:inline-block}
            .spinner.is-active{visibility:visible}
        </style>
        <?php
    }
    
    /**
     * Renderiza JavaScript
     */
    private static function render_javascript($openai_key, $gemini_key, $replicate_key, $valueserp_key) {
        ?>
        <script>
        jQuery(document).ready(function($){
            // Mostrar/ocultar campos según el proveedor seleccionado
            function toggleProviderSettings() {
                var provider = $('#asap_ia_provider_config').val();
                $('.provider-settings').hide();
                if (provider === 'openai') {
                    $('.openai-settings').show();
                } else if (provider === 'gemini') {
                    $('.gemini-settings').show();
                }
            }
            
            // Ejecutar al cargar y al cambiar
            toggleProviderSettings();
            $('#asap_ia_provider_config').on('change', toggleProviderSettings);
            
            // Actualizar costo según modelo seleccionado
            function updateModelCost() {
                var provider = $('#asap_ia_provider_config').val();
                var model = provider === 'openai' ? $('#asap_ia_openai_model').val() : $('#asap_ia_gemini_model').val();
                
                // Costos aproximados por 1000 palabras de artículo
                // Considerando TODAS las llamadas: intro + N secciones + conclusión + meta + contexto
                // Un artículo de 3000 palabras ~= 5-7 llamadas a la API con contexto
                var costs = {
                    // OpenAI
                    'gpt-4o-mini': 0.003,     // $0.003 por 1000 palabras
                    'gpt-4o': 0.015,          // $0.015 por 1000 palabras
                    'gpt-4-turbo': 0.060,     // $0.060 por 1000 palabras
                    'gpt-4.1-mini': 0.003,
                    'gpt-4.1-nano': 0.002,
                    'o1-mini': 0.020,
                    'o1': 0.090,
                    
                    // Gemini
                    'gemini-2.5-flash': 0.0005,       // $0.0005 por 1000 palabras
                    'gemini-2.5-flash-lite': 0.0005,
                    'gemini-2.5-pro': 0.004,
                    'gemini-2.0-flash-exp': 0.0005
                };
                
                var cost = costs[model] || 0.01;
                var costText = '~$' + cost.toFixed(4) + ' cada 1000 palabras';
                
                if (provider === 'openai') {
                    $('#openai_token_cost').text(costText);
                } else {
                    $('#gemini_token_cost').text(costText);
                }
            }
            
            // Ejecutar al cargar y al cambiar modelo
            updateModelCost();
            $('#asap_ia_openai_model, #asap_ia_gemini_model').on('change', updateModelCost);
            
            // Test OpenAI API Key
            $('#asap_test_api_key_config').on('click', function(){
                var apiKey = $('#asap_ia_openai_api_key_config').val();
                var $btn = $(this);
                var $status = $('#asap_api_status_config');
                var $spinner = $('#asap_api_spinner_config');
                
                // Si el campo tiene ******** usamos la key guardada
                if (apiKey === '********') {
                    apiKey = '<?php echo esc_js($openai_key); ?>';
                }
                
                if (!apiKey || apiKey.length < 20) {
                    $status.html('<span style="color:#dc3232;">⚠ Por favor ingresa una API Key válida</span>');
                    return;
                }
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $status.html('');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'asap_test_openai_key',
                        nonce: '<?php echo wp_create_nonce('asap_test_openai_key'); ?>',
                        api_key: apiKey
                    },
                    success: function(response){
                        if (response.success) {
                            $status.html('<span style="color:#46b450;">✓ ' + response.data.message + '</span>');
                        } else {
                            var errorMsg = response.data && response.data.message ? response.data.message : 'Error desconocido';
                            $status.html('<span style="color:#dc3232;">✗ ' + errorMsg + '</span>');
                        }
                    },
                    error: function(){
                        $status.html('<span style="color:#dc3232;">✗ Error de conexión</span>');
                    },
                    complete: function(){
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
            
            // Test Gemini API Key
            $('#asap_test_gemini_key_config').on('click', function(){
                var apiKey = $('#asap_ia_gemini_api_key_config').val();
                var $btn = $(this);
                var $status = $('#asap_gemini_status_config');
                var $spinner = $('#asap_gemini_spinner_config');
                
                // Si el campo tiene ******** usamos la key guardada
                if (apiKey === '********') {
                    apiKey = '<?php echo esc_js($gemini_key); ?>';
                }
                
                if (!apiKey || apiKey.length < 20) {
                    $status.html('<span style="color:#dc3232;">⚠ Por favor ingresa una API Key válida</span>');
                    return;
                }
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $status.html('');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'asap_test_gemini_key',
                        nonce: '<?php echo wp_create_nonce('asap_test_gemini_key'); ?>',
                        api_key: apiKey
                    },
                    success: function(response){
                        if (response.success) {
                            $status.html('<span style="color:#46b450;">✓ ' + response.data.message + '</span>');
                        } else {
                            var errorMsg = response.data && response.data.message ? response.data.message : 'Error desconocido';
                            $status.html('<span style="color:#dc3232;">✗ ' + errorMsg + '</span>');
                        }
                    },
                    error: function(){
                        $status.html('<span style="color:#dc3232;">✗ Error de conexión</span>');
                    },
                    complete: function(){
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
            
            // Test Replicate API Token
            $('#asap_test_replicate_token').on('click', function(){
                var apiToken = $('#asap_ia_replicate_api_token').val();
                var $btn = $(this);
                var $status = $('#asap_replicate_status');
                var $spinner = $('#asap_replicate_spinner');
                
                // Si el campo tiene ******** usamos la key guardada
                if (apiToken === '********') {
                    apiToken = '<?php echo esc_js($replicate_key); ?>';
                }
                
                if (!apiToken || apiToken.length < 10) {
                    $status.html('<span style="color:#dc3232;">⚠ Por favor ingresa un Token válido</span>');
                    return;
                }
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $status.html('');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'asap_test_replicate_token',
                        nonce: '<?php echo wp_create_nonce('asap_test_replicate_token'); ?>',
                        api_token: apiToken
                    },
                    success: function(response){
                        if (response.success) {
                            $status.html('<span style="color:#46b450;">✓ ' + response.data.message + '</span>');
                        } else {
                            var errorMsg = response.data && response.data.message ? response.data.message : 'Error desconocido';
                            $status.html('<span style="color:#dc3232;">✗ ' + errorMsg + '</span>');
                        }
                    },
                    error: function(){
                        $status.html('<span style="color:#dc3232;">✗ Error de conexión</span>');
                    },
                    complete: function(){
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
            
            // Test ValueSERP API Key
            $('#asap_test_valueserp_key').on('click', function(){
                var apiKey = $('#asap_ia_valueserp_api_key').val();
                var $btn = $(this);
                var $status = $('#asap_valueserp_status');
                var $spinner = $('#asap_valueserp_spinner');
                
                // Si el campo tiene ******** usamos la key guardada
                if (apiKey === '********') {
                    apiKey = '<?php echo esc_js($valueserp_key); ?>';
                }
                
                if (!apiKey || apiKey.length < 10) {
                    $status.html('<span style="color:#dc3232;">⚠ Por favor ingresa una API Key válida</span>');
                    return;
                }
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $status.html('');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'asap_test_valueserp_key',
                        nonce: '<?php echo wp_create_nonce('asap_test_valueserp_key'); ?>',
                        api_key: apiKey
                    },
                    success: function(response){
                        if (response.success) {
                            $status.html('<span style="color:#46b450;">✓ ' + response.data.message + '</span>');
                        } else {
                            var errorMsg = response.data && response.data.message ? response.data.message : 'Error desconocido';
                            $status.html('<span style="color:#dc3232;">✗ ' + errorMsg + '</span>');
                        }
                    },
                    error: function(){
                        $status.html('<span style="color:#dc3232;">✗ Error de conexión</span>');
                    },
                    complete: function(){
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
            
            // ==========================================
            // 📊 CARGA DE LOGS DE GENERACIÓN
            // ==========================================
            
            $('#btn_load_logs').on('click', function(){
                var limit = parseInt($('#log_limit').val()) || 100;
                var $btn = $(this);
                var $spinner = $('#log_loader');
                var $container = $('#generation_logs_container');
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                $container.html('<div style="color:#888;text-align:center;padding:20px;">⏳ Cargando logs...</div>');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'asap_get_recent_logs',
                        nonce: '<?php echo wp_create_nonce('asap_recent_logs'); ?>',
                        limit: limit
                    },
                    success: function(resp){
                        if (resp && resp.success && resp.data) {
                            var logs = resp.data.logs || [];
                            var stats = resp.data.stats || {};
                            
                            if (logs.length === 0) {
                                $container.html('<div style="color:#888;text-align:center;padding:20px;">📭 No hay logs disponibles</div>');
                                return;
                            }
                            
                            // Renderizar logs
                            var html = '';
                            logs.forEach(function(log){
                                var icon = getIconForLogType(log.type);
                                var color = getColorForLogType(log.type);
                                var time = log.created_at || '';
                                
                                html += '<div style="margin-bottom:6px;color:' + color + ';">';
                                html += '<span style="color:#888;">[' + time + ']</span> ';
                                html += icon + ' ' + escapeHtml(log.message);
                                
                                if (log.duration) {
                                    html += ' <span style="color:#888;">(' + parseFloat(log.duration).toFixed(2) + 's)</span>';
                                }
                                if (log.cost_usd && parseFloat(log.cost_usd) > 0) {
                                    html += ' <span style="color:#888;">($' + parseFloat(log.cost_usd).toFixed(4) + ')</span>';
                                }
                                
                                if (log.keyword) {
                                    html += ' <span style="color:#666;font-size:11px;">[' + escapeHtml(log.keyword) + ']</span>';
                                }
                                
                                html += '</div>';
                            });
                            
                            $container.html(html);
                            
                            // Estadísticas
                            if (stats) {
                                var statsText = 'Mostrando ' + logs.length + ' logs';
                                if (stats.total_sessions) {
                                    statsText += ' | ' + stats.total_sessions + ' sesiones';
                                }
                                if (stats.total_cost) {
                                    statsText += ' | Costo total: $' + parseFloat(stats.total_cost).toFixed(4);
                                }
                                $('#generation_stats').html(statsText);
                            }
                        } else {
                            $container.html('<div style="color:#dc3232;text-align:center;padding:20px;">❌ Error al cargar logs</div>');
                        }
                    },
                    error: function(){
                        $container.html('<div style="color:#dc3232;text-align:center;padding:20px;">❌ Error de conexión</div>');
                    },
                    complete: function(){
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
            
            // Limpiar logs antiguos
            $('#btn_clear_logs').on('click', function(){
                if (!confirm('¿Estás seguro de limpiar todos los logs de más de 30 días? Esta acción no se puede deshacer.')) {
                    return;
                }
                
                var $btn = $(this);
                var $spinner = $('#log_loader');
                
                $btn.prop('disabled', true);
                $spinner.addClass('is-active');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'asap_clean_old_logs',
                        nonce: '<?php echo wp_create_nonce('asap_clean_logs'); ?>'
                    },
                    success: function(resp){
                        if (resp && resp.success) {
                            alert('✅ ' + resp.data.message);
                            $('#btn_load_logs').trigger('click'); // Recargar logs
                        } else {
                            alert('❌ ' + (resp.data && resp.data.message ? resp.data.message : 'Error desconocido'));
                        }
                    },
                    error: function(){
                        alert('❌ Error de conexión');
                    },
                    complete: function(){
                        $btn.prop('disabled', false);
                        $spinner.removeClass('is-active');
                    }
                });
            });
            
            // Helpers
            function getIconForLogType(type) {
                switch(type) {
                    case 'success': return '✅';
                    case 'error': return '❌';
                    case 'warning': return '⚠️';
                    case 'debug': return '🔧';
                    default: return '📝';
                }
            }
            
            function getColorForLogType(type) {
                switch(type) {
                    case 'success': return '#4ec9b0';
                    case 'error': return '#f48771';
                    case 'warning': return '#dcdcaa';
                    case 'debug': return '#858585';
                    default: return '#d4d4d4';
                }
            }
            
            function escapeHtml(text) {
                if (!text) return '';
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
            }
        });
        </script>
        <?php
    }
}


