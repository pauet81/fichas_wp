<?php
/**
 * Image Processor Helper
 * 
 * Funciones auxiliares para procesar y guardar imágenes en la biblioteca de medios.
 * 
 * @package ASAP_Theme
 * @subpackage IA\Helpers
 */

if (!defined('ABSPATH')) exit;

class ASAP_IA_Helpers_Image_Processor {
    
    /**
     * Guardar imagen base64 en biblioteca de medios
     */
    public static function save_base64_image_to_media($b64, $filename, $post_id = 0, $format = 'png', $watermark = '', $alt = '') {
        $data = base64_decode($b64);
        if ($data === false) return 0;
        
        $tmp = wp_tempnam($filename);
        if (!$tmp) return 0;
        
        file_put_contents($tmp, $data);

        $processed = self::process_image_file($tmp, $format, $watermark);
        if (is_wp_error($processed)) {
            @unlink($tmp);
            return 0;
        }

        $file_array = [
            'name' => self::ensure_ext($filename, $format),
            'type' => mime_content_type($processed),
            'tmp_name' => $processed,
            'error' => 0,
            'size' => filesize($processed),
        ];
        
        $results = wp_handle_sideload($file_array, ['test_form' => false]);
        @unlink($processed);
        
        if (!empty($results['error'])) return 0;

        $attachment = [
            'post_mime_type' => $results['type'],
            'post_title' => sanitize_file_name(pathinfo($results['file'], PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        
        $attach_id = wp_insert_attachment($attachment, $results['file'], $post_id);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $results['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        if ($alt) {
            update_post_meta($attach_id, '_wp_attachment_image_alt', sanitize_text_field($alt));
        }
        
        return $attach_id;
    }
    
    /**
     * Descargar y guardar imagen desde URL
     */
    public static function sideload_image_from_url($url, $post_id = 0, $format = 'png', $watermark = '', $alt = '') {
        $logger = new ASAP_IA_Database_Generation_Logger();
        $session_id = ASAP_IA_Database_Generation_Logger::generate_session_id();
        
        // Validar URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $logger->error($session_id, 'image', "URL inválida para sideload de imagen", ['url' => $url], $post_id);
            return 0;
        }
        
        // Descargar imagen desde URL
        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            $logger->error($session_id, 'image', "Error descargando imagen: " . $tmp->get_error_message(), ['url' => $url], $post_id);
            return 0;
        }
        
        // Procesar imagen (marca de agua, formato, etc)
        $processed = self::process_image_file($tmp, $format, $watermark);
        if (is_wp_error($processed)) {
            $logger->error($session_id, 'image', "Error procesando imagen: " . $processed->get_error_message(), ['url' => $url], $post_id);
            @unlink($tmp);
            return 0;
        }
        
        // Preparar archivo para sideload
        $file_array = [
            'name' => basename(parse_url($url, PHP_URL_PATH)) ?: ('image-' . time() . '.' . $format),
            'type' => mime_content_type($processed),
            'tmp_name' => $processed,
            'error' => 0,
            'size' => filesize($processed),
        ];
        
        // Subir a biblioteca de medios
        $results = wp_handle_sideload($file_array, ['test_form' => false]);
        @unlink($processed);
        
        if (!empty($results['error'])) {
            $logger->error($session_id, 'image', "Error en wp_handle_sideload: " . $results['error'], ['url' => $url], $post_id);
            return 0;
        }

        // Crear attachment en WordPress
        $attachment = [
            'post_mime_type' => $results['type'],
            'post_title' => sanitize_file_name(pathinfo($results['file'], PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        ];
        
        $attach_id = wp_insert_attachment($attachment, $results['file'], $post_id);
        
        if (!$attach_id || is_wp_error($attach_id)) {
            $error_msg = is_wp_error($attach_id) ? $attach_id->get_error_message() : 'ID inválido';
            $logger->error($session_id, 'image', "Error insertando attachment: " . $error_msg, ['url' => $url], $post_id);
            return 0;
        }
        
        // Generar metadatos de imagen
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $results['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        // Agregar texto alternativo si se proporciona
        if ($alt) {
            update_post_meta($attach_id, '_wp_attachment_image_alt', sanitize_text_field($alt));
        }
        
        $logger->success($session_id, 'image', "Imagen subida exitosamente desde URL", [
            'attachment_id' => $attach_id,
            'url' => $url,
            'format' => $format
        ], $post_id);
        
        return $attach_id;
    }
    
    /**
     * Procesar archivo de imagen (convertir formato y aplicar marca de agua)
     */
    public static function process_image_file($file, $format = 'png', $watermark = '') {
        $format = in_array($format, ['png', 'jpg', 'jpeg', 'webp'], true) ? $format : 'png';

        $editor = wp_get_image_editor($file);
        if (is_wp_error($editor)) return $editor;

        if (!empty($watermark)) {
            self::apply_text_watermark($editor, $watermark);
        }

        $dest = $editor->generate_filename(null, null, $format === 'jpg' ? 'jpeg' : $format);
        $save = $editor->save($dest, $format === 'jpg' ? 'image/jpeg' : ('image/' . $format));
        
        if (is_wp_error($save)) return $save;
        
        @unlink($file);
        return $dest;
    }
    
    /**
     * Aplicar marca de agua de texto a una imagen
     */
    public static function apply_text_watermark($editor, $text) {
        if (method_exists($editor, 'get_size')) {
            $size = $editor->get_size();
            $width = $size['width'];
            $height = $size['height'];
            
            $font_size = max(12, min(48, (int)($width * 0.03)));
            $padding_x = (int)($width * 0.02);
            $padding_y = (int)($height * 0.02);
            
            // Posición: esquina inferior derecha
            $x = $width - $padding_x;
            $y = $height - $padding_y;
            
            if (method_exists($editor, 'text')) {
                $editor->text($text, $x, $y, [
                    'color' => 'ffffff',
                    'opacity' => 70,
                    'font_size' => $font_size,
                    'align' => 'right',
                    'valign' => 'bottom'
                ]);
            }
        }
    }
    
    /**
     * Asegurar que el archivo tenga la extensión correcta
     */
    private static function ensure_ext($filename, $format) {
        $info = pathinfo($filename);
        $name = $info['filename'];
        $ext = $format === 'jpg' ? 'jpeg' : $format;
        return $name . '.' . $ext;
    }
}




