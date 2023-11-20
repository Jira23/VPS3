<?php
/**
 *  @package  narezovy-formular
 */
namespace Inc\Import;


class ImportCeska {

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
            foreach ($lines as $line) {
                $errors[] = $this->check_item_params(explode(';', $line));
            }
         


/*        
 !!!! dodelat fci na nastaveni toho co bude v selectoru #hrana nebo nechat vzdy ""privzorovana" nebo dat dalsi option "import"?
 !!!! dodelat fci ktera prozene hranu filtrem jako v metode "filter_hrany"? Slo by ale jen o "is_PDK"
 !!!! jake dat lepidlo? standartne transparentni?
 !!!! zeptat se Pavlase jak pojmenovat import, pak zmenit z Ceska na to co rekne
*/      
        
            $converted_data = $this->convert_data($lines);

            $to_return = ['data' => $converted_data, 'errors' => $errors];
            return $to_return;
        
        } catch (\Exception $e) {
            return ['data' => false, 'errors' => [['status' => false, 'message' => $e->getMessage()]]];
        }
    }
   
    private function convert_data($lines){
 
        $converted = [];
        foreach ($lines as $key => $line) {
            $columns = explode(';', $line);
            $converted[$key]['lamino_id'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[0]));
            $converted[$key]['nazev_dilce'] = sanitize_text_field($columns[1]);





$converted[$key]['hrana_id'] = 0;
$converted[$key]['lepidlo'] = 0;
            
            $converted[$key]['delka_dilu'] = (int)$columns[2];
            $converted[$key]['sirka_dilu'] = (int)$columns[3];
            $converted[$key]['ks'] = (int)$columns[4];
            $converted[$key]['hrana_dolni'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[5]));
            $converted[$key]['hrana_horni'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[6]));
            $converted[$key]['hrana_prava'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[7]));
            $converted[$key]['hrana_leva'] = wc_get_product_id_by_sku($this->sanitizeSKU($columns[8]));
            
$converted[$key]['hrana'] = $this->get_hrana_option($converted[$key]['lamino_id'], [$converted[$key]['hrana_dolni'], $converted[$key]['hrana_horni'], $converted[$key]['hrana_prava'], $converted[$key]['hrana_leva']]);
            
        }
        
        return $converted;

    }
   
   private function get_hrana_option($lamino_id, $hrany){
       
       /*
        * !!!!!!!!!! prekontolovat funkcnost !!!!!!!!!
        */
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
        }
        
        if($params[5] !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[5]))) $errors[5] = ('Nenalezeno SKU hrany přední!');
        }
        
        if($params[6] !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[6]))) $errors[6] = ('Nenalezeno SKU hrany zadní!');
        }
        
        if($params[7] !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[7]))) $errors[7] = ('Nenalezeno SKU hrany pravé!');            
        }
        
        if($params[8] !== ''){
            if(!wc_get_product_id_by_sku($this->sanitizeSKU($params[8]))) $errors[8] = ('Nenalezeno SKU hrany levé!');            
        }
        
        if(!empty($errors)){
            return (array('status' => false, 'message' => $errors));
        } else {
            return (array('status' => true, 'message' => 'OK'));
        }
    }
    
    // vybere z retezce pouze cisla. Nemohu pouzit (int) - nektera SKU zacinaji nulou
    private function sanitizeSKU($sku){
        return (preg_replace('/[^0-9]/', '', $sku));
    }
    
    private function load_data(){
        //$file_data = file_get_contents($_FILES['file']['tmp_name']);
//$file_data = file_get_contents('/var/www/html/wordpress/satniky_LTD.csv');
$file_data = file_get_contents('/var/www/html/wordpress/satniky_LTD_no_error.csv');
//$file_data = file_get_contents('/var/www/html/wordpress/satniky_LTD_one_error.csv');
//$file_data = file_get_contents('/var/www/html/wordpress/satniky_LTD_syntax_error.csv');

        $utf8EncodedData = iconv('Windows-1250', 'UTF-8', $file_data);

        return($utf8EncodedData); 
    }    
}
