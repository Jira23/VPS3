<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Base;

class User {
    
    // returns user id. If user is not registered, returns email from cookies as its id
    public function get_id(){  
        if(get_current_user_id() != 0) {
            return get_current_user_id();                                   
        } else {
            if(isset($this->get_cookies()['nf_email'])){
                return $this->get_cookies()['nf_email'];
            } else{
                return false;
            }
        }
    }

    public function is_registered(){        
        $user_id = $this->get_id();
        return is_int($user_id) ? true : false;
    }
    
    public function is_form_owner($form_id){
        global $wpdb;
        $prepared_statement = $wpdb->prepare("SELECT GROUP_CONCAT(`id`) FROM " . NF_FORMULARE_TABLE . " WHERE userId LIKE %s", $this->get_id());
        $results = $wpdb->get_var($prepared_statement);
        if(!isset($results)) return false;
        
        $user_forms = explode(',', $results);
        return in_array($form_id, $user_forms) ? true : false;
    }
    
        // nacte cookies data - funkce pro neregistrovaneho uzivatele
        public function get_cookies(){
            
            foreach ($_COOKIE as $key => $value) {
                $cookies[$key] = sanitize_text_field($value);
            }
            if(isset($cookies['jmeno']) && isset($cookies['prijmeni']) && isset($cookies['nf_email']) && isset($cookies['telefon']) && isset($cookies['ulice']) && isset($cookies['mesto'])){
                return $cookies;
            } else {
                return false;
            }
        }    
}
