<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags;

use Inc\Base\BaseController;

class InfoModal extends BaseController {
    
    
    public function render($modal_id){
        
        $props = $this->define_modal_props($modal_id);
        echo '<div class="modal-container" id="' .$props['id'] .'">';
        echo '  <div class="modal-content">';
        echo '      <div class="modal-header"><span class="dashicons dashicons-warning"></span></div>';
        echo '      <div class="modal-body"><h4>' .$props['message']  .'</h4></div>';
        echo '      <div class="modal-footer">';
        
        foreach ($props['buttons'] as $button) {
            echo $button;    
        }
        
        if ($props['return_button']) echo '        <button class="button button-main" onclick="$(\'#' .$props['id'] .'\').css(\'display\', \'none\');">Zpět</button>';
        echo '      </div>'; 
        echo '  </div>';
        echo '</div>';
        
    }    
    
    private function define_modal_props($modal_id){
        $modal = [
            'price_alert' => [
                'id' => 'price_alert',
                'message' => 'Ceny produktů se od poslední optimalizace změnily.<br>Je nutné provést novou optimalizaci.',
                'buttons' => [
                    0 => '<button name="btn_optimalizovat" class="button button-main" type="button" onclick="$(\'#price_alert\').css(\'display\', \'none\');">Optimalizovat</button>',
                ],
                'return_button' => true
            ],
            'hash_alert' => [
                'id' => 'hash_alert',
                'message' => 'Fomulář neexistuje. Zkontrolujte prosím správnost odkazu.',
                'buttons' => [
                    0 => '<a href="/"><button name="btn_optimalizovat" class="button button-main" type="button">Zavřít</button></a>',
                ],
                'return_button' => false
            ]            
        ];
        
        return $modal[$modal_id];
        
    }

}
