<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

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
            'navod' => [
                'text' => 'Návod',
                'disabled' => null,
                'attrs' => [
                    'class' => 'button button-main',
                    'type' => 'button'
                ]
            ],
            'smazat_opt' => [
                'text' => 'Díly nelze editovat. Pro odemčení editace klikněte zde.',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_delete_opt',
                    'class' => 'button-delete-opt',
                    'type' => 'button',
                    'title' => 'Odemknout editaci dílů'
                ]
            ],
            'aplikovat_zmeny' => [
                'text' => 'Aplikovat změny',
                'disabled' => null,
                'attrs' => [
                    'id'   => 'apply-changes-button',
                    'class' => 'button button-main',
                    'type' => 'button',
                    'title' => 'Aplikovat změny',
                    'style' => 'margin-top: 10px;'
                ]
            ],
            'mat_select' => [
                'text' => 'Uložit',
                'disabled' => null,
                'attrs' => [
                    'id'   => 'mat-select-button',
                    'class' => 'button button-main',
                    'type' => 'button',
                    'title' => 'Uložit',
                    'style' => 'margin-top: 10px;'
                ]
            ],
            'smazat_radek' => [
                'text' => '<span class="dashicons dashicons-small dashicons-trash"></span>',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_smazat_radek',
                    'class' => 'button button-sm',
                    'type' => 'button',
                    'title' => 'smazat'
                ]
            ],
            'duplikovat_radek' => [
                'text' => '<span class="dashicons dashicons-small dashicons-admin-page"></span>',
                'disabled' => null,
                'attrs' => [
                    'name'   => 'btn_duplikovat_radek',
                    'class' => 'button button-sm',
                    'type' => 'button',
                    'title' => 'duplikovat'
                ]
            ],            
        ];
        return $button[$button_id];
    }
}
