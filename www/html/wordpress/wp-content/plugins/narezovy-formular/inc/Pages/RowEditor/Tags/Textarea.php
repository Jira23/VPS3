<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class Textarea {
    
    public function render($textarea_id, $value = null){
       
        $props = $this->define_input_props($textarea_id);
        if (isset($props['label'])) echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<textarea name="' .$props['name'] .'" ';

        foreach ($props['attrs'] as $attr_name => $attr_value) {
            echo $attr_name .'="' .$attr_value .'" ';
        }
        
        $value_to_render = $value ?? $props['value'];
        
        echo'>' .$value_to_render .'</textarea>' .PHP_EOL;
    }
    
    private function define_input_props($textarea_id){
        $textarea = [
            'poznamka' => [
                'name'   => 'formular[poznamka]',
                'label' => 'Poznámka (max. 1000 znaků):',
                'value' => NULL,
                'attrs' => [
                    'maxlength' => '1000',
                    'style' => 'width:100%;'
                ]
            ]            
        ];

        return $textarea[$textarea_id];
    }
}
