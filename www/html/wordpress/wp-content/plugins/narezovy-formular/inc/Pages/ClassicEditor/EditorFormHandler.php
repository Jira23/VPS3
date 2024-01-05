<?php

namespace Inc\Pages\ClassicEditor;

use Inc\Base\User;
use Inc\Base\EmailText;
use Inc\Base\BaseController;
use Inc\OrderHandler\OrderHandler;

class EditorFormHandler extends BaseController{

    public function __construct() {
        parent::__construct();
        $this->query_params = [];
        $this->form_data = [];                                              // data from "formular" part of edit page
        $this->part_data = [];                                              // data from "zadani dilu" part of edit page
        $this->sanitize_input();            
    }

    public function handle_edit_form(){
        if(isset($this->query_params['url_hash'])) $this->handle_hash($this->query_params['url_hash']);

        if(isset($_POST['btn_ulozit_dil'])) $this->save_form();
        if(isset($_POST['btn_ulozit_zadani'])) $this->save_form(false);
        if(isset($_POST['btn_duplikovat_dil'])) $this->duplicate_part();
        if(isset($_POST['btn_smazat_dil'])) $this->delete_part();
        if(isset($_POST['btn_delete_opt'])) $this->delete_opt();
        if(isset($_POST['btn_odeslat'])) $this->handle_order();
    }

    private function handle_order(){
        $form_id = $this->query_params['form_id'];
        $oh = new OrderHandler();

        if($oh->check_prices($form_id)){                                    // check if prices changed since last optimalization
            $oh->handle_order($form_id);
            self::jQuery_redirect(get_permalink() .'?form_id=' .$form_id .'&part_id=0&order_sent=1');
        } else {
            (new \Inc\Pages\Tags\InfoModal())->render('price_alert');
        }
    }

    private function delete_opt(){
        global $wpdb;
        $form_id = $this->query_params['form_id'];
        $wpdb->delete(NF_OPT_RESULTS_TABLE, array('form_id' => $form_id), array('%d'));            
    }

    private function delete_part(){
        global $wpdb;
        $part_id = (int)$_POST['btn_smazat_dil'];
        $wpdb->delete(NF_DILY_TABLE, array('id' => $part_id), array('%d'));
    }

    private function duplicate_part(){
        global $wpdb;
        $part_id = (int)$_POST['btn_duplikovat_dil'];
        $row_to_duplicate = $wpdb->get_row("SELECT * FROM " .NF_DILY_TABLE ." WHERE id = " .$part_id, ARRAY_A);
        if (!$row_to_duplicate) return;

        $new_row = $row_to_duplicate;
        unset($new_row['id']);
        $wpdb->insert(NF_DILY_TABLE, $new_row);
    }

    private function save_form($create_new_part = true){
        global $wpdb;

        if ($this->query_params['form_id'] == 0) {                                                              // first edit of form - it is not saved yet (not existing in db)
            $user = new User();
            $this->form_data['userId'] = $user->get_id();
            if($user->is_logged_with_cookies()) {
                $this->form_data['urlHash'] = urlencode(md5(time()));                       // gererates random url hash - unregistered user can edit his form using this hash in url
                $this->form_data['userContact'] = json_encode($user->get_cookies());        // add user address
                $this->send_hash_email();                                                   // sends email with link to form to unregistered user
            }
            $wpdb->insert(NF_FORMULARE_TABLE, $this->form_data);
            $form_id = $wpdb->insert_id;
        } else {
            $form_id = $this->query_params['form_id'];
            $wpdb->update(NF_FORMULARE_TABLE, $this->form_data, ['id' => $form_id]);
        }

        if ($this->query_params['part_id'] == 0){
            if($create_new_part) $wpdb->insert(NF_DILY_TABLE, array_merge(['form_id' => $form_id], $this->part_data));
        } else {
            $wpdb->update(NF_DILY_TABLE, array_merge(['form_id' => $form_id], $this->part_data), ['id' => $this->query_params['part_id']]);
        }

        self::jQuery_redirect(get_permalink() .'?form_id=' .$form_id .'&part_id=0#form_top');
    }

    private function handle_hash($url_hash){                                // unregistered user has unique url to his order form
        global $wpdb;

        $form_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM " .NF_FORMULARE_TABLE ." WHERE urlHash = %s", $url_hash));
        if(empty($form_data) || $form_data[0]->userContact == '') (new \Inc\Pages\Tags\InfoModal())->render('hash_alert');

        // set cookies
        $to_cookies = json_decode($form_data[0]->userContact);
        foreach ($to_cookies as $key => $value) {
            setcookie($key, $value, strtotime('+1day'), '/', $_SERVER['HTTP_HOST']);
        }

        self::jQuery_redirect(get_permalink() .'?form_id=' .$form_data[0]->id .'&part_id=0');
    }

    private static function jQuery_redirect($url){
        echo "<script>window.location.href = '" .$url ."';</script>";
    }

    private function sanitize_input(){

        if(isset($_GET['unreg_id'])) {
            $this->query_params['url_hash'] = sanitize_text_field($_GET['unreg_id']);
        }

        if((isset($_GET['form_id']) && isset($_GET['part_id']))) {
            $this->query_params['form_id'] = (int)($_GET['form_id']);
            $this->query_params['part_id'] = (int)($_GET['part_id']);
        } else {
            return;
        }

        if (isset($_POST['formular']) && is_array($_POST['formular'])) {    // not really necessary, at least setting $_POST to variable
            foreach ($_POST['formular'] as $key => $value) {
                $this->form_data[$key] = sanitize_text_field($value);
            }
        }

        if (isset($_POST['dil']) && is_array($_POST['dil'])) {              // not really necessary, at least setting $_POST to variable
            foreach ($_POST['dil'] as $key => $value) {
                $this->part_data[$key] = sanitize_text_field($value);
            }
        }
    }
    
    public function send_hash_email(){                                                      // sends email with link to form to unregistered user
        $hash_url = $this->editor_page .'/?unreg_id=' .$this->form_data['urlHash'];
        
        $to = (new User())->get_contact()['email'];
        $subject = 'Nářezový formlulář - odkaz';
        $message = (new EmailText())->hash_email($hash_url);
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        wp_mail($to, $subject, $message, $headers);        
    }
}

