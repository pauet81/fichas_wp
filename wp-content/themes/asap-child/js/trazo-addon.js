/* ============================================
   TRAZO ADDON v2.0 - INTEGRADO CON FICHAS.JS
   ============================================
   - SIN botón comprobar individual
   - Solo botón borrar
   - La comprobación se hace desde fichas.js
   ============================================ */

(function() {
  'use strict';

  function inicializarEjerciciosTrazo() {
    var ejercicios = document.querySelectorAll('.ejercicio-trazo');
    if (ejercicios.length === 0) return;

    ejercicios.forEach(function(ejercicio) {
      var canvas = ejercicio.querySelector('canvas');
      if (!canvas) return;

      var ctx = canvas.getContext('2d');
      var btnBorrar = ejercicio.querySelector('.btn-borrar-trazo');

      var dibujando = false;
      var ultimoX = 0;
      var ultimoY = 0;

      // Configuración del trazo
      ctx.lineWidth = 8;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = '#2571A3';

      // ============================================
      // FUNCIONES DE DIBUJO
      // ============================================
      function empezarDibujo(e) {
        dibujando = true;
        var pos = obtenerPosicion(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        ultimoX = pos.x;
        ultimoY = pos.y;
      }

      function dibujar(e) {
        if (!dibujando) return;

        var pos = obtenerPosicion(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();

        ultimoX = pos.x;
        ultimoY = pos.y;
      }

      function terminarDibujo() {
        if (!dibujando) return;
        dibujando = false;
        ctx.closePath();
      }

      function obtenerPosicion(e) {
        var rect = canvas.getBoundingClientRect();
        var x, y;

        if (e.touches && e.touches.length > 0) {
          x = e.touches[0].clientX - rect.left;
          y = e.touches[0].clientY - rect.top;
        } else {
          x = e.clientX - rect.left;
          y = e.clientY - rect.top;
        }

        return { x: x, y: y };
      }

      // ============================================
      // EVENTOS MOUSE
      // ============================================
      canvas.addEventListener('mousedown', empezarDibujo);
      canvas.addEventListener('mousemove', dibujar);
      canvas.addEventListener('mouseup', terminarDibujo);
      canvas.addEventListener('mouseout', terminarDibujo);

      // ============================================
      // EVENTOS TOUCH
      // ============================================
      canvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        empezarDibujo(e);
      }, { passive: false });

      canvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
        dibujar(e);
      }, { passive: false });

      canvas.addEventListener('touchend', function(e) {
        e.preventDefault();
        terminarDibujo();
      }, { passive: false });

      // ============================================
      // BOTÓN BORRAR
      // ============================================
      if (btnBorrar) {
        btnBorrar.addEventListener('click', function() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
      }
    });
  }

  // INICIALIZACIÓN
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarEjerciciosTrazo);
  } else {
    inicializarEjerciciosTrazo();
  }

})();
