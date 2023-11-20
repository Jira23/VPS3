<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

// handles Ardis form optimization and returns results

class Optimize {
    
    const ARDIS_SERVER_URL = 'https://ardis.drevoobchoddolezal.cz/';
    
    public function optimize() {

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         

        global  $wpdb;

        $this->form_id = (int)$_POST['form_id'];
        $parts = $parts = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY `id` DESC");
        $form = $wpdb->get_results("SELECT * FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$this->form_id ."'")[0];
        
        $response = $this->send_request(['form' => $form, 'parts' => $parts]);
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
        var_dump($response_body);
    }
    
    private function report_error($error){
        echo '<h4 style="color: red;">Během optimalizace se vyskytla chyba. Zkuste to prosím později.</h4>';
        $to = get_option('admin_email');
        $subject = 'Optimization error';
        $message = 'Při optimalizaci formluláře id:' .$this->form_id .' se vyskytla následující chyba:' .PHP_EOL .$error;
        wp_mail($to, $subject, $message);
        die();
    }
    
}
