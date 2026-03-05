<?php
/**
 * Template Name: Single Ficha Educativa
 * Template para mostrar fichas educativas individuales
 * CPT: ficha
 */

get_header(); 

// Obtener campos ACF
$edad = get_field('edad_recomendada');
$tiempo = get_field('tiempo_estimado');
$destacada = get_field('destacada');
$descripcion = get_field('descripcion');
$contenido_html = get_field('contenido_ficha_html');
$objetivos = get_field('objetivos');
$instrucciones = get_field('instrucciones');

// Obtener términos de taxonomías
$asignaturas = get_the_terms(get_the_ID(), 'asignatura');
$temas = get_the_terms(get_the_ID(), 'tema');
$cursos = get_the_terms(get_the_ID(), 'curso');

// Función para construir URL custom del breadcrumb
function construir_url_breadcrumb($curso = null, $asignatura = null) {
    $base_url = home_url('/primaria/');
    
    if (!$curso) {
        return $base_url;
    }
    
    $curso_slug = $curso->slug;
    
    if (!$asignatura) {
        return $base_url . $curso_slug . '/';
    }
    
    $asignatura_slug = $asignatura->slug;
    return $base_url . $curso_slug . '/' . $asignatura_slug . '/';
}

// Función para obtener ficha siguiente/anterior (VERSIÓN MEJORADA)
function obtener_ficha_adyacente($tipo = 'next') {
    global $post;
    
    $cursos = get_the_terms($post->ID, 'curso');
    $asignaturas = get_the_terms($post->ID, 'asignatura');
    
    // Construir tax_query básico
    $tax_query = array('relation' => 'AND');
    
    if ($cursos && !is_wp_error($cursos)) {
        $tax_query[] = array(
            'taxonomy' => 'curso',
            'field' => 'term_id',
            'terms' => wp_list_pluck($cursos, 'term_id'),
        );
    }
    
    if ($asignaturas && !is_wp_error($asignaturas)) {
        $tax_query[] = array(
            'taxonomy' => 'asignatura',
            'field' => 'term_id',
            'terms' => wp_list_pluck($asignaturas, 'term_id'),
        );
    }
    
    // Obtener TODAS las fichas con la misma taxonomía
    $args = array(
        'post_type' => 'ficha',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'ASC',
        'fields' => 'ids',
    );
    
    if (!empty($tax_query) && count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }
    
    $query = new WP_Query($args);
    $fichas_ids = $query->posts;
    
    // Si no hay fichas o solo hay 1, retornar null
    if (empty($fichas_ids) || count($fichas_ids) <= 1) {
        return null;
    }
    
    // Encontrar la posición actual
    $posicion_actual = array_search($post->ID, $fichas_ids);
    
    if ($posicion_actual === false) {
        return null;
    }
    
    // Calcular la posición del adyacente
    if ($tipo === 'next') {
        $nueva_posicion = $posicion_actual + 1;
        // Si llegamos al final, volver al inicio (circular)
        if ($nueva_posicion >= count($fichas_ids)) {
            $nueva_posicion = 0;
        }
    } else {
        $nueva_posicion = $posicion_actual - 1;
        // Si llegamos al inicio, ir al final (circular)
        if ($nueva_posicion < 0) {
            $nueva_posicion = count($fichas_ids) - 1;
        }
    }
    
    // Obtener el post adyacente
    $ficha_adyacente_id = $fichas_ids[$nueva_posicion];
    
    // Evitar devolver la misma ficha
    if ($ficha_adyacente_id == $post->ID) {
        return null;
    }
    
    return get_post($ficha_adyacente_id);
}

?>

<main class="ficha-wrapper">
    

    <!-- SCHEMA MARKUP JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LearningResource",
        "@id": "<?php echo esc_url(get_permalink()); ?>#learningresource",
        "name": <?php echo json_encode(get_the_title(), JSON_UNESCAPED_UNICODE); ?>,
        "description": <?php echo json_encode($descripcion ? wp_strip_all_tags($descripcion) : get_the_title(), JSON_UNESCAPED_UNICODE); ?>,
        "url": "<?php echo esc_url(get_permalink()); ?>",
        "learningResourceType": "Worksheet",
        <?php if ($cursos && !is_wp_error($cursos) && isset($cursos[0]->name)): ?>
        "educationalLevel": <?php echo json_encode($cursos[0]->name, JSON_UNESCAPED_UNICODE); ?>,
        <?php endif; ?>
        "inLanguage": "es-ES",
        "isAccessibleForFree": true,
        "datePublished": "<?php echo get_the_date('c'); ?>",
        "dateModified": "<?php echo get_the_modified_date('c'); ?>",
        "audience": {
            "@type": "EducationalAudience",
            "educationalRole": "student"
        },
        "author": {
            "@type": "Organization",
            "name": "Pau Castells",
            "url": "<?php echo esc_url(home_url('/')); ?>"
        },
        "educationalUse": "assignment",
        "typicalAgeRange": "<?php echo $edad ? esc_attr($edad) : '6-12'; ?>",
        "timeRequired": "PT<?php echo $tiempo ? intval($tiempo) : 15; ?>M",
        "interactivityType": "mixed"
        <?php if ($asignaturas && !is_wp_error($asignaturas) && isset($asignaturas[0]->name)): ?>
        ,"teaches": <?php echo json_encode($asignaturas[0]->name, JSON_UNESCAPED_UNICODE); ?>
        <?php endif; ?>
    }
    </script>

    <?php fichas_breadcrumbs(); ?>

    <!-- ============================================



    <?php while (have_posts()) : the_post(); ?>

    

    <!-- HEADER CON TAXONOMÍAS INTEGRADAS -->
    <header class="ficha-header">

        <?php if ($destacada): ?>
            <div class="destacada-badge">⭐ Ficha Destacada</div>
        <?php endif; ?>

        <h1 class="ficha-titulo"><?php the_title(); ?></h1>


         <!-- Metadata minimalista -->
<div class="ficha-metadata-minimal">
    <?php // ✅ NUEVO: Veces realizada ?>
    <span class="meta-badge meta-vistas">
        👁️ Ficha realizada <?php echo fichas_format_vistas(); ?> veces
    </span>
    <?php
    // Edad
    if ($edad) :
    ?>
        <span class="meta-badge meta-edad">
            👤 <?php echo esc_html($edad); ?> años
        </span>
    <?php endif; ?>
    
    <?php // Tiempo
    if ($tiempo) :
    ?>
        <span class="meta-badge meta-tiempo">
            ⏱ <?php echo esc_html($tiempo); ?> min
        </span>
    <?php endif; ?>
    
    <?php // Dificultad
    $dificultad = get_the_terms(get_the_ID(), 'dificultad');
    if ($dificultad && !is_wp_error($dificultad)) :
        $dif_slug = $dificultad[0]->slug;
        $dif_class = 'meta-dificultad-' . $dif_slug;
    ?>
        <span class="meta-badge meta-dificultad <?php echo $dif_class; ?>">
            <?php 
            $emoji = '';
            if ($dif_slug == 'facil') $emoji = '⭐';
            elseif ($dif_slug == 'medio') $emoji = '⭐⭐';
            elseif ($dif_slug == 'dificil') $emoji = '⭐⭐⭐';
            echo $emoji . ' ' . esc_html($dificultad[0]->name);
            ?>
        </span>
    <?php endif; ?>
    
    <?php // Tema
    $temas = get_the_terms(get_the_ID(), 'tema');
    if ($temas && !is_wp_error($temas)) :
    ?>
        <span class="meta-badge meta-tema">
            🏷️ <?php echo esc_html($temas[0]->name); ?>
        </span>
    <?php endif; ?>
    
    <?php // Bloques LOMLOE
    $lomloe = get_the_terms(get_the_ID(), 'bloque-lomloe');
    if ($lomloe && !is_wp_error($lomloe)) :
    ?>
        <span class="meta-badge meta-lomloe">
            📚 <?php echo esc_html($lomloe[0]->name); ?>
        </span>
    <?php endif; ?>
    
    
</div>



    </header>

    <!-- CONTENIDO PRINCIPAL (FICHA INTERACTIVA) -->
    <?php if ($contenido_html): ?>
        <div class="ficha-contenido-principal">
            <?php echo $contenido_html; ?>
        </div>
    <?php else: ?>
        <div class="ficha-contenido-principal">
            <p class="no-content">⚠️ No hay contenido disponible para esta ficha.</p>
        </div>
    <?php endif; ?>

<!-- NAVEGACIÓN ANTERIOR/SIGUIENTE (CON TÍTULOS) -->
<?php
$ficha_anterior = obtener_ficha_adyacente('prev');
$ficha_siguiente = obtener_ficha_adyacente('next');

if ($ficha_anterior || $ficha_siguiente): ?>
    <nav class="ficha-navegacion-cards" aria-label="Navegación entre fichas">
        
        <?php if ($ficha_anterior): ?>
            <a href="<?php echo esc_url(get_permalink($ficha_anterior->ID)); ?>" class="nav-card nav-card-prev">
                <div class="nav-card-icon">←</div>
                <div class="nav-card-content">
                    <span class="nav-card-label">Ficha anterior</span>
                    <span class="nav-card-title"><?php echo esc_html($ficha_anterior->post_title); ?></span>
                </div>
            </a>
        <?php else: ?>
            <div class="nav-card nav-card-disabled">
                <div class="nav-card-icon">←</div>
                <div class="nav-card-content">
                    <span class="nav-card-label">No hay anterior</span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($ficha_siguiente): ?>
            <a href="<?php echo esc_url(get_permalink($ficha_siguiente->ID)); ?>" class="nav-card nav-card-next">
                <div class="nav-card-content">
                    <span class="nav-card-label">Siguiente ficha</span>
                    <span class="nav-card-title"><?php echo esc_html($ficha_siguiente->post_title); ?></span>
                </div>
                <div class="nav-card-icon">→</div>
            </a>
        <?php else: ?>
            <div class="nav-card nav-card-disabled">
                <div class="nav-card-content">
                    <span class="nav-card-label">No hay siguiente</span>
                </div>
                <div class="nav-card-icon">→</div>
            </div>
        <?php endif; ?>

    </nav>
<?php endif; ?>



    <!-- DESCRIPCIÓN (1 COLUMNA COMPLETA) -->
    <?php if ($descripcion): ?>
        <div class="ficha-descripcion-completa">
            <h2 class="info-titulo">📝 Descripción</h2>
            <div class="descripcion-texto">
                <?php echo wpautop(wp_kses_post($descripcion)); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- OBJETIVOS + INSTRUCCIONES (2 COLUMNAS) -->
    <?php if ($objetivos || $instrucciones): ?>
        <div class="ficha-grid-info">

            <!-- Objetivos de Aprendizaje -->
            <div class="info-box">
                <h2 class="info-titulo">🎯 Objetivos de Aprendizaje</h2>
                <?php if ($objetivos): ?>
                    <ul class="objetivos-lista">
                        <?php 
                        $objetivos_array = explode('|', $objetivos);
                        foreach ($objetivos_array as $objetivo) {
                            $objetivo = trim($objetivo);
                            if (!empty($objetivo)) {
                                echo '<li>' . esc_html($objetivo) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                <?php else: ?>
                    <p class="no-info">No hay objetivos definidos.</p>
                <?php endif; ?>
            </div>

            <!-- Instrucciones -->
            <div class="info-box">
                <h2 class="info-titulo">📋 Instrucciones</h2>
                <?php if ($instrucciones): ?>
                    <ul class="instrucciones-lista">
                        <?php 
                        $instrucciones_array = explode('|', $instrucciones);
                        foreach ($instrucciones_array as $instruccion) {
                            $instruccion = trim($instruccion);
                            if (!empty($instruccion)) {
                                echo '<li>' . esc_html($instruccion) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                <?php else: ?>
                    <p class="no-info">No hay instrucciones definidas.</p>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>

    <!-- FICHAS RELACIONADAS (CARRUSEL) -->
    <?php
    // Obtener fichas relacionadas por taxonomía
    $related_args = array(
        'post_type' => 'ficha',
        'posts_per_page' => 12,
        'post__not_in' => array(get_the_ID()),
        'tax_query' => array(
            'relation' => 'OR',
        )
    );

    // Añadir query por asignatura
    if ($asignaturas && !is_wp_error($asignaturas)) {
        $asignatura_ids = wp_list_pluck($asignaturas, 'term_id');
        $related_args['tax_query'][] = array(
            'taxonomy' => 'asignatura',
            'field' => 'term_id',
            'terms' => $asignatura_ids,
        );
    }

    // Añadir query por curso
    if ($cursos && !is_wp_error($cursos)) {
        $curso_ids = wp_list_pluck($cursos, 'term_id');
        $related_args['tax_query'][] = array(
            'taxonomy' => 'curso',
            'field' => 'term_id',
            'terms' => $curso_ids,
        );
    }

    $related_query = new WP_Query($related_args);

    if ($related_query->have_posts()): ?>
        <div class="fichas-relacionadas-box">
            <div class="relacionadas-header">
                <h2 class="relacionadas-titulo">💡 Te pueden interesar estas fichas</h2>
               
            </div>
            
            <div class="fichas-carousel-wrapper">
                <div class="fichas-relacionadas-carousel">
                    <?php while ($related_query->have_posts()): $related_query->the_post(); 
                        $rel_edad = get_field('edad_recomendada');
                        $rel_tiempo = get_field('tiempo_estimado');
                    ?>
                        <article class="ficha-relacionada-card">
                            <a href="<?php the_permalink(); ?>" class="card-link">
                                <h3 class="card-titulo"><?php the_title(); ?></h3>
                                <div class="card-meta">
                                    <?php if ($rel_edad): ?>
                                        <span class="card-meta-item">👶 <?php echo esc_html($rel_edad); ?></span>
                                    <?php endif; ?>
                                    <?php if ($rel_tiempo): ?>
                                        <span class="card-meta-item">⏱️ <?php echo esc_html($rel_tiempo); ?> min</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
                 <div class="carousel-controls">
                    <button class="carousel-btn carousel-prev" aria-label="Anterior">
                        <span class="carousel-arrow">←</span>
                    </button>
                    <button class="carousel-btn carousel-next" aria-label="Siguiente">
                        <span class="carousel-arrow">→</span>
                    </button>
                </div>
            </div>
        </div>
    <?php 
    endif;
    wp_reset_postdata();
    ?>

    <?php endwhile; ?>

    <?php if (current_user_can('administrator')): ?>
    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        <button id="generarImagenPinterest" class="btn-generar-pinterest" style="padding: 12px 24px; background: #e60023; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
            📌 Generar Imagen Pinterest
        </button>
        <p style="font-size: 12px; margin-top: 8px; color: #666;">Captura la ficha educativa como imagen para Pinterest</p>
    </div>
<?php endif; ?>

</main>

<?php get_footer(); ?>

