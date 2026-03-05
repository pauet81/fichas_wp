/**
 * Asap Theme - Dark Mode Script
 * JavaScript mejorado para el modo oscuro
 * 
 * @package AsapTheme
 */

(function($) {
    'use strict';

    /**
     * Clase principal del Modo Oscuro
     */
    class AsapDarkMode {
        constructor() {
            this.storageKey = 'asapDarkMode';
            this.bodyClass = 'asap-dark-mode';
            this.button = null;
            this.config = window.asapDarkMode || {};
            
            this.init();
        }

        /**
         * Inicializa el modo oscuro
         */
        init() {
            // Aplicar modo guardado inmediatamente para evitar flash
            this.applyStoredMode();
            
            // Esperar a que el DOM esté listo
            $(document).ready(() => {
                this.createToggleButton();
                this.bindEvents();
                this.updateButtonState();
                this.checkSystemPreference();
            });
        }

        /**
         * Aplica el modo guardado desde localStorage
         * NOTA: Ya se aplicó en el head para evitar FOUC, solo verificamos aquí
         */
        applyStoredMode() {
            const storedMode = localStorage.getItem(this.storageKey);
            
            // Ya se aplicó en el head, solo aseguramos que esté sincronizado
            if (storedMode === 'on') {
                if (!document.body.classList.contains(this.bodyClass)) {
                    document.body.classList.add(this.bodyClass);
                }
            } else if (storedMode === 'off') {
                document.body.classList.remove(this.bodyClass);
            }
        }

        /**
         * Verifica preferencia del sistema
         * DESHABILITADO: El usuario debe activar manualmente el modo oscuro
         */
        checkSystemPreference() {
            // Deshabilitamos la detección automática del sistema
            // El modo oscuro SOLO se activa cuando el usuario hace clic en el botón
            
            /* COMENTADO - Detección automática deshabilitada por solicitud del usuario
            const storedMode = localStorage.getItem(this.storageKey);
            
            if (!storedMode && window.matchMedia) {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (prefersDark) {
                    this.enableDarkMode(false);
                }
            }
            */
            
            // Escuchar cambios en la preferencia del sistema (opcional para futuro)
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    const storedMode = localStorage.getItem(this.storageKey);
                    
                    // Solo aplicar si el usuario ya había activado el modo oscuro manualmente
                    if (storedMode === 'on') {
                        // Mantener el modo oscuro activado
                    }
                });
            }
        }

        /**
         * Crea el botón flotante de toggle
         */
        createToggleButton() {
            // Verificar si ya existe
            if ($('#asap-dark-mode-toggle').length > 0) {
                this.button = $('#asap-dark-mode-toggle');
                return;
            }

            // Crear el botón
            const buttonText = this.getButtonText();
            const position = this.config.buttonPosition || 'bottom-left';
            
            this.button = $('<button>', {
                id: 'asap-dark-mode-toggle',
                class: `position-${position}`,
                html: buttonText,
                attr: {
                    'aria-label': 'Alternar modo oscuro',
                    'title': 'Alternar modo oscuro'
                }
            });

            // Agregar al body
            $('body').append(this.button);
        }

        /**
         * Vincula eventos
         */
        bindEvents() {
            if (!this.button) return;

            // Click en el botón
            this.button.on('click', (e) => {
                e.preventDefault();
                this.toggle();
            });

            // Atajo de teclado opcional (Ctrl/Cmd + D)
            $(document).on('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                    e.preventDefault();
                    this.toggle();
                }
            });
        }

        /**
         * Alterna entre modo oscuro y claro
         */
        toggle() {
            if ($('body').hasClass(this.bodyClass)) {
                this.disableDarkMode();
            } else {
                this.enableDarkMode();
            }
        }

        /**
         * Activa el modo oscuro
         */
        enableDarkMode(save = true) {
            $('body').addClass(this.bodyClass);
            
            if (save) {
                localStorage.setItem(this.storageKey, 'on');
            }
            
            this.updateButtonState();
            this.triggerEvent('enabled');
            
            // Animación suave
            this.animateTransition();
        }

        /**
         * Desactiva el modo oscuro
         */
        disableDarkMode(save = true) {
            $('body').removeClass(this.bodyClass);
            
            if (save) {
                localStorage.setItem(this.storageKey, 'off');
            }
            
            this.updateButtonState();
            this.triggerEvent('disabled');
            
            // Animación suave
            this.animateTransition();
        }

        /**
         * Actualiza el estado visual del botón
         */
        updateButtonState() {
            if (!this.button) return;

            const isActive = $('body').hasClass(this.bodyClass);
            const buttonText = this.getButtonText(isActive);
            
            this.button.html(buttonText);
            
            // Añadir clase de estado
            if (isActive) {
                this.button.addClass('is-active');
            } else {
                this.button.removeClass('is-active');
            }
        }

        /**
         * Obtiene el texto del botón según el estado
         */
        getButtonText(isActive = null) {
            if (isActive === null) {
                isActive = $('body').hasClass(this.bodyClass);
            }

            if (isActive) {
                return this.config.buttonTextActive || '☀️';
            } else {
                return this.config.buttonText || '🌙';
            }
        }

        /**
         * Animación de transición suave
         */
        animateTransition() {
            // Pequeña animación en el botón
            if (this.button) {
                this.button.css('transform', 'scale(0.9)');
                
                setTimeout(() => {
                    this.button.css('transform', '');
                }, 150);
            }
        }

        /**
         * Dispara un evento personalizado
         */
        triggerEvent(action) {
            const event = new CustomEvent('asapDarkMode', {
                detail: {
                    action: action,
                    isActive: $('body').hasClass(this.bodyClass)
                }
            });
            
            document.dispatchEvent(event);
        }

        /**
         * API pública
         */
        isEnabled() {
            return $('body').hasClass(this.bodyClass);
        }

        enable() {
            this.enableDarkMode();
        }

        disable() {
            this.disableDarkMode();
        }
    }

    // Inicializar cuando el documento esté listo
    const darkMode = new AsapDarkMode();

    // Exponer API global para uso externo
    window.AsapDarkMode = {
        toggle: () => darkMode.toggle(),
        enable: () => darkMode.enable(),
        disable: () => darkMode.disable(),
        isEnabled: () => darkMode.isEnabled()
    };

    // Evento personalizado para que otros scripts puedan escuchar
    // Uso: document.addEventListener('asapDarkMode', function(e) { console.log(e.detail); });

})(jQuery);

