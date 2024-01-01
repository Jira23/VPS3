<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Optimalization;

class HandleResponse  {
    
    public function handle($form_id, $response_body) {
        

        $items_with_DOD_services = (new Services())->get_DOD_services($response_body['ItemsList']);
        
        $converted_response = $this->convert_items($form_id, $items_with_DOD_services, $response_body['Layouts']);
        $this->save_response($form_id, $converted_response);
    }
    
    private function convert_items($form_id, $items, $layouts){
        
        foreach ($layouts as $layout) {
            $DOD_layouts[] = ARDIS_SERVER_IMG_PATH .$layout['OrderId'] .'/' .basename($layout['ImgPath']);
        }
        
        $converted = [];
        foreach ($items as $key => $item) {
            $product = wc_get_product($item['ItemCode']);
            if(!$product) throw new NFOptException('Product doesnt exist!', 'Produkt nenalezen! Zkontrolujte prosím zadání.');

            $converted[$key]['form_id'] = $form_id;
            $converted[$key]['order_id'] = $item['MpsId'];
            $converted[$key]['item_id'] = $item['ItemCode'];
            $converted[$key]['item_label'] = $product->get_name();
            $converted[$key]['quantity'] = $item['Quantity'];
            $converted[$key]['price'] = $product->get_price();
            $converted[$key]['unit_name'] = get_post_meta($item['ItemCode'], 'unit_name', true);
            $converted[$key]['layouts'] = json_encode($DOD_layouts);
        }
        return $converted;
    }
    
    private function save_response($form_id, $response){
        global $wpdb;
        $order_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " .NF_OPT_RESULTS_TABLE ." WHERE form_id = %d", $form_id ));
        
        if($order_exists != 0){                                                     // there is no order with this form_id in db, remove it so it can be "updated"
            $wpdb->delete(NF_OPT_RESULTS_TABLE, ['form_id' => $form_id]);
        }
        
        foreach ($response as $item) {
            $wpdb->insert(NF_OPT_RESULTS_TABLE,  $item);
        }        
    }
    
}