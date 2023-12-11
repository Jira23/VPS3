<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\ClassicEditor;

use Inc\AJAX\AjaxUtils;
use Inc\Base\User;
use Inc\Pages\PagesController;
use Inc\Pages\OptResults;

class RenderEditor extends PagesController {
    
    public function __construct() {
        parent::__construct();
        
        if(!isset($_GET['form_id']) || !isset($_GET['part_id'])) exit;

        $this->form_id = (int)$_GET['form_id'];
        $this->part_id = (int)$_GET['part_id'];

        $this->parts = $this->get_parts();
        $this->form = $this->get_form();
        $this->current_part = $this->get_current_part();
        
        $this->max_unfinished_orders_reached = !current_user_can('administrator') && (new User())->count_opts() >= NF_MAX_UNFINISHED_ORDERS;    // check if user reached max. nubmer of optimalization withou placed order, not used with admin rights users
        
        $this->check_user();                                                    // check if user is allowed to be on this page
    }
    
    public function render_edit_page(){
        
        if(isset($this->form['odeslano']) && $this->form['odeslano'] == 1){     // render summary of closed order
            $this->render_order_summary();
        } else {                                                                // render editor form
            $this->render_header();
            $this->renderButtons();
            $this->render_form_upper_section();
            $this->render_part_upper_section();
            $this->render_part_lower_section();
            $this->render_figures();
            (new PartsList())->render_parts_list();
            (new OptResults($this->form_id))->render_opt_results();
            $this->render_form_lower_section();
            $this->renderButtons();
            $this->render_footer();        
        }
    }
    
    private function render_header(){
        echo '<form method="post" id="mainForm">' .PHP_EOL;
    }
    
    private function render_form_upper_section(){
        ?>
            <h3>Formulář</h3>
            <div style="text-align: left;">
                <div class="form-section">    
                    <?php 
                        $this->input->render_input('nazev', $this->form['nazev'] ?? null);
                        $this->tooltip->render_tooltip('nazev');
                    ?>
                </div>
                <div class="form-section">
                    <?php $this->select_box->render_select_box('olepeni', $this->form['olepeni'] ?? null) ?>
                </div>
                <div style="display: table;">
                    <div class="form-section" style="display: table-cell;">
                        <?php 
                            $this->select_box->render_select_box('stitky', $this->form['stitky'] ?? null);
                            $this->tooltip->render_tooltip('stitky');
                        ?>
                    </div>
                    <div class="form-section" style="display: table-cell;">
                        <?php 
                            $this->select_box->render_select_box('doprava', $this->form['doprava'] ?? null);
                            $this->tooltip->render_tooltip('doprava');
                        ?>
                    </div>        
                </div>
            </div>
            <hr>        
    <?php    
    }
    
    private function render_part_upper_section(){
    ?>        
        <div id="dil-horni-cast">
            <h2 id="form_top">Zadání dílu</h2>
            <h4>Parametry</h4>
            <div style="text-align: left;">
                <div style="display: flex;">
                    <div class="form-section" style="margin-bottom: 0px; display: inline-block; width: 50%;">
                    <?php 
                        $this->input->render_input('deska', $this->get_deska_name_by_id($this->current_part['lamino_id'] ?? NULL) ?? $this->get_last_deska_value('title'));
                        $this->tooltip->render_tooltip('deska');
                        $this->input->render_input('deska_hidden', $this->current_part['lamino_id'] ?? $this->get_last_deska_value());
                    ?>
                        
                    <div class="icon-lamino" id="icon-lamino">
                        <img src="<?php echo $this->get_deska_icon(); ?>">
                    </div>
                    <table id="deska-products-list" class="lamino-list" style="margin-bottom: 0px;">
                    </table>                    
                </div>
                <div style="display: inline-block; margin-left: 40px;  width: 50%;">
                    <div id="div_tree"></div>
                </div>
            </div>
            <div class="form-section">
                    <?php 
                        $this->select_box->render_select_box('hrana_select', $this->current_part['hrana'] ?? null);
                        $this->tooltip->render_tooltip('hrana_select');
                        $this->input->render_input('hrana_input', $this->get_hrana_name_by_id($this->current_part['hrana_id'] ?? NULL) ?? $this->get_last_hrana_value('title'));
                        $this->input->render_input('hrana_input_hidden', $this->current_part['hrana_id'] ?? $this->get_last_hrana_value());
                    ?>                
                <div class="icon-lamino" id="icon-hrana" <?php if(empty($this->current_part) || $this->current_part['hrana'] != '1') echo 'hidden';?>>
                    <img src="<?php echo $this->get_hrana_icon(); ?>">
                </div>            
            </div>
        </div>
        <table id="hrana-products-list" class="lamino-list" style="margin-bottom: 0px;">
        </table>                
        <div class="form-section">
            <?php 
                $this->select_box->render_select_box('lepidlo', $this->current_part['lepidlo'] ?? null);
                $this->tooltip->render_tooltip('lepidlo');
            ?>
        </div>
    </div>                  
    <hr>                
        <?php
    }    

    private function render_part_lower_section(){
        if(isset($this->current_part['lamino_id'])){                                                    // get maximum dimensions for current part
            $dimensions = (new \Inc\AJAX\Desky())->filterDeska($this->current_part['lamino_id']);
            $max_delka = $dimensions['delka'];
            $max_sirka = $dimensions['sirka'];
        }
    ?>
    <div id="dil-spodni-cast" style="text-align: left;">
        <div style="display: table;">
            <div class="form-section" style="display: inline-block; margin-bottom: 0px;">
                <?php 
                    $this->input->render_input('nazev_dilce', $this->current_part['nazev_dilce'] ?? null);
                    $this->tooltip->render_tooltip('nazev_dilce');
                ?>                
            </div>
            <div class="form-section" style="display: inline-block; margin-bottom: 0px; margin-right: 50px;">
                <?php $this->input->render_input('ks', $this->current_part['ks'] ?? null);?>                                
            </div>
            <div class="form-section" style="display: inline-block;">
                <?php 
                    $this->select_box->render_select_box('tupl', $this->current_part['tupl'] ?? null);
                    $this->tooltip->render_tooltip('tupl');
                ?>                                
            </div>        
        </div>
        <div style="margin-top: 20px;">
            <h4 style="color:red;">Uvádějte rozměry v "mm" včetně olepení (hrany)!!!</h4>
        </div>
        <div style="overflow-x: auto; overflow-y: auto;">
            <table class="editor-table">
                <tr>
                    <td></td>
                    <td style="text-align: center; padding-top: 0px;">
                        <div class="form-section" style="margin-top: 0px;">
                        <?php 
                            $this->input->render_input('delka_dilu', $this->current_part['delka_dilu'] ?? null, $max_delka ?? '');
                            $this->tooltip->render_tooltip('delka_dilu');
                        ?>                             
                        </div>
                        <div  id="hrana-horni-block-sb" class="form-section" style="margin-bottom: 0px;">
                        <?php 
                            $this->tooltip->render_tooltip('hrana_horni');
                            $this->select_box->render_select_box('hrana_horni');
                        ?>                            
                        </div> 
                        <div id="hrana-horni-block-icon" class="linear-loading-icon" hidden><img src="<?php echo $this->plugin_url; ?>assets/img/linear_loading_icon.gif"/></div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        <div id="hrana-leva-block-sb" class="form-section" style="margin-top: 110px;">
                            <?php $this->select_box->render_select_box('hrana_leva');?>
                        </div>
                        <div id="hrana-leva-block-icon" class="linear-loading-icon" hidden><img src="<?php echo $this->plugin_url; ?>assets/img/linear_loading_icon.gif"/></div>
                    </td>
                    <td style="text-align: center; width: 35%;">
                        <img src="<?php echo $this->plugin_url; ?>assets/img/drevo.png"/>    
                    </td>
                    <td>
                        <div class="form-section">
                        <?php 
                            $this->input->render_input('sirka_dilu', $this->current_part['sirka_dilu'] ?? null, $max_sirka ?? '');
                            $this->tooltip->render_tooltip('sirka_dilu');
                        ?>                             
                        </div>                        
                        <div id="hrana-prava-block-sb" class="form-section">
                            <?php $this->select_box->render_select_box('hrana_prava');?>
                        </div>
                        <div id="hrana-prava-block-icon" class="linear-loading-icon" hidden><img src="<?php echo $this->plugin_url; ?>assets/img/linear_loading_icon.gif"/></div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: center;">
                        <div id="hrana-dolni-block-sb" class="form-section" style="margin-top: 0px;">
                            <?php $this->select_box->render_select_box('hrana_dolni');?>
                        </div>
                        <div id="hrana-dolni-block-icon" class="linear-loading-icon" hidden><img src="<?php echo $this->plugin_url; ?>assets/img/linear_loading_icon.gif"/></div>
                    </td>
                    <td></td>
                </tr>
            </table>
            <div id="hrany-selectboxes-selected-values" hidden><?php echo json_encode([
                'select-hrana-horni' => $this->current_part['hrana_horni'] ?? null,
                'select-hrana-leva' => $this->current_part['hrana_leva'] ?? null,
                'select-hrana-prava' => $this->current_part['hrana_prava'] ?? null,
                'select-hrana-dolni' => $this->current_part['hrana_dolni'] ?? null
                 ]) ?></div>
            </div>

            <div class="form-section">    
                <?php $this->select_box->render_select_box('orientace', $this->current_part['orientace'] ?? null); ?>
            </div>
            <div class="form-section" style="display: inline-block; margin-bottom: 30px; margin-right: 50px;">
                <?php 
                    $this->input->render_input('fig_name', $this->current_part['fig_name'] ?? null);
                    $this->input->render_input('fig_part_code', $this->current_part['fig_part_code'] ?? null);
                    $this->input->render_input('fig_formula', $this->current_part['fig_formula'] ?? null);
                ?>
            </div>        
        
        </div>         
        <div style="display: flex; align-items: center;">    
            <?php 
                $this->button->render_button('ulozit_dil');
                $this->tooltip->render_tooltip('ulozit_dil');
            ?>
            <div id="save-alert" hidden>
                <div class="button button-alert">Díl není uložen!</div>
            </div>
            <div  id="hrana-alert" hidden>
                <div class="button button-alert">Rozměry na jednotlivých hranách se liší.</div>
            </div>
        </div>
    <?php
    }    
    
    private function render_figures(){
    ?>
    <h2 style="margin-top: 20px;">Figury 
        <span class="icon">
            <i class="show-icon fas fa-eye"></i>
            <i class="hide-icon fas fa-eye-slash" style="display: none;"></i>
        </span>
    </h2>
    
    <div class="toggle-vis" style="">
    <!--div class="toggle-vis" style="display: none;"-->
    <div id="figures-inputs-section">
        <?php
            $all_fig_ormulas = array_column(
                array_filter($this->parts, function($subarray) {
                    return $subarray->fig_formula !== '';
                }),
                'fig_formula'
            );
                
            $unique_fig_formulas = array_unique($all_fig_ormulas);
            
            foreach ($unique_fig_formulas as $formula) {
                echo '<div class="form-section figures-section"><input type="text" class="figure-input input-small" value="' .$formula .'"><span class="dashicons dashicons-trash figure-delete-button"></span></div>';
            }
        ?>
    </div>
    <span class="dashicons dashicons-plus-alt" id="figures-add-button"></span><br>
        <button type="button" id="apply-changes-button">Aplikovat změny</button>
    </div>


    <?php
    }
    
    private function render_form_lower_section(){
    ?>
        <hr>
        <div style="text-align: left; margin-top: 50px;">
            <div class="form-section">
                <?php $this->textarea->render_textarea('poznamka', $this->form['poznamka'] ?? null); ?>
            </div>
            <div class="form-section">    
                <?php $this->checkbox->render_checkbox('obchodni_podminky'); ?>
            </div>        
        </div>
    <?php
    }    
    
    private function render_footer(){
        echo '</form>' .PHP_EOL;
    }  
    
    private function renderButtons(){
        $user = new \Inc\Base\User();
        
        echo '<div style="text-align: left; margin-bottom: 30px;">';

        // buttons will be disabled if there are no records in db
        if($user->is_registered()) empty($this->parts) ? $this->button->render_button('ulozit', 'disabled') : $this->button->render_button('ulozit');
        
        $opt_results = (new OptResults($this->form_id))->opt_results;
        if(empty($opt_results)){
            $this->button->render_button('odeslat', NULL, ['style' => 'display: none;']);
            if(!empty($this->parts) && !$this->max_unfinished_orders_reached) $this->button->render_button('optimalizovat');
        } else {
            $this->button->render_button('odeslat');
            if(!empty($this->parts)) $this->button->render_button('optimalizovat', NULL, ['style' => 'display: none;']);
        }
            
        if($user->is_registered()) {
            echo '<a href="' .$this->forms_list_page .'">';
            $this->button->render_button('zpet_na_seznam'); 
            echo '</a>';            
        } else {
            $this->button->render_button('opustit');
        }
        if($this->max_unfinished_orders_reached && empty($opt_results)) $this->alert->render_alert('Max. počet optimalizovaných zakázek je 5!');    // show whem max. limit is reached and there is no opt. for this order
        if(empty($this->parts)) $this->alert->render_alert('Formulář je možné odeslat, pokud je uložen alespoň 1 díl.');
        echo '</div>'; 
    }

    private function render_order_summary(){
        echo '<div><a href="' .$this->forms_list_page .'#tabs-2">';
        $this->button->render_button('zpet_na_seznam');
        echo '</a></div>';
        (new \Inc\Output\Output())->render_customer_summary_html($this->form_id);
    }
    
    public function get_parts() {

        if(!isset($this->parts )){                                              // query will be executed only once per object init

            global $wpdb;
$parts = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY `id` DESC");
//$parts = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY fig_formula ASC, id DESC");

            $this->parts = $parts;
        }

        return $this->parts;        
    }

    private function get_form() {
        if($this->form_id == 0) return [];
        global $wpdb;
        $form = $wpdb->get_results("SELECT * FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$this->form_id ."'");
        return (empty($form)) ? array() : (array)$form[0];
    }
    
    public  function get_current_part(){
        if($this->part_id == 0) return array();
        
        global $wpdb;
        $current_part = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `id` LIKE '" .$this->part_id ."'");
        return (empty($current_part)) ? array() : (array)$current_part[0];
    }
    
    public function get_deska_name_by_id($deska_id){
        if($deska_id === NULL || $deska_id == 0) return NULL;
        
        $deska_post = get_post($deska_id);
        if($deska_post === NULL) return 'JIŽ NENÍ V PRODEJI';
        
        $deska_name = $deska_post->post_title;
        
        return $deska_name;
    }

    public function get_hrana_name_by_id($hrana_id, $include_dimensions = false){
        if($hrana_id == NULL || $hrana_id == 0) return NULL;
        $hrana = wc_get_product($hrana_id);
        $au = new AjaxUtils();
        $hrana_title = $au->shorten_hrana_title($hrana)['decor'];
        if($include_dimensions) $hrana_title .= ' ' .$au->shorten_hrana_title($hrana)['rozmer'];
        return $hrana_title;
    }
    
    public function get_last_deska_value($form = 'id'){
        if(!isset($this->parts[0])) return '';
        
        $last_deska_id = $this->parts[0]->lamino_id;
        if($form == 'id') return $last_deska_id;
        
        $last_deska_title = get_post($last_deska_id)->post_title;
        if($form == 'title') return $last_deska_title;
    }
    
    public function get_last_hrana_value($form = 'id'){
        if(!isset($this->parts[0])) return '';
        if($this->parts[0]->hrana_id == 0) return '';

        $last_hrana_id = $this->parts[0]->hrana_id;
        if($form == 'id') return $last_hrana_id;

        $last_hrana_title = $this->get_hrana_name_by_id($last_hrana_id);
        if($form == 'title') return $last_hrana_title;
    }
    
    private function get_deska_icon(){
        if($this->part_id != 0) {
            return wp_get_attachment_image_src( wc_get_product($this->current_part['lamino_id'])->get_image_id())[0]; 
        } else {
            if($this->get_last_deska_value() !== '') return wp_get_attachment_image_src(wc_get_product($this->get_last_deska_value())->get_image_id())[0];
        }
    }
    
    private function get_hrana_icon(){
        $hrana_id = $this->current_part['hrana_id'] ?? $this->get_last_hrana_value();
        if($hrana_id == '' || $hrana_id == '0') return;
        $image_id = wc_get_product($hrana_id)->get_image_id();
        if($image_id == '') return $this->plugin_url .'assets/img/no_img_icon.png';
        return wp_get_attachment_image_src($image_id)[0];
    }

    private function check_user(){                                              // check if user is allowed to be on this page
        $user = new User();
        if(!($user->is_registered() || $user->is_logged_with_cookies())) $this->jQuery_redirect($this->register_user_page);                             // redirect unknown user
        if(!$user->is_form_owner($this->form_id) && $this->form_id != 0) $this->jQuery_redirect($this->forms_list_page);                                // form owner/form exist check
        if($this->part_id != 0 && empty($this->current_part)) $this->jQuery_redirect(get_permalink() .'?form_id=' .$this->form_id .'&part_id=0');       // part exist check            
    }
    
}
