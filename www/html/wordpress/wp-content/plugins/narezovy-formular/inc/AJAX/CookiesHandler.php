<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

class CookiesHandler {

    public function set_user_cookies(){
        $to_insert = array();
        foreach ($_POST['formData'] as $input) {
            $to_insert[sanitize_text_field($input['name'])] = sanitize_text_field($input['value']);
        }

        foreach ($to_insert as $key => $value) {
            setcookie($key, $value, strtotime('+1day'), '/', $_SERVER['HTTP_HOST']);
        }        
    }
    
    public function unset_user_cookies(){
        (new \Inc\Base\User())->unset_cookies();
    }
}
