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
        $this->send_customer_email($form_id);
        $this->render_thankyou_page();
    }
    
    public function send_customer_email($form_id){
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
        $subject = 'Nářezový formulář číslo ' .$form_id;
        $message = (new EmailText())->customer_email();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $attachments = ['seznam_dilu.pdf' => $temp_pdf_path];
        
        wp_mail($to, $subject, $message, $headers, $attachments);               // email to customer
        
        // modify for admin
        $to = NF_NEW_ORDER_NOTICE_EMAILS;
        $message = $subject;
//        wp_mail($to, $subject, $message, $headers, $attachments);               // email to DOD
        
        // Delete the temporary file
        fclose($temp_pdf);
        if (file_exists($temp_pdf_path)) unlink($temp_pdf_path);
    }

    private function render_thankyou_page(){
        echo '<h2>Děkujeme. Váš formulář byl odeslán.</h2>';
        $user = new User();
        if($user->is_registered()) {
            echo '<a href="' .$this->forms_list_page .'"><button class="button" type="button">Zpět na seznam</button></a>';
        } else {
            echo '<a href="/"><button class="button" type="button">Zpět na hlavní stranu</button></a>';
            $user->unset_cookies();
        }
        wp_die();
    } 

}
