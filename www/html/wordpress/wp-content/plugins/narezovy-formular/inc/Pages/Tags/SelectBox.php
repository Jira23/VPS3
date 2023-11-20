<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags;

class SelectBox {
    
    public function render_select_box($select_box_id, $select = null){
        $props = $this->define_select_box_props($select_box_id);
        
        echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<select name="' .$props['name'] .'" '; 
        
        foreach ($props['attrs'] as $attr_name => $attr_value) {
            echo $attr_name .'="' .$attr_value .'" ';
        }
        
        echo '>' .PHP_EOL;
       
        $is_selected = $select ?? $props['select'];
        foreach ($props['options'] as $value => $text) {
            echo '<option value="' .$value .'"';
            if($value == $is_selected) echo ' selected ';

            if(isset($text['attrs'])){
                foreach ($text['attrs'] as $option_attr_name => $option_attr_value) {
                    echo $option_attr_name .'="' .$option_attr_value .'" ';
                }
                echo '>' .$text['text'] .'</option>' .PHP_EOL;   
            } else {
                echo '>' .$text .'</option>' .PHP_EOL;   
            }
        }
        
        echo '</select>' .PHP_EOL;
    }
    
    private function define_select_box_props($select_box_id){
        $select_box = [
            'olepeni' => [
                'name'  => 'formular[olepeni]',
                'label' => 'Možnosti olepení:',
                'options' => [
                    '0' => 'Hranění standard - průmyslová verze',
                    '1' => 'Hranění s dočištěním - dočištění chemií s kontrolou - s příplatkem'
                ],
                'select' => null,
                'attrs' => [
                    'style' => 'width:450px;',
                    'tabindex' => '2'
                ]
            ],
            'stitky' => [
                'name'  => 'formular[stitky]',
                'label' => 'Štítky s rozměry:',
                'options' => [
                    '1' => 'Ano',
                    '0' => 'Ne'
                ],
                'select' => null,
                'attrs' => [
                    'style' => 'width:100px;',
                    'tabindex' => '3'
                ]
            ],
            'doprava' => [
                'name'  => 'formular[doprava]',
                'label' => 'Doprava:',
                'options' => [
                    '0' => 'Osobně Ml. Boleslav',
                    '1' => 'Osobně Jiz. Vtelno',
                    '2' => 'Rozvoz',
                ],
                'select' => null,
                'attrs' => [
                    'style' => 'width:180px;',
                    'tabindex' => '4'
                ]
            ],
            'hrana_select' => [
                'name'  => 'dil[hrana]',
                'label' => 'Hrana (č. dekoru + struktura):',
                'options' => [
                    '0' => 'Přivzorovaná',
                    '-1' => 'Žádná',
                    '1' => 'Odlišná'
                ],
                'select' => null,
                'attrs' => [
                    'id' => 'hrana',
                    'style' => 'width:200px;',
                    'tabindex' => '6'
                ]
            ],
            'lepidlo' => [
                'name'  => 'dil[lepidlo]',
                'label' => 'Barva lepidla:',
                'options' => [
                    '' => [
                        'text' => 'Vyberte',
                        'attrs' =>[
                            'disabled' => '',
                            'selected' => '',
                        ]
                    ],
                    '0' => 'Transparentní',
                    '1' => 'Bílá',
                ],
                'select' => '',
                'attrs' => [
                    'id' => 'lepidlo',
                    'style' => 'width:200px;',
                    'tabindex' => '6',
                    'required' => ''
                ]
            ],
            'tupl' => [
                'name'  => 'dil[tupl]',
                'label' => 'Tupl:',
                'options' => [
                    'NE' => 'NE (síla 18mm)',
                    '30mm' => '30mm (podlepení deskou 12mm - dekor - bílá)',
                    '36mm' => '36mm (podlepení deskou 18mm ve stejném dekoru)',
                    '36mm-bila' => '36mm (podlepení deskou 18mm - dekor - bílá)',
                ],
                'select' => null,
                'attrs' => [
                    'id' => 'tupl',
                    'style' => 'width:380px;',
                    'tabindex' => '9'
                ]
            ],
            'hrana_horni' => [
                'name'  => 'dil[hrana_horni]',
                'label' => 'Hrana zadní:',
                'options' => ['0' => 'bez hrany'],
                'select' => null,
                'attrs' => [
                    'id' => 'select-hrana-horni',
                    'style' => 'width:250px;',
                    'tabindex' => '13'
                ]
            ],            
            'hrana_leva' => [
                'name'  => 'dil[hrana_leva]',
                'label' => 'Hrana levá:',
                'options' => ['0' => 'bez hrany'],
                'select' => null,
                'attrs' => [
                    'id' => 'select-hrana-leva',
                    'style' => 'width:250px;',
                    'tabindex' => '15'
                ]
            ],
            'hrana_prava' => [
                'name'  => 'dil[hrana_prava]',
                'label' => 'Hrana pravá:',
                'options' => ['0' => 'bez hrany'],
                'select' => null,
                'attrs' => [
                    'id' => 'select-hrana-prava',
                    'style' => 'width:250px;',
                    'tabindex' => '14'
                ]
            ],
            'hrana_dolni' => [
                'name'  => 'dil[hrana_dolni]',
                'label' => 'Hrana přední:',
                'options' => ['0' => 'bez hrany'],
                'select' => null,
                'attrs' => [
                    'id' => 'select-hrana-dolni',
                    'style' => 'width:250px;',
                    'tabindex' => '12'
                ]
            ]
        ];

        return $select_box[$select_box_id];
    }
}
