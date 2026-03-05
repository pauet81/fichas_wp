<style>
    .wrapper-asap-options select {
        width: 250px;
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

    .asap-options h3 {
        display: flex;
        margin: 0;
        font-size: 16px !important;
            width: 100%;
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

  .asap-options .form-table th.deh3 {
    padding-top: 28px;
  }  

    tr th i {
        display: inline-block;
        vertical-align: top;
        box-sizing: border-box;
        margin: 1px 0 0 6px;
        padding: 0 5px;
        min-width: 18px;
        height: 18px;
        border-radius: 3px;
        background-color: #e74c3c;
        color: #fff;
        font-size: 11px;
        line-height: 1.715;
        text-align: center;
        font-style: normal;
    }

</style>



<?php if (current_user_can('manage_options')) : ?>

<?php

// Verifica si el formulario ha sido enviado y si el nonce es válido
if (isset($_POST['submit']) && check_admin_referer('devlog_nonce_action', 'devlog_nonce_field')) {
    // Guardar las opciones configuradas en el formulario
    update_option('asap_optimize_html', isset($_POST['asap_optimize_html']) ? '1' : '0');
    update_option('asap_optimize_analytics', isset($_POST['asap_optimize_analytics']) ? '1' : '0');
    update_option('asap_enable_js_defer', isset($_POST['asap_enable_js_defer']) ? '1' : '0');
    update_option('asap_deactivate_jquery', isset($_POST['asap_deactivate_jquery']) ? '1' : '0');
    update_option('asap_optimize_youtube', isset($_POST['asap_optimize_youtube']) ? '1' : '0');
    update_option('asap_disable_embed', isset($_POST['asap_disable_embed']) ? '1' : '0');
    update_option('asap_preload_featured_image', isset($_POST['asap_preload_featured_image']) ? '1' : '0');
    update_option('asap_options_fonts', $_POST['asap_options_fonts']);
    update_option('asap_resources_module', isset($_POST['asap_resources_module']) ? '1' : '0');
    update_option('asap_remove_feed_links', isset($_POST['asap_remove_feed_links']) ? '1' : '0');
    update_option('asap_optimize_adsense', isset($_POST['asap_optimize_adsense']) ? '1' : '0');
    update_option('asap_deactivate_no_escencial', isset($_POST['asap_deactivate_no_escencial']) ? '1' : '0');
    update_option('asap_deactivate_contactform', isset($_POST['asap_deactivate_contactform']) ? '1' : '0');

    update_option(
        'asap_enable_asap_thumbnails',
        isset( $_POST['asap_enable_asap_thumbnails'] ) ? '1' : '0'
    );


    // === NUEVO: Optimización de imágenes + WebP ===
    update_option('asap_image_opt_enable', isset($_POST['asap_image_opt_enable']) ? '1' : '0');

    $asap_quality = isset($_POST['asap_image_opt_quality']) ? intval($_POST['asap_image_opt_quality']) : 75;
    $asap_quality = max(10, min(100, $asap_quality));
    update_option('asap_image_opt_quality', $asap_quality);

    update_option('asap_image_opt_webp', isset($_POST['asap_image_opt_webp']) ? '1' : '0');

    // === NUEVO: Precarga de enlaces internos ===
    update_option('asap_link_prefetch', isset($_POST['asap_link_prefetch']) ? '1' : '0');

    // === NUEVO: límites de dimensión ===
    $max_w = isset($_POST['asap_image_opt_max_w']) ? intval($_POST['asap_image_opt_max_w']) : 0;
    $max_w = max(0, min(10000, $max_w));
    update_option('asap_image_opt_max_w', $max_w);

    $max_h = isset($_POST['asap_image_opt_max_h']) ? intval($_POST['asap_image_opt_max_h']) : 0;
    $max_h = max(0, min(10000, $max_h));
    update_option('asap_image_opt_max_h', $max_h);

    update_option('asap_image_opt_strip_exif', isset($_POST['asap_image_opt_strip_exif']) ? '1' : '0');

    if (function_exists('is_woocommerce')) {
        update_option('asap_wc_remove_dependencies', isset($_POST['asap_wc_remove_dependencies']) ? '1' : '0');
    }

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

        <h2><?php _e('Performance', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Asap Theme itself is very fast, but these functions allow you to activate different web optimization options to further improve the performance of your website.', 'asap'); ?></span></span></h2>

        <tbody>

             <tr>
                <th scope="row"><label for="asap_resources_module"><?php _e('Activate resource management module', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to activate the resource or script management module. It will allow you, on each post or page, to disable CSS or JS scripts individually to improve the performance of the site.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_resources_module" id="asap_resources_module" value="1" <?php checked('1', get_option('asap_resources_module', '0')); ?> /></td>
            </tr>
           

            <tr >
                <th scope="row" colspan="2"  class="deh3">
                    <h3><?php _e('Optimizar HTML', 'asap'); ?></h3>
                </th>
            </tr>

            <tr>
                <th scope="row"><label for="asap_optimize_html"><?php _e('Optimize HTML code', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to optimize your sites HTML code, removing unnecessary whitespace and comments, resulting in faster loading times and improved performance.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_optimize_html" id="asap_optimize_html" value="1" <?php checked('1', get_option('asap_optimize_html', '0')); ?> /></td>
            </tr>

            <tr>
                <th scope="row"><label for="asap_remove_feed_links"><?php _e('Remove RSS feed links', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to automatically remove RSS feed links from your WordPress site, reducing the number of outbound links and potentially improving security and performance.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_remove_feed_links" id="asap_remove_feed_links" value="1" <?php checked('1', get_option('asap_remove_feed_links', '0')); ?> /></td>
            </tr>

            <!-- ====== NUEVO: Precarga de enlaces internos ====== -->
            <tr>
                <th scope="row"><label for="asap_link_prefetch">
                    <?php _e('Precarga de enlaces internos', 'asap'); ?>
                </label>
                <span class="asap-tooltip">?<span class="tooltiptext">
                    <?php _e('Hace prefetch de páginas internas al pasar el mouse o tocar un enlace, acelerando la navegación percibida. Respeta enlaces externos/descargas/anchors.', 'asap'); ?>
                </span></span>
                </th>
                <td>
                    <input type="checkbox"
                           name="asap_link_prefetch"
                           id="asap_link_prefetch"
                           value="1"
                           <?php checked('1', get_option('asap_link_prefetch', '0')); ?> />
                </td>
            </tr>

            <tr  >
                <th scope="row"  colspan="2" class="deh3">
                    <h3><?php _e('Optimizar JS', 'asap'); ?></h3>
                </th>
            </tr>

            <tr>
                <th scope="row"><label for="asap_optimize_adsense"><?php _e('Optimizar Google AdSense', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Esta función optimiza la carga de los anuncios de Google AdSense, precargando los recursos esenciales y configurándolos para que se carguen de forma asíncrona. La función utiliza la estrategia de precarga (preload) para asegurar que los recursos de AdSense estén disponibles lo antes posible, lo que minimiza el impacto en la velocidad de carga de la página.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_optimize_adsense" id="asap_optimize_adsense" value="1" <?php checked('1', get_option('asap_optimize_adsense', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_optimize_analytics"><?php _e('Optimize Google Analytics', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to optimize the integration of Google Analytics on your site, ensuring that tracking does not slow down performance and providing more efficient loading of the Analytics script.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_optimize_analytics" id="asap_optimize_analytics" value="1" <?php checked('1', get_option('asap_optimize_analytics', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_enable_js_defer"><?php _e('Enable Javascript lazy loading', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to enable lazy loading of JavaScript files, meaning that scripts will load after the main content of the page has loaded, improving initial load times and user experience.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_enable_js_defer" id="asap_enable_js_defer" value="1" <?php checked('1', get_option('asap_enable_js_defer', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_deactivate_jquery"><?php _e('Disable jQuery', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to disable the jQuery library on your WordPress site if it is not needed, reducing the number of scripts loaded and improving site performance. Make sure no plugin needs it, otherwise it could cause problems.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_deactivate_jquery" id="asap_deactivate_jquery" value="1" <?php checked('1', get_option('asap_deactivate_jquery', '0')); ?> /></td>
            </tr>

            <tr>
                <th scope="row"><label for="asap_deactivate_no_escencial"><?php _e('Desactivar JS no esencial del frontend', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Esta opción te permite desactivar algunos scripts de JavaScript que WordPress carga por defecto en el frontend, como wp-hooks o wp-i18n. Si no usás bloques dinámicos ni funcionalidades avanzadas del editor, podés reducir el peso de la página y mejorar el rendimiento. Asegurate de que tu theme o plugins no dependan de ellos, ya que podrían dejar de funcionar correctamente.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_deactivate_no_escencial" id="asap_deactivate_no_escencial" value="1" <?php checked('1', get_option('asap_deactivate_no_escencial', '0')); ?> /></td>
            </tr>



            <tr>
                <th scope="row"><label for="asap_deactivate_contactform"><?php _e('Optimizar Contact Form 7', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Evita que el plugin Contact Form 7 cargue sus scripts y estilos en todas las páginas. Solo los cargará si detecta un formulario presente en el contenido. Esto mejora el rendimiento general del sitio.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_deactivate_contactform" id="asap_deactivate_contactform" value="1" <?php checked('1', get_option('asap_deactivate_contactform', '1')); ?> /></td>
            </tr>





            <tr >
                <th scope="row" colspan="2" class="deh3" >
                    <h3><?php _e('Optimizar Medios', 'asap'); ?></h3>
                </th>
            </tr>

            <tr>
                <th scope="row"><label for="asap_optimize_youtube"><?php _e('Optimize YouTube videos', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to load videos only when the user clicks on them, instead of loading them automatically, reducing initial page load time and improving the user experience.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_optimize_youtube" id="asap_optimize_youtube" value="1" <?php checked('1', get_option('asap_optimize_youtube', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_disable_embed"><?php _e('Disable embedded content', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to disable embedded third-party content on your WordPress site, such as videos and social media posts, reducing external requests and improving loading times and security.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_disable_embed" id="asap_disable_embed" value="1" <?php checked('1', get_option('asap_disable_embed', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_preload_featured_image"><?php _e('Preload featured image', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_preload_featured_image" id="asap_preload_featured_image" value="1" <?php checked('1', get_option('asap_preload_featured_image', '1')); ?> /></td>
            </tr>

            <tr>
                <th scope="row"><label for="asap_options_fonts"><?php _e('Google Fonts', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to select how you want to load Google Fonts on your WordPress site. You can choose to host fonts locally (recommended to improve performance), host them externally from Google servers, or disable Google Fonts completely.', 'asap'); ?></span></span></th>
                <td>
                    <select name="asap_options_fonts" id="asap_options_fonts">
                        <option value="2" <?php selected(get_option('asap_options_fonts', '2'), '2'); ?>><?php _e('Host locally (Recommended)', 'asap'); ?></option>
                        <option value="1" <?php selected(get_option('asap_options_fonts', '2'), '1'); ?>><?php _e('Host externally', 'asap'); ?></option>
                        <option value="3" <?php selected(get_option('asap_options_fonts', '2'), '3'); ?>><?php _e('Disable Google Fonts', 'asap'); ?></option>
                    </select>
                </td>
            </tr>



            <!-- ====== NUEVO: Optimización de imágenes ====== -->
            <tr>
                <th scope="row"><label for="asap_image_opt_enable">
                    <?php _e('Optimización de imágenes', 'asap'); ?>
                </label>
                <span class="asap-tooltip">?<span class="tooltiptext">
                    <?php _e('Recomprime las imágenes nuevas al subirlas según la calidad indicada y, si está habilitado, genera copias WebP. No modifica imágenes existentes, solo impacta en las nuevas.', 'asap'); ?>
                </span></span>
                </th>
                <td>
                    <input type="checkbox"
                           name="asap_image_opt_enable"
                           id="asap_image_opt_enable"
                           value="1"
                           <?php checked('1', get_option('asap_image_opt_enable', '0')); ?> />
                </td>
            </tr>

            <tr class="asap-image-opt-advanced">
                <th scope="row"><label for="asap_image_opt_quality">
                    <?php _e('Calidad de compresión', 'asap'); ?>
                </label>
                <span class="asap-tooltip">?<span class="tooltiptext">
                    <?php _e('En escala del 1 al 100, siendo 1 la menor calidad y 100 la calidad máxima.', 'asap'); ?>
                </span></span>
            </th>
                <td>
                    <input type="number"
                           min="10" max="100" step="1"
                           name="asap_image_opt_quality"
                           id="asap_image_opt_quality"
                           value="<?php echo esc_attr( get_option('asap_image_opt_quality', 75) ); ?>" />
                </td>
            </tr>

            <tr class="asap-image-opt-advanced">
                <th scope="row"><label for="asap_image_opt_webp">
                    <?php _e('Intentar convertir a WebP', 'asap'); ?>
                </label>
                <span class="asap-tooltip">?<span class="tooltiptext">
                    <?php _e('Genera copias WebP de la imagen original y de sus tamaños al subir. Requiere soporte WebP en GD o Imagick. Se servirán en el frontend cuando estén disponibles.', 'asap'); ?>
                </span></span>
                </th>
                <td>
                    <input type="checkbox"
                           name="asap_image_opt_webp"
                           id="asap_image_opt_webp"
                           value="1"
                           <?php checked('1', get_option('asap_image_opt_webp', '0')); ?> />
                </td>
            </tr>

            <tr class="asap-image-opt-advanced">
                <th scope="row">
                    <label for="asap_image_opt_max_w"><?php _e('Ancho máximo en píxeles', 'asap'); ?></label>
                    <span class="asap-tooltip">?<span class="tooltiptext">
                        <?php _e('Si la imagen nueva excede este ancho, se redimensiona manteniendo proporciones (sin recorte). Solo aplica a imágenes subidas a partir de ahora.', 'asap'); ?>
                    </span></span>
                </th>
                <td>
                    <input type="number" min="0" step="1"
                           name="asap_image_opt_max_w"
                           id="asap_image_opt_max_w"
                           value="<?php echo esc_attr( get_option('asap_image_opt_max_w', 800) ); ?>" />
                    <span class="description">
                        <?php _e('0 desactiva', 'asap'); ?>
                    </span>
                </td>
            </tr>

            <tr class="asap-image-opt-advanced">
                <th scope="row">
                    <label for="asap_image_opt_max_h"><?php _e('Alto máximo en píxeles', 'asap'); ?></label>
                    <span class="asap-tooltip">?<span class="tooltiptext">
                        <?php _e('Si se indica, también se limita el alto. Mantiene proporciones (no recorta). Útil para imágenes extremadamente altas', 'asap'); ?>
                    </span></span>
                </th>
                <td>
                    <input type="number" min="0" step="1"
                           name="asap_image_opt_max_h"
                           id="asap_image_opt_max_h"
                           value="<?php echo esc_attr( get_option('asap_image_opt_max_h', 0) ); ?>" />
                    <span class="description">
                        <?php _e('0 para ignorar alto máximo', 'asap'); ?>
                    </span>
                </td>
            </tr>

            <tr class="asap-image-opt-advanced">
                <th scope="row">
                    <label for="asap_image_opt_strip_exif"><?php _e('Eliminar metadatos EXIF', 'asap'); ?></label>
                    <span class="asap-tooltip">?<span class="tooltiptext">
                        <?php _e('Remueve datos EXIF (GPS, cámara, fecha, etc.) para reducir peso y mejorar privacidad. Mantiene la orientación aplicándola antes de limpiar. Solo afecta a imágenes subidas de ahora en adelante.', 'asap'); ?>
                    </span></span>
                </th>
                <td>
                    <input type="checkbox"
                           name="asap_image_opt_strip_exif"
                           id="asap_image_opt_strip_exif"
                           value="1"
                           <?php checked('1', get_option('asap_image_opt_strip_exif', '0')); ?> />
                </td>
            </tr>


            <tr>
                <th scope="row">
                    <label for="asap_enable_asap_thumbnails">
                        <?php _e( 'Habilitar miniaturas de ASAP', 'asap' ); ?>
                    </label>
                    <span class="asap-tooltip">?
                        <span class="tooltiptext">
                            <?php _e( 'Activa o desactiva los tamaños de imagen “post-thumbnail”, “side-thumbnail” y los thumbnails usados en el diseño tipo diario. Te recomendamos desactivarlo solo si no estás usando el diseño diario ni el listado de posts clásico de WordPress. Al desactivarlo, se generan muchas menos imágenes al subir una foto, lo cual ahorra espacio en disco. Eso sí: puede que algunas secciones carguen más lento si no hay miniaturas disponibles y se usa la imagen completa.', 'asap' ); ?>
                        </span>
                    </span>
                </th>
                <td>
                    <input type="checkbox"
                           name="asap_enable_asap_thumbnails"
                           id="asap_enable_asap_thumbnails"
                           value="1"
                           <?php checked( '1', get_option( 'asap_enable_asap_thumbnails', '1' ) ); ?> />
                </td>
            </tr>
            

            <?php if (function_exists('is_woocommerce')): ?>
            <tr>
                <th scope="row" colspan="2"  class="deh3" >
                    <h3><?php _e('Optimizar Woocommerce', 'asap'); ?></h3>
                </th>
            </tr>            
            <tr>
                <th scope="row"><label for="asap_wc_remove_dependencies"><?php _e('Remove WooCommerce dependencies outside their domains', 'asap'); ?></label></th>
                <td><input type="checkbox" name="asap_wc_remove_dependencies" id="asap_wc_remove_dependencies" value="1" <?php checked('1', get_option('asap_wc_remove_dependencies', '0')); ?> /></td>
            </tr>
            <?php endif; ?>
        

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

<script>
    jQuery(document).ready(function($) {

        $(document).on('keypress', 'input:not(textarea)', function(e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

        $('#asap_calculate_cost').off('click').on('click', function(e) {
            e.preventDefault();
            var addImage = jQuery('#asap_create_image').prop('checked') ? '1' : '0';
            var keywords = $('#asap_keywords').val();
            var totalPosts = keywords.trim().split('\n').filter(function(line) { return line.trim() !== ''; }).length;
            
            $('#resultado-costo').html('');

            var spinner = $(this).next('.spinner'); 
                            
            spinner.css({
            'float': 'none',          
            'margin-top': '5px',     
            'margin-left': '10px'    
            });
                            
            spinner.show();

            $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_return_total_cost',
                addImage: addImage,
                totalPosts: totalPosts
            },
            success: function(response) {
                spinner.hide();
                $('#resultado-costo').html('Costo aproximado: $' + response);
            },
            error: function() {
                $('#resultado-costo').html('Error al calcular el costo.');
            }
            });



        });       

        // Mostrar/ocultar campos avanzados de optimización de imágenes
        function asapToggleImageOptPanel() {
            var on = jQuery('#asap_image_opt_enable').is(':checked');
            jQuery('.asap-image-opt-advanced')[on ? 'show' : 'hide']();
        }
        asapToggleImageOptPanel();
        jQuery('#asap_image_opt_enable').on('change', asapToggleImageOptPanel);


    });
</script>