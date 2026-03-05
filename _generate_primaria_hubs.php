<?php
/**
 * Generate missing Primaria hubs (4º-6º) and subject pages with ACF content.
 * Run from WSL: php /home/pauca/proyectos/fichas/wordpress/_generate_primaria_hubs.php
 */

require_once __DIR__ . '/wp-load.php';

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from CLI.\n";
    exit(1);
}

$primaria = get_page_by_path('primaria', OBJECT, 'page');
if (!$primaria) {
    echo "Primaria page not found.\n";
    exit(1);
}

$acf_keys = array(
    'hub_h1' => 'field_hub_h1',
    'hub_subtitulo' => 'field_hub_subtitulo',
    'hub_intro_h2' => 'field_hub_intro_h2',
    'hub_intro_content' => 'field_hub_intro_content',
    'hub_seo_h2' => 'field_hub_seo_h2',
    'hub_seo_content' => 'field_hub_seo_content',
    'hub_faq_h2' => 'field_hub_faq_h2',
    'hub_faq_items' => 'field_hub_faq_items',
    'hub_faq_pregunta' => 'field_hub_faq_pregunta',
    'hub_faq_respuesta' => 'field_hub_faq_respuesta',
);

function upsert_page($path, $args) {
    $existing = get_page_by_path($path, OBJECT, 'page');
    if ($existing) {
        $args['ID'] = $existing->ID;
        wp_update_post($args);
        return $existing->ID;
    }
    return wp_insert_post($args, true);
}

function set_acf_meta($post_id, $key, $value, $field_key) {
    update_post_meta($post_id, $key, $value);
    update_post_meta($post_id, '_' . $key, $field_key);
}

function set_faq($post_id, $items, $acf_keys) {
    set_acf_meta($post_id, 'hub_faq_items', (string)count($items), $acf_keys['hub_faq_items']);
    foreach ($items as $i => $item) {
        $q_key = "hub_faq_items_{$i}_pregunta";
        $a_key = "hub_faq_items_{$i}_respuesta";
        update_post_meta($post_id, $q_key, $item['q']);
        update_post_meta($post_id, $a_key, $item['a']);
        update_post_meta($post_id, '_' . $q_key, $acf_keys['hub_faq_pregunta']);
        update_post_meta($post_id, '_' . $a_key, $acf_keys['hub_faq_respuesta']);
    }
}

$subjects = array(
    array('slug' => 'matematicas', 'title' => 'Matemáticas'),
    array('slug' => 'lengua', 'title' => 'Lenguaje'),
    array('slug' => 'conocimiento-medio', 'title' => 'Conocimiento del Medio'),
    array('slug' => 'ingles', 'title' => 'Inglés'),
);

$course_notes = array(
    4 => 'En 4º de Primaria se consolidan competencias clave.',
    5 => 'En 5º de Primaria se amplían contenidos y se gana autonomía.',
    6 => 'En 6º de Primaria se prepara la transición a secundaria.',
);

foreach (array(4, 5, 6) as $n) {
    $course_slug = $n . '-primaria';
    $course_title = $n . 'º Primaria';
    $course_path = 'primaria/' . $course_slug;

    $course_id = upsert_page($course_path, array(
        'post_author' => 1,
        'post_title' => $course_title,
        'post_name' => $course_slug,
        'post_parent' => $primaria->ID,
        'post_status' => 'publish',
        'post_type' => 'page',
        'menu_order' => $n,
        'post_content' => "Fichas educativas de {$n}º de Primaria. Recursos imprimibles de matemáticas, lenguaje, conocimiento del medio e inglés.",
        'comment_status' => 'open',
        'ping_status' => 'open',
    ));

    if (is_wp_error($course_id)) {
        echo "Error creating course {$course_title}: " . $course_id->get_error_message() . "\n";
        continue;
    }

    update_post_meta($course_id, '_wp_page_template', 'page-hub-curso.php');

    set_acf_meta($course_id, 'hub_h1', "Fichas Interactivas de {$n}º de Primaria", $acf_keys['hub_h1']);
    set_acf_meta($course_id, 'hub_subtitulo', "Actividades por asignaturas para repasar {$n}º de Primaria con ejercicios online y autocorrección.", $acf_keys['hub_subtitulo']);
    set_acf_meta($course_id, 'hub_intro_h2', "Refuerzo de {$n}º de Primaria con fichas interactivas", $acf_keys['hub_intro_h2']);
    set_acf_meta(
        $course_id,
        'hub_intro_content',
        "<p>Aquí tienes una selección de <strong>fichas interactivas de {$n}º de Primaria</strong> organizadas por asignaturas. Son ideales para practicar matemáticas, lenguaje, inglés y conocimiento del medio con corrección inmediata.</p>",
        $acf_keys['hub_intro_content']
    );
    set_acf_meta($course_id, 'hub_seo_h2', "Fichas online para {$n}º de Primaria: práctica diaria", $acf_keys['hub_seo_h2']);
    set_acf_meta(
        $course_id,
        'hub_seo_content',
        "<p>{$course_notes[$n]} Nuestras <strong>fichas online</strong> ayudan a reforzar contenidos con ejercicios claros y autocorregibles.</p>",
        $acf_keys['hub_seo_content']
    );
    set_acf_meta($course_id, 'hub_faq_h2', 'Preguntas frecuentes', $acf_keys['hub_faq_h2']);
    set_faq($course_id, array(
        array('q' => "¿Qué asignaturas incluye {$n}º de Primaria?", 'a' => 'Incluye matemáticas, lenguaje, inglés y conocimiento del medio, organizadas por temas.'),
        array('q' => '¿Las fichas son gratuitas?', 'a' => 'Sí, todo el contenido es gratuito y sin registro.'),
        array('q' => '¿Cómo funcionan las fichas interactivas?', 'a' => 'Cada ficha se completa online y se corrige al instante.'),
        array('q' => '¿Puedo usarlas desde casa?', 'a' => 'Sí, están pensadas para usar en casa o en el aula.'),
    ), $acf_keys);

    foreach ($subjects as $i => $subj) {
        $subj_path = $course_path . '/' . $subj['slug'];
        $subj_id = upsert_page($subj_path, array(
            'post_author' => 1,
            'post_title' => $subj['title'],
            'post_name' => $subj['slug'],
            'post_parent' => $course_id,
            'post_status' => 'publish',
            'post_type' => 'page',
            'menu_order' => $i + 1,
            'post_content' => "Fichas de {$subj['title']} para {$n}º de Primaria.",
            'comment_status' => 'open',
            'ping_status' => 'open',
        ));

        if (is_wp_error($subj_id)) {
            echo "Error creating subject {$subj['title']} {$n}º: " . $subj_id->get_error_message() . "\n";
            continue;
        }

        update_post_meta($subj_id, '_wp_page_template', 'page-asignatura.php');

        set_acf_meta($subj_id, 'hub_h1', "Fichas de {$subj['title']} para {$n}º de Primaria", $acf_keys['hub_h1']);
        set_acf_meta($subj_id, 'hub_subtitulo', "Ejercicios interactivos de {$subj['title']} para repasar contenidos de {$n}º de Primaria con corrección inmediata.", $acf_keys['hub_subtitulo']);

        $intro_h2 = "Recursos de {$subj['title']} en {$n}º de Primaria";
        $intro_content = "<p>Estas <strong>fichas interactivas de {$subj['title']}</strong> ayudan a practicar contenidos clave de {$n}º de Primaria con autocorrección y ejemplos sencillos.</p>";
        $seo_content = "<p>Nuestras <strong>fichas online de {$subj['title']}</strong> combinan ejercicios autocorregibles y práctica progresiva para afianzar el aprendizaje.</p>";

        if ($subj['slug'] === 'lengua') {
            $intro_h2 = "Lenguaje en {$n}º de Primaria: lectura y expresión";
            $intro_content = "<p>Estas <strong>fichas interactivas de Lenguaje</strong> ayudan a mejorar comprensión lectora, ortografía y expresión escrita en {$n}º de Primaria.</p>";
        } elseif ($subj['slug'] === 'conocimiento-medio') {
            $intro_h2 = "Conocimiento del Medio en {$n}º de Primaria: descubre y aprende";
            $intro_content = "<p>Estas <strong>fichas interactivas de Conocimiento del Medio</strong> ayudan a reforzar ciencias naturales y sociales en {$n}º de Primaria.</p>";
            $seo_content = "<p>Nuestras <strong>fichas online de Conocimiento del Medio</strong> proponen actividades claras y autocorregibles para afianzar conceptos.</p>";
        } elseif ($subj['slug'] === 'ingles') {
            $intro_h2 = "Inglés en {$n}º de Primaria: vocabulario y gramática";
            $intro_content = "<p>Estas <strong>fichas interactivas de Inglés</strong> ayudan a practicar vocabulario, gramática y comprensión en {$n}º de Primaria.</p>";
        } elseif ($subj['slug'] === 'matematicas') {
            $intro_h2 = "Matemáticas en {$n}º de Primaria: práctica guiada";
        }

        set_acf_meta($subj_id, 'hub_intro_h2', $intro_h2, $acf_keys['hub_intro_h2']);
        set_acf_meta($subj_id, 'hub_intro_content', $intro_content, $acf_keys['hub_intro_content']);
        set_acf_meta($subj_id, 'hub_seo_h2', "Recursos de {$subj['title']} para reforzar {$n}º de Primaria", $acf_keys['hub_seo_h2']);
        set_acf_meta($subj_id, 'hub_seo_content', $seo_content, $acf_keys['hub_seo_content']);
        set_acf_meta($subj_id, 'hub_faq_h2', 'Preguntas frecuentes', $acf_keys['hub_faq_h2']);

        $faq_a0 = "Sirven para repasar y consolidar contenidos de {$subj['title']} en {$n}º de Primaria con ejercicios guiados.";
        if ($subj['slug'] === 'lengua') {
            $faq_a0 = 'Sirven para practicar lectura, ortografía y expresión escrita de forma guiada.';
        } elseif ($subj['slug'] === 'conocimiento-medio') {
            $faq_a0 = 'Sirven para repasar contenidos de ciencias naturales y sociales de forma guiada.';
        } elseif ($subj['slug'] === 'ingles') {
            $faq_a0 = 'Sirven para practicar vocabulario y gramática de forma guiada.';
        }

        set_faq($subj_id, array(
            array('q' => "¿Para qué sirven las fichas de {$subj['title']} en {$n}º de Primaria?", 'a' => $faq_a0),
            array('q' => '¿Cuánto tiempo dedicar a cada ficha?', 'a' => 'Recomendamos sesiones de 10 a 15 minutos.'),
            array('q' => '¿Necesito registrarme?', 'a' => 'No. El acceso es gratuito y sin registro.'),
            array('q' => '¿Se pueden usar en móvil o tablet?', 'a' => 'Sí, funcionan en ordenador, móvil y tablet.'),
        ), $acf_keys);
    }
}

echo "Primaria hubs generated/updated.\n";
