<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\RowEditor;

use Inc\Pages\PagesController;
use Inc\AJAX\AjaxUtils;


class RenderParts extends RenderEditor{

    public $materials_props;
    public $count;
    
    public function render_parts($parts){
        $this->render_table_header();
        //$this->render_table_head();
        $this->render_table_body($parts);
        $this->render_table_footer();

        $this->materials_props = [];
    }
    
    private function render_table_header(){
    ?>
        <div class="parts-table-container">
            <h3>Rozpis dílů</h3>
            <table class="parts-table">
    <?php
    }
  
    private function render_subheader($visible = true){
        $titles = ['č.', 'název', 'ks', 'délka', 'šířka', 'orient.', 'materiál<br>hrany', 'tupl', 'hrana<br>dokola', 'hrana<br>přední', 'hrana<br>zadní', 'hrana<br>pravá', 'hrana<br>levá&nbsp;&nbsp;', 'figura', 'úpravy'];
        echo $visible ? '  <tr class="NF-material-group-subheader">' : '  <tr class="NF-material-group-subheader" style="display:none;" id="material-group-subheader-hidden">';
        foreach ($titles as $title) {
            echo '<td>' .$title;
            if($title == 'hrana<br>dokola') echo '<div class="info-icon-wrapper"><span class="dashicons dashicons-info NF-info-icon" style="display: inline;"><span class="tooltip-text">Vloží vybraný rozměr do všech čtyř hran.</span></span></div>';
            if($title == 'hrana<br>přední') echo '<div class="info-icon-wrapper"><span class="dashicons dashicons-info NF-info-icon" style="display: inline;"><img src="'.$this->plugin_url .'assets/img/hrany_predni.png' .'" class="edge-select-image"></span></div>';
            if($title == 'hrana<br>zadní') echo '<div class="info-icon-wrapper"><span class="dashicons dashicons-info NF-info-icon" style="display: inline;"><img src="'.$this->plugin_url .'assets/img/hrany_zadni.png' .'" class="edge-select-image"></span></div>';
            if($title == 'hrana<br>pravá') echo '<div class="info-icon-wrapper"><span class="dashicons dashicons-info NF-info-icon" style="display: inline;"><img src="'.$this->plugin_url .'assets/img/hrany_prava.png' .'" class="edge-select-image"></span></div>';
            if($title == 'hrana<br>levá&nbsp;&nbsp;') echo '<div class="info-icon-wrapper"><span class="dashicons dashicons-info NF-info-icon" style="display: inline;"><img src="'.$this->plugin_url .'assets/img/hrany_leva.png' .'" class="edge-select-image"></span></div>';
            echo '</td>';
        }
        echo '</tr>';
    }
    
    private function group_parts($parts){
        $outputArray = [];

        foreach ($parts as $object) {
            $lamino_id = $object->lamino_id;
            if (!isset($outputArray[$lamino_id])) $outputArray[$lamino_id] = [];
            $outputArray[$lamino_id][] = $object;
        }

        return $outputArray;         
    }
    
    private function render_group_row($group){
        
        echo $group ?  '<tr class="NF-edit-group-material">':'<tr class="NF-edit-group-material-empty" style="display: none;" >' ;
        echo '  <td colspan="8">';
        $mat_params = '{}';
        
            if($group){
                $part = $group[0];
                                
                $name = $this->get_deska_name_by_id($part->lamino_id);
                $img_url = $this->get_deska_icon($part->lamino_id);        
                $deska_params = $part->params == '' ? $this->set_deska_params($part->lamino_id, $name, $img_url, $part->hrana, $part->hrana_id, '') : json_decode(stripslashes($part->params), true);
                $mat_params =  array_intersect_key($deska_params, array_flip(['id', 'name', 'sku', 'sirka', 'delka', 'sila', 'isPDK', 'categoryIds', 'imgUrl', 'edgeId', 'edgeName', 'edgeImgUrl', 'edgeDims']));
                echo '<div class="group-material-info" id="' .$mat_params['id'] .'">';
                echo '  <div id="group-material-icon"><img src="' .$mat_params['imgUrl'] .'"></div>';                
                echo '  <h5 id="group-material-nazev">' .$mat_params['name'] .'</h5>';
            } else {
                echo '<div class="group-material-info">';            
                echo '  <div id="group-material-icon"><img src=""></div>';                
                echo '  <h5 id="group-material-nazev"></h5>';                            
            }

            echo '<div>';
            echo '<input id="mat-group-data" value="' .htmlspecialchars(json_encode($mat_params), ENT_QUOTES, 'UTF-8') .'" style="display: none;"/>';
        echo '  </td>';
        echo '  <td colspan="6">';
        echo '<div class="figure-input-wrapper">';
        echo '<h5>Vzorec pro figuru: </h5><input type="text" class="parts-table-input-figure" value="'  .$this->get_group_formula($group) .'">'; 
        echo '<div class="info-icon-wrapper"><a href="' .$this->plugin_url .'assets/pdf/figury_navod_DOD.pdf" target="_blank" class="dashicons-link"><span class="dashicons dashicons-info NF-info-icon" style="display: inline;"></span></a></div>';
        echo '</div>';
        echo '<div class="figure-alerts">';
        $this->alert->render_alert('Ve vzorci jsou neexistující díly!', 'error', true, 'alert-fig-numbers-check');
        $this->alert->render_alert('Vzorec obsahuje nepovolené znaky!', 'error', true, 'alert-fig-syntax-check');
        echo '</div></td>';
        echo '<td><button name="btn_smazat_material" class="button button-sm" type="button" title="smazat materiál"><span class="dashicons dashicons-trash"></span></button></td>';
        
        echo '</tr>';
        
    }
    
    private function get_group_formula($group){
        if(!$group) return false;
        foreach ($group as $part) {
             if($part->fig_formula !== '') return $part->fig_formula;
        }        
        return false;
    }
    
    private function render_add_material(){
        echo '<div class="NF-new-group-mat-button" id="add-group-material">'; 
//echo '<div class="button-content">';
        echo '    <div class="mat-icon">'; 
        echo '        <img src="'.$this->plugin_url .'assets/img/icon_plus.png' .'">';
        echo '    </div>';
        echo '        <h1><b>Přidat nový materiál</b></h1>';        
        echo '</div>';
//echo '    </div>';        
    }
    
    private function render_table_body($parts){
    ?>
        <tbody>
            <?php
                $grouped_parts = $this->group_parts($parts);    
                
                $i = 1;
                foreach ($grouped_parts as $group) {

                    $group_row_id = 1;
                    
                    $this->render_group_row($group);
                    $this->render_subheader();
                    
                    foreach ($group as $part) {

                        $name = $this->get_deska_name_by_id($part->lamino_id);
                        $img_url = $this->get_deska_icon($part->lamino_id);

                        $deska_params = $part->params == '' ? $this->set_deska_params($part->lamino_id, $name, $img_url, $part->hrana, $part->hrana_id, $i) : json_decode(stripslashes($part->params), true);
                        //var_dump($deska_params);
                        echo '<tr row-id="' .$i .'">';
                        echo '<td>'; echo $part->group_number == '0' ? $group_row_id : $part->group_number; echo '</td>';
                        echo '<td>'; echo $this->input->render('název', $part->nazev_dilce, 'parts[' .$i .']'); echo '</td>';
                        echo '<td>'; echo $this->input_with_warning->render('počet', $part->ks, 'parts[' .$i .']'); echo'</td>';
                        echo '<td>'; echo $this->input_with_warning->render('délka', $part->delka_dilu, 'parts[' .$i .']', $deska_params['delka']); echo'</td>';
                        echo '<td>'; echo $this->input_with_warning->render('šířka', $part->sirka_dilu, 'parts[' .$i .']', $deska_params['sirka']); echo'</td>';
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

                        echo '<td>'; 
                        if($deska_params['isPDK']){
                            echo $this->select_box->render('tupl', NULL, $part->tupl, 'parts[' .$i .']', true); 
                        } else {
                            echo $this->select_box->render('tupl', NULL, $part->tupl, 'parts[' .$i .']'); 
                        }
                        echo'</td>';


                        if($part->hrana === '-1'){
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana dokola', [0 => ''], null, null, true); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana predni', [0 => ''], null, 'parts[' .$i .']', true); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana zadni', [0 => ''], null, 'parts[' .$i .']', true); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana prava', [0 => ''], null, 'parts[' .$i .']', true); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana leva', [0 => ''], null, 'parts[' .$i .']', true); echo'</td>';
                        }
/*
    echo '<pre>';                            
    var_dump($deska_params);                            
    echo '</pre>';                            
*/
                        
                        if($part->hrana === '0'){

                            $options = ['0' => ''] + $deska_params['edgeDims'];
                            $select = $part->hrana_dolni === '0' ? '' : $part->hrana_dolni;

                            echo '<td>'; echo $this->select_box_with_loading->render('hrana dokola', $options); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana predni', $options, $part->hrana_dolni === '0' ? 0 : $part->hrana_dolni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana zadni', $options, $part->hrana_horni === '0' ? 0 : $part->hrana_horni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana prava', $options, $part->hrana_prava === '0' ? 0 : $part->hrana_prava, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana leva', $options, $part->hrana_leva === '0' ? 0 : $part->hrana_leva, 'parts[' .$i .']'); echo'</td>';
                        }

                        if($part->hrana === '1'){
                                

    
                            $options = ['0' => ''] + $deska_params['diffEdgeDims'];
                            //$select = $part->hrana_dolni === '0' ? '' : $part->hrana_dolni;
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana dokola', $options); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana predni', $options, $part->hrana_dolni === '0' ? '' : $part->hrana_dolni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana zadni', $options, $part->hrana_horni === '0' ? '' : $part->hrana_horni, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana prava', $options, $part->hrana_prava === '0' ? '' : $part->hrana_prava, 'parts[' .$i .']'); echo'</td>';
                            echo '<td>'; echo $this->select_box_with_loading->render('hrana leva', $options, $part->hrana_leva === '0' ? '' : $part->hrana_leva, 'parts[' .$i .']'); echo'</td>';
                        }

//                        echo '<td>'; echo $this->select_box->render('lepidlo', NULL, $part->lepidlo, 'parts[' .$i .']'); echo'</td>';

                        echo '<td class="fig-formula-visible">'; echo $part->fig_formula; echo '</td>';
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
                        echo $this->input->render('group_number_hidden', $part->group_number == 0 ? $group_row_id : $part->group_number, 'parts[' .$i .']');
                        echo $this->input->render('params_hidden', htmlspecialchars(json_encode($deska_params), ENT_QUOTES, 'UTF-8'), 'parts[' .$i .']');
                        echo '</tr>';
                        $group_row_id++;
                        $i++;
                    }
                    
                }
                
            ?>
        </tbody>
    <?php
    echo '<tfoot>';
    $this->render_group_row(false);
    $this->render_subheader(false);
    $this->render_empty_row();
    echo '</tfoot>';
    }
    
    private function render_table_footer(){
    ?>
            </table>
            <?php $this->render_add_material(); ?>
            <div class="parts-table-overlay" <?php if(!$this->has_opt_results()) echo 'style="display: none;"'; ?>>
                <?php $this->button->render_button('smazat_opt'); ?>
            </div>            
        </div>
    <?php
    }
    
    private function has_opt_results(){
        global $wpdb;
        $opt_results = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' LIMIT 1");
        return (!empty($opt_results)) ? true : false;
    }    
    
    public function get_deska_name_by_id($deska_id){
        if($deska_id === NULL || $deska_id == 0) return NULL;
        
        $deska_post = get_post($deska_id);
        if($deska_post === NULL) return 'JIŽ NENÍ V PRODEJI';
        
        $deska_name = $deska_post->post_title;
        
        return $deska_name;
    }
    
    public function get_hrana_name_by_id($part, $include_dimensions = false){

        $hrana_id_array = array_unique([$part->hrana_horni, $part->hrana_dolni, $part->hrana_prava, $part->hrana_leva]);
        
        if(isset($hrana_id_array[1]) && $hrana_id_array[0] === '0') {
            $hrana_id = $hrana_id_array[1];
        } else {
            $hrana_id = $hrana_id_array[0];
        }

        if($hrana_id == NULL || $hrana_id == 0) return NULL;
        $hrana = wc_get_product($hrana_id);
        $au = new AjaxUtils();
        $hrana_title = $au->shorten_hrana_title($hrana)['decor'];
        if($include_dimensions) $hrana_title .= ' ' .$au->shorten_hrana_title($hrana)['rozmer'];
        return $hrana_title;
    }    

    private function get_deska_icon($deska_id){
        if($deska_id == '' || $deska_id == '0') return;
        $image_id = wc_get_product($deska_id)->get_image_id();
        if($image_id == '') return '';
        return wp_get_attachment_image_src($image_id)[0]; 
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
//var_dump($this->count);
        
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
            'isPDK' => $edge['isPDK'],
            'imgUrl' => $img_url,
            'edgeType' => $typ_hrany,
            'edgeId' => $edge['edgeId'],
            'edgeName' => $edge['edgeName'],
            'edgeImgUrl' => $edge['edgeImgUrl'],
            'edgeDims' => $edge['edgeDims']
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

        echo '<tr row-id="" style="display: none;" id="empty-row">';
        echo '<td></td>';
        echo '<td>'; echo $this->input->render('název', null, 'parts[' .$i .']'); echo '</td>';
        echo '<td>'; echo $this->input_with_warning->render('počet', null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->input_with_warning->render('délka', null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->input_with_warning->render('šířka', null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->checkbox->render('orient', true, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->mat_selector->render('', '', 'material_hrana'); echo'</td>';
        echo '<td>'; echo $this->select_box->render('tupl', null, null, 'parts[' .$i .']'); echo'</td>';        
        echo '<td>'; echo $this->select_box_with_loading->render('hrana dokola', [0 => '']); echo'</td>';
        echo '<td>'; echo $this->select_box_with_loading->render('hrana predni', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box_with_loading->render('hrana zadni', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box_with_loading->render('hrana prava', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
        echo '<td>'; echo $this->select_box_with_loading->render('hrana leva', [0 => ''], null, 'parts[' .$i .']'); echo'</td>';
//        echo '<td>'; echo $this->select_box->render('lepidlo', null, null, 'parts[' .$i .']'); echo'</td>';
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
        echo $this->input->render('group_number_hidden', null, 'parts[' .$i .']');
        echo $this->input->render('params_hidden', null, 'parts[' .$i .']');
        echo '</tr>';        
    }
    
}
