<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

use Inc\Pages\Tags\Alert;

// handles import file upload and returns results
class ImportUpload {
    
    public function import_upload() {
        
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         
        
        $response = (new \Inc\Import\Import())->init_import();
     
        $is_error = in_array(false, array_column($response["errors"], "status"), true);

        if($is_error){
            echo $this->assemble_error_report($response["errors"]);
            $this->send_email($response["errors"]);
        } else {
            $this->render_success_report($response['new_form_id']);
        }

        wp_die();
    }

    private function assemble_error_report($errors) {
        $report = '<h2>Během importu se vyskytly následující chyby:</h2>';
        $report .= '<table>';
        foreach ($errors as $row => $error) {
            if(is_array($error['message'])) {
                $report .= '<tr><td>Řádek&nbsp' .$row + 1 .':</td>';
                foreach ($error['message'] as $message) {
                    $report .= '<td>' .$message .'</td>';
                }
                $report .= '</tr>';
            } else if(!$error['status']){
                $report .= '<h4>Chyba ve stuktuře souboru! Zkonrolujte prosím zda soubor odpovídá formátu CSV.</h4>';
            }
        }
        $report .= '</table>';
        return $report;
    }
    
    private function render_success_report($new_form_id) {
        (new Alert())->render_alert('Import proběhl v pořádku.', 'success');
        ?>
            <h3><a href="<?php echo (new \Inc\Base\BaseController())->editor_page .'?form_id=' .$new_form_id .'&part_id=0' ?>">Přejít do editoru.</a></h3>
        <?php
    }
    
    private function send_email($errors){
        $to = get_option('admin_email');
        $subject = 'Import error';
        $message = $this->assemble_error_report($errors);
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $attachment = [$_FILES['file']['tmp_name']];
        wp_mail($to, $subject, $message, $headers, $attachment);
    }
    
}
