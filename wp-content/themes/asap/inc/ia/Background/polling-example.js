/**
 * Ejemplo de Polling para Background Tasks
 * 
 * Evita timeouts dividiendo procesos largos en etapas < 30 segundos
 * 
 * USO:
 * 1. Iniciar tarea
 * 2. Recibir task_id
 * 3. Hacer polling cada 2 segundos
 * 4. Procesar resultado cuando status === 'completed'
 */

// ==================================================
// EJEMPLO 1: Crear Briefing con Polling
// ==================================================

function createBriefingWithPolling(keyword) {
    // 1. Iniciar tarea
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'asap_start_background_task',
            nonce: asapIA.nonce,
            task_type: 'create_briefing',
            task_data: {
                keyword: keyword,
                options: {
                    location: 'Argentina',
                    language: 'es'
                }
            }
        },
        success: function(response) {
            if (response.success) {
                const taskId = response.data.task_id;
                console.log('✅ Tarea iniciada:', taskId);
                
                // 2. Iniciar polling
                startPolling(taskId, function(result) {
                    // 3. Callback cuando termina
                    console.log('✅ Briefing completado:', result);
                    displayBriefing(result.briefing);
                });
            } else {
                console.error('❌ Error:', response.data.message);
            }
        }
    });
}

// ==================================================
// EJEMPLO 2: Generar Artículo con Polling
// ==================================================

function generateArticleWithPolling(params) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'asap_start_background_task',
            nonce: asapIA.nonce,
            task_type: 'generate_article',
            task_data: {
                params: params
            }
        },
        success: function(response) {
            if (response.success) {
                const taskId = response.data.task_id;
                console.log('✅ Generación iniciada:', taskId);
                
                startPolling(taskId, function(result) {
                    console.log('✅ Artículo generado, Post ID:', result.post_id);
                    window.location.href = '/wp-admin/post.php?post=' + result.post_id + '&action=edit';
                });
            }
        }
    });
}

// ==================================================
// SISTEMA DE POLLING REUTILIZABLE
// ==================================================

/**
 * Inicia polling de una tarea
 * 
 * @param {string} taskId - ID de la tarea
 * @param {function} onComplete - Callback cuando termina
 * @param {function} onProgress - Callback de progreso (opcional)
 * @param {function} onError - Callback de error (opcional)
 */
function startPolling(taskId, onComplete, onProgress, onError) {
    let pollInterval;
    
    // Actualizar UI inicial
    showProgressBar();
    
    // Polling cada 2 segundos
    pollInterval = setInterval(function() {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_poll_task_status',
                nonce: asapIA.nonce,
                task_id: taskId
            },
            success: function(response) {
                if (!response.success) {
                    clearInterval(pollInterval);
                    if (onError) {
                        onError(response.data.message);
                    }
                    return;
                }
                
                const task = response.data.task;
                const status = task.status;
                const progress = task.progress;
                
                // Actualizar UI de progreso
                updateProgressBar(progress.percentage, progress.message);
                
                // Callback de progreso (opcional)
                if (onProgress) {
                    onProgress(progress);
                }
                
                // Si completó
                if (status === 'completed') {
                    clearInterval(pollInterval);
                    hideProgressBar();
                    
                    if (onComplete) {
                        onComplete(task.result);
                    }
                }
                
                // Si error
                if (status === 'error') {
                    clearInterval(pollInterval);
                    hideProgressBar();
                    
                    if (onError) {
                        onError(task.error);
                    } else {
                        alert('Error: ' + task.error);
                    }
                }
            },
            error: function(xhr, status, error) {
                clearInterval(pollInterval);
                if (onError) {
                    onError('Error AJAX: ' + error);
                } else {
                    console.error('Error AJAX:', error);
                }
            }
        });
    }, 2000); // Poll cada 2 segundos
    
    // Timeout de seguridad (5 minutos)
    setTimeout(function() {
        clearInterval(pollInterval);
        hideProgressBar();
        if (onError) {
            onError('Timeout: La tarea tomó más de 5 minutos');
        }
    }, 300000); // 5 minutos
}

// ==================================================
// UI HELPERS (Personalizar según tu diseño)
// ==================================================

function showProgressBar() {
    jQuery('#briefing-progress').show();
    jQuery('#briefing-progress .progress-bar').css('width', '0%');
    jQuery('#briefing-progress .progress-message').text('Iniciando...');
}

function updateProgressBar(percentage, message) {
    jQuery('#briefing-progress .progress-bar').css('width', percentage + '%');
    jQuery('#briefing-progress .progress-message').text(message);
    
    console.log(`[${percentage}%] ${message}`);
}

function hideProgressBar() {
    jQuery('#briefing-progress').fadeOut();
}

function displayBriefing(briefing) {
    // Mostrar briefing en la UI
    console.log('Briefing:', briefing);
    jQuery('#briefing-result').html('<pre>' + JSON.stringify(briefing, null, 2) + '</pre>');
}

// ==================================================
// EJEMPLO COMPLETO: Botón que genera con polling
// ==================================================

jQuery(document).ready(function($) {
    
    // Botón: Crear Briefing
    $('#btn-create-briefing').on('click', function() {
        const keyword = $('#keyword-input').val();
        
        if (!keyword) {
            alert('Ingresa una keyword');
            return;
        }
        
        createBriefingWithPolling(keyword);
    });
    
    // Botón: Generar Artículo
    $('#btn-generate-article').on('click', function() {
        const params = {
            h1: $('#h1-input').val(),
            keyword: $('#keyword-input').val(),
            outline: JSON.parse($('#outline-input').val() || '[]'),
            target_len: parseInt($('#length-input').val()) || 2000,
            // ... más params
        };
        
        generateArticleWithPolling(params);
    });
    
});

// ==================================================
// EJEMPLO AVANZADO: Con callback de progreso
// ==================================================

function advancedExample() {
    const keyword = 'mejor hosting wordpress';
    
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'asap_start_background_task',
            nonce: asapIA.nonce,
            task_type: 'create_briefing',
            task_data: { keyword: keyword }
        },
        success: function(response) {
            if (response.success) {
                startPolling(
                    response.data.task_id,
                    
                    // onComplete
                    function(result) {
                        console.log('✅ COMPLETADO');
                        alert('Briefing creado: ' + result.briefing.keyword);
                    },
                    
                    // onProgress
                    function(progress) {
                        console.log('⏳ Progreso:', progress.percentage + '%', progress.message);
                        
                        // Ejemplo: Cambiar color según etapa
                        if (progress.current_step === 0) {
                            $('#progress-bar').addClass('analyzing');
                        } else if (progress.current_step <= 3) {
                            $('#progress-bar').addClass('scraping');
                        } else {
                            $('#progress-bar').addClass('building');
                        }
                    },
                    
                    // onError
                    function(error) {
                        console.error('❌ ERROR:', error);
                        alert('Error: ' + error);
                    }
                );
            }
        }
    });
}

// ==================================================
// HTML SUGERIDO para Progress Bar
// ==================================================

/*
<div id="briefing-progress" style="display: none;">
    <div class="progress-container">
        <div class="progress-bar" style="width: 0%; background: #0073aa; height: 20px;"></div>
    </div>
    <div class="progress-message" style="margin-top: 10px;">Iniciando...</div>
    <div class="progress-details">
        <span class="current-step">0</span> / <span class="total-steps">6</span> etapas
    </div>
</div>

<div id="briefing-result" style="display: none;"></div>
*/

// ==================================================
// VENTAJAS de este sistema:
// ==================================================

/*
✅ Evita timeouts (cada etapa < 30 seg)
✅ Progreso en tiempo real
✅ Mejor UX (usuario ve avance)
✅ Resiliente (si falla una etapa, no pierde todo)
✅ Escalable (agregar más etapas fácilmente)
✅ Compatible con cualquier servidor
✅ No requiere WP-Cron ni jobs complejos
*/

// ==================================================
// FLUJO TÉCNICO:
// ==================================================

/*
FRONTEND (JavaScript):
1. Usuario hace clic
2. AJAX → start_background_task
3. Recibe task_id
4. Loop: cada 2 seg → poll_task_status
5. Cuando status=completed → callback

BACKEND (PHP):
1. start_background_task crea tarea en wp_options
2. poll_task_status:
   a. Lee tarea
   b. Ejecuta UNA etapa (< 30 seg)
   c. Guarda progreso
   d. Retorna estado
3. Frontend recibe estado y vuelve a hacer poll
4. Repite hasta completar todas las etapas

RESULTADO:
- Proceso de 60 segundos → 3 polls × 20 seg cada uno
- Cada poll retorna antes de timeout
- Frontend ve progreso en tiempo real
*/



