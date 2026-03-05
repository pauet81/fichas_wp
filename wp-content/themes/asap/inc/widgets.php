<?php
/**
 * Social Buttons Widget
 *
 * @package AsapTheme
 */


class asap_Widget_Social_Buttons extends WP_Widget
{

  function __construct()
  {
    $widget_ops = array(
      'classname' => 'widget-social-buttons',
      'description' => __('Links to your social networks.', 'asap'),
      'customize_selective_refresh' => true,
    );
      parent::__construct('social-buttons-asap', __('ASAP − Social networks', 'asap'), $widget_ops);
  }

  function widget($args, $instance)
  {
    if (! isset($args['widget_id'])) {
      $args['widget_id'] = $this->id;
    }
    
    $title = ( ! empty($instance['title']) ) ? $instance['title'] : __('Follow us', 'asap');
    $title = apply_filters('widget_title', $title, $instance, $this->id_base);
    $fb = ( ! empty($instance['fb']) );
    $tw = ( ! empty($instance['tw']) );
    $ig = ( ! empty($instance['ig']) );
    $yt = ( ! empty($instance['yt']) );
    $pi = ( ! empty($instance['pi']) );
    $tl = ( ! empty($instance['tl']) );
    $tk = ( ! empty($instance['tk']) );
    $lk = ( ! empty($instance['lk']) );
    $em = ( ! empty($instance['em']) );
    $ap = ( ! empty($instance['ap']) );
    $ws = ( ! empty($instance['ws']) );

    ?>
      
    <?php echo $args['before_widget']; ?>
      
    <?php if ($title) { echo $args['before_title'] . $title . $args['after_title']; } ?>

    <?php echo '<div class="asap-content-sb">'; ?>


    <?php if ( $fb ) : ?>
      
    <a title="Facebook" href="<?php echo $instance['fb']; ?>" class="asap-icon icon-facebook" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" /></svg></a>
      
    <?php endif; ?>
      
    <?php if ( $tw ) { ?>
      
    <a title="X" href="<?php echo $instance['tw']; ?>" class="asap-icon icon-twitter" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg></a>
      
    <?php } ?>
      
    <?php if ( $ig ) { ?>
      
    <a title="Instagram" href="<?php echo $instance['ig']; ?>" class="asap-icon icon-instagram" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="4" /><circle cx="12" cy="12" r="3" /><line x1="16.5" y1="7.5" x2="16.5" y2="7.501" /></svg></a>
      
    <?php } ?>
      
    <?php if ( $yt ) { ?>
    
    <a title="Youtube" href="<?php echo $instance['yt']; ?>" class="asap-icon icon-youtube" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="4" /><path d="M10 9l5 3l-5 3z" /></svg></a>
      
    <?php } ?>
      
    <?php if ( $pi ) { ?>
      
    <a title="Pinterest" href="<?php echo $instance['pi']; ?>" class="asap-icon icon-pinterest" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="8" y1="20" x2="12" y2="11" /><path d="M10.7 14c.437 1.263 1.43 2 2.55 2c2.071 0 3.75 -1.554 3.75 -4a5 5 0 1 0 -9.7 1.7" /><circle cx="12" cy="12" r="9" /></svg></a>
      
    <?php } ?>

    <?php if ( $tl ) { ?>
      
    <a title="Telegram" href="<?php echo $instance['tl']; ?>" class="asap-icon icon-telegram" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" /></svg></a>
      
    <?php } ?>

    <?php if ( $tk ) { ?>
      
    <a title="TikTok" href="<?php echo $instance['tk']; ?>" class="asap-icon icon-tiktok" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12a4 4 0 1 0 4 4v-12a5 5 0 0 0 5 5" /></svg></a>
      
    <?php } ?>

    <?php if ( $lk ) { ?>
      
    <a title="LinkedIn" href="<?php echo $instance['lk']; ?>" class="asap-icon icon-linkedin" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="8" y1="11" x2="8" y2="16" /><line x1="8" y1="8" x2="8" y2="8.01" /><line x1="12" y1="16" x2="12" y2="11" /><path d="M16 16v-3a2 2 0 0 0 -4 0" /></svg></a>
      
    <?php } ?>

    <?php if ( $em ) : ?>
      
    <a title="Email" href="<?php echo $instance['em']; ?>" class="asap-icon icon-email"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2" /><polyline points="3 7 12 13 21 7" /></svg></a>
      
    <?php endif; ?>

    <?php if ( $ap ) : ?>
      
    <a title="App" href="<?php echo $instance['ap']; ?>" class="asap-icon icon-app"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="10" x2="4" y2="16" /><line x1="20" y1="10" x2="20" y2="16" /><path d="M7 9h10v8a1 1 0 0 1 -1 1h-8a1 1 0 0 1 -1 -1v-8a5 5 0 0 1 10 0" /><line x1="8" y1="3" x2="9" y2="5" /><line x1="16" y1="3" x2="15" y2="5" /><line x1="9" y1="18" x2="9" y2="21" /><line x1="15" y1="18" x2="15" y2="21" /></svg></a>
      
    <?php endif; ?>

    <?php if ( $ws ) : ?>
      
    <a title="WhatsApp" href="<?php echo $instance['ws']; ?>" class="asap-icon icon-ws">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
      <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
    </svg>
    </a>
          
    <?php endif; ?>
      

    <?php echo '</div>'; ?>

    <?php echo $args['after_widget']; ?>
      
  <?php
    
  }

  function update($new_instance, $old_instance) {

    $instance = $old_instance;
    $instance['title'] = sanitize_text_field($new_instance['title']);   
    $instance['fb'] = sanitize_text_field($new_instance['fb']);
    $instance['tw'] = sanitize_text_field($new_instance['tw']);   
    $instance['ig'] = sanitize_text_field($new_instance['ig']);   
    $instance['yt'] = sanitize_text_field($new_instance['yt']);   
    $instance['pi'] = sanitize_text_field($new_instance['pi']);       
    $instance['tl'] = sanitize_text_field($new_instance['tl']);       
    $instance['tk'] = sanitize_text_field($new_instance['tk']);       
    $instance['lk'] = sanitize_text_field($new_instance['lk']);   
    $instance['em'] = sanitize_text_field($new_instance['em']);   
    $instance['ap'] = sanitize_text_field($new_instance['ap']);           
    $instance['ws'] = sanitize_text_field($new_instance['ws']);           
    return $instance;
  }

  function form($instance) {
    
    $title  = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $fb     = isset($instance['fb']) ? esc_attr($instance['fb']) : '';
    $tw     = isset($instance['tw']) ? esc_attr($instance['tw']) : '';
    $ig     = isset($instance['ig']) ? esc_attr($instance['ig']) : '';
    $yt     = isset($instance['yt']) ? esc_attr($instance['yt']) : '';
    $pi     = isset($instance['pi']) ? esc_attr($instance['pi']) : '';
    $tl     = isset($instance['pi']) ? esc_attr($instance['tl']) : '';
    $tk     = isset($instance['tk']) ? esc_attr($instance['tk']) : '';
    $lk     = isset($instance['lk']) ? esc_attr($instance['lk']) : '';
    $em     = isset($instance['em']) ? esc_attr($instance['em']) : '';
    $ap     = isset($instance['ap']) ? esc_attr($instance['ap']) : '';
    $ws     = isset($instance['ws']) ? esc_attr($instance['ws']) : '';
    ?>
  
    <div class="widget_asap">   
  
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </p>

      <p>
        <label for="<?php echo $this->get_field_id('fb'); ?>"><?php _e('Facebook URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('fb'); ?>" name="<?php echo $this->get_field_name('fb'); ?>" type="text" value="<?php echo $fb; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('tw'); ?>"><?php _e('Twitter URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('tw'); ?>" name="<?php echo $this->get_field_name('tw'); ?>" type="text" value="<?php echo $tw; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('ig'); ?>"><?php _e('Instagram URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('ig'); ?>" name="<?php echo $this->get_field_name('ig'); ?>" type="text" value="<?php echo $ig; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('yt'); ?>"><?php _e('YouTube URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('yt'); ?>" name="<?php echo $this->get_field_name('yt'); ?>" type="text" value="<?php echo $yt; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('pi'); ?>"><?php _e('Pinterest URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('pi'); ?>" name="<?php echo $this->get_field_name('pi'); ?>" type="text" value="<?php echo $pi; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('tl'); ?>"><?php _e('Telegram URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('tl'); ?>" name="<?php echo $this->get_field_name('tl'); ?>" type="text" value="<?php echo $tl; ?>" />
      </p>  
      
      <p>
        <label for="<?php echo $this->get_field_id('tk'); ?>"><?php _e('TikTok URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('tk'); ?>" name="<?php echo $this->get_field_name('tk'); ?>" type="text" value="<?php echo $tk; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('lk'); ?>"><?php _e('LinkedIn URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('lk'); ?>" name="<?php echo $this->get_field_name('lk'); ?>" type="text" value="<?php echo $lk; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('em'); ?>"><?php _e('Contact URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('em'); ?>" name="<?php echo $this->get_field_name('em'); ?>" type="text" value="<?php echo $em; ?>" />
      </p>
      
      <p>
        <label for="<?php echo $this->get_field_id('ap'); ?>"><?php _e('App URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('ap'); ?>" name="<?php echo $this->get_field_name('ap'); ?>" type="text" value="<?php echo $ap; ?>" />
      </p>

    
      <p>
        <label for="<?php echo $this->get_field_id('ws'); ?>"><?php _e('WhatsApp URL', 'asap'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('ws'); ?>" name="<?php echo $this->get_field_name('ws'); ?>" type="text" value="<?php echo $ws; ?>" />
      </p>


    </div>

    <?php
  }
}



add_action( 'widgets_init', 'asap_load_widgets' );

function asap_load_widgets() {
    register_widget( 'asap_Widget_Social_Buttons' );

}
/* ====== Render del índice ====== */
function nr_render_nav($post_id = null, $title = 'Navegación rápida', $style = '1'){
    global $post;
    $p = $post_id ? get_post($post_id) : $post;
    if (!$p || !is_singular()) return '';
    $raw = get_post_field('post_content', $p->ID);
    if (!$raw) return '';

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $raw);
    $h2s = $doc->getElementsByTagName('h2');
    if (!$h2s || $h2s->length === 0) return '';

    $items = []; $used = [];
    foreach ($h2s as $node){
        $text = trim(preg_replace('/\s+/u',' ', $node->textContent));
        if ($text === '') continue;

        $id = $node->getAttribute('id');
        $id = $id ? sanitize_title($id) : sanitize_title($text);

        // Evitar ids duplicados
        $base = $id; $n = 2;
        while (in_array($id, $used, true)) { $id = $base . '-' . $n; $n++; }
        $used[] = $id;

        $items[] = ['id' => $id, 'text' => $text];
    }
    if (empty($items)) return '';

    $style_class = ($style === '2') ? ' toc-rapida--s2' : '';

    ob_start(); ?>
    <nav class="toc-rapida<?php echo $style_class; ?>" aria-label="Navegación rápida">
        <p class="sidebar-title"><?php echo esc_html($title); ?></p>
        <ul class="toc-rapida__list">
            <?php foreach ($items as $it): ?>
                <li class="toc-rapida__item">
                    <a class="toc-rapida__link"
                       href="#<?php echo esc_attr($it['id']); ?>"
                       data-toc-id="<?php echo esc_attr($it['id']); ?>">
                       <?php echo esc_html($it['text']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

<script>
(function () {
  var toc = document.currentScript.parentElement;
  var ul  = toc.querySelector('.toc-rapida__list');

  // Contenedor principal del contenido (más estricto para evitar capturar cosas del layout)
  var content = document.querySelector('.entry-content, .post-content, article .entry-content, main') || document.body;

  var links = Array.from(toc.querySelectorAll('.toc-rapida__link'));
  var sections = [], ids = [], tops = [];
  var lastActive = null, lock = false;
  var LOCK_MS = 400;

  // ------- utils -------
  function slugify(s){
    return (s || '')
      .toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // acentos
      .replace(/[^a-z0-9]+/g, '-')                     // separadores
      .replace(/^-+|-+$/g, '')
      .slice(0, 80) || 'seccion';
  }

  function getStickyOffset(){
    var off = 0, el;
    el = document.getElementById('wpadminbar');
    if (el && getComputedStyle(el).position === 'fixed') off += el.offsetHeight;
    el = document.querySelector('.site-header, header[role="banner"]');
    if (el && getComputedStyle(el).position === 'fixed') off += el.offsetHeight;
    // margen de seguridad
    return off + 8;
  }

  function clearActive(){
    links.forEach(function(a){
      a.classList.remove('is-active');
      if (a.parentElement) a.parentElement.classList.remove('is-active');
    });
    lastActive = null;
  }

  function setActive(id){
    if (lastActive === id) return;
    links.forEach(function(a){
      var on = a.getAttribute('data-toc-id') === id;
      a.classList.toggle('is-active', on);
      if (a.parentElement) a.parentElement.classList.toggle('is-active', on);
    });
    lastActive = id;
  }

  function updateTops(){
    var off = getStickyOffset();
    tops = sections.map(function(el){
      return el.getBoundingClientRect().top + window.pageYOffset - off;
    });
  }

  function pickCurrent(){
    if (lock) return;
    var vh  = window.innerHeight || document.documentElement.clientHeight;
    var ref = window.pageYOffset + getStickyOffset() + vh * 0.35;  // línea de referencia
    var idx = -1;
    for (var i = 0; i < tops.length; i++) {
      if (tops[i] <= ref) idx = i; else break;
    }
    if (idx >= 0) setActive(ids[idx]);
    else if (ids.length) setActive(ids[0]);
  }

  function scrollToId(id){
    var target = document.getElementById(id);
    if (!target) return;
    lock = true;
    setActive(id);

    var root = document.documentElement;
    var prev = root.style.scrollBehavior;
    root.style.scrollBehavior = 'auto';

    var top = target.getBoundingClientRect().top + window.pageYOffset - getStickyOffset();
    window.scrollTo(0, top);

    if (history.pushState) history.pushState(null, '', '#' + id);
    else location.hash = id;

    setTimeout(function(){ root.style.scrollBehavior = prev; lock = false; }, LOCK_MS);
  }

  function bindClicks(){
    links.forEach(function(a){
      if (a.__bound) return;
      a.addEventListener('click', function(e){
        e.preventDefault();
        var id = this.getAttribute('data-toc-id');
        scrollToId(id);
      });
      a.__bound = true;
    });
  }

  function debounce(fn, ms){
    var t; return function(){ clearTimeout(t); t = setTimeout(fn, ms); };
  }
  var scheduleRefresh = debounce(function(){ rebuildFromDOM(); pickCurrent(); }, 60);
  var scheduleUpdate  = debounce(function(){ updateTops(); pickCurrent(); }, 60);

  // --- núcleo: reconstruir ids/links según el DOM real ---
  function rebuildFromDOM(){
var h2s = Array.from(content.querySelectorAll('h2:not(.content-cluster h2):not(.content-cluster *)'));
    var seen = new Set();

    // Asegurar ids únicos en los H2 reales
    ids = h2s.map(function(h, i){
      var id = h.id && h.id.trim() ? h.id.trim() : slugify(h.textContent) || ('seccion-' + (i+1));
      var base = id, n = 2;
      while (seen.has(id)) { id = base + '-' + n; n++; }
      if (h.id !== id) h.id = id;
      seen.add(id);
      return id;
    });
    sections = h2s;

    // Si el TOC server-side no coincide, reconstruimos la lista
    if (links.length !== ids.length){
      ul.innerHTML = ids.map(function(id, i){
        var text = (sections[i].textContent || ('Sección ' + (i+1))).replace(/\s+/g,' ').trim();
        return '<li class="toc-rapida__item"><a class="toc-rapida__link" href="#'+id+'" data-toc-id="'+id+'">'+text+'</a></li>';
      }).join('');
      links = Array.from(toc.querySelectorAll('.toc-rapida__link'));
      bindClicks();
    } else {
      // Si coincide la cantidad, sincronizamos href/data/texto por índice
      links.forEach(function(a, i){
        a.setAttribute('href', '#'+ids[i]);
        a.setAttribute('data-toc-id', ids[i]);
        var txt = (sections[i].textContent || '').replace(/\s+/g,' ').trim();
        if (txt) a.textContent = txt; // opcional: alinear títulos con el DOM real
      });
    }

    updateTops();
  }

  // ---- init ----
  rebuildFromDOM();
  bindClicks();
  clearActive();
  pickCurrent();
  if (!toc.querySelector('.toc-rapida__link.is-active') && links.length) setActive(ids[0]);

  // Recalcular al terminar de cargar todo
  window.addEventListener('load', scheduleRefresh);
  window.addEventListener('resize', function(){ scheduleUpdate(); });

  // Observar cambios del DOM y cargas tardías (Gutenberg/embeds/lazy)
  if (window.MutationObserver){
    var mo = new MutationObserver(scheduleRefresh);
    mo.observe(content, { childList: true, subtree: true });
  }
  if (window.ResizeObserver){
    var ro = new ResizeObserver(scheduleUpdate);
    ro.observe(content);
  }
  Array.from(content.querySelectorAll('img, iframe, video')).forEach(function(el){
    // si aún no estaba cargado, al cargar puede mover el layout
    if (!('complete' in el) || !el.complete) el.addEventListener('load', scheduleUpdate);
  });

  // Si la página llega con #hash, ajustar con offset
  if (location.hash) {
    var id = location.hash.slice(1);
    setTimeout(function(){ scrollToId(id); }, 0);
  }
})();
</script>

    </nav>
    <?php
    return ob_get_clean();
}

/* ====== Widget ====== */
class Nav_Rapida_Widget extends WP_Widget {
    public function __construct(){
        parent::__construct(
            'nav_rapida_widget',
            'ASAP − Índice de contenidos',
            ['description' => 'Tabla de contenidos perfecta para la barra lateral sticky.']
        );
    }

    public function widget($args, $instance){
        if (!is_singular()) return;
        global $post; if (empty($post)) return;

        $title = isset($instance['title_text']) ? $instance['title_text'] : 'Navegación rápida';
        $style = isset($instance['style']) ? $instance['style'] : '1';

        echo $args['before_widget'] ?? '';
        echo nr_render_nav($post->ID, $title, $style);
        echo $args['after_widget'] ?? '';
    }

    public function form($instance){
        $title  = isset($instance['title_text']) ? $instance['title_text'] : 'Navegación rápida';
        $style  = isset($instance['style']) ? $instance['style'] : '1';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title_text')); ?>"><strong>Título:</strong></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title_text')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><strong>Estilo:</strong></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="1" <?php selected($style, '1'); ?>>Estilo 1)</option>
                <option value="2" <?php selected($style, '2'); ?>>Estilo 2</option>
            </select>
        </p>
        <?php
    }

    public function update($new, $old){
        $out = [];
        $out['title_text'] = isset($new['title_text']) ? sanitize_text_field($new['title_text']) : 'Navegación rápida';
        $out['style']      = (isset($new['style']) && $new['style'] === '2') ? '2' : '1';
        return $out;
    }
}
add_action('widgets_init', function(){ register_widget('Nav_Rapida_Widget'); });
