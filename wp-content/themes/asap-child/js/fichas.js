(function() {
    'use strict';

    if (typeof window === 'undefined') return;

    // =============================
    // Progress system (local)
    // =============================
    var PROGRESS_KEY = 'fichas_progress_v1';
    var POINTS_PER_CORRECT = 10;
    var BONUS_COMPLETE = 50;
    var POINTS_PER_LEVEL = 100;

    function getProgress() {
        try {
            var raw = localStorage.getItem(PROGRESS_KEY);
            if (raw) return JSON.parse(raw);
        } catch (e) {}
        return { points: 0, level: 1, totalCorrect: 0, totalCompleted: 0, updatedAt: null };
    }

    function setProgress(data) {
        try {
            localStorage.setItem(PROGRESS_KEY, JSON.stringify(data));
        } catch (e) {}
    }

    function recalcLevel(points) {
        var lvl = Math.floor(points / POINTS_PER_LEVEL) + 1;
        return lvl < 1 ? 1 : lvl;
    }

    function updateProgressUI(animate) {
        var badge = document.getElementById('fichas-progress-badge');
        if (!badge) return;
        var data = getProgress();
        data.level = recalcLevel(data.points);
        var levelEl = badge.querySelector('.level-value');
        var pointsEl = badge.querySelector('.points-value');
        var fill = badge.querySelector('.progress-fill');
        if (levelEl) levelEl.textContent = data.level;
        if (pointsEl) pointsEl.textContent = data.points;
        if (fill) {
            var mod = data.points % POINTS_PER_LEVEL;
            var pct = Math.round((mod / POINTS_PER_LEVEL) * 100);
            fill.style.width = pct + '%';
        }
        if (animate) {
            badge.classList.add('is-pulse');
            setTimeout(function(){ badge.classList.remove('is-pulse'); }, 600);
            spawnStars(badge);
        }
    }

    function addProgress(correctCount, totalCount) {
        var data = getProgress();
        var points = correctCount * POINTS_PER_CORRECT;
        var prevLevel = data.level;
        if (totalCount && correctCount === totalCount) {
            points += BONUS_COMPLETE;
            data.totalCompleted += 1;
        }
        data.totalCorrect += correctCount;
        data.points += points;
        data.level = recalcLevel(data.points);
        data.updatedAt = Date.now();
        setProgress(data);
        updateProgressUI(true);
        if (data.level > prevLevel) {
            spawnConfetti(document.getElementById('fichas-progress-badge'));
        }
    }

    function spawnStars(container) {
        if (!container) return;
        var burst = document.createElement('div');
        burst.className = 'fichas-burst';
        var rect = container.getBoundingClientRect();
        for (var i = 0; i < 8; i++) {
            var star = document.createElement('span');
            star.className = 'fichas-star';
            var x = Math.random() * rect.width;
            var y = Math.random() * rect.height;
            star.style.left = x + 'px';
            star.style.top = y + 'px';
            burst.appendChild(star);
        }
        container.appendChild(burst);
        setTimeout(function(){ burst.remove(); }, 900);
    }

    function spawnConfetti(container) {
        if (!container) return;
        var burst = document.createElement('div');
        burst.className = 'fichas-burst';
        var colors = ['#34d399', '#60a5fa', '#f59e0b', '#f472b6', '#a78bfa'];
        for (var i = 0; i < 14; i++) {
            var conf = document.createElement('span');
            conf.className = 'fichas-confetti';
            conf.style.background = colors[i % colors.length];
            var dx = (Math.random() * 2 - 1) * 80;
            var dy = (Math.random() * -1) * 90 - 20;
            conf.style.setProperty('--dx', dx + 'px');
            conf.style.setProperty('--dy', dy + 'px');
            conf.style.left = '50%';
            conf.style.top = '50%';
            burst.appendChild(conf);
        }
        container.appendChild(burst);
        setTimeout(function(){ burst.remove(); }, 1400);
    }

    // Force navigation on megamenu links in case other handlers block it
    function forceMegaNavigation(e) {
        var link = e.target && e.target.closest ? e.target.closest('.mega-panel a') : null;
        if (link && link.href) {
            e.preventDefault();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
            e.stopPropagation();
            window.location.assign(link.href);
        }
    }
    document.addEventListener('mousedown', forceMegaNavigation, true);
    document.addEventListener('click', forceMegaNavigation, true);

    function updateHeaderOffsets() {
        var header = document.querySelector('.site-header');
        if (header) {
            var headerHeight = Math.ceil(header.getBoundingClientRect().height || 0);
            if (headerHeight) {
                document.documentElement.style.setProperty('--fichas-header-height', headerHeight + 'px');
            }
        }

        var adminBar = document.getElementById('wpadminbar');
        if (adminBar) {
            var adminHeight = Math.ceil(adminBar.getBoundingClientRect().height || 0);
            document.documentElement.style.setProperty('--fichas-adminbar-height', adminHeight + 'px');
        } else {
            document.documentElement.style.setProperty('--fichas-adminbar-height', '0px');
        }

        var breadcrumbs = document.querySelector('.site-breadcrumbs');
        if (breadcrumbs) {
            var bcHeight = Math.ceil(breadcrumbs.getBoundingClientRect().height || 0);
            document.documentElement.style.setProperty('--fichas-breadcrumb-height', bcHeight + 'px');
        } else {
            document.documentElement.style.setProperty('--fichas-breadcrumb-height', '0px');
        }
    }

    function setupProgressBadgePlacement() {
        var badge = document.getElementById('fichas-progress-badge');
        if (!badge) return;
        var originalParent = badge.parentNode;
        var originalNext = badge.nextSibling;

        function placeBadge() {
            var isMobile = window.matchMedia && window.matchMedia('(max-width: 1050px)').matches;
            if (isMobile) {
                // Do not move DOM on mobile to avoid visual jump; CSS handles fixed position.
            } else {
                var header = document.querySelector('.site-header-content');
                var logo = header ? header.querySelector('.site-logo, .site-name') : null;
                if (header && logo) {
                    if (badge.parentNode !== header || badge.previousSibling !== logo) {
                        header.insertBefore(badge, logo.nextSibling);
                    }
                } else if (originalParent && badge.parentNode !== originalParent) {
                    if (originalNext && originalNext.parentNode === originalParent) {
                        originalParent.insertBefore(badge, originalNext);
                    } else {
                        originalParent.appendChild(badge);
                    }
                }
            }
            requestAnimationFrame(function() {
                badge.classList.add('is-ready');
            });
            updateHeaderOffsets();
        }

        document.addEventListener('DOMContentLoaded', function() {
            placeBadge();
            setTimeout(updateHeaderOffsets, 200);
        });
        window.addEventListener('resize', placeBadge);

        // Ensure immediate placement if DOM is already ready
        if (document.readyState !== 'loading') {
            placeBadge();
        }
    }

    document.addEventListener('DOMContentLoaded', updateHeaderOffsets);
    window.addEventListener('load', updateHeaderOffsets);
    window.addEventListener('resize', updateHeaderOffsets);
    document.addEventListener('DOMContentLoaded', updateProgressUI);
    document.addEventListener('DOMContentLoaded', setupProgressBadgePlacement);

    function setupOffcanvasMenu() {
        var trigger = document.getElementById('nav-icon');
        var offcanvas = document.getElementById('mobile-offcanvas');
        var overlay = document.getElementById('mobile-offcanvas-overlay');
        var closeBtn = offcanvas ? offcanvas.querySelector('.mobile-offcanvas-close') : null;
        var targetNav = offcanvas ? offcanvas.querySelector('.mobile-offcanvas-nav') : null;
        var sourceMenu = document.querySelector('.mega-menu');

        if (!trigger || !offcanvas || !overlay || !targetNav) return;

        function openMenu() {
            document.body.classList.add('menu-open');
            offcanvas.setAttribute('aria-hidden', 'false');
            overlay.setAttribute('aria-hidden', 'false');
            trigger.setAttribute('aria-expanded', 'true');
            trigger.classList.add('is-open');
        }

        function closeMenu() {
            document.body.classList.remove('menu-open');
            offcanvas.setAttribute('aria-hidden', 'true');
            overlay.setAttribute('aria-hidden', 'true');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.classList.remove('is-open');
        }

        function syncMenuState() {
            var isHidden = offcanvas.getAttribute('aria-hidden') === 'true';
            if (isHidden) {
                document.body.classList.remove('menu-open');
                trigger.setAttribute('aria-expanded', 'false');
                trigger.classList.remove('is-open');
            }
        }

        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            var isOpen = document.body.classList.contains('menu-open');
            if (isOpen) closeMenu();
            else openMenu();
        });
        overlay.addEventListener('click', closeMenu);
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMenu();
        });
        offcanvas.addEventListener('transitionend', syncMenuState);

        function makeUrl(path) {
            if (!path) return '#';
            if (/^https?:\/\//i.test(path)) return path;
            return window.location.origin.replace(/\/$/, '') + path;
        }

        function buildMobileMenu() {
            var container = document.createElement('div');
            container.className = 'mobile-menu';

            var blogLink = sourceMenu ? sourceMenu.querySelector('a[href*="/blog"]') : null;
            var blogHref = blogLink ? blogLink.getAttribute('href') : makeUrl('/blog/');

            var menuData = [
                {
                    title: 'Infantil',
                    href: makeUrl('/infantil/'),
                    level: 'infantil',
                    children: [
                        { title: '3 años', href: makeUrl('/infantil/3-anos/'), items: [
                            { title: 'Lengua', href: makeUrl('/infantil/3-anos/lengua/') },
                            { title: 'Inglés', href: makeUrl('/infantil/3-anos/ingles/') },
                            { title: 'Matemáticas', href: makeUrl('/infantil/3-anos/matematicas/') },
                            { title: 'Conocimiento del entorno', href: makeUrl('/infantil/3-anos/conocimiento-del-entorno/') }
                        ]},
                        { title: '4 años', href: makeUrl('/infantil/4-anos/'), items: [
                            { title: 'Lengua', href: makeUrl('/infantil/4-anos/lengua/') },
                            { title: 'Inglés', href: makeUrl('/infantil/4-anos/ingles/') },
                            { title: 'Matemáticas', href: makeUrl('/infantil/4-anos/matematicas/') },
                            { title: 'Conocimiento del entorno', href: makeUrl('/infantil/4-anos/conocimiento-del-entorno/') }
                        ]},
                        { title: '5 años', href: makeUrl('/infantil/5-anos/'), items: [
                            { title: 'Lengua', href: makeUrl('/infantil/5-anos/lengua/') },
                            { title: 'Inglés', href: makeUrl('/infantil/5-anos/ingles/') },
                            { title: 'Matemáticas', href: makeUrl('/infantil/5-anos/matematicas/') },
                            { title: 'Conocimiento del entorno', href: makeUrl('/infantil/5-anos/conocimiento-del-entorno/') }
                        ]},
                        { title: 'Temáticas', href: makeUrl('/infantil/tematicas/'), items: [
                            { title: 'Halloween', href: makeUrl('/infantil/halloween/') },
                            { title: 'Invierno', href: makeUrl('/infantil/invierno/') },
                            { title: 'Otoño', href: makeUrl('/infantil/otono/') },
                            { title: 'Primavera', href: makeUrl('/infantil/primavera/') },
                            { title: 'Verano', href: makeUrl('/infantil/verano/') }
                        ]}
                    ]
                },
                {
                    title: 'Primaria',
                    href: makeUrl('/primaria/'),
                    level: 'primaria',
                    children: [
                        { title: '1º Primaria', href: makeUrl('/primaria/1-primaria/'), items: [
                            { title: 'Matemáticas', href: makeUrl('/primaria/1-primaria/matematicas/') },
                            { title: 'Lenguaje', href: makeUrl('/primaria/1-primaria/lenguaje/') },
                            { title: 'Conocimiento del medio', href: makeUrl('/primaria/1-primaria/conocimiento-del-medio/') },
                            { title: 'Inglés', href: makeUrl('/primaria/1-primaria/ingles/') }
                        ]},
                        { title: '2º Primaria', href: makeUrl('/primaria/2-primaria/'), items: [
                            { title: 'Matemáticas', href: makeUrl('/primaria/2-primaria/matematicas/') },
                            { title: 'Lenguaje', href: makeUrl('/primaria/2-primaria/lenguaje/') },
                            { title: 'Conocimiento del medio', href: makeUrl('/primaria/2-primaria/conocimiento-del-medio/') },
                            { title: 'Inglés', href: makeUrl('/primaria/2-primaria/ingles/') }
                        ]},
                        { title: '3º Primaria', href: makeUrl('/primaria/3-primaria/'), items: [
                            { title: 'Matemáticas', href: makeUrl('/primaria/3-primaria/matematicas/') },
                            { title: 'Lenguaje', href: makeUrl('/primaria/3-primaria/lenguaje/') },
                            { title: 'Conocimiento del medio', href: makeUrl('/primaria/3-primaria/conocimiento-del-medio/') },
                            { title: 'Inglés', href: makeUrl('/primaria/3-primaria/ingles/') }
                        ]},
                        { title: '4º Primaria', href: makeUrl('/primaria/4-primaria/'), items: [
                            { title: 'Matemáticas', href: makeUrl('/primaria/4-primaria/matematicas/') },
                            { title: 'Lenguaje', href: makeUrl('/primaria/4-primaria/lenguaje/') },
                            { title: 'Conocimiento del medio', href: makeUrl('/primaria/4-primaria/conocimiento-del-medio/') },
                            { title: 'Inglés', href: makeUrl('/primaria/4-primaria/ingles/') }
                        ]},
                        { title: '5º Primaria', href: makeUrl('/primaria/5-primaria/'), items: [
                            { title: 'Matemáticas', href: makeUrl('/primaria/5-primaria/matematicas/') },
                            { title: 'Lenguaje', href: makeUrl('/primaria/5-primaria/lenguaje/') },
                            { title: 'Conocimiento del medio', href: makeUrl('/primaria/5-primaria/conocimiento-del-medio/') },
                            { title: 'Inglés', href: makeUrl('/primaria/5-primaria/ingles/') }
                        ]},
                        { title: '6º Primaria', href: makeUrl('/primaria/6-primaria/'), items: [
                            { title: 'Matemáticas', href: makeUrl('/primaria/6-primaria/matematicas/') },
                            { title: 'Lenguaje', href: makeUrl('/primaria/6-primaria/lenguaje/') },
                            { title: 'Conocimiento del medio', href: makeUrl('/primaria/6-primaria/conocimiento-del-medio/') },
                            { title: 'Inglés', href: makeUrl('/primaria/6-primaria/ingles/') }
                        ]}
                    ]
                },
                {
                    title: 'Blog',
                    href: blogHref,
                    level: 'blog',
                    children: []
                }
            ];

            menuData.forEach(function(sectionData) {
                var section = document.createElement('div');
                section.className = 'mobile-section';
                if (sectionData.level === 'infantil') section.classList.add('level-infantil');
                if (sectionData.level === 'primaria') section.classList.add('level-primaria');

                var title = document.createElement('div');
                title.className = 'mobile-section-title';
                var titleLink = document.createElement('a');
                titleLink.href = sectionData.href;
                titleLink.textContent = sectionData.title;
                title.appendChild(titleLink);
                section.appendChild(title);

                if (Array.isArray(sectionData.children) && sectionData.children.length) {
                    sectionData.children.forEach(function(child) {
                        var sub = document.createElement('div');
                        sub.className = 'mobile-subsection';

                        var subTitle = document.createElement('div');
                        subTitle.className = 'mobile-subtitle';

                        var subLink = document.createElement('a');
                        subLink.href = child.href;
                        subLink.className = 'mobile-subtitle-link';
                        subLink.textContent = child.title;
                        subTitle.appendChild(subLink);

                        if (Array.isArray(child.items) && child.items.length) {
                            var subToggle = document.createElement('button');
                            subToggle.type = 'button';
                            subToggle.className = 'mobile-toggle';
                            subToggle.setAttribute('aria-expanded', 'false');
                            subToggle.setAttribute('aria-label', 'Desplegar materias');
                            subTitle.appendChild(subToggle);
                        }

                        sub.appendChild(subTitle);

                        if (Array.isArray(child.items) && child.items.length) {
                            var newUl = document.createElement('ul');
                            newUl.className = 'mobile-sublist';
                            child.items.forEach(function(item) {
                                var li = document.createElement('li');
                                var link = document.createElement('a');
                                link.href = item.href;
                                link.textContent = item.title;
                                li.appendChild(link);
                                newUl.appendChild(li);
                            });
                            sub.appendChild(newUl);
                        }

                        section.appendChild(sub);
                    });
                }

                container.appendChild(section);
            });

            targetNav.innerHTML = '';
            targetNav.appendChild(container);

            targetNav.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', closeMenu);
            });

            targetNav.querySelectorAll('.mobile-subtitle .mobile-toggle').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var sub = btn.closest('.mobile-subsection');
                    if (!sub) return;
                    var isOpen = sub.classList.toggle('is-open');
                    btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });
        }

        buildMobileMenu();

        window.addEventListener('resize', function() {
            if (window.innerWidth > 1050) {
                closeMenu();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', setupOffcanvasMenu);

    // Hard-disable ASAP theme megamenu markup if injected
    function removeThemeMegamenu() {
        document.querySelectorAll('.asap-megamenu-overlay, .asap-megamenu-trigger, .asap-megamenu').forEach(function(el) {
            el.parentNode && el.parentNode.removeChild(el);
        });
    }

    document.addEventListener('DOMContentLoaded', removeThemeMegamenu);

    function setupDesktopMegaHover() {
        if (!window.matchMedia || !window.matchMedia('(min-width: 1051px)').matches) return;
        var items = document.querySelectorAll('.mega-menu > .menu-item.has-mega');
        items.forEach(function(item) {
            var closeTimer = null;
            function open() {
                if (closeTimer) clearTimeout(closeTimer);
                item.classList.add('is-open');
            }
            function close() {
                closeTimer = setTimeout(function() {
                    item.classList.remove('is-open');
                }, 120);
            }
            item.addEventListener('mouseenter', open);
            item.addEventListener('mouseleave', close);

            var panel = item.querySelector('.mega-panel');
            if (panel) {
                panel.addEventListener('mouseenter', open);
                panel.addEventListener('mouseleave', close);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', setupDesktopMegaHover);

    var imprimiendo = false;

    // ============================================
    // FUNCIONES INTERACTIVIDAD - VERSIÓN 3.0
    // Sin True/False - Con PDF Completo
    // Soporta: Simples, Fill, DragDrop, Memory, Trazo, WordSearch
    // ============================================

    function inicializarFichaInteractiva() {
        var fichaEducativa = document.querySelector('.ficha-educativa');
        if (!fichaEducativa) return;

        var respuestasUsuario = {
            'simple': {},
            'fill': {}
        };
        var yaComprobado = false;

        // ============================================
        // CONTAR TODOS LOS EJERCICIOS
        // ============================================
        var ejerciciosSimples = fichaEducativa.querySelectorAll('.bloque-ejercicio');
        var ejerciciosFillBlanks = fichaEducativa.querySelectorAll('.fillblank-input');
        var ejerciciosDragDrop = fichaEducativa.querySelectorAll('.ejercicio-dragdrop');
        var ejerciciosMemory = fichaEducativa.querySelectorAll('.ejercicio-memory');
        var ejerciciosTrazo = fichaEducativa.querySelectorAll('.ejercicio-trazo');
        var ejerciciosWordSearch = fichaEducativa.querySelectorAll('.ejercicio-wordsearch');

        var totalEjercicios = ejerciciosSimples.length 
            + ejerciciosFillBlanks.length 
            + ejerciciosDragDrop.length 
            + ejerciciosMemory.length 
            + ejerciciosTrazo.length 
            + ejerciciosWordSearch.length;

        // ============================================
        // PREGUNTAS SIMPLES
        // ============================================
        var opcionesBtns = fichaEducativa.querySelectorAll('.bloque-ejercicio .opcion-btn');
        opcionesBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (yaComprobado) return;

                var ejercicio = this.closest('.bloque-ejercicio');
                var ejercicioIndex = Array.from(ejercicio.parentElement.children).indexOf(ejercicio);

                var opcionesHermanas = ejercicio.querySelectorAll('.opcion-btn');
                opcionesHermanas.forEach(function(opcion) {
                    opcion.classList.remove('seleccionado');
                });

                this.classList.add('seleccionado');
                respuestasUsuario['simple'][ejercicioIndex] = this.getAttribute('data-valor');
            });
        });

        // ============================================
        // FILL IN THE BLANKS
        // ============================================
        var inputsFill = fichaEducativa.querySelectorAll('.fillblank-input');
        inputsFill.forEach(function(input, index) {
            input.addEventListener('input', function() {
                if (yaComprobado) return;
                respuestasUsuario['fill'][index] = this.value.trim();
            });

            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var btnComprobar = fichaEducativa.querySelector('.btn-comprobar');
                    if (btnComprobar) btnComprobar.click();
                }
            });
        });

        // ============================================
        // DRAG DROP - Inicializar
        // ============================================
        ejerciciosDragDrop.forEach(function(ejercicio, index) {
            inicializarDragDrop(ejercicio, index, respuestasUsuario);
        });

        // ============================================
        // MEMORY - SOLO DETECCIÓN
        // ============================================
        // (vacío intencionalmente - memory-addon.js lo inicializa)

        // ============================================
        // TRAZO - Inicializar
        // ============================================
        ejerciciosTrazo.forEach(function(ejercicio, index) {
            inicializarTrazo(ejercicio, index);
        });

        // ============================================
        // WORD SEARCH - Inicializar
        // ============================================
        ejerciciosWordSearch.forEach(function(ejercicio, index) {
            inicializarWordSearch(ejercicio, index);
        });

        // ============================================
        // BOTÓN COMPROBAR GLOBAL
        // ============================================
        var btnComprobar = fichaEducativa.querySelector('.btn-comprobar');
        if (btnComprobar) {
            btnComprobar.addEventListener('click', function() {
                if (yaComprobado) return;

                var correctas = 0;

                // COMPROBAR PREGUNTAS SIMPLES
                ejerciciosSimples.forEach(function(ejercicio, index) {
                    var respuestaCorrecta = ejercicio.getAttribute('data-respuesta-correcta');
                    var respuestaUsuario = respuestasUsuario['simple'][index];

                    var opciones = ejercicio.querySelectorAll('.opcion-btn');
                    opciones.forEach(function(opcion) {
                        var valorOpcion = opcion.getAttribute('data-valor');

                        if (valorOpcion === respuestaCorrecta) {
                            opcion.classList.add('correcto');
                        }

                        if (valorOpcion === respuestaUsuario && valorOpcion !== respuestaCorrecta) {
                            opcion.classList.add('incorrecto');
                        }
                    });

                    ejercicio.classList.remove('ejercicio-correcto', 'ejercicio-incorrecto');
                    if (respuestaUsuario === respuestaCorrecta) {
                        ejercicio.classList.add('ejercicio-correcto');
                        correctas++;
                    } else {
                        ejercicio.classList.add('ejercicio-incorrecto');
                    }
                });

                // COMPROBAR FILL IN THE BLANKS
                inputsFill.forEach(function(input, index) {
                    var respuestaCorrecta = normalizar(input.getAttribute('data-respuesta'));
                    var respuestaUsuario = normalizar(respuestasUsuario['fill'][index]);

                    input.classList.remove('correcto', 'incorrecto');

                    var bloqueFill = input.closest('.ejercicio-fillblanks');
                    if (bloqueFill) bloqueFill.classList.remove('ejercicio-correcto', 'ejercicio-incorrecto');

                    if (respuestaUsuario === respuestaCorrecta) {
                        input.classList.add('correcto');
                        if (bloqueFill) bloqueFill.classList.add('ejercicio-correcto');
                        correctas++;
                    } else {
                        input.classList.add('incorrecto');
                        if (bloqueFill) bloqueFill.classList.add('ejercicio-incorrecto');
                    }

                    input.disabled = true;
                });

                // COMPROBAR DRAG DROP
                ejerciciosDragDrop.forEach(function(ejercicio) {
                    ejercicio.classList.remove('ejercicio-correcto', 'ejercicio-incorrecto');
                    if (comprobarDragDrop(ejercicio)) {
                        ejercicio.classList.add('ejercicio-correcto');
                        correctas++;
                    } else {
                        ejercicio.classList.add('ejercicio-incorrecto');
                    }
                });

                // COMPROBAR MEMORY
                ejerciciosMemory.forEach(function(ejercicio) {
                    if (ejercicio.dataset.memoryCompleted === 'true') {
                        ejercicio.classList.remove('ejercicio-incorrecto');
                        ejercicio.classList.add('ejercicio-correcto');
                        correctas++;
                    } else {
                        ejercicio.classList.remove('ejercicio-correcto');
                        ejercicio.classList.add('ejercicio-incorrecto');
                    }
                });

                // COMPROBAR TRAZO
                ejerciciosTrazo.forEach(function(ejercicio) {
                    ejercicio.classList.remove('ejercicio-correcto', 'ejercicio-incorrecto');
                    if (comprobarTrazo(ejercicio)) {
                        ejercicio.classList.add('ejercicio-correcto');
                        correctas++;
                    } else {
                        ejercicio.classList.add('ejercicio-incorrecto');
                    }
                });

                // COMPROBAR WORD SEARCH
                ejerciciosWordSearch.forEach(function(ejercicio) {
                    ejercicio.classList.remove('ejercicio-correcto', 'ejercicio-incorrecto');
                    if (comprobarWordSearch(ejercicio)) {
                        ejercicio.classList.add('ejercicio-correcto');
                        correctas++;
                    } else {
                        ejercicio.classList.add('ejercicio-incorrecto');
                    }
                });

                // MOSTRAR FEEDBACK GLOBAL
                var feedback = fichaEducativa.querySelector('.feedback');
                if (feedback) {
                    var porcentaje = Math.round((correctas / totalEjercicios) * 100);

                    if (porcentaje >= 80) {
                        feedback.className = 'feedback exito';
                        feedback.textContent = '¡Excelente! Has acertado ' + correctas + ' de ' + totalEjercicios + ' ejercicios. ¡Sigue así!';
                    } else if (porcentaje >= 60) {
                        feedback.className = 'feedback mejorar';
                        feedback.textContent = '¡Bien! Has acertado ' + correctas + ' de ' + totalEjercicios + ' ejercicios. Puedes mejorar.';
                    } else {
                        feedback.className = 'feedback mejorar';
                        feedback.textContent = 'Has acertado ' + correctas + ' de ' + totalEjercicios + ' ejercicios. ¡Inténtalo de nuevo!';
                    }

                    feedback.style.display = 'block';
                }

                yaComprobado = true;
                this.disabled = true;
                this.textContent = 'Respuestas comprobadas';
                this.style.opacity = '0.6';
                this.style.cursor = 'not-allowed';

                // Update progress points
                addProgress(correctas, totalEjercicios);

                mostrarBotonReiniciar();
            });
        }
    }

    // ============================================
    // FUNCIÓN AUXILIAR: Normalizar texto
    // ============================================
    function normalizar(texto) {
        if (!texto) return '';
        return texto
            .toLowerCase()
            .trim()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    // ============================================
    // DRAG DROP - Funciones
    // ============================================
    function inicializarDragDrop(ejercicio, index, respuestasUsuario) {
        var items = ejercicio.querySelectorAll('.dragdrop-item');
        var zones = ejercicio.querySelectorAll('.dragdrop-zone');

        items.forEach(function(item) {
            item.draggable = true;

            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', item.getAttribute('data-valor'));
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', function() {
                item.classList.remove('dragging');
            });
        });

        zones.forEach(function(zone) {
            var zoneContent = zone.querySelector('.dragdrop-zone-content');
            if (!zoneContent) return;

            zoneContent.addEventListener('dragover', function(e) {
                e.preventDefault();
                zone.classList.add('dragover');
            });

            zoneContent.addEventListener('dragleave', function() {
                zone.classList.remove('dragover');
            });

            zoneContent.addEventListener('drop', function(e) {
                e.preventDefault();
                zone.classList.remove('dragover');

                var valorItem = e.dataTransfer.getData('text/plain');
                var itemArrastrado = ejercicio.querySelector('.dragdrop-item[data-valor="' + valorItem + '"]');

                if (itemArrastrado) {
                    this.appendChild(itemArrastrado);
                    itemArrastrado.classList.add('dropped');
                }
            });
        });

        ejercicio.dataset.dragdropIndex = index;
    }

    function comprobarDragDrop(ejercicio) {
        var zones = ejercicio.querySelectorAll('.dragdrop-zone');
        var todasCorrectas = true;

        zones.forEach(function(zone) {
            var respuestaCorrecta = zone.getAttribute('data-respuesta-correcta');
            var zoneContent = zone.querySelector('.dragdrop-zone-content');
            var itemsEnZona = zoneContent ? zoneContent.querySelectorAll('.dragdrop-item') : [];

            if (itemsEnZona.length === 0) {
                zone.classList.add('incorrect');
                todasCorrectas = false;
                return;
            }

            var zoneCorrecta = true;
            itemsEnZona.forEach(function(item) {
                var valorItem = item.getAttribute('data-valor');
                var respuestasValidas = respuestaCorrecta.split(',').map(function(r) {
                    return r.trim();
                });

                if (respuestasValidas.includes(valorItem)) {
                    item.classList.add('correcto');
                } else {
                    item.classList.add('incorrecto');
                    zoneCorrecta = false;
                    todasCorrectas = false;
                }
            });

            if (zoneCorrecta) {
                zone.classList.add('correct');
            } else {
                zone.classList.add('incorrect');
            }
        });

        return todasCorrectas;
    }

    // ============================================
    // TRAZO - Funciones
    // ============================================
    function inicializarTrazo(ejercicio, index) {
        var canvas = ejercicio.querySelector('canvas');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        var dibujando = false;
        var trazosRealizados = 0;

        function ajustarCanvas() {
            var rect = canvas.getBoundingClientRect();
            var dpr = window.devicePixelRatio || 1;
            canvas.width = Math.round(rect.width * dpr);
            canvas.height = Math.round(rect.height * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }

        ajustarCanvas();
        window.addEventListener('resize', ajustarCanvas);

        ctx.lineWidth = 6;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#2571A3';

        function empezarDibujo(e) {
            dibujando = true;
            var pos = obtenerPosicion(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function dibujar(e) {
            if (!dibujando) return;
            var pos = obtenerPosicion(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            trazosRealizados++;
        }

        function terminarDibujo() {
            dibujando = false;
        }

        function obtenerPosicion(e) {
            var rect = canvas.getBoundingClientRect();
            var clientX = (e.clientX !== undefined) ? e.clientX : e.touches[0].clientX;
            var clientY = (e.clientY !== undefined) ? e.clientY : e.touches[0].clientY;
            var x = clientX - rect.left;
            var y = clientY - rect.top;
            return { x: x, y: y };
        }

        canvas.addEventListener('mousedown', empezarDibujo);
        canvas.addEventListener('mousemove', dibujar);
        canvas.addEventListener('mouseup', terminarDibujo);
        canvas.addEventListener('mouseout', terminarDibujo);

        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            empezarDibujo(e);
        });
        canvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
            dibujar(e);
        });
        canvas.addEventListener('touchend', terminarDibujo);

        var btnBorrar = ejercicio.querySelector('.btn-borrar-trazo');
        if (btnBorrar) {
            btnBorrar.addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                trazosRealizados = 0;
            });
        }

        ejercicio.dataset.trazoIndex = index;
        ejercicio.trazosRealizados = function() {
            return trazosRealizados;
        };
    }

    function comprobarTrazo(ejercicio) {
        var canvas = ejercicio.querySelector('canvas');
        if (!canvas) return false;

        var ctx = canvas.getContext('2d');
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var pixeles = imageData.data;
        var pixelesConColor = 0;

        for (var i = 0; i < pixeles.length; i += 4) {
            if (pixeles[i + 3] > 0) {
                pixelesConColor++;
            }
        }

        return pixelesConColor > 100;
    }

    // ============================================
    // WORD SEARCH - Funciones
    // ============================================
    function inicializarWordSearch(ejercicio, index) {
        var table = ejercicio.querySelector('.wordsearch-table');
        if (!table) return;

        var palabrasObjetivo = [];
        try {
            var rawWords = table.getAttribute('data-words') || '[]';
            palabrasObjetivo = JSON.parse(rawWords);
        } catch (e) {
            var fallback = table.getAttribute('data-words');
            if (fallback) {
                palabrasObjetivo = fallback.split(',').map(function(w) { return w.trim(); });
            }
        }
        if (!palabrasObjetivo.length) {
            var wordItems = ejercicio.querySelectorAll('.word-item');
            palabrasObjetivo = Array.from(wordItems).map(function(item) {
                return item.textContent.trim();
            });
        }
        var palabrasEncontradas = [];
        var isSelecting = false;
        var selectedCells = [];

        var cells = table.querySelectorAll('.wordsearch-cell');

        cells.forEach(function(cell) {
            cell.addEventListener('mousedown', function(e) {
                e.preventDefault();
                isSelecting = true;
                selectedCells = [cell];
                cell.classList.add('selected');
            });

            cell.addEventListener('mouseover', function() {
                if (isSelecting && !cell.classList.contains('found')) {
                    if (!selectedCells.includes(cell)) {
                        selectedCells.push(cell);
                        cell.classList.add('selected');
                    }
                }
            });
        });

        document.addEventListener('mouseup', function() {
            if (!isSelecting) return;
            isSelecting = false;

            var texto = selectedCells.map(function(c) {
                return c.textContent.trim();
            }).join('');

            var textoInverso = texto.split('').reverse().join('');

            var palabraEncontrada = palabrasObjetivo.find(function(p) {
                return p.toUpperCase() === texto || p.toUpperCase() === textoInverso;
            });

            if (palabraEncontrada && !palabrasEncontradas.includes(palabraEncontrada)) {
                palabrasEncontradas.push(palabraEncontrada);

                selectedCells.forEach(function(c) {
                    c.classList.remove('selected');
                    c.classList.add('found');
                });

                var wordItems = ejercicio.querySelectorAll('.word-item');
                wordItems.forEach(function(item) {
                    if (item.textContent.toUpperCase().trim() === palabraEncontrada.toUpperCase()) {
                        item.classList.add('found');
                    }
                });
            } else {
                selectedCells.forEach(function(c) {
                    c.classList.remove('selected');
                });
            }

            selectedCells = [];
        });

        ejercicio.dataset.wordsearchIndex = index;
        ejercicio.palabrasEncontradas = palabrasEncontradas;
        ejercicio.dataset.palabrasTotal = palabrasObjetivo.length;
    }

    function comprobarWordSearch(ejercicio) {
        var palabrasTotal = parseInt(ejercicio.dataset.palabrasTotal, 10) || 0;
        var foundItems = ejercicio.querySelectorAll('.word-item.found');
        if (foundItems.length && palabrasTotal) {
            return foundItems.length >= palabrasTotal;
        }
        var palabrasEncontradas = ejercicio.palabrasEncontradas || [];
        return palabrasTotal > 0 && palabrasEncontradas.length === palabrasTotal;
    }

    // ============================================
    // FUNCIÓN REINICIAR FICHA
    // ============================================
    function mostrarBotonReiniciar() {
        var acciones = document.querySelector('.acciones');
        if (!acciones) return;

        var btnExistente = document.getElementById('btn-reiniciar-ficha');
        if (btnExistente) return;

        var btnReiniciar = document.createElement('button');
        btnReiniciar.id = 'btn-reiniciar-ficha';
        btnReiniciar.className = 'btn-reiniciar';
        btnReiniciar.textContent = '🔄 Reiniciar Ficha';
        btnReiniciar.style.cssText = 'padding: 14px 28px; border: none; border-radius: 10px; font-weight: 700; font-size: 1em; cursor: pointer; background: #667eea; color: white; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); margin-left: 15px;';

        btnReiniciar.addEventListener('mouseenter', function() {
            this.style.background = '#5568d3';
            this.style.transform = 'translateY(-2px)';
        });

        btnReiniciar.addEventListener('mouseleave', function() {
            this.style.background = '#667eea';
            this.style.transform = 'translateY(0)';
        });

        btnReiniciar.addEventListener('click', function() {
            window.location.reload();
        });

        acciones.appendChild(btnReiniciar);
    }

    // ============================================
    // FAQ ACORDEON - Front Page
    // ============================================
    function inicializarFaqAcordeon() {
        var faqItems = document.querySelectorAll('.faq-item');
        if (!faqItems.length) return;

        faqItems.forEach(function(item) {
            var btn = item.querySelector('.faq-question');
            var answer = item.querySelector('.faq-answer');
            if (!btn || !answer) return;

            btn.setAttribute('aria-expanded', 'false');
            answer.setAttribute('aria-hidden', 'true');
            answer.style.maxHeight = '0px';

            btn.addEventListener('click', function() {
                // Cerrar otros
                faqItems.forEach(function(other) {
                    if (other === item) return;
                    other.classList.remove('active');
                    var otherBtn = other.querySelector('.faq-question');
                    var otherAnswer = other.querySelector('.faq-answer');
                    if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                    if (otherAnswer) {
                        otherAnswer.setAttribute('aria-hidden', 'true');
                        otherAnswer.style.maxHeight = '0px';
                    }
                    var otherIcon = other.querySelector('.faq-icono');
                    if (otherIcon) otherIcon.textContent = '+';
                });

                var isActive = item.classList.toggle('active');
                btn.setAttribute('aria-expanded', isActive ? 'true' : 'false');
                answer.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                if (isActive) {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                } else {
                    answer.style.maxHeight = '0px';
                }

                var icon = item.querySelector('.faq-icono');
                if (icon) icon.textContent = isActive ? '-' : '+';
            });
        });
    }

    // ============================================
    // RESALTAR NAVEGACIÓN AL COMPLETAR FICHA
    // ============================================
    function inicializarNavegacionAtencion() {
        var nav = document.querySelector('.ficha-navegacion-cards');
        if (!nav) return;
        var navNext = nav.querySelector('.nav-card-next');

        var fichaEducativa = document.querySelector('.ficha-educativa');
        var completada = !fichaEducativa;
        var activada = false;
        var navVisible = false;

        function activar() {
            if (activada || !completada) return;
            nav.classList.add('nav-attention');
            if (navNext) {
                navNext.classList.add('nav-next-attention');
                if (!navNext.dataset.flashRunning) {
                    navNext.dataset.flashRunning = 'true';
                    setInterval(function() {
                        navNext.classList.add('nav-next-flash');
                        setTimeout(function() {
                            navNext.classList.remove('nav-next-flash');
                        }, 520);
                    }, 1800);
                }
            }
            activada = true;
        }

        function navEnVista() {
            var rect = nav.getBoundingClientRect();
            return rect.top < window.innerHeight * 0.7 && rect.bottom > 0;
        }

        if (fichaEducativa) {
            var btnComprobar = fichaEducativa.querySelector('.btn-comprobar');
            if (btnComprobar) {
                btnComprobar.addEventListener('click', function() {
                    completada = true;
                    activar();
                });
            } else {
                completada = true;
            }
        }

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    navVisible = entry.isIntersecting;
                    if (entry.isIntersecting) activar();
                });
            }, { threshold: 0.6 });
            observer.observe(nav);
        } else {
            window.addEventListener('scroll', function() {
                if (!activada && completada && navEnVista()) activar();
            });
        }
    }

    // ============================================
    // CARRUSEL FICHAS RELACIONADAS
    // ============================================
    function inicializarCarruselRelacionadas() {
        var wrappers = document.querySelectorAll('.fichas-carousel-wrapper');
        if (!wrappers.length) return;

        wrappers.forEach(function(wrapper) {
            var carousel = wrapper.querySelector('.fichas-relacionadas-carousel');
            var prevBtn = wrapper.querySelector('.carousel-prev');
            var nextBtn = wrapper.querySelector('.carousel-next');

            if (!carousel || !prevBtn || !nextBtn) return;

            function getStep() {
                var cards = carousel.querySelectorAll('.ficha-card-mini-v2');
                if (cards.length >= 2) {
                    var firstLeft = cards[0].getBoundingClientRect().left;
                    var secondLeft = cards[1].getBoundingClientRect().left;
                    var delta = Math.abs(secondLeft - firstLeft);
                    if (delta > 0) return delta;
                }
                if (cards.length === 1) return cards[0].offsetWidth;
                return Math.max(240, Math.round(carousel.clientWidth * 0.85));
            }

            function updateButtons() {
                var maxScroll = Math.max(0, carousel.scrollWidth - carousel.clientWidth);
                var left = carousel.scrollLeft;
                prevBtn.disabled = left <= 2;
                nextBtn.disabled = left >= (maxScroll - 2);
            }

            function scrollByStep(direction) {
                carousel.scrollBy({
                    left: getStep() * direction,
                    behavior: 'smooth'
                });
            }

            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                scrollByStep(-1);
            });

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                scrollByStep(1);
            });

            carousel.addEventListener('scroll', updateButtons, { passive: true });
            window.addEventListener('resize', updateButtons);
            updateButtons();
        });
    }

    // ============================================
    // ============================================
    // SISTEMA DE IMPRESIÓN PDF v3.0
    // ============================================
    // ============================================

    function escaparHtml(s) {
        return (s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function clonarPreguntasHTML() {
        var ficha = document.querySelector('.ficha-educativa');
        if (!ficha) return '';

        // ✅ CLONAR TODA LA FICHA (no solo .contenedor-preguntas)
        var clone = ficha.cloneNode(true);

        // ❌ ELIMINAR: Memory (no imprimible)
        var elementosNoImprimibles = clone.querySelectorAll('.ejercicio-memory');
        elementosNoImprimibles.forEach(function(el) {
            el.remove();
        });

        // ❌ ELIMINAR: Botones y feedback
        var acciones = clone.querySelectorAll(
            '.acciones, .feedback, .btn-comprobar, .btn-reiniciar, ' +
            '.dragdrop-actions, .btn-borrar-trazo, .trazo-actions, ' +
            '.acciones-wordsearch, .btn-pista-wordsearch, .btn-reintentar-wordsearch, ' +
            '.feedback-wordsearch, .memory-actions, .btn-reiniciar-dragdrop'
        );
        acciones.forEach(function(el) {
            el.remove();
        });

        // ❌ ELIMINAR: Clases de feedback visual
        var elementosConFeedback = clone.querySelectorAll('.correcto, .incorrecto, .seleccionado');
        elementosConFeedback.forEach(function(el) {
            el.classList.remove('correcto', 'incorrecto', 'seleccionado');
        });

        // ✅ LIMPIAR inputs completados
        var inputs = clone.querySelectorAll('.fillblank-input');
        inputs.forEach(function(input) {
            input.value = '';
            input.disabled = false;
            input.classList.remove('correcto', 'incorrecto');
        });

        return clone.outerHTML;
    }

    function imprimirPDFVisual() {
        if (imprimiendo) return;
        imprimiendo = true;

        try {
            var h1 = document.querySelector('h1');
            var titulo = h1 ? h1.textContent.trim() : document.title;
            
            var preguntasHtml = clonarPreguntasHTML();

            if (!preguntasHtml) {
                alert('No se encontraron preguntas para imprimir.');
                imprimiendo = false;
                return;
            }

            var w = window.open('', '_blank');
            if (!w) {
                alert('El navegador bloqueó la ventana de impresión. Permite ventanas emergentes.');
                imprimiendo = false;
                return;
            }

            var css = '<style>'
                + '  @page { size: A4; margin: 12mm; }'
                + '  * { box-sizing: border-box; margin: 0; padding: 0; }'
                + '  body { margin: 0; font-family: Arial, sans-serif; color: #0f172a; background: white; }'
                + '  .ficha-educativa { width: 100%; max-width: 100%; margin: 0; padding: 0; }'
                + '  h1 { background: #6d5bd0; border-radius: 12px; padding: 16px; color: #fff; margin-bottom: 16px; font-size: 24px; font-weight: 800; text-align: center; }'
                + '  .instrucciones { background: #fef3c7; border: 2px solid #f59e0b; border-radius: 10px; padding: 12px; text-align: center; font-weight: 700; margin-bottom: 16px; font-size: 13px; }'
                + '  .contenedor-preguntas { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 12px; }'
                + '  .bloque-ejercicio { background: #f9fafb; border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; break-inside: avoid; }'
                + '  .pregunta { font-weight: 800; font-size: 13px; margin-bottom: 6px; color: #1f2937; }'
                + '  .visual { text-align: center; font-size: 20px; margin: 8px 0; }'
                + '  .opciones { display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; }'
                + '  .opcion-btn { border: 2px solid #2EC4B6; border-radius: 8px; padding: 6px 10px; font-weight: 700; background: #fff; font-size: 12px; display: inline-block; }'
                + '  .ejercicio-fillblanks { background: #f0f9ff; border: 2px solid #0ea5e9; border-radius: 10px; padding: 12px; margin-bottom: 12px; grid-column: 1 / -1; }'
                + '  .pregunta-fillblanks { font-weight: 800; font-size: 13px; margin-bottom: 8px; color: #1f2937; }'
                + '  .fillblanks-texto { font-size: 13px; line-height: 1.8; }'
                + '  .fillblank-input { border: none; border-bottom: 2px solid #2EC4B6; background: white; min-width: 80px; padding: 2px 6px; font-size: 12px; display: inline-block; }'
                + '  .ejercicio-dragdrop { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 3px solid #f59e0b; border-radius: 12px; padding: 16px; margin-bottom: 12px; page-break-inside: avoid; }'
                + '  .ejercicio-dragdrop .pregunta { color: #78350f; font-size: 14px; text-align: center; margin-bottom: 12px; }'
                + '  .dragdrop-container { display: flex; gap: 12px; justify-content: space-between; }'
                + '  .dragdrop-items, .dragdrop-zones { flex: 1; }'
                + '  .dragdrop-items { background: white; border: 2px dashed #d97706; border-radius: 10px; padding: 12px; }'
                + '  .dragdrop-items-title { font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 8px; text-align: center; }'
                + '  .dragdrop-items-grid { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }'
                + '  .dragdrop-item { background: white; border: 2px solid #fbbf24; border-radius: 8px; padding: 8px; font-size: 20px; text-align: center; }'
                + '  .dragdrop-zones { display: flex; flex-direction: column; gap: 8px; }'
                + '  .dragdrop-zone { background: white; border: 2px dashed #94a3b8; border-radius: 10px; padding: 10px; }'
                + '  .dragdrop-zone-title { font-size: 12px; font-weight: 700; color: #475569; text-align: center; margin-bottom: 6px; }'
                + '  .dragdrop-zone-content { min-height: 40px; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 6px; }'
                + '  .ejercicio-trazo { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border: 3px solid #0ea5e9; border-radius: 12px; padding: 16px; margin-bottom: 12px; page-break-inside: avoid; }'
                + '  .pregunta-trazo { font-size: 14px; font-weight: 800; color: #0c4a6e; text-align: center; margin-bottom: 12px; }'
                + '  .trazo-canvas-container { position: relative; background: white; border: 2px solid #0ea5e9; border-radius: 10px; padding: 10px; min-height: 200px; display: flex; align-items: center; justify-content: center; }'
                + '  .trazo-guia { font-size: 120px; color: #cbd5e1; font-weight: 900; }'
                + '  canvas { display: none; }'
                + '  .ejercicio-wordsearch { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); border: 3px solid #a855f7; border-radius: 12px; padding: 16px; margin-bottom: 12px; page-break-inside: avoid; }'
                + '  .pregunta-wordsearch { font-size: 14px; font-weight: 800; color: #581c87; text-align: center; margin-bottom: 12px; }'
                + '  .wordsearch-container { display: flex; gap: 12px; justify-content: center; }'
                + '  .wordsearch-grid { flex: 1; }'
                + '  .wordsearch-table { border-collapse: collapse; margin: 0 auto; background: white; border: 2px solid #a855f7; border-radius: 8px; overflow: hidden; }'
                + '  .wordsearch-cell { width: 28px; height: 28px; text-align: center; font-weight: 700; font-size: 13px; border: 1px solid #e9d5ff; background: white; }'
                + '  .wordsearch-words { background: white; border: 2px solid #a855f7; border-radius: 8px; padding: 10px; max-width: 150px; }'
                + '  .wordsearch-words-title { font-size: 12px; font-weight: 800; color: #581c87; margin-bottom: 6px; text-align: center; }'
                + '  .wordsearch-words-list { display: flex; flex-direction: column; gap: 4px; }'
                + '  .word-item { font-size: 11px; padding: 4px 8px; background: #f3e8ff; border-radius: 6px; text-align: center; }'
                + '  .acciones, .feedback, .btn-comprobar, .btn-reiniciar, .dragdrop-actions, .trazo-actions, .btn-borrar-trazo, .acciones-wordsearch, .btn-pista-wordsearch, .btn-reintentar-wordsearch, .feedback-wordsearch, .memory-actions, .btn-reiniciar-dragdrop, .btn-descargar-pdf { display: none !important; }'
                + '</style>';

            var html = '<!doctype html><html><head><meta charset="utf-8"><title>' + escaparHtml(titulo) + '</title>' + css + '</head><body>'
                + preguntasHtml
                + '<script>'
                + '  window.onload = function(){'
                + '    setTimeout(function(){ window.print(); }, 500);'
                + '  };'
                + '</script>'
                + '</body></html>';

            w.document.open();
            w.document.write(html);
            w.document.close();

        } catch (e) {
            console.error('Error al preparar la impresión:', e);
            alert('Error al preparar la impresión: ' + e.message);
        } finally {
            setTimeout(function() {
                imprimiendo = false;
            }, 1000);
        }
    }

    // ============================================
    // LISTENER BOTÓN PDF
    // ============================================
    document.addEventListener('click', function (e) {
        var btn = e.target;

        if (!btn || !btn.classList || !btn.classList.contains('btn-descargar-pdf')) return;

        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        imprimirPDFVisual();
    }, true);

    // ============================================
    // INICIALIZACIÓN
    // ============================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarFichaInteractiva);
    } else {
        inicializarFichaInteractiva();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarFaqAcordeon);
    } else {
        inicializarFaqAcordeon();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarNavegacionAtencion);
    } else {
        inicializarNavegacionAtencion();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarCarruselRelacionadas);
    } else {
        inicializarCarruselRelacionadas();
    }
    // ============================================
    // ============================================
    // GENERAR IMAGEN PINTEREST v3.0
    // ============================================
    // ============================================

    function inicializarGeneradorPinterest() {
        var btnGenerar = document.getElementById('generarImagenPinterest');
        if (!btnGenerar) return;

        btnGenerar.addEventListener('click', function() {
            var fichaHeader = document.querySelector('.ficha-header');
            var fichaEducativa = document.querySelector('.ficha-educativa');

            if (!fichaHeader || !fichaEducativa) {
                alert('No se encontraron los elementos necesarios.');
                return;
            }

            // Verificar que html2canvas esté cargado
            if (typeof html2canvas === 'undefined') {
                alert('Error: html2canvas no está cargado. Recarga la página.');
                return;
            }

            // Cambiar texto del botón
            btnGenerar.textContent = '⏳ Generando imagen...';
            btnGenerar.disabled = true;

            // Crear contenedor temporal
            var container = document.createElement('div');
            container.style.cssText = 'position: fixed; left: -9999px; top: 0; width: 1200px; background: #5b6fd8; padding: 30px; border-radius: 20px; font-family: Arial, sans-serif;';

            // Clonar header
            var headerClone = fichaHeader.cloneNode(true);
            headerClone.style.marginBottom = '20px';

            // Clonar ficha
            var fichaClone = fichaEducativa.cloneNode(true);

            // ❌ OCULTAR BOTONES Y FEEDBACK EN EL CLON
            var acciones = fichaClone.querySelector('.acciones');
            if (acciones) acciones.style.display = 'none';

            var feedback = fichaClone.querySelector('.feedback');
            if (feedback) feedback.style.display = 'none';

            // Crear footer con URL
            var footer = document.createElement('div');
            footer.style.cssText = 'background: white; padding: 20px; border-radius: 12px; text-align: center; margin-top: 20px; font-size: 18px; font-weight: 700; color: #2c3e50; box-shadow: 0 4px 12px rgba(0,0,0,0.1);';
            footer.innerHTML = '📚 Descarga más fichas en <span style="color: #667eea;">fichas.es</span>';

            // Ensamblar
            container.appendChild(headerClone);
            container.appendChild(fichaClone);
            container.appendChild(footer);
            document.body.appendChild(container);

            // Capturar con html2canvas
            html2canvas(container, {
                backgroundColor: '#5b6fd8',
                scale: 2,
                logging: false,
                useCORS: true,
                width: 1200,
                windowWidth: 1200
            }).then(function(canvas) {
                // Eliminar contenedor temporal
                document.body.removeChild(container);

                // Descargar imagen
                canvas.toBlob(function(blob) {
                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    var titulo = document.querySelector('.ficha-titulo')?.textContent || 'ficha';
                    link.download = 'pinterest-' + titulo.toLowerCase().replace(/\s+/g, '-') + '.png';
                    link.href = url;
                    link.click();

                    // Restaurar botón
                    btnGenerar.textContent = '✅ Imagen generada';
                    setTimeout(function() {
                        btnGenerar.textContent = '📌 Generar Imagen Pinterest';
                        btnGenerar.disabled = false;
                    }, 2000);
                });
            }).catch(function(error) {
                // Eliminar contenedor en caso de error
                if (document.body.contains(container)) {
                    document.body.removeChild(container);
                }
                console.error('Error al generar imagen:', error);
                alert('Error al generar la imagen.');
                btnGenerar.textContent = '📌 Generar Imagen Pinterest';
                btnGenerar.disabled = false;
            });
        });
    }

    // ============================================
    // INICIALIZACIÓN PINTEREST
    // ============================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarGeneradorPinterest);
    } else {
        inicializarGeneradorPinterest();
    }

})();

