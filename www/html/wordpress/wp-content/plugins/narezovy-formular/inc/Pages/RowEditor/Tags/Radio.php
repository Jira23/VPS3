<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class Radio {
    
    public function render($select_box_id, $options = null, $select = null){
        $props = $this->define_radio_props($select_box_id);
        
        foreach ($props['options'] as $key => $value) {
            echo '<label><input type="radio" name="' .$props['name']  .'" value="' .$key .'" ';  
            if($key == $props['select']) echo ' checked';
            echo '>' .$value .'</label>' .PHP_EOL;
        }
    }
    
    private function define_radio_props($select_box_id){
        $select_box = [
            'modal edge type' => [
                'name'   => 'modal-edge-type',
                'options' => [
                    '-1' => 'Žádná',
                    '0' => 'Přivzorovaná',
                    '1' => 'Odlišná'
                ],
                'select' => -1,
            ],
         ];

        return $select_box[$select_box_id];
    }
}
