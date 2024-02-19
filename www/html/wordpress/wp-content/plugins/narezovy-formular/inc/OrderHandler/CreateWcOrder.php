<?php

namespace Inc\OrderHandler;

use Inc\Base\User;

class CreateWcOrder{

    public function create_order($form_id){
        
        $user = new User();
        $order = new \WC_Order();

        //$order->set_customer_note('--- Z NÁŘEZOVÉHO FORMULÁŘE ---' .PHP_EOL .$this->get_customer_note($form_id));
        //$order->add_order_note('--- Z NÁŘEZOVÉHO FORMULÁŘE č.' .$form_id .' ---' .PHP_EOL);
        
        
        $this->set_products($order, $form_id);
        $this->set_addresses($order);
          
        if($user->is_registered()) $order->set_customer_id($user->get_id());

        //$order->set_payment_method('cop');
        //$order->set_payment_method_title('Dobírka');        
        
        $order->calculate_totals();
        $order->set_status('processing');
        
        $WC_order_id = $order->save();
        
        //update_post_meta($order_id, '_from_NF', 'true');
        $custom_order_id = get_post_meta($WC_order_id, '_alg_wc_custom_order_number', true);
        
        return $custom_order_id;
    }

    private function set_addresses($order){
        $user = new User();
        $user_id = $user->get_id();
        
        if($user->is_registered()){
            // Set billing address
            $billing_address = array(
              'first_name' => get_user_meta($user_id, 'billing_first_name', true),
              'last_name' => get_user_meta($user_id, 'billing_last_name', true),
              'company' => get_user_meta($user_id, 'billing_company', true),
              'address_1' => get_user_meta($user_id, 'billing_address_1', true),
              'address_2' => get_user_meta($user_id, 'billing_address_2', true),
              'city' => get_user_meta($user_id, 'billing_city', true),
              'postcode' => get_user_meta($user_id, 'billing_postcode', true),
              'country' => get_user_meta($user_id, 'billing_country', true),
              'email' => get_user_meta($user_id, 'billing_email', true),
              'phone' => get_user_meta($user_id, 'billing_phone', true)
            );

            // Set shipping address
            $shipping_address = array(
                'first_name' => get_user_meta($user_id, 'shipping_first_name', true),
                'last_name' => get_user_meta($user_id, 'shipping_last_name', true),
                'company' => get_user_meta($user_id, 'shipping_company', true),
                'address_1' => get_user_meta($user_id, 'shipping_address_1', true),
                'address_2' => get_user_meta($user_id, 'shipping_address_2', true),
                'city' => get_user_meta($user_id, 'shipping_city', true),
                'postcode' => get_user_meta($user_id, 'shipping_postcode', true),
                'country' => get_user_meta($user_id, 'shipping_country', true)
            );
            
        } else {
            
            // set billing and shipping address from data in cookies for unregistered user
            $contact = $user->get_contact();
            if(!$contact) return;
            
            $billing_address = $shipping_address = array(
                'first_name' => $contact['jmeno'],
                'last_name' => $contact['prijmeni'],
                'address_1' => $contact['ulice'],
                'country' => 'CZ',
                'phone' => $contact['telefon']
            );  
            
            if(strpos($contact['mesto'], ',')){
                $billing_address['city'] = $shipping_address['city'] = explode(',', $contact['mesto'])[0];
                $billing_address['postcode'] = $shipping_address['postcode'] = explode(',', $contact['mesto'])[1];
            } else {
                $billing_address['address_2'] = $shipping_address['address_2'] = $contact['mesto'];
            }
            if(strpos($contact['email'], '@') && strpos($contact['email'], '.')) $billing_address['email'] = $shipping_address['email'] = $contact['email'];
        }
        
        // finally set data
        $order->set_address($billing_address, 'billing');
        $order->set_address($shipping_address, 'shipping');            
    }
    
    private function set_products($order, $form_id){
        global $wpdb;
        $order_products = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' ORDER BY `id` ASC");
        
        foreach ($order_products as $order_product) {
            $product_id = $order_product->item_id;
            $quantity = $order_product->quantity;
            $kolekce_price = $order_product->price;
            $product = wc_get_product($product_id);
            if($product) $this->add_product_to_order($order, $product, $product_id, $quantity, $kolekce_price);
        }
    }
    
    private function get_customer_note($form_id){
        global $wpdb;
        $customer_note = $wpdb->get_results("SELECT `poznamka` FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$form_id ."' LIMIT 1")[0];        
        return $customer_note->poznamka;
    }
    
    private function add_product_to_order($order, $product, $product_id, $quantity, $kolekce_price){
         if (has_term(NF_KOLEKCE_TAG, 'product_tag', $product_id )) {                                   // if product has "kolekce" tag, it will be replaced by virtual product and price will be changed
            $original_product_name = $product->get_name();
            $product = wc_get_product(NF_VIRTUAL_PRODUCT_ID);
            $item_id = $order->add_product($product, $quantity, ['name' => $original_product_name, 'subtotal'=> $kolekce_price * $quantity, 'total'=> $kolekce_price * $quantity]);
        } else {
            $item_id = $order->add_product($product, $quantity);
        }

        if (!empty($item_id)){                                          // add product meta
            $order_item = $order->get_item($item_id);
            $order_item->add_meta_data('_productuid', $product->get_meta('_productuid'), true);
            $order_item->add_meta_data('_unit_name', $product->get_meta('unit_name'), true);
            $order_item->add_meta_data('_filled_amount', $product->get_meta('filled_amount'), true);
            $order_item->save();
        }       
    }

}


