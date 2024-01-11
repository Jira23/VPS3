<?php 
/**
 *  @package  narezovy-formular
 */

namespace Inc\Base;
use Inc\Base\BaseController;

class CustomPostTypeController extends BaseController {

    public function register(){
        add_shortcode('narezovy-formular-editor', array($this, 'render_editor_content'));
        add_shortcode('narezovy-formular-seznam', array($this, 'render_forms_list_content'));
        add_shortcode('narezovy-formular-register', array($this, 'render_register_user_content'));
        add_shortcode('narezovy-formular-import', array($this, 'render_import_content'));
    }

    public function render_editor_content() {
        
        
if(get_current_user_id() === 9259){
    (new \Inc\Pages\RowEditor\EditorFormHandler())->handle_edit_form();
    (new \Inc\Pages\RowEditor\RenderEditor)->render();
} else {
    (new \Inc\Pages\ClassicEditor\EditorFormHandler())->handle_edit_form();
    (new \Inc\Pages\ClassicEditor\RenderEditor)->render_edit_page();
}
    }

    public function render_forms_list_content(){
        (new \Inc\Pages\FormsListFormHandler())->handle_forms_list_form();
        (new \Inc\Pages\RenderFormsList())->render_forms_list();
    }
    
    public function render_register_user_content(){
        (new \Inc\Pages\RenderRegisterUser())->render_register_user();
    }    

    public function render_import_content(){
        (new \Inc\Pages\RenderImport())->render_import();
    }
    
    public function is_NF_page(){
        return strpos($_SERVER["REQUEST_URI"], 'narezovy-formular-') !== false ? true : false;
    }
    
    public function is_editor_page(){
        return strpos($_SERVER["REQUEST_URI"], 'narezovy-formular-editor') !== false ? true : false;
    }
    
}