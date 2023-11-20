<?php

namespace Inc\Pages;

use Inc\Base\BaseController;

    class FormsListFormHandler extends BaseController{

        public function __construct() {
            parent::__construct();
        }

        public function handle_forms_list_form(){
            if(isset($_POST['btn_duplikovat_formular'])) $this->duplicate_form();
            if(isset($_POST['btn_smazat_formular'])) $this->delete_form();
        }
        
        private function delete_form(){
            global $wpdb;
            $form_id = (int)$_POST['btn_smazat_formular'];
            $wpdb->delete(NF_FORMULARE_TABLE, array('id' => $form_id));
            $wpdb->delete(NF_DILY_TABLE, array('form_id' => $form_id));
        }
        
        private function duplicate_form(){
            global $wpdb;
            $form_id = (int)$_POST['btn_duplikovat_formular'];
            
            // clone form
            $row_to_clone = (array)$wpdb->get_row("SELECT * FROM " .NF_FORMULARE_TABLE ." WHERE id = " .$form_id);

            if ($row_to_clone) {
                unset($row_to_clone['id']);
                unset($row_to_clone['datum']);
                $row_to_clone['odeslano'] = 0;
                $row_to_clone['poptano'] = 0;
                $wpdb->insert(NF_FORMULARE_TABLE, $row_to_clone);
                $cloned_row_id = $wpdb->insert_id;
            }
            
            // clone parts
            $rows_to_clone = $wpdb->get_results("SELECT * FROM " .NF_DILY_TABLE ." WHERE form_id = " .$form_id);
            if (!empty($rows_to_clone)) {
                foreach ($rows_to_clone as $row) {
                    $row = (array)$row;
                    unset($row['id']);
                    $row['form_id'] = $cloned_row_id;
                    $wpdb->insert(NF_DILY_TABLE, $row);
                }
            }            
        }
        
    }

