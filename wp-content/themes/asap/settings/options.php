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



<?php if (current_user_can('manage_options')) : ?>

<?php

// Verifica si el formulario ha sido enviado y si el nonce es válido
if (isset($_POST['submit']) && check_admin_referer('devlog_nonce_action', 'devlog_nonce_field')) {
    // Guardar las opciones configuradas en el formulario
    update_option('asap_delete_guten_css', isset($_POST['asap_delete_guten_css']) ? '1' : '0');
    update_option('asap_classic_editor_widgets', isset($_POST['asap_classic_editor_widgets']) ? '1' : '0');
    update_option('asap_enable_awesome', isset($_POST['asap_enable_awesome']) ? '1' : '0');
    update_option('asap_remove_comments_links', isset($_POST['asap_remove_comments_links']) ? '1' : '0');
    update_option('asap_show_comments_url', isset($_POST['asap_show_comments_url']) ? '1' : '0');
    update_option('asap_redirect_404_home', isset($_POST['asap_redirect_404_home']) ? '1' : '0');
    update_option('asap_blocks_enable', isset($_POST['asap_blocks_enable']) ? '1' : '0');

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

        <h2><?php _e('General Options', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('General Options from Asap Theme.', 'asap'); ?></span></span></h2>

        <tbody>
            <tr>
                <th scope="row"><label for="asap_delete_guten_css"><?php _e('Activate Classic Editor', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to enable the classic WordPress Editor instead of the Gutenberg Block Editor, without the need to install any additional plugin.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_delete_guten_css" id="asap_delete_guten_css" value="1" <?php checked('1', get_option('asap_delete_guten_css', '0')); ?> /></td>
            </tr>
            <tr id="classic-editor-widgets-row" <?php if (get_option('asap_delete_guten_css', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_classic_editor_widgets"><?php _e('Activate Classic Editor on Widgets', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to activate the classic WordPress Editor exclusively for widgets. This makes it easy to manipulate without needing to change the post editor, allowing you to continue using Gutenberg for posts.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_classic_editor_widgets" id="asap_classic_editor_widgets" value="1" <?php checked('1', get_option('asap_classic_editor_widgets', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_enable_awesome"><?php _e('Activate Awesome library', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to enable the Font Awesome icon library, providing access to a wide range of high-quality icons that you can use in your posts, pages and widgets to improve the appearance and visual functionality without requiring additional configurations.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_enable_awesome" id="asap_enable_awesome" value="1" <?php checked('1', get_option('asap_enable_awesome', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_remove_comments_links"><?php _e('Remove links in comments', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to automatically remove all href links from comments on your WordPress site. This helps prevent spam and improve the security of your site, keeping the comments section clean and secure without affecting the content of the comment.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_remove_comments_links" id="asap_remove_comments_links" value="1" <?php checked('1', get_option('asap_remove_comments_links', '1')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_show_comments_url"><?php _e('Show URL in comments', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to enable the URL field in the comments section of your WordPress site. This allows users to enter your website along with their comments, encouraging interaction and allowing them to share more information about themselves. It is disabled by default to avoid spam.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_show_comments_url" id="asap_show_comments_url" value="1" <?php checked('1', get_option('asap_show_comments_url', '0')); ?> /></td>
            </tr>
            <tr>
                <th scope="row"><label for="asap_redirect_404_home"><?php _e('Redirect error 404 to home', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('This option allows you to automatically redirect all 404 error pages to the home page of your WordPress site. This improves the user experience by ensuring they dont get stuck on error pages and helps keep them browsing your site.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_redirect_404_home" id="asap_redirect_404_home" value="1" <?php checked('1', get_option('asap_redirect_404_home', '0')); ?> /></td>
            </tr>
            <tr id="asap-blocks-row" <?php if (get_option('asap_delete_guten_css', '0')) echo 'style="display:none;"'; ?>>
                <th scope="row"><label for="asap_blocks_enable"><?php _e('Mostrar bloques nativos en Gutenberg', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Activa esta opción para mostrar en el editor Gutenberg todos los bloques nativos de Asap Theme, como Hero completo, tabla comparativa, tabla de precios y más. Si no los necesitas, puedes desactivarla para evitar que se carguen en el backend y mejorar el rendimiento.', 'asap'); ?></span></span></th>
                <td><input type="checkbox" name="asap_blocks_enable" id="asap_blocks_enable" value="1" <?php checked('1', get_option('asap_blocks_enable', '1')); ?> /></td>
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
        function toggleClassicEditorWidgets() {
            if ($('#asap_delete_guten_css').is(':checked')) {
                $('#classic-editor-widgets-row').hide();
                $('#asap-blocks-row').hide();
            } else {
                $('#classic-editor-widgets-row').show();
                $('#asap-blocks-row').show();
            }
        }

        // Add change event listener to the checkbox
        $('#asap_delete_guten_css').change(function() {
            toggleClassicEditorWidgets();
        });
    });
</script>
<?php else: ?>

<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p>Parece que no tienes los permisos suficientes para ver esta página.</p></div>

<?php endif; ?>