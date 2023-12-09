<?php

namespace Inc\Pages\OrderHandler;

use Inc\Base\User;

class CreateWcOrder{

    public function create_order($form_id){
        
        $user = new User();
        $order = new \WC_Order();

        $order->set_customer_note('--- Z NÁŘEZOVÉHO FORMULÁŘE ---' .PHP_EOL .$this->get_customer_note($form_id));
        $this->set_products($order, $form_id);
        $this->set_addresses($order);
        $order->set_status('processing');
          
        if($user->is_registered()) $order->set_customer_id($user->get_id());

        //$order->set_payment_method('cop');
        //$order->set_payment_method_title('Hotově / kartou (při vyzvednutí)');        
        
        $order->calculate_totals();
        $order_id = $order->save();
        
        return $order_id;
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
                'first_name' => $contact['nf_jmeno'],
                'last_name' => $contact['nf_prijmeni'],
                'address_1' => $contact['nf_ulice'],
                'country' => 'CZ',
                'phone' => $contact['nf_telefon']
            );  
            
            if(strpos($contact['nf_mesto'], ',')){
                $billing_address['city'] = $shipping_address['city'] = explode(',', $contact['nf_mesto'])[0];
                $billing_address['postcode'] = $shipping_address['postcode'] = explode(',', $contact['nf_mesto'])[1];
            } else {
                $billing_address['address_2'] = $shipping_address['address_2'] == $contact['nf_mesto'];
            }
            if(strpos($contact['nf_email'], '@') && strpos($contact['nf_email'], '.')) $billing_address['email'] = $shipping_address['email'] = $contact['nf_email'];
        }
        
        // finally set data
        $order->set_address($billing_address, 'billing');
        $order->set_address($shipping_address, 'shipping');            
    }
    
    private function set_products($order, $form_id){
        global $wpdb;
        $order_products = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' ORDER BY `id` ASC");
        
        foreach ($order_products as $product) {
            $product_id = $product->item_id;
            $quantity = $product->quantity;
            $product = wc_get_product($product_id);
            if($product)  $order->add_product($product, $quantity);                    
        }
    }
    
    private function get_customer_note($form_id){
        global $wpdb;
        $customer_note = $wpdb->get_results("SELECT `poznamka` FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$form_id ."' LIMIT 1")[0];        
        return $customer_note->poznamka;
    }
}

