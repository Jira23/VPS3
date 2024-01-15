<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class Input {
    
    public function render($input_id, $value = null, $index = null, $max = ''){

        $props = $this->define_input_props($input_id);
        if (isset($props['label'])) echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<input ';
        if(isset($props['name'])) {
            echo 'name="';
            if($index) echo $index;
            echo $props['name'] .'" ';
        }
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
            'form nazev' => [
                'name'   => 'formular[nazev]',
                'label' => 'Název formuláře:',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'style' => 'width:450px;',
                    'tabindex' => '1',
                    'required' => ''
                ]
            ],            
            'název' => [
                'name'   => '[nazev_dilce]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-name',
                ]
            ],
            'materiál' => [
                'name'   => '[lamino_id]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'style' => 'display:none;',
                ]
            ],
            'počet' => [
                'name'   => '[ks]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-pocet',
                ]
            ],
            'délka' => [
                'name'   => '[delka_dilu]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-size',
                ]
            ],
            'šířka' => [
                'name'   => '[sirka_dilu]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'class' => 'parts-table-input-size',
                ]
            ],
            'deska_hidden' => [
                'name'   => '[lamino_id]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'lamino_id'
                ]
            ],
            'hrana_type_hidden' => [
                'name'   => '[hrana]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'hrana_type'
                ]
            ],
            'hrana_id_hidden' => [
                'name'   => '[hrana_id]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'hrana_id'
                ]
            ],
            'fig_name_hidden' => [
                'name'   => '[fig_name]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'class' => 'fig_name'
                ]
            ],
            'fig_part_code_hidden' => [
                'name'   => '[fig_part_code]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'class' => 'fig_part_code'
                ]
            ],
            'fig_formula_hidden' => [
                'name'   => '[fig_formula]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'class' => 'fig_formula'
                ]
            ],
            'params_hidden' => [
                'name'   => '[params]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'params'
                ]
            ]              
        ];

        return $input[$input_id];
    }
}
