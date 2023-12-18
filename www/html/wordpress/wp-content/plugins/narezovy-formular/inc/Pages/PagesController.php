<?php 
/**
 * @package  narezovy-formular
 */
namespace Inc\Pages;

use Inc\Base\BaseController;

class PagesController extends BaseController {
    
    public $select_box;
    public $tooltip;
    public $input;
    public $button;
    public $textarea;
    public $checkbox;
    public $alert;
    
    public function __construct() {
        parent::__construct();
        
        // tags init
        $this->select_box = new Tags\SelectBox();
        $this->checkbox = new Tags\CheckBox();
        $this->input = new Tags\Input();
        $this->button = new Tags\Button();        
//$this->select_box = new Tags\RowEditor\SelectBox();        
//$this->checkbox = new Tags\RowEditor\CheckBox();
//$this->input = new Tags\RowEditor\Input();
//$this->button = new Tags\RowEditor\Button();        
//$this->mat_selector = new Tags\RowEditor\MatSelector();
        
        $this->tooltip = new Tags\Tooltip();
        $this->textarea = new Tags\Textarea();
        $this->alert = new Tags\Alert();

        add_action('wp_loaded', array($this, 'adapt_NF_pages'));        
        add_action('wp_loaded', array($this, 'show_nf_errors'));        
    }
    
    public function jQuery_redirect($url){
        echo "<script>window.location.href = '" .$url ."';</script>";
    }
    
    public function adapt_NF_pages(){
        $cptc = new \Inc\Base\CustomPostTypeController();
        
        if($cptc->is_NF_page()) {
            unregister_sidebar('sidebar');
        }
    }
    
    public function show_nf_errors(){
        $cptc = new \Inc\Base\CustomPostTypeController();
        if($cptc->is_NF_page() && SHOW_NF_ERRORS) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL & ~E_DEPRECATED); 
            //error_reporting(E_ALL); 
        }
    }
    
}
