<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class Input {
    
    public function render($input_id, $value = null, $max = ''){

        $props = $this->define_input_props($input_id);
        if (isset($props['label'])) echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<input ';
        if(isset($props['name'])) echo 'name="' .$props['name'] .'" ';

        foreach ($props['attrs'] as $attr_name => $attr_value) {
            echo $attr_name .'="' .$attr_value .'" ';
        }
        
        $value_to_render = $value ?? $props['value'];
        
        echo 'value="' .$value_to_render .'"';
        if($max != '') echo ' max="' .$max .'"';
        echo'></input>' .PHP_EOL;
    }
    
    private function define_input_props($input_id){
        $input = [
            'název' => [
                'name'   => 'parts[nazev]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-name',
                ]
            ],
            'materiál' => [
                'name'   => 'parts[lamino_id]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'style' => 'display:none;',
                ]
            ],
            'počet' => [
                'name'   => 'parts[ks]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-pocet',
                ]
            ],
            'délka' => [
                'name'   => 'parts[delka_dilu]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-size',
                ]
            ],
            'šířka' => [
                'name'   => 'parts[sirka_dilu]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-size',
                ]
            ],
            'deska_hidden' => [
                'name'   => 'dil[lamino_id]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'lamino_id'
                ]
            ],
            'hrana_type_hidden' => [
                'name'   => 'dil[hrana]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'hrana_type'
                ]
            ]
        ];

        return $input[$input_id];
    }
}
