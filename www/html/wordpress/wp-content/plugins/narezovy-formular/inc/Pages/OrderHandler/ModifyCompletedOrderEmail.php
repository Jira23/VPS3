<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\OrderHandler;

// add selectbox to user edit page
class ModifyCompletedOrderEmail {
    
    public function register(){
       // add_action('woocommerce_email_content_customer_processing_order', array($this, 'customize_customer_processing_order_email'));
    }

    public function customize_customer_processing_order_email($content, $order) {

    // Customize the email content
    $new_content = 'Your Custom Message';
    
    // Append the custom content to the existing email content
    $content = $new_content;

    return $content;
    }
    
    
}
