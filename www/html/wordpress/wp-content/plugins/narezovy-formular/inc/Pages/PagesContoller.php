<?php 
/**
 * @package  narezovy-formular
 */
namespace Inc\Pages;

use Inc\Base\BaseController;

class PagesContoller extends BaseController {
    public function __construct() {
        parent::__construct();
        
        // tags init
        $this->select_box = new Tags\SelectBox();
        $this->tooltip = new Tags\Tooltip();
        $this->input = new Tags\Input();
        $this->button = new Tags\Button();        
        $this->textarea = new Tags\Textarea();
        $this->checkbox = new Tags\CheckBox();
    }
    
    public function jQuery_redirect($url){
        echo "<script>window.location.href = '" .$url ."';</script>";
    }
}
