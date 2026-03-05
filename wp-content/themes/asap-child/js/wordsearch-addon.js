/**
 * ============================================
 * WORD SEARCH ADDON v2.2 - Touch-Friendly
 * Sistema de selección por clicks individuales
 * Compatible con móvil y desktop
 * Auto-validación cuando se forma palabra válida
 * Sin botón Pista
 * ============================================
 */

(function() {
    'use strict';

    if (typeof window === 'undefined') return;

    // ============================================
    // INICIALIZACIÓN
    // ============================================
    
    function inicializarWordSearches() {
        var ejercicios = document.querySelectorAll('.ejercicio-wordsearch');
        
        ejercicios.forEach(function(ejercicio, index) {
            inicializarWordSearch(ejercicio, index);
        });
    }

    function inicializarWordSearch(ejercicio, index) {
        var table = ejercicio.querySelector('.wordsearch-table');
        if (!table) {
            console.warn('⚠️ WordSearch: No se encontró .wordsearch-table');
            return;
        }

        // Obtener palabras objetivo
        var palabrasRaw = table.getAttribute('data-words');
        if (!palabrasRaw) {
            console.warn('⚠️ WordSearch: Falta data-words en la tabla');
            return;
        }

        var palabrasObjetivo = JSON.parse(palabrasRaw).map(function(p) {
            return p.toUpperCase();
        });

        // Estado del ejercicio
        var palabrasEncontradas = [];
        var celdasSeleccionadas = [];

        // Obtener todas las celdas
        var celdas = Array.from(table.querySelectorAll('.wordsearch-cell'));

        // ============================================
        // EVENTOS DE SELECCIÓN
        // ============================================

        celdas.forEach(function(celda, idx) {
            // Click en celda
            celda.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Si ya está encontrada, ignorar
                if (celda.classList.contains('found')) {
                    return;
                }

                // Si ya está seleccionada, deseleccionar
                if (celda.classList.contains('selected')) {
                    deseleccionarCelda(celda);
                    return;
                }

                // Añadir a selección
                seleccionarCelda(celda);
            });

            // Prevenir selección de texto
            celda.addEventListener('selectstart', function(e) {
                e.preventDefault();
            });
        });

        // Click fuera del grid → limpiar selección
        document.addEventListener('click', function(e) {
            if (!ejercicio.contains(e.target)) {
                limpiarSeleccion();
            }
        });

        // También limpiar si click en el contenedor pero fuera de celdas
        ejercicio.addEventListener('click', function(e) {
            if (e.target === ejercicio || 
                e.target.classList.contains('wordsearch-container') ||
                e.target.classList.contains('wordsearch-grid')) {
                limpiarSeleccion();
            }
        });

        // ============================================
        // FUNCIONES DE SELECCIÓN
        // ============================================

        function seleccionarCelda(celda) {
            celda.classList.add('selected');
            celdasSeleccionadas.push(celda);
            
            // Validar inmediatamente
            validarSeleccion();
        }

        function deseleccionarCelda(celda) {
            celda.classList.remove('selected');
            var index = celdasSeleccionadas.indexOf(celda);
            if (index > -1) {
                celdasSeleccionadas.splice(index, 1);
            }
        }

        function limpiarSeleccion() {
            celdasSeleccionadas.forEach(function(celda) {
                celda.classList.remove('selected');
            });
            celdasSeleccionadas = [];
        }

        // ============================================
        // VALIDACIÓN DE PALABRA
        // ============================================

        function validarSeleccion() {
            if (celdasSeleccionadas.length < 2) {
                return; // Necesitamos al menos 2 letras
            }

            // Construir texto de la selección
            var texto = celdasSeleccionadas.map(function(c) {
                return c.textContent.trim();
            }).join('');

            var textoInverso = texto.split('').reverse().join('');

            // Verificar si forma línea recta (horizontal o vertical)
            if (!esLineaRecta(celdasSeleccionadas)) {
                return; // No es línea válida
            }

            // Buscar coincidencia con palabras objetivo
            var palabraEncontrada = null;
            
            for (var i = 0; i < palabrasObjetivo.length; i++) {
                var palabra = palabrasObjetivo[i];
                if (texto === palabra || textoInverso === palabra) {
                    // Verificar que no se haya encontrado ya
                    if (palabrasEncontradas.indexOf(palabra) === -1) {
                        palabraEncontrada = palabra;
                        break;
                    }
                }
            }

            if (palabraEncontrada) {
                marcarPalabraEncontrada(palabraEncontrada);
            }
        }

        function esLineaRecta(celdas) {
            if (celdas.length < 2) return false;

            // Obtener posiciones (fila, columna)
            var posiciones = celdas.map(function(celda) {
                var allCells = Array.from(table.querySelectorAll('.wordsearch-cell'));
                var index = allCells.indexOf(celda);
                var cols = table.rows[0].cells.length;
                return {
                    fila: Math.floor(index / cols),
                    col: index % cols
                };
            });

            // Verificar horizontal (misma fila)
            var mismaFila = posiciones.every(function(pos) {
                return pos.fila === posiciones[0].fila;
            });

            // Verificar vertical (misma columna)
            var mismaColumna = posiciones.every(function(pos) {
                return pos.col === posiciones[0].col;
            });

            if (!mismaFila && !mismaColumna) {
                return false; // Ni horizontal ni vertical
            }

            // Verificar que sean consecutivas
            if (mismaFila) {
                // Ordenar por columna
                posiciones.sort(function(a, b) { return a.col - b.col; });
            } else {
                // Ordenar por fila
                posiciones.sort(function(a, b) { return a.fila - b.fila; });
            }

            // Verificar consecutividad
            for (var i = 1; i < posiciones.length; i++) {
                var diff = mismaFila ? 
                    (posiciones[i].col - posiciones[i-1].col) :
                    (posiciones[i].fila - posiciones[i-1].fila);
                
                if (diff !== 1) {
                    return false; // No son consecutivas
                }
            }

            return true;
        }

        function marcarPalabraEncontrada(palabra) {
            // Marcar celdas como encontradas (verde permanente)
            celdasSeleccionadas.forEach(function(celda) {
                celda.classList.remove('selected');
                celda.classList.add('found');
            });

            // Añadir a lista de encontradas
            palabrasEncontradas.push(palabra);

            // Tachar palabra en la lista lateral
            var wordItems = ejercicio.querySelectorAll('.word-item');
            wordItems.forEach(function(item) {
                if (item.textContent.toUpperCase().trim() === palabra) {
                    item.classList.add('found');
                }
            });

            // Limpiar selección
            celdasSeleccionadas = [];

            // Verificar si completó todas
            if (palabrasEncontradas.length === palabrasObjetivo.length) {
                mostrarFeedbackExito(ejercicio);
            }
        }

        function mostrarFeedbackExito(ejercicio) {
            var feedback = ejercicio.querySelector('.feedback-wordsearch');
            if (feedback) {
                feedback.textContent = '🎉 ¡Felicidades! Has encontrado todas las palabras';
                feedback.className = 'feedback-wordsearch exito';
                feedback.style.display = 'block';
            }

            // Marcar ejercicio como completado para fichas.js
            ejercicio.dataset.wordsearchCompleted = 'true';
        }

        // ============================================
        // ELIMINAR BOTÓN PISTA
        // ============================================

        var btnPista = ejercicio.querySelector('.btn-pista-wordsearch');
        if (btnPista) {
            btnPista.remove();
        }

        // ============================================
        // BOTÓN REINTENTAR
        // ============================================

        var btnReintentar = ejercicio.querySelector('.btn-reintentar-wordsearch');
        if (btnReintentar) {
            btnReintentar.addEventListener('click', function() {
                reiniciarWordSearch(ejercicio);
            });
        }

        function reiniciarWordSearch(ejercicio) {
            // Limpiar todas las celdas
            celdas.forEach(function(celda) {
                celda.classList.remove('selected', 'found');
            });

            // Limpiar palabras encontradas
            var wordItems = ejercicio.querySelectorAll('.word-item');
            wordItems.forEach(function(item) {
                item.classList.remove('found');
            });

            // Resetear estado
            palabrasEncontradas = [];
            celdasSeleccionadas = [];

            // Ocultar feedback y botón reintentar
            var feedback = ejercicio.querySelector('.feedback-wordsearch');
            if (feedback) {
                feedback.style.display = 'none';
            }

            btnReintentar.style.display = 'none';
            delete ejercicio.dataset.wordsearchCompleted;
        }

        // Guardar referencia para acceso externo (desde fichas.js)
        ejercicio._wordsearchData = {
            palabrasObjetivo: palabrasObjetivo,
            palabrasEncontradas: palabrasEncontradas
        };
    }

    // ============================================
    // INICIAR AL CARGAR LA PÁGINA
    // ============================================

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarWordSearches);
    } else {
        inicializarWordSearches();
    }

})();
