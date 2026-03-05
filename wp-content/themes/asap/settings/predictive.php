<?php if (current_user_can('manage_options')) : ?>

<?php

$post_types = get_post_types(array('public' => true), 'names');
$post_types = array_diff($post_types, array('attachment'));

// Procesar los datos del formulario al enviar
if (isset($_POST['submit']) && check_admin_referer('asap_predictive_nonce_action', 'asap_predictive_nonce')) {
    // Guardar las nuevas opciones
    update_option('asap_predictive_enabled', isset($_POST['asap_predictive_enabled']) ? '1' : '0');
    foreach ($post_types as $post_type) {
        update_option("asap_predictive_{$post_type}", isset($_POST["asap_predictive_{$post_type}"]) ? '1' : '0');
    }
    update_option('asap_predictive_results_count', intval($_POST['asap_predictive_results_count']));

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

<form method="post" action="">
    <?php wp_nonce_field('asap_predictive_nonce_action', 'asap_predictive_nonce'); ?>
    <table class="form-table" id="asap-fieldset-one">
        <h2><?php _e('Predictive Search', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This function allows you to activate predictive search and also select which types of content the search will be performed on.', 'asap'); ?></span></span></h2>
       
       <?php if (get_option('asap_deactivate_jquery')) {
            echo '<div class="notice notice-warning"><p><strong>Atención</strong>: Vemos que está activa la opción de rendimiento de <strong>Desactivar librería jQuery</strong>. Para que la búsqueda predictiva funcione, es necesario desactivar esta opción.</p></div>';

       }
       ?>

        <tbody>
            <tr>
                <th scope="row"><label for="asap_predictive_enabled"><?php _e('Enable predictive search', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Esta opción activa la búsqueda predictiva en desktop.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_predictive_enabled" id="asap_predictive_enabled" value="1" <?php checked('1', get_option('asap_predictive_enabled', '0')); ?> /></td>
            </tr>
            <?php
            foreach ($post_types as $post_type):
                if ($post_type == 'post') {
                    $label = 'Entrada';
                } elseif ($post_type == 'page') {
                    $label = 'Página';
                } else {
                    $label = ucfirst($post_type);
                } 
            ?>
            <tr class="predictive-settings-row" <?php if (!get_option('asap_predictive_enabled')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_predictive_<?php echo $post_type; ?>"><?php _e("Search on", 'asap'); ?> <?php echo $label; ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Esta opción permite que solo se muestre el tipo de contenido seleccionado en los resultados de búsqueda:', 'asap'); ?> <?php echo $label; ?></label></span></span></th>
                <td><input type="checkbox" name="asap_predictive_<?php echo $post_type; ?>" id="asap_predictive_<?php echo $post_type; ?>" value="1" <?php checked('1', get_option("asap_predictive_{$post_type}", '0')); ?> /></td>
            </tr>
            <?php endforeach; ?>
            <tr class="predictive-settings-row" <?php if (!get_option('asap_predictive_enabled')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_predictive_results_count"><?php _e('Number of results', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Esta opción le permite seleccionar la cantidad de resultados que se mostrarán en la búsqueda predictiva.', 'asap'); ?></span></span></th>
                <td><input type="number" name="asap_predictive_results_count" id="asap_predictive_results_count" value="<?php echo esc_attr(get_option('asap_predictive_results_count', '5')); ?>" min="1" max="20" /></td>
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
            if ($('#asap_predictive_enabled').is(':checked')) {
                $('.predictive-settings-row').show();
            } else {
                $('.predictive-settings-row').hide();
            }
        }

        // Add change event listener to the checkbox
        $('#asap_predictive_enabled').change(function() {
            toggleCookieSettings();
        });
    });
</script>
<?php else: ?>
<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p><?php _e('Parece que no tienes permisos suficientes para ver esta página.', 'asap'); ?></p></div>
<?php endif; ?>
