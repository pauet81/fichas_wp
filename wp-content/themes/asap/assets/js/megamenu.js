/**
 * ASAP Megamenu - Frontend JavaScript
 * Full Screen Professional Megamenu
 */

(function($) {
    'use strict';
    
    var AsapMegamenu = {
        $menuItems: null,
        $body: null,
        isOpen: false,
        currentItem: null,
        
        init: function() {
            this.$body = $('body');
            this.$menuItems = $('.menu-item.has-megamenu');
            
            // También manejar el nuevo sistema de megamenu
            this.bindNewMegamenuEvents();
            
            if (this.$menuItems.length === 0) return;
            
            this.bindEvents();
            this.handleEscape();
            this.handleClickOutside();
        },
        
        bindEvents: function() {
            var self = this;
            
            // Desktop: Click para abrir/cerrar
            this.$menuItems.on('click', '> a', function(e) {
                if ($(window).width() > 992) {
                    e.preventDefault();
                    var $item = $(this).parent();
                    self.toggle($item);
                }
            });
            
            // Botón cerrar
            $(document).on('click', '.asap-megamenu-close', function(e) {
                e.preventDefault();
                self.close();
            });
            
            // Click en overlay
            $(document).on('click', '.asap-megamenu-overlay', function(e) {
                e.preventDefault();
                self.close();
            });
            
            // Prevenir que clicks dentro del megamenu lo cierren
            $(document).on('click', '.asap-megamenu', function(e) {
                e.stopPropagation();
            });
            
            // Mobile: Accordion behavior
            if ($(window).width() <= 992) {
                this.initMobileAccordion();
            }
            
            // Re-inicializar en resize
            var resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if ($(window).width() <= 992) {
                        self.close();
                        self.initMobileAccordion();
                    }
                }, 250);
            });
        },
        
        toggle: function($item) {
            if ($item.hasClass('is-active')) {
                this.close();
            } else {
                this.open($item);
            }
        },
        
        open: function($item) {
            // Cerrar cualquier megamenu abierto
            this.close();
            
            // Abrir el nuevo
            $item.addClass('is-active');
            
            // Get megamenu style
            var style = typeof asapMegamenuConfig !== 'undefined' ? asapMegamenuConfig.style : 'fullscreen';
            
            // Solo bloquear scroll en fullscreen y sidebar
            if (style === 'fullscreen' || style === 'sidebar') {
            this.$body.addClass('megamenu-open').css('overflow', 'hidden');
            } else if (style === 'dropdown') {
                // Para dropdown, no bloqueamos el scroll
                this.$body.addClass('megamenu-open');
            }
            
            this.isOpen = true;
            this.currentItem = $item;
            
            // Animación con delay para stagger effect
            var $columns = $item.find('.asap-megamenu-column');
            $columns.each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
            });
            
            // Accessibility: Focus en el primer enlace
            setTimeout(function() {
                $item.find('.asap-megamenu-column-items a').first().focus();
            }, 400);
            
            // Trigger custom event
            $(document).trigger('asap_megamenu_opened', [$item]);
        },
        
        close: function() {
            if (!this.isOpen) return;
            
            this.$menuItems.removeClass('is-active');
            this.$body.removeClass('megamenu-open').css('overflow', '');
            this.isOpen = false;
            
            // Return focus to trigger
            if (this.currentItem) {
                this.currentItem.find('> a').focus();
                this.currentItem = null;
            }
            
            // Trigger custom event
            $(document).trigger('asap_megamenu_closed');
        },
        
        handleEscape: function() {
            var self = this;
            
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen) {
                    self.close();
                }
            });
        },
        
        handleClickOutside: function() {
            var self = this;
            
            $(document).on('click', function(e) {
                if (self.isOpen && !$(e.target).closest('.menu-item.has-megamenu').length) {
                    self.close();
                }
            });
        },
        
        initMobileAccordion: function() {
            var self = this;
            
            this.$menuItems.each(function() {
                var $item = $(this);
                var $megamenu = $item.find('.asap-megamenu');
                
                // Ocultar por defecto
                $megamenu.hide();
                
                // Toggle accordion
                $item.off('click.accordion').on('click.accordion', '> a', function(e) {
                    e.preventDefault();
                    $megamenu.slideToggle(300);
                    $item.toggleClass('is-active');
                });
            });
        },
        
        // Nuevo sistema de megamenu
        bindNewMegamenuEvents: function() {
            // Toggle megamenu
            $(document).on('click', '.asap-megamenu-toggle', this.toggleNewMegamenu);
            
            // Close megamenu
            $(document).on('click', '.asap-megamenu-close', this.closeNewMegamenu);
            $(document).on('click', '.asap-megamenu-overlay', function(e) {
                if (e.target === this) {
                    AsapMegamenu.closeNewMegamenu();
                }
            });
            
            // Close on escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27 && $('.asap-megamenu-overlay').hasClass('active')) {
                    AsapMegamenu.closeNewMegamenu();
                }
            });
        },
        
        // Toggle nuevo megamenu
        toggleNewMegamenu: function(e) {
            e.preventDefault();
            console.log('ASAP Megamenu toggle clicked');
            
            if ($('.asap-megamenu-overlay').hasClass('active')) {
                console.log('Closing megamenu');
                AsapMegamenu.closeNewMegamenu();
            } else {
                console.log('Opening megamenu');
                AsapMegamenu.openNewMegamenu();
            }
        },
        
        // Open nuevo megamenu
        openNewMegamenu: function() {
            $('.asap-megamenu-overlay').addClass('active').show();
            $('.asap-megamenu-toggle').addClass('active');
            
            // Get megamenu style
            var style = typeof asapMegamenuConfig !== 'undefined' ? asapMegamenuConfig.style : 'fullscreen';
            
            // Solo bloquear scroll en fullscreen y sidebar
            if (style === 'fullscreen' || style === 'sidebar') {
                $('body').addClass('megamenu-open').css('overflow', 'hidden');
            } else if (style === 'dropdown') {
                // Para dropdown, no bloqueamos el scroll
            $('body').addClass('megamenu-open');
            }
        },
        
        // Close nuevo megamenu
        closeNewMegamenu: function() {
            $('.asap-megamenu-overlay').removeClass('active');
            $('.asap-megamenu-toggle').removeClass('active');
            setTimeout(function() {
                $('.asap-megamenu-overlay').hide();
            }, 300);
            $('body').removeClass('megamenu-open').css('overflow', '');
        }
    };
    
    // Init on document ready
    $(document).ready(function() {
        console.log('ASAP Megamenu JS loaded');
        AsapMegamenu.init();
    });
    
    // Re-init on Turbolinks or similar navigation
    $(document).on('turbolinks:load', function() {
        AsapMegamenu.init();
    });
    
})(jQuery);

