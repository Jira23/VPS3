<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

// handles Ardis form optimization and returns results

class Optimize {
    
    const ARDIS_SERVER_URL = 'https://ardis.drevoobchoddolezal.cz/';
    const ARDIS_SERVER_IMG_PATH = self::ARDIS_SERVER_URL .'img/';
    
    public function optimize() {

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         

        global  $wpdb;

        $this->form_id = (int)$_POST['form_id'];
        $parts = $parts = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY `id` DESC");
        $form = $wpdb->get_results("SELECT * FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$this->form_id ."'")[0];
        $plotny = $this->get_plotny($parts);

        $response = $this->send_request(['form' => $form, 'parts' => $parts, 'plotny' => $plotny]);

        $response_data = json_decode($response, true);

        if(isset($response_data['state']) && $response_data['state'] === 'success'){
            $this->response_handler($response_data['body']);
        } else {
            $this->report_error('Ardis error at ' .date('d.m.Y H:i'));
        }
        
        wp_die();
    }

    private function send_request($parts){
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($parts)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents(self::ARDIS_SERVER_URL, false, $context);

        if ($result === FALSE) {
            $this->report_error('file_get_content() returned false!');
        } else {
            return $result;
        }        
    }
    
    private function response_handler($response_body){
        $converted_response = $this->convert_response($response_body);
        $this->save_response($converted_response);
        (new \Inc\Pages\OptResults($this->form_id))->render_table();
        wp_die();
    }
    
    private function report_error($error){
        echo '<h4 style="color: red;">Během optimalizace se vyskytla chyba. Zkuste to prosím později.</h4>';
        $to = get_option('admin_email');
        $subject = 'Optimization error';
        $message = 'Při optimalizaci formluláře id:' .$this->form_id .' se vyskytla následující chyba:' .PHP_EOL .$error;
        wp_mail($to, $subject, $message);
        wp_die();
    }

    private function get_plotny($parts){
        
        foreach ($parts as $part) {
            $parts_ids[] = $part->lamino_id;
            if ($part->hrana_horni != 0) $parts_ids[] = $part->hrana_horni;
            if ($part->hrana_leva != 0) $parts_ids[] = $part->hrana_leva;
            if ($part->hrana_prava != 0) $parts_ids[] = $part->hrana_prava;
            if ($part->hrana_dolni != 0) $parts_ids[] = $part->hrana_dolni;
        }
        
        $unique_ids = array_unique($parts_ids);
        $plotny = [];
        
        foreach ($unique_ids as $productId) {
            $product = wc_get_product($productId);
            $plotny[$productId]['id'] = $productId;
            $plotny[$productId]['name'] = $product->get_name();
            $plotny[$productId]['price'] = $product->get_price();
            $plotny[$productId]['delka'] = $product->get_attribute('pa_delka');
            $plotny[$productId]['sirka'] = $product->get_attribute('pa_sirka');
            $plotny[$productId]['sila'] = $product->get_attribute('pa_sila');            
        }
       
       return $plotny;
    }
    
    private function convert_response($response_body){
       
        foreach ($response_body['Layouts'] as $key => $item) {
            $layouts[] = self::ARDIS_SERVER_IMG_PATH .$item['OrderId'] .'/' .basename($item['ImgPath']);
        }
        
        foreach ($response_body['ItemsList'] as $key => $item) {
            $converted[$key]['form_id'] = $this->form_id;
            $converted[$key]['order_id'] = $item['MpsId'];
            $converted[$key]['item_id'] = $item['ItemCode'];
            $converted[$key]['quantity'] = $item['Quantity'];
            $converted[$key]['price'] = $item['Price'];
            $converted[$key]['layouts'] = json_encode($layouts);
        }
        return $converted;
    }
    
    private function save_response($response){
        global $wpdb;
        $order_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " .NF_OPT_RESULTS_TABLE ." WHERE form_id = %d", $this->form_id ));
        
        if($order_exists != 0){                                                 // there is no order with this form_id in db, remove it so it can be "updated"
            $wpdb->delete(NF_OPT_RESULTS_TABLE, ['form_id' => $this->form_id]);
        }
        
        foreach ($response as $item) {
            $wpdb->insert(NF_OPT_RESULTS_TABLE,  $item);
        }        
    }
    
}
