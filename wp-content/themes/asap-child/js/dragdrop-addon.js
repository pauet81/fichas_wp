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
      var isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
      var selectedItem = null;

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
        if (isTouchDevice) {
          item.draggable = false;

          // UX móvil infantil: tocar ficha y luego tocar destino
          item.addEventListener('click', function(e) {
            e.preventDefault();
            if (item.classList.contains('dropped')) return;

            items.forEach(function(i) { i.classList.remove('drag-selected'); });
            selectedItem = item;
            item.classList.add('drag-selected');
          });
          return;
        }

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
          if (isTouchDevice) return;
          e.preventDefault();
          zone.classList.remove('drag-over');

          if (draggedElement) {
            this.appendChild(draggedElement);
            draggedElement.classList.add('dropped');
          }
        });

        if (isTouchDevice) {
          zoneContent.addEventListener('click', function() {
            if (!selectedItem) return;
            zoneContent.appendChild(selectedItem);
            selectedItem.classList.add('dropped');
            selectedItem.classList.remove('drag-selected');
            selectedItem = null;
          });
        }
      });

      // ============================================
      // BOTÓN REINICIAR
      // ============================================
      if (btnReiniciar) {
        btnReiniciar.addEventListener('click', function() {
          // Devolver items a posición original
          itemsOriginales.forEach(function(obj) {
              obj.parent.appendChild(obj.element);
              obj.element.classList.remove('dropped', 'correcto', 'incorrecto', 'drag-selected');
          });
          selectedItem = null;

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
