<?php

namespace Inc\Pages\RowEditor;

use Inc\Base\User;
use Inc\Base\EmailText;
use Inc\Base\BaseController;
use Inc\OrderHandler\OrderHandler;

class EditorFormHandler extends BaseController{

    public $query_params;
    public $form_data;
    public $part_data;
    
    public function __construct() {
        parent::__construct();
        $this->query_params = [];
        $this->form_data = [];                                              // data from "formular" part of edit page
        $this->part_data = [];                                              // data from "zadani dilu" part of edit page
        $this->sanitize_input();            
    }

    public function handle_edit_form(){
        if(isset($this->query_params['url_hash'])) $this->handle_hash($this->query_params['url_hash']);
        if(isset($_POST['btn_ulozit_zadani'])) $this->save_form();
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

    private function save_form(){
        global $wpdb;
        
        $redirect = false;
            
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
            $redirect = true;
        } else {
            $form_id = $this->query_params['form_id'];
        }
/*        
echo '<pre>';        
var_dump($_POST);
echo '</pre>';        
*/
      
        $wpdb->delete(NF_DILY_TABLE, array('form_id' => $form_id));

        $values = array();
        $place_holders = array();
        $query = "INSERT INTO " .NF_DILY_TABLE ." (form_id, orientace, nazev_dilce, ks, delka_dilu, sirka_dilu, tupl, hrana_dolni, hrana_horni, hrana_prava, hrana_leva, lepidlo, lamino_id, hrana, hrana_id, fig_name, fig_part_code, fig_formula, params) VALUES ";

        //var_dump($this->part_data);
        foreach ($this->part_data as $row) {
            $orientace_checkbox = isset($row['orientace']) && $row['orientace'] ? 1 : 0;
            $complete_data = ['form_id' => $form_id, 'orientace' => $orientace_checkbox] + $row;
            $place_holders[] = '(%d, %s, %s, %d, %d, %d, %s, %d, %d, %d, %d, %s, %d, %s, %d, %s, %s, %s, %s)';
            $values = array_merge($values, array_values($complete_data));
        }


        $query .= implode(', ', $place_holders);
//        var_dump($wpdb->prepare($query, $values));

        $a = $wpdb->query($wpdb->prepare($query, $values));
//        var_dump($a);
//        var_dump($wpdb->last_error);


        if($redirect) self::jQuery_redirect(get_permalink() .'?form_id=' .$form_id .'&part_id=0');
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

        if (isset($_POST['formular']) && is_array($_POST['formular'])) {        // not really necessary, at least setting $_POST to variable
            foreach ($_POST['formular'] as $key => $value) {
                $this->form_data[$key] = sanitize_text_field($value);
            }
        }

        if (isset($_POST['parts']) && is_array($_POST['parts'])) {
            $this->part_data = $_POST['parts'];
            unset($this->part_data['empty']);                                       // remove hidden row
            array_pop($this->part_data);                                      // remove empty row
            
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

