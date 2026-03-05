/**
 * Ejemplo de Controles para Background Tasks
 * 
 * - Pausar ⏸️
 * - Reanudar ▶️
 * - Cancelar ❌
 * - Reiniciar 🔄
 * - Reintentar 🔄
 * 
 * Incluye UI completa y manejo de estados
 */

// ==================================================
// CLASE PARA MANEJAR TAREAS CON CONTROLES
// ==================================================

class TaskController {
    constructor(taskId) {
        this.taskId = taskId;
        this.pollInterval = null;
        this.isPaused = false;
    }
    
    /**
     * Iniciar polling
     */
    start(onComplete, onProgress, onError) {
        this.onComplete = onComplete;
        this.onProgress = onProgress;
        this.onError = onError;
        
        this.poll();
    }
    
    /**
     * Polling loop
     */
    poll() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
        
        this.pollInterval = setInterval(() => {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'asap_poll_task_status',
                    nonce: asapIA.nonce,
                    task_id: this.taskId
                },
                success: (response) => {
                    if (!response.success) {
                        this.stop();
                        if (this.onError) {
                            this.onError(response.data.message);
                        }
                        return;
                    }
                    
                    const task = response.data.task;
                    const status = task.status;
                    
                    // Callback de progreso
                    if (this.onProgress) {
                        this.onProgress(task.progress, task);
                    }
                    
                    // Si completó
                    if (status === 'completed') {
                        this.stop();
                        if (this.onComplete) {
                            this.onComplete(task.result, task);
                        }
                    }
                    
                    // Si está pausada, no hacer nada (esperar resume)
                    if (status === 'paused') {
                        this.isPaused = true;
                    } else {
                        this.isPaused = false;
                    }
                    
                    // Si canceló
                    if (status === 'cancelled') {
                        this.stop();
                        if (this.onError) {
                            this.onError('Tarea cancelada por el usuario');
                        }
                    }
                    
                    // Si error final (sin más reintentos)
                    if ((status === 'error' || status === 'timeout') && task.current_retry >= 3) {
                        this.stop();
                        if (this.onError) {
                            this.onError(task.error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.stop();
                    if (this.onError) {
                        this.onError('Error AJAX: ' + error);
                    }
                }
            });
        }, 2000); // Poll cada 2 seg
    }
    
    /**
     * Detener polling
     */
    stop() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }
    
    /**
     * Pausar tarea
     */
    pause(callback) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_pause_task',
                nonce: asapIA.nonce,
                task_id: this.taskId
            },
            success: (response) => {
                if (response.success) {
                    console.log('✅ Tarea pausada');
                    if (callback) callback(response.data.task);
                } else {
                    console.error('❌ Error al pausar:', response.data.message);
                }
            }
        });
    }
    
    /**
     * Reanudar tarea pausada
     */
    resume(callback) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_resume_task',
                nonce: asapIA.nonce,
                task_id: this.taskId
            },
            success: (response) => {
                if (response.success) {
                    console.log('✅ Tarea reanudada');
                    this.isPaused = false;
                    if (callback) callback(response.data.task);
                } else {
                    console.error('❌ Error al reanudar:', response.data.message);
                }
            }
        });
    }
    
    /**
     * Cancelar tarea
     */
    cancel(callback) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_cancel_task',
                nonce: asapIA.nonce,
                task_id: this.taskId
            },
            success: (response) => {
                if (response.success) {
                    console.log('✅ Tarea cancelada');
                    this.stop();
                    if (callback) callback(response.data.task);
                } else {
                    console.error('❌ Error al cancelar:', response.data.message);
                }
            }
        });
    }
    
    /**
     * Reiniciar tarea desde el principio
     */
    restart(callback) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_restart_task',
                nonce: asapIA.nonce,
                task_id: this.taskId
            },
            success: (response) => {
                if (response.success) {
                    console.log('✅ Tarea reiniciada');
                    
                    // Reiniciar polling
                    if (response.data.auto_resume) {
                        this.poll();
                    }
                    
                    if (callback) callback(response.data.task);
                } else {
                    console.error('❌ Error al reiniciar:', response.data.message);
                }
            }
        });
    }
    
    /**
     * Reintentar tarea fallida (desde último checkpoint)
     */
    retry(callback) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asap_retry_task',
                nonce: asapIA.nonce,
                task_id: this.taskId
            },
            success: (response) => {
                if (response.success) {
                    console.log('✅ Reintentando tarea');
                    
                    // Reiniciar polling
                    if (response.data.auto_resume) {
                        this.poll();
                    }
                    
                    if (callback) callback(response.data.task);
                } else {
                    console.error('❌ Error al reintentar:', response.data.message);
                }
            }
        });
    }
}

// ==================================================
// EJEMPLO COMPLETO DE USO
// ==================================================

let taskController;

jQuery(document).ready(function($) {
    
    // Iniciar nueva tarea
    $('#btn-start-briefing').on('click', function() {
        const keyword = $('#keyword-input').val();
        
        $.ajax({
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
                    
                    // Crear controlador
                    taskController = new TaskController(taskId);
                    
                    // Mostrar controles
                    showTaskControls();
                    
                    // Iniciar polling
                    taskController.start(
                        // onComplete
                        function(result, task) {
                            console.log('✅ COMPLETADO:', result);
                            hideTaskControls();
                            displayResult(result.briefing);
                        },
                        // onProgress
                        function(progress, task) {
                            updateProgressUI(progress);
                            
                            // Mostrar info de reintentos si hay
                            if (task.current_retry > 0) {
                                showRetryInfo(task.current_retry, task.retries);
                            }
                        },
                        // onError
                        function(error) {
                            console.error('❌ ERROR:', error);
                            showErrorUI(error);
                            
                            // Mostrar botón de reintentar
                            $('#btn-retry').show();
                        }
                    );
                }
            }
        });
    });
    
    // Botón: Pausar
    $('#btn-pause').on('click', function() {
        if (taskController) {
            taskController.pause(function(task) {
                updateButtonStates('paused');
                $('#status-message').text('⏸️ Tarea pausada');
            });
        }
    });
    
    // Botón: Reanudar
    $('#btn-resume').on('click', function() {
        if (taskController) {
            taskController.resume(function(task) {
                updateButtonStates('processing');
                $('#status-message').text('▶️ Reanudando...');
            });
        }
    });
    
    // Botón: Cancelar
    $('#btn-cancel').on('click', function() {
        if (confirm('¿Seguro que quieres cancelar esta tarea?')) {
            if (taskController) {
                taskController.cancel(function(task) {
                    hideTaskControls();
                    $('#status-message').text('❌ Tarea cancelada');
                });
            }
        }
    });
    
    // Botón: Reiniciar
    $('#btn-restart').on('click', function() {
        if (confirm('¿Reiniciar desde el principio?')) {
            if (taskController) {
                taskController.restart(function(task) {
                    updateButtonStates('processing');
                    resetProgressUI();
                    $('#status-message').text('🔄 Reiniciando...');
                });
            }
        }
    });
    
    // Botón: Reintentar
    $('#btn-retry').on('click', function() {
        if (taskController) {
            taskController.retry(function(task) {
                updateButtonStates('processing');
                $('#btn-retry').hide();
                $('#status-message').text('🔄 Reintentando desde etapa ' + task.progress.current_step + '...');
            });
        }
    });
    
});

// ==================================================
// FUNCIONES UI
// ==================================================

function showTaskControls() {
    jQuery('#task-controls').show();
    updateButtonStates('processing');
}

function hideTaskControls() {
    jQuery('#task-controls').hide();
}

function updateButtonStates(status) {
    const $ = jQuery;
    
    if (status === 'processing') {
        $('#btn-pause').prop('disabled', false).show();
        $('#btn-resume').prop('disabled', true).hide();
        $('#btn-cancel').prop('disabled', false).show();
        $('#btn-restart').prop('disabled', false).show();
    } else if (status === 'paused') {
        $('#btn-pause').prop('disabled', true).hide();
        $('#btn-resume').prop('disabled', false).show();
        $('#btn-cancel').prop('disabled', false).show();
        $('#btn-restart').prop('disabled', false).show();
    }
}

function updateProgressUI(progress) {
    const $ = jQuery;
    
    $('#progress-bar').css('width', progress.percentage + '%');
    $('#progress-message').text(progress.message);
    $('#progress-steps').text(progress.current_step + ' / ' + progress.total_steps);
    
    console.log('[' + progress.percentage + '%] ' + progress.message);
}

function resetProgressUI() {
    updateProgressUI({
        percentage: 0,
        message: 'Reiniciando...',
        current_step: 0,
        total_steps: 6
    });
}

function showRetryInfo(currentRetry, retries) {
    const $ = jQuery;
    
    $('#retry-info').show().html(
        '⚠️ Reintentos: ' + currentRetry + ' / 3<br>' +
        '<small>Último error: ' + (retries[retries.length - 1]?.error || 'timeout') + '</small>'
    );
}

function showErrorUI(error) {
    const $ = jQuery;
    
    $('#error-container').show().html(
        '<div class="error-message">' +
        '<strong>❌ Error:</strong> ' + error +
        '</div>'
    );
}

function displayResult(briefing) {
    const $ = jQuery;
    
    $('#result-container').show().html(
        '<h3>✅ Briefing Completado</h3>' +
        '<pre>' + JSON.stringify(briefing, null, 2) + '</pre>'
    );
}

// ==================================================
// HTML SUGERIDO
// ==================================================

/*
<div id="task-container">
    <div class="input-group">
        <input type="text" id="keyword-input" placeholder="Keyword...">
        <button id="btn-start-briefing" class="button button-primary">
            🚀 Crear Briefing
        </button>
    </div>
    
    <div id="task-controls" style="display: none;">
        <div id="progress-container">
            <div class="progress-bar-wrapper">
                <div id="progress-bar" class="progress-bar"></div>
            </div>
            <div id="progress-message">Iniciando...</div>
            <div id="progress-steps">0 / 6 etapas</div>
        </div>
        
        <div id="retry-info" style="display: none;"></div>
        
        <div class="button-group">
            <button id="btn-pause" class="button">⏸️ Pausar</button>
            <button id="btn-resume" class="button" style="display: none;">▶️ Reanudar</button>
            <button id="btn-cancel" class="button">❌ Cancelar</button>
            <button id="btn-restart" class="button">🔄 Reiniciar</button>
            <button id="btn-retry" class="button" style="display: none;">🔄 Reintentar</button>
        </div>
        
        <div id="status-message"></div>
    </div>
    
    <div id="error-container" style="display: none;"></div>
    <div id="result-container" style="display: none;"></div>
</div>
*/

// ==================================================
// CSS SUGERIDO
// ==================================================

/*
.progress-bar-wrapper {
    width: 100%;
    height: 20px;
    background: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #0073aa, #005177);
    width: 0%;
    transition: width 0.3s ease;
}

#progress-message {
    font-weight: bold;
    color: #0073aa;
    margin: 10px 0;
}

#progress-steps {
    font-size: 12px;
    color: #666;
}

.button-group {
    margin: 15px 0;
    display: flex;
    gap: 10px;
}

.button-group .button {
    flex: 1;
}

#retry-info {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
}

.error-message {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    color: #721c24;
}
*/

// ==================================================
// VENTAJAS DEL SISTEMA
// ==================================================

/*
✅ PAUSAR:
   - Usuario puede pausar si tarda mucho
   - Libera recursos temporalmente
   - Puede reanudar después

✅ CANCELAR:
   - Detiene todo inmediatamente
   - No gasta más API calls
   - Limpia estado

✅ REINICIAR:
   - Empieza desde cero
   - Útil si cambió parámetros
   - Limpia todo y reinicia

✅ REINTENTAR:
   - Continúa desde última etapa exitosa
   - No repite trabajo ya hecho
   - Automático si hay error (3 veces)

✅ REINTENTOS AUTOMÁTICOS:
   - Si etapa falla → reintenta 3 veces
   - Si timeout → reintenta 3 veces
   - Usuario ve progreso de reintentos

RESULT:
- UX superior
- Control total del usuario
- Resiliente a errores
- No gasta recursos innecesarios
*/



