/**
 * ASAP Megamenu - Admin Constructor Visual
 * Full Screen Builder para crear megamenus profesionales
 */

(function($) {
    'use strict';
    
    var AsapMegamenuBuilder = {
        currentItemId: null,
        currentColumns: [],
        selectedColumnIndex: null,
        
        init: function() {
            this.bindEvents();
            this.initSortable();
        },
        
        bindEvents: function() {
            var self = this;
            
            // Toggle megamenu options
            $(document).on('change', '.asap-megamenu-toggle', function() {
                $(this).closest('.asap-megamenu-settings').find('.asap-megamenu-options').toggle($(this).is(':checked'));
            });
            
            // Abrir builder
            $(document).on('click', '.asap-open-builder', function(e) {
                e.preventDefault();
                self.currentItemId = $(this).data('item-id');
                self.openBuilder();
            });
            
            // Cerrar builder
            $(document).on('click', '.asap-builder-close, .asap-builder-overlay', function() {
                self.closeBuilder();
            });
            
            // Agregar columna
            $(document).on('click', '#asap-add-column', function() {
                self.addColumn();
            });
            
            // Seleccionar columna
            $(document).on('click', '.asap-builder-column', function() {
                self.selectColumn($(this).index());
            });
            
            // Eliminar columna
            $(document).on('click', '.delete-column', function(e) {
                e.stopPropagation();
                if (confirm(asapMegamenu.strings.delete_confirm)) {
                    var index = $(this).closest('.asap-builder-column').index();
                    self.deleteColumn(index);
                }
            });
            
            // Guardar builder
            $(document).on('click', '#asap-save-builder', function() {
                self.saveBuilder();
            });
            
            // Agregar item a columna
            $(document).on('click', '.asap-add-item', function() {
                self.addItemToColumn();
            });
            
            // Eliminar item
            $(document).on('click', '.asap-builder-item-delete', function() {
                $(this).closest('.asap-builder-item').remove();
                self.updateColumnFromEditor();
            });
            
            // Upload de imagen
            $(document).on('click', '.asap-upload-image', function(e) {
                e.preventDefault();
                self.openMediaUploader($(this));
            });
            
            // Remover imagen
            $(document).on('click', '.asap-remove-image', function(e) {
                e.preventDefault();
                $('#column-image').val('');
                $('.asap-builder-field-image-preview').html('<span>Sin imagen</span>').addClass('empty');
                self.updateColumnFromEditor();
            });
            
            // Actualizar columna en tiempo real
            $(document).on('input change', '.asap-builder-sidebar input, .asap-builder-sidebar textarea, .asap-builder-sidebar select', function() {
                self.updateColumnFromEditor();
            });
            
            // Actualizar items en tiempo real
            $(document).on('input', '.asap-builder-item-fields input', function() {
                self.updateColumnFromEditor();
            });
        },
        
        initSortable: function() {
            var self = this;
            
            // Sortable para columnas
            $('#asap-builder-columns').sortable({
                handle: '.asap-builder-column-drag',
                opacity: 0.8,
                placeholder: 'ui-sortable-placeholder',
                tolerance: 'pointer',
                cursor: 'move',
                update: function() {
                    self.reorderColumns();
                }
            });
            
            // Sortable para items dentro de columna (se inicializa dinámicamente)
        },
        
        openBuilder: function() {
            var self = this;
            
            // Mostrar modal
            $('#asap-megamenu-builder-modal').fadeIn(300);
            $('body').css('overflow', 'hidden');
            
            // Cargar contenido
            this.loadBuilderContent();
        },
        
        closeBuilder: function() {
            $('#asap-megamenu-builder-modal').fadeOut(300);
            $('body').css('overflow', '');
            
            this.currentColumns = [];
            this.selectedColumnIndex = null;
            $('#asap-builder-columns').empty();
            this.showEditor(false);
        },
        
        loadBuilderContent: function() {
            var self = this;
            
            $('#asap-builder-columns').html('<div class="asap-builder-loading"><div class="asap-builder-spinner"></div><p>Cargando...</p></div>');
            
            $.ajax({
                url: asapMegamenu.ajax_url,
                type: 'POST',
                data: {
                    action: 'asap_get_megamenu_content',
                    nonce: asapMegamenu.nonce,
                    item_id: self.currentItemId
                },
                success: function(response) {
                    if (response.success) {
                        var content = JSON.parse(response.data.content);
                        self.currentColumns = content.length > 0 ? content : [];
                        self.renderColumns();
                    } else {
                        alert('Error al cargar contenido');
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        },
        
        renderColumns: function() {
            var $container = $('#asap-builder-columns');
            $container.empty();
            
            if (this.currentColumns.length === 0) {
                $container.html(this.getEmptyState());
                return;
            }
            
            $.each(this.currentColumns, function(index, column) {
                $container.append(this.renderColumn(column, index));
            }.bind(this));
            
            // Reinicializar sortable
            this.initSortable();
        },
        
        getEmptyState: function() {
            return '<div class="asap-builder-empty">' +
                '<div class="asap-builder-empty-icon">📋</div>' +
                '<h3>Aún no hay columnas</h3>' +
                '<p>Click en "Agregar Columna" para empezar a crear tu megamenu</p>' +
                '</div>';
        },
        
        renderColumn: function(column, index) {
            var html = '<div class="asap-builder-column" data-index="' + index + '">';
            
            // Header
            html += '<div class="asap-builder-column-header">';
            html += '<span class="asap-builder-column-drag">⠿</span>';
            html += '<span class="asap-builder-column-title">' + (column.title || 'Columna ' + (index + 1)) + '</span>';
            html += '<div class="asap-builder-column-actions">';
            html += '<button class="delete-column" title="Eliminar">🗑️</button>';
            html += '</div>';
            html += '</div>';
            
            // Preview
            html += '<div class="asap-builder-column-preview">';
            
            if (column.image) {
                html += '<img src="' + column.image + '" class="asap-builder-column-preview-image">';
            }
            
            if (column.icon) {
                html += '<div class="asap-builder-column-preview-icon">' + column.icon + '</div>';
            }
            
            if (column.description) {
                html += '<p>' + column.description + '</p>';
            }
            
            if (column.items && column.items.length > 0) {
                html += '<ul class="asap-builder-column-preview-items">';
                $.each(column.items, function(i, item) {
                    html += '<li>' + (item.text || 'Item ' + (i + 1)) + '</li>';
                });
                html += '</ul>';
            }
            
            if (column.cta_text) {
                html += '<p style="margin-top:10px;"><strong>CTA:</strong> ' + column.cta_text + '</p>';
            }
            
            html += '</div>';
            html += '</div>';
            
            return html;
        },
        
        addColumn: function() {
            var newColumn = {
                type: 'standard',
                title: 'Nueva Columna',
                description: '',
                image: '',
                icon: '',
                items: [],
                cta_text: '',
                cta_url: '',
                featured: false
            };
            
            this.currentColumns.push(newColumn);
            this.renderColumns();
            
            // Seleccionar la nueva columna
            this.selectColumn(this.currentColumns.length - 1);
        },
        
        deleteColumn: function(index) {
            this.currentColumns.splice(index, 1);
            this.renderColumns();
            
            if (this.selectedColumnIndex === index) {
                this.selectedColumnIndex = null;
                this.showEditor(false);
            }
        },
        
        selectColumn: function(index) {
            this.selectedColumnIndex = index;
            
            // Visual feedback
            $('.asap-builder-column').removeClass('is-selected');
            $('.asap-builder-column').eq(index).addClass('is-selected');
            
            // Mostrar editor
            this.showEditor(true);
            this.loadEditor(this.currentColumns[index]);
        },
        
        showEditor: function(show) {
            if (show) {
                $('#asap-builder-editor').html(this.getEditorHTML());
                this.initItemsSortable();
            } else {
                $('#asap-builder-editor').html('<p style="color: #666; text-align: center; padding: 40px 20px;">Selecciona una columna para editarla</p>');
            }
        },
        
        getEditorHTML: function() {
            return '<div class="asap-builder-editor-content">' +
                '<div class="asap-builder-field">' +
                    '<label>Título</label>' +
                    '<input type="text" id="column-title" placeholder="Ej: Servicios">' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>Descripción</label>' +
                    '<textarea id="column-description" placeholder="Descripción breve de la columna"></textarea>' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>Imagen</label>' +
                    '<div class="asap-builder-field-image">' +
                        '<div class="asap-builder-field-image-preview empty"><span>Sin imagen</span></div>' +
                        '<div class="asap-builder-field-buttons">' +
                            '<button type="button" class="button asap-upload-image">Subir imagen</button>' +
                            '<button type="button" class="button asap-remove-image">Quitar</button>' +
                        '</div>' +
                    '</div>' +
                    '<input type="hidden" id="column-image">' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>Icono (emoji o HTML)</label>' +
                    '<input type="text" id="column-icon" placeholder="Ej: 🚀 o <svg>...</svg>">' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>Items / Enlaces</label>' +
                    '<div class="asap-builder-items-list" id="column-items-list"></div>' +
                    '<button type="button" class="asap-add-item">+ Agregar Item</button>' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>Botón CTA - Texto</label>' +
                    '<input type="text" id="column-cta-text" placeholder="Ej: Ver más">' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>Botón CTA - URL</label>' +
                    '<input type="url" id="column-cta-url" placeholder="https://">' +
                '</div>' +
                
                '<div class="asap-builder-field">' +
                    '<label>' +
                        '<input type="checkbox" id="column-featured"> ' +
                        'Marcar como destacada (solo layout Featured)' +
                    '</label>' +
                '</div>' +
            '</div>';
        },
        
        loadEditor: function(column) {
            $('#column-title').val(column.title || '');
            $('#column-description').val(column.description || '');
            $('#column-image').val(column.image || '');
            $('#column-icon').val(column.icon || '');
            $('#column-cta-text').val(column.cta_text || '');
            $('#column-cta-url').val(column.cta_url || '');
            $('#column-featured').prop('checked', column.featured || false);
            
            // Imagen preview
            if (column.image) {
                $('.asap-builder-field-image-preview').html('<img src="' + column.image + '">').removeClass('empty');
            }
            
            // Items
            this.renderItems(column.items || []);
        },
        
        renderItems: function(items) {
            var $list = $('#column-items-list');
            $list.empty();
            
            $.each(items, function(index, item) {
                $list.append(this.renderItem(item, index));
            }.bind(this));
            
            this.initItemsSortable();
        },
        
        renderItem: function(item, index) {
            return '<div class="asap-builder-item" data-index="' + index + '">' +
                '<span class="asap-builder-item-drag">⠿</span>' +
                '<div class="asap-builder-item-fields">' +
                    '<input type="text" class="item-text" placeholder="Texto del enlace" value="' + (item.text || '') + '">' +
                    '<input type="url" class="item-url" placeholder="https://" value="' + (item.url || '') + '">' +
                    '<input type="text" class="item-icon" placeholder="Icono (opcional)" value="' + (item.icon || '') + '">' +
                '</div>' +
                '<button class="asap-builder-item-delete">✕</button>' +
            '</div>';
        },
        
        initItemsSortable: function() {
            var self = this;
            
            $('#column-items-list').sortable({
                handle: '.asap-builder-item-drag',
                opacity: 0.8,
                placeholder: 'ui-sortable-placeholder',
                update: function() {
                    self.updateColumnFromEditor();
                }
            });
        },
        
        addItemToColumn: function() {
            var $list = $('#column-items-list');
            var newItem = {
                text: '',
                url: '',
                icon: ''
            };
            
            var index = $list.children().length;
            $list.append(this.renderItem(newItem, index));
            
            this.initItemsSortable();
            this.updateColumnFromEditor();
        },
        
        updateColumnFromEditor: function() {
            if (this.selectedColumnIndex === null) return;
            
            var column = {
                type: 'standard',
                title: $('#column-title').val(),
                description: $('#column-description').val(),
                image: $('#column-image').val(),
                icon: $('#column-icon').val(),
                cta_text: $('#column-cta-text').val(),
                cta_url: $('#column-cta-url').val(),
                featured: $('#column-featured').is(':checked'),
                items: []
            };
            
            // Recopilar items
            $('#column-items-list .asap-builder-item').each(function() {
                var text = $(this).find('.item-text').val();
                var url = $(this).find('.item-url').val();
                var icon = $(this).find('.item-icon').val();
                
                if (text || url) {
                    column.items.push({
                        text: text,
                        url: url,
                        icon: icon
                    });
                }
            });
            
            this.currentColumns[this.selectedColumnIndex] = column;
            
            // Re-renderizar solo la columna actual
            var $column = $('.asap-builder-column').eq(this.selectedColumnIndex);
            var html = this.renderColumn(column, this.selectedColumnIndex);
            $column.replaceWith(html);
            
            // Mantener seleccionada
            $('.asap-builder-column').eq(this.selectedColumnIndex).addClass('is-selected');
        },
        
        reorderColumns: function() {
            var newOrder = [];
            
            $('.asap-builder-column').each(function() {
                var index = $(this).data('index');
                newOrder.push(this.currentColumns[index]);
            }.bind(this));
            
            this.currentColumns = newOrder;
            
            // Re-renderizar para actualizar índices
            this.renderColumns();
            
            // Mantener selección
            if (this.selectedColumnIndex !== null) {
                this.selectColumn(this.selectedColumnIndex);
            }
        },
        
        openMediaUploader: function($button) {
            var self = this;
            
            var mediaUploader = wp.media({
                title: 'Seleccionar imagen',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#column-image').val(attachment.url);
                $('.asap-builder-field-image-preview').html('<img src="' + attachment.url + '">').removeClass('empty');
                self.updateColumnFromEditor();
            });
            
            mediaUploader.open();
        },
        
        saveBuilder: function() {
            var self = this;
            var $button = $('#asap-save-builder');
            
            $button.prop('disabled', true).html('<span class="dashicons dashicons-update"></span> Guardando...');
            
            $.ajax({
                url: asapMegamenu.ajax_url,
                type: 'POST',
                data: {
                    action: 'asap_save_megamenu_builder',
                    nonce: asapMegamenu.nonce,
                    item_id: self.currentItemId,
                    content: JSON.stringify(self.currentColumns)
                },
                success: function(response) {
                    if (response.success) {
                        self.showMessage('success', response.data.message || asapMegamenu.strings.save_success);
                        
                        // Actualizar hidden field en el menú
                        $('.asap-megamenu-content-input').filter(function() {
                            return $(this).attr('name') === 'asap_megamenu_content[' + self.currentItemId + ']';
                        }).val(JSON.stringify(self.currentColumns));
                        
                        // Cerrar después de 1 segundo
                        setTimeout(function() {
                            self.closeBuilder();
                        }, 1000);
                    } else {
                        self.showMessage('error', response.data.message || asapMegamenu.strings.save_error);
                    }
                },
                error: function() {
                    self.showMessage('error', 'Error de conexión');
                },
                complete: function() {
                    $button.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Guardar');
                }
            });
        },
        
        showMessage: function(type, message) {
            var $message = $('<div class="asap-builder-message ' + type + '">' + message + '</div>');
            $('body').append($message);
            
            setTimeout(function() {
                $message.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };
    
    // Init
    $(document).ready(function() {
        AsapMegamenuBuilder.init();
    });
    
})(jQuery);





