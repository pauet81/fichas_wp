/* ============================================
   FILL BLANKS ADDON v2.0 - INTEGRADO CON FICHAS.JS
   ============================================
   - SIN botón comprobar individual
   - Solo inputs interactivos
   - La comprobación se hace desde fichas.js
   ============================================ */

(function() {
  'use strict';

  function inicializarFillBlanks() {
    var ejercicios = document.querySelectorAll('.ejercicio-fillblanks');
    if (ejercicios.length === 0) return;

    ejercicios.forEach(function(ejercicio) {
      var inputs = ejercicio.querySelectorAll('.fillblank-input');
      var btnReiniciar = ejercicio.querySelector('.btn-reintentar-fillblanks');

      // ============================================
      // EVENTOS DE INPUTS
      // ============================================
      inputs.forEach(function(input, index) {
        // Enter para avanzar al siguiente input
        input.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();

            if (index < inputs.length - 1) {
              inputs[index + 1].focus();
            } else {
              this.blur();
            }
          }
        });

        // Autocompletar mayúsculas si es necesario
        input.addEventListener('input', function() {
          var respuestaCorrecta = this.getAttribute('data-respuesta');

          // Si la respuesta correcta está en mayúsculas, convertir input
          if (respuestaCorrecta && respuestaCorrecta === respuestaCorrecta.toUpperCase()) {
            this.value = this.value.toUpperCase();
          }
        });
      });

      // ============================================
      // BOTÓN REINICIAR
      // ============================================
      if (btnReiniciar) {
        btnReiniciar.addEventListener('click', function() {
          inputs.forEach(function(input) {
            input.value = '';
            input.classList.remove('correcto', 'incorrecto');
            input.disabled = false;
          });

          var feedback = ejercicio.querySelector('.feedback-fillblanks');
          if (feedback) feedback.style.display = 'none';
        });
      }
    });
  }

  // INICIALIZACIÓN
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarFillBlanks);
  } else {
    inicializarFillBlanks();
  }

})();
