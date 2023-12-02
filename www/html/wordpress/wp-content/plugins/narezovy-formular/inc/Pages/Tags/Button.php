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
            'opustit' => [
                'text' => 'Opustit formulář',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_opustit',
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'ulozit' => [
                'text' => 'Uložit zadání',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_ulozit_zadani',
                    'class' => 'button button-main',
                    'type' => 'input'
                ]
            ],
            'optimalizovat' => [
                'text' => 'optimalizovat',
                'disabled' => null,
                'attrs' => [
                    'name' => 'btn_optimalizovat',
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'odeslat' => [
                'text' => 'Odeslat objednávku',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_odeslat',
                    'class' => 'button button-main',
                    'type' => 'input'
                ]
            ],
            'zpet_na_seznam' => [
                'text' => 'Zpět na seznam',
                'disabled' => null,
                'attrs' => [
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'ulozit_dil' => [
                'text' => 'Uložit díl',
                'disabled' => null,
                'attrs' => [
                    'id' => 'btn_ulozit_dil',
                    'name'   => 'btn_ulozit_dil',
                    'class' => 'button button-main',
                    'type' => 'input'
                ]
            ],
            'smazat_dil' => [
                'text' => 'X',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_smazat_dil',
                    'class' => 'button button-sm',
                    'type' => 'button',
                    'title' => 'smazat'
                ]
            ],
            'duplikovat_dil' => [
                'text' => 'D',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_duplikovat_dil',
                    'class' => 'button button-sm',
                    'type' => 'button',
                    'title' => 'duplikovat'
                ]
            ],
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
            ]            
        ];
        return $button[$button_id];
    }
}
