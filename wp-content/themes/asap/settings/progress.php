<?php if ( current_user_can( 'manage_options' ) ) : ?>

<?php
$post_types = get_post_types( [ 'public' => true ], 'names' );
$post_types = array_diff( $post_types, [ 'attachment' ] );

if ( isset( $_POST['submit'] ) && check_admin_referer( 'asap_progress_nonce_action', 'asap_progress_nonce' ) ) {
    update_option( 'asap_progress_enabled',               isset( $_POST['asap_progress_enabled'] )               ? '1' : '0' );
    foreach ( $post_types as $post_type ) {
        update_option( "asap_progress_{$post_type}",      isset( $_POST["asap_progress_{$post_type}"] )          ? '1' : '0' );
    }
    update_option( 'asap_progress_position',             sanitize_text_field( $_POST['asap_progress_position'] ) );
    update_option( 'asap_progress_height',               absint( $_POST['asap_progress_height'] ) );
    update_option( 'asap_progress_color',                sanitize_hex_color( $_POST['asap_progress_color'] ) );
    update_option( 'asap_progress_show_mobile',          isset( $_POST['asap_progress_show_mobile'] )           ? '1' : '0' );
    update_option( 'asap_progress_show_percentage',      isset( $_POST['asap_progress_show_percentage'] )       ? '1' : '0' );

    echo '<div class="notice notice-success is-dismissible"><p><strong>Ajustes guardados</strong>.</p></div>';
}
?>

<style>
    .wrapper-asap-options input[type="number"]{min-width:60px;margin:0;padding:4px 6px!important;}
    .wrapper-asap-options input[type="color"]{width:90px;height:38px;padding:0;border:1px solid #8c8f94;border-radius:4px;}
    .asap-options h2{display:flex;align-items:center;}
    .asap-options h2 span{background:#202225;color:#fff;margin-left:6px;}
</style>

<form method="post" action="">
    <?php wp_nonce_field( 'asap_progress_nonce_action', 'asap_progress_nonce' ); ?>
    <table class="form-table" id="asap-fieldset-reading-progress">
        <h2><?php _e( 'Barra de progreso de lectura', 'asap' ); ?>
            <span class="asap-tooltip">?
                <span class="tooltiptext">
                    <?php _e( 'Muestra una barra de progreso basada en el scroll del usuario dentro de .the-content.', 'asap' ); ?>
                </span>
            </span>
        </h2>

        <tbody>
            <tr>
                <th scope="row"><label for="asap_progress_enabled"><?php _e( 'Activar barra de progreso', 'asap' ); ?></label></th>
                <td><input type="checkbox" id="asap_progress_enabled" name="asap_progress_enabled" value="1" <?php checked( '1', get_option( 'asap_progress_enabled', '0' ) ); ?>></td>
            </tr>

            <?php foreach ( $post_types as $pt ) :
                $label = $pt === 'post' ? 'Entrada' : ( $pt === 'page' ? 'Página' : ucfirst( $pt ) ); ?>
                <tr class="progress-settings-row" <?php if ( ! get_option( 'asap_progress_enabled' ) ) echo 'style="display:none"'; ?>>
                    <th scope="row"><label for="asap_progress_<?php echo esc_attr( $pt ); ?>"><?php _e( 'Mostrar en', 'asap' ); ?> <?php echo esc_html( $label ); ?></label></th>
                    <td><input type="checkbox" id="asap_progress_<?php echo esc_attr( $pt ); ?>" name="asap_progress_<?php echo esc_attr( $pt ); ?>" value="1" <?php checked( '1', get_option( "asap_progress_{$pt}", '0' ) ); ?>></td>
                </tr>
            <?php endforeach; ?>

            <tr class="progress-settings-row" <?php if ( ! get_option( 'asap_progress_enabled' ) ) echo 'style="display:none"'; ?>>
                <th scope="row"><label for="asap_progress_position"><?php _e( 'Posición de la barra', 'asap' ); ?></label></th>
                <td>
                    <select id="asap_progress_position" name="asap_progress_position">
                        <option value="top"    <?php selected( 'top',    get_option( 'asap_progress_position', 'top' ) ); ?>><?php _e( 'Arriba (top)',    'asap' ); ?></option>
                        <option value="bottom" <?php selected( 'bottom', get_option( 'asap_progress_position', 'top' ) ); ?>><?php _e( 'Abajo (bottom)', 'asap' ); ?></option>
                    </select>
                </td>
            </tr>

            <tr class="progress-settings-row" <?php if ( ! get_option( 'asap_progress_enabled' ) ) echo 'style="display:none"'; ?>>
                <th scope="row"><label for="asap_progress_height"><?php _e( 'Alto de la barra (px)', 'asap' ); ?></label></th>
                <td><input type="number" id="asap_progress_height" name="asap_progress_height" value="<?php echo esc_attr( get_option( 'asap_progress_height', 4 ) ); ?>" min="1" max="20"></td>
            </tr>

            <tr class="progress-settings-row" <?php if ( ! get_option( 'asap_progress_enabled' ) ) echo 'style="display:none"'; ?>>
                <th scope="row"><label for="asap_progress_color"><?php _e( 'Color', 'asap' ); ?></label></th>
                <td><input type="color" id="asap_progress_color" name="asap_progress_color" value="<?php echo esc_attr( get_option( 'asap_progress_color', '#1e73be' ) ); ?>"></td>
            </tr>

            <tr class="progress-settings-row" <?php if ( ! get_option( 'asap_progress_enabled' ) ) echo 'style="display:none"'; ?>>
                <th scope="row"><label for="asap_progress_show_mobile"><?php _e( 'Mostrar en móvil', 'asap' ); ?></label></th>
                <td><input type="checkbox" id="asap_progress_show_mobile" name="asap_progress_show_mobile" value="1" <?php checked( '1', get_option( 'asap_progress_show_mobile', '1' ) ); ?>></td>
            </tr>

            <tr class="progress-settings-row" <?php if ( ! get_option( 'asap_progress_enabled' ) ) echo 'style="display:none"'; ?>>
                <th scope="row"><label for="asap_progress_show_percentage"><?php _e( 'Mostrar porcentaje', 'asap' ); ?></label></th>
                <td><input type="checkbox" id="asap_progress_show_percentage" name="asap_progress_show_percentage" value="1" <?php checked( '1', get_option( 'asap_progress_show_percentage', '0' ) ); ?>></td>
            </tr>

            <tr><th scope="row"><?php submit_button(); ?></th><td></td></tr>
        </tbody>
    </table>
</form>

<script>
    jQuery(function($){
        function toggleRows(){
            $('#asap_progress_enabled').is(':checked') ? $('.progress-settings-row').show() : $('.progress-settings-row').hide();
        }
        $('#asap_progress_enabled').on('change', toggleRows);
        toggleRows();
    });
</script>

<?php else : ?>
    <div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px"><p><?php _e( 'Parece que no tienes permisos suficientes para ver esta página.', 'asap' ); ?></p></div>
<?php endif; ?>
