<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Base;

class User {
    
    const USER_COOKIES = ['nf_jmeno', 'nf_prijmeni', 'nf_email', 'nf_telefon', 'nf_ulice', 'nf_mesto'];         // cookies to store unregistered user data. Must correspond with inputs on registration page. Unregistered users are allowed to place only one order and dont have acces to store forms. Cookies are the way how to identify unregistered users and their orders.
    
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
    
    public function is_logged_with_cookies(){
        return strpos($this->get_id(), '@') ? true : false;
    }
    
    public function is_form_owner($form_id){
        global $wpdb;
        $prepared_statement = $wpdb->prepare("SELECT GROUP_CONCAT(`id`) FROM " . NF_FORMULARE_TABLE . " WHERE userId LIKE %s", $this->get_id());
        $results = $wpdb->get_var($prepared_statement);
        if(!isset($results)) return false;
        
        $user_forms = explode(',', $results);
        return in_array($form_id, $user_forms) ? true : false;
    }
    
    public function get_cookies(){

        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            $cookies[$key] = sanitize_text_field($value);
        }
        
        if(empty(array_diff(self::USER_COOKIES, array_keys($cookies)))){
            return $cookies;
        } else {
            return false;
        }
    }
    
    public function get_contact() {
        $user_id = $this->get_id();

        if(strpos($user_id, '@')){                                              // unregistered user email address as id
            return $this->get_cookies();
        } else {
            $contact['jmeno'] = get_user_meta($user_id, 'first_name', true);
            $contact['prijmeni'] = get_user_meta($user_id, 'last_name', true);
            $contact['email'] = get_userdata($user_id)->data->user_email;
            $contact['telefon'] = get_user_meta($user_id, 'billing_phone', true);
            $contact['ulice'] = get_user_meta($user_id, 'billing_address_1', true);
            $contact['mesto'] = get_user_meta($user_id, 'billing_postcode', true) .', ' .get_user_meta($user_id, 'billing_city', true);; 
            $contact['ICO'] = get_user_meta($user_id, 'billing_ic', true);;
            return $contact;
        }
    }    
    
    public function unset_cookies(){
        foreach (self::USER_COOKIES as $cookie_name) {
            if (isset($_COOKIE[$cookie_name])) {
                unset($_COOKIE[$cookie_name]); 
                setcookie($cookie_name, '', -1, '/'); 
            }
        }
    }
    
    public function count_opts(){                                               // counts unfinished orders wich are not
        $user_id = $this->get_id();
        global $wpdb;
        $query = $wpdb->prepare("SELECT `id` FROM " .NF_FORMULARE_TABLE ." WHERE userId = %d AND `odeslano` LIKE '0'",$user_id);
        $orders_to_place = $wpdb->get_results($query);
        $opt_orders = 0;
        foreach ($orders_to_place as $form_id) {
            $query = $wpdb->prepare("SELECT COUNT(*) FROM " .NF_OPT_RESULTS_TABLE ." WHERE form_id = %d", $form_id->id);
            $result = $wpdb->get_var($query);
            if ($result > 0) $opt_orders++;
        }
        
        return $opt_orders;
    }
    
}
