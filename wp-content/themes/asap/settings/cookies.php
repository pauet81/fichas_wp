
<?php if (current_user_can('manage_options')) : ?>

<?php

// Verifica si el formulario ha sido enviado y si el nonce es válido
if (isset($_POST['submit']) && check_admin_referer('devlog_nonce_action', 'devlog_nonce_field')) {
    // Guardar las opciones configuradas en el formulario
    update_option('asap_show_cookies', isset($_POST['asap_show_cookies']) ? '1' : '0');
    update_option('asap_cookies_text', sanitize_text_field($_POST['asap_cookies_text']));
    update_option('asap_cookies_text_btn', sanitize_text_field($_POST['asap_cookies_text_btn']));
    update_option('asap_cookies_text_link', sanitize_text_field($_POST['asap_cookies_text_link']));
    update_option('asap_cookies_link', absint($_POST['asap_cookies_link']));


    if (get_option('asap_show_cookies') && (!get_option('asap_cookies_text') || !get_option('asap_cookies_text_btn') || !get_option('asap_cookies_text_link') || !get_option('asap_cookies_link'))) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Se produjo un error</strong>. Es necesario completar todos los campos para que el aviso de cookies se muestre correctamente.</p></div>';       

    } else {
    // Mensaje de confirmación
        echo '<div class="notice notice-success is-dismissible"><p><strong>Ajustes guardados</strong>.</p></div>';
    }
}

?>

<style>
    .asap-tooltip-a {
        cursor: pointer;
    }
</style>
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


<form method="post" id="asap-manual-niche">

    <?php wp_nonce_field('devlog_nonce_action', 'devlog_nonce_field'); ?>

    <table class="form-table" id="asap-fieldset-one">

        <h2><?php _e('Cookie Notice', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This feature allows you to activate a simple but elegant cookie notice. It is not adapted to the European GDPR, so if you have traffic from Europe we recommend using the Complianz plugin.', 'asap'); ?></span></span></h2>

        <tbody>

        <div class="notice notice-warning">
            <p>Este aviso de cookies no cumple con la normativa GDPR de Europa. Si tienes tráfico europeo, <a href="https://es.wordpress.org/plugins/complianz-gdpr/" target="_blank" rel="nofollow noopener">te recomendamos instalar Complianz</a>.</p>
        </div>
                   
            <tr>
                <th scope="row"><label for="asap_show_cookies"><?php _e('Activate cookies', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activa la funcionalidad del aviso de cookies.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_show_cookies" id="asap_show_cookies" value="1" <?php checked('1', get_option('asap_show_cookies', '0')); ?> /></td>
            </tr>
            <tr class="cookie-settings-row" <?php if (!get_option('asap_show_cookies', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_cookies_text"><?php _e('Message', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('El mensaje que se mostrará en el banner de consentimiento de cookies.', 'asap'); ?></span></span></th>
                <td><textarea name="asap_cookies_text" id="asap_cookies_text"><?php echo esc_textarea(get_option('asap_cookies_text', '')); ?></textarea></td>
            </tr>
            <tr class="cookie-settings-row" <?php if (!get_option('asap_show_cookies', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_cookies_text_btn"><?php _e('Accept text', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('El texto del botón aceptar en el banner de consentimiento de cookies.', 'asap'); ?></span></span></th>
                <td><input type="text" name="asap_cookies_text_btn" id="asap_cookies_text_btn" value="<?php echo esc_attr(get_option('asap_cookies_text_btn', '')); ?>" /></td>
            </tr>
            <tr class="cookie-settings-row" <?php if (!get_option('asap_show_cookies', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_cookies_text_link"><?php _e('More information text', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('El texto del enlace de más información en el banner de consentimiento de cookies.', 'asap'); ?></span></span></th>
                <td><input type="text" name="asap_cookies_text_link" id="asap_cookies_text_link" value="<?php echo esc_attr(get_option('asap_cookies_text_link', '')); ?>" /></td>
            </tr>
            <tr class="cookie-settings-row" <?php if (!get_option('asap_show_cookies', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_cookies_link"><?php _e('Cookies page', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('La página que contiene la política de cookies.', 'asap'); ?></span></span></th>
                <td><?php wp_dropdown_pages(array('name' => 'asap_cookies_link', 'selected' => get_option('asap_cookies_link'))); ?></td>
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
<script>
    jQuery(document).ready(function($) {
        function toggleCookieSettings() {
            if ($('#asap_show_cookies').is(':checked')) {
                $('.cookie-settings-row').show();
            } else {
                $('.cookie-settings-row').hide();
            }
        }

        // Add change event listener to the checkbox
        $('#asap_show_cookies').change(function() {
            toggleCookieSettings();
        });
    });
</script>
<?php else: ?>

<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p>Parece que no tienes los permisos suficientes para ver esta página.</p></div>

<?php endif; ?>