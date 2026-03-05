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


    .asap-options h3 {
        display: flex;
        margin: 0;
        font-size: 16px !important;
            width: 100%;
    }
 .asap-options .form-table th.deh3 {
    padding-top: 28px;
  }     
</style>



<?php if (current_user_can('manage_options')) : ?>

<?php

// Verifica si el formulario ha sido enviado y si el nonce es válido
if (isset($_POST['submit']) && check_admin_referer('devlog_nonce_action', 'devlog_nonce_field')) {
    // Guardar las opciones configuradas en el formulario
    update_option('asap_delete_version', isset($_POST['asap_delete_version']) ? '1' : '0');
    update_option('asap_delete_wlw', isset($_POST['asap_delete_wlw']) ? '1' : '0');
    update_option('asap_delete_rds', isset($_POST['asap_delete_rds']) ? '1' : '0');
    update_option('asap_delete_api_rest_link', isset($_POST['asap_delete_api_rest_link']) ? '1' : '0');
    update_option('asap_content_type_options', isset($_POST['asap_content_type_options']) ? '1' : '0');
    update_option('asap_frame_options', isset($_POST['asap_frame_options']) ? '1' : '0');
    update_option('asap_xxs_protection', isset($_POST['asap_xxs_protection']) ? '1' : '0');
    update_option('asap_strict_transport_security', isset($_POST['asap_strict_transport_security']) ? '1' : '0');
    update_option('asap_referrer_policy', isset($_POST['asap_referrer_policy']) ? '1' : '0');

    // Mensaje de confirmación
    echo '<div class="notice notice-success is-dismissible"><p><strong>Ajustes guardados</strong>.</p></div>';
}

?>

<style>
    .asap-tooltip-a {
        cursor: pointer;
    }
</style>

<form method="post" id="asap-manual-niche">

    <?php wp_nonce_field('devlog_nonce_action', 'devlog_nonce_field'); ?>

    <table class="form-table" id="asap-fieldset-one">

        <h2><?php _e('Security', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This section allows you to activate various security options to strengthen the protection of your website.', 'asap'); ?></span></span></h2>

        <tbody>
            <tr>
                <th scope="row"><label for="asap_delete_version"><?php _e('Delete WordPress version', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activating this option hides the WordPress version. It is unwise to reveal full WordPress version information in the header. People with bad intentions can easily use Google to find sites that use a specific version of WordPress and target them with exploits.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_delete_version" id="asap_delete_version" value="1" <?php checked('1', get_option('asap_delete_version', '1')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_delete_wlw"><?php _e('Delete WLW Link', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Enable this option hides Windows Live Writer link. If youre not using Windows Live Writer, theres really no valid reason to have your link in the page header. This is to hide that you are using WordPress.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_delete_wlw" id="asap_delete_wlw" value="1" <?php checked('1', get_option('asap_delete_wlw', '1')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_delete_rds"><?php _e('Delete RDS Link', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activate this option hides Really Simple Discovery link. If you are not using any Really Simple Discovery services such as pingbacks, there is no need to announce this link in the header. This is to hide that you are using WordPress.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_delete_rds" id="asap_delete_rds" value="1" <?php checked('1', get_option('asap_delete_rds', '1')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_delete_api_rest_link"><?php _e('Delete API REST Link', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Enable this option hides REST API link. WordPress comes with a REST API system that allows you to access different data in a structured format. We recommend you disable these links that appear in the header.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_delete_api_rest_link" id="asap_delete_api_rest_link" value="1" <?php checked('1', get_option('asap_delete_api_rest_link', '1')); ?> /></td>
            </tr>
            
            <tr >
                <th scope="row" colspan="2"  class="deh3">
                    <h3><?php _e('Security Headers', 'asap'); ?></h3>
                </th>
            </tr>

            <tr>
                <th scope="row"><label for="asap_content_type_options"><?php _e('X-Content-Type-Options', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Enabling this option will force the browser to only load external resources if the content type matches what is expected. This prevents malicious hidden code in unexpected files.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_content_type_options" id="asap_content_type_options" value="1" <?php checked('1', get_option('asap_content_type_options', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_frame_options"><?php _e('X-Frame-Options', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activating this option will prevent clickjacking attacks by simply not allowing your content to be embedded on other websites. Some sites have issues with the theme customizer previewing when this code is enabled. If you notice any problems with the sites iframes or videos, disable this option.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_frame_options" id="asap_frame_options" value="1" <?php checked('1', get_option('asap_frame_options', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_xxs_protection"><?php _e('X-XXS-Protection', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Enabling this option helps prevent some types of script injection attacks.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_xxs_protection" id="asap_xxs_protection" value="1" <?php checked('1', get_option('asap_xxs_protection', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_strict_transport_security"><?php _e('Strict-Transport-Security', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Enabling this option will tell your web server to use only HTTPS and not allow insecure HTTP connections. Before implementing this, it is important that you verify that your website has an SSL certificate and that it is working correctly.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_strict_transport_security" id="asap_strict_transport_security" value="1" <?php checked('1', get_option('asap_strict_transport_security', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_referrer_policy"><?php _e('Referrer-Policy', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activating this option allows you to continue tracking data internally on your website, but no other website will know that a visitor has arrived from a link on your website.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_referrer_policy" id="asap_referrer_policy" value="1" <?php checked('1', get_option('asap_referrer_policy', '0')); ?> /></td>
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

<?php else: ?>

<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p>Parece que no tienes los permisos suficientes para ver esta página.</p></div>

<?php endif; ?>