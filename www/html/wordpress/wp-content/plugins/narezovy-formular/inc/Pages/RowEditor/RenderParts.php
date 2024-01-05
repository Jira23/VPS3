<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\RowEditor;

use Inc\Pages\PagesController;
use Inc\AJAX\AjaxUtils;

class RenderParts extends RenderEditor{

    
    public function render_parts($parts){
        $this->render_table_header();
        $this->render_table_head();
        $this->render_table_body($parts);
        $this->render_table_footer();

    }
    
    private function render_table_header(){
    ?>
        <div class="parts-table-container">
            <table class="parts-table">
    <?php
    }
  
    private function render_table_head(){
        $titles = ['č.', 'název', 'materiál desky', 'ks', 'délka', 'šířka', 'orient.', 'materiál<br>hrany', 'hrana<br>přední', 'hrana<br>zadní', 'hrana<br>pravá', 'hrana<br>levá', 'tupl', 'lepidlo', 'figura'];

        echo '<thead>';
        echo '  <tr>';
        foreach ($titles as $title) {
            echo '<th>' .$title .'</th>';
        }
        echo '</tr>';
        echo '</thead>';
    }
    
    private function render_table_body($parts){
    ?>
        <tbody>
            <?php
                $i = 1;
                foreach ($parts as $part) {
                    echo '<tr>';
                    echo '<td>' .$i .'</td>';
                    echo '<td>'; echo $this->input->render('název', $part->nazev_dilce); echo '</td>';
                    //echo '<td>'; echo $this->input->render('materiál', $this->get_deska_name_by_id($part->lamino_id)); echo'</td>';
                    echo '<td>'; echo $this->mat_selector->render($this->get_deska_name_by_id($part->lamino_id), $this->get_deska_icon($part->lamino_id), 'material_deska'); echo'</td>';
                    echo '<td>'; echo $this->input->render('počet', $part->ks); echo'</td>';
                    echo '<td>'; echo $this->input->render('délka', $part->delka_dilu); echo'</td>';
                    echo '<td>'; echo $this->input->render('šířka', $part->sirka_dilu); echo'</td>';
                    echo '<td>'; echo $this->checkbox->render('orient', $part->orientace == 1 ? true : false); echo'</td>';
                    echo '<td>'; echo $this->mat_selector->render($this->get_hrana_name_by_id($part), $this->get_hrana_icon($part), 'material_hrana'); echo'</td>';           // there must be same id for all edges, so I can get it from anywhere
                    echo '<td>'; echo $this->select_box->render('hrana predni'); echo'</td>';
                    echo '<td>'; echo $this->select_box->render('hrana zadni'); echo'</td>';
                    echo '<td>'; echo $this->select_box->render('hrana prava'); echo'</td>';
                    echo '<td>'; echo $this->select_box->render('hrana leva'); echo'</td>';
                    echo '<td>'; echo $this->select_box->render('tupl', NULL, $part->tupl); echo'</td>';
                    echo '<td>'; echo $this->select_box->render('lepidlo', NULL, $part->lepidlo); echo'</td>';
                    echo '<td>(1,2,3)/(4,5,6)</td>';
                    echo '</tr>';
                    $i++;
                }        
            ?>
        </tbody>
    <?php
    }
    
    private function render_table_footer(){
    ?>
            </table>       
        </div>
    <?php
    }
    
    public function get_deska_name_by_id($deska_id){
        if($deska_id === NULL || $deska_id == 0) return NULL;
        
        $deska_post = get_post($deska_id);
        if($deska_post === NULL) return 'JIŽ NENÍ V PRODEJI';
        
        $deska_name = $deska_post->post_title;
        
        return $deska_name;
    }
    
    public function get_hrana_name_by_id($part, $include_dimensions = false){
        $hrana_id = array_unique([$part->hrana_horni, $part->hrana_dolni, $part->hrana_prava, $part->hrana_leva])[0];
        if($hrana_id == NULL || $hrana_id == 0) return NULL;
        $hrana = wc_get_product($hrana_id);
        $au = new AjaxUtils();
        $hrana_title = $au->shorten_hrana_title($hrana)['decor'];
        if($include_dimensions) $hrana_title .= ' ' .$au->shorten_hrana_title($hrana)['rozmer'];
        return $hrana_title;
    }    

    private function get_deska_icon($deska_id){
        if($deska_id == '' || $deska_id == '0') return;
        return wp_get_attachment_image_src( wc_get_product($deska_id)->get_image_id())[0]; 
    }
    
    private function get_hrana_icon($part){
        $hrana_id = array_unique([$part->hrana_horni, $part->hrana_dolni, $part->hrana_prava, $part->hrana_leva])[0];
        if($hrana_id == '' || $hrana_id == '0') return;
        $image_id = wc_get_product($hrana_id)->get_image_id();
        if($image_id == '') return $this->plugin_url .'assets/img/no_img_icon.png';
        return wp_get_attachment_image_src($image_id)[0];
    }    
    
}
