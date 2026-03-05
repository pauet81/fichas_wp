/* ============================================
   MEMORY ADDON v2.3 - COMPATIBLE CON data-pair
   ============================================
   ✅ Usa data-pair (no data-card)
   ✅ Usa data-pares (no data-pairs)
   ============================================ */

(function() {
    'use strict';

    function inicializarMemoryGames() {
        var ejercicios = document.querySelectorAll('.ejercicio-memory');
        if (ejercicios.length === 0) return;

        ejercicios.forEach(function(ejercicio) {
            var cards = ejercicio.querySelectorAll('.memory-card');
            var grid = ejercicio.querySelector('.memory-grid');
            var btnReiniciar = ejercicio.querySelector('.btn-reiniciar-memory');
            
            // ✅ CORREGIDO: data-pares (no data-pairs)
            var totalPairs = parseInt(ejercicio.getAttribute('data-pares')) || cards.length / 2;
            
            var flippedCards = [];
            var matchedPairs = 0;
            var moves = 0;
            var lockBoard = false;

            // Barajar cartas al inicio
            var cardsArray = Array.from(cards);
            cardsArray.sort(function() { return Math.random() - 0.5; });
            cardsArray.forEach(function(card) {
                grid.appendChild(card);
            });

            // ============================================
            // EVENTOS DE CARTAS
            // ============================================

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    // PREVENIR CLICS INVÁLIDOS
                    if (lockBoard) return;
                    if (card.classList.contains('flipped')) return;
                    if (card.classList.contains('matched')) return;

                    // VOLTEAR CARTA
                    card.classList.add('flipped');
                    flippedCards.push(card);

                    console.log('Carta volteada. Total:', flippedCards.length);

                    // VERIFICAR SI HAY 2 CARTAS VOLTEADAS
                    if (flippedCards.length === 2) {
                        lockBoard = true;
                        moves++;
                        actualizarStats();

                        // ✅ CORREGIDO: data-pair (no data-card)
                        var card1Value = flippedCards[0].getAttribute('data-pair') || flippedCards[0].getAttribute('data-valor');
                        var card2Value = flippedCards[1].getAttribute('data-pair') || flippedCards[1].getAttribute('data-valor');

                        console.log('Comparando:', card1Value, 'vs', card2Value);

                        if (card1Value === card2Value) {
                            // ✅ ¡PAREJA ENCONTRADA!
                            console.log('✅ PAREJA CORRECTA');
                            setTimeout(function() {
                                flippedCards[0].classList.add('matched');
                                flippedCards[1].classList.add('matched');

                                matchedPairs++;
                                actualizarStats();

                                flippedCards = [];
                                lockBoard = false;

                                // Verificar victoria
                                if (matchedPairs === totalPairs) {
                                    setTimeout(function() {
                                        ejercicio.dataset.memoryCompleted = 'true';
                                        mostrarMensajeVictoria();
                                    }, 500);
                                }
                            }, 600);

                        } else {
                            // ❌ NO ES PAREJA
                            console.log('❌ NO es pareja');
                            setTimeout(function() {
                                flippedCards[0].classList.remove('flipped');
                                flippedCards[1].classList.remove('flipped');
                                flippedCards = [];
                                lockBoard = false;
                                console.log('Tablero desbloqueado');
                            }, 1200);
                        }
                    }
                });
            });

            // ============================================
            // ACTUALIZAR ESTADÍSTICAS
            // ============================================

            function actualizarStats() {
                var movesEl = ejercicio.querySelector('.memory-moves strong');
                var pairsEl = ejercicio.querySelector('.memory-pairs strong');

                if (movesEl) movesEl.textContent = moves;
                if (pairsEl) pairsEl.textContent = matchedPairs + '/' + totalPairs;
            }

            // ============================================
            // MENSAJE DE VICTORIA
            // ============================================

            function mostrarMensajeVictoria() {
                var feedback = ejercicio.querySelector('.feedback-memory');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'feedback-memory';
                    ejercicio.appendChild(feedback);
                }

                feedback.className = 'feedback-memory exito';
                feedback.innerHTML = '🎉 ¡Felicidades! Completaste el juego en <strong>' + moves + '</strong> movimientos.';
                feedback.style.display = 'block';
            }

            // ============================================
            // BOTÓN REINICIAR
            // ============================================

            if (btnReiniciar) {
                btnReiniciar.addEventListener('click', function() {
                    console.log('🔄 Reiniciando...');

                    // Resetear variables
                    flippedCards = [];
                    matchedPairs = 0;
                    moves = 0;
                    lockBoard = false;
                    ejercicio.dataset.memoryCompleted = 'false';

                    // Limpiar clases
                    cards.forEach(function(card) {
                        card.classList.remove('flipped', 'matched');
                    });

                    // Barajar de nuevo
                    var cardsArray = Array.from(cards);
                    cardsArray.sort(function() { return Math.random() - 0.5; });
                    cardsArray.forEach(function(card) {
                        grid.appendChild(card);
                    });

                    // Actualizar stats
                    actualizarStats();

                    // Ocultar feedback
                    var feedback = ejercicio.querySelector('.feedback-memory');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                });
            }

            // Inicializar stats
            actualizarStats();

            console.log('Memory Game inicializado:', cards.length, 'cartas,', totalPairs, 'parejas');
        });
    }

    // INICIALIZACIÓN
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarMemoryGames);
    } else {
        inicializarMemoryGames();
    }

})();
