<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

// return list of desky

class Desky extends AjaxUtils {
    
    const MAX_ITEMS_ON_PAGE = 25;
    
    public function get_desky() {
        
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);         
                
        
        $keyword = sanitize_text_field($_POST['keyword']);
        $source = sanitize_text_field($_POST['source']);
        $page = isset($_POST['page']) ? intval($_POST['page']) - 1 : 0;

        // keyword can be category slug (from pickletree) or name/sku (from input)
        if($source == 'input') $products = $this->do_query_product($keyword);
        if($source == 'ptree') $products = $this->do_query_category($keyword);
        if($source == 'ptree-tag') $products = $this->do_query_tag($keyword);
/*     
echo '<pre>';        
var_dump($products);
echo '</pre>';
 */
        
        $filtered_products = $this->filter_products($products);

        if(empty($filtered_products)) {
            echo '<tr><td colspan="2"><h4 style="color: red;">Nic nenalezeno! Zkuste jiný výraz.</h4></td></tr>';
            wp_die();        
        }
        
        echo '<table><thead><th colspan="3"><h3>Klikněte na požadovaný produkt...</h3></th></thead><tbody>';        

        for ($i = (self::MAX_ITEMS_ON_PAGE * $page) + 1 ; $i <= self::MAX_ITEMS_ON_PAGE * ($page + 1) ; $i++) {
            if($i > count($filtered_products)) break;                                                                       // breaks loop if there is no more products in array
            $filter = $filtered_products[$i - 1];
       
            self::assembleResponse($filter['sirka'], $filter['delka'], $filter['sila'], $filter['product']);
        }
        
        echo '</tbody></table>';
        
        if(count($filtered_products) > self::MAX_ITEMS_ON_PAGE) $this->render_pagination($page, count($filtered_products), $source, $keyword);        
        
        
        
        wp_die();
    }    
    
    private function render_pagination($page, $products_count, $source, $keyword){
        $pages_count = ceil($products_count / self::MAX_ITEMS_ON_PAGE);
        $page++;

        echo '<div class="NF-pagination">';
        
        if($page > 1) echo '<button class="button button-main NF-pagination-button" value="' .$page-1 .'" keyword="' .$keyword .'" source="' .$source .'"><<</button>';
        if($page > 3) echo '<button class="button button-main NF-pagination-button" value="1" keyword="' .$keyword .'" source="' .$source .'">1</button><h1>...</h1>';
        if($page - 1 > 0) echo '<button class="button button-main NF-pagination-button" value="' .$page-1 .'" keyword="' .$keyword .'" source="' .$source .'">' .$page-1 .'</button>';
        echo '<button class="button button-main NF-pagination-button NF-pagination-button-current-page" value="' .$page .'" keyword="' .$keyword .'" source="' .$source .'">' .$page .'</button>';
        if($page + 1 < $pages_count + 1) echo '<button class="button button-main NF-pagination-button" value="' .$page+1 .'" keyword="' .$keyword .'" source="' .$source .'">' .$page+1 .'</button>';
        if($page < $pages_count - 2) echo '<h1>...</h1><button class="button button-main NF-pagination-button" value="' .$pages_count .'" keyword="' .$keyword .'" source="' .$source .'">' .$pages_count .'</button>';
        if($page < $pages_count) echo '<button class="button button-main NF-pagination-button" value="' .$page+1 .'" keyword="' .$keyword .'" source="' .$source .'">>></button>';

        echo '</div>';
    }

    // filter all products
    private function filter_products($products){
        $filtered_products = [];
        foreach ($products as $product) {
            $filter = $this->filterDeska($product->ID);
            if($filter !== false) $filtered_products[] = $filter;
        } 
        return $filtered_products;
    }
    
    // returns product with tag (for pickletree)
    public function do_query_tag($tag) {

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'name',
                    'terms'    => $tag
                ),
            ),
        );

        $products = get_posts($args);     
        
        $product_ids = array_map(function($product) {
            return $product->ID;
        }, $products);        
        
        return $products;
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
                'orderby' => 'title',
                'order' => 'ASC'
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
            GROUP BY post.ID
            ORDER BY post.post_title ASC",
                
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

        // modify dimenisoins
        $delka -= 30;
        if(!in_array('Pracovní desky kuchyňské', $product_categories)) $sirka -= 30;                    // no sirka modification for product from category PD
        
        return(array('delka' => $delka, 'sirka' => $sirka, 'sila' => $sila, 'product' => $product));
    }    
    
    public function get_edge_props($product_id){
        $product = wc_get_product($product_id);
        $product_category_ids = $product->get_category_ids();
        $empty_response = ['edgeId' => '', 'edgeName' => '', 'edgeImgUrl' => '', 'edgeDims' => [], 'isPDK' => false];
        if(in_array(MDF_LAKOVANE_CATEGORY_ID, $product_category_ids) && $product->get_attribute('pa_sila') == '3') {       // if is in category "MDF Lakovane" and has sila = "3", deska will be without edges
            return $empty_response;
        }
        
        $hrany = wc_get_products(array('include' => (new HranyDimensions())->getRelatedProducts($product_id),'status' => 'publish'));
        if(empty($hrany) || !isset($hrany[0])) return $empty_response;

        $isPDK = in_array(PDK_CATEGORY_ID, $product_category_ids);
        
        $hrana_dims = [];
        foreach ($hrany as $key => $hrana) {

            $hrana_dims[$hrana->get_id()] = (new self())->shorten_hrana_title($hrana)['rozmer'];
            if($key === 0 ){
                $hrana_id = $hrany[0]->get_id();
                $hrana_title = (new self())->shorten_hrana_title($hrany[0])['decor'];
                $image_url = wp_get_attachment_image_src($hrany[0]->get_image_id())[0];                
            }
        }
        
        return ['edgeId' => $hrana_id, 'edgeName' => $hrana_title, 'edgeImgUrl' => $image_url, 'edgeDims' => $hrana_dims, 'isPDK' => $isPDK];
    }    
    
    public static function assembleResponse($sirka, $delka, $sila, $product){
        //$img_url = wp_get_attachment_image_src( $product->get_image_id())[0];
        $img_url = (new self())->get_product_image_url($product);
        
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

        $stock_status = get_post_meta($params['id'], 'stockstatus', true);
        $in_stock = (strpos($stock_status, "Není na skladě") === 0) ? '<span class="NF-modal-list-not-in-stock">na dotaz</span>' : '<span class="NF-modal-list-in-stock">skladem</span>';
        
        echo '<tr><td width="25%"><img src="' .$img_url .'" style="max-width: 50%;" /></td>' .PHP_EOL;
        echo '<td>' .$product->get_data()['name'] .'</td>' .PHP_EOL;
        echo '<td width="15%">' .$in_stock .'</td>' .PHP_EOL;
        echo '<td hidden id="selected_product_param">' .json_encode($params) .'</td>' .PHP_EOL;
        echo '</tr>' .PHP_EOL;
    }
}

