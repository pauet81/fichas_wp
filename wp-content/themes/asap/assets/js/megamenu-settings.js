/**
 * ASAP Megamenu Settings JavaScript
 * Constructor visual para megamenus en Settings
 */

(function($) {
    'use strict';

    const ASAP_MegamenuSettings = {
        
        // Estado actual
        currentMegamenu: null,
        isEditing: false,
        
        // Inicialización
        init: function() {
            this.bindEvents();
            this.initSortable();
        },
        
        // Eventos
        bindEvents: function() {
            // Botones principales
            $(document).on('click', '#asap-create-new-megamenu, #asap-create-first-megamenu', this.openBuilder);
            $(document).on('click', '.asap-edit-megamenu', this.editMegamenu);
            $(document).on('click', '.asap-preview-megamenu', this.previewMegamenu);
            $(document).on('click', '.asap-delete-megamenu', this.deleteMegamenu);
            $(document).on('click', '#asap-migrate-megamenus', this.migrateMegamenus);
            
            // Modal
            $(document).on('click', '.asap-megamenu-modal-close, #asap-megamenu-cancel', this.closeModal);
            $(document).on('click', '.asap-megamenu-modal', function(e) {
                if (e.target === this) {
                    ASAP_MegamenuSettings.closeModal();
                }
            });
            
            // Constructor
            $(document).on('click', '#asap-add-column', this.addColumn);
            $(document).on('click', '#asap-reset-builder', this.resetBuilder);
            $(document).on('click', '.asap-delete-column', this.deleteColumn);
            $(document).on('click', '.asap-add-item', this.addItem);
            $(document).on('click', '.asap-delete-item', this.deleteItem);
            
            // Formulario
            $(document).on('change', '#asap-megamenu-layout, #asap-megamenu-columns', this.updateLayout);
            $(document).on('click', '#asap-megamenu-save', this.saveMegamenu);
            
            // Media uploader
            $(document).on('click', '.asap-upload-image', this.openMediaUploader);
        },
        
        // Abrir constructor
        openBuilder: function(e) {
            e.preventDefault();
            ASAP_MegamenuSettings.currentMegamenu = null;
            ASAP_MegamenuSettings.isEditing = false;
            ASAP_MegamenuSettings.showBuilderModal();
        },
        
        // Editar megamenu
        editMegamenu: function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            ASAP_MegamenuSettings.loadMegamenu(id);
        },
        
        // Cargar megamenu
        loadMegamenu: function(id) {
            $.ajax({
                url: ASAP_MEGAMENU.ajax,
                type: 'POST',
                data: {
                    action: 'asap_megamenu_load',
                    nonce: ASAP_MEGAMENU.nonce,
                    id: id
                },
                beforeSend: function() {
                    $('.asap-megamenu-settings').addClass('asap-megamenu-loading');
                },
                success: function(response) {
                    if (response.success) {
                        ASAP_MegamenuSettings.currentMegamenu = response.data;
                        ASAP_MegamenuSettings.isEditing = true;
                        ASAP_MegamenuSettings.showBuilderModal();
                        ASAP_MegamenuSettings.populateForm(response.data);
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                },
                complete: function() {
                    $('.asap-megamenu-settings').removeClass('asap-megamenu-loading');
                }
            });
        },
        
        // Vista previa
        previewMegamenu: function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            
            $.ajax({
                url: ASAP_MEGAMENU.ajax,
                type: 'POST',
                data: {
                    action: 'asap_megamenu_preview',
                    nonce: ASAP_MEGAMENU.nonce,
                    id: id
                },
                beforeSend: function() {
                    $('.asap-megamenu-settings').addClass('asap-megamenu-loading');
                },
                success: function(response) {
                    if (response.success) {
                        ASAP_MegamenuSettings.showPreviewModal(response.data.html);
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                },
                complete: function() {
                    $('.asap-megamenu-settings').removeClass('asap-megamenu-loading');
                }
            });
        },
        
        // Eliminar megamenu
        deleteMegamenu: function(e) {
            e.preventDefault();
            
            if (!confirm(ASAP_MEGAMENU.strings.confirm_delete)) {
                return;
            }
            
            const id = $(this).data('id');
            
            $.ajax({
                url: ASAP_MEGAMENU.ajax,
                type: 'POST',
                data: {
                    action: 'asap_megamenu_delete',
                    nonce: ASAP_MEGAMENU.nonce,
                    id: id
                },
                beforeSend: function() {
                    $('.asap-megamenu-settings').addClass('asap-megamenu-loading');
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                },
                complete: function() {
                    $('.asap-megamenu-settings').removeClass('asap-megamenu-loading');
                }
            });
        },
        
        // Mostrar modal constructor
        showBuilderModal: function() {
            $('#asap-megamenu-builder-modal').show();
            $('#asap-megamenu-modal-title').text(
                ASAP_MegamenuSettings.isEditing ? '✏️ Editar Megamenu' : '🚀 Crear Nuevo Megamenu'
            );
            
            if (!ASAP_MegamenuSettings.isEditing) {
                ASAP_MegamenuSettings.resetBuilder();
            }
        },
        
        // Mostrar modal vista previa
        showPreviewModal: function(html) {
            $('#asap-megamenu-preview-content').html(html);
            $('#asap-megamenu-preview-modal').show();
        },
        
        // Cerrar modal
        closeModal: function() {
            $('.asap-megamenu-modal').hide();
            ASAP_MegamenuSettings.currentMegamenu = null;
            ASAP_MegamenuSettings.isEditing = false;
        },
        
        // Poblar formulario
        populateForm: function(data) {
            $('#asap-megamenu-name').val(data.name);
            $('#asap-megamenu-layout').val(data.layout);
            $('#asap-megamenu-columns').val(data.columns);
            
            // Cargar contenido
            const content = JSON.parse(data.content);
            ASAP_MegamenuSettings.renderColumns(content);
        },
        
        // Agregar columna
        addColumn: function(e) {
            e.preventDefault();
            const columns = $('#asap-megamenu-columns').val();
            const currentColumns = $('.asap-megamenu-column').length;
            
            if (currentColumns >= parseInt(columns)) {
                alert('No puedes agregar más columnas que el límite seleccionado');
                return;
            }
            
            const columnHtml = ASAP_MegamenuSettings.getColumnTemplate();
            $('#asap-megamenu-builder-content').append(columnHtml);
            ASAP_MegamenuSettings.initSortable();
        },
        
        // Eliminar columna
        deleteColumn: function(e) {
            e.preventDefault();
            $(this).closest('.asap-megamenu-column').remove();
        },
        
        // Agregar item
        addItem: function(e) {
            e.preventDefault();
            const column = $(this).closest('.asap-megamenu-column');
            const itemsContainer = column.find('.asap-megamenu-items');
            const itemHtml = ASAP_MegamenuSettings.getItemTemplate();
            itemsContainer.append(itemHtml);
        },
        
        // Eliminar item
        deleteItem: function(e) {
            e.preventDefault();
            $(this).closest('.asap-megamenu-item').remove();
        },
        
        // Resetear constructor
        resetBuilder: function(e) {
            if (e) e.preventDefault();
            
            if (!confirm('¿Estás seguro de que quieres resetear el constructor? Se perderán todos los cambios.')) {
                return;
            }
            
            $('#asap-megamenu-name').val('');
            $('#asap-megamenu-layout').val('grid');
            $('#asap-megamenu-columns').val('4');
            ASAP_MegamenuSettings.renderColumns([]);
        },
        
        // Actualizar layout
        updateLayout: function() {
            const columns = $('#asap-megamenu-columns').val();
            const layout = $('#asap-megamenu-layout').val();
            
            const container = $('#asap-megamenu-builder-content');
            container.removeClass().addClass('asap-megamenu-columns ' + layout + '-' + columns);
            
            // Ajustar columnas existentes
            const currentColumns = $('.asap-megamenu-column').length;
            const targetColumns = parseInt(columns);
            
            if (currentColumns > targetColumns) {
                // Eliminar columnas extras
                $('.asap-megamenu-column').slice(targetColumns).remove();
            } else if (currentColumns < targetColumns) {
                // Agregar columnas faltantes
                for (let i = currentColumns; i < targetColumns; i++) {
                    const columnHtml = ASAP_MegamenuSettings.getColumnTemplate();
                    container.append(columnHtml);
                }
            }
            
            ASAP_MegamenuSettings.initSortable();
        },
        
        // Renderizar columnas
        renderColumns: function(content) {
            const container = $('#asap-megamenu-builder-content');
            container.empty();
            
            if (content.length === 0) {
                // Crear columnas por defecto
                const columns = $('#asap-megamenu-columns').val();
                for (let i = 0; i < parseInt(columns); i++) {
                    const columnHtml = ASAP_MegamenuSettings.getColumnTemplate();
                    container.append(columnHtml);
                }
            } else {
                // Renderizar contenido existente
                content.forEach(function(column) {
                    const columnHtml = ASAP_MegamenuSettings.getColumnTemplate(column);
                    container.append(columnHtml);
                });
            }
            
            ASAP_MegamenuSettings.updateLayout();
        },
        
        // Template de columna
        getColumnTemplate: function(data = {}) {
            const columnIndex = $('.asap-megamenu-column').length;
            
            return `
                <div class="asap-megamenu-column" data-index="${columnIndex}">
                    <div class="asap-megamenu-column-header">
                        <h4 class="asap-megamenu-column-title">Columna ${columnIndex + 1}</h4>
                        <div class="asap-megamenu-column-actions">
                            <button type="button" class="button button-small asap-delete-column">🗑️</button>
                        </div>
                    </div>
                    
                    <div class="asap-megamenu-column-content">
                        <div class="asap-megamenu-column-field">
                            <label>Imagen (opcional)</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="text" class="asap-column-image" placeholder="URL de la imagen" value="${data.image || ''}">
                                <button type="button" class="button asap-upload-image">📷</button>
                            </div>
                        </div>
                        
                        <div class="asap-megamenu-column-field">
                            <label>Icono (opcional)</label>
                            <input type="text" class="asap-column-icon" placeholder="SVG o clase de icono" value="${data.icon || ''}">
                        </div>
                        
                        <div class="asap-megamenu-column-field">
                            <label>Título</label>
                            <input type="text" class="asap-column-title" placeholder="Título de la columna" value="${data.title || ''}">
                        </div>
                        
                        <div class="asap-megamenu-column-field">
                            <label>Descripción</label>
                            <textarea class="asap-column-description" placeholder="Descripción de la columna">${data.description || ''}</textarea>
                        </div>
                        
                        <div class="asap-megamenu-items">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <label style="margin: 0;">Items del menú</label>
                                <button type="button" class="button button-small asap-add-item">➕ Agregar</button>
                            </div>
                            <div class="asap-megamenu-items-list">
                                ${ASAP_MegamenuSettings.renderItems(data.items || [])}
                            </div>
                        </div>
                        
                        <div class="asap-megamenu-column-field">
                            <label>Texto CTA (opcional)</label>
                            <input type="text" class="asap-column-cta-text" placeholder="Texto del botón" value="${data.cta_text || ''}">
                        </div>
                        
                        <div class="asap-megamenu-column-field">
                            <label>URL CTA (opcional)</label>
                            <input type="url" class="asap-column-cta-url" placeholder="https://..." value="${data.cta_url || ''}">
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Renderizar items
        renderItems: function(items) {
            if (items.length === 0) {
                return '<p style="color: #646970; font-size: 12px; margin: 0;">No hay items. Click en "Agregar" para añadir items al menú.</p>';
            }
            
            return items.map(function(item, index) {
                return `
                    <div class="asap-megamenu-item" data-index="${index}">
                        <input type="text" class="asap-item-icon" placeholder="Icono" value="${item.icon || ''}" style="width: 60px;">
                        <input type="text" class="asap-item-title" placeholder="Título del item" value="${item.title || ''}">
                        <input type="url" class="asap-item-url" placeholder="URL" value="${item.url || ''}">
                        <div class="asap-megamenu-item-actions">
                            <button type="button" class="button button-small asap-delete-item">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');
        },
        
        // Template de item
        getItemTemplate: function() {
            return `
                <div class="asap-megamenu-item">
                    <input type="text" class="asap-item-icon" placeholder="Icono" style="width: 60px;">
                    <input type="text" class="asap-item-title" placeholder="Título del item">
                    <input type="url" class="asap-item-url" placeholder="URL">
                    <div class="asap-megamenu-item-actions">
                        <button type="button" class="button button-small asap-delete-item">🗑️</button>
                    </div>
                </div>
            `;
        },
        
        // Inicializar sortable
        initSortable: function() {
            $('.asap-megamenu-columns').sortable({
                handle: '.asap-megamenu-column-header',
                placeholder: 'asap-megamenu-column ui-sortable-placeholder',
                helper: 'clone',
                opacity: 0.8,
                update: function() {
                    // Reindexar columnas
                    $('.asap-megamenu-column').each(function(index) {
                        $(this).attr('data-index', index);
                        $(this).find('.asap-megamenu-column-title').text('Columna ' + (index + 1));
                    });
                }
            });
        },
        
        // Media uploader
        openMediaUploader: function(e) {
            e.preventDefault();
            
            const button = $(this);
            const input = button.siblings('input');
            
            const mediaUploader = wp.media({
                title: 'Seleccionar imagen',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.url);
            });
            
            mediaUploader.open();
        },
        
        // Guardar megamenu
        saveMegamenu: function(e) {
            e.preventDefault();
            
            const name = $('#asap-megamenu-name').val().trim();
            if (!name) {
                alert('El nombre es requerido');
                return;
            }
            
            const layout = $('#asap-megamenu-layout').val();
            const columns = $('#asap-megamenu-columns').val();
            
            // Recopilar datos de las columnas
            const content = [];
            $('.asap-megamenu-column').each(function() {
                const column = $(this);
                const columnData = {
                    image: column.find('.asap-column-image').val(),
                    icon: column.find('.asap-column-icon').val(),
                    title: column.find('.asap-column-title').val(),
                    description: column.find('.asap-column-description').val(),
                    cta_text: column.find('.asap-column-cta-text').val(),
                    cta_url: column.find('.asap-column-cta-url').val(),
                    items: []
                };
                
                // Recopilar items
                column.find('.asap-megamenu-item').each(function() {
                    const item = $(this);
                    const itemData = {
                        icon: item.find('.asap-item-icon').val(),
                        title: item.find('.asap-item-title').val(),
                        url: item.find('.asap-item-url').val()
                    };
                    
                    if (itemData.title) {
                        columnData.items.push(itemData);
                    }
                });
                
                content.push(columnData);
            });
            
            const data = {
                action: 'asap_megamenu_save',
                nonce: ASAP_MEGAMENU.nonce,
                name: name,
                layout: layout,
                columns: columns,
                content: JSON.stringify(content)
            };
            
            if (ASAP_MegamenuSettings.isEditing && ASAP_MegamenuSettings.currentMegamenu) {
                data.id = ASAP_MegamenuSettings.currentMegamenu.id;
            }
            
            $.ajax({
                url: ASAP_MEGAMENU.ajax,
                type: 'POST',
                data: data,
                beforeSend: function() {
                    $('#asap-megamenu-save').text(ASAP_MEGAMENU.strings.saving).prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        alert(ASAP_MEGAMENU.strings.saved);
                        location.reload();
                    } else {
                        alert(ASAP_MEGAMENU.strings.error + ': ' + response.data);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                },
                complete: function() {
                    $('#asap-megamenu-save').text('💾 Guardar Megamenu').prop('disabled', false);
                }
            });
        },
        
        // Migrar megamenus
        migrateMegamenus: function(e) {
            e.preventDefault();
            
            if (!confirm('¿Estás seguro de que quieres migrar los megamenus existentes? Esto creará copias en el nuevo sistema.')) {
                return;
            }
            
            $.ajax({
                url: ASAP_MEGAMENU.ajax,
                type: 'POST',
                data: {
                    action: 'asap_megamenu_migrate',
                    nonce: ASAP_MEGAMENU.nonce
                },
                beforeSend: function() {
                    $('#asap-migrate-megamenus').text('🔄 Migrando...').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                },
                complete: function() {
                    $('#asap-migrate-megamenus').text('🔄 Migrar Megamenus Existentes').prop('disabled', false);
                }
            });
        }
    };
    
    // Inicializar cuando el documento esté listo
    $(document).ready(function() {
        ASAP_MegamenuSettings.init();
    });
    
})(jQuery);
