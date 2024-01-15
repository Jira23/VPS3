<?php

    namespace Inc\OrderHandler;

    use Inc\Base\BaseController;
    use \Inc\Base\User;
    use PclZip;

    // downloads and modify original ardis files for saw. It wil change zip file name, file names in zip and content in these files
    class CreateZipAttachment extends BaseController{

        public $temp_dir_path;
        
        public function __construct() {
            parent::__construct();
        }

        public function create($form_id, $WC_order_id){
            $this->temp_dir_path = sys_get_temp_dir() .'/' .$WC_order_id .'/';
            if(is_dir($this->temp_dir_path)) $this->clear();
            mkdir($this->temp_dir_path);
            
            $orig_zip_DOD_path = $this->download_ardis_file($form_id);
            
            $original_zip = new PclZip($orig_zip_DOD_path);
            $new_zip = new PclZip($this->temp_dir_path .$WC_order_id .'_' .(new User())->get_sanitized_user_name() .'.zip');
            
            $original_zip->extract(PCLZIP_OPT_PATH, $this->temp_dir_path, PCLZIP_OPT_REMOVE_ALL_PATH);
            
            $list = $original_zip->listContent();
            $new_files = [];
            foreach ($list as $file) {
                $new_files[] = $this->modify_file($form_id, $WC_order_id, $this->temp_dir_path .$file['filename']);
            }            
            
            $new_zip->create($new_files, PCLZIP_OPT_REMOVE_PATH, dirname($new_files[0]));
       
            return $new_zip->zipname;
            
        }

        public function clear(){
            array_map('unlink', glob("$this->temp_dir_path/*.*"));
            rmdir($this->temp_dir_path);
        }

        private function download_ardis_file($form_id){
            $order_id = $this->get_order_id($form_id);
            $orig_zip_content = file_get_contents(ARDIS_SERVER_SAW_FILES_PATH .$order_id .'.zip');
            $orig_zip_DOD_path = $this->temp_dir_path .$form_id .'.zip';
            file_put_contents($orig_zip_DOD_path, $orig_zip_content);            
            return $orig_zip_DOD_path;
        }

        private function modify_file($form_id, $WC_order_id, $file_path){

            $order_id = $this->get_order_id($form_id);
            $original_file_content = file_get_contents($file_path);
            
            $new_file_path = str_replace($order_id .'_', $WC_order_id .'_', $file_path);
            $first_line = explode(PHP_EOL, $original_file_content)[0];
            $to_replace = explode(' ', $first_line)[4];                                                             // get string wich will be replaced
            $replace_with = str_replace($order_id .'_', $WC_order_id .'_', $to_replace);                             // change this string
            $updated_content = str_replace($to_replace, $replace_with, $original_file_content);                     // replace this string with changed strin in whole file
            file_put_contents($new_file_path, $updated_content);
            
            return $new_file_path;
        }
        
        private function get_order_id($form_id){
            global $wpdb;
            $prepared_statement = $wpdb->prepare("SELECT `order_id` FROM " .NF_OPT_RESULTS_TABLE ." WHERE form_id LIKE %s LIMIT 1", $form_id);
            $order_id = $wpdb->get_var($prepared_statement);
            
            return $order_id;
        }

    }
