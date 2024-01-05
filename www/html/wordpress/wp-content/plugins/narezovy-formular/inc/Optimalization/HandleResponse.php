<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Optimalization;

use Inc\Base\User;
use Inc\Exceptions\NFOptException;

class HandleResponse  {
    
    public function handle($form_id, $response_body) {
        $converted_response = $this->convert_items($form_id, $response_body);
        $this->save_response($form_id, $converted_response);
    }
    
    private function convert_items($form_id, $response_body){

        foreach ($response_body['Layouts'] as $layout) {
            $DOD_layouts[] = ARDIS_SERVER_IMG_PATH .$layout['OrderId'] .'/' .basename($layout['ImgPath']);
        }
        
        $converted = [];
        foreach ($response_body['ItemsList'] as $key => $item) {
            $product = wc_get_product($item['ItemCode']);
            if(!$product) throw new NFOptException('Product id ' .$item['ItemCode'] .'doesnt exist!', 'Produkt nenalezen! Zkontrolujte prosím zadání.');

            $converted[$key]['form_id'] = $form_id;
            $converted[$key]['order_id'] = $item['MpsId'];
            $converted[$key]['item_id'] = $item['ItemCode'];
            $converted[$key]['item_label'] = $product->get_name();
            $converted[$key]['quantity'] = $item['Quantity'];
            $converted[$key]['price'] = $product->get_price();
            $converted[$key]['unit_name'] = $this->set_unit($item['ItemCode']);
            $converted[$key]['layouts'] = json_encode($DOD_layouts);
        }
        
        $sorted = $this->sort_services($converted);

        return $sorted;
    }
    
    private function save_response($form_id, $response){
        global $wpdb;
        $order_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " .NF_OPT_RESULTS_TABLE ." WHERE form_id = %d", $form_id ));
        if($order_exists != 0) $wpdb->delete(NF_OPT_RESULTS_TABLE, ['form_id' => $form_id]);                                                    // if there is order with this form_id in db, remove it so it can be "updated". Should not happen, beacouse results should be deleted by user click on "make table editable" button. But just in case...
 
        foreach ($response as $item) {
            $wpdb->insert(NF_OPT_RESULTS_TABLE,  $item);
        }        
        
        (new User())->update_opts_limit($form_id);                              //update user optimalizations counter
    }

    // moves defined services to the end of items array
    private function sort_services($items){
                
        $moveToEnd = [618427, 618424];                                                  // Zhotoveni narezovaho planu a balne
        
        $itemsNotToEnd = array_filter($items, function ($item) use ($moveToEnd) {       // Filter the subarrays with item_ids not in $moveToEnd
            return !in_array($item['item_id'], $moveToEnd);
        });

        $itemsToEnd = array_filter($items, function ($item) use ($moveToEnd) {          // Filter the subarrays with item_ids in $moveToEnd
            return in_array($item['item_id'], $moveToEnd);
        });

        $result = array_merge($itemsNotToEnd, $itemsToEnd);

        return $result;
    }
    
    private function set_unit($product_id){
        if(!(new User())->is_registered() && has_term(NF_KOLEKCE_TAG, 'product_tag', $product_id )){          // change unit if part is in "Kolekce" and user is not registered    
            return 'm2';
        } else {
            return get_post_meta($product_id, 'unit_name', true);
        }
    }    
}