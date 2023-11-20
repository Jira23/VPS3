<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags;

class Input {
    
    public function render_input($input_id, $value = null, $max = ''){
       
        $props = $this->define_input_props($input_id);
        if (isset($props['label'])) echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<input name="' .$props['name'] .'" ';

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
            'nazev' => [
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
            'deska' => [
                'name'   => '',
                'label' => 'Lamino (č. dekoru/materiál):',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'style' => 'width:250px;',
                    'id' => 'input-deska',
                    'autocomplete' => 'off',
                    'tabindex' => '4',
                    'required' => ''
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
            'hrana_input' => [
                'name'   => '',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'id' => 'input-hrana',
                    'autocomplete' => 'off',
                    'style' => 'display:none; width:250px;'
                ]
            ],
            'hrana_input_hidden' => [
                'name'   => 'dil[hrana_id]',
                'value' => NULL,
                'attrs' => [
                    'type' => 'hidden',
                    'id' => 'hrana_id'
                ]
            ],            
            'nazev_dilce' => [
                'name'   => 'dil[nazev_dilce]',
                'label' => 'Název dílce:',
                'value' => NULL,
                'attrs' => [
                    'type' => 'text',
                    'id' => 'nazev_dilce',
                    'style' => 'width:250px;',
                    'tabindex' => '7'
                ]
            ],
            'ks' => [
                'name'   => 'dil[ks]',
                'label' => 'Počet ks:',
                'value' => 1,
                'attrs' => [
                    'type' => 'number',
                    'id' => 'ks',
                    'min' => '1',
                    'style' => 'width:100px;',
                    'tabindex' => '8',
                    'required' => ''                    
                ]
            ],
            'delka_dilu' => [
                'name'   => 'dil[delka_dilu]',
                'label' => 'Délka dílu:',
                'value' => null,
                'attrs' => [
                    'type' => 'number',
                    'id' => 'delka_dilu',
                    'style' => 'width:80px;',
                    'tabindex' => '10',
                    'required' => ''                    
                ]
            ],
            'sirka_dilu' => [
                'name'   => 'dil[sirka_dilu]',
                'label' => 'Šířka dílu:',
                'value' => null,
                'attrs' => [
                    'type' => 'number',
                    'id' => 'sirka_dilu',
                    'style' => 'width:80px;',
                    'tabindex' => '11',
                    'required' => ''                    
                ]
            ]            
        ];

        return $input[$input_id];
    }
}
