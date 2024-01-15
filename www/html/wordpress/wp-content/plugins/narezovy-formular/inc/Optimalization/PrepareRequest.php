<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Optimalization;

use \Inc\Base\User;

class PrepareRequest  {
    
    public function prepare($form_id) {
        global  $wpdb;
        
        $parts = $this->get_parts($form_id);
        $form = $this->get_form($form_id);
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
                $part->nazev_dilce = 'Tupl - ' .$part->nazev_dilce;
            } elseif($part->tupl == '36mm'){                                    // podlepeni deskou ve stejnem dekoru
                $part->tupl_mat_id = $part->lamino_id;
                $part->nazev_dilce = 'Tupl - ' .$part->nazev_dilce;
            } elseif($part->tupl == '36mm-bila'){                               // podlepeni bilou deskou 18mm
                $part->tupl_mat_id = PLOTNA_TUPL_36;
                $part->nazev_dilce = 'Tupl - ' .$part->nazev_dilce;
            } else{
                $part->tupl_mat_id = '';
            }
        }
        unset($part);                                                           // unset the reference after the loop to avoid potential conflicts

        return $parts;
    }
    
    private function get_form($form_id){
        global $wpdb;
                
        $form = $wpdb->get_results("SELECT * FROM `" .NF_FORMULARE_TABLE ."` WHERE `id` LIKE '" .$form_id ."'")[0];
        $form->user_name = (new User())->get_sanitized_user_name();
        
        return $form;
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
        
        foreach ($unique_ids as $product_id) {
            $product = wc_get_product($product_id);
            if(!$product) throw new NFOptException('Product doesn`t exist!', 'Produkt nenalezen! Zkontrolujte prosím zadání.');
            
            $plotny[$product_id]['id'] = $product_id;
            $plotny[$product_id]['name'] = $product->get_name();
            $plotny[$product_id]['price'] = $product->get_price();
            $plotny[$product_id]['delka'] = $product->get_attribute('pa_delka');
            $plotny[$product_id]['sirka'] = $product->get_attribute('pa_sirka');
            $plotny[$product_id]['sila'] = $product->get_attribute('pa_sila');            
            $plotny[$product_id]['orientace'] = $this->get_complex_meta($product_id, 'Orientace dekoru (léta)') == 'Ano' ? 1 : 0;
            $plotny[$product_id]['kategorie'] = $this->set_category($product_id);
        }
       
       return $plotny;
    }
    
    private function set_category($product_id){
        
        // check if PD - pracovni deska
        $terms = wp_get_post_terms( $product_id, 'product_cat' );

        $product_categories = array();
        foreach ( $terms as $term ) {
            $product_categories[] = $term->term_id;
        }
        
        $category = in_array(PDK_CATEGORY_ID, $product_categories) ? 'PD' : 'NA';
        
        // check if K - kolekce, for unregistered customers only
        if(!(new User())->is_registered()){
            if (has_term(NF_KOLEKCE_TAG, 'product_tag', $product_id )) $category = 'K';
        }
                
        return $category;
    }
    
    // find meta wich is stored in complex structure - ACF params (begins with _params_0_param-name)
    private function get_complex_meta($product_id, $meta_name){
        $all_meta = get_post_meta($product_id);
        $desired_value = NULL;
        foreach ($all_meta as $key => $values) {
            if (strpos($key, 'param-name') !== false && in_array($meta_name, $values)) {
                $index = explode('_', $key)[1];                                 // Extract the index number from the key
                $value_key = 'params_' . $index . '_param-value';               // Use the index to find the associated value
                if (isset($all_meta[$value_key])) {
                    $desired_value = $all_meta[$value_key][0];
                    break;
                }
            }
        }
        return $desired_value;
    }
}