<?php
/**
 *  @package  narezovy-formular
 */
namespace Inc\Import;


class ImportPro100 {

    public function __construct() {
        set_error_handler(array($this, 'errorHandler'));                        // import can trigger unexpected PHP errors and warnings
    }

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        throw new \Exception("PHP Error/Warning: $errstr in $errfile on line $errline");
    }
    
    public function import(){
        try{
            $csv_data = $this->load_data();
            $lines = explode(PHP_EOL, $csv_data);
            $errors = [];
            $do_coversion = true;
            $converted_data = false;
            foreach ($lines as $line) {
                if($line == '') continue;
                $item_check = $this->check_item_params(explode(';', $line)); 
                if(!$item_check['status']) $do_coversion = false;               // skip data conversion if error
                $errors[] = $item_check;
            }

            if($do_coversion) $converted_data = array_reverse($this->convert_data($lines));       // reversing to be in same order as in csv file

            $to_return = ['data' => $converted_data, 'errors' => $errors];
            return $to_return;
        
        } catch (\Exception $e) {
            return ['data' => false, 'errors' => [['status' => false, 'message' => $e->getMessage()]]];
        }
    }
   
    private function convert_data($lines){
 
        $converted = [];
        foreach ($lines as $key => $line) {
            if($line == '') continue;
            $columns = explode(';', $line);
            $converted[$key]['lamino_id'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[0]));
            $converted[$key]['nazev_dilce'] = sanitize_text_field($columns[1]);
            $converted[$key]['lepidlo'] = 0;
            $converted[$key]['delka_dilu'] = (int)$columns[2];
            $converted[$key]['sirka_dilu'] = (int)$columns[3];
            $converted[$key]['ks'] = (int)$columns[4];
            $converted[$key]['orientace'] = (strtolower($columns[5]) == 'ano') ? 1 : 0;
            $converted[$key]['hrana_dolni'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[6]));
            $converted[$key]['hrana_horni'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[7]));
            $converted[$key]['hrana_prava'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[8]));
            $converted[$key]['hrana_leva'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[9]));
            $converted[$key]['tupl'] = 'NE';
            $converted[$key]['hrana'] = $this->get_hrana_option($converted[$key]['lamino_id'], [$converted[$key]['hrana_dolni'], $converted[$key]['hrana_horni'], $converted[$key]['hrana_prava'], $converted[$key]['hrana_leva']]);
            $converted[$key]['hrana_id'] = $this->get_hrana_id([$converted[$key]['hrana_dolni'], $converted[$key]['hrana_horni'], $converted[$key]['hrana_prava'], $converted[$key]['hrana_leva']]);
        }
        
        return $converted;

    }
    
    private function filter_hrana($sku, $product_id){
        
        $product = wc_get_product(wc_get_product_id_by_sku($product_id));
        if(!$product) return true;                                                                  // if there is no product for deska id, $is_PDK trigger PHP error and filter_hrany() method cant be used. I dont want to return edge error (beacouse it not edge error but actually deska error) so I return true.
        
        $hrana_id = wc_get_product_id_by_sku($this->sanitizeSKU($sku));                             // prepare data
        $hrana = wc_get_products(['include' => [$hrana_id]]);
        $is_PDK = in_array(PDK_CATEGORY_ID, $product->category_ids);
        $filtered_edge = (new \Inc\AJAX\HranyDimensions())->filter_hrany($hrana, $is_PDK, 'NE');      // call fitering function
        
        return empty($filtered_edge) ? false : $hrana_id;
    }

    // if there is any edge set, return its id as "hrana_id"
    private function get_hrana_id($hrany){
        foreach ($hrany as $hrana) {
            if($hrana !== 0) return $hrana;
        }
        
        return 0;
    }

    // set option for hrana selectbox
    private function get_hrana_option($lamino_id, $hrany){

        if (array_sum($hrany) === 0) return -1;                                                     // if there are no edges return -1

        $related_products = (new \Inc\AJAX\HranyDimensions())->getRelatedProducts($lamino_id);
    
        $commonValues = array_intersect($related_products, $hrany);                                 // Use array_intersect to find the common elements
        $allValuesInFirstArray = count($commonValues) === count($hrany);                            // Check if all values from the second array are present in the first array
        //var_dump($allValuesInFirstArray);
        
        return $allValuesInFirstArray ?  0: 1; 
    }
    
    
    private function check_item_params($params){

        $errors = array();
        
        if($params[0] == '') $errors[0] = ('Není zadáné SKU desky!');
        if($params[2] == '') $errors[2] = ('Není zadána délka desky!');
        if($params[3] == '') $errors[3] = ('Není zadána šířka desky!');
        if($params[4] == '') $errors[4] = ('Není zadán počet kusů desky!');

        if(!(isset($errors[0]))) {
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[0]))) $errors[0] = ('Nenalezeno SKU desky!');
            //if(wc_get_product_id_by_sku($this->sanitizeSKU($params[0])) === 0) $errors[0] = ('Nenalezeno SKU desky!');
        }
        
        if($this->sanitizeSKU($params[6]) !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[6]))) $errors[6] = ('Nenalezeno SKU hrany přední!');
            if(!$this->filter_hrana($this->sanitizeSKU($params[6]), $this->sanitizeSKU($params[0]))) $errors[6] = ('Hranu přední nelze použít! Neprošla filtrem.');
        }
        
        if($this->sanitizeSKU($params[7]) !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[7]))) $errors[7] = ('Nenalezeno SKU hrany zadní!');
            if(!$this->filter_hrana($this->sanitizeSKU($params[7]), $this->sanitizeSKU($params[0]))) $errors[7] = ('Hranu zadní nelze použít! Neprošla filtrem.');
        }
        
        if($this->sanitizeSKU($params[8]) !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[8]))) $errors[8] = ('Nenalezeno SKU hrany pravé!');            
            if(!$this->filter_hrana($this->sanitizeSKU($params[8]), $this->sanitizeSKU($params[0]))) $errors[8] = ('Hranu provou nelze použít! Neprošla filtrem.');
        }
        
        if($this->sanitizeSKU($params[9]) !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[9]))) $errors[9] = ('Nenalezeno SKU hrany levé!');
            if(!$this->filter_hrana($this->sanitizeSKU($params[9]), $this->sanitizeSKU($params[0]))) $errors[9] = ('Hranu levou nelze použít! Neprošla filtrem.');
        }
        
        if(!empty($errors)){
            return (array('status' => false, 'message' => $errors));
        } else {
            return (array('status' => true, 'message' => 'OK'));
        }
    }
    
    private function sanitizeSKU($sku){

        $int_sku = preg_replace('/[^0-9]/', '', $sku);
        if($int_sku == '') return '';
        $five_digits = str_pad(substr($int_sku, 0, 5), 5, '0', STR_PAD_LEFT);               // adds zeros in the beginning of nuber if its not 5 digits format
        return $five_digits;
    }
    
    private function load_data(){
        $file_data = file_get_contents($_FILES['file']['tmp_name']);
//$file_data = file_get_contents('/var/www/html/wordpress/satniky_LTD_no_error.csv');
//$file_data = file_get_contents('/var/www/html/wordpress/import_test.csv');
        
        $utf8EncodedData = iconv('Windows-1250', 'UTF-8', $file_data);
        
        return($utf8EncodedData); 
    }    
}
