<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

// return list of desky

class Desky extends AjaxUtils {
    
    public function get_desky() {
        
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         
                
        
        $keyword = sanitize_text_field($_POST['keyword']);
        $source = sanitize_text_field($_POST['source']);

        // keyword can be category slug (from pickletree) or name/sku (from input)
        if($source == 'input') $products = $this->do_query_product($keyword);
        if($source == 'ptree') $products = $this->do_query_category($keyword);
        
        echo '<thead><th colspan="2"><h3>Klikněte na požadovaný produkt...</h3></th></thead>';        
        
        $productCount = 0;
        foreach ($products as $product) {

            $filter = $this->filterDeska($product->ID);                                                             // odfiltruje nechtene produkty
            if($filter == false) continue;
            

            self::assembleResponse($filter['sirka'], $filter['delka'], $filter['sila'], $filter['product']);       // sestavi html odpoved - udaje o danem produktu
            $productCount++;

            if($productCount > 50) {                                                                                // pokud je pocet zobrazenych produktu vyssi nez  limit, prerusim vykreslovani
                echo '<tr><td colspan="2"><h3>Nejsou zobrazeny všechny výsledky. Upřesněte zadání.</h3></td></tr>';
                break;
            }
        }

        if($productCount == 0 ) echo '<tr><td colspan="2"><h4 style="color: red;">Nic nenalezeno! Zkuste jiný výraz.</h4></td></tr>';        // pokud nic nenaleznu, vyhodim chybovou hlasku
        wp_die();
    }    
    
    // returns product in category (for pickletree)
    public function do_query_category($category_slug) {
        
        $category = get_term_by('slug', $category_slug, 'product_cat');
        if ($category) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => -1,                                            // Get all products
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $category->term_id,
                        'operator' => 'IN',
                    ),
                ),
                'fields' => 'ids',                                              // Retrieve only post IDs
            ));
        }

        // convert to same format as in do_query_product() funcion
        foreach ($products as $product_id) {
            $product_object = new \stdClass();
            $product_object->ID = $product_id;
            $product_ids[] = $product_object;
        }
        return $product_ids;
    }
    
    // returns product ids based on name or sku (for form input)
    public function do_query_product($keyword) {
        global $wpdb;

        $partial_input = '%' . $wpdb->esc_like($keyword) . '%';                                     // Sanitize input for LIKE query.
        $category_id = get_term_by('name', 'Deskový materiál', 'product_cat')->term_id;

        $query = $wpdb->prepare(
            "SELECT post.ID
            FROM $wpdb->posts AS post
            LEFT JOIN $wpdb->postmeta AS meta ON post.ID = meta.post_id
            LEFT JOIN $wpdb->term_relationships AS rel ON post.ID = rel.object_id
            LEFT JOIN $wpdb->term_taxonomy AS tax ON rel.term_taxonomy_id = tax.term_taxonomy_id
            LEFT JOIN $wpdb->terms AS terms ON tax.term_id = terms.term_id
            WHERE post.post_type = 'product'
            AND tax.term_id = %d
            AND (post.post_title LIKE %s OR meta.meta_key = '_sku' AND meta.meta_value LIKE %s)
            GROUP BY post.ID",
            $category_id,
            $partial_input,
            $partial_input
        );

        $results = $wpdb->get_results($query);
        return $results;
    }    
    
    // vraci rozmery desky nebo false, pokud nevyhovuje filtru
    public function filterDeska($product_id){

        $product = wc_get_product($product_id);

        $delka = $product->get_attribute('pa_delka');
        $sirka = $product->get_attribute('pa_sirka');
        $sila = $product->get_attribute('pa_sila');

        if($delka == '' || $sirka == '' || $sila == '') return false;

        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));

        foreach (NF_DENIDED_CATEGORIES as $denided_cat) {                                                  // vyradim kategorie, ktere tam byt nemaji
            if(in_array($denided_cat, $product_categories)) return (false);  
        }

        if(in_array('Dřevotřískové desky', $product_categories)){                                       // pokud se jedna o kategorii Dřevotřískové desky, odectu od max delky a sirky 30mm
            $delka -= 30;
            $sirka -= 30;
        }                                

        return(array('delka' => $delka, 'sirka' => $sirka, 'sila' => $sila, 'product' => $product));
    }    
    
    public function get_edge_props($product_id){
        $product = wc_get_product($product_id);
        if(in_array(MDF_LAKOVANE_CATEGORY_ID, $product->get_category_ids()) && $product->get_attribute('pa_sila') == '3') {       // if is in category "MDF Lakovane" and has sila = "3", deska will be without edges
            return [];
        }
        
        $hrany = wc_get_products(array('include' => (new HranyDimensions())->getRelatedProducts($product_id),'status' => 'publish'));
        if(empty($hrany) || !isset($hrany[0])) return [];

        $hrana_dims = [];
        foreach ($hrany as $key => $hrana) {
            $hrana_dims[$hrana->get_id()] = (new self())->shorten_hrana_title($hrana)['rozmer'];
            if($key === 0 ){
                $hrana_id = $hrany[0]->get_id();
                $hrana_title = (new self())->shorten_hrana_title($hrany[0])['decor'];
                $image_url = wp_get_attachment_image_src($hrany[0]->get_image_id())[0];                
            }
        }
        
        return ['edgeId' => $hrana_id, 'edgeName' => $hrana_title, 'edgeImgUrl' => $image_url, 'edgeDims' => $hrana_dims];
    }    
    
    public static function assembleResponse($sirka, $delka, $sila, $product){
        $img_url = wp_get_attachment_image_src( $product->get_image_id())[0];
        
        $params = array(
            'id' => $product->get_data()['id'],
            'name' => $product->get_data()['name'],
            'sku' => $product->get_data()['sku'],
            'sirka' => $sirka,
            'delka' => $delka,
            'sila' => $sila,
            'isPDK' => in_array(PDK_CATEGORY_ID, $product->category_ids),
            'categoryIds' => $product->get_data()['category_ids'],
            'imgUrl' => $img_url,
        );
        
        echo '<tr><td width="25%"><img src="' .$img_url .'" style="max-width: 50%;" /></td>' .PHP_EOL;
        echo '<td>' .$product->get_data()['name'] .'</td>' .PHP_EOL;
        echo '<td hidden id="selected_product_param">' .json_encode($params) .'</td>' .PHP_EOL;
        echo '</tr>' .PHP_EOL;
    }
}

