<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Optimalization;

class PrepareRequest  {
    
    public function prepare($form_id) {
        global  $wpdb;
        
        $parts = $this->get_parts($form_id);
        $form = $wpdb->get_results("SELECT * FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$form_id ."'")[0];
        $plotny = $this->get_plotny($parts);        

        $request_data = ['form' => $form, 'parts' => $parts, 'plotny' => $plotny];

        return $request_data;
    }
    
    private function get_parts($form_id){
        global  $wpdb;
        $parts = $wpdb->get_results("SELECT * FROM `" .NF_DILY_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' ORDER BY `id` DESC");
        
        foreach ($parts as $part) {
            if($part->tupl == '30mm') {                                         // podlepeni bilou deskou 12mm
                $part->tupl_mat_id = PLOTNA_TUPL_30;
            } elseif($part->tupl == '36mm'){                                    // podlepeni deskou ve stejnem dekoru
                $part->tupl_mat_id = $part->lamino_id;
            } elseif($part->tupl == '36mm-bila'){                               // podlepeni bilou deskou 18mm
                $part->tupl_mat_id = PLOTNA_TUPL_36;
            } else{
                $part->tupl_mat_id = '';
            }
        }
        unset($part);                                                           // unset the reference after the loop to avoid potential conflicts

        return $parts;
    }
    
    private function get_plotny($parts){
        
        foreach ($parts as $part) {
            $parts_ids[] = $part->lamino_id;
            if ($part->hrana_horni != 0) $parts_ids[] = $part->hrana_horni;
            if ($part->hrana_leva != 0) $parts_ids[] = $part->hrana_leva;
            if ($part->hrana_prava != 0) $parts_ids[] = $part->hrana_prava;
            if ($part->hrana_dolni != 0) $parts_ids[] = $part->hrana_dolni;
            if ($part->tupl == '30mm') $parts_ids[] = PLOTNA_TUPL_30;                   
            if ($part->tupl == '36mm-bila') $parts_ids[] = PLOTNA_TUPL_36;         
        }
        
        $unique_ids = array_unique($parts_ids);
        $plotny = [];
        
        foreach ($unique_ids as $productId) {
            $product = wc_get_product($productId);
            if(!$product) throw new NFOptException('Product doesnt exist!', 'Produkt nenalezen! Zkontrolujte prosím zadání.');
            
            $plotny[$productId]['id'] = $productId;
            $plotny[$productId]['name'] = $product->get_name();
            $plotny[$productId]['price'] = $product->get_price();
            $plotny[$productId]['delka'] = $product->get_attribute('pa_delka');
            $plotny[$productId]['sirka'] = $product->get_attribute('pa_sirka');
            $plotny[$productId]['sila'] = $product->get_attribute('pa_sila');            
        }
       
       return $plotny;
    }
    
}