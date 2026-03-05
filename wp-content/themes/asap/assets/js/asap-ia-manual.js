jQuery(function($){
  var params = new URLSearchParams(window.location.search);
  if (params.get('page') !== 'asap-menu-ia') return;
  if (params.get('tab') && params.get('tab') !== 'ia_new') return; // solo en la pestaña "Nuevo"

  var $form  = $('#asap-ia-settings');
  if (!$form.length) return;

  var $btn   = $('#asap_manual_rewrite');
  var $spin  = $('#asap_manual_spinner');
  var $bar   = $('#asap_manual_progress_bar');
  var $pwrap = $('#asap_manual_progress');
  var $ptext = $('#asap_manual_progress_text');
  var $res   = $('#asap_manual_result');

  function progress(pct, text){
    $pwrap.show();
    $bar.css('width', pct + '%');
    if (text) $ptext.text(text);
  }
  function disableUI(disabled) {
    $btn.prop('disabled', disabled);
    if (disabled) { $spin.addClass('is-active'); } else { $spin.removeClass('is-active'); }
  }

  // Evitar que Enter en el input URL envíe el form (solo queremos AJAX)
  $('#asap_manual_url').on('keypress', function(e){
    if (e.which === 13) { e.preventDefault(); $btn.click(); }
  });

  $btn.on('click', function(){
    var url = $('#asap_manual_url').val();
    if (!url || !/^https?:\/\//i.test(url)) {
      alert('Introduce una URL válida.');
      return;
    }

    // Tomamos los valores actuales del mismo form (sin necesidad de guardar)
    var status     = $('#asap_ia_default_status').val();
    var post_type  = $('#asap_ia_default_post_type').val();
    var author     = $('#asap_ia_default_author').val();
    if (author === '-1') author = 0; // — Automático —
    var lang       = $('#asap_ia_default_lang').val();
    var style      = $('#asap_ia_default_style').val();
    var featured   = $('#asap_ia_featured_image_mode').val();
    var simThr     = $('#asap_ia_similarity_threshold').val();
    var blockKws   = $('#asap_ia_block_keywords').val();
    var titleMode  = $('#asap_manual_title_mode').val();
    var extra      = $('#asap_manual_extra_prompt').val();
    var nonce      = $('#asap_manual_rewrite_nonce').val(); // hidden nonce del mismo form

    $res.empty();
    disableUI(true);
    progress(8, 'Descargando URL...');

    $.ajax({
      url:  ASAP_IA.ajax,
      type: 'POST',
      data: {
        action:       'asap_manual_rewrite',
        nonce:        nonce,
        url:          url,
        status:       status,
        post_type:    post_type,
        author:       author,
        lang:         lang,
        style:        style,
        title_mode:   titleMode,
        featured:     featured,
        extra:        extra,
        // overrides (sin guardar)
        sim_threshold:  simThr,
        block_keywords: blockKws
      },
      beforeSend: function(){ progress(18, 'Extrayendo contenido...'); },
      success: function(resp){
        if (!resp || !resp.success) {
          var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Ocurrió un error.';
          $res.html('<div class="notice notice-error"><p><strong>Error:</strong> '+ msg +'</p></div>');
          return;
        }
        progress(95, 'Creando post...');
        var d = resp.data;
        var html = '<div class="notice notice-success"><p><strong>'+ d.message +'</strong></p>'+
                   '<p><a class="button button-primary" href="'+ d.edit_url +'">Editar post</a> '+
                   '<a class="button" target="_blank" href="'+ d.view_url +'">Ver</a></p></div>';
        $res.html(html);
        progress(100, 'Hecho.');
      },
      error: function(_, __, err){
        $res.html('<div class="notice notice-error"><p><strong>Error:</strong> '+ (err || 'Error de red') +'</p></div>');
      },
      complete: function(){
        disableUI(false);
        setTimeout(function(){ progress(100,''); }, 500);
      }
    });
  });
});
