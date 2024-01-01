<?php

namespace Inc\Pages\OrderHandler;

use Inc\Base\BaseController;
use Inc\Base\User;

// do actions after order is placed
class OrderHandler extends BaseController{

    public function __construct() {
        parent::__construct();
    }

    public function handle_order($form_id) {
        (new CreateWcOrder())->create_order($form_id);
        $this->send_emails($form_id);
//        $this->change_order_status($form_id);
    }
    
    public function send_emails($form_id){
        $user = new User();

        add_filter('wp_mail_from', function () {
            return 'rezaninamiru@drevoobchoddolezal.cz';
        });        
        
        add_filter('wp_mail_from_name', function () {
            return 'Dřevoobchod doležal';
        });
        
        // creating temp PDF file in memory. Cant send attachment wich is not file with wp_mail (PHPMailer)
        $temp_pdf = tmpfile();
        fwrite($temp_pdf, (new \Inc\Output\Output())->render_customer_summary_pdf($form_id));
        $temp_pdf_meta = stream_get_meta_data($temp_pdf);
        $temp_pdf_path = $temp_pdf_meta['uri'];        

        // prepare email customer
        $to = $user->get_contact()['email'];
$to = 'jiri.freelancer@gmail.com';
        $subject = 'Nářezový formulář číslo ' .$form_id;
        $message = (new EmailText())->customer_email();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $attachments = ['seznam_dilu.pdf' => $temp_pdf_path, 'Obchodni_a_technicke_podminky_vyroby.pdf' => '/home/drevoobchoddolezal.cz/public_html/DOD_Obchodni_a_technicke_podminky_vyroby.pdf'];
        wp_mail($to, $subject, $message, $headers, $attachments);               // email to customer
        
        // modify for admin
        $to = NF_NEW_ORDER_NOTICE_EMAILS;
        $message = (new EmailText())->admin_email($this->get_saw_file_url($form_id));
        $attachments = ['seznam_dilu.pdf' => $temp_pdf_path];

        wp_mail($to, $subject, $message, $headers, $attachments);               // email to DOD
        
        // Delete the temporary file
        fclose($temp_pdf);
        if (file_exists($temp_pdf_path)) unlink($temp_pdf_path);
    }
    
    public function change_order_status($form_id){
        global $wpdb;
        $wpdb->update(NF_FORMULARE_TABLE, ['odeslano' => 1], ['id' => $form_id]);
       
    }
    
    private function get_saw_file_url($form_id){
        global $wpdb;
        $order_id = $wpdb->get_results("SELECT `order_id` FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' LIMIT 1")[0]->order_id;

        if($order_id) return ARDIS_SERVER_SAW_FILES_PATH .$order_id .'.NC';        
    }

}

