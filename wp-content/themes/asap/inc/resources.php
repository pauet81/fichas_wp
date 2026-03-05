<?php

function asap_custom_get_meta_fields() {

    global $post;

    if (!is_admin() || !in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')) || is_customize_preview()) {
        return array(); // Si no estamos en el backend, no se está editando una entrada/página, o estamos en la vista de personalización, salimos de la función.
    }

    // Verificar si el objeto $post está definido y es válido
    if (!isset($post) || !is_object($post) || $post->post_status !== 'publish') {
        return array();
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    @$doc->loadHTMLFile(get_permalink($_GET["post"]) . "?bypass_unload=yes");
    $xpath = new DOMXpath($doc);
    libxml_clear_errors();
    libxml_use_internal_errors(false);

    $assets_arr = array(
        array(
            "values" => $xpath->query("//link[@rel='stylesheet']"),
            "name" => "style",
            "attribute" => "href",
            "file" => "css"
        ),
        array(
            "values" => $xpath->query("//script[@type='text/javascript']"),
            "name" => "script",
            "attribute" => "src",
            "file" => "js"
        )
    );

    $first_value = true;
    $fields = [];
    $count = 0;
    foreach ($assets_arr as $asset) {
        $first_value = true;
        if ($asset["values"]->length > 0) {
            foreach ($asset["values"] as $single_value) {
                $url = false;
                $id = false;
                foreach ($single_value->attributes as $attribute) {
                    if ($attribute->name === $asset["attribute"]) {
                        $url = $attribute->nodeValue;
                    } elseif ($attribute->name === "id") {
                        $id = $attribute->nodeValue;
                    }
                }
                if ($url && $id) {
                    $fields[] = array(
                        'id' => $asset["file"] . "_unload-" . hash('ripemd160', $url),
                        'label' => ($first_value) ? "Desactivar " . $asset["file"] . " " . $asset["name"] : "",
                        'sublabel' => $url,
                        'value' => $id,
                        'type' => 'multiple',
                        'default' => '0',
                    );
                    $first_value = false;
                    $count++;
                }
            }
        }
    }
    return $fields;
}

function asap_custom_get_meta_screens() {
    $screens = array(
        'post',
        'page',
    );
    return $screens;
}

add_action('add_meta_boxes', 'asap_custom_add_meta_boxes', 30);

function asap_custom_add_meta_boxes() {
    $screens = asap_custom_get_meta_screens();
    foreach ($screens as $screen) {
        add_meta_box(
            'option-page',
            __('ASAP − Gestión de recursos JS / CSS', 'asap'),
            'asap_custom_add_meta_box_callback',
            $screen,
            'normal',
            'high',
            array('arg' => 'value')
        );
    }
}

function asap_custom_add_meta_box_callback($post) {
    wp_nonce_field('option_page_data', 'option_page_nonce');
    asap_custom_generate_fields($post);
}

function asap_custom_generate_fields($post) {
    $output = '';
    $fields = asap_custom_get_meta_fields();
    foreach ($fields as $field) {
        if ($field['value'] != 'asap-google-fonts-css') {
            if ($field['type'] !== 'multiple') {
                $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
            } else {
                $label = $field['label'];
            }
            $db_value = get_post_meta($post->ID, 'option_page_' . $field['id'], true);

            if ($db_value === '') {
                if (isset($field['default'])) {
                    $db_value = $field['default'];
                }
            }

            switch ($field['type']) {
                case 'heading':
                    $input = false;
                    break;
                case 'separator':
                    $input = '<hr>';
                    break;
                case 'multiple':
                    $input = sprintf(
                        "<input %s id='%s' name='%s' type='checkbox' value='%s'> <label for='%s'> <b>%s</b><br>%s</label>",
                        $db_value === $field['value'] ? 'checked' : '',
                        $field['id'],
                        $field['id'],
                        $field['value'],
                        $field['id'],
                        $field['value'],
                        $field['sublabel']
                    );
                    break;
                default:
                    $input = sprintf(
                        '<input %s id="%s" name="%s" type="%s" value="%s">',
                        $field['type'] !== 'color' ? 'class="regular-text"' : '',
                        $field['id'],
                        $field['id'],
                        $field['type'],
                        $db_value
                    );
            }

            $output .= asap_custom_row_format($label, $input);
        }
    }
    echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
}

function asap_custom_row_format($label, $input) {
    return ($input) ?
        sprintf('<tr><th scope="row">%s</th><td>%s</td></tr>', $label, $input) :
        "<tr><th colspan=\"2\" scope=\"row\"><h3>$label</h3></th></tr>";
}

function asap_custom_save_post($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    $fields = asap_custom_get_meta_fields();
    foreach ($fields as $field) {
        if (isset($_POST[$field['id']])) {
            update_post_meta($post_id, 'option_page_' . $field['id'], $_POST[$field['id']]);
        } else if ($field['type'] === 'checkbox' || $field['type'] === 'multiple') {
            update_post_meta($post_id, 'option_page_' . $field['id'], '0');
        }
    }
}

add_action('save_post', 'asap_custom_save_post');

function asap_custom_unload_js() {
    if (is_admin() || (isset($_GET["bypass_unload"]) && $_GET["bypass_unload"] === "yes")) {
        return;
    }
    $meta = get_post_meta(get_the_ID());
    if (is_array($meta) && (is_page() || is_single())) {
        foreach ($meta as $key => $val) {
            $query_js = "option_page_js_unload";
            if (substr($key, 0, strlen($query_js)) === $query_js) {
                if ($val) {
                    wp_deregister_script(substr($val[0], 0, -3));
                    wp_dequeue_script(substr($val[0], 0, -3));
                }
            }
        }
    }
}

function asap_custom_unload_css() {
    if (is_admin() || (isset($_GET["bypass_unload"]) && $_GET["bypass_unload"] === "yes")) {
        return;
    }
    $meta = get_post_meta(get_the_ID());
    if (is_array($meta) && (is_page() || is_single())) {
        foreach ($meta as $key => $val) {
            $query_css = "option_page_css_unload";
            if (substr($key, 0, strlen($query_css)) === $query_css) {
                if ($val) {
                    wp_deregister_style(substr($val[0], 0, -4));
                    wp_dequeue_style(substr($val[0], 0, -4));
                }
            }
        }
    }
}

add_action('wp_print_scripts', 'asap_custom_unload_js', 100);
add_action('wp_print_styles', 'asap_custom_unload_css', 100);

?>
