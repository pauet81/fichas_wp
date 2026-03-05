<?php if (current_user_can('manage_options')) : ?>


<?php

// Procesar los datos del formulario al enviar
if (isset($_POST['submit']) && check_admin_referer('asap_schema_nonce_action', 'asap_schema_nonce')) {
    update_option('asap_schema_organization', isset($_POST['asap_schema_organization']) ? '1' : '0');
    update_option('asap_schema_article', isset($_POST['asap_schema_article']) ? '1' : '0');
    update_option('asap_schema_breadcrumbs', isset($_POST['asap_schema_breadcrumbs']) ? '1' : '0');
    update_option('asap_schema_search', isset($_POST['asap_schema_search']) ? '1' : '0');
    update_option('asap_enable_schema_video', isset($_POST['asap_enable_schema_video']) ? '1' : '0');
    update_option('asap_youtube_api_key', sanitize_text_field($_POST['asap_youtube_api_key']));
    //update_option('asap_schema_rating', isset($_POST['asap_schema_rating']) ? '1' : '0');
    //update_option('asap_schema_howto', isset($_POST['asap_schema_howto']) ? '1' : '0');

    // Mensaje de confirmación

    if (get_option('asap_enable_schema_video') && !get_option('asap_youtube_api_key')) {
     echo '<div class="notice notice-error is-dismissible"><p><strong>Se produjo un error</strong>. Para activar los datos estructurados de video, es necesario ingresar la API Key de Youtube.<br><a href="https://www.webempresa.com/blog/como-obtener-la-api-key-de-youtube-sin-aburrirse-en-el-proceso.html" target="_blank" rel="nofollow noopener">Haz clic aquí para conocer cómo obtenerla</a>.</p></div>';       
    } else {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Ajustes guardados</strong>.</p></div>';
    }
 }

// Mostrar errores o mensajes guardados
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

<form method="post" action="">
    <?php wp_nonce_field('asap_schema_nonce_action', 'asap_schema_nonce'); ?>
    <table class="form-table" id="asap-fieldset-one">
        <h2><?php _e('Schema', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This feature enables structured data to optimize your websites on-page SEO. By default, all options are enabled except structured video data. To activate the latter, it is necessary to enter your YouTube API Key.', 'gan'); ?></span></span></h2>

        <tbody>
            <tr>
                <th scope="row"><label for="asap_schema_organization"><?php _e('Activate organization structured data', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activates structured data for organization, helping search engines understand your site.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_schema_organization" id="asap_schema_organization" value="1" <?php checked('1', get_option('asap_schema_organization', '1')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_schema_article"><?php _e('Activate article structured data', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activates structured data for articles, helping search engines understand your content.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_schema_article" id="asap_schema_article" value="1" <?php checked('1', get_option('asap_schema_article', '1')); ?> /></td>
            </tr>

            <tr>
                <th scope="row"><label for="asap_schema_breadcrumbs"><?php _e('Activate breadcrumbs structured data', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activates structured data for breadcrumbs, improving navigation and search visibility.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_schema_breadcrumbs" id="asap_schema_breadcrumbs" value="1" <?php checked('1', get_option('asap_schema_breadcrumbs', '1')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_schema_search"><?php _e('Activate search structured data', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activates structured data for search, enhancing search engine understanding of your site.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_schema_search" id="asap_schema_search" value="1" <?php checked('1', get_option('asap_schema_search', '1')); ?> /></td>
            </tr>
            <!--<tr>
                <th scope="row"><label for="asap_schema_howto"><?php //_e('Activate how to structured data', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php //_e('Activates structured data for articles, helping search engines understand your content.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_schema_howto" id="asap_schema_howto" value="1" <?php //checked('1', get_option('asap_schema_howto', '0')); ?> /></td>
            </tr>            
            <tr id="rating-row" <?php //if (!get_option('asap_schema_howto', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_schema_rating"><?php //_e('Activate rating structured data', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php //_e('Activates structured data for search, enhancing search engine understanding of your site.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_schema_rating" id="asap_schema_rating" value="1" <?php //checked('1', get_option('asap_schema_rating', '0')); ?> /></td>
            </tr>
            -->     
            <tr>
                <th scope="row"><label for="asap_enable_schema_video"><?php _e('Enable video Schema', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Enables video schema to improve the visibility of your videos in search engines.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_enable_schema_video" id="asap_enable_schema_video" value="1" <?php checked('1', get_option('asap_enable_schema_video', '0')); ?> /></td>
            </tr>
            <tr id="youtube-api-key-row" <?php if (!get_option('asap_enable_schema_video', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_youtube_api_key"><?php _e('Youtube API Key', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Required to enable video schema. Enter your YouTube API Key here.', 'asap'); ?></span></span></th>
                <td>
                    <input type="text" name="asap_youtube_api_key" id="asap_youtube_api_key" value="<?php echo esc_attr(get_option('asap_youtube_api_key', '')); ?>" />
                    <a style="margin-top: 8px;display: inline-block; width: 100%;" href="https://www.webempresa.com/blog/como-obtener-la-api-key-de-youtube-sin-aburrirse-en-el-proceso.html" target="_blank" rel="noopener nofollow">Cómo obtener la Youtube API Key</a>
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
<script>
    jQuery(document).ready(function($) {
        function toggleYouTubeApiKey() {
            if ($('#asap_enable_schema_video').is(':checked')) {
                $('#youtube-api-key-row').show();
            } else {
                $('#youtube-api-key-row').hide();
            }
        }

        // Add change event listener to the checkbox
        $('#asap_enable_schema_video').change(function() {
            toggleYouTubeApiKey();
        });

         function showRating() {
            if ($('#asap_schema_howto').is(':checked')) {
                $('#rating-row').show();
            } else {
                $('#rating-row').hide();
            }
        }

        // Add change event listener to the checkbox
        $('#asap_schema_howto').change(function() {
            showRating();
        });
    });
</script>
<?php else: ?>
<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p><?php _e('It looks like you do not have sufficient permissions to view this page.', 'asap'); ?></p></div>
<?php endif; ?>
