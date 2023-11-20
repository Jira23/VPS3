<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;

    // upraveny puvodni kod od Distro, je ve snippetech pod nazvem "Related Products by UID and by Title"
    // fce UID and by Title je upravena tak, ze si z puvodniho kodu bere kodem pripravenou sql query a tu posle jako dotaz do db
    // Hleda Související sortiment (příslušenství) pro desky (tj. hrany). Kod je upraven tak, aby prijimal id hrany. Puvodni kod je psany pro stranku produktu, kdy id desky je zname ze stranky produktu.

    // has related products - 'DTD dyha BOR. 2800*2070*19';
    // no related products 'L U708 PM/ST9 mat. 2800*2070*18 PerfectSense';

    class HranyDimensions extends AjaxUtils {
        
        public function get_hrany_dimensions() {
            
            if(isset($_POST['product_id'])) $product_id = (int)sanitize_text_field($_POST['product_id']);
            if(isset($_POST['tupl'])) $tupl = sanitize_text_field($_POST['tupl']);
            if(isset($_POST['dekor'])) $dekor = sanitize_text_field($_POST['dekor']);                           // this method can be called 2 ways. Edge is to be found by deska id (privzorovana) or by decor name (odlisna). When odlisna, variable decor is defined in call

            if($product_id == NULL) wp_die();
            if(!is_int($product_id)) wp_die();
            
            $product = wc_get_product($product_id);
            
            if(in_array(MDF_LAKOVANE_CATEGORY_ID, $product->category_ids) && $product->get_attribute('pa_sila') == '3') {       // if is in category "MDF Lakovane" and has sila = "3", deska will be without edges
                self::assembleResponse(array());
                wp_die();
            }    

            if($dekor == ''){
                $hrany = wc_get_products(array('include' => $this->getRelatedProducts($product_id),'status' => 'publish'));     // get hrany by id of deska
//$hrany = wc_get_products(array('include' => array(13439, 16269, 13247, 12910),'status' => 'publish'));            
            } else {
                $hrany = $this->getHranyByDekor($dekor);                                                                        // get hrany by keyword in input hrany
            }

            $hrany_filtered = $this->filter_hrany($hrany, in_array(PDK_CATEGORY_ID, $product->category_ids), $tupl);

            self::assembleResponse($hrany_filtered);
            wp_die();
        }
        
        public function getRelatedProducts($product_id) {
            $toReturn = $this->getRelatedProductsByUID($product_id);
            if(empty($toReturn)) $toReturn = $this->getRelatedProductsByTitle(get_the_title($product_id));      // pokud nema deska related products, najdu je podle nazvu
            return ($toReturn);
        }        
        
        // najde related products na zaklade id desky. Prima title desky (podle nej najde jeji id)
        private function getRelatedProductsByUID($UID){

            global $wpdb;
            
            $related_items = get_field( "related-products" , $UID) ?: [];	
if($_SERVER['SERVER_ADDR'] == '194.182.64.183') $related_items = array(0 => array(0 => '{0101E00B-75F4-429F-94EA-390FA16236B2}'), 1 => array(0 => '{9B173F34-0AA3-462B-896C-56DC99202CFC}'));   // for testing outside DOD server. Simulates related products

            $items = [];

            foreach ($related_items as $item ) {
                array_push($items, array_values($item)[0]);
            }

            if (empty($items)) {
                array_push($items, 'totalgibrish');
            }	

            $query = new \WP_Query();
            $query->set('post_type', 'product');
            $query->set('force_no_results', true);

            $metaQuery = array(
                'post_type' => 'product',
                'meta_query' => array(
                'relation' => 'AND',
                    array(
                       'key' => 'uid',
                        'value'  => $items,
                        'compare'      => 'in'
                    ),
                ),
            );

            $query->set( 'meta_query', $metaQuery );
           $posts = $query->get_posts();
            $relatedProducts = array();
            foreach ( $posts as $post ) {
                    $relatedProducts[] = $post->ID;
            }

            return ($relatedProducts);
        }          
        
        
        // najde related products na zaklade nazvu desky.
        private function getRelatedProductsByTitle($productTitle){

            global $wpdb;

            $query = new \WP_Query();

            $match_array = explode(" ", $productTitle);
            $query->set('exact', true);
            $match_string = $match_array[1] . " " . $match_array[2];

//            if (array_intersect($product->category_ids, ["2265","2276"]) == null);

            $items = [];
            $myproducts = $wpdb->get_results( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title LIKE '%s'", '%'. $wpdb->esc_like( $match_string ) .'%') );

            if ($myproducts) {
                foreach ($myproducts as $item ) {
                    $products_ids[] = $item->ID;
                }
            }
     
            $sql  = $wpdb->prepare("
                SELECT SQL_CALC_FOUND_ROWS wp_posts.ID
                FROM wp_posts
                LEFT JOIN wp_term_relationships
                ON (wp_posts.ID = wp_term_relationships.object_id)
                WHERE 1=1
                AND wp_posts.ID IN (" .implode(',', $products_ids) .")
                AND ( ( ( wp_term_relationships.term_taxonomy_id IN (2256,2257,2258,2275,2290,2360,2401,2891,2937) ) ) )
                AND wp_posts.post_type = 'product'
                AND ((wp_posts.post_status = 'publish'))
                GROUP BY wp_posts.ID
                ORDER BY wp_posts.post_title ASC
                LIMIT 0, 20"
            );
            
            // 2256,2257,2258,2275,2290,2360,2401,2891,2937 jsou podkategorie 2256 (vcetne). 2256 - Hrany + ABS
            
            $results = $wpdb->get_results($sql);
            
            $relatedProducts = array();
            foreach ($results as $row) {
                $relatedProducts[] = $row->ID;
            }            

            return ($relatedProducts);
        }            

        private function getProductIdByTitle($productTitle) {

            $product_id = 0;                                                    // Návratová hodnota v případě, že produkt nebyl nalezen
            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                's'          => $productTitle                                   // "s" je parametr pro vyhledání názvu nebo nadpisu
            );

            $query = new \WP_Query($args);
            if ($query->have_posts()) {
                $query->the_post();
                $product_id = get_the_ID();
            }

            wp_reset_postdata();
            return $product_id;
 
        }
        
        private function getHranyByDekor($dekor){
            $products = wc_get_products(array(
                'status' => 'publish',
                's' => $dekor,
                'limit' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => 'hrany-abs',
                    ),
                ),
            ));            
            
            return $products;
        }
        
        private function filter_hrany($hrany, $is_PDK, $tupl){
            foreach ($hrany as $key => $hrana ) {

                if($hrana->get_attribute('pa_provedeni') == 'S lepidlem') unset($hrany[$key]);      // vyrazuju produkty, ktere maji parametr "Provedeni" = "S lepidlem"
                
                $sirka = (int)$hrana->get_attribute('pa_sirka');
                if($is_PDK){                                                    // PDK - "pracovní desky kuchynske"
                    if($sirka < 40 || $sirka > 45) unset($hrany[$key]);         // vyhodim vsechny hrany, ktere nemaji tloustku 40 az 45
                } else{
                    // selekce pri zadani tuplu - pokud je tupl 30mm nebo 36mm, vyhodim vsechny hrany, ktere nemaji tloustku 42 nebo 43 a pokud je tupl ne, vyhodim ty ktere nemaji tloustlu 22,23,24 a 28
                    if($tupl !== 'NE'){
                        if(!in_array($sirka, array(42, 43))) unset($hrany[$key]);
                    } else {
                        if(!in_array($sirka, array(22, 23, 24, 28))) unset($hrany[$key]);
                    }
                }
            }

            $sorted = wc_products_array_orderby( $hrany, 'title', 'DESC' );                                       // srovnam podle abecedy
            return $sorted;
        }
        
        public static function assembleResponse($hrany){
            //var_dump($hrany);
            echo '<option value="0">bez hrany</option>' .PHP_EOL;
            foreach ($hrany as $hrana) {
                $to_show = (new self())->shorten_hrana_title($hrana);
                echo '<option value="' .$hrana->get_id() .'">' .$to_show['decor'] .' ' .$to_show['rozmer'] .'</option>' .PHP_EOL;
            }
        }
        
    }
