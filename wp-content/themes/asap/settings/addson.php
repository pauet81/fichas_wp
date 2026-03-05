<style>
    .wrapper-asap-options select {
        width: 250px;
    }
    #asap_calculate_cost {
        min-height: 33px;
    }    
    #cancel-process {
        text-decoration: underline;
        cursor: pointer;
        color: #b32d2e;              
    }
    #cancel-process:hover {
        text-decoration: none;
    }    
    .wrapper-asap-options input[type='number'] {
        margin: 0;
        min-width: 60px;
        padding: 4px 6px !important;
        margin-top: 1px;
    }
    .asap-options h2 {
        display: flex;
        align-items: center;
    }

    .asap-options h3 {
        display: flex;
        margin: 0;
        font-size: 16px !important;
            width: 100%;
    }

    .asap-options h2 span {
        background: #202225;
        color: #fff;
        margin-left: 6px;
    }      
    .select2-container--default .select2-selection--single {
        width: 250px;
        padding: 4px 6px !important;
        border-radius: 4px !important;
        border: 1px solid #8c8f94 !important;
        font-size: 14px;
        line-height: 2;
        color: #2c3338;
        background-size: 16px 16px;
        cursor: pointer;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px; 
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px; /* O ajusta según el tamaño de tu flecha de fondo */
        background: none; /* Remueve cualquier flecha por defecto de Select2 si es necesario */
    }

    .select2-container .select2-selection--single {
        height: 38px;
        min-height: 38px;
    }
    /* Ajusta el posicionamiento del texto dentro del select para que no se sobreponga con la flecha */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-right: 24px; /* Ajusta según sea necesario */
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
    }
    /* Estilos para el botón cuando está deshabilitado */
    .asap-options .submit .button-disabled:disabled {
        font-size: 15px;
        line-height: 40px;
        height: 42px;
        padding: 0 34px;
        color: #a7aaad !important;
        border-color: #dcdcde !important;
        background: #f6f7f7 !important;
        box-shadow: none !important;
        cursor: default;
        transform: none !important;
    }

    .asap-options .submit .button-primary:not(.not-button .button-primary):disabled {
        color: #a7aaad !important;
        border-color: #dcdcde !important;
        background: #f6f7f7 !important;
        box-shadow: none !important;
        cursor: default;
        transform: none !important;            
    }

    .asap-options textarea {
        width: 450px !important;
        max-width: 450px !important;
    }

  .asap-options .form-table th.deh3 {
    padding-top: 28px;
  }  
  #asap-ir {
    color: #1abc9c;
    border-color: #1abc9c ;
    background: #f1faf9;
    vertical-align: top;
    display: inline-block;
    text-decoration: none;
    font-size: 13px;
    line-height: 2.15384615;
    min-height: 30px;
    margin: 0;
    padding: 0 10px;
    cursor: pointer;
    border-width: 1px;
    border-style: solid;
    -webkit-appearance: none;
    border-radius: 3px;
    white-space: nowrap;
    box-sizing: border-box;
}
  #asap-ir:hover {
    background: #e8f8f5;
    border-color: #17a98c ;
    color: #17a98c;
    
  }
</style>



<?php if (current_user_can('manage_options')) : ?>


<style>
    .asap-tooltip-a {
        cursor: pointer;
    }
</style>

<form method="post" id="asap-manual-niche">

    <?php wp_nonce_field('devlog_nonce_action', 'devlog_nonce_field'); ?>

    <table class="form-table" id="asap-fieldset-one">

        <h2><?php _e('Complementos', 'asap'); ?><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('En esta sección encontrarás addons o complementos que potencian o agregan funciones a Asap Theme.', 'asap'); ?></span></span></h2>

        <tbody>

             <tr>
                <th scope="row"><label for="asap_resources_module"><?php _e('ASAP Premium Layouts', 'asap'); ?></label><span class="asap-tooltip">?<span class="tooltiptext"><?php _e('Este complemento potenciará la portada diario de Asap Theme con 10 nuevos diseños únicos, exclusivos y muy rápidos.', 'asap'); ?></span></span></th>
                <td><a href="https://asaptheme.com/asap-premium-layouts" id="asap-ir" target="_blank" rel="nofollow noopener" class="button">Ver complemento</a></td>
            </tr>

        </tbody>


    </table>

</form>

<?php else: ?>

<div class="notice notice-warning inline active-plugin-edit-warning" style="margin:10px !important;"><p>Parece que no tienes los permisos suficientes para ver esta página.</p></div>

<?php endif; ?>