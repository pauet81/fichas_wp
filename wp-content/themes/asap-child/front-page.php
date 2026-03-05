<?php
/**
 * Template Name: Homepage
 * 
 * @package Fichas_Theme
 * @version 3.0 - Bloques ACF Integrados
 */

get_header(); ?>

<main id="main-content" class="site-main">
    <div class="home-wrapper">

<!-- ============================================
     1. HERO SECTION
     ============================================ -->
<section class="hero-home">
    <div class="hero-content">
        <?php 
        // Obtener H1 y descripción desde ACF
        $hero_titulo = get_field('hero_titulo');
        $hero_descripcion = get_field('hero_descripcion');
        ?>
        
        <h1><?php echo $hero_titulo ? esc_html($hero_titulo) : 'Fichas Interactivas con Corrección Automática'; ?></h1>
        
        <p class="hero-descripcion">
            <?php echo $hero_descripcion ? esc_html($hero_descripcion) : 'Recursos educativos interactivos para Infantil, Primaria y Secundaria'; ?>
        </p>
        
        <div class="hero-features">
            <div class="hero-feature-item">
                <span>✅</span>
                <span>Corrección automática</span>
            </div>
            <div class="hero-feature-item">
                <span>📄</span>
                <span>Descarga en PDF</span>
            </div>
            <div class="hero-feature-item">
                <span>🎯</span>
                <span>Sin registro</span>
            </div>
        </div>

      <!-- CTAs por Nivel Educativo -->
<div class="hero-niveles-cta">
    <!-- Infantil - ACTIVO -->
    <a href="/infantil/" class="nivel-cta-card nivel-activo">
        <?php
        $infantil_page = get_page_by_path('infantil');
        $infantil_img = $infantil_page ? get_the_post_thumbnail_url($infantil_page->ID, 'medium_large') : '';
        ?>
        <span class="nivel-icono nivel-thumb">
            <?php if ($infantil_img): ?>
                <img src="<?php echo esc_url($infantil_img); ?>" alt="Infantil">
            <?php else: ?>
                &#x1F9D2;
            <?php endif; ?>
        </span>
        <span class="nivel-nombre">Infantil</span>
        <span class="nivel-edad">3-6 a&ntilde;os</span>
        <span class="nivel-count"><?php 
            $count_infantil = new WP_Query(array(
                'post_type' => 'ficha',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'tax_query' => array(array(
                    'taxonomy' => 'nivel-educativo',
                    'field' => 'slug',
                    'terms' => 'infantil'
                ))
            ));
            echo $count_infantil->found_posts;
            wp_reset_postdata();
        ?> fichas</span>
    </a>

    <!-- Primaria - ACTIVO -->
    <a href="/primaria/" class="nivel-cta-card nivel-activo">
        <?php
        $primaria_page = get_page_by_path('primaria');
        $primaria_img = $primaria_page ? get_the_post_thumbnail_url($primaria_page->ID, 'medium_large') : '';
        ?>
        <span class="nivel-icono nivel-thumb">
            <?php if ($primaria_img): ?>
                <img src="<?php echo esc_url($primaria_img); ?>" alt="Primaria">
            <?php else: ?>
                &#x1F4DA;
            <?php endif; ?>
        </span>
        <span class="nivel-nombre">Primaria</span>
        <span class="nivel-edad">6-12 a&ntilde;os</span>
        <span class="nivel-count"><?php 
            $count_primaria = new WP_Query(array(
                'post_type' => 'ficha',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'tax_query' => array(array(
                    'taxonomy' => 'nivel-educativo',
                    'field' => 'slug',
                    'terms' => 'primaria'
                ))
            ));
            echo $count_primaria->found_posts;
            wp_reset_postdata();
        ?> fichas</span>
    </a>

    <!-- Secundaria - Pr�ximamente -->
    <div class="nivel-cta-card nivel-proximo">
        <?php
        $secundaria_page = get_page_by_path('secundaria');
        $secundaria_img = $secundaria_page ? get_the_post_thumbnail_url($secundaria_page->ID, 'medium_large') : '';
        ?>
        <span class="nivel-icono nivel-thumb">
            <?php if ($secundaria_img): ?>
                <img src="<?php echo esc_url($secundaria_img); ?>" alt="Secundaria">
            <?php else: ?>
                &#x1F393;
            <?php endif; ?>
        </span>
        <span class="nivel-nombre">Secundaria</span>
        <span class="nivel-edad">12-16 a&ntilde;os</span>
        <span class="nivel-badge-prox">Proximamente</span>
    </div>
</div>

</section>



        <!-- ============================================
             2. BLOQUE ACF: ¿Qué son las Fichas Interactivas?
             ============================================ -->
        <?php
        $bloque_que_son = get_field('bloque_que_son');
        if ($bloque_que_son && $bloque_que_son['mostrar']) : ?>
            <section class="bloque-que-son-fichas">
                <div class="contenido-wrapper">
                    <?php if (!empty($bloque_que_son['titulo'])) : ?>
                        <h2 class="section-titulo"><?php echo esc_html($bloque_que_son['titulo']); ?></h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($bloque_que_son['contenido'])) : ?>
                        <div class="que-son-contenido">
                            <?php echo wp_kses_post($bloque_que_son['contenido']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

       <!-- ============================================
     3. NIVELES EDUCATIVOS CON IMÁGENES
     ============================================ -->
<section class="niveles-educativos-home">
    <h2 class="section-titulo">Nivel Educativo</h2>
    <p class="section-subtitulo">Elige el nivel educativo para explorar las fichas</p>

    <div class="niveles-grid">
        <?php
        // Array de configuración de niveles
        $niveles_config = array(
            array(
    'slug' => 'infantil',
    'titulo' => 'Infantil',
    'subtitulo' => '3-6 años',
    'descripcion' => 'Fichas adaptadas para educación infantil con actividades lúdicas y visuales.',
    'estado' => 'activo',
    'url' => '/infantil/'
),
            array(
                'slug' => 'primaria',
                'titulo' => 'Primaria',
                'subtitulo' => '6-12 años',
                'descripcion' => 'Fichas interactivas para todos los cursos de primaria organizadas por asignatura.',
                'estado' => 'activo',
                'url' => '/primaria/'
            ),
            array(
                'slug' => 'secundaria',
                'titulo' => 'Secundaria',
                'subtitulo' => '12-16 años',
                'descripcion' => 'Recursos educativos para reforzar contenidos de la ESO.',
                'estado' => 'proximamente',
                'url' => '#' // Cambiar cuando esté activo
            )
        );

        foreach ($niveles_config as $nivel):
            // Buscar la página por slug
            $nivel_page = get_page_by_path($nivel['slug']);
            $imagen_destacada = '';
            
            if ($nivel_page) {
                $imagen_destacada = get_the_post_thumbnail_url($nivel_page->ID, 'large');
            }
            
            // Clases CSS según estado
            $card_class = 'nivel-card-imagen';
            if ($nivel['estado'] === 'activo') {
                $card_class .= ' nivel-activo';
            } else {
                $card_class .= ' nivel-disabled';
            }
            
            // Wrapper (enlace o div)
            $tag_open = ($nivel['estado'] === 'activo') ? '<a href="' . esc_url($nivel['url']) . '" class="' . $card_class . '">' : '<div class="' . $card_class . '">';
            $tag_close = ($nivel['estado'] === 'activo') ? '</a>' : '</div>';
            
            echo $tag_open;
            ?>
            
            <!-- Badge de estado -->
            <?php if ($nivel['estado'] === 'activo'): ?>
                <span class="nivel-badge badge-activa">Disponible</span>
            <?php else: ?>
                <span class="nivel-badge badge-prox">Proximamente</span>
            <?php endif; ?>
            
            <!-- Imagen destacada con overlay -->
            <div class="nivel-imagen-wrapper">
                <?php if ($imagen_destacada): ?>
                    <img src="<?php echo esc_url($imagen_destacada); ?>" alt="<?php echo esc_attr($nivel['titulo']); ?>" class="nivel-imagen-bg">
                    <div class="nivel-overlay"></div>
                <?php else: ?>
                    <!-- Fallback si no hay imagen -->
                    <div class="nivel-imagen-fallback">
                        <span class="nivel-fallback-icon">
                            <?php 
                            $iconos = array('infantil' => '🧸', 'primaria' => '📚', 'secundaria' => '🎓');
                            echo isset($iconos[$nivel['slug']]) ? $iconos[$nivel['slug']] : '📖';
                            ?>
                        </span>
                    </div>
                    <div class="nivel-overlay"></div>
                <?php endif; ?>
            </div>
            
            <!-- Contenido sobre la imagen -->
            <div class="nivel-contenido">
                <h3 class="nivel-titulo"><?php echo esc_html($nivel['titulo']); ?></h3>
                <p class="nivel-subtitulo"><?php echo esc_html($nivel['subtitulo']); ?></p>
                <p class="nivel-descripcion"><?php echo esc_html($nivel['descripcion']); ?></p>
                
                <?php if ($nivel['estado'] === 'activo'): ?>
                    <span class="nivel-cta-texto">Explorar fichas →</span>
                <?php endif; ?>
            </div>
            
            <?php
            echo $tag_close;
        endforeach;
        ?>
    </div>
</section>

  <!-- ============================================
     4. FICHAS DESTACADAS
     ============================================ -->
<section class="fichas-destacadas-home">
    <h2 class="section-titulo">Fichas Destacadas</h2>
    <p class="section-subtitulo">Las fichas más populares de nuestra colección</p>

    <div class="fichas-grid">
        <?php
        $fichas_destacadas = new WP_Query(array(
    'post_type' => 'ficha',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'meta_key' => 'fichas_vistas',
    'orderby' => 'meta_value_num',
    'order' => 'DESC'
));

        if ($fichas_destacadas->have_posts()) :
            while ($fichas_destacadas->have_posts()) : $fichas_destacadas->the_post();
                // Obtener metadatos
                $edad = get_field('edad_recomendada');
                $tiempo = get_field('tiempo_estimado');
                
                // Obtener taxonomías
                $asignaturas_ficha = get_the_terms(get_the_ID(), 'asignatura');
                $dificultad = get_the_terms(get_the_ID(), 'dificultad');
                $nivel = get_the_terms(get_the_ID(), 'nivel-educativo');
                $curso = get_the_terms(get_the_ID(), 'curso');
                
                // Determinar color del ribete según nivel educativo
                $nivel_class = 'nivel-primaria'; // default
                if ($nivel && !is_wp_error($nivel)) {
                    $nivel_slug = $nivel[0]->slug;
                    if ($nivel_slug === 'infantil') {
                        $nivel_class = 'nivel-infantil';
                    } elseif ($nivel_slug === 'secundaria') {
                        $nivel_class = 'nivel-secundaria';
                    }
                }
                ?>
                <div class="ficha-card-mini-v2 <?php echo $nivel_class; ?>">
    <a href="<?php the_permalink(); ?>" class="ficha-card-link-v2">
        
        <!-- Header con badges y altura fija -->
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
            <!-- ✅ NUEVO: Badge de veces realizada -->
    <div class="badge-vistas-card">
        👁️ <?php echo fichas_format_vistas(get_the_ID()); ?>
    </div>
        </div>
        
        <!-- Cuerpo de la card -->
        <div class="ficha-card-body-v2">
            <!-- Título centrado -->
            <h3 class="ficha-card-titulo-v2"><?php the_title(); ?></h3>
            
            <!-- Meta información: edad + tiempo + dificultad en la misma línea -->
            <div class="ficha-card-meta-v2">
                <?php if ($edad): ?>
                    <span class="meta-item-v2">
                        <span class="meta-icon">👤</span>
                        <span class="meta-text"><?php echo esc_html($edad); ?> años</span>
                    </span>
                <?php endif; ?>
                
                <?php if ($tiempo): ?>
                    <span class="meta-item-v2">
                        <span class="meta-icon">⏱️</span>
                        <span class="meta-text"><?php echo esc_html($tiempo); ?> min</span>
                    </span>
                <?php endif; ?>
                
                <?php if ($dificultad && !is_wp_error($dificultad)): ?>
                    <span class="meta-item-v2">
                        <span class="meta-icon">⭐</span>
                        <span class="meta-text"><?php echo esc_html($dificultad[0]->name); ?></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- CTA -->
        <div class="ficha-card-footer-v2">
            <span class="ficha-card-cta-v2">
                <span class="cta-text">Empezar ficha</span>
                <span class="cta-arrow">→</span>
            </span>
        </div>
    </a>
</div>

            <?php endwhile;
            wp_reset_postdata();
        else : ?>
            <p class="no-fichas">No hay fichas disponibles en este momento.</p>
        <?php endif; ?>
    </div>

    <div class="fichas-destacadas-cta">
        <a href="/infantil/" class="cta-primary">Ver Fichas de Infantil</a>
        <a href="/primaria/" class="cta-primary">Ver Fichas de Primaria</a>
        <a href="/secundaria/" class="cta-primary">Ver Fichas de Secundaria</a>
    </div>
</section>



        <!-- ============================================
             5. BLOQUE ACF: Ventajas de las Fichas Interactivas
             ============================================ -->
        <?php
        $bloque_ventajas = get_field('bloque_ventajas_primaria');
        if ($bloque_ventajas && $bloque_ventajas['mostrar']) : ?>
            <section class="ventajas-fichas">
                <div class="contenido-wrapper">
                    <?php if (!empty($bloque_ventajas['titulo'])) : ?>
                        <h2 class="section-titulo"><?php echo esc_html($bloque_ventajas['titulo']); ?></h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($bloque_ventajas['introduccion'])) : ?>
                        <div class="ventajas-intro">
                            <?php echo wp_kses_post($bloque_ventajas['introduccion']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($bloque_ventajas['ventajas_lista'])) : ?>
                        <div class="ventajas-grid">
                            <?php 
                            $count = 1;
                            foreach ($bloque_ventajas['ventajas_lista'] as $ventaja) : ?>
                                <div class="ventaja-item">
                                    <div class="ventaja-icon ventaja-icon-<?php echo $count; ?>">
                                        <?php echo !empty($ventaja['icono']) ? esc_html($ventaja['icono']) : '✅'; ?>
                                    </div>
                                    <?php if (!empty($ventaja['titulo'])) : ?>
                                        <h3 class="ventaja-titulo"><?php echo esc_html($ventaja['titulo']); ?></h3>
                                    <?php endif; ?>
                                    <?php if (!empty($ventaja['descripcion'])) : ?>
                                        <p class="ventaja-descripcion"><?php echo esc_html($ventaja['descripcion']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php 
                            $count++;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- ============================================
             7. BLOQUE ACF: C�mo Funcionan las Fichas
             ============================================ -->
        <?php
        $bloque_como_funciona = get_field('bloque_como_funciona');
        if ($bloque_como_funciona && $bloque_como_funciona['mostrar']) : ?>
            <section class="bloque-como-funciona">
                <div class="contenido-wrapper">
                    <?php if (!empty($bloque_como_funciona['titulo'])) : ?>
                        <h2 class="section-titulo"><?php echo esc_html($bloque_como_funciona['titulo']); ?></h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($bloque_como_funciona['pasos'])) : ?>
                        <div class="pasos-grid">
                            <?php 
                            $paso_num = 1;
                            foreach ($bloque_como_funciona['pasos'] as $paso) : ?>
                                <div class="paso-item">
                                    <div class="paso-numero"><?php echo $paso_num; ?></div>
                                    <div class="paso-contenido">
                                        <?php if (!empty($paso['titulo'])) : ?>
                                            <h3 class="paso-titulo"><?php echo esc_html($paso['titulo']); ?></h3>
                                        <?php endif; ?>
                                        <?php if (!empty($paso['descripcion'])) : ?>
                                            <p class="paso-descripcion"><?php echo esc_html($paso['descripcion']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php 
                            $paso_num++;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

       <?php
// BLOQUE ACF: Mini-FAQ
$bloque_faq = get_field('bloque_mini_faq');
if ($bloque_faq && $bloque_faq['mostrar'] && !empty($bloque_faq['preguntas'])) : ?>
    <section class="bloque-mini-faq">
        <div class="contenido-wrapper">
            <?php if (!empty($bloque_faq['titulo'])) : ?>
                <h2 class="section-titulo"><?php echo esc_html($bloque_faq['titulo']); ?></h2>
            <?php endif; ?>
            
            <div class="faq-acordeon">
                <?php foreach ($bloque_faq['preguntas'] as $index => $faq) : ?>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span class="faq-pregunta-texto"><?php echo esc_html($faq['pregunta']); ?></span>
                            <span class="faq-icono" aria-hidden="true">+</span>
                        </button>
                        <div class="faq-answer">
                            <?php echo wp_kses_post($faq['respuesta']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($bloque_faq['link_faq_completo'])) : ?>
                <p class="faq-link-completo">
                    <a href="<?php echo esc_url($bloque_faq['link_faq_completo']); ?>">
                        <?php echo !empty($bloque_faq['link_texto']) ? esc_html($bloque_faq['link_texto']) : 'Ver todas las preguntas frecuentes'; ?> →
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </section>
    
    <?php 
    // Schema Markup JSON-LD para FAQs
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
<?php endif; ?>

    </div> <!-- .home-wrapper -->
</main>

<?php get_footer(); ?>


