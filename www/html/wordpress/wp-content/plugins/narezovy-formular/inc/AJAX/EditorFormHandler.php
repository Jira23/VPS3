<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

class EditorFormHandler {
    
    public $query_params;
    public $form_data;
    public $part_data;
    
    
    public function __construct() {
        $this->query_params = [];
        $this->form_data = [];                                              // data from "formular" part of edit page
        $this->part_data = [];                                              // data from "zadani dilu" part of edit page
        $this->sanitize_input();            
    }    
    
    
    public function handle_editor_form() {
        
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         
        
        //var_dump($_POST);
        
        //(new \Inc\Pages\RowEditor\EditorFormHandler())->handle_edit_form();
        $this->save_form();

        wp_die();
    }
    
    private function save_form(){
        global $wpdb;
        $redirect = false;
/*
echo '<pre>';        
var_dump($_POST);
echo '</pre>';                
*/

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
            $wpdb->update(NF_FORMULARE_TABLE, $this->form_data, ['id' => $form_id]);
        }
      
        $wpdb->delete(NF_DILY_TABLE, array('form_id' => $form_id));

        $values = array();
        $place_holders = array();
        $query = "INSERT INTO " .NF_DILY_TABLE ." (form_id, orientace, nazev_dilce, ks, delka_dilu, sirka_dilu, tupl, hrana_dolni, hrana_horni, hrana_prava, hrana_leva, lamino_id, hrana, hrana_id, fig_name, fig_part_code, fig_formula, group_number, params) VALUES ";

        //var_dump($this->part_data);
        foreach ($this->part_data as $row) {
            $orientace_checkbox = isset($row['orientace']) && $row['orientace'] ? 1 : 0;
            $complete_data = ['form_id' => $form_id, 'orientace' => $orientace_checkbox] + $row;
            $place_holders[] = '(%d, %s, %s, %d, %d, %d, %s, %d, %d, %d, %d, %d, %s, %d, %s, %s, %s, %d, %s)';
            $values = array_merge($values, array_values($complete_data));
        }


        $query .= implode(', ', $place_holders);
//        var_dump($wpdb->prepare($query, $values));

        $a = $wpdb->query($wpdb->prepare($query, $values));
        var_dump($a);
        var_dump($wpdb->last_error);


        if($redirect) self::jQuery_redirect(get_permalink() .'?form_id=' .$form_id .'&part_id=0');
    }    
    
    private function sanitize_input(){


        $this->query_params['form_id'] = (int)($_POST['form_id']);


        if (isset($_POST['formular']) && is_array($_POST['formular'])) {        // not really necessary, at least setting $_POST to variable
            foreach ($_POST['formular'] as $key => $value) {
                $this->form_data[$key] = sanitize_text_field($value);
            }
        }

        if (isset($_POST['parts']) && is_array($_POST['parts'])) {
            $this->part_data = $_POST['parts'];
            unset($this->part_data['empty']);                                   // remove hidden row
        }
    }    

    
}
