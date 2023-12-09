<?php

namespace Inc\Pages\ClassicEditor;

use Inc\Base\BaseController;
use Inc\Base\User;
use Inc\Pages\OrderHandler\OrderHandler;


    class EditorFormHandler extends BaseController{

        public function __construct() {
            parent::__construct();
            $this->query_params = [];
            $this->form_data = [];                                              // data from "formular" part of edit page
            $this->part_data = [];                                              // data from "zadani dilu" part of edit page
            $this->sanitize_input();            
        }

        public function handle_edit_form(){
            if(isset($_POST['btn_ulozit_dil'])) $this->save_form();
            if(isset($_POST['btn_ulozit_zadani'])) $this->save_form(false);
            if(isset($_POST['btn_duplikovat_dil'])) $this->duplicate_part();
            if(isset($_POST['btn_smazat_dil'])) $this->delete_part();
            if(isset($_POST['btn_delete_opt'])) $this->delete_opt();
            if(isset($_POST['btn_odeslat'])) $this->handle_order();
        }
        
        private function handle_order(){
            $form_id = $this->query_params['form_id'];
            (new OrderHandler())->handle_order($form_id);
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
            
            if ($this->query_params['form_id'] == 0) {
                $this->form_data['userId'] = (new User())->get_id();
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
        
        private static function jQuery_redirect($url){
            echo "<script>window.location.href = '" .$url ."';</script>";
        }

        private function sanitize_input(){
            
            if(isset($_GET['form_id']) && isset($_GET['part_id'])) {
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
}

