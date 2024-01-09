<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\RowEditor;

use Inc\Pages\PagesController;
use Inc\AJAX\AjaxUtils;

class RenderParts extends RenderEditor{

    public $materials_props;
    
    public function render_parts($parts){
        $this->render_table_header();
        $this->render_table_head();
        $this->render_table_body($parts);
        $this->render_table_footer();

        $this->materials_props = [];
    }
    
    private function render_table_header(){
    ?>
        <div class="parts-table-container">
            <table class="parts-table">
    <?php
    }
  
    private function render_table_head(){
        $titles = ['č.', 'název', 'materiál desky', 'ks', 'délka', 'šířka', 'orient.', 'materiál<br>hrany', 'hrana<br>dokola', 'hrana<br>přední', 'hrana<br>zadní', 'hrana<br>pravá', 'hrana<br>levá', 'tupl', 'lepidlo', 'figura', 'úpravy'];

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
                    $name = $this->get_deska_name_by_id($part->lamino_id);
                    $img_url = $this->get_deska_icon($part->lamino_id);

                    $deska_params = $part->params == '' ? $this->set_deska_params($part->lamino_id, $name, $img_url, $part->hrana, $part->hrana_id, $i) : json_decode(stripslashes($part->params), true);

/*
echo '<pre>';
var_dump($deska_params);
echo '</pre>';
*/
 
                    //echo '<tr deska-params="' .htmlspecialchars(json_encode($deska_params)) .'" row-id="' .$i .'">';
                    echo '<tr row-id="' .$i .'">';
                    echo '<td>' .$i .'</td>';
                    echo '<td>'; echo $this->input->render('název', $part->nazev_dilce, 'parts[' .$i .']'); echo '</td>';
                    echo '<td>'; echo $this->mat_selector->render($name, $img_url, 'material_deska'); echo'</td>';
                    echo '<td>'; echo $this->input->render('počet', $part->ks, 'parts[' .$i .']'); echo'</td>';
                    echo '<td>'; echo $this->input->render('délka', $part->delka_dilu, 'parts[' .$i .']'); echo'</td>';
                    echo '<td>'; echo $this->input->render('šířka', $part->sirka_dilu, 'parts[' .$i .']'); echo'</td>';
                    echo '<td>'; echo $this->checkbox->render('orient', $part->orientace == 1 ? true : false, 'parts[' .$i .']'); echo'</td>';
                    
                    if($part->hrana == '-1'){
                        echo '<td>'; echo $this->mat_selector->render(null, null, 'material_hrana'); echo'</td>';
                    }
                    if($part->hrana == '0'){
                        echo '<td>'; echo $this->mat_selector->render($deska_params['edgeName'], $deska_params['edgeImgUrl'], 'material_hrana'); echo'</td>';
                    }
                    if($part->hrana == '1'){
                        echo '<td>'; echo $this->mat_selector->render($deska_params['diffEdgeName'], $deska_params['diffEdgeImgUrl'], 'material_hrana'); echo'</td>';
                    }

                    
                    
                        if($part->hrana === '-1'){
                            echo '<td>'; echo $this->select_box->render('hrana predni', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana zadni', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana prava', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana leva', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
                        }

                        if($part->hrana === '0'){
                            $options = ['0' => ''] + $deska_params['edgDims'];
                            $select = $part->hrana_dolni === '0' ? '' : $part->hrana_dolni;

                            echo '<td>'; echo $this->select_box->render('hrana dokola', $options); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana predni', $options, $part->hrana_dolni === '0' ? 0 : $part->hrana_dolni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana zadni', $options, $part->hrana_horni === '0' ? 0 : $part->hrana_horni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana prava', $options, $part->hrana_prava === '0' ? 0 : $part->hrana_prava, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana leva', $options, $part->hrana_leva === '0' ? 0 : $part->hrana_leva, 'parts[' .$i .']'); echo'</td>';
                        }

                        if($part->hrana === '1'){
                            $options = ['0' => ''] + $deska_params['diffEdgeDims'];
                            //$select = $part->hrana_dolni === '0' ? '' : $part->hrana_dolni;
                            echo '<td>'; echo $this->select_box->render('hrana dokola', $options); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana predni', $options, $part->hrana_dolni === '0' ? '' : $part->hrana_dolni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana zadni', $options, $part->hrana_horni === '0' ? '' : $part->hrana_horni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana prava', $options, $part->hrana_prava === '0' ? '' : $part->hrana_prava, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box->render('hrana leva', $options, $part->hrana_leva === '0' ? '' : $part->hrana_leva, 'parts[' .$i .']'); echo'</td>';
                        }

                    
//var_dump($part->hrana_horni);                    

                    echo '<td>'; echo $this->select_box->render('tupl', NULL, $part->tupl, 'parts[' .$i .']'); echo'</td>';
                    echo '<td>'; echo $this->select_box->render('lepidlo', NULL, $part->lepidlo, 'parts[' .$i .']'); echo'</td>';

                    echo '<td>'; echo $part->fig_formula; echo '</td>';
                    echo '<td>';
                        $this->button->render_button('duplikovat_radek', null); 
                        $this->button->render_button('smazat_radek', null); 
                    echo '</td>';
                    echo $this->input->render('deska_hidden', $part->lamino_id, 'parts[' .$i .']');
                    echo $this->input->render('hrana_type_hidden', $part->hrana, 'parts[' .$i .']');
                    echo $this->input->render('hrana_id_hidden', $part->hrana_id, 'parts[' .$i .']');
                    echo $this->input->render('fig_name_hidden', $part->fig_name, 'parts[' .$i .']');
                    echo $this->input->render('fig_part_code_hidden', $part->fig_part_code, 'parts[' .$i .']');
                    echo $this->input->render('fig_formula_hidden', $part->fig_formula, 'parts[' .$i .']');
                    //echo $this->input->render('params_hidden', htmlspecialchars(json_encode($deska_params)), 'parts[' .$i .']');
                    echo $this->input->render('params_hidden', htmlspecialchars(json_encode($deska_params), ENT_QUOTES, 'UTF-8'), 'parts[' .$i .']');
                    echo '</tr>';
                    $i++;
                }
                
            
            ?>
        </tbody>
        
    <?php
    echo '<tfoot>';
    $this->render_empty_row();
    echo '</tfoot>';
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
    
    private function set_deska_params($product_id, $name, $img_url, $typ_hrany, $diff_edge_id, $row_id){    // sets params used by jQuery functions
        
        if(isset($this->materials_props[$product_id]) && $diff_edge_id === '0') {                        // if mat props are already loaded, change index and return it. Avoids multiple loadin properties of same material
            $to_return = $this->materials_props[$product_id];
            $to_return['row_id'] = $row_id;
            return $to_return;
        }
        
        if(!isset($this->count)) $this->count = 0;
        $this->count ++;
        var_dump($this->count);
        
        $product = wc_get_product($product_id);
        
        $sku = $product->get_sku();
        $delka = $product->get_attribute('pa_delka');
        $sirka = $product->get_attribute('pa_sirka');
        $sila = $product->get_attribute('pa_sila');
        
        $edge = (new \Inc\AJAX\Desky())->get_edge_props($product_id);

        $to_return = [
            'row_id' => $row_id,
            'id' => $product_id,
            'name' => $name,
            'sku' => $sku,
            'sirka' => $sirka,
            'delka' => $delka,
            'sila' => $sila,
            'imgUrl' => $img_url,
            'edgeType' => $typ_hrany,
            'edgeId' => $edge['edgeId'],
            'edgeName' => $edge['edgeName'],
            'edgeImgUrl' => $edge['edgeImgUrl'],
            'edgDims' => $edge['edgeDims']
        ];
        
        if($diff_edge_id !== '0') {
            $diff_edge = (new \Inc\AJAX\Desky())->get_edge_props($diff_edge_id);
            $diff_edge_params =[
                'diffEdgeId' => $diff_edge['edgeId'],
                'diffEdgeName' => $diff_edge['edgeName'],
                'diffEdgeImgUrl' => $diff_edge['edgeImgUrl'],
                'diffEdgeDims' => $diff_edge['edgeDims']                
            ];
            $to_return = $to_return + $diff_edge_params;
        }
        
        $this->materials_props[$product_id] = $to_return;

        return $to_return;
    }
    
    private function render_empty_row(){
        $i = 'empty';

        echo '<tr deska-params=\'{"row_id":}\' row-id="" style="display: none;" id="empty-row">';
        echo '<td>' .$i .'</td>';
        echo '<td>'; echo $this->input->render('název', null, 'parts[' .$i .']'); echo '</td>';
        echo '<td>'; echo $this->mat_selector->render('', '', 'material_deska'); echo'</td>';
        echo '<td>'; echo $this->input->render('počet', null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->input->render('délka', null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->input->render('šířka', null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->checkbox->render('orient', true, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->mat_selector->render('', '', 'material_hrana'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('hrana dokola', [0 => '']); echo'</td>';
        echo '<td>'; echo $this->select_box->render('hrana predni', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('hrana zadni', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('hrana prava', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('hrana leva', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('tupl', null, null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('lepidlo', null, null, 'parts[' .$i .']'); echo'</td>';
        echo '<td></td>';
        echo '<td>';
            $this->button->render_button('duplikovat_radek', null); 
            $this->button->render_button('smazat_radek', null); 
        echo '</td>';        
        echo $this->input->render('deska_hidden', null, 'parts[' .$i .']');
        echo $this->input->render('hrana_type_hidden', null, 'parts[' .$i .']');
        echo $this->input->render('hrana_id_hidden', null, 'parts[' .$i .']');
        echo $this->input->render('fig_name_hidden', null, 'parts[' .$i .']');
        echo $this->input->render('fig_part_code_hidden', null, 'parts[' .$i .']');
        echo $this->input->render('fig_formula_hidden', null, 'parts[' .$i .']');        
        echo $this->input->render('params_hidden', null, 'parts[' .$i .']');
        echo '</tr>';        
    }
    
}
