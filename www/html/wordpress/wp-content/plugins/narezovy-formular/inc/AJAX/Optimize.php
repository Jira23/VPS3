<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

use \Inc\Exceptions\NFOptException;
use \Inc\Optimalization\HandleResponse;
use \Inc\Pages\OptResults;
use \Inc\Optimalization\PrepareRequest;

// handles Ardis form optimization and returns results


class Optimize {
    
    public $parts;
    public $form_id;
    
    const CONNECTION_TIMEOUT = 120;
    
    public function optimize() {

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         
        
        try {
            // prepare data
            $form_id = (int)$_POST['form_id'];
            $request_data = (new PrepareRequest())->prepare($form_id);
            
            // send request
            $response_data = $this->send_request($request_data);
            
            // handle response - convert it for DOD
            (new HandleResponse())->handle($form_id, $response_data['body']);
            (new OptResults($form_id))->render_table();
            
        } catch (\Throwable $t) {
            $this->report_error($t, $form_id);    
        }
        
        wp_die();
    }

    private function send_request($parts){
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'timeout' => self::CONNECTION_TIMEOUT,                          // Timeout in seconds
                'content' => http_build_query($parts)
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents(ARDIS_SERVER_URL, false, $context);

        if ($response === FALSE) throw new NFOptException('file_get_content() returned false!');
        
        $response_data = json_decode($response, true);            
        if(!isset($response_data['state']) && $response_data['state'] !== 'success') throw new NFOptException('Unknown Ardis error!');
        
        if($response_data['state'] === 'success' && empty($response_data['body']['ItemsList']))  throw new NFOptException('Ardis returned empty array!');
        
        return $response_data;
    }  
    
    private function report_error($t, $form_id){
echo '<pre>';        
var_dump($t);        
echo '<pre>';

        $message = ($t instanceof \Inc\Exceptions\NFOptException && $t->get_user_message()) ? $t->get_user_message() : 'Během optimalizace se vyskytla chyba. Zkuste to prosím později.';

        echo '<h4 style="color: red;">' .$message .'</h4>';
//$to = get_option('admin_email');
$to = 'jiri.freelancer@gmail.com';
        $subject = 'Optimization error';
        $body = 'Při optimalizaci formluláře id:' .$form_id .' se vyskytla následující chyba:' .PHP_EOL .$t .PHP_EOL;
//        wp_mail($to, $subject, $body);
        wp_die();
    }
}
