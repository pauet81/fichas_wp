<?php if (current_user_can('manage_options')) : ?>

<?php

$post_types = get_post_types(array('public' => true), 'names');
$post_types = array_diff($post_types, array('attachment'));

// Procesar los datos del formulario al enviar
if (isset($_POST['submit']) && check_admin_referer('asap_stars_nonce_action', 'asap_stars_nonce')) {
    foreach ($post_types as $post_type) {
        update_option("asap_stars_enable_{$post_type}", isset($_POST["asap_stars_enable_{$post_type}"]) ? '1' : '0');
    }
    update_option('asap_stars_show_loop', isset($_POST['asap_stars_show_loop']) ? '1' : '0');
    update_option('asap_stars_position', sanitize_text_field($_POST['asap_stars_position']));

    echo '<div class="notice notice-success is-dismissible"><p><strong>Ajustes guardados</strong>.</p></div>';

    foreach ($post_types as $post_type) {
        $option_name = "asap_stars_enable_{$post_type}";
        $is_rating_enabled = get_option($option_name, '0');
        if ($is_rating_enabled) {
            break;
        }
    }

    //if (!get_option('asap_schema_rating') && $is_rating_enabled) {
    //    echo '<div class="notice notice-warning is-dismissible"><p><strong>Recomendación</strong>: Para maximizar los beneficios de la función de valoración de estrellas y mejorar tu CTR, te recomendamos activar la opción de datos estructurados de fragmento de opinión. <a style="margin-top: 8px;" href="'.admin_url('admin.php?page=asap-menu&tab=schema_settings').'">Ir a la pestaña de datos estructurados</a>.</p></div>';
    //}
}

$any_active = false;

foreach ($post_types as $post_type) {
    $label = ucfirst($post_type);
    if (get_option("asap_stars_enable_{$post_type}", '0') == '1') {
        $any_active = true;
    }
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
    <?php wp_nonce_field('asap_stars_nonce_action', 'asap_stars_nonce'); ?>
    <table class="form-table" id="asap-fieldset-one">
         <h2><?php _e('Star Rating', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This section allows you to enable a feature so that users can leave a star rating alongside their comments. These reviews will automatically be integrated into the sites listings.', 'asap'); ?></span></span></h2>
       
        <tbody>
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
            <tr>
                <th scope="row"><label for="asap_stars_enable_<?php echo $post_type; ?>"><?php _e("Enable voting on", 'asap'); ?> <?php echo $label; ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e("Activate the star rating system on", 'asap'); ?> <?php echo $label; ?></span></span></th>
                <td><input type="checkbox" name="asap_stars_enable_<?php echo $post_type; ?>" id="asap_stars_enable_<?php echo $post_type; ?>" value="1" <?php checked('1', get_option("asap_stars_enable_{$post_type}", '0')); ?> /></td>
            </tr>
            <?php endforeach; ?>
            <tr class="star-settings-row" <?php if (!$any_active) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_stars_show_loop"><?php _e('Show stars in loop', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to display voting stars in post listings, providing a professional and visually appealing design. This not only improves the appearance of your site, but also gives users a quick way to evaluate the content.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_stars_show_loop" id="asap_stars_show_loop" value="1" <?php checked('1', get_option('asap_stars_show_loop', '1')); ?> /></td>
            </tr>
            <tr class="star-settings-row" <?php if (!$any_active) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_stars_position"><?php _e('Position into content', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Sets the position of the voting stars.', 'asap'); ?></span></span></th>
                <td>
                    <select name="asap_stars_position" id="asap_stars_position">
                        <option value="top" <?php selected(get_option('asap_stars_position', 'top'), 'top'); ?>><?php _e('Show top', 'asap'); ?></option>
                        <option value="bottom" <?php selected(get_option('asap_stars_position', 'top'), 'bottom'); ?>><?php _e('Show bottom', 'asap'); ?></option>
                    </select>
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
        function toggleAdditionalSettings() {
            let anyChecked = false;

            <?php foreach ($post_types as $post_type): ?>
                if ($('#asap_stars_enable_<?php echo $post_type; ?>').is(':checked')) {
                    anyChecked = true;
                }
            <?php endforeach; ?>

            if (anyChecked) {
                $('.star-settings-row').show();
            } else {
                $('.star-settings-row').hide();
            }
        }

        // Check the initial state on page load
        toggleAdditionalSettings();

        // Add change event listeners to all post type checkboxes
        <?php foreach ($post_types as $post_type): ?>
            $('#asap_stars_enable_<?php echo $post_type; ?>').change(function() {
                toggleAdditionalSettings();
            });
        <?php endforeach; ?>
    });
</script>
<?php else: ?>
<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p><?php _e('It looks like you do not have sufficient permissions to view this page.', 'asap'); ?></p></div>
<?php endif; ?>
