<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class InputWithWarning {
    
    public function render($input_id, $value = null, $index = null, $max = '', $tooltip_text = null){

        $props = $this->define_input_props($input_id);
        if (isset($props['label'])) echo '<label for="' .$props['name'] .'">' .$props['label'] .'</label>' .PHP_EOL;
        echo '<div class="input-with-warning">' .PHP_EOL;
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
        
        $tooltip_to_render = $tooltip_text ?? $props['tooltip-text'];
        echo '<span class="dashicons dashicons-warning dim-warning">';
        if($tooltip_to_render) echo '<span class="tooltip-text">' .$tooltip_to_render .'</span>';
        echo '</span>' .PHP_EOL;
        echo '</div>';
    }
    
    private function define_input_props($input_id){
        $input = [
            'počet' => [
                'name'   => '[ks]',
                'value' => NULL,
                'tooltip-text' => NULL,
                'attrs' => [
                    'type' => 'number',
                    'class' => 'parts-table-input-pocet',
                    'min' => '1'
                ]
            ],
            'délka' => [
                'name'   => '[delka_dilu]',
                'value' => NULL,
                'tooltip-text' => 'Délka dílce je zadána nesprávně. Chybí nebo je příliš velká.',
                'attrs' => [
                    'type' => 'number',
                    'class' => 'parts-table-input-dimension',
                    'min' => '50'
                ]
            ],
            'šířka' => [
                'name'   => '[sirka_dilu]',
                'value' => NULL,
                'tooltip-text' => 'Šířka dílce je zadána nesprávně. Chybí nebo je příliš velká.',
                'attrs' => [
                    'type' => 'number',
                    'class' => 'parts-table-input-dimension',
                    'min' => '50'
                ]
            ]
         ];

        return $input[$input_id];
    }
}
