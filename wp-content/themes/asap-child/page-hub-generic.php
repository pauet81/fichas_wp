
<?php
/**
 * Template Name: Hub Generico
 * Descripcion: Plantilla unica para niveles, cursos y asignaturas.
 */

// Ensure hub styles are loaded for all hub pages before wp_head runs.
$hub_css_path = get_stylesheet_directory() . '/css/hub-nivel.css';
$hub_css_ver = file_exists($hub_css_path) ? filemtime($hub_css_path) : '1.0.0';
if (!function_exists('fichas_force_hub_css')) {
    function fichas_force_hub_css() {
        $hub_css_path = get_stylesheet_directory() . '/css/hub-nivel.css';
        $hub_css_ver = file_exists($hub_css_path) ? filemtime($hub_css_path) : '1.0.0';
        $href = get_stylesheet_directory_uri() . '/css/hub-nivel.css?ver=' . rawurlencode($hub_css_ver);
        echo '<link rel="stylesheet" href="' . esc_url($href) . '" type="text/css" media="all" />' . "\n";
    }
    add_action('wp_head', 'fichas_force_hub_css', 1);
}

get_header();

fichas_breadcrumbs();
global $post;

$hub_force_type = isset($hub_force_type) ? $hub_force_type : null;
$hub_force_nivel_slug = isset($hub_force_nivel_slug) ? $hub_force_nivel_slug : null;

$post_id = $post ? $post->ID : 0;
$parent_id = $post_id ? wp_get_post_parent_id($post_id) : 0;
$grandparent_id = $parent_id ? wp_get_post_parent_id($parent_id) : 0;

if ($hub_force_type) {
    $hub_type = $hub_force_type;
} else {
    if ($grandparent_id) {
        $hub_type = 'asignatura';
    } elseif ($parent_id) {
        $hub_type = 'curso';
    } else {
        $hub_type = 'nivel';
    }
}

$nivel_slug = '';
$curso_slug = '';
$asignatura_slug = '';

if ($hub_type === 'nivel') {
    $nivel_slug = $hub_force_nivel_slug ?: get_post_field('post_name', $post_id);
} elseif ($hub_type === 'curso') {
    $curso_slug = get_post_field('post_name', $post_id);
    $nivel_slug = get_post_field('post_name', $parent_id);
} else {
    $asignatura_slug = get_post_field('post_name', $post_id);
    $curso_slug = get_post_field('post_name', $parent_id);
    $nivel_slug = get_post_field('post_name', $grandparent_id);
}

$nivel_term = $nivel_slug ? get_term_by('slug', $nivel_slug, 'nivel-educativo') : null;
$curso_term = $curso_slug ? get_term_by('slug', $curso_slug, 'curso') : null;
$asignatura_term = $asignatura_slug ? get_term_by('slug', $asignatura_slug, 'asignatura') : null;

$nivel_nombre = $nivel_term ? $nivel_term->name : ($nivel_slug ? ucfirst($nivel_slug) : '');
$curso_nombre = $curso_term ? $curso_term->name : '';
$asignatura_nombre = $asignatura_term ? $asignatura_term->name : '';

$is_estacional = (
    $nivel_slug === 'infantil' &&
    in_array($curso_slug, array('halloween', 'invierno', 'navidad', 'otono', 'primavera', 'verano'), true)
);

$is_tematicas_hub = ($hub_type === 'curso' && $nivel_slug === 'infantil' && $curso_slug === 'tematicas');

if ($is_estacional && $curso_slug) {
    $tema_estacional_term = get_term_by('slug', $curso_slug, 'tema-estacional');
    if ($tema_estacional_term && !is_wp_error($tema_estacional_term) && empty($curso_nombre)) {
        $curso_nombre = $tema_estacional_term->name;
    }
}

$wrapper_class = ($hub_type === 'nivel') ? 'nivel-wrapper' : (($hub_type === 'curso') ? 'curso-wrapper' : 'curso-asignatura-wrapper');
?>

<div class="<?php echo esc_attr($wrapper_class); ?>">
    <?php while (have_posts()) : the_post(); ?>

    <?php if ($hub_type === 'nivel'): ?>
        <section class="nivel-hero">
            <div class="nivel-hero-content">
                <?php if ($nivel_nombre): ?>
                    <div class="nivel-header-meta">
                        <span class="nivel-badge">Nivel Educativo</span>
                    </div>
                <?php endif; ?>
                <h1 class="nivel-titulo">
                    <?php echo esc_html(get_field('hub_h1') ?: get_the_title()); ?>
                </h1>
                <?php
                $hub_subtitulo = get_field('hub_subtitulo');
                if ($hub_subtitulo):
                ?>
                    <p class="curso-descripcion"><?php echo esc_html($hub_subtitulo); ?></p>
                <?php elseif ($nivel_slug === 'infantil'): ?>
                    <p class="curso-descripcion">
                        Fichas interactivas y educativas para ninos de 3 a 6 a&ntilde;os. Recursos digitales con correccion automatica para el aula y casa.
                    </p>
                <?php endif; ?>

                <?php
                $total_fichas_query = new WP_Query(array(
                    'post_type' => 'ficha',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'nivel-educativo',
                            'field' => 'slug',
                            'terms' => $nivel_slug
                        )
                    )
                ));
                $total_fichas = $total_fichas_query->found_posts;
                wp_reset_postdata();

                $total_cursos = ($nivel_slug === 'infantil') ? 3 : count(get_terms(array(
                    'taxonomy' => 'curso',
                    'hide_empty' => true,
                )));
                $total_asignaturas = count(get_terms(array(
                    'taxonomy' => 'asignatura',
                    'hide_empty' => true,
                )));
                ?>

                <div class="nivel-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($total_fichas, 0, ',', '.'); ?></span>
                        <span class="stat-label">Fichas disponibles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_cursos; ?></span>
                        <span class="stat-label">Cursos</span>
                    </div>
                    <?php if ($nivel_slug !== 'infantil'): ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $total_asignaturas; ?></span>
                            <span class="stat-label">Asignaturas</span>
                        </div>
                    <?php endif; ?>
                    <div class="stat-item">
                        <span class="stat-icon">&#10003;</span>
                        <span class="stat-label">Gratis y sin registro</span>
                    </div>
                </div>
            </div>
        </section>
    <?php elseif ($hub_type === 'curso'): ?>
        <section class="curso-hero">
            <div class="curso-hero-content">
                <div class="nivel-header-meta">
                    <span class="nivel-badge">Curso</span>
                    <?php if ($nivel_nombre): ?>
                        <span class="hero-badge"><?php echo esc_html($nivel_nombre); ?></span>
                    <?php endif; ?>
                </div>

                <h1 class="curso-titulo">
                    <?php echo esc_html(get_field('hub_h1') ?: ('Fichas Interactivas de ' . get_the_title())); ?>
                </h1>

                <?php
                $hub_subtitulo = get_field('hub_subtitulo');
                if ($hub_subtitulo):
                ?>
                    <p class="curso-descripcion"><?php echo esc_html($hub_subtitulo); ?></p>
                <?php elseif (get_field('descripcion_curso')): ?>
                    <p class="curso-descripcion"><?php echo esc_html(get_field('descripcion_curso')); ?></p>
                <?php endif; ?>

                <?php
                $args_count = array(
                    'post_type' => 'ficha',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'curso',
                            'field' => 'slug',
                            'terms' => $curso_slug
                        )
                    ),
                    'posts_per_page' => -1
                );
                $fichas_count = new WP_Query($args_count);
                $total_fichas = $fichas_count->found_posts;
                wp_reset_postdata();

                $total_asignaturas = count(get_terms(array(
                    'taxonomy' => 'asignatura',
                    'hide_empty' => true,
                )));
                ?>

                <div class="curso-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_fichas; ?></span>
                        <span class="stat-label">Fichas interactivas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_asignaturas; ?></span>
                        <span class="stat-label">Asignaturas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">&#10003;</span>
                        <span class="stat-label">Gratis y sin registro</span>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="curso-asignatura-hero">
            <div class="curso-asignatura-hero-content">
                <div class="curso-asignatura-header-meta">
                    <?php if ($nivel_nombre): ?>
                        <span class="curso-asignatura-badge nivel-badge-asig"><?php echo esc_html($nivel_nombre); ?></span>
                    <?php endif; ?>
                    <?php if ($curso_nombre): ?>
                        <span class="curso-asignatura-badge curso-badge-asig"><?php echo esc_html($curso_nombre); ?></span>
                    <?php endif; ?>
                    <?php if ($asignatura_nombre): ?>
                        <span class="curso-asignatura-badge asignatura-badge-asig"><?php echo esc_html($asignatura_nombre); ?></span>
                    <?php endif; ?>
                </div>

                <h1 class="curso-asignatura-titulo">
                    <?php
                    $hub_h1 = get_field('hub_h1');
                    echo esc_html($hub_h1 ?: ('Fichas Interactivas de ' . $asignatura_nombre . ' ' . $curso_nombre));
                    ?>
                </h1>

                <?php
                $args_count = array(
                    'post_type' => 'ficha',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'curso',
                            'field' => 'slug',
                            'terms' => $curso_slug
                        ),
                        array(
                            'taxonomy' => 'asignatura',
                            'field' => 'slug',
                            'terms' => $asignatura_slug
                        )
                    )
                );
                $count_query = new WP_Query($args_count);
                $total_fichas = $count_query->found_posts;
                wp_reset_postdata();

                $temas_query = new WP_Query($args_count);
                $temas_unicos = array();
                if ($temas_query->have_posts()) {
                    while ($temas_query->have_posts()) {
                        $temas_query->the_post();
                        $temas = get_the_terms(get_the_ID(), 'tema');
                        if ($temas && !is_wp_error($temas)) {
                            foreach ($temas as $tema) {
                                $temas_unicos[$tema->slug] = $tema->name;
                            }
                        }
                    }
                }
                wp_reset_postdata();
                $total_temas = count($temas_unicos);
                ?>

                <div class="curso-asignatura-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_fichas; ?></span>
                        <span class="stat-label">Fichas disponibles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_temas; ?></span>
                        <span class="stat-label">Temas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">&#10003;</span>
                        <span class="stat-label">Gratis y sin registro</span>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $hub_intro_h2 = get_field('hub_intro_h2');
    $hub_intro_content = get_field('hub_intro_content');
    $intro_seo = get_field('intro_seo');
    ?>
    <?php if ($hub_intro_h2 || $hub_intro_content || $intro_seo): ?>
        <section class="hub-intro-seo">
            <div class="content-seo-wrapper">
                <?php if ($hub_intro_h2): ?>
                    <h2 class="section-titulo"><?php echo esc_html($hub_intro_h2); ?></h2>
                <?php endif; ?>
                <?php if ($hub_intro_content): ?>
                    <?php echo wp_kses_post($hub_intro_content); ?>
                <?php elseif ($intro_seo): ?>
                    <?php echo wp_kses_post($intro_seo); ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($hub_type === 'nivel'): ?>
        <section class="nivel-cursos">
            <div class="content-wrapper">
                <div class="section-header">
                    <h2 class="section-titulo"><?php echo ($nivel_slug === 'infantil') ? 'Fichas por Edad' : 'Fichas por Curso de Primaria'; ?></h2>
                    <p class="section-descripcion">Selecciona el curso para acceder a las fichas interactivas</p>
                </div>

                <div class="cursos-grid">
                    <?php if ($nivel_slug === 'infantil'): ?>
                        <?php
                        $cursos_infantil = array(
                            '3-anos' => array('icono' => '3', 'label' => '3 a&ntilde;os'),
                            '4-anos' => array('icono' => '4', 'label' => '4 a&ntilde;os'),
                            '5-anos' => array('icono' => '5', 'label' => '5 a&ntilde;os'),
                        );
                        foreach ($cursos_infantil as $slug => $data):
                            $count_query = new WP_Query(array(
                                'post_type' => 'ficha',
                                'posts_per_page' => -1,
                                'tax_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy' => 'curso',
                                        'field' => 'slug',
                                        'terms' => $slug
                                    ),
                                    array(
                                        'taxonomy' => 'nivel-educativo',
                                        'field' => 'slug',
                                        'terms' => 'infantil'
                                    )
                                )
                            ));
                            $count = $count_query->found_posts;
                            wp_reset_postdata();
                        ?>
                            <a href="<?php echo esc_url(home_url('/infantil/' . $slug . '/')); ?>" class="curso-card">
                                <div class="curso-icon"><?php echo $data['icono']; ?></div>
                                <h3 class="curso-nombre"><?php echo esc_html($data['label']); ?></h3>
                                <p class="curso-descripcion-card">Fichas educativas para ninos de <?php echo esc_html($data['label']); ?></p>
                                <div class="curso-fichas-count">
                                    <span class="fichas-number"><?php echo $count; ?></span>
                                    <span class="fichas-label">fichas</span>
                                    <span class="curso-arrow">-></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                        $cursos_config = array(
                            '1-primaria' => array('nombre' => '1º Primaria', 'icono' => '1', 'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'edad' => '6-7 a&ntilde;os'),
                            '2-primaria' => array('nombre' => '2º Primaria', 'icono' => '2', 'color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 'edad' => '7-8 a&ntilde;os'),
                            '3-primaria' => array('nombre' => '3º Primaria', 'icono' => '3', 'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 'edad' => '8-9 a&ntilde;os'),
                            '4-primaria' => array('nombre' => '4º Primaria', 'icono' => '4', 'color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'edad' => '9-10 a&ntilde;os'),
                            '5-primaria' => array('nombre' => '5º Primaria', 'icono' => '5', 'color' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)', 'edad' => '10-11 a&ntilde;os'),
                            '6-primaria' => array('nombre' => '6º Primaria', 'icono' => '6', 'color' => 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)', 'edad' => '11-12 a&ntilde;os')
                        );
                        foreach ($cursos_config as $slug => $curso):
                            $count_query = new WP_Query(array(
                                'post_type' => 'ficha',
                                'posts_per_page' => -1,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'curso',
                                        'field' => 'slug',
                                        'terms' => $slug
                                    )
                                )
                            ));
                            $num_fichas = $count_query->found_posts;
                            wp_reset_postdata();
                        ?>
                            <div class="curso-card">
                                <a href="<?php echo esc_url(home_url('/primaria/' . $slug . '/')); ?>" class="curso-link">
                                    <div class="curso-icon" style="background: <?php echo $curso['color']; ?>;">
                                        <span><?php echo $curso['icono']; ?></span>
                                    </div>
                                    <h3 class="curso-nombre"><?php echo esc_html($curso['nombre']); ?></h3>
                                    <p class="curso-descripcion-card"><?php echo esc_html($curso['edad']); ?></p>
                                    <div class="curso-fichas-count">
                                        <span class="fichas-number"><?php echo $num_fichas; ?></span>
                                        <span class="fichas-label">fichas disponibles</span>
                                    </div>
                                    <span class="curso-arrow">-></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if ($nivel_slug === 'infantil'): ?>
            <section class="nivel-tematicas-estacionales">
                <div class="content-wrapper">
                    <div class="section-header">
                        <h2 class="section-titulo">Fichas Tematicas</h2>
                        <p class="section-descripcion">Fichas especiales para celebrar cada epoca del ano</p>
                    </div>

                    <div class="tematicas-grid">
                        <?php
                        $temas_estacionales = get_terms(array(
                            'taxonomy' => 'tema-estacional',
                            'hide_empty' => false
                        ));

                        if ($temas_estacionales && !is_wp_error($temas_estacionales)):
                            foreach ($temas_estacionales as $tema_estacional):
                                $tax_query = array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy' => 'nivel-educativo',
                                        'field' => 'slug',
                                        'terms' => 'infantil'
                                    ),
                                    array(
                                        'relation' => 'OR',
                                        array(
                                            'taxonomy' => 'tema-estacional',
                                            'field' => 'term_id',
                                            'terms' => $tema_estacional->term_id
                                        ),
                                        array(
                                            'taxonomy' => 'curso',
                                            'field' => 'slug',
                                            'terms' => $tema_estacional->slug
                                        )
                                    )
                                );

                                $count_query = new WP_Query(array(
                                    'post_type' => 'ficha',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1,
                                    'fields' => 'ids',
                                    'tax_query' => $tax_query
                                ));
                                $num_fichas_tematica = $count_query->found_posts;
                                wp_reset_postdata();

                                $iconos_tematicas = array(
                                    'halloween' => '🎃',
                                    'navidad' => '🎄',
                                    'invierno' => '❄️',
                                    'primavera' => '🌸',
                                    'verano' => '☀️',
                                    'otono' => '🍂',
                                    'otonio' => '🍂'
                                );
                                $icono = isset($iconos_tematicas[$tema_estacional->slug]) ? $iconos_tematicas[$tema_estacional->slug] : '⭐';
                        ?>
                                <a href="<?php echo esc_url(home_url('/infantil/' . $tema_estacional->slug . '/')); ?>" class="tematica-card">
                                    <div class="tematica-icon"><?php echo esc_html($icono); ?></div>
                                    <h3 class="tematica-nombre"><?php echo esc_html($tema_estacional->name); ?></h3>
                                    <?php if (!empty($tema_estacional->description)): ?>
                                        <p class="tematica-descripcion"><?php echo esc_html($tema_estacional->description); ?></p>
                                    <?php endif; ?>
                                    <div class="tematica-fichas-count">
                                        <span class="fichas-number"><?php echo (int) $num_fichas_tematica; ?></span>
                                        <span class="fichas-label">fichas</span>
                                        <span class="tematica-arrow">→</span>
                                    </div>
                                </a>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php elseif ($hub_type === 'curso' && $is_tematicas_hub): ?>
        <section class="nivel-tematicas-estacionales">
            <div class="content-wrapper">
                <div class="section-header">
                    <h2 class="section-titulo">Fichas Tematicas</h2>
                    <p class="section-descripcion">Fichas especiales para celebrar cada epoca del ano</p>
                </div>

                <div class="tematicas-grid">
                    <?php
                    $temas_estacionales = get_terms(array(
                        'taxonomy' => 'tema-estacional',
                        'hide_empty' => false
                    ));

                    if ($temas_estacionales && !is_wp_error($temas_estacionales)):
                        foreach ($temas_estacionales as $tema_estacional):
                            $tax_query = array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'nivel-educativo',
                                    'field' => 'slug',
                                    'terms' => 'infantil'
                                ),
                                array(
                                    'relation' => 'OR',
                                    array(
                                        'taxonomy' => 'tema-estacional',
                                        'field' => 'term_id',
                                        'terms' => $tema_estacional->term_id
                                    ),
                                    array(
                                        'taxonomy' => 'curso',
                                        'field' => 'slug',
                                        'terms' => $tema_estacional->slug
                                    )
                                )
                            );

                            $count_query = new WP_Query(array(
                                'post_type' => 'ficha',
                                'post_status' => 'publish',
                                'posts_per_page' => -1,
                                'fields' => 'ids',
                                'tax_query' => $tax_query
                            ));
                            $num_fichas_tematica = $count_query->found_posts;
                            wp_reset_postdata();

                            $iconos_tematicas = array(
                                'halloween' => '🎃',
                                'navidad' => '🎄',
                                'invierno' => '❄️',
                                'primavera' => '🌸',
                                'verano' => '☀️',
                                'otono' => '🍂',
                                'otonio' => '🍂'
                            );
                            $icono = isset($iconos_tematicas[$tema_estacional->slug]) ? $iconos_tematicas[$tema_estacional->slug] : '⭐';
                    ?>
                            <a href="<?php echo esc_url(home_url('/infantil/' . $tema_estacional->slug . '/')); ?>" class="tematica-card">
                                <div class="tematica-icon"><?php echo esc_html($icono); ?></div>
                                <h3 class="tematica-nombre"><?php echo esc_html($tema_estacional->name); ?></h3>
                                <?php if (!empty($tema_estacional->description)): ?>
                                    <p class="tematica-descripcion"><?php echo esc_html($tema_estacional->description); ?></p>
                                <?php endif; ?>
                                <div class="tematica-fichas-count">
                                    <span class="fichas-number"><?php echo (int) $num_fichas_tematica; ?></span>
                                    <span class="fichas-label">fichas</span>
                                    <span class="tematica-arrow">→</span>
                                </div>
                            </a>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </section>
    <?php elseif ($hub_type === 'curso' && !$is_estacional): ?>
        <section class="curso-asignaturas">
            <h2 class="section-titulo">Explora por Asignatura</h2>
            <p class="section-descripcion">Elige la asignatura para ver las fichas disponibles</p>

            <div class="asignaturas-grid">
                <?php
                $asignaturas_config = array(
                    'matematicas' => array('nombre' => 'Matematicas', 'icono' => 'M', 'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'descripcion' => 'Numeros y problemas'),
                    'lenguaje' => array('nombre' => 'Lenguaje', 'icono' => 'L', 'color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 'descripcion' => 'Lectura y escritura'),
                    'ingles' => array('nombre' => 'Ingles', 'icono' => 'I', 'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 'descripcion' => 'Vocabulario')
                );

                if ($nivel_slug === 'infantil') {
                    $asignaturas_config['conocimiento-del-entorno'] = array('nombre' => 'Conocimiento del Entorno', 'icono' => 'C', 'color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'descripcion' => 'Ciencias');
                } else {
                    $asignaturas_config['conocimiento-del-medio'] = array('nombre' => 'Conocimiento del Medio', 'icono' => 'C', 'color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'descripcion' => 'Ciencias');
                }

                foreach ($asignaturas_config as $slug => $asignatura):
                    $count_query = new WP_Query(array(
                        'post_type' => 'ficha',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'curso',
                                'field' => 'slug',
                                'terms' => $curso_slug
                            ),
                            array(
                                'taxonomy' => 'asignatura',
                                'field' => 'slug',
                                'terms' => $slug
                            )
                        )
                    ));
                    $num_fichas_asig = $count_query->found_posts;
                    wp_reset_postdata();

                    $url_asignatura = home_url('/' . $nivel_slug . '/' . $curso_slug . '/' . $slug . '/');
                ?>
                    <div class="asignatura-card">
                        <a href="<?php echo esc_url($url_asignatura); ?>" class="asignatura-link">
                            <div class="asignatura-icon" style="background: <?php echo $asignatura['color']; ?>;">
                                <span><?php echo $asignatura['icono']; ?></span>
                            </div>
                            <h3 class="asignatura-nombre"><?php echo esc_html($asignatura['nombre']); ?></h3>
                            <p class="asignatura-descripcion"><?php echo esc_html($asignatura['descripcion']); ?></p>
                            <div class="asignatura-fichas-count">
                                <span class="fichas-number"><?php echo $num_fichas_asig; ?></span>
                                <span class="fichas-label">fichas disponibles</span>
                            </div>
                            <span class="asignatura-arrow">-></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php else: ?>
        <section class="seccion-fichas">
            <h2 class="section-titulo">
                <?php if (!empty($asignatura_nombre)): ?>
                    Todas las Fichas de <?php echo esc_html($asignatura_nombre); ?> para <?php echo esc_html($curso_nombre); ?>
                <?php else: ?>
                    Todas las Fichas de <?php echo esc_html($nivel_nombre); ?> de <?php echo esc_html($curso_nombre); ?>
                <?php endif; ?>
            </h2>
            <p class="section-descripcion">Recursos educativos interactivos organizados por temas</p>

            <?php
            if ($is_estacional) {
                $tax_query = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'nivel-educativo',
                        'field' => 'slug',
                        'terms' => $nivel_slug
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => 'tema-estacional',
                            'field' => 'slug',
                            'terms' => $curso_slug
                        ),
                        array(
                            'taxonomy' => 'curso',
                            'field' => 'slug',
                            'terms' => $curso_slug
                        )
                    )
                );
            } else {
                $tax_query = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'curso',
                        'field' => 'slug',
                        'terms' => $curso_slug
                    ),
                    array(
                        'taxonomy' => 'asignatura',
                        'field' => 'slug',
                        'terms' => $asignatura_slug
                    )
                );
            }

            $args_fichas = array(
                'post_type' => 'ficha',
                'post_status' => 'publish',
                'posts_per_page' => 50,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => $tax_query
            );

            $fichas_query = new WP_Query($args_fichas);

            if ($fichas_query->have_posts()) :
            ?>
                <div class="fichas-grid">
                    <?php while ($fichas_query->have_posts()) : $fichas_query->the_post(); ?>
                        <?php
                        $edad = get_field('edad_recomendada');
                        $tiempo = get_field('tiempo_estimado');
                        $asignaturas_ficha = get_the_terms(get_the_ID(), 'asignatura');
                        $dificultad = get_the_terms(get_the_ID(), 'dificultad');
                        $nivel = get_the_terms(get_the_ID(), 'nivel-educativo');
                        $curso = get_the_terms(get_the_ID(), 'curso');

                        $nivel_class = 'nivel-primaria';
                        if ($nivel && !is_wp_error($nivel)) {
                            $nivel_slug_item = $nivel[0]->slug;
                            if ($nivel_slug_item === 'infantil') {
                                $nivel_class = 'nivel-infantil';
                            } elseif ($nivel_slug_item === 'secundaria') {
                                $nivel_class = 'nivel-secundaria';
                            }
                        }
                        ?>
                        <div class="ficha-card-mini-v2 <?php echo $nivel_class; ?>">
                            <a href="<?php the_permalink(); ?>" class="ficha-card-link-v2">
                                <div class="ficha-card-header-v2">
                                    <div class="badges-row">
                                        <?php if ($nivel && !is_wp_error($nivel)): ?>
                                            <span class="badge-nivel-mini"><?php echo esc_html($nivel[0]->name); ?></span>
                                        <?php endif; ?>
                                        <?php if ($curso && !is_wp_error($curso)): ?>
                                            <span class="badge-curso-mini"><?php echo esc_html($curso[0]->name); ?></span>
                                        <?php endif; ?>
                                        <?php if ($asignaturas_ficha && !is_wp_error($asignaturas_ficha)): ?>
                                            <span class="badge-asignatura-mini"><?php echo esc_html($asignaturas_ficha[0]->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="badge-vistas-card">
                                        &#x1F441;&#xFE0F; <?php echo fichas_format_vistas(get_the_ID()); ?>
                                    </div>
                                </div>

                                <div class="ficha-card-body-v2">
                                    <h3 class="ficha-card-titulo-v2"><?php the_title(); ?></h3>
                                    <div class="ficha-card-meta-v2">
                                        <?php if ($edad): ?>
                                            <span class="meta-item-v2">
                                                <span class="meta-icon">&#x1F464;</span>
                                                <span class="meta-text"><?php echo esc_html($edad); ?> a&ntilde;os</span>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($tiempo): ?>
                                            <span class="meta-item-v2">
                                                <span class="meta-icon">&#x23F1;&#xFE0F;</span>
                                                <span class="meta-text"><?php echo esc_html($tiempo); ?> min</span>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($dificultad && !is_wp_error($dificultad)): ?>
                                            <span class="meta-item-v2">
                                                <span class="meta-icon">&#x2B50;</span>
                                                <span class="meta-text"><?php echo esc_html($dificultad[0]->name); ?></span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="ficha-card-footer-v2">
                                    <span class="ficha-card-cta-v2">
                                        <span class="cta-text">Empezar ficha</span>
                                        <span class="cta-arrow">&#x2192;</span>
                                    </span>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-fichas">
                    <p>Todavia no hay fichas de <?php echo esc_html($asignatura_nombre); ?> para <?php echo esc_html($curso_nombre); ?>. Proximamente.</p>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </section>

        <?php /* Sección "Explora por Temas" eliminada temporalmente */ ?>
    <?php endif; ?>

    <?php
    $hub_seo_h2 = get_field('hub_seo_h2');
    $hub_seo_content = get_field('hub_seo_content');
    $contenido_principal = get_field('contenido_principal_seo');
    ?>
    <?php if ($hub_seo_h2 || $hub_seo_content || $contenido_principal): ?>
        <section class="hub-contenido-principal-seo">
            <div class="content-seo-wrapper">
                <?php if ($hub_seo_h2): ?>
                    <h2 class="section-titulo"><?php echo esc_html($hub_seo_h2); ?></h2>
                <?php endif; ?>
                <?php if ($hub_seo_content): ?>
                    <?php echo wp_kses_post($hub_seo_content); ?>
                <?php elseif ($contenido_principal): ?>
                    <?php echo wp_kses_post($contenido_principal); ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($hub_type === 'nivel' || $hub_type === 'curso'): ?>
        <section class="<?php echo ($hub_type === 'curso') ? 'curso-fichas-destacadas' : 'nivel-fichas-destacadas'; ?>">
            <div class="content-wrapper">
                <div class="section-header">
                    <h2 class="section-titulo">
                        <?php
                        if ($hub_type === 'curso') {
                            echo 'Fichas Destacadas de ' . esc_html(get_the_title());
                        } else {
                            echo 'Fichas Destacadas';
                        }
                        ?>
                    </h2>
                    <p class="section-descripcion">
                        <?php echo ($hub_type === 'curso') ? 'Las fichas mas populares de este curso' : 'Las fichas mas populares de este nivel'; ?>
                    </p>
                </div>

                <div class="fichas-grid">
                    <?php
                    $tax_query = array();
                    if ($hub_type === 'curso') {
                        $tax_query[] = array(
                            'taxonomy' => 'curso',
                            'field' => 'slug',
                            'terms' => $curso_slug
                        );
                    } else {
                        $tax_query[] = array(
                            'taxonomy' => 'nivel-educativo',
                            'field' => 'slug',
                            'terms' => $nivel_slug
                        );
                    }

                    $args_destacadas = array(
                        'post_type' => 'ficha',
                        'posts_per_page' => 6,
                        'post_status' => 'publish',
                        'tax_query' => $tax_query,
                        'meta_key' => 'fichas_vistas',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC'
                    );

                    $destacadas = new WP_Query($args_destacadas);

                    if ($destacadas->have_posts()):
                        while ($destacadas->have_posts()): $destacadas->the_post();
                            $edad = get_field('edad_recomendada');
                            $tiempo = get_field('tiempo_estimado');
                            $asignaturas_ficha = get_the_terms(get_the_ID(), 'asignatura');
                            $dificultad = get_the_terms(get_the_ID(), 'dificultad');
                            $nivel = get_the_terms(get_the_ID(), 'nivel-educativo');
                            $curso = get_the_terms(get_the_ID(), 'curso');

                            $nivel_class = 'nivel-primaria';
                            if ($nivel && !is_wp_error($nivel)) {
                                $nivel_slug_item = $nivel[0]->slug;
                                if ($nivel_slug_item === 'infantil') {
                                    $nivel_class = 'nivel-infantil';
                                } elseif ($nivel_slug_item === 'secundaria') {
                                    $nivel_class = 'nivel-secundaria';
                                }
                            }
                    ?>
                            <div class="ficha-card-mini-v2 <?php echo $nivel_class; ?>">
                                <a href="<?php the_permalink(); ?>" class="ficha-card-link-v2">
                                    <div class="ficha-card-header-v2">
                                        <div class="badges-row">
                                            <?php if ($nivel && !is_wp_error($nivel)): ?>
                                                <span class="badge-nivel-mini"><?php echo esc_html($nivel[0]->name); ?></span>
                                            <?php endif; ?>
                                            <?php if ($curso && !is_wp_error($curso)): ?>
                                                <span class="badge-curso-mini"><?php echo esc_html($curso[0]->name); ?></span>
                                            <?php endif; ?>
                                            <?php if ($asignaturas_ficha && !is_wp_error($asignaturas_ficha)): ?>
                                                <span class="badge-asignatura-mini"><?php echo esc_html($asignaturas_ficha[0]->name); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="badge-vistas-card">
                                            &#x1F441;&#xFE0F; <?php echo fichas_format_vistas(get_the_ID()); ?>
                                        </div>
                                    </div>

                                    <div class="ficha-card-body-v2">
                                        <h3 class="ficha-card-titulo-v2"><?php the_title(); ?></h3>
                                        <div class="ficha-card-meta-v2">
                                            <?php if ($edad): ?>
                                                <span class="meta-item-v2">
                                                    <span class="meta-icon">&#x1F464;</span>
                                                    <span class="meta-text"><?php echo esc_html($edad); ?> a&ntilde;os</span>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($tiempo): ?>
                                                <span class="meta-item-v2">
                                                    <span class="meta-icon">&#x23F1;&#xFE0F;</span>
                                                    <span class="meta-text"><?php echo esc_html($tiempo); ?> min</span>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($dificultad && !is_wp_error($dificultad)): ?>
                                                <span class="meta-item-v2">
                                                    <span class="meta-icon">&#x2B50;</span>
                                                    <span class="meta-text"><?php echo esc_html($dificultad[0]->name); ?></span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="ficha-card-footer-v2">
                                        <span class="ficha-card-cta-v2">
                                            <span class="cta-text">Empezar ficha</span>
                                            <span class="cta-arrow">&#x2192;</span>
                                        </span>
                                    </div>
                                </a>
                            </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                        echo '<p class="no-fichas">Todavia no hay fichas publicadas. Proximamente.</p>';
                    endif;
                    ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $hub_faq_h2 = get_field('hub_faq_h2');
    $hub_faq_items = get_field('hub_faq_items');
    $bloque_faq = get_field('bloque_mini_faq');
    $contenido_adicional = get_field('contenido_adicional_seo');
    $faq_rendered = false;

    if (!empty($hub_faq_items)) :
        $faq_rendered = true;
    ?>
        <section class="bloque-mini-faq">
            <div class="contenido-wrapper">
                <h2 class="section-titulo"><?php echo esc_html($hub_faq_h2 ?: 'Preguntas frecuentes'); ?></h2>
                <div class="faq-acordeon">
                    <?php foreach ($hub_faq_items as $faq) : ?>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span class="faq-pregunta-texto"><?php echo esc_html($faq['pregunta']); ?></span>
                                <span class="faq-icono" aria-hidden="true">+</span>
                            </button>
                            <div class="faq-answer">
                                <p><?php echo wp_kses_post($faq['respuesta']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        $faq_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );
        foreach ($hub_faq_items as $faq) {
            $faq_schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $faq['pregunta'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($faq['respuesta'])
                )
            );
        }
        ?>
        <script type="application/ld+json">
        <?php echo wp_json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        </script>
    <?php elseif ($bloque_faq && $bloque_faq['mostrar'] && !empty($bloque_faq['preguntas'])) :
        $faq_rendered = true;
    ?>
        <section class="bloque-mini-faq">
            <div class="contenido-wrapper">
                <?php if (!empty($bloque_faq['titulo'])) : ?>
                    <h2 class="section-titulo"><?php echo esc_html($bloque_faq['titulo']); ?></h2>
                <?php endif; ?>
                <div class="faq-acordeon">
                    <?php foreach ($bloque_faq['preguntas'] as $faq) : ?>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span class="faq-pregunta-texto"><?php echo esc_html($faq['pregunta']); ?></span>
                                <span class="faq-icono" aria-hidden="true">+</span>
                            </button>
                            <div class="faq-answer">
                                <p><?php echo wp_kses_post($faq['respuesta']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($bloque_faq['link_faq_completo'])) : ?>
                    <p class="faq-link-completo">
                        <a href="<?php echo esc_url($bloque_faq['link_faq_completo']); ?>">
                            <?php echo !empty($bloque_faq['link_texto']) ? esc_html($bloque_faq['link_texto']) : 'Ver todas las preguntas frecuentes'; ?> ->
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </section>
        <?php
        $faq_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );
        foreach ($bloque_faq['preguntas'] as $faq) {
            $faq_schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $faq['pregunta'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($faq['respuesta'])
                )
            );
        }
        ?>
        <script type="application/ld+json">
        <?php echo wp_json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        </script>
    <?php elseif ($contenido_adicional) :
        $faq_items = array();
        if (preg_match_all('/<h3[^>]*>(.*?)<\/h3>\s*<p[^>]*>(.*?)<\/p>/is', $contenido_adicional, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $faq_items[] = array(
                    'pregunta' => wp_strip_all_tags($m[1]),
                    'respuesta' => wp_kses_post($m[2]),
                );
            }
        }
        if (empty($faq_items)) {
            $faq_items[] = array(
                'pregunta' => 'Mas informacion',
                'respuesta' => wp_kses_post($contenido_adicional),
            );
        }
        $faq_rendered = true;
    ?>
        <section class="bloque-mini-faq">
            <div class="contenido-wrapper">
                <h2 class="section-titulo">Preguntas frecuentes</h2>
                <div class="faq-acordeon">
                    <?php foreach ($faq_items as $faq) : ?>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span class="faq-pregunta-texto"><?php echo esc_html($faq['pregunta']); ?></span>
                                <span class="faq-icono" aria-hidden="true">+</span>
                            </button>
                            <div class="faq-answer">
                                <p><?php echo $faq['respuesta']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($contenido_adicional && !$faq_rendered): ?>
        <section class="hub-contenido-adicional-seo">
            <div class="content-seo-wrapper">
                <?php echo $contenido_adicional; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
