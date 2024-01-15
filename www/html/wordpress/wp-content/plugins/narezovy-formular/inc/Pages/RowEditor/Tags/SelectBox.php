<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class SelectBox {
    
    public function render($select_box_id, $options = null, $select = null, $index = null, $readonly = null){
        $props = $this->define_select_box_props($select_box_id);
        
        if(isset($props['label'])) echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<select '; 
        
        if(isset($props['name'])) {
            echo 'name="';
            if($index) echo $index;
            echo $props['name'] .'" ';
        }
        
        foreach ($props['attrs'] as $attr_name => $attr_value) {
            echo $attr_name .'="' .$attr_value .'" ';
        }

        if($readonly) echo ' readonly';
        
        echo '>' .PHP_EOL;
       
        $is_selected = $select ?? $props['select'];
        if($options === NULL) $options = $props['options'];

        foreach ($options as $value => $text) {
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
            'tupl' => [
                'name'   => '[tupl]',
                'options' => [
                    'NE' => 'NE',
                    '30mm' => '30mm',
                    '36mm' => '36mm dek.',
                    '36mm-bila' => '36mm bílá',                
                ],
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-tupl',
                ]
            ],
            'lepidlo' => [
                'name'   => '[lepidlo]',
                'options' => [
                    '0' => 'Trans.',
                    '1' => 'Bílá',
                ],                
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-lepidlo',
                ]
            ]         
        ];

        return $select_box[$select_box_id];
    }
}
