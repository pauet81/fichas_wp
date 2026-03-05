<?php
/**
 * Bloque FAQ con Schema para Asap Theme
 */

add_action( 'enqueue_block_editor_assets', 'asap_theme_enqueue_block_editor_assets' );
function asap_theme_enqueue_block_editor_assets() {
    // Verifica que la opción esté activa. Si la opción no existe, se asume false.
    if ( get_option( 'asap_blocks_enable', true ) &&  !get_option('asap_delete_guten_css', false)) {
        $block_js_url  = get_template_directory_uri() . '/inc/gutenberg/assets/js/blocks.js';
        $block_js_path = get_template_directory() . '/inc/gutenberg/assets/js/blocks.js';

        wp_enqueue_script(
            'asap-theme-faq-block-editor',
            $block_js_url,
            array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
            filemtime( $block_js_path ),
            true
        );
    }
}
