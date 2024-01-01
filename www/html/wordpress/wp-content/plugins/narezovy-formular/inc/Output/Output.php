<?php
/**
 *  @package  narezovy-formular
 */

// creates PDF output for customer and DOD

namespace Inc\Output;

use Dompdf\Dompdf;

class Output  {
    
    public function render_customer_summary_html($form_id){
        echo $this->render_header('html');
        echo $this->render_user_table($form_id);
        echo $this->render_parts_table($form_id);
        (new \Inc\Pages\OptResults($form_id))->render_opt_results();
        echo $this->render_footer();
    }
    
    public function render_customer_summary_pdf($form_id){
        
        $html = $this->render_header('pdf');
        $html .= $this->render_user_table($form_id);
        $html .= $this->render_parts_table($form_id);
        $html .= '<h4 class="email-center page-break">Výsledky optimalizace</h4>';

        ob_start();
        (new \Inc\Pages\OptResults($form_id))->render_table(false);
        $html .= ob_get_clean();
        $html .= $this->render_layouts($form_id);
        $html .= $this->render_footer();

        $dompdf = new Dompdf(['enable_remote' => true]);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdf_content = $dompdf->output();
        
//$pdf_file_path = '/var/www/html/wordpress/wp-content/plugins/narezovy-formular/yourfile.pdf';
//file_put_contents($pdf_file_path, $pdf_content);

        return $pdf_content;
    }    

    private function render_layouts($form_id){
        global $wpdb;    
        $results = $wpdb->get_results("SELECT `layouts` FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' LIMIT 1");
        $layouts = json_decode($results[0]->layouts);
        
        $to_return = '';
        foreach ($layouts as  $layout) {
            $to_return .= '<img src="' .$layout .'" class="page-break" />';
        }
        return $to_return;
    }
    
    private function render_header($for){
        $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                ' .$this->set_style($for) .'
            </head>
            <body>';           

        return $html;
    }
    
    private function render_footer(){
        $html = '
                </body>
            </html>
        ';           

        return $html;
    }    

    private function render_user_table($form_id){

        global $wpdb;    
        $formular_data = $wpdb->get_results("SELECT * FROM " .NF_FORMULARE_TABLE ." WHERE `id` LIKE '" .$form_id ."'")[0];
        $user_data = (new \Inc\Base\User())->get_contact();

        $table = '
            <h5 style=" text-align: center;">Dřevoobchod Doležal</h5>
            <h3 style=" text-align: center;">Nářezový formulář číslo ' .$form_id .' - ' .$formular_data->nazev .'</h3>

            <table class="user-table">
                    <td colspan="10" style="text-align: center; border-bottom: 2px solid black;"><h4>Detail zakázky</h4></td>
                <tr>
                    <td colspan="2" style="width:10%;"><b>Firma/osoba:</b></td>
                    <td colspan="3">' .$user_data['jmeno'] .' ' .$user_data['prijmeni'] .'</td>
                    <td></td>
                    <td style="width:10%;"><b>Datum:</b></td>
                    <td colspan="3">' .date('j.n.Y', strtotime($formular_data->datum)) .'</td>
                </tr>
                <tr>
                    <td colspan="2"><b>Telefon:</b></td>
                    <td colspan="3">' .$user_data['telefon'] .'</td>
                    <td></td>
                    <td><b>Olepení:</b></td>
                    <td colspan="3">'; 
                    ($formular_data->olepeni == 0) ? $table .= 'Standart' : $table .= 'S dočištěním';
                    $table .=
                    '</td>                        
                </tr>
                <tr>
                    <td colspan="2"><b>Dodací adresa:</b></td>
                    <td colspan="3">' .$user_data['ulice'] .' ' .$user_data['mesto']  .'</td>
                    <td></td>
                    <td><b>Štítky:</b></td>
                    <td colspan="3">'; 
                    ($formular_data->stitky == 0) ? $table .= 'NE' : $table .= 'ANO';
                    $table .=
                    '</td>                        
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="3"></td>
                    <td></td>
                    <td><b>Doprava:</b></td>
                    <td colspan="3">'; 
                    if ($formular_data->doprava == 0) $table .= 'Osobně Ml. Boleslav';
                    if ($formular_data->doprava == 1) $table .= 'Osobně Jiz. Vtelno';
                    if ($formular_data->doprava == 2) $table .= 'Rozvoz';
                    $table .=
                    '</td>                        
                </tr>                    
                <tr></tr>
            </table>';

            if($formular_data->poznamka != ''){
                $table .= '<p>Poznámka:<br>' .nl2br($formular_data->poznamka) .'</p>';
            }
        return $table;
    }

    private function render_parts_table($form_id){

        global $wpdb;    
        $dily_data = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' ORDER BY `lamino_id`, `id`");

        $table = '
            <br>
            <div style="text-align: center;" class="page-break">
                <h4>Rozpis dílů</h4>
            </div>
            <table class="parts-table">
                <tr>
                    <td style="border-bottom: 2px solid black;" colspan=8><b>Formátování</b></td>
                    <td style="border-bottom: 2px solid black; border-left: 1px solid black;" colspan=7><b>Ohranění</b></td>
                </tr>
                <tr>
                    <td><b>číslo</b></td>
                    <td><b>č.&nbsp;produktu</b></td>
                    <td><b>název produktu</b></td>
                    <td><b>označení dílce</b></td>
                    <td><b>ks</b></td>
                    <td><b>délka</b></td>
                    <td><b>šířka</b></td>
                    <td><b>orient.</b></td>                            
                    <td><b>hrana přední</b></td>
                    <td><b>hrana zadní</b></td>
                    <td><b>hrana pravá</b></td>
                    <td><b>hrana levá</b></td>
                    <td><b>tupl</b></td>
                    <td><b>lepidlo</b></td>
                    <td><b>figura</b></td>
                </tr>';


                $re = new \Inc\Pages\ClassicEditor\RenderEditor();                
                $i = 1;
                foreach ($dily_data as $row) {                                       // vypis dat
                    $table .=
                    '<tr>'
                    .'<td>' .$i .'</td>'
                    .'<td>' .$row->lamino_id .'</td>'                                    
                    .'<td>' .$re->get_deska_name_by_id($row->lamino_id) .'</td>'
                    .'<td>' .$row->nazev_dilce .'</td>'
                    .'<td>' .$row->ks .'</td>'
                    .'<td>' .$row->delka_dilu .'</td>'
                    .'<td>' .$row->sirka_dilu .'</td>'
                    .'<td>' .($row->orientace == 1 ? 'ANO' : 'NE') .'</td>'
                    .'<td>' .($row->hrana_dolni != 0 ? $re->get_hrana_name_by_id($row->hrana_dolni, true) : '')  .'</td>'
                    .'<td>' .($row->hrana_horni != 0 ? $re->get_hrana_name_by_id($row->hrana_horni, true) : '') .'</td>'                                    
                    .'<td>' .($row->hrana_prava != 0 ? $re->get_hrana_name_by_id($row->hrana_prava, true) : '') .'</td>'
                    .'<td>' .($row->hrana_leva != 0 ? $re->get_hrana_name_by_id($row->hrana_leva, true) : '') .'</td>'
                    .'<td>' .$row->tupl .'</td>'
                    .'<td>' .($row->lepidlo === '0' ? 'Trans.' : ($row->lepidlo === '1' ? 'Bílé' : ''))  .'</td>'
                    .'<td>' .(empty($row->fig_name) ? '' : $row->fig_name .'|' .$row->fig_part_code) .'</td>'
                    .'</tr>';

                    $i++;
                }
        $table .= '</table>';

        return $table;

    }

    private function set_style($for = 'html'){
        $style = '<style>';
        $style .= file_get_contents( (new \Inc\Base\BaseController())->plugin_url .'assets/css/email.css');
        if($for == 'html'){
              $style .= ' .parts-table { font-size:12px; }';
        } else {
            $style .= ' .parts-table { font-size:8px; }';
        }        
        $style .= '</style>';
        return $style;
    }
    
}