<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

// handles import file upload and returns results

class ImportUpload {
    
    public function import_upload() {
        
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         
        
        $response = (new \Inc\Import\Import())->init_import();
     
        $is_error = in_array(false, array_column($response["errors"], "status"), true);

        if($is_error){
            $this->render_error_report($response["errors"]);
        } else {
            $this->render_success_report($response['new_form_id']);
        }

        wp_die();
    }
    
    private function render_error_report($errors) {
var_dump($errors);
        ?>
        <h2>Během importu se vyskytly následující chyby:</h2>
        <table>
        <?php
        foreach ($errors as $row => $error) {
            if(is_array($error['message'])) {
                echo '<tr><td>Řádek&nbsp' .$row .':</td>';
                foreach ($error['message'] as $message) {
                    echo '<td>' .$message .'</td>';
                }
                echo '</tr>';
            } else if(!$error['status']){
                echo '<h4>Chyba ve stuktuře souboru! Zkonrolujte prosím zda soubor odpovídá formátu CSV.</h4>';
            }
        }
        ?>
        </table>    
        <?php
    }
    
    private function render_success_report($new_form_id) {
        ?>
            <h2>Import proběhl v pořádku.</h2>
            <h3><a href="<?php echo (new \Inc\Base\BaseController())->editor_page .'?form_id=' .$new_form_id .'&part_id=0' ?>">Přejít do editoru.</a></h3>
        <?php
    }    
    
}
