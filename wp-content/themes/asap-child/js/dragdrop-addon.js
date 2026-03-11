/* ============================================
   DRAG & DROP ADDON v2.0 - INTEGRADO CON FICHAS.JS
   ============================================
   - SIN botón comprobar individual
   - La comprobación se hace desde fichas.js
   - Mantiene botón reiniciar
   ============================================ */

(function() {
  'use strict';

  function inicializarEjerciciosDragDrop() {
    var ejercicios = document.querySelectorAll('.ejercicio-dragdrop');
    if (ejercicios.length === 0) return;

    ejercicios.forEach(function(ejercicio) {
      var items = ejercicio.querySelectorAll('.dragdrop-item');
      var zones = ejercicio.querySelectorAll('.dragdrop-zone');
      var btnReiniciar = ejercicio.querySelector('.btn-reiniciar-dragdrop');

      var draggedElement = null;
      var itemsOriginales = [];

      // Guardar estado inicial
      items.forEach(function(item) {
        itemsOriginales.push({
          element: item,
          parent: item.parentElement
        });
      });

      // ============================================
      // EVENTOS PARA ELEMENTOS ARRASTRABLES
      // ============================================
      items.forEach(function(item) {
        // MOUSE - Drag start
        item.addEventListener('dragstart', function(e) {
          draggedElement = this;
          this.classList.add('dragging');
          e.dataTransfer.effectAllowed = 'move';
          e.dataTransfer.setData('text/html', this.innerHTML);
        });

        // MOUSE - Drag end
        item.addEventListener('dragend', function(e) {
          this.classList.remove('dragging');
          draggedElement = null;
        });

        // TOUCH - Touch start
        item.addEventListener('touchstart', function(e) {
          draggedElement = this;
          this.classList.add('dragging');
          this.dataset.touchStartX = String(e.touches[0].pageX);
          this.dataset.touchStartY = String(e.touches[0].pageY);
          this.dataset.touchDragging = 'false';
        });

        // TOUCH - Touch move
        item.addEventListener('touchmove', function(e) {
          if (!draggedElement) return;

          var startX = parseFloat(this.dataset.touchStartX || '0');
          var startY = parseFloat(this.dataset.touchStartY || '0');
          var currentX = e.touches[0].pageX;
          var currentY = e.touches[0].pageY;
          var deltaX = Math.abs(currentX - startX);
          var deltaY = Math.abs(currentY - startY);

          // Evita sacar el item de su sitio con un simple toque
          if (this.dataset.touchDragging !== 'true' && (deltaX > 6 || deltaY > 6)) {
            this.dataset.touchDragging = 'true';
            this.style.position = 'fixed';
            this.style.zIndex = '1000';
            this.style.width = this.offsetWidth + 'px';
          }

          if (this.dataset.touchDragging === 'true') {
            e.preventDefault();
          }
          moveAt(e.touches[0].pageX, e.touches[0].pageY);
        });

        // TOUCH - Touch end
        item.addEventListener('touchend', function(e) {
          this.classList.remove('dragging');
          var wasDragging = this.dataset.touchDragging === 'true';
          this.style.position = '';
          this.style.zIndex = '';
          this.style.width = '';
          this.style.left = '';
          this.style.top = '';
          this.dataset.touchDragging = '';
          this.dataset.touchStartX = '';
          this.dataset.touchStartY = '';

          var touch = e.changedTouches[0];
          var elementBelow = document.elementFromPoint(touch.clientX, touch.clientY);
          var zoneBelow = elementBelow?.closest('.dragdrop-zone-content');

          if (wasDragging && zoneBelow && draggedElement) {
            zoneBelow.appendChild(draggedElement);
            draggedElement.classList.add('dropped');
          }

          draggedElement = null;
        });
      });

      function moveAt(pageX, pageY) {
        if (!draggedElement) return;
        draggedElement.style.left = pageX - draggedElement.offsetWidth / 2 + 'px';
        draggedElement.style.top = pageY - draggedElement.offsetHeight / 2 + 'px';
      }

      // ============================================
      // EVENTOS PARA ZONAS DE DESTINO
      // ============================================
      zones.forEach(function(zone) {
        var zoneContent = zone.querySelector('.dragdrop-zone-content');
        if (!zoneContent) return;

        // Drag over
        zoneContent.addEventListener('dragover', function(e) {
          e.preventDefault();
          e.dataTransfer.dropEffect = 'move';
          zone.classList.add('drag-over');
        });

        // Drag leave
        zoneContent.addEventListener('dragleave', function(e) {
          zone.classList.remove('drag-over');
        });

        // Drop
        zoneContent.addEventListener('drop', function(e) {
          e.preventDefault();
          zone.classList.remove('drag-over');

          if (draggedElement) {
            this.appendChild(draggedElement);
            draggedElement.classList.add('dropped');
          }
        });
      });

      // ============================================
      // BOTÓN REINICIAR
      // ============================================
      if (btnReiniciar) {
        btnReiniciar.addEventListener('click', function() {
          // Devolver items a posición original
          itemsOriginales.forEach(function(obj) {
            obj.parent.appendChild(obj.element);
            obj.element.classList.remove('dropped', 'correcto', 'incorrecto');
          });

          // Limpiar estados de zonas
          zones.forEach(function(zone) {
            zone.classList.remove('correct', 'incorrect', 'drag-over');
          });
        });
      }
    });
  }

  // INICIALIZACIÓN
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarEjerciciosDragDrop);
  } else {
    inicializarEjerciciosDragDrop();
  }

})();
