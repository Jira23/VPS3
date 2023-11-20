<?php

namespace Inc\Pages;

use Inc\Base\BaseController;

class RegisterUserFormHandler extends PagesContoller{

    public function __construct() {
        parent::__construct();
    }
    
    public function register(){
        add_action('init', array($this, 'handle_register_user_form'));
    } 

    public function handle_register_user_form(){
        
        if(isset($_POST['btn_odeslat_registraci'])){
            
            $to_insert = array();
            foreach ($_POST as $key => $value) {
                $to_insert[$key] = sanitize_text_field($value);
            }
            
            foreach ($to_insert as $key => $value) {
                setcookie($key, $value, strtotime('+1 day'), '/', $_SERVER['HTTP_HOST']);
            }
        }
    }
}

