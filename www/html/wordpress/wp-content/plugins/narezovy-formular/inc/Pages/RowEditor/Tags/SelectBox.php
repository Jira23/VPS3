<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class SelectBox {
    
    public function render($select_box_id, $options = null, $select = null, $index = null){
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
            'hrana dokola' => [
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-edge',
                ]
            ],
            'hrana predni' => [
                'name'   => '[hrana_dolni]',
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-edge',
                ]
            ],
            'hrana zadni' => [
                'name'   => '[hrana_horni]',
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-edge',
                ]
            ],
            'hrana prava' => [
                'name'   => '[hrana_prava]',
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-edge',
                ]
            ],
            'hrana leva' => [
                'name'   => '[hrana_leva]',
                'select' => null,
                'attrs' => [
                    'class' => 'parts-table-selectbox-edge',
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
            ],            
        ];

        return $select_box[$select_box_id];
    }
}
