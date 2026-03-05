<?php

function asap_sanitize_check( $value ) {
	  if( ! empty( $value ) ) {
		return 1; 
	  } else { 
		return 0;  
	  }
}


/* 
 * POST METABOX
 */


add_action( 'add_meta_boxes', 'asap_posts_meta_boxes', 10);

function asap_posts_meta_boxes() 
{
	add_meta_box( 'post-meta-box',
				 __( 'ASAP − Post Options', 'asap' ), 
				 'asap_meta_box_callback', 
				 'post',
				 'normal', 
				 'high', 
				 array( 'arg' => 'value') 
				);
	
	add_meta_box( 'asap-anchor-text', 
				 __( 'ASAP − Anchor text Options', 'asap' ),
				 'asap_anchor_text_callback', 
				 'post', 
				 'normal', 
				 'high', 
				 array( 'arg' => 'value') 
				);
}

function asap_meta_box_callback( $post ) 
{
	
	wp_nonce_field( 'asap_custom_box_posts', 'asap_custom_box_posts_nonce' );
	
    $post_meta 							= get_post_custom( $post->ID );   

    $box_design 						= get_theme_mod('asap_design');
	$hero_post 							= get_theme_mod('asap_hero_post', 'normal');
	
	$asap_featured_post 				= get_post_meta( $post->ID, 'featured_post', true );
	$asap_hide_image_post 				= get_post_meta( $post->ID, 'hide_image_post', true );  
	$asap_subtitle_post 				= get_post_meta( $post->ID, 'subtitle_post', true );
	$asap_hide_related_post 			= get_post_meta( $post->ID, 'hide_related_post', true );
	$asap_hide_sidebar 					= get_post_meta( $post->ID, 'hide_sidebar', true );
	$asap_hide_ads 						= get_post_meta( $post->ID, 'hide_ads', true );
	$asap_hide_h1 						= get_post_meta( $post->ID, 'hide_h1', true );
	$asap_single_bc_text 				= get_post_meta( $post->ID, 'single_bc_text', true );
	$asap_hide_toc 						= get_post_meta( $post->ID, 'hide_toc', true );
	$asap_hide_social_btn 				= get_post_meta( $post->ID, 'hide_social_btn', true );	
	$asap_single_bc_text_pillar_page 	= get_post_meta( $post->ID, 'single_bc_text_pillar_page', true );
	$asap_single_bc_url_pillar_page 	= get_post_meta( $post->ID, 'single_bc_url_pillar_page', true );
	$asap_disable_header 				= get_post_meta( $post->ID, 'disable_header', true );
	$asap_disable_footer				= get_post_meta( $post->ID, 'disable_footer', true );
	$asap_hide_breadcrumbs 				= get_post_meta( $post->ID, 'hide_breadcrumbs', true );
	$asap_single_bc_featured 			= get_post_meta( $post->ID, 'single_bc_featured', true );
	$asap_head_custom_code 				= get_post_meta( $post->ID, 'head_custom_code', true );
	$asap_foot_custom_code 				= get_post_meta( $post->ID, 'foot_custom_code', true );
	$asap_disable_dynamic 				= get_post_meta( $post->ID, 'asap_disable_dynamic', true );
	$asap_disable_box_design 			= get_post_meta( $post->ID, 'asap_disable_box_design', true );
	$asap_header_design 				= get_post_meta( $post->ID, 'asap_header_design', true) ?: '';
	$asap_exclude_news	 				= get_post_meta( $post->ID, 'asap_exclude_news', true);
	$asap_add_news_css	 				= get_post_meta( $post->ID, 'asap_add_news_css', true);

	
?>
	
<style>
	
	.asap-metabox-title {
		font-size:20px;
	}
	.postmetabox { margin:1rem 0; overflow:hidden; clear:both; height:auto; }
	.metabox_option { margin: 0 0 2rem 0; }
	.metabox_option .label {  display:inline !important;}
	.metabox_option input {
		margin-top:.1rem;
	}
	
	.metabox_option label {
		cursor: pointer;
	}

	.postmetabox_left {
		width:30%;
		float:left;	
	}
	
	.postmetabox_right {
		width:70%;
		float:right;
	}
		
	.metabox_option input[type=text],
	.postmetabox textarea {
	  width: 80% !important;
	  padding: 10px;
	}
	
	.metabox_option.metabox_mbottom {
		padding-bottom:2rem !important;
	}
	
	.metabox_option.metabox_mbottom4 {
		padding-bottom:5.5rem !important;
	}
	
	.metabox_option.metabox_mbottom1 {
		padding-bottom:1.5rem !important;
	}	
	
	.metabox_option select {
	  width: 50% !important;
	  padding: 5px;
	}

	.metabox_option label span {
		background: #1abc9c;
		padding: 2px 6px 4px 6px;
		border-radius: 3px; 
		color: #fff;
		font-size: 11px;
		margin-left: 4px;
	}
	.metabox_search {
    margin-bottom: 10px;
}

#metabox_search_input {
    width: calc(100% - 16px);
    padding: 8px;
    margin-bottom: 16px;
    box-sizing: border-box;
    margin-top: 6px;
}

</style>
<script type="text/javascript">
jQuery(document).ready(function($) {
    var originalLeftClasses = {};  // Para guardar las clases originales de los elementos a la izquierda
    var originalRightClasses = {}; // Para guardar las clases originales de los elementos a la derecha
    var classesSaved = false;      // Bandera para asegurar que las clases solo se guarden una vez

    // Guardar las clases originales solo una vez cuando se carga la página
    function saveOriginalClasses() {
        if (!classesSaved) {
            $('.postmetabox_left .metabox_option').each(function(index) {
                originalLeftClasses[index] = $(this).attr('class'); // Guardar las clases originales de la izquierda
                console.log("Clase original guardada para el elemento de la izquierda con índice " + index + ": " + originalLeftClasses[index]);
            });
            $('.postmetabox_right .metabox_option').each(function(index) {
                originalRightClasses[index] = $(this).attr('class'); // Guardar las clases originales de la derecha
                console.log("Clase original guardada para el elemento de la derecha con índice " + index + ": " + originalRightClasses[index]);
            });
            classesSaved = true; // Marcar como guardadas para no volver a guardar
        }
    }

    saveOriginalClasses(); // Llamar la función para guardar clases al inicio

    $('#metabox_search_input').on('keyup', function() {
        var value = $(this).val().toLowerCase();

        // Filtrar y mostrar/ocultar elementos
        $('.postmetabox_left .metabox_option').each(function(index) {
            var match = $(this).text().toLowerCase().indexOf(value) > -1;

            if (match) {
                $(this).show();
                $('.postmetabox_right .metabox_option').eq(index).show();
            } else {
                $(this).hide();
                $('.postmetabox_right .metabox_option').eq(index).hide();
            }
        });

        // Restaurar todas las opciones y sus clases originales si el campo de búsqueda está vacío
        if (value === '') {
            console.log("Restaurando clases originales...");
            $('.postmetabox_left .metabox_option').each(function(index) {
                if (originalLeftClasses[index]) {
                    $(this).attr('class', originalLeftClasses[index]).show();
                    console.log("Clase restaurada para el elemento de la izquierda con índice " + index + ": " + originalLeftClasses[index]);
                }
            });
            $('.postmetabox_right .metabox_option').each(function(index) {
                if (originalRightClasses[index]) {
                    $(this).attr('class', originalRightClasses[index]).show();
                    console.log("Clase restaurada para el elemento de la derecha con índice " + index + ": " + originalRightClasses[index]);
                }
            });
        }
    });
});



</script>
<div class="postmetabox">

<div class="metabox_search">
    <input type="text" id="metabox_search_input" placeholder="<?php _e('Buscar opciones...', 'asap'); ?>">
</div>

<div class="postmetabox_left">
		
		<?php if ( $box_design ) : ?>
	
		<div class="metabox_option metabox_option_design">
			<label class="label" for="disable_box_design"><?php  _e( 'Disable box design', 'asap' ); ?></label>
		</div>		
		
		<?php endif; ?>
		
		<div class="metabox_option metabox_option_header_design_global metabox_mbottom1">
			<label class="label"><?php  _e( 'Header design - Global', 'asap' ); ?></label>
		</div>
				
		<div class="metabox_option metabox_option_header_design metabox_mbottom1">
			<label class="label" for="asap_header_design"><?php  _e( 'Header design', 'asap' ); ?></label>
		</div>		
		
		<div class="metabox_option metabox_option_news_css">
			<label class="label" for="asap_add_news_css"><?php  _e( 'Cargar CSS diseño diario', 'asap' ); ?><span>Nuevo</span></label>
		</div>

		<div class="metabox_option metabox_option_exclude_news">
			<label class="label" for="asap_exclude_news"><?php  _e( 'No mostrar en listados diseño diario', 'asap' ); ?></label>
		</div>

		<div class="metabox_option metabox_option_featured_post">
			<label class="label" for="featured_post"><?php  _e( 'Featured post', 'asap' ); ?></label>
		</div>
				
		<div class="metabox_option metabox_option_bc_featured metabox_mbottom" style="margin-bottom:1.5rem !important;">
			<label class="label" for="single_bc_featured"><?php  _e( 'Featured Text', 'asap' ); ?></label>
		</div>	
		
		<div class="metabox_option metabox_option_hide_image_post">
			<label class="label" for="hide_image_post"><?php  _e( 'Hide featured image', 'asap' ); ?></label>
		</div>
			
		<div class="metabox_option metabox_option_hide_breadcrumbs">
			<label class="label" for="hide_breadcrumbs"><?php  _e( 'Disable breadcrumbs', 'asap' ); ?></label>
		</div>
		
		<div class="metabox_option metabox_option_hide_sidebar">
			<label class="label" for="hide_sidebar"><?php  _e( 'Disable sidebar', 'asap' ); ?></label>
		</div>
	
		<div class="metabox_option metabox_option_hide_related_post">
			<label class="label" for="hide_related_post"><?php  _e( 'Disable related posts', 'asap' ); ?></label>
		</div>

		<div class="metabox_option metabox_option_hide_ads">
			<label class="label" for="hide_ads"><?php  _e( 'Disable Ads', 'asap' ); ?></label>
		</div>	
		
		<div class="metabox_option metabox_option_hide_h1">
			<label class="label" for="hide_h1"><?php  _e( 'Disable H1', 'asap' ); ?></label>
		</div>		
		
		<div class="metabox_option metabox_option_hide_toc">
			<label class="label" for="hide_toc"><?php  _e( 'Disable Table of Contents', 'asap' ); ?></label>
		</div>				

		<div class="metabox_option metabox_option_hide_social_btn">
			<label class="label" for="hide_social_btn"><?php  _e( 'Disable Social Buttons', 'asap' ); ?></label>
		</div>		
		
		<div class="metabox_option metabox_option_disable_header">
			<label class="label" for="disable_header"><?php  _e( 'Disable header', 'asap' ); ?></label>
		</div>
		
		<div class="metabox_option metabox_option_disable_footer">
			<label class="label" for="disable_footer"><?php  _e( 'Disable footer', 'asap' ); ?></label>
		</div>
		
		<div class="metabox_option metabox_option_disable_dynamic">
			<label class="label" for="asap_disable_dynamic"><?php  _e( 'Disable dynamic paragraph', 'asap' ); ?></label>
		</div>	

		<div class="metabox_option metabox_option_subtitle_post metabox_mbottom4" style="padding-bottom:9.5rem !important;">
			<label class="label" for="subtitle_post"><?php  _e( 'Subtitle', 'asap' ); ?></label>
		</div>		

		<div class="metabox_option metabox_option_bc_text metabox_mbottom">
			<label class="label" for="single_bc_text"><?php  _e( 'Breadcrumb Text', 'asap' ); ?></label>
		</div>	
		
		<div class="metabox_option metabox_option_bc_text_pillar_page metabox_mbottom">
			<label class="label" for="single_bc_text_pillar_page"><?php  _e( 'Text Breadcrumb Pillar Page', 'asap' ); ?></label>
		</div>	
		
		<div class="metabox_option metabox_option_bc_url_pillar_page metabox_mbottom">
			<label class="label" for="single_bc_url_pillar_page"><?php  _e( 'URL Breadcrumb Pillar Page', 'asap' ); ?></label>
		</div>	
		

		<div class="metabox_option metabox_option_head_custom_code metabox_mbottom4">
			<label class="label" for="head_custom_code"><?php  _e( 'Custom code in header', 'asap' ); ?></label>
		</div>	
		
		<div class="metabox_option metabox_option_foot_custom_code metabox_mbottom">
			<label class="label" for="foot_custom_code"><?php  _e( 'Custom code in footer', 'asap' ); ?></label>
		</div>	
		
	</div>
	<div class="postmetabox_right">
		
		<?php if ( $box_design ) : ?>
	
		<div class="metabox_option metabox_option_design">
			<input type="checkbox" name="asap_disable_box_design" id="asap_disable_box_design" value="1" <?php checked( $asap_disable_box_design, 1 ); ?>>
		</div>
		
		<?php endif; ?>		
		
		<div class="metabox_option metabox_option_header_design_global">
			<select disabled title="<?php  _e( 'This global option can only be changed from the customizer options.', 'asap' ); ?>">  
			<option value="normal" <?php selected( $hero_post, "normal" ); ?>><?php  _e( 'Normal', 'asap' ); ?></option>  
			<option value="1" <?php selected( $hero_post, "1" ); ?>><?php  _e( 'Featured', 'asap' ); ?></option>
			<option value="2" <?php selected( $hero_post, "2" ); ?>><?php  _e( 'Featured without search engine', 'asap' ); ?></option>
			 </select>			

	   	</div>				
		
		<div class="metabox_option metabox_option_header_design">
			<select name="asap_header_design" id="asap_header_design">  
      			<option value="" <?php if (empty($asap_header_design)) echo 'selected'; ?> ></option>
				<option value="normal" <?php selected($asap_header_design, "normal", true); ?>><?php _e('Normal', 'asap'); ?></option>  
				<option value="1" <?php selected($asap_header_design, "1", true); ?>><?php _e('Featured', 'asap'); ?></option>
				<option value="2" <?php selected($asap_header_design, "2", true); ?>><?php _e('Featured without search engine', 'asap'); ?></option>
			</select>	
		</div>

		<div class="metabox_option metabox_option_news_css">
			<input type="checkbox" name="asap_add_news_css" id="asap_add_news_css" value="1" <?php checked( $asap_add_news_css, 1 ); ?>>
		</div>		

		<div class="metabox_option metabox_option_exclude_news">
			<input type="checkbox" name="asap_exclude_news" id="asap_exclude_news" value="1" <?php checked( $asap_exclude_news, 1 ); ?>>
		</div>		
				
		<div class="metabox_option metabox_option_featured_post">
			<input type="checkbox" name="featured_post" id="featured_post" value="1" <?php checked( $asap_featured_post, 1 ); ?>>
		</div>		
		
		<div class="metabox_option metabox_option_bc_featured">
			<input type="text" name="single_bc_featured" id="single_bc_featured" value="<?php echo  esc_html( $asap_single_bc_featured ) ; ?>" placeholder="<?php  _e( 'Featured', 'asap' ); ?>"  >
	   </div>	
			
		
		<div class="metabox_option metabox_option_hide_image_post">
			<input type="checkbox" name="hide_image_post" id="hide_image_post" value="1" <?php checked( $asap_hide_image_post, 1 ); ?>>
	   </div>
		
		<div class="metabox_option metabox_option_hide_breadcrumbs">
			<input type="checkbox" name="hide_breadcrumbs" id="hide_breadcrumbs" value="1" <?php checked( $asap_hide_breadcrumbs, 1 ); ?>>
	   </div>
		
		<div class="metabox_option metabox_option_hide_sidebar">
			<input type="checkbox" name="hide_sidebar" id="hide_sidebar" value="1" <?php checked( $asap_hide_sidebar, 1 ); ?>>
	   </div>
		
		<div class="metabox_option metabox_option_hide_related_post">
			<input type="checkbox" name="hide_related_post" id="hide_related_post" value="1" <?php checked( $asap_hide_related_post, 1 ); ?>>
	   </div>
			
		<div class="metabox_option metabox_option_hide_ads">
			<input type="checkbox" name="hide_ads" id="hide_ads" value="1" <?php checked( $asap_hide_ads, 1 ); ?>>
	   </div>	
		
		<div class="metabox_option metabox_option_hide_h1">
			<input type="checkbox" name="hide_h1" id="hide_h1" value="1" <?php checked( $asap_hide_h1, 1 ); ?>>
	   </div>	
			
		<div class="metabox_option metabox_option_hide_toc">
			<input type="checkbox" name="hide_toc" id="hide_toc" value="1" <?php checked( $asap_hide_toc, 1 ); ?>>
	   	</div>	

		<div class="metabox_option metabox_option_hide_social_btn">
			<input type="checkbox" name="hide_social_btn" id="hide_social_btn" value="1" <?php checked( $asap_hide_social_btn, 1 ); ?>>
	   	</div>	
		
		<div class="metabox_option metabox_option_disable_header">
			<input type="checkbox" name="disable_header" id="disable_header" value="1" <?php checked( $asap_disable_header, 1 ); ?>>
	   	</div>	
		
		<div class="metabox_option metabox_option_disable_footer">
			<input type="checkbox" name="disable_footer" id="disable_footer" value="1" <?php checked( $asap_disable_footer, 1 ); ?>>
	   	</div>	
	
		<div class="metabox_option metabox_option_disable_dynamic">
			<input type="checkbox" name="asap_disable_dynamic" id="asap_disable_dynamic" value="1" <?php checked( $asap_disable_dynamic, 1 ); ?>>
	   	</div>	
		
		<div class="metabox_option metabox_option_subtitle_post">
		    <?php
			$current_screen = get_current_screen();

			if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			    // Estamos en Gutenberg
			    echo '<textarea name="subtitle_post" rows="4" style="margin-bottom:4rem !important;">' . esc_textarea( $asap_subtitle_post ) . '</textarea>';
			} else {
			    // Estamos en el editor clásico
			    $content    = $asap_subtitle_post;
			    $editor_id  = 'subtitle_post';
			    $settings   = array(
			        'textarea_name' => 'subtitle_post',
			        'media_buttons' => false,
			        'textarea_rows' => 4,
			        'teeny'         => true,
			        'quicktags'     => false
			    );
			    wp_editor( $content, $editor_id, $settings );
			}
			?>
		</div>
		
		<div class="metabox_option metabox_option_bc_text">
			<input type="text" name="single_bc_text" id="single_bc_text" value="<?php echo  esc_html( $asap_single_bc_text ) ; ?>" >
	   </div>	
		
		<div class="metabox_option metabox_option_bc_text_pillar_page">
			<input type="text" name="single_bc_text_pillar_page" id="single_bc_text_pillar_page" value="<?php echo  esc_html( $asap_single_bc_text_pillar_page ) ; ?>" >
	   </div>	
		
 		<div class="metabox_option metabox_option_bc_url_pillar_page">
			<input type="text" name="single_bc_url_pillar_page" id="single_bc_url_pillar_page" value="<?php echo  esc_html( $asap_single_bc_url_pillar_page ) ; ?>" >
	   </div>		

		
		<div class="metabox_option metabox_option_head_custom_code">
			<textarea name="head_custom_code" id="head_custom_code" rows="4"><?php echo esc_textarea($asap_head_custom_code); ?></textarea>
		</div>

		<div class="metabox_option metabox_option_foot_custom_code">
			<textarea name="foot_custom_code" id="foot_custom_code"  rows="4"><?php echo esc_textarea( $asap_foot_custom_code ); ?></textarea>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};

			// Configuración para el editor de CSS
			var cssEditorSettings = _.extend({}, editorSettings.codemirror, {
				lineNumbers: true,
				mode: 'css', // Modo CSS
				indentUnit: 2,
				viewportMargin: Infinity,
			});

			// Configuración para el editor de JavaScript
			var jsEditorSettings = _.extend({}, editorSettings.codemirror, {
				lineNumbers: true,
				mode: 'javascript', // Modo JavaScript
				indentUnit: 2,
				viewportMargin: Infinity,
			});

			// Inicializar editor para el código CSS
			var headEditor = wp.codeEditor.initialize($('#head_custom_code'), { codemirror: cssEditorSettings });

			// Inicializar editor para el código JavaScript
			var footEditor = wp.codeEditor.initialize($('#foot_custom_code'), { codemirror: jsEditorSettings });

			// Refrescar los editores después de la inicialización
			setTimeout(function() {
				headEditor.codemirror.refresh();
				footEditor.codemirror.refresh();
			}, 500);

			// Función para sincronizar contenido de CodeMirror con el textarea
			function saveEditorsContent() {
				headEditor.codemirror.save();
				footEditor.codemirror.save();
			}

			// Para el editor clásico
			if (typeof wp === 'undefined' || typeof wp.data === 'undefined') {
				$('#post').on('submit', function () {
					saveEditorsContent();
				});
			}

			// Para Gutenberg, solo si wp.data está definido y core/editor es accesible
			if (typeof wp !== 'undefined' && typeof wp.data !== 'undefined' && typeof wp.data.select('core/editor') !== 'undefined') {
				wp.data.subscribe(function () {
					const isSavingPost = wp.data.select('core/editor').isSavingPost();
					const isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

					if (isSavingPost && !isAutosavingPost) {
						saveEditorsContent();
					}
				});
			}
		});
		</script>

	</div>
</div>

<?php } 




function asap_anchor_text_callback( $post ) {
	$asap_anchor_home 			= get_post_meta( $post->ID, 'asap_anchor_home', true );
	$asap_anchor_side 			= get_post_meta( $post->ID, 'asap_anchor_side', true );
	$asap_anchor_related 		= get_post_meta( $post->ID, 'asap_anchor_related', true );
	$asap_anchor_cluster 		= get_post_meta( $post->ID, 'asap_anchor_cluster', true );

?>
	

<div class="postmetabox">
	<div class="postmetabox_left">
			
		<div class="metabox_option metabox_mbottom">
			<label class="label" for="asap_anchor_home"><?php  _e( 'Home Loop Anchor Text', 'asap' ); ?></label>
		</div>	
	
		<div class="metabox_option metabox_mbottom">
			<label class="label" for="asap_anchor_side"><?php  _e( 'Sidebar Anchor Text', 'asap' ); ?></label>
		</div>			
	
		<div class="metabox_option metabox_mbottom">
			<label class="label" for="asap_anchor_related"><?php  _e( 'Related Post Anchor Text', 'asap' ); ?></label>
		</div>					

		<div class="metabox_option metabox_mbottom">
			<label class="label" for="asap_anchor_cluster"><?php  _e( 'Clúster Anchor Text', 'asap' ); ?></label>
		</div>				
		
	</div>
	
	<div class="postmetabox_right">
		
		<div class="metabox_option">
			
			<input type="text" name="asap_anchor_home" id="asap_anchor_home" value="<?php echo  esc_html( $asap_anchor_home ) ; ?>" placeholder="<?php  echo the_title(); ?>"  >
			
	   </div>	
			
			
		<div class="metabox_option">
						
			<input type="text" name="asap_anchor_side" id="asap_anchor_side" value="<?php echo  esc_html( $asap_anchor_side ) ; ?>" placeholder="<?php  echo the_title(); ?>"  >

	   </div>	
			
		<div class="metabox_option">
			
			<input type="text" name="asap_anchor_related" id="asap_anchor_related" value="<?php echo  esc_html( $asap_anchor_related ) ; ?>" placeholder="<?php  echo the_title(); ?>"  >
			
	   </div>			
		
		<div class="metabox_option">
			
			<input type="text" name="asap_anchor_cluster" id="asap_anchor_cluster" value="<?php echo  esc_html( $asap_anchor_cluster ) ; ?>" placeholder="<?php  echo the_title(); ?>"  >
			
	   </div>	
		
	</div>
	
</div>

<?php } 




	
add_action( 'save_post', 'asap_save_custom_fields', 10, 2 );

function asap_save_custom_fields( $post_id, $post ){

	if ( ! isset( $_POST['asap_custom_box_posts_nonce'] ) ) {
		return;
	}

	$nonce = $_POST['asap_custom_box_posts_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'asap_custom_box_posts' ) ) {
		return;
	}
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}	
	
	
	/* */

	if ( isset( $_POST['asap_header_design'] ) ) {
        update_post_meta( $post_id, 'asap_header_design',  sanitize_text_field ( $_POST['asap_header_design'] ) );
    }
	
	/* */
    if( isset( $_POST['asap_add_news_css'] ) && $_POST['asap_add_news_css'] == "1" ) {
        update_post_meta( $post_id, 'asap_add_news_css', asap_sanitize_check ( $_POST['asap_add_news_css'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_add_news_css' );
    } 

    if( isset( $_POST['asap_exclude_news'] ) && $_POST['asap_exclude_news'] == "1" ) {
        update_post_meta( $post_id, 'asap_exclude_news', asap_sanitize_check ( $_POST['asap_exclude_news'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_exclude_news' );
    }

    if( isset( $_POST['featured_post'] ) && $_POST['featured_post'] == "1" ) {
        update_post_meta( $post_id, 'featured_post', asap_sanitize_check ( $_POST['featured_post'] ) );
    } else {
		delete_post_meta( $post_id, 'featured_post' );
    }
	
	if( isset( $_POST['hide_image_post'] ) && $_POST['hide_image_post'] == "1" ) {
        update_post_meta( $post_id, 'hide_image_post', asap_sanitize_check ( $_POST['hide_image_post'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_image_post' );
    }
	
	if( isset( $_POST['hide_related_post'] ) && $_POST['hide_related_post'] == "1" ) {
        update_post_meta( $post_id, 'hide_related_post', asap_sanitize_check ( $_POST['hide_related_post'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_related_post' );
    }
	
	if( isset( $_POST['hide_sidebar'] ) && $_POST['hide_sidebar'] == "1" ) {
        update_post_meta( $post_id, 'hide_sidebar', asap_sanitize_check ( $_POST['hide_sidebar'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_sidebar' );
    }
	
	if( isset( $_POST['hide_breadcrumbs'] ) && $_POST['hide_breadcrumbs'] == "1" ) {
        update_post_meta( $post_id, 'hide_breadcrumbs', asap_sanitize_check ( $_POST['hide_breadcrumbs'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_breadcrumbs' );
    }

	if( isset( $_POST['hide_ads'] ) && $_POST['hide_ads'] == "1" ) {
        update_post_meta( $post_id, 'hide_ads', asap_sanitize_check ( $_POST['hide_ads'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_ads' );
    }
	
	if( isset( $_POST['hide_h1'] ) && $_POST['hide_h1'] == "1" ) {
        update_post_meta( $post_id, 'hide_h1', asap_sanitize_check ( $_POST['hide_h1'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_h1' );
    }	
	
	if( isset( $_POST['hide_toc'] ) && $_POST['hide_toc'] == "1" ) {
        update_post_meta( $post_id, 'hide_toc', asap_sanitize_check ( $_POST['hide_toc'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_toc' );
    }	
	
	if( isset( $_POST['hide_social_btn'] ) && $_POST['hide_social_btn'] == "1" ) {
        update_post_meta( $post_id, 'hide_social_btn', asap_sanitize_check ( $_POST['hide_social_btn'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_social_btn' );
    }	

	if( isset( $_POST['disable_header'] ) && $_POST['disable_header'] == "1" ) {
        update_post_meta( $post_id, 'disable_header', asap_sanitize_check ( $_POST['disable_header'] ) );
    } else {
		delete_post_meta( $post_id, 'disable_header' );
    }	
	
	if( isset( $_POST['disable_footer'] ) && $_POST['disable_footer'] == "1" ) {
        update_post_meta( $post_id, 'disable_footer', asap_sanitize_check ( $_POST['disable_footer'] ) );
    } else {
		delete_post_meta( $post_id, 'disable_footer' );
    }	
	
	if( isset( $_POST['asap_disable_dynamic'] ) && $_POST['asap_disable_dynamic'] == "1" ) {
        update_post_meta( $post_id, 'asap_disable_dynamic', asap_sanitize_check ( $_POST['asap_disable_dynamic'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_disable_dynamic' );
    }	

	if( isset( $_POST['asap_disable_box_design'] ) && $_POST['asap_disable_box_design'] == "1" ) {
        update_post_meta( $post_id, 'asap_disable_box_design', asap_sanitize_check ( $_POST['asap_disable_box_design'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_disable_box_design' );
    }	
	
	
	/* */
	
	if( isset( $_POST['subtitle_post'] ) && !empty( $_POST['subtitle_post'] ) ) {
	    update_post_meta( $post_id, 'subtitle_post', $_POST['subtitle_post'] );
	} else {
	    delete_post_meta( $post_id, 'subtitle_post' );
	}

	
	/* */	
	
	if( isset( $_POST['single_bc_featured'] ) && (!empty($_POST['single_bc_featured'])) ) {
        update_post_meta( $post_id, 'single_bc_featured', sanitize_text_field ( $_POST['single_bc_featured'] ) );
    } else {
		delete_post_meta( $post_id, 'single_bc_featured' );
    }

	if( isset( $_POST['single_bc_text'] ) && (!empty($_POST['single_bc_text'])) ) {
        update_post_meta( $post_id, 'single_bc_text', sanitize_text_field ( $_POST['single_bc_text'] ) );
    } else {
		delete_post_meta( $post_id, 'single_bc_text' );
    }
	
	if( isset( $_POST['single_bc_text_pillar_page'] ) && (!empty($_POST['single_bc_text_pillar_page'])) ) {
        update_post_meta( $post_id, 'single_bc_text_pillar_page', sanitize_text_field ( $_POST['single_bc_text_pillar_page'] ) );
    } else {
		delete_post_meta( $post_id, 'single_bc_text_pillar_page' );
    }



	
	/* */
	
	if( isset( $_POST['single_bc_url_pillar_page'] ) && (!empty($_POST['single_bc_url_pillar_page'])) ) {
        update_post_meta( $post_id, 'single_bc_url_pillar_page', sanitize_url ( $_POST['single_bc_url_pillar_page'] ) );
    }  else {
		delete_post_meta( $post_id, 'single_bc_url_pillar_page' );
    }
						 
						 
	/* */
	if( isset( $_POST['head_custom_code'] ) && (!empty($_POST['head_custom_code'])) ) {
        update_post_meta( $post_id, 'head_custom_code',  $_POST['head_custom_code']  );
    }  else {
		delete_post_meta( $post_id, 'head_custom_code' );
    }
	
	if( isset( $_POST['foot_custom_code'] ) && (!empty($_POST['foot_custom_code'])) ) {
        update_post_meta( $post_id, 'foot_custom_code',  $_POST['foot_custom_code'] );
    }  else {
		delete_post_meta( $post_id, 'foot_custom_code' );
    }
						 
						 
	/* */
						 
	if( isset( $_POST['asap_anchor_home'] ) && (!empty($_POST['asap_anchor_home'])) ) {
        update_post_meta( $post_id, 'asap_anchor_home', sanitize_text_field ( $_POST['asap_anchor_home'] ) );
    }  else {
		delete_post_meta( $post_id, 'asap_anchor_home' );
    }
	
	if( isset( $_POST['asap_anchor_side'] ) && (!empty($_POST['asap_anchor_side'])) ) {
        update_post_meta( $post_id, 'asap_anchor_side', sanitize_text_field ( $_POST['asap_anchor_side'] ) );
    }  else {
		delete_post_meta( $post_id, 'asap_anchor_side' );
    }	
	
	if( isset( $_POST['asap_anchor_related'] ) && (!empty($_POST['asap_anchor_related'])) ) {
        update_post_meta( $post_id, 'asap_anchor_related', sanitize_text_field ( $_POST['asap_anchor_related'] ) );
    }  else {
		delete_post_meta( $post_id, 'asap_anchor_related' );
    }	
	
	if( isset( $_POST['asap_anchor_cluster'] ) && (!empty($_POST['asap_anchor_cluster'])) ) {
        update_post_meta( $post_id, 'asap_anchor_cluster', sanitize_text_field ( $_POST['asap_anchor_cluster'] ) );
    }  else {
		delete_post_meta( $post_id, 'asap_anchor_cluster' );
    }		
	
	
}







/* 
 * PAGE METABOX
 */


add_action( 'add_meta_boxes_page', 'meta_boxes_page' );

function meta_boxes_page() {
	
    add_meta_box( 'page-meta-box', 
				 __( 'ASAP − Page Options', 'asap' ), 
				 'meta_box_callback_page', 
				 'page',
				 'normal', 
				 'high',
				 array( 'arg' => 'value') 
				);
	
	add_meta_box( 'asap-anchor-text', 
				__( 'ASAP − Anchor text Options', 'asap' ),
				'asap_anchor_text_page_callback', 
				'page', 
				'normal', 
				'high', 
			 	array( 'arg' => 'value') 
				);
}

function meta_box_callback_page( $post ) {
	
	wp_nonce_field( 'asap_custom_box_pages', 'asap_custom_box_pages_nonce' );

    $post_meta 							= get_post_custom( $post->ID );   	
   
	$box_design 						= get_theme_mod('asap_design');
	$hero_page 							= get_theme_mod('asap_hero_page', 'normal');

	$asap_subtitle_post 				= get_post_meta( $post->ID, 'subtitle_post', true );
	$asap_hide_image_page 				= get_post_meta( $post->ID, 'hide_image_page', true );  
	$asap_hide_ads 						= get_post_meta( $post->ID, 'hide_ads', true );  	
	$asap_hide_h1 						= get_post_meta( $post->ID, 'hide_h1', true );  	
	$asap_hide_sidebar 					= get_post_meta( $post->ID, 'hide_sidebar', true );  	
	$asap_hide_toc 						= get_post_meta( $post->ID, 'hide_toc', true );  	
	$asap_hide_social_btn 				= get_post_meta( $post->ID, 'hide_social_btn', true );  	
	$asap_disable_header 				= get_post_meta( $post->ID, 'disable_header', true );
	$asap_disable_footer 				= get_post_meta( $post->ID, 'disable_footer', true );
	$asap_single_bc_text 				= get_post_meta( $post->ID, 'single_bc_text', true );
	$asap_single_bc_text_pillar_page 	= get_post_meta( $post->ID, 'single_bc_text_pillar_page', true );
	$asap_single_bc_url_pillar_page 	= get_post_meta( $post->ID, 'single_bc_url_pillar_page', true );
	$asap_hide_breadcrumbs 				= get_post_meta( $post->ID, 'hide_breadcrumbs', true );  	
	$asap_head_custom_code 				= get_post_meta( $post->ID, 'head_custom_code', true );
	$asap_foot_custom_code 				= get_post_meta( $post->ID, 'foot_custom_code', true );
	$asap_disable_dynamic 				= get_post_meta( $post->ID, 'asap_disable_dynamic', true );
	$asap_disable_box_design 			= get_post_meta( $post->ID, 'asap_disable_box_design', true );
	$asap_disable_author 				= get_post_meta( $post->ID, 'asap_disable_author', true );
	$asap_disable_author_box			= get_post_meta( $post->ID, 'asap_disable_author_box', true );
	$asap_disable_date	 				= get_post_meta( $post->ID, 'asap_disable_date', true );
	$asap_header_design 				= get_post_meta($post->ID, 'asap_header_design', true ) ?: '';
	$asap_exclude_news	 				= get_post_meta( $post->ID, 'asap_exclude_news', true);
	$asap_add_news_css	 				= get_post_meta( $post->ID, 'asap_add_news_css', true);

?>
	
<style>
	
	.postmetabox { margin:1rem 0; overflow:hidden; clear:both; height:auto; }
	.metabox_option { margin: 0 0 2rem 0; }
	.metabox_option .label {  display:inline !important;}
	.metabox_option input {
		margin-top:.1rem;
	}
	
	.postmetabox_left {
		width:30%;
		float:left;	
	}
	
	.postmetabox_right {
		width:70%;
		float:right;
	}
		
	.metabox_option input[type=text],
	.postmetabox textarea {
	  width: 80% !important;
	  padding: 10px;
	}
	
	.metabox_option select {
	  width: 50% !important;
	  padding: 5px;
	}
	
	.metabox_option.metabox_mbottom {
		padding-bottom:2rem !important;
	}
	
	.metabox_option.metabox_mbottom1 {
		padding-bottom:1.25rem !important;
	}
			
	.metabox_option.metabox_mbottom4 {
		padding-bottom:5.5rem !important;
	}
	
	.metabox_option label span {
		background: #1abc9c;
		padding: 2px 6px 4px 6px;
		border-radius: 3px; 
		color: #fff;
		font-size: 11px;
		margin-left: 4px;
	}
	
</style>

<style>
	.asap-metabox-title {
		font-size: 20px;
	}

	.postmetabox {
		margin: 1rem 0;
		overflow: hidden;
		clear: both;
		height: auto;
	}

	.metabox_option {
		margin: 0 0 2rem 0;
	}

	.metabox_option .label {
		display: inline !important;
	}

	.metabox_option input {
		margin-top: .1rem;
	}

	.metabox_option label {
		cursor: pointer;
	}

	.postmetabox_left {
		width: 30%;
		float: left;
	}

	.postmetabox_right {
		width: 70%;
		float: right;
	}

	.metabox_option input[type=text],
	.postmetabox textarea {
		width: 80% !important;
		padding: 10px;
	}

	.metabox_option.metabox_mbottom {
		padding-bottom: 2rem !important;
	}

	.metabox_option.metabox_mbottom4 {
		padding-bottom: 5.5rem !important;
	}

	.metabox_option.metabox_mbottom1 {
		padding-bottom: 1.5rem !important;
	}

	.metabox_option select {
		width: 50% !important;
		padding: 5px;
	}

	.metabox_option label span {
		background: #1abc9c;
		padding: 2px 6px 4px 6px;
		border-radius: 3px;
		color: #fff;
		font-size: 11px;
		margin-left: 4px;
	}

	.metabox_search {
		margin-bottom: 10px;
	}

	#metabox_search_input {
		width: calc(100% - 16px);
		padding: 8px;
		margin-bottom: 16px;
		box-sizing: border-box;
		margin-top: 6px;
	}
</style>

<script type="text/javascript">
	jQuery(document).ready(function($) {
    var originalLeftClasses = {};  // Para guardar las clases originales de los elementos a la izquierda
    var originalRightClasses = {}; // Para guardar las clases originales de los elementos a la derecha
    var classesSaved = false;      // Bandera para asegurar que las clases solo se guarden una vez

    // Guardar las clases originales solo una vez cuando se carga la página
    function saveOriginalClasses() {
        if (!classesSaved) {
            $('.postmetabox_left .metabox_option').each(function(index) {
                originalLeftClasses[index] = $(this).attr('class'); // Guardar las clases originales de la izquierda
                console.log("Clase original guardada para el elemento de la izquierda con índice " + index + ": " + originalLeftClasses[index]);
            });
            $('.postmetabox_right .metabox_option').each(function(index) {
                originalRightClasses[index] = $(this).attr('class'); // Guardar las clases originales de la derecha
                console.log("Clase original guardada para el elemento de la derecha con índice " + index + ": " + originalRightClasses[index]);
            });
            classesSaved = true; // Marcar como guardadas para no volver a guardar
        }
    }

    saveOriginalClasses(); // Llamar la función para guardar clases al inicio

    $('#metabox_search_input').on('keyup', function() {
        var value = $(this).val().toLowerCase();

        // Filtrar y mostrar/ocultar elementos
        $('.postmetabox_left .metabox_option').each(function(index) {
            var match = $(this).text().toLowerCase().indexOf(value) > -1;

            if (match) {
                $(this).show();
                $('.postmetabox_right .metabox_option').eq(index).show();
            } else {
                $(this).hide();
                $('.postmetabox_right .metabox_option').eq(index).hide();
            }
        });

        // Restaurar todas las opciones y sus clases originales si el campo de búsqueda está vacío
        if (value === '') {
            console.log("Restaurando clases originales...");
            $('.postmetabox_left .metabox_option').each(function(index) {
                if (originalLeftClasses[index]) {
                    $(this).attr('class', originalLeftClasses[index]).show();
                    console.log("Clase restaurada para el elemento de la izquierda con índice " + index + ": " + originalLeftClasses[index]);
                }
            });
            $('.postmetabox_right .metabox_option').each(function(index) {
                if (originalRightClasses[index]) {
                    $(this).attr('class', originalRightClasses[index]).show();
                    console.log("Clase restaurada para el elemento de la derecha con índice " + index + ": " + originalRightClasses[index]);
                }
            });
        }
    });
});

</script>

<div class="postmetabox">
	<div class="metabox_search">
		<input type="text" id="metabox_search_input" placeholder="<?php _e('Buscar opciones...', 'asap'); ?>">
	</div>

	<div class="postmetabox_left">
		<?php if ( $box_design ) : ?>
		<div class="metabox_option metabox_option_design">
			<label class="label" for="disable_box_design"><?php _e('Disable box design', 'asap'); ?></label>
		</div>
		<?php endif; ?>
		<div class="metabox_option metabox_option_header_design_global metabox_mbottom1">
			<label class="label"><?php _e('Header design - Global', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_header_design metabox_mbottom1">
			<label class="label" for="asap_header_design"><?php _e('Header design', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_news_css">
			<label class="label" for="asap_add_news_css"><?php _e('Cargar CSS diseño diario', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_exclude_news">
			<label class="label" for="asap_exclude_news"><?php _e('No mostrar en listados diseño diario', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_image_page">
			<label class="label" for="hide_image_page"><?php _e('Hide featured post', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_ads">
			<label class="label" for="hide_ads"><?php _e('Disable Ads', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_breadcrumbs">
			<label class="label" for="hide_breadcrumbs"><?php _e('Disable breadcrumbs', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_sidebar">
			<label class="label" for="hide_sidebar"><?php _e('Disable Sidebar', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_h1">
			<label class="label" for="hide_h1"><?php _e('Disable H1', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_toc">
			<label class="label" for="hide_toc"><?php _e('Disable Table of Contents', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_hide_social_btn">
			<label class="label" for="hide_social_btn"><?php _e('Disable Social Buttons', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_disable_header">
			<label class="label" for="disable_header"><?php _e('Disable header', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_disable_footer">
			<label class="label" for="disable_footer"><?php _e('Disable footer', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_disable_dynamic">
			<label class="label" for="asap_disable_dynamic"><?php _e('Disable dynamic paragraph', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_disable_author">
			<label class="label" for="asap_disable_author"><?php _e('Desactivar autor', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_disable_author_box">
			<label class="label" for="asap_disable_author_box"><?php _e('Disable author box', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_subtitle_post metabox_mbottom4" style="padding-bottom:9.5rem !important;">
			<label class="label" for="subtitle_post"><?php _e('Subtitle', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_bc_text metabox_mbottom">
			<label class="label" for="single_bc_text"><?php _e('Breadcrumb Text', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_bc_text_pillar_page metabox_mbottom">
			<label class="label" for="single_bc_text_pillar_page"><?php _e('Text Breadcrumb Pillar Page', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_bc_url_pillar_page metabox_mbottom">
			<label class="label" for="single_bc_url_pillar_page"><?php _e('URL Breadcrumb Pillar Page', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_head_custom_code metabox_mbottom4">
			<label class="label" for="head_custom_code"><?php _e('Custom code in header', 'asap'); ?></label>
		</div>
		<div class="metabox_option metabox_option_foot_custom_code metabox_mbottom">
			<label class="label" for="foot_custom_code"><?php _e('Custom code in footer', 'asap'); ?></label>
		</div>
	</div>
	<div class="postmetabox_right">
		<?php if ($box_design) : ?>
		<div class="metabox_option metabox_option_design">
			<input type="checkbox" name="asap_disable_box_design" id="asap_disable_box_design" value="1" <?php checked($asap_disable_box_design, 1); ?>>
		</div>
		<?php endif; ?>
		<div class="metabox_option metabox_option_header_design_global">
			<select disabled title="<?php _e('This global option can only be changed from the customizer options.', 'asap'); ?>">
				<option value="normal" <?php selected($hero_page, "normal"); ?>><?php _e('Normal', 'asap'); ?></option>
				<option value="1" <?php selected($hero_page, "1"); ?>><?php _e('Featured', 'asap'); ?></option>
				<option value="2" <?php selected($hero_page, "2"); ?>><?php _e('Featured without search engine', 'asap'); ?></option>
			</select>
		</div>
		<div class="metabox_option metabox_option_header_design">
			<select name="asap_header_design" id="asap_header_design">
				<option value="" <?php if (empty($asap_header_design)) echo 'selected'; ?>></option>
				<option value="normal" <?php selected($asap_header_design, "normal"); ?>><?php _e('Normal', 'asap'); ?></option>
				<option value="1" <?php selected($asap_header_design, "1"); ?>><?php _e('Featured', 'asap'); ?></option>
				<option value="2" <?php selected($asap_header_design, "2"); ?>><?php _e('Featured without search engine', 'asap'); ?></option>
			</select>
		</div>
		<div class="metabox_option metabox_option_news_css">
			<input type="checkbox" name="asap_add_news_css" id="asap_add_news_css" value="1" <?php checked($asap_add_news_css, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_exclude_news">
			<input type="checkbox" name="asap_exclude_news" id="asap_exclude_news" value="1" <?php checked($asap_exclude_news, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_image_page">
			<input type="checkbox" name="hide_image_page" id="hide_image_page" value="1" <?php checked($asap_hide_image_page, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_ads">
			<input type="checkbox" name="hide_ads" id="hide_ads" value="1" <?php checked($asap_hide_ads, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_breadcrumbs">
			<input type="checkbox" name="hide_breadcrumbs" id="hide_breadcrumbs" value="1" <?php checked($asap_hide_breadcrumbs, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_sidebar">
			<input type="checkbox" name="hide_sidebar" id="hide_sidebar" value="1" <?php checked($asap_hide_sidebar, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_h1">
			<input type="checkbox" name="hide_h1" id="hide_h1" value="1" <?php checked($asap_hide_h1, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_toc">
			<input type="checkbox" name="hide_toc" id="hide_toc" value="1" <?php checked($asap_hide_toc, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_hide_social_btn">
			<input type="checkbox" name="hide_social_btn" id="hide_social_btn" value="1" <?php checked($asap_hide_social_btn, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_disable_header">
			<input type="checkbox" name="disable_header" id="disable_header" value="1" <?php checked($asap_disable_header, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_disable_footer">
			<input type="checkbox" name="disable_footer" id="disable_footer" value="1" <?php checked($asap_disable_footer, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_disable_dynamic">
			<input type="checkbox" name="asap_disable_dynamic" id="asap_disable_dynamic" value="1" <?php checked($asap_disable_dynamic, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_disable_author">
			<input type="checkbox" name="asap_disable_author" id="asap_disable_author" value="1" <?php checked($asap_disable_author, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_disable_author_box">
			<input type="checkbox" name="asap_disable_author_box" id="asap_disable_author_box" value="1" <?php checked($asap_disable_author_box, 1); ?>>
		</div>
		<div class="metabox_option metabox_option_subtitle_post">
		    <?php
			$current_screen = get_current_screen();

			if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			    // Estamos en Gutenberg
			    echo '<textarea name="subtitle_post" rows="4">' . esc_textarea( $asap_subtitle_post ) . '</textarea>';
			} else {
			    // Estamos en el editor clásico
			    $content    = $asap_subtitle_post;
			    $editor_id  = 'subtitle_post';
			    $settings   = array(
			        'textarea_name' => 'subtitle_post',
			        'media_buttons' => false,
			        'textarea_rows' => 4,
			        'teeny'         => true,
			        'quicktags'     => false
			    );
			    wp_editor( $content, $editor_id, $settings );
			}
			?>
		</div>
		<div class="metabox_option metabox_option_bc_text">
			<input type="text" name="single_bc_text" id="single_bc_text" value="<?php echo esc_html($asap_single_bc_text); ?>">
		</div>
		<div class="metabox_option metabox_option_bc_text_pillar_page">
			<input type="text" name="single_bc_text_pillar_page" id="single_bc_text_pillar_page" value="<?php echo esc_html($asap_single_bc_text_pillar_page); ?>">
		</div>
		<div class="metabox_option metabox_option_bc_url_pillar_page">
			<input type="text" name="single_bc_url_pillar_page" id="single_bc_url_pillar_page" value="<?php echo esc_html($asap_single_bc_url_pillar_page); ?>">
		</div>
		<div class="metabox_option metabox_option_head_custom_code">
			<textarea name="head_custom_code" id="head_custom_code" rows="4"><?php echo esc_textarea($asap_head_custom_code); ?></textarea>
		</div>
		<div class="metabox_option metabox_option_foot_custom_code">
			<textarea name="foot_custom_code" id="foot_custom_code" rows="4"><?php echo esc_textarea($asap_foot_custom_code); ?></textarea>
		</div>
		<script>
			jQuery(document).ready(function ($) {
				var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};

				// Configuración para el editor de CSS
				var cssEditorSettings = _.extend({}, editorSettings.codemirror, {
					lineNumbers: true,
					mode: 'css', // Modo CSS
					indentUnit: 2,
					viewportMargin: Infinity,
				});

				// Configuración para el editor de JavaScript
				var jsEditorSettings = _.extend({}, editorSettings.codemirror, {
					lineNumbers: true,
					mode: 'javascript', // Modo JavaScript
					indentUnit: 2,
					viewportMargin: Infinity,
				});

				// Inicializar editor para el código CSS
				var headEditor = wp.codeEditor.initialize($('#head_custom_code'), { codemirror: cssEditorSettings });

				// Inicializar editor para el código JavaScript
				var footEditor = wp.codeEditor.initialize($('#foot_custom_code'), { codemirror: jsEditorSettings });

				// Refrescar los editores después de la inicialización
				setTimeout(function () {
					headEditor.codemirror.refresh();
					footEditor.codemirror.refresh();
				}, 500);

				// Función para sincronizar contenido de CodeMirror con el textarea
				function saveEditorsContent() {
					headEditor.codemirror.save();
					footEditor.codemirror.save();
				}

				// Para el editor clásico
				if (typeof wp === 'undefined' || typeof wp.data === 'undefined') {
					$('#post').on('submit', function () {
						saveEditorsContent();
					});
				}

				// Para Gutenberg, solo si wp.data está definido y core/editor es accesible
				if (typeof wp !== 'undefined' && typeof wp.data !== 'undefined' && typeof wp.data.select('core/editor') !== 'undefined') {
					wp.data.subscribe(function () {
						const isSavingPost = wp.data.select('core/editor').isSavingPost();
						const isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();

						if (isSavingPost && !isAutosavingPost) {
							saveEditorsContent();
						}
					});
				}
			});
		</script>
	</div>
</div>

<?php } 
	

function asap_anchor_text_page_callback( $post ) {
	$asap_anchor_cluster 		= get_post_meta( $post->ID, 'asap_anchor_cluster', true );
?>
	

<div class="postmetabox">
	<div class="postmetabox_left">			

		<div class="metabox_option metabox_mbottom">
			<label class="label" for="asap_anchor_cluste"><?php  _e( 'Clúster Anchor Text', 'asap' ); ?></label>
		</div>				
		
	</div>
	
	<div class="postmetabox_right">
				
		<div class="metabox_option">
			
			<input type="text" name="asap_anchor_cluster" id="asap_anchor_cluster" value="<?php echo  esc_html( $asap_anchor_cluster ) ; ?>" placeholder="<?php  echo the_title(); ?>"  >
			
	   </div>	
		
	</div>
	
</div>

<?php } 






add_action( 'save_post_page', 'asap_save_custom_fields_page', 10, 2 );

function asap_save_custom_fields_page( $post_id, $post ){


	if ( ! isset( $_POST['asap_custom_box_pages_nonce'] ) ) {
		return;
	}

	$nonce = $_POST['asap_custom_box_pages_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'asap_custom_box_pages' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

 	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}	   




	/* */

	if ( isset( $_POST['asap_header_design'] ) ) {
        update_post_meta( $post_id, 'asap_header_design',  sanitize_text_field ( $_POST['asap_header_design'] ) );
    }

    if( isset( $_POST['asap_add_news_css'] ) && $_POST['asap_add_news_css'] == "1" ) {
        update_post_meta( $post_id, 'asap_add_news_css', asap_sanitize_check ( $_POST['asap_add_news_css'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_add_news_css' );
    }    

    if( isset( $_POST['asap_exclude_news'] ) && $_POST['asap_exclude_news'] == "1" ) {
        update_post_meta( $post_id, 'asap_exclude_news', asap_sanitize_check ( $_POST['asap_exclude_news'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_exclude_news' );
    }

	/* */

	if( isset( $_POST['hide_image_page'] ) && $_POST['hide_image_page'] == "1" ) {
        update_post_meta( $post_id, 'hide_image_page', asap_sanitize_check ( $_POST['hide_image_page'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_image_page' );
    }
	
	if( isset( $_POST['hide_sidebar'] ) && $_POST['hide_sidebar'] == "1" ) {
        update_post_meta( $post_id, 'hide_sidebar', asap_sanitize_check ( $_POST['hide_sidebar'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_sidebar' );
    }
	
	if( isset( $_POST['hide_breadcrumbs'] ) && $_POST['hide_breadcrumbs'] == "1" ) {
        update_post_meta( $post_id, 'hide_breadcrumbs', asap_sanitize_check ( $_POST['hide_breadcrumbs'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_breadcrumbs' );
    }
	if( isset( $_POST['hide_ads'] ) && $_POST['hide_ads'] == "1" ) {
        update_post_meta( $post_id, 'hide_ads', asap_sanitize_check ( $_POST['hide_ads'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_ads' );
    }	
	
	if( isset( $_POST['hide_h1'] ) && $_POST['hide_h1'] == "1" ) {
        update_post_meta( $post_id, 'hide_h1', asap_sanitize_check ( $_POST['hide_h1'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_h1' );
    }
	
	if( isset( $_POST['hide_toc'] ) && $_POST['hide_toc'] == "1" ) {
        update_post_meta( $post_id, 'hide_toc', asap_sanitize_check ( $_POST['hide_toc'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_toc' );
    }

	if( isset( $_POST['hide_social_btn'] ) && $_POST['hide_social_btn'] == "1" ) {
        update_post_meta( $post_id, 'hide_social_btn', asap_sanitize_check ( $_POST['hide_social_btn'] ) );
    } else {
		delete_post_meta( $post_id, 'hide_social_btn' );
    }
	
	if( isset( $_POST['disable_header'] ) && $_POST['disable_header'] == "1" ) {
        update_post_meta( $post_id, 'disable_header', asap_sanitize_check ( $_POST['disable_header'] ) );
    } else {
		delete_post_meta( $post_id, 'disable_header' );
    }	
	
	if( isset( $_POST['disable_footer'] ) && $_POST['disable_footer'] == "1" ) {
        update_post_meta( $post_id, 'disable_footer', asap_sanitize_check ( $_POST['disable_footer'] ) );
    } else {
		delete_post_meta( $post_id, 'disable_footer' );
    }	

	if( isset( $_POST['asap_disable_dynamic'] ) && $_POST['asap_disable_dynamic'] == "1" ) {
        update_post_meta( $post_id, 'asap_disable_dynamic', asap_sanitize_check ( $_POST['asap_disable_dynamic'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_disable_dynamic' );
    }	

	if( isset( $_POST['asap_disable_author'] ) && $_POST['asap_disable_author'] == "1" ) {
        update_post_meta( $post_id, 'asap_disable_author', asap_sanitize_check ( $_POST['asap_disable_author'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_disable_author' );
    }

	if( isset( $_POST['asap_disable_author_box'] ) && $_POST['asap_disable_author_box'] == "1" ) {
        update_post_meta( $post_id, 'asap_disable_author_box', asap_sanitize_check ( $_POST['asap_disable_author_box'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_disable_author_box' );
    }			
	
	if( isset( $_POST['asap_disable_box_design'] ) && $_POST['asap_disable_box_design'] == "1" ) {
        update_post_meta( $post_id, 'asap_disable_box_design', asap_sanitize_check ( $_POST['asap_disable_box_design'] ) );
    } else {
		delete_post_meta( $post_id, 'asap_disable_box_design' );
    }	
	

	/* */

	if( isset( $_POST['subtitle_post'] ) && !empty( $_POST['subtitle_post'] ) ) {
	    update_post_meta( $post_id, 'subtitle_post', wp_kses_post( $_POST['subtitle_post'] ) );
	} else {
	    delete_post_meta( $post_id, 'subtitle_post' );
	}

	
	/* */

	if( isset( $_POST['single_bc_text'] ) && (!empty($_POST['single_bc_text'])) ) {
        update_post_meta( $post_id, 'single_bc_text', sanitize_text_field ( $_POST['single_bc_text'] ) );
    } else {
		delete_post_meta( $post_id, 'single_bc_text' );
    }
	
	if( isset( $_POST['single_bc_text_pillar_page'] ) && (!empty($_POST['single_bc_text_pillar_page'])) ) {
        update_post_meta( $post_id, 'single_bc_text_pillar_page', sanitize_text_field ( $_POST['single_bc_text_pillar_page'] ) );
    } else {
		delete_post_meta( $post_id, 'single_bc_text_pillar_page' );
    }


    /* */

	
	if( isset( $_POST['single_bc_url_pillar_page'] ) && (!empty($_POST['single_bc_url_pillar_page'])) ) {
        update_post_meta( $post_id, 'single_bc_url_pillar_page', sanitize_url ( $_POST['single_bc_url_pillar_page'] ) );
    }  else {
		delete_post_meta( $post_id, 'single_bc_url_pillar_page' );
    }	


    /* */

	if( isset( $_POST['head_custom_code'] ) && (!empty($_POST['head_custom_code'])) ) {
        update_post_meta( $post_id, 'head_custom_code', $_POST['head_custom_code'] );
    }  else {
		delete_post_meta( $post_id, 'head_custom_code' );
    }
	
 	if( isset( $_POST['foot_custom_code'] ) && (!empty($_POST['foot_custom_code'])) ) {
        update_post_meta( $post_id, 'foot_custom_code', $_POST['foot_custom_code'] );
    }  else {
		delete_post_meta( $post_id, 'foot_custom_code' );
    }	
	
	if( isset( $_POST['asap_anchor_cluster'] ) && (!empty($_POST['asap_anchor_cluster'])) ) {
        update_post_meta( $post_id, 'asap_anchor_cluster', sanitize_text_field ( $_POST['asap_anchor_cluster'] ) );
    }  else {
		delete_post_meta( $post_id, 'asap_anchor_cluster' );
    }		
	
	

}

?>