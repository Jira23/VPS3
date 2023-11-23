<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

class PartsList extends RenderEditor {
    public function __construct() {
        parent::__construct();
    }
    
    public function render_parts_list(){
        $this->render_title();
        if(!$this->parts){
            $this->render_empty_list();
            return;
        }
        $this->render_head();
        $this->render_table_content();
        $this->render_footer();
    }
    
    private function render_title(){
        ?>
            <div>
                <h4 style="margin-top: 40px; display: inline-block;">Výpis zadaných dílů</h4>
                <div class="tooltip" style="margin-right: 50px;">
                    <?php $this->tooltip->render_tooltip('vypis_dilu'); ?>
                </div>    
            </div>
        <?php
    }
    
    private function render_table_content(){
        echo '<tbody style="display: block; overflow: auto;">';
        foreach ($this->parts as $row) {
            echo '<tr' .(empty($row->fig_name) ? '' : ' class="figure"') . '>';
            echo '<td style="width: 15%"><a href="?form_id=' .$this->form_id .'&part_id=' .$row->id .'">' .$this->get_deska_name_by_id($row->lamino_id) .'</a></td>';
            echo '<td style="width: 10%"><a href="?form_id=' .$this->form_id .'&part_id=' .$row->id .'">' .$row->nazev_dilce .'</a></td>';
            echo '<td style="width: 5%">' .$row->ks .'</td>';
            echo '<td style="width: 7%">' .$row->delka_dilu .'</td>';
            echo '<td style="width: 7%">' .$row->sirka_dilu .'</td>';
            echo '<td style="width: 8%">' . ($row->orientace == 1 ? 'ANO' : 'NE') . '</td>';
            echo '<td style="width: 8%">' .($row->hrana_dolni != 0 ? $this->get_hrana_name_by_id($row->hrana_dolni, true) : '') .'</td>';
            echo '<td style="width: 8%">' .($row->hrana_horni != 0 ? $this->get_hrana_name_by_id($row->hrana_horni, true) : '') .'</td>';
            echo '<td style="width: 8%">' .($row->hrana_prava != 0 ? $this->get_hrana_name_by_id($row->hrana_prava, true) : '') .'</td>';
            echo '<td style="width: 8%">' .($row->hrana_leva != 0 ? $this->get_hrana_name_by_id($row->hrana_leva, true) : '') .'</td>';
            echo ($row->tupl == '36mm') ? '<td style="width: 5%">' .$row->tupl .'-dekor</td>' : '<td style="width: 5%">' .$row->tupl .'</td>';
            echo '<td style="width: 5%">' .($row->lepidlo === '0' ? 'Trans.' : ($row->lepidlo === '1' ? 'Bílé' : '')) .'</td>';
            echo '<td style="width: 5%">' .(empty($row->fig_name) ? '' : $row->fig_name .'|' .$row->fig_part_code) .'</td>';
            echo '<td style="width: 5%">'; 
            $this->button->render_button('smazat_dil', null, ['value' => $row->id]); 
            $this->button->render_button('duplikovat_dil', null, ['value' => $row->id]); 
            echo '</td>';
            echo '</tr>';
        }   
        echo '</tbody>';
    }
    
    private function render_head(){
        ?>
            <div style="overflow-x: auto;">
                <table class="shop_table cart wishlist_table wishlist_view traditional responsive">
                    <thead style="display: block;" class="th-middle">
                        <th style="width: 15%">Lamino</th>
                        <th style="width: 10%">Název dílu</th>
                        <th style="width: 5%">Počet</th>
                        <th style="width: 7%">Délka</th>
                        <th style="width: 7%">Šířka</th>
                        <th style="width: 8%">Orien.</th>
                        <th style="width: 8%">Hrana přední</th>
                        <th style="width: 8%">Hrana zadní</th>
                        <th style="width: 8%">Hrana pravá</th>
                        <th style="width: 8%">Hrana levá</th>
                        <th style="width: 5%">Tupl</th>
                        <th style="width: 5%">Lepidlo</th>
                        <th style="width: 5%">Figura</th>
                        <th style="width: 5%;"></th>
                    </thead>
        <?php
    }
    
    private function render_footer(){
        ?>
                </table>
            </div>  
        <?php
    }
    
    private function render_empty_list(){
        echo '<h2>Zatím zde nejsou žádné díly.</h2>';
    }
}
