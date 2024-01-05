<?php

namespace Inc\OrderHandler;

use Inc\Base\BaseController;
use Inc\Base\User;
use Inc\Base\EmailText;

// do actions after order is placed
class OrderHandler extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function handle_order($form_id) {
        $WC_order_id = (new CreateWcOrder())->create_order($form_id);
        $this->change_order_status($form_id, $WC_order_id);
        (new User())->reset_opts_limit();                                 
        $this->send_emails($form_id);
    }
    
    public function send_emails($form_id){
        global $wpdb;
        $user = new User();

        add_filter('wp_mail_from', function () {
            return 'rezaninamiru@drevoobchoddolezal.cz';
        });        
        
        add_filter('wp_mail_from_name', function () {
            return 'Dřevoobchod doležal';
        });
        
        // prepare files to attachement
        $temp_pdf = $this->prepare_temp_file((new \Inc\Output\Output())->render_customer_summary_pdf($form_id));
        $temp_pdf_path = stream_get_meta_data($temp_pdf)['uri'];
        $temp_nc = $this->prepare_temp_file(@file_get_contents($this->get_saw_file_url($form_id)));
        $temp_nc_path = stream_get_meta_data($temp_nc)['uri'];        
        
        // prepare email customer 
        $to = $user->get_contact()['email'];
//$to = 'jiri.freelancer@gmail.com';
        $subject = 'Nářezový formulář číslo ' .$this->get_wc_order_id($form_id);
        $message = (new EmailText())->customer_email();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $attachments = ['seznam_dilu.pdf' => $temp_pdf_path, 'Obchodni_a_technicke_podminky_vyroby.pdf' => '/home/drevoobchoddolezal.cz/public_html/DOD_Obchodni_a_technicke_podminky_vyroby.pdf'];
        wp_mail($to, $subject, $message, $headers, $attachments);               // email to customer
        
        // modify for admin
        $to = NF_NEW_ORDER_NOTICE_EMAILS;
        $message = (new EmailText())->admin_email();
        $attachments = ['seznam_dilu.pdf' => $temp_pdf_path, $this->get_nc_filename($form_id) .'.NC' => $temp_nc_path];

        wp_mail($to, $subject, $message, $headers, $attachments);               // email to DOD
    }
    
    public function change_order_status($form_id, $WC_order_id){
        global $wpdb;
        $wpdb->update(NF_FORMULARE_TABLE, ['odeslano' => 1, 'WcZakazkaId' => $WC_order_id], ['id' => $form_id]);
    }
    
    private function get_saw_file_url($form_id){
        global $wpdb;
        $order_id = $wpdb->get_results("SELECT `order_id` FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' LIMIT 1")[0]->order_id;

        if($order_id) return ARDIS_SERVER_SAW_FILES_PATH .$order_id .'.NC';        
    }
    
    private function prepare_temp_file($data){
        $temp_file = tmpfile();
        fwrite($temp_file, $data);
        return $temp_file;
    }
    
    private function get_nc_filename($form_id){                                 // prepares file name in form of "form-id_user-name"
        global $wpdb;
        $wc_order_id = $this->get_wc_order_id($form_id);
        $user_contact = (new User())->get_contact();
        $file_name = $wc_order_id .'_' .sanitize_file_name($user_contact['jmeno'] .'_' .$user_contact['prijmeni']);
        
        return $file_name;
    }
    
    public function check_prices($form_id){
        global $wpdb;
        $order_products = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' ORDER BY `id` ASC");
        
        foreach ($order_products as $product) {
            $opt_price = round($product->price, 2);
            $product = wc_get_product($product->item_id);
            if(!$product) return false;
            $current_price = round($product->get_price(), 2);
            if($opt_price !== $current_price) return false;
        }
        
        return true;
    }
    
    private function get_wc_order_id($form_id){
        global $wpdb;
        $wc_order_id = $wpdb->get_results("SELECT `WcZakazkaId` FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$form_id ."' LIMIT 1")[0]->WcZakazkaId;    
        return (int)($wc_order_id);
    }

}

