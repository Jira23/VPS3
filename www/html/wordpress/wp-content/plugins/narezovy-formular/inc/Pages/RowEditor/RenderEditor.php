<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\RowEditor;

use Inc\AJAX\AjaxUtils;
use Inc\Base\User;
use Inc\Pages\PagesController;
use Inc\Pages\OptResults;

class RenderEditor extends PagesController {
    
    public $form_id;
    public $part_id;
    public $parts;
    public $form;
    public $current_part;
    public $max_unfinished_orders_reached;
    
    
    public function __construct() {
        parent::__construct();
      
        if(!isset($_GET['form_id']) || !isset($_GET['part_id'])) exit;

        $this->form_id = (int)$_GET['form_id'];
        $this->part_id = (int)$_GET['part_id'];

        $this->parts = $this->get_parts();
        $this->form = $this->get_form();
        
        $user = new User();
        $this->max_unfinished_orders_reached = !$user->has_unlimited_opt() && count($user->get_opts()) >= NF_MAX_UNFINISHED_ORDERS;    // check if user reached max. nubmer of optimalization withou placed order, not used with admin rights users
      
        $this->check_user();                                                    // check if user is allowed to be on this page
        
        $this->init_tags();
    }
    
    private function init_tags(){
        $this->select_box = new Tags\SelectBox();
        $this->select_box_with_loading = new Tags\SelectBoxWithLoading();
        $this->checkbox = new Tags\CheckBox();
        $this->input = new Tags\Input();
        $this->button = new Tags\Button();        
        $this->radio = new Tags\Radio();
        $this->mat_selector = new Tags\MatSelector();        
    }
    
    public function render(){
        
        if(isset($this->form['odeslano']) && $this->form['odeslano'] == 1){     // render summary of closed order
            $this->render_order_summary();
        } else {                                                                // render editor form
            $this->render_header();
            $this->renderButtons();
            (new RenderParts())->render_parts($this->parts);
            
            $this->render_footer();
            $this->render_modals();
        }
    }
    
    private function render_header(){
        echo '<form method="post" id="mainForm">' .PHP_EOL;
    }
  
    
    private function render_footer(){
        echo '</form>' .PHP_EOL;
    }
    
    private function render_modals(){
        echo '<!-- MODALS -->';
        (new RenderMaterialSelectModal())->render();
    }
    
    private function renderButtons(){
        $user = new \Inc\Base\User();
        
        echo '<div style="text-align: left; margin-bottom: 30px;">';

        // buttons will be disabled if there are no records in db
        if($user->is_registered()) $this->button->render_button('ulozit');
        
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
        if($this->max_unfinished_orders_reached && empty($opt_results)) $this->alert->render_alert('Max. počet rozpracovaných optimalizací je 5!');    // show whem max. limit is reached and there is no opt. for this order
        echo '</div>'; 
    }
    
    private function render_order_summary(){
        if(isset($_GET['order_sent'])) {                                        // add thank you block for just sent order
            $this->render_thankyou();
        } else {
            echo '<div><a href="' .$this->forms_list_page .'#tabs-2">';
            $this->button->render_button('zpet_na_seznam');
            echo '</a></div>';
        }
        
        (new \Inc\Output\Output())->render_customer_summary_html($this->form_id);
    }

    private function render_thankyou(){
        echo '<div style="text-align: center; margin-bottom:100px;"><h1>Děkujeme. Váše objednávka  byla odeslána ke zpracování.</h1>';
        $user = new User();
        if($user->is_registered()) {
            echo '<a href="' .$this->forms_list_page .'"><button class="button" type="button">Zpět na seznam</button></a>';
        } else {
            echo '<a href="/"><button class="button" type="button">Zpět na hlavní stranu</button></a>';
            $user->unset_cookies();
        }
        echo '</div><hr>';
    }  
    
    public function get_parts() {

        if(!isset($this->parts )){                                              // query will be executed only once per object init

            global $wpdb;
$parts = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY `id` ASC");
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
