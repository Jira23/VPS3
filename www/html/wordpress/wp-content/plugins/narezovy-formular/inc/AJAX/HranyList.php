<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

class HranyList extends AjaxUtils{
    
    public function get_hrany_list() {
        
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);        
        
        $keyword = sanitize_text_field($_POST['keyword']);
        
        $products = wc_get_products(array(
            'status' => 'publish',
            's' => $keyword, 
            'limit' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => 'hrany-abs',
                    ),
                )            
        ));
   
        echo '<thead><th colspan="3"><h3>Klikněte na požadovaný produkt...</h3></th></thead>';        
        
        $productCount = 0;
        $decors_to_show = array();
        foreach ($products as $product) {
            
            $product_decor = $this->shorten_hrana_title($product)['decor'];
            
            if(in_array($product_decor, $decors_to_show)) continue;                                                                         // pokud jsem dekor jiz zobrazil, preskocim ho. Delam to proto, aby se v seznamu neduplikovali nazvy dekoru. Tim ze odstranim z nazvu vse za hvezdickou, zobrazovaly by se pak nazvy vicekrat.
            $decors_to_show[] = $product_decor;                                                                                             // do pole zapisuji jiz zobrazene dekory            
            
            self::assembleResponse($product);                                                                                               // sestavi html odpoved - udaje o danem produktu
            $productCount++;

            if($productCount > 50) {                                                                                                        // pokud je pocet zobrazenych produktu vyssi nez  limit, prerusim vykreslovani
                echo '<tr><td colspan="2"><h3>Nejsou zobrazeny všechny výsledky. Upřesněte zadání.</h3></td></tr>';
                break;
            }
        }

        if($productCount == 0 ) echo '<tr><td colspan="2"><h4 style="color: red;">Nic nenalezeno! Zkuste jiný výraz.</h4></td></tr>';        // pokud nic nenaleznu, vyhodim chybovou hlasku
        wp_die();
    }    
    
    public static function assembleResponse($product){
        $img_url = (new self())->get_product_image_url($product);
        
        $stock_status = get_post_meta($product->get_data()['id'], 'stockstatus', true);
        $in_stock = (strpos($stock_status, "Není na skladě") === 0) ? '<span class="NF-modal-list-not-in-stock">na dotaz</span>' : '<span class="NF-modal-list-in-stock">skladem</span>';        
        
        echo '<tr><td width="25%"><img src="' .$img_url .'" style="max-width: 50%;" /></td>' .PHP_EOL;
        echo '<td>' .(new self())->shorten_hrana_title($product)['decor'] .'</td>' .PHP_EOL;
        echo '<td width="15%">' .$in_stock .'</td>' .PHP_EOL;
        echo '<td hidden id="selected_product_param">' .json_encode(array('id' => $product->get_data()['id'], 'name' => (new self())->shorten_hrana_title($product)['decor'], 'imgUrl' => $img_url)) .'</td>' .PHP_EOL;
        echo '</tr>' .PHP_EOL;
    }
}
