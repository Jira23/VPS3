<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

use \Inc\Exceptions\NFOptException;
use \Inc\Exceptions\NFArdisException;
use \Inc\Optimalization\HandleResponse;
use \Inc\Pages\OptResults;
use \Inc\Optimalization\PrepareRequest;

// handles Ardis form optimization and returns results

class Optimize {
    
    public $parts;
    public $form_id;
    
    const CONNECTION_TIMEOUT = 120;
    
    public function optimize() {
        
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
        if(!isset($response_data['state'])) throw new NFOptException('Unknown Ardis error!');
        if(isset($response_data['state']) && $response_data['state'] === 'opt_error') $this->handle_opt_error ($response_data['body']);
        if($response_data['state'] === 'success' && empty($response_data['body']['ItemsList']))  throw new NFOptException('Ardis returned empty array!');
        
        return $response_data;
    }  
    
    private function report_error($t, $form_id){
/*
echo '<pre>';        
var_dump($t);        
echo '<pre>';
*/

    if($t instanceof \Inc\Exceptions\NFArdisException){
        $this->render_ardis_error($t->get_user_message());
    } else {
        $this->render_server_error($form_id);
    }

        
        
//$to = get_option('admin_email');
$to = ['jiri.freelancer@gmail.com', 'jakub.stabl@salusoft89.cz'];
//$to = ['jiri.freelancer@gmail.com'];
        $subject = 'Optimization error';
        $body = 'Při optimalizaci formluláře id:' .$form_id .' se vyskytla následující chyba:' .PHP_EOL .$t .PHP_EOL;
        wp_mail($to, $subject, $body);
        wp_die();
    }
  
    
    // create error message for user based on ardis error message
    private function handle_opt_error($encoded_message){

        $errors = json_decode($encoded_message);
        
        $ardis_errors_alias = [                                                                         // known ardis errors are converted to user friendly message
            'Byla použita odlišná definice pro stejné schéma'   => '"Byla použita odlišná definice pro stejné schéma".<br>Zkontolujte prosím zadání figur.',
            'Neplatná hodnota.'                                 => '"Neplatná hodnota".<br>Zkontolujte prosím zadání figur.',
            'Parts quantities do not match figure definition'   => '"Počet dílů neodpovídá vzorci figury".<br>Zkontolujte prosím zadání figur.',
            'Optimalizace nekompletní'   => '"Optimalizace nekompletní."<br>Zkontolujte prosím zadání.',
        ];
        
        foreach ($errors as $value) {
            $message = end(explode(':', $value->Msg));
            if(isset($ardis_errors_alias[trim($message)])) throw new NFArdisException(print_r($errors, true), 'Během optimalizace se vyskytla chyba ' .$ardis_errors_alias[trim($message)]);
        }
                
        throw new NFArdisException(print_r($errors, true), 'Během optimalizace se vyskytla chyba. Zkontrolujte prosím díly, zda jsou hodnoty zadány správně.');
    }
    
    private function render_server_error($form_id){
    ?>
        <h2>Během optimalizace se vyskytla chyba. Na jejím odstranění intenzivně pracujeme.</h2>
        <h4>Zkuste to, prosím, znovu později nebo odešlete poptávku ze staré verze formuláře <a href="/narezovy-formular/?form_id=<?php echo $form_id; ?>&part_id=0">zde.</a></h4>
    <?php
    }
    
    private function render_ardis_error($message){
        echo '<h2>' . $message .'</h2>';
    }    
}
