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
    
    public function is_nf_admin(){
        return in_array($this->get_id(), NF_ADMIN_USERS) ? true : false;
    }    
    
    public function get_cookies(){

        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            if(in_array(sanitize_text_field($key), self::USER_COOKIES)) {
                $cookies[$key] = sanitize_text_field($value);
            }
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
            $cookies = $this->get_cookies();
            foreach ($cookies as $key => $value) {                              // change cookies to address fields
                $contact[str_replace('nf_', '', $key)] = $value;
            }
        } else {
            $contact['jmeno'] = get_user_meta($user_id, 'first_name', true);
            $contact['prijmeni'] = get_user_meta($user_id, 'last_name', true);
            $contact['email'] = get_userdata($user_id)->data->user_email;
            $contact['telefon'] = get_user_meta($user_id, 'billing_phone', true);
            $contact['ulice'] = get_user_meta($user_id, 'billing_address_1', true);
            $contact['mesto'] = get_user_meta($user_id, 'billing_postcode', true) .', ' .get_user_meta($user_id, 'billing_city', true);; 
            $contact['ICO'] = get_user_meta($user_id, 'billing_ic', true);;
            
        }
        
        return $contact;
    }    
    
    public function unset_cookies(){
        foreach (self::USER_COOKIES as $cookie_name) {
            if (isset($_COOKIE[$cookie_name])) {
                unset($_COOKIE[$cookie_name]); 
                setcookie($cookie_name, '', -1, '/'); 
            }
        }
    }
    
    public function has_unlimited_opt(){                                        // has user no limit for optimalizatons
        return in_array($this->get_id(), NF_UNLIMITED_OPT_USERS) ? true : false;
    }
    
    // get ids of users optimalized orders
    public function get_opt_orders_ids(){
        global $wpdb;
        $query = $wpdb->prepare("
            SELECT r.form_id
            FROM " .NF_OPT_RESULTS_TABLE ." AS r
            INNER JOIN " .NF_FORMULARE_TABLE ." AS f ON r.form_id = f.id
            WHERE f.userId = %s"
            , $this->get_id()
        );
        
        $results = $wpdb->get_results($query);        
        
        $ids_array = [];
        foreach ($results as $result) {
            $ids_array[] = $result->form_id;
        }        
        
        return array_unique($ids_array);
    }
    
    // get ids of users optimalized and processed (sended) orders
    public function get_opt_processed_orders_ids(){
        global $wpdb;
        $query = $wpdb->prepare("
            SELECT r.form_id
            FROM " . NF_OPT_RESULTS_TABLE . " AS r
            INNER JOIN " . NF_FORMULARE_TABLE . " AS f ON r.form_id = f.id
            WHERE f.userId = %s AND f.odeslano LIKE 1",
            $this->get_id()
        );
        
        $results = $wpdb->get_results($query);        
        
        $ids_array = [];
        foreach ($results as $result) {
            $ids_array[] = $result->form_id;
        }        
        
        return array_unique($ids_array);
    }
    
    // counts how many times user did optimalization without submt new order
    public function update_opts_limit($form_id){
        $user_id = $this->get_id();
        if($this->is_logged_with_cookies()) return;                                     // no metadata stored for unregistered user
        
        $opts_limit = $this->get_opts();
        $new_opts_limit = array_merge($opts_limit, [$form_id]);
        $to_meta = array_unique($new_opts_limit);
        update_user_meta($user_id, 'nf_optimizations_count', $to_meta);
   }
    
    public function reset_opts_limit(){
            $user_id = $this->get_id();    
            update_user_meta($user_id, 'nf_optimizations_count', []);
    }
    
    public function get_opts() {
        $user_id = $this->get_id();
        if(!$user_id) return [];                                                        // no metadata stored for not existing user
        if($this->is_logged_with_cookies()) return [];                                  // no metadata stored for unregistered user

        $user_meta = get_user_meta($user_id, 'nf_optimizations_count', true);
        if($user_meta == '') {
            update_user_meta($user_id, 'nf_optimizations_count', []);
            $user_meta = get_user_meta($user_id, 'nf_optimizations_count', true);
        }
        return $user_meta;
    }
    
    public function get_sanitized_user_name(){
        $user_contact = $this->get_contact();
        $sanitized_user_name = sanitize_file_name($user_contact['jmeno'] .'_' .$user_contact['prijmeni']);
        return $sanitized_user_name;
    }    
    
}
