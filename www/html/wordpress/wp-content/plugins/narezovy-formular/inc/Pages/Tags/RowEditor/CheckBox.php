<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags\RowEditor;

class CheckBox {
    
    //                <input id="obchodni_podminky" type="checkbox">
    //            <label for="obchodni_podminky">Souhlasím s <a href="/obchodni-a-technicke-podminky-vyroby-rezani/">obchodními podmínkami</a>.</label>
    
    
    public function render($checkbox_id, $checked = false){
       
        $props = $this->define_checkbox_props($checkbox_id);
        echo '<input type="checkbox" ';

        echo isset($props['id']) ? 'id="' .$props['id'] .'"' : '';
        
        foreach ($props['attrs'] as $attr_name => $attr_value) {
            echo $attr_name .'="' .$attr_value .'" ';
        }
        
        echo $checked ? 'checked' : $props['checked'];
        echo '></input>' .PHP_EOL;
        
        if (isset($props['label'])) echo '<label for="' .$props['id'] .'">' .$props['label'] .'</label>' .PHP_EOL;
    }
    
    private function define_checkbox_props($checkbox_id){
        $checkbox= [
            'orient' => [
                'name' => 'parts[orientace]',
                'checked' => NULL,
                'attrs' => []
            ]
        ];

        return $checkbox[$checkbox_id];
    }
}
