<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags;

class Button {
    
    public function render_button($button_id, $disabled = null, $variable_attrs = array()){
       
        $props = $this->define_button_props($button_id);

        echo '<button ';

        if(!empty($variable_attrs)) $props['attrs'] = array_merge($props['attrs'], $variable_attrs);

        foreach ($props['attrs'] as $attr_name => $attr_value) {
            echo $attr_name .'="' .$attr_value .'" ';
        }

        $is_disabled = $disabled ?? $props['disabled'];        
        
        echo $is_disabled .'>' .$props['text'] .'</button>' .PHP_EOL;        

    }
    
    private function define_button_props($button_id){
        $button = [
            'smazat_formular' => [
                'text' => '<span class="dashicons dashicons-trash"></span>',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_smazat_formular',
                    'class' => 'button button-sm',
                    'type' => 'button',
                    'title' => 'smazat'
                ]
            ],
            'duplikovat_formular' => [
                'text' => '<span class="dashicons dashicons-admin-page"></span>',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_duplikovat_formular',
                    'class' => 'button button-sm',
                    'type' => 'button',
                    'title' => 'duplikovat'
                ]
            ],
            'nove_zadani' => [
                'text' => 'Nové zadání',
                'disabled' => null,
                'attrs' => [
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'import' => [
                'text' => 'Import',
                'disabled' => null,
                'attrs' => [
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'navod' => [
                'text' => 'Návod',
                'disabled' => null,
                'attrs' => [
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'zpet_na_seznam' => [
                'text' => 'Zpět na seznam',
                'disabled' => null,
                'attrs' => [
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ]            
        ];
        return $button[$button_id];
    }
}
