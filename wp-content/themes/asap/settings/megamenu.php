<?php
if ( ! defined('ABSPATH') ) exit;

// Verifica si el formulario ha sido enviado y si el nonce es válido
if (isset($_POST['submit']) && check_admin_referer('devlog_nonce_action', 'devlog_nonce_field')) {
    // Guardar las opciones configuradas en el formulario
    update_option('asap_megamenu_enabled', isset($_POST['asap_megamenu_enabled']) ? '1' : '0');
    
    // Guardar estilo de megamenu
    $style = sanitize_text_field($_POST['asap_megamenu_style'] ?? 'fullscreen');
    if (!in_array($style, ['fullscreen', 'dropdown', 'sidebar'])) {
        $style = 'fullscreen';
    }
    update_option('asap_megamenu_style', $style);
    
    // Guardar colores
    update_option('asap_megamenu_bg_color', sanitize_hex_color($_POST['asap_megamenu_bg_color'] ?? '#667eea'));
    update_option('asap_megamenu_bg_color_2', sanitize_hex_color($_POST['asap_megamenu_bg_color_2'] ?? '#764ba2'));
    update_option('asap_megamenu_card_bg', sanitize_hex_color($_POST['asap_megamenu_card_bg'] ?? '#ffffff'));
    update_option('asap_megamenu_text_color', sanitize_hex_color($_POST['asap_megamenu_text_color'] ?? '#333333'));
    update_option('asap_megamenu_hover_color', sanitize_hex_color($_POST['asap_megamenu_hover_color'] ?? '#667eea'));
    update_option('asap_megamenu_column_bg', sanitize_hex_color($_POST['asap_megamenu_column_bg'] ?? '#f8f9fa'));
    update_option('asap_megamenu_link_hover_color', sanitize_hex_color($_POST['asap_megamenu_link_hover_color'] ?? '#135e96'));
    
    // Guardar efectos visuales
    update_option('asap_megamenu_enable_zoom', isset($_POST['asap_megamenu_enable_zoom']) ? '1' : '0');
    update_option('asap_megamenu_enable_shadow', isset($_POST['asap_megamenu_enable_shadow']) ? '1' : '0');
    
    // Guardar texto del logo
    update_option('asap_megamenu_logo_text', sanitize_text_field($_POST['asap_megamenu_logo_text'] ?? 'Menú'));
    update_option('asap_megamenu_hide_logo', isset($_POST['asap_megamenu_hide_logo']) ? '1' : '0');
    
    echo '<div class="notice notice-success is-dismissible"><p><strong>Ajustes guardados</strong>.</p></div>';
}

?>
<style>
    .wrapper-asap-options select {
        width: 300px;
    }
    #asap_calculate_cost {
        min-height: 33px;
    }    
    #cancel-process {
        text-decoration: underline;
        cursor: pointer;
        color: #b32d2e;              
    }
    #cancel-process:hover {
        text-decoration: none;
    }    
    .wrapper-asap-options input[type='number'] {
        margin: 0;
        min-width: 60px;
        padding: 4px 6px !important;
        margin-top: 1px;
    }
    .asap-options h2 {
        display: flex;
        align-items: center;
    }
    .asap-options h2 span {
        background: #202225;
        color: #fff;
        margin-left: 6px;
    }      
    .select2-container--default .select2-selection--single {
        width: 250px;
        padding: 4px 6px !important;
        border-radius: 4px !important;
        border: 1px solid #8c8f94 !important;
        font-size: 14px;
        line-height: 2;
        color: #2c3338;
        background-size: 16px 16px;
        cursor: pointer;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px; 
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px; /* O ajusta según el tamaño de tu flecha de fondo */
        background: none; /* Remueve cualquier flecha por defecto de Select2 si es necesario */
    }

    .select2-container .select2-selection--single {
        height: 38px;
        min-height: 38px;
    }
    /* Ajusta el posicionamiento del texto dentro del select para que no se sobreponga con la flecha */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-right: 24px; /* Ajusta según sea necesario */
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
    }
    /* Estilos para el botón cuando está deshabilitado */
    .asap-options .submit .button-disabled:disabled {
        font-size: 15px;
        line-height: 40px;
        height: 42px;
        padding: 0 34px;
        color: #a7aaad !important;
        border-color: #dcdcde !important;
        background: #f6f7f7 !important;
        box-shadow: none !important;
        cursor: default;
        transform: none !important;
    }

    .asap-options .submit .button-primary:not(.not-button .button-primary):disabled {
        color: #a7aaad !important;
        border-color: #dcdcde !important;
        background: #f6f7f7 !important;
        box-shadow: none !important;
        cursor: default;
        transform: none !important;            
    }

    .asap-options textarea {
        width: 450px !important;
        max-width: 450px !important;
    }
</style>
<form method="post" id="asap-megamenu-settings">

    <?php wp_nonce_field('devlog_nonce_action', 'devlog_nonce_field'); ?>

    <table class="form-table" id="asap-fieldset-megamenu">

        <h2><?php _e('Megamenu Options', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Configure the full-screen megamenu system.', 'asap'); ?></span></span></h2>



        <tbody>
            <tr>
                <th scope="row"><label for="asap_megamenu_enabled"><?php _e('Activar', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activa el sistema de megamenú profesional para tu sitio.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_megamenu_enabled" id="asap_megamenu_enabled" value="1" <?php checked('1', get_option('asap_megamenu_enabled', '0')); ?> /></td>
            </tr>
            
            <tr id="megamenu-style-row" <?php if (!get_option('asap_megamenu_enabled', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_megamenu_style"><?php _e('Estilo', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Elige el estilo visual del megamenu. Full Screen cubre toda la pantalla, Dropdown Compact se integra debajo del header, y Sidebar Slide aparece desde el lateral.', 'asap'); ?></span></span></th>
                <td>
                    <?php $current_style = get_option('asap_megamenu_style', 'dropdown'); ?>
                    <select name="asap_megamenu_style" id="asap_megamenu_style" style="width:350px;">
                        <option value="dropdown" <?php selected($current_style, 'dropdown'); ?>>Minimalist Dropdown</option>
                         <option value="fullscreen" <?php selected($current_style, 'fullscreen'); ?>>Full Screen</option>                       
                        <option value="sidebar" <?php selected($current_style, 'sidebar'); ?>>Sidebar Slide</option>
                    </select>

                </td>
            </tr>
            
            <tr id="megamenu-constructor-row" <?php if (!get_option('asap_megamenu_enabled', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label><?php _e('Constructor visual', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Usa el constructor visual para crear tu megamenu personalizado.', 'asap'); ?></span></span></th>
                <td>
                    <button type="button" class="button button-secondary" id="asap-open-megamenu-builder">
                        Abrir constructor visual
                    </button>
                </td>
            </tr>
            
            <tr id="megamenu-colors-row" <?php 
                $current_style = get_option('asap_megamenu_style', 'dropdown');
                if (!get_option('asap_megamenu_enabled', '0') || $current_style === 'dropdown') {
                    echo 'style="display:none;"';
                }
            ?>>
                <th scope="row"><label><?php _e('Colores', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Personaliza los colores del megamenu para que coincidan con tu marca.', 'asap'); ?></span></span></th>
                <td>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:15px 20px;max-width:650px;">
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Fondo principal</label>
                            <input type="color" name="asap_megamenu_bg_color" value="<?php echo esc_attr(get_option('asap_megamenu_bg_color', '#667eea')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Fondo secundario</label>
                            <input type="color" name="asap_megamenu_bg_color_2" value="<?php echo esc_attr(get_option('asap_megamenu_bg_color_2', '#764ba2')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Fondo de tarjetas</label>
                            <input type="color" name="asap_megamenu_card_bg" value="<?php echo esc_attr(get_option('asap_megamenu_card_bg', '#ffffff')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Fondo de columnas</label>
                            <input type="color" name="asap_megamenu_column_bg" value="<?php echo esc_attr(get_option('asap_megamenu_column_bg', '#f8f9fa')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Color de texto</label>
                            <input type="color" name="asap_megamenu_text_color" value="<?php echo esc_attr(get_option('asap_megamenu_text_color', '#333333')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Color texto header</label>
                            <input type="color" name="asap_megamenu_header_text_color" value="<?php echo esc_attr(get_option('asap_megamenu_header_text_color', '#ffffff')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                            <p style="margin:5px 0 0 0;font-size:12px;color:#666;">Para el texto "Menú" y botón cerrar (X)</p>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Color hover columnas</label>
                            <input type="color" name="asap_megamenu_hover_color" value="<?php echo esc_attr(get_option('asap_megamenu_hover_color', '#667eea')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="display:block;margin-bottom:5px;font-weight:600;">Color hover enlaces</label>
                            <input type="color" name="asap_megamenu_link_hover_color" value="<?php echo esc_attr(get_option('asap_megamenu_link_hover_color', '#135e96')); ?>" style="width:100%;height:40px;border:1px solid #c3c4c7;border-radius:4px;">
                        </div>
                    </div>

                </td>
            </tr>
            
            <tr id="megamenu-effects-row" <?php 
                if (!get_option('asap_megamenu_enabled', '0') || $current_style === 'dropdown') {
                    echo 'style="display:none;"';
                }
            ?>>
                <th scope="row"><label><?php _e('Efectos visuales', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activa o desactiva efectos de hover como zoom y sombras en las columnas.', 'asap'); ?></span></span></th>
                <td>
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <label style="display:inline-flex;align-items:center;gap:8px;">
                            <input type="checkbox" name="asap_megamenu_enable_zoom" value="1" <?php checked('1', get_option('asap_megamenu_enable_zoom', '0')); ?> />
                            <strong>Efecto zoom</strong>
                        </label>
                        <label style="display:inline-flex;align-items:center;gap:8px;">
                            <input type="checkbox" name="asap_megamenu_enable_shadow" value="1" <?php checked('1', get_option('asap_megamenu_enable_shadow', '1')); ?> />
                            <strong>Sombras en columnas</strong>
                        </label>
                    </div>
                    <p class="description" style="margin-top:10px;">Desactiva estos efectos si prefieres un diseño más minimalista y plano.</p>
                </td>
            </tr>
            
            <tr id="megamenu-logo-row" <?php if (!get_option('asap_megamenu_enabled', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label><?php _e('Texto del título', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Personaliza el texto que aparece en el header del megamenu, o ocúltalo completamente.', 'asap'); ?></span></span></th>
                <td>
                    <input type="text" name="asap_megamenu_logo_text" value="<?php echo esc_attr(get_option('asap_megamenu_logo_text', 'Menú')); ?>" placeholder="Menú" style="width:350px;padding:8px 12px;border:1px solid #c3c4c7;border-radius:4px;margin-bottom:10px;">
                    <br>
                    <label style="display:inline-flex;align-items:center;gap:8px;margin-top:10px;">
                        <input type="checkbox" name="asap_megamenu_hide_logo" value="1" <?php checked('1', get_option('asap_megamenu_hide_logo', '0')); ?> />
                        <?php _e('Ocultar texto del logo completamente', 'asap'); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <?php submit_button(); ?>
                </th>
                <td></td>
            </tr>

        </tbody>

    </table>

</form>

<!-- Font Awesome 5.14.0 para el selector de iconos -->
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css?ver=<?php echo ASAP_VERSION; ?>' type='text/css' media='all' />

<style>
/* Tooltip específico para H2 en megamenu settings */
#asap-fieldset-megamenu h2 .asap-tooltip {
    background: #202225;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    cursor: help;
    position: relative;
    margin-left: 8px;
    vertical-align: middle;
    float: none;
}

#asap-fieldset-megamenu h2 .asap-tooltip:hover {
    background: #2c2f33;
}

#asap-fieldset-megamenu h2 .asap-tooltip .tooltiptext {
    visibility: hidden;
    background-color: #202225;
    color: white;
    text-align: left;
    padding: 12px 16px;
    border-radius: 8px;
    position: absolute;
    z-index: 1000;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    width: 280px;
    font-size: 13px;
    line-height: 1.5;
    font-weight: 400;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transition: opacity 0.3s, visibility 0.3s;
    pointer-events: none;
}

#asap-fieldset-megamenu h2 .asap-tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -6px;
    border-width: 6px;
    border-style: solid;
    border-color: #202225 transparent transparent transparent;
}

#asap-fieldset-megamenu h2 .asap-tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle constructor and colors rows
    function toggleConstructorRow() {
        if ($('#asap_megamenu_enabled').is(':checked')) {
            $('#megamenu-style-row').show();
            $('#megamenu-constructor-row').show();
            $('#megamenu-colors-row').show();
            $('#megamenu-effects-row').show();
            $('#megamenu-logo-row').show();
            
            // ✅ Después de mostrar todo, verificar si hay que ocultar colores/efectos (si es dropdown)
            toggleMinimalistOptions();
        } else {
            $('#megamenu-style-row').hide();
            $('#megamenu-constructor-row').hide();
            $('#megamenu-colors-row').hide();
            $('#megamenu-effects-row').hide();
            $('#megamenu-logo-row').hide();
        }
    }

    // Add change event listener to the checkbox
    $('#asap_megamenu_enabled').change(function() {
        toggleConstructorRow();
    });
    
    // Ocultar colores y efectos para Dropdown Compact (minimalista)
    function toggleMinimalistOptions() {
        var style = $('#asap_megamenu_style').val();
        if (style === 'dropdown') {
            $('#megamenu-colors-row').hide();
            $('#megamenu-effects-row').hide();
            
            // ✅ Forzar layout a "grid" en el constructor visual
            var constructorData = $('#asap_megamenu_global_content').val();
            if (constructorData) {
                try {
                    var data = JSON.parse(constructorData);
                    if (data.layout !== 'grid') {
                        data.layout = 'grid';
                        $('#asap_megamenu_global_content').val(JSON.stringify(data));
                    }
                } catch(e) {
                    console.error('Error al parsear datos del megamenu:', e);
                }
            }
        } else {
            $('#megamenu-colors-row').show();
            $('#megamenu-effects-row').show();
        }
    }
    
    // ✅ Ejecutar al cargar la página para ocultar si es dropdown
    toggleMinimalistOptions();
    
    // Ejecutar al cargar y al cambiar estilo
    toggleMinimalistOptions();
    $('#asap_megamenu_style').change(function() {
        toggleMinimalistOptions();
    });
    
    // Open builder
    $('#asap-open-megamenu-builder').click(function(e) {
        e.preventDefault();
        
        // Crear modal con constructor completo
        var modal = $('<div class="asap-megamenu-modal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:99999;display:flex;align-items:center;justify-content:center;">' +
            '<div style="background:white;border-radius:8px;max-width:95vw;max-height:95vh;width:100%;display:flex;flex-direction:column;box-shadow:0 10px 30px rgba(0,0,0,0.3);">' +
                '<div style="display:flex;justify-content:space-between;align-items:center;padding:20px 30px;border-bottom:1px solid #e1e5e9;background:#f8f9fa;border-radius:8px 8px 0 0;">' +
                    '<h2 style="margin:0;color:#1d2327;">🚀 Constructor Visual</h2>' +
                    '<button type="button" class="asap-close-modal" style="background:none;border:none;font-size:24px;cursor:pointer;color:#646970;padding:0;width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:50%;">&times;</button>' +
                '</div>' +
                '<div style="flex:1;padding:30px;overflow-y:auto;">' +
                    '<div class="asap-megamenu-constructor">' +
                        '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;padding:20px;background:#f8f9fa;border-radius:6px;">' +
                            '<div style="display:flex;flex-direction:column;">' +
                                '<label style="font-weight:600;margin-bottom:5px;color:#1d2327;">Layout:</label>' +
                                '<select id="asap-megamenu-layout" style="padding:8px 12px;border:1px solid #c3c4c7;border-radius:4px;font-size:14px;">' +
                                    '<option value="grid">📊 Grid (columnas iguales)</option>' +
                                    '<option value="featured">⭐ Featured (1 destacada + grid)</option>' +
                                    '<option value="cards">🎴 Cards (con imágenes)</option>' +
                                '</select>' +
                            '</div>' +
                            '<div style="display:flex;flex-direction:column;">' +
                                '<label style="font-weight:600;margin-bottom:5px;color:#1d2327;">Columnas:</label>' +
                                '<select id="asap-megamenu-columns" style="padding:8px 12px;border:1px solid #c3c4c7;border-radius:4px;font-size:14px;">' +
                                    '<option value="2">2 columnas</option>' +
                                    '<option value="3">3 columnas</option>' +
                                    '<option value="4" selected>4 columnas</option>' +
                                    '<option value="5">5 columnas</option>' +
                                    '<option value="6">6 columnas</option>' +
                                '</select>' +
                            '</div>' +
                        '</div>' +
                        '<div style="border:1px solid #e1e5e9;border-radius:6px;overflow:hidden;">' +
                            '<div style="display:flex;justify-content:space-between;align-items:center;padding:15px 20px;background:#f8f9fa;border-bottom:1px solid #e1e5e9;">' +
                                '<h3 style="margin:0;color:#1d2327;">🎨 Constructor Visual</h3>' +
                                '<div style="display:flex;gap:10px;">' +
                                    '<button type="button" class="button" id="asap-add-column">➕ Agregar Columna</button>' +
                                    '<button type="button" class="button" id="asap-reset-builder">🔄 Resetear</button>' +
                                '</div>' +
                            '</div>' +
                            '<div style="padding:20px;min-height:400px;background:white;" id="asap-megamenu-builder-content">' +
                                '<div class="asap-megamenu-columns grid-4" style="display:grid;gap:20px;min-height:300px;">' +
                                    '<div class="asap-megamenu-column" data-index="0" style="border:2px dashed #c3c4c7;border-radius:6px;padding:15px;background:#f8f9fa;position:relative;transition:all 0.3s ease;max-height:600px;overflow-y:auto;display:flex;flex-direction:column;">' +
                                        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding-bottom:10px;border-bottom:1px solid #e1e5e9;">' +
                                            '<h4 style="margin:0;font-size:14px;font-weight:600;color:#1d2327;">Columna 1</h4>' +
                                            '<div style="display:flex;gap:5px;">' +
                                                '<button type="button" class="button button-small asap-delete-column" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">🗑️</button>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div style="display:flex;flex-direction:column;gap:10px;flex:1;overflow-y:auto;">' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Imagen (opcional)</label>' +
                                                '<div style="display:flex;gap:5px;">' +
                                                    '<input type="text" class="asap-column-image" placeholder="URL de la imagen" style="flex:1;font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                                    '<button type="button" class="button asap-upload-image" style="font-size:12px;padding:6px 8px;">📷</button>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Icono (opcional)</label>' +
                                                '<input type="text" class="asap-column-icon" placeholder="👆 Click para seleccionar icono" readonly style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                                                '<small style="font-size:11px;color:#646970;">Click para abrir selector de iconos Font Awesome</small>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Título</label>' +
                                                '<input type="text" class="asap-column-title" placeholder="Título de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Descripción</label>' +
                                                '<textarea class="asap-column-description" placeholder="Descripción de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;resize:vertical;min-height:60px;"></textarea>' +
                                            '</div>' +
                                            '<div style="margin-top:10px;">' +
                                                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
                                                    '<label style="margin:0;font-size:12px;font-weight:600;color:#646970;">Items del menú</label>' +
                                                    '<button type="button" class="button button-small asap-add-item" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">➕ Agregar</button>' +
                                                '</div>' +
                                                '<div class="asap-megamenu-items-list">' +
                                                    '<p style="color:#646970;font-size:12px;margin:0;">No hay items. Click en "Agregar" para añadir items al menú.</p>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Texto CTA (opcional)</label>' +
                                                '<input type="text" class="asap-column-cta-text" placeholder="Texto del botón" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">URL CTA (opcional)</label>' +
                                                '<input type="url" class="asap-column-cta-url" placeholder="https://..." style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="asap-megamenu-column" data-index="1" style="border:2px dashed #c3c4c7;border-radius:6px;padding:15px;background:#f8f9fa;position:relative;transition:all 0.3s ease;max-height:600px;overflow-y:auto;display:flex;flex-direction:column;">' +
                                        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding-bottom:10px;border-bottom:1px solid #e1e5e9;">' +
                                            '<h4 style="margin:0;font-size:14px;font-weight:600;color:#1d2327;">Columna 2</h4>' +
                                            '<div style="display:flex;gap:5px;">' +
                                                '<button type="button" class="button button-small asap-delete-column" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">🗑️</button>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div style="display:flex;flex-direction:column;gap:10px;flex:1;overflow-y:auto;">' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Imagen (opcional)</label>' +
                                                '<div style="display:flex;gap:5px;">' +
                                                    '<input type="text" class="asap-column-image" placeholder="URL de la imagen" style="flex:1;font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                                    '<button type="button" class="button asap-upload-image" style="font-size:12px;padding:6px 8px;">📷</button>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Icono (opcional)</label>' +
                                                '<input type="text" class="asap-column-icon" placeholder="👆 Click para seleccionar icono" readonly style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                                                '<small style="font-size:11px;color:#646970;">Click para abrir selector de iconos Font Awesome</small>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Título</label>' +
                                                '<input type="text" class="asap-column-title" placeholder="Título de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Descripción</label>' +
                                                '<textarea class="asap-column-description" placeholder="Descripción de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;resize:vertical;min-height:60px;"></textarea>' +
                                            '</div>' +
                                            '<div style="margin-top:10px;">' +
                                                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
                                                    '<label style="margin:0;font-size:12px;font-weight:600;color:#646970;">Items del menú</label>' +
                                                    '<button type="button" class="button button-small asap-add-item" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">➕ Agregar</button>' +
                                                '</div>' +
                                                '<div class="asap-megamenu-items-list">' +
                                                    '<p style="color:#646970;font-size:12px;margin:0;">No hay items. Click en "Agregar" para añadir items al menú.</p>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Texto CTA (opcional)</label>' +
                                                '<input type="text" class="asap-column-cta-text" placeholder="Texto del botón" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">URL CTA (opcional)</label>' +
                                                '<input type="url" class="asap-column-cta-url" placeholder="https://..." style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="asap-megamenu-column" data-index="2" style="border:2px dashed #c3c4c7;border-radius:6px;padding:15px;background:#f8f9fa;position:relative;transition:all 0.3s ease;max-height:600px;overflow-y:auto;display:flex;flex-direction:column;">' +
                                        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding-bottom:10px;border-bottom:1px solid #e1e5e9;">' +
                                            '<h4 style="margin:0;font-size:14px;font-weight:600;color:#1d2327;">Columna 3</h4>' +
                                            '<div style="display:flex;gap:5px;">' +
                                                '<button type="button" class="button button-small asap-delete-column" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">🗑️</button>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div style="display:flex;flex-direction:column;gap:10px;flex:1;overflow-y:auto;">' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Imagen (opcional)</label>' +
                                                '<div style="display:flex;gap:5px;">' +
                                                    '<input type="text" class="asap-column-image" placeholder="URL de la imagen" style="flex:1;font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                                    '<button type="button" class="button asap-upload-image" style="font-size:12px;padding:6px 8px;">📷</button>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Icono (opcional)</label>' +
                                                '<input type="text" class="asap-column-icon" placeholder="👆 Click para seleccionar icono" readonly style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                                                '<small style="font-size:11px;color:#646970;">Click para abrir selector de iconos Font Awesome</small>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Título</label>' +
                                                '<input type="text" class="asap-column-title" placeholder="Título de la columna" style="flex:1;font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Descripción</label>' +
                                                '<textarea class="asap-column-description" placeholder="Descripción de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;resize:vertical;min-height:60px;"></textarea>' +
                                            '</div>' +
                                            '<div style="margin-top:10px;">' +
                                                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
                                                    '<label style="margin:0;font-size:12px;font-weight:600;color:#646970;">Items del menú</label>' +
                                                    '<button type="button" class="button button-small asap-add-item" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">➕ Agregar</button>' +
                                                '</div>' +
                                                '<div class="asap-megamenu-items-list">' +
                                                    '<p style="color:#646970;font-size:12px;margin:0;">No hay items. Click en "Agregar" para añadir items al menú.</p>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Texto CTA (opcional)</label>' +
                                                '<input type="text" class="asap-column-cta-text" placeholder="Texto del botón" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">URL CTA (opcional)</label>' +
                                                '<input type="url" class="asap-column-cta-url" placeholder="https://..." style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="asap-megamenu-column" data-index="3" style="border:2px dashed #c3c4c7;border-radius:6px;padding:15px;background:#f8f9fa;position:relative;transition:all 0.3s ease;max-height:600px;overflow-y:auto;display:flex;flex-direction:column;">' +
                                        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding-bottom:10px;border-bottom:1px solid #e1e5e9;">' +
                                            '<h4 style="margin:0;font-size:14px;font-weight:600;color:#1d2327;">Columna 4</h4>' +
                                            '<div style="display:flex;gap:5px;">' +
                                                '<button type="button" class="button button-small asap-delete-column" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">🗑️</button>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div style="display:flex;flex-direction:column;gap:10px;flex:1;overflow-y:auto;">' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Imagen (opcional)</label>' +
                                                '<div style="display:flex;gap:5px;">' +
                                                    '<input type="text" class="asap-column-image" placeholder="URL de la imagen" style="flex:1;font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                                    '<button type="button" class="button asap-upload-image" style="font-size:12px;padding:6px 8px;">📷</button>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Icono (opcional)</label>' +
                                                '<input type="text" class="asap-column-icon" placeholder="👆 Click para seleccionar icono" readonly style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                                                '<small style="font-size:11px;color:#646970;">Click para abrir selector de iconos Font Awesome</small>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Título</label>' +
                                                '<input type="text" class="asap-column-title" placeholder="Título de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Descripción</label>' +
                                                '<textarea class="asap-column-description" placeholder="Descripción de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;resize:vertical;min-height:60px;"></textarea>' +
                                            '</div>' +
                                            '<div style="margin-top:10px;">' +
                                                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
                                                    '<label style="margin:0;font-size:12px;font-weight:600;color:#646970;">Items del menú</label>' +
                                                    '<button type="button" class="button button-small asap-add-item" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">➕ Agregar</button>' +
                                                '</div>' +
                                                '<div class="asap-megamenu-items-list">' +
                                                    '<p style="color:#646970;font-size:12px;margin:0;">No hay items. Click en "Agregar" para añadir items al menú.</p>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">Texto CTA (opcional)</label>' +
                                                '<input type="text" class="asap-column-cta-text" placeholder="Texto del botón" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                            '<div style="display:flex;flex-direction:column;gap:5px;">' +
                                                '<label style="font-size:12px;font-weight:600;color:#646970;">URL CTA (opcional)</label>' +
                                                '<input type="url" class="asap-column-cta-url" placeholder="https://..." style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div style="padding:20px 30px;border-top:1px solid #e1e5e9;background:#f8f9fa;border-radius:0 0 8px 8px;display:flex;justify-content:space-between;align-items:center;gap:10px;">' +
                    '<button type="button" class="button asap-close-modal">Cerrar</button>' +
                    '<button type="button" class="button button-primary" id="asap-save-megamenu">Guardar Megamenu</button>' +
                '</div>' +
            '</div>' +
        '</div>');
        
        $('body').append(modal);
        
        // Detectar si es sidebar o dropdown y ocultar opciones innecesarias
        var currentStyle = $('#asap_megamenu_style').val();
        if (currentStyle === 'sidebar') {
            // Ocultar layout y columnas para sidebar
            $('#asap-megamenu-layout').closest('div').hide();
            $('#asap-megamenu-columns').closest('div').hide();
            
            // Agregar mensaje informativo
            $('.asap-megamenu-constructor > div').first().prepend(
                '<div style="grid-column:1/-1;padding:15px;background:#e7f5ff;border:1px solid #2196f3;border-radius:6px;color:#1565c0;font-size:14px;margin-bottom:10px;">' +
                    '<strong>📱 Modo Sidebar:</strong> Los ítems se apilarán verticalmente. Layout y columnas no aplican para este estilo.' +
                '</div>'
            );
        } else if (currentStyle === 'dropdown') {
            // ✅ Ocultar layout para dropdown (siempre es grid)
            $('#asap-megamenu-layout').val('grid').closest('div').hide();
            
            // ✅ Agregar mensaje informativo
            $('.asap-megamenu-constructor > div').first().prepend(
                '<div style="grid-column:1/-1;padding:15px;background:#d1fae5;border:1px solid #10b981;border-radius:6px;color:#065f46;font-size:14px;margin-bottom:10px;">' +
                    '<strong>✨ Modo Minimalist:</strong> El layout siempre es Grid. Personaliza las columnas y su contenido.' +
                '</div>'
            );
        }
        
        // Initialize constructor functionality
        initializeConstructor();
        
        // Cargar contenido guardado
        loadSavedMegamenu();
        
        // Close modal
        modal.find('.asap-close-modal').click(function() {
            modal.remove();
        });
        
        modal.click(function(e) {
            if (e.target === this) {
                modal.remove();
            }
        });
        
        // ⭐ RESTRICCIÓN PARA DROPDOWN COMPACT: Solo Grid
        var currentStyle = $('#asap_megamenu_style').val();
        if (currentStyle === 'dropdown') {
            // Forzar layout a Grid
            $('#asap-megamenu-layout').val('grid').prop('disabled', true);
            
            // Ocultar selector de columnas (no es necesario para dropdown)
            $('#asap-megamenu-columns').closest('div').hide();
            
            // Agregar mensaje informativo
            $('#asap-megamenu-layout').closest('div').after(
                '<div style="grid-column: 1/-1; padding: 10px 15px; background: #e7f3ff; border-left: 3px solid #2271b1; border-radius: 4px; font-size: 13px; color: #1d2327;">' +
                    '<strong>ℹ️ Dropdown Compact:</strong> Solo layout Grid disponible para mantener el diseño minimalista.' +
                '</div>'
            );
        }
    });
    
    // Initialize constructor functionality
    function initializeConstructor() {
        // Update layout and columns display
        function updateLayoutDisplay() {
            var columns = $('#asap-megamenu-columns').val();
            var layout = $('#asap-megamenu-layout').val();
            var container = $('#asap-megamenu-builder-content .asap-megamenu-columns');
            
            // Remove all layout classes
            container.removeClass().addClass('asap-megamenu-columns');
            
            // Apply layout styles
            container.css('display', 'grid');
            container.css('gap', '20px');
            
            // ⭐ SIEMPRE mostrar 2 columnas en el backend para facilitar edición
            // El número real de columnas se guarda en data-columns y se usa en el frontend
            container.attr('data-columns', columns);
            container.css('grid-template-columns', 'repeat(2, 1fr)');
            
            // Resetear estilos de todas las columnas
            container.find('.asap-megamenu-column').css({
                'grid-column': 'auto',
                'border': '2px dashed #c3c4c7',
                'background': '#f8f9fa',
                'grid-row': 'auto'
            });
            
            // Adjust number of columns
            var currentColumns = $('.asap-megamenu-column').length;
            var targetColumns = parseInt(columns);
            
            if (currentColumns > targetColumns) {
                $('.asap-megamenu-column').slice(targetColumns).remove();
            } else if (currentColumns < targetColumns) {
                for (var i = currentColumns; i < targetColumns; i++) {
                    var columnHtml = getColumnTemplate(i);
                    container.append(columnHtml);
                }
            }
            
            // Reindex columns
            $('.asap-megamenu-column').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('h4').text('Columna ' + (index + 1));
            });
        }
        
        // Update layout when columns change
        $('#asap-megamenu-columns').off('change').on('change', function() {
            updateLayoutDisplay();
        });
        
        // Update layout when layout selector changes
        $('#asap-megamenu-layout').off('change').on('change', function() {
            updateLayoutDisplay();
        });
        
        // Initialize display
        updateLayoutDisplay();
        
        // Add column
        $(document).off('click', '#asap-add-column').on('click', '#asap-add-column', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var columns = $('#asap-megamenu-columns').val();
            var currentColumns = $('.asap-megamenu-column').length;
            
            if (currentColumns >= parseInt(columns)) {
                alert('No puedes agregar más columnas que el límite seleccionado');
                return;
            }
            
            var columnHtml = getColumnTemplate(currentColumns);
            $('#asap-megamenu-builder-content .asap-megamenu-columns').append(columnHtml);
        });
        
        // Delete column
        $(document).off('click', '.asap-delete-column').on('click', '.asap-delete-column', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.asap-megamenu-column').remove();
            
            // Reindex columns
            $('.asap-megamenu-column').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('h4').text('Columna ' + (index + 1));
            });
        });
        
        // Add item
        $(document).off('click', '.asap-add-item').on('click', '.asap-add-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var column = $(this).closest('.asap-megamenu-column');
            var itemsContainer = column.find('.asap-megamenu-items-list');
            
            if (itemsContainer.find('.asap-megamenu-item').length === 0) {
                itemsContainer.empty();
            }
            
            var itemHtml = '<div class="asap-megamenu-item" style="display:flex;align-items:center;gap:8px;padding:8px;background:white;border:1px solid #e1e5e9;border-radius:4px;margin-bottom:5px;">' +
                '<input type="text" class="asap-item-icon" placeholder="👆 Click" title="Click para seleccionar icono" readonly style="width:90px;font-size:11px;padding:4px 6px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                '<input type="text" class="asap-item-title" placeholder="Título del item" style="flex:1;font-size:12px;padding:4px 6px;border:1px solid #c3c4c7;border-radius:3px;">' +
                '<input type="url" class="asap-item-url" placeholder="URL" style="flex:1;font-size:12px;padding:4px 6px;border:1px solid #c3c4c7;border-radius:3px;">' +
                '<div style="display:flex;gap:3px;">' +
                    '<button type="button" class="button button-small asap-delete-item" style="font-size:10px;padding:2px 4px;height:auto;line-height:1;">🗑️</button>' +
                '</div>' +
            '</div>';
            
            itemsContainer.append(itemHtml);
        });
        
        // Delete item
        $(document).off('click', '.asap-delete-item').on('click', '.asap-delete-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.asap-megamenu-item').remove();
        });
        
        // Upload image button - WordPress Media Uploader
        var mediaUploader;
        $(document).on('click', '.asap-upload-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var inputField = button.prev('.asap-column-image');
            
            // Si el uploader ya existe, ábrelo
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Crear el media uploader
            mediaUploader = wp.media({
                title: 'Seleccionar imagen para el megamenu',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // Cuando se selecciona una imagen
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
                inputField.trigger('change');
            });
            
            // Abrir el uploader
            mediaUploader.open();
        });
        
        // Icon Picker - Selector visual de iconos Font Awesome
        var currentIconInput = null;
        
        // Iconos Font Awesome 5.14.0 más populares
        var fontAwesomeIcons = [
            'home', 'user', 'envelope', 'phone', 'star', 'heart', 'check', 'times', 
            'search', 'cog', 'shopping-cart', 'calendar', 'camera', 'globe', 'map-marker-alt',
            'comment', 'bell', 'bookmark', 'share', 'download', 'upload', 'lock', 'unlock',
            'edit', 'trash', 'save', 'print', 'file', 'folder', 'image', 'video',
            'music', 'play', 'pause', 'stop', 'forward', 'backward', 'volume-up', 'volume-down',
            'wifi', 'signal', 'battery-full', 'plug', 'lightbulb', 'rocket', 'gift', 'trophy',
            'crown', 'gem', 'coins', 'dollar-sign', 'credit-card', 'chart-line', 'chart-bar',
            'book', 'graduation-cap', 'briefcase', 'building', 'hospital', 'ambulance',
            'car', 'plane', 'ship', 'train', 'bicycle', 'motorcycle', 'bus', 'subway',
            'utensils', 'coffee', 'pizza-slice', 'hamburger', 'ice-cream', 'apple-alt',
            'dumbbell', 'running', 'basketball-ball', 'football-ball', 'futbol', 'hockey-puck',
            'paint-brush', 'palette', 'theater-masks', 'film', 'tv', 'gamepad',
            'anchor', 'umbrella', 'key', 'shield-alt', 'thumbs-up', 'thumbs-down',
            'smile', 'frown', 'laugh', 'angry', 'sad-tear', 'grin-hearts'
        ];
        
        // Función para abrir el selector de iconos
        function openIconPicker(inputElement) {
            currentIconInput = inputElement;
            
            // Verificar si ya existe el modal
            if ($('#asap-icon-picker-modal').length > 0) {
                $('#asap-icon-picker-modal').show();
                return;
            }
            
            // Crear el modal
            var modalHtml = '<div id="asap-icon-picker-modal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);z-index:100001;display:flex;align-items:center;justify-content:center;">' +
                '<div style="background:white;border-radius:12px;max-width:800px;width:90%;max-height:80vh;overflow:hidden;display:flex;flex-direction:column;">' +
                    '<div style="padding:20px;border-bottom:1px solid #e1e5e9;display:flex;justify-content:space-between;align-items:center;">' +
                        '<h3 style="margin:0;font-size:18px;font-weight:600;">Seleccionar Icono Font Awesome</h3>' +
                        '<button type="button" class="asap-close-icon-picker" style="background:none;border:none;font-size:24px;cursor:pointer;padding:0;line-height:1;color:#666;">&times;</button>' +
                    '</div>' +
                    '<div style="padding:15px;border-bottom:1px solid #e1e5e9;">' +
                        '<input type="text" id="asap-icon-search" placeholder="🔍 Buscar icono..." style="width:100%;padding:10px;border:1px solid #c3c4c7;border-radius:6px;font-size:14px;">' +
                        '<p style="margin:8px 0 0 0;font-size:12px;color:#646970;">También puedes pegar código SVG personalizado en el campo de texto</p>' +
                    '</div>' +
                    '<div id="asap-icon-grid" style="flex:1;overflow-y:auto;padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:10px;">' +
                        // Iconos se agregarán dinámicamente
                    '</div>' +
                '</div>' +
            '</div>';
            
            $('body').append(modalHtml);
            
            // Agregar todos los iconos al grid
            var iconGrid = $('#asap-icon-grid');
            fontAwesomeIcons.forEach(function(icon) {
                var iconHtml = '<div class="asap-icon-option" data-icon="fa fa-' + icon + '" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:15px 10px;border:2px solid #e1e5e9;border-radius:8px;cursor:pointer;transition:all 0.2s ease;text-align:center;">' +
                    '<i class="fa fa-' + icon + '" style="font-size:24px;color:#333;margin-bottom:8px;"></i>' +
                    '<span style="font-size:10px;color:#646970;word-break:break-word;">' + icon + '</span>' +
                '</div>';
                iconGrid.append(iconHtml);
            });
            
            // Hover effect
            $(document).on('mouseenter', '.asap-icon-option', function() {
                $(this).css({
                    'border-color': '#2271b1',
                    'background': '#f0f6fc',
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 4px 12px rgba(0,0,0,0.1)'
                });
            }).on('mouseleave', '.asap-icon-option', function() {
                $(this).css({
                    'border-color': '#e1e5e9',
                    'background': 'white',
                    'transform': 'translateY(0)',
                    'box-shadow': 'none'
                });
            });
            
            // Click en icono
            $(document).on('click', '.asap-icon-option', function() {
                var iconClass = $(this).data('icon');
                if (currentIconInput) {
                    currentIconInput.val(iconClass);
                    currentIconInput.trigger('change');
                }
                $('#asap-icon-picker-modal').remove();
            });
            
            // Cerrar modal
            $(document).on('click', '.asap-close-icon-picker', function() {
                $('#asap-icon-picker-modal').remove();
            });
            
            $(document).on('click', '#asap-icon-picker-modal', function(e) {
                if (e.target === this) {
                    $('#asap-icon-picker-modal').remove();
                }
            });
            
            // Búsqueda de iconos
            $('#asap-icon-search').on('input', function() {
                var search = $(this).val().toLowerCase();
                $('.asap-icon-option').each(function() {
                    var iconName = $(this).data('icon').toLowerCase();
                    if (iconName.includes(search)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        }
        
        // Click en campos de iconos - Abrir selector
        $(document).on('click', '.asap-column-icon, .asap-item-icon', function(e) {
            var input = $(this);
            
            // Si está readonly, abrir el picker
            if (input.prop('readonly')) {
                e.preventDefault();
                openIconPicker(input);
            }
        });
        
        // Doble click para permitir edición manual (SVG personalizado)
        $(document).on('dblclick', '.asap-column-icon, .asap-item-icon', function() {
            var input = $(this);
            input.prop('readonly', false);
            input.css({'background': 'white', 'cursor': 'text'});
            input.attr('placeholder', 'Pegar código SVG o clase Font Awesome');
            input.select();
        });
        
        // Al perder foco, volver al modo selector
        $(document).on('blur', '.asap-column-icon, .asap-item-icon', function() {
            var input = $(this);
            setTimeout(function() {
                if (input.val() === '') {
                    input.prop('readonly', true);
                    input.css({'background': '#f9f9f9', 'cursor': 'pointer'});
                    input.attr('placeholder', '👆 Click para seleccionar icono');
                }
            }, 100);
        });
        
        // Save megamenu
        $(document).on('click', '#asap-save-megamenu', function(e) {
            e.preventDefault();
            
            // Collect data
            var content = [];
            $('.asap-megamenu-column').each(function() {
                var column = $(this);
                var columnData = {
                    image: column.find('.asap-column-image').val(),
                    icon: column.find('.asap-column-icon').val(),
                    title: column.find('.asap-column-title').val(),
                    description: column.find('.asap-column-description').val(),
                    cta_text: column.find('.asap-column-cta-text').val(),
                    cta_url: column.find('.asap-column-cta-url').val(),
                    items: []
                };
                
                // Collect items
                column.find('.asap-megamenu-item').each(function() {
                    var item = $(this);
                    var itemData = {
                        icon: item.find('.asap-item-icon').val(),
                        title: item.find('.asap-item-title').val(),
                        url: item.find('.asap-item-url').val()
                    };
                    
                    if (itemData.title) {
                        columnData.items.push(itemData);
                    }
                });
                
                content.push(columnData);
            });
            
            // Mostrar loading
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.text('⏳ Guardando...').prop('disabled', true);
            
            // Save to global option
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'asap_save_megamenu_from_settings',
                    nonce: '<?php echo wp_create_nonce("asap_megamenu_nonce"); ?>',
                    content: JSON.stringify(content),
                    layout: $('#asap-megamenu-layout').val(),
                    columns: $('#asap-megamenu-columns').val()
                },
                success: function(response) {
                    if (response.success) {
                        $btn.text('✅ ¡Guardado!');
                        setTimeout(function() {
                            $('.asap-megamenu-modal').remove();
                            // Mostrar mensaje en la página
                            if ($('.notice-success').length === 0) {
                                $('h2').first().after('<div class="notice notice-success is-dismissible" style="margin: 20px 0;"><p><strong>¡Megamenu guardado correctamente!</strong> Los cambios se verán reflejados en el frontend.</p></div>');
                            }
                        }, 500);
                    } else {
                        $btn.text(originalText).prop('disabled', false);
                        alert('Error al guardar: ' + (response.data || 'Error desconocido'));
                    }
                },
                error: function(xhr, status, error) {
                    $btn.text(originalText).prop('disabled', false);
                    alert('Error de conexión: ' + error);
                    console.error('Error AJAX:', xhr, status, error);
                }
            });
        });
    }
    
    // Load saved megamenu
    function loadSavedMegamenu() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_load_megamenu_from_settings',
                nonce: '<?php echo wp_create_nonce("asap_megamenu_nonce"); ?>'
            },
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;
                    
                    // Set layout and columns
                    $('#asap-megamenu-layout').val(data.layout || 'grid');
                    $('#asap-megamenu-columns').val(data.columns || 4);
                    
                    // Parse content
                    var content = [];
                    try {
                        content = JSON.parse(data.content);
                    } catch (e) {
                        content = [];
                    }
                    
                    if (content && content.length > 0) {
                        // Clear existing columns
                        $('.asap-megamenu-column').remove();
                        
                        // Add columns with data
                        var container = $('#asap-megamenu-builder-content .asap-megamenu-columns');
                        
                        content.forEach(function(columnData, index) {
                            var columnHtml = getColumnTemplate(index);
                            container.append(columnHtml);
                            
                            var column = $('.asap-megamenu-column[data-index="' + index + '"]');
                            
                            // Fill data
                            column.find('.asap-column-image').val(columnData.image || '');
                            column.find('.asap-column-icon').val(columnData.icon || '');
                            column.find('.asap-column-title').val(columnData.title || '');
                            column.find('.asap-column-description').val(columnData.description || '');
                            column.find('.asap-column-cta-text').val(columnData.cta_text || '');
                            column.find('.asap-column-cta-url').val(columnData.cta_url || '');
                            
                            // Si tiene icono, quitar readonly para mostrarlo
                            if (columnData.icon) {
                                var iconInput = column.find('.asap-column-icon');
                                iconInput.prop('readonly', false);
                                iconInput.css({'background': 'white', 'cursor': 'text'});
                            }
                            
                            // Add items
                            if (columnData.items && columnData.items.length > 0) {
                                var itemsContainer = column.find('.asap-megamenu-items-list');
                                itemsContainer.empty();
                                
                                columnData.items.forEach(function(itemData) {
                                    var itemHtml = '<div class="asap-megamenu-item" style="display:flex;align-items:center;gap:8px;padding:8px;background:white;border:1px solid #e1e5e9;border-radius:4px;margin-bottom:5px;">' +
                                        '<input type="text" class="asap-item-icon" placeholder="👆 Click" title="Click para seleccionar icono" readonly value="' + (itemData.icon || '') + '" style="width:90px;font-size:11px;padding:4px 6px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                                        '<input type="text" class="asap-item-title" placeholder="Título del item" value="' + (itemData.title || '') + '" style="flex:1;font-size:12px;padding:4px 6px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                        '<input type="url" class="asap-item-url" placeholder="URL" value="' + (itemData.url || '') + '" style="flex:1;font-size:12px;padding:4px 6px;border:1px solid #c3c4c7;border-radius:3px;">' +
                                        '<div style="display:flex;gap:3px;">' +
                                            '<button type="button" class="button button-small asap-delete-item" style="font-size:10px;padding:2px 4px;height:auto;line-height:1;">🗑️</button>' +
                                        '</div>' +
                                    '</div>';
                                    
                                    itemsContainer.append(itemHtml);
                                    
                                    // Si tiene un icono, quitar el readonly para mostrarlo
                                    if (itemData.icon) {
                                        var lastItem = itemsContainer.find('.asap-megamenu-item:last');
                                        var iconInput = lastItem.find('.asap-item-icon');
                                        iconInput.prop('readonly', false);
                                        iconInput.css({'background': 'white', 'cursor': 'text'});
                                    }
                                });
                            }
                        });
                    }
                }
            },
            error: function() {
                console.log('Error al cargar megamenu guardado');
            }
        });
    }
    
    // Get column template
    function getColumnTemplate(index) {
        return '<div class="asap-megamenu-column" data-index="' + index + '" style="border:2px dashed #c3c4c7;border-radius:6px;padding:15px;background:#f8f9fa;position:relative;transition:all 0.3s ease;max-height:600px;overflow-y:auto;display:flex;flex-direction:column;">' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding-bottom:10px;border-bottom:1px solid #e1e5e9;">' +
                '<h4 style="margin:0;font-size:14px;font-weight:600;color:#1d2327;">Columna ' + (index + 1) + '</h4>' +
                '<div style="display:flex;gap:5px;">' +
                    '<button type="button" class="button button-small asap-delete-column" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">🗑️</button>' +
                '</div>' +
            '</div>' +
            '<div style="display:flex;flex-direction:column;gap:10px;">' +
                '<div style="display:flex;flex-direction:column;gap:5px;">' +
                    '<label style="font-size:12px;font-weight:600;color:#646970;">Imagen (opcional)</label>' +
                    '<div style="display:flex;gap:5px;">' +
                        '<input type="text" class="asap-column-image" placeholder="URL de la imagen" style="flex:1;font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                        '<button type="button" class="button asap-upload-image" style="font-size:12px;padding:6px 8px;">📷</button>' +
                    '</div>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:5px;">' +
                    '<label style="font-size:12px;font-weight:600;color:#646970;">Icono (opcional)</label>' +
                    '<input type="text" class="asap-column-icon" placeholder="👆 Click para seleccionar icono" readonly title="Click para seleccionar icono" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;cursor:pointer;background:#f9f9f9;">' +
                    '<small style="font-size:11px;color:#646970;">Click para abrir selector de iconos Font Awesome</small>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:5px;">' +
                    '<label style="font-size:12px;font-weight:600;color:#646970;">Título</label>' +
                    '<input type="text" class="asap-column-title" placeholder="Título de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:5px;">' +
                    '<label style="font-size:12px;font-weight:600;color:#646970;">Descripción</label>' +
                    '<textarea class="asap-column-description" placeholder="Descripción de la columna" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;resize:vertical;min-height:60px;"></textarea>' +
                '</div>' +
                '<div style="margin-top:10px;">' +
                    '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
                        '<label style="margin:0;font-size:12px;font-weight:600;color:#646970;">Items del menú</label>' +
                        '<button type="button" class="button button-small asap-add-item" style="font-size:11px;padding:2px 6px;height:auto;line-height:1.2;">➕ Agregar</button>' +
                    '</div>' +
                    '<div class="asap-megamenu-items-list">' +
                        '<p style="color:#646970;font-size:12px;margin:0;">No hay items. Click en "Agregar" para añadir items al menú.</p>' +
                    '</div>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:5px;">' +
                    '<label style="font-size:12px;font-weight:600;color:#646970;">Texto CTA (opcional)</label>' +
                    '<input type="text" class="asap-column-cta-text" placeholder="Texto del botón" style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:5px;">' +
                    '<label style="font-size:12px;font-weight:600;color:#646970;">URL CTA (opcional)</label>' +
                    '<input type="url" class="asap-column-cta-url" placeholder="https://..." style="font-size:12px;padding:6px 8px;border:1px solid #c3c4c7;border-radius:3px;">' +
                '</div>' +
            '</div>' +
        '</div>';
    }
});
</script>
