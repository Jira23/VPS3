<?php

namespace Inc\Pages;

    // generates init script for pickletree (on category tree based selector)
    class FormTreeKolekce {
        
	public function register(){
            add_action('wp_footer', array( $this, 'initMenu'));
	}        
        
        // inicializuje stromove menu
        public function initMenu(){
            if(!is_page(get_option(NF_PAGE_EDITOR_OPTION_NAME))) return;        // activate on editor page only
            
            echo "
                <script>
                    const tree_tag = new PickleTree({
                        c_target: 'div_tree_product_tag',
                        c_config: {
                            foldedStatus: true,                                 //start as folded or unfolded
                            logMode: false,                                     //for logging
                            switchMode: false,                                  //for switch element
                            autoChild: false,                                   //for automaticly select childs
                            autoParent: true,                                   //for automaticly select parents
                            drag: false                                         //for drag / drop
                        },
                        c_data:";
            
            echo "[{n_id: 1, n_title: 'Kolekce', n_parentid: 0},";
            $this->generateMenu();                                              // vytvori seznam kategorii podle DOD
            echo "]";
            echo "
                    });
                </script>
            ";
        }
        
        // vykresli menu pro picktree podle menu na DOD
        public function generateMenu() {
            
            echo "{n_id: 4078, n_title: 'Bílá lesk', n_parentid: 1, n_addional: 'Kolekce - Bílá lesk'},";    
            echo "{n_id: 4079, n_title: 'Zlatá', n_parentid: 1, n_addional: 'Kolekce - Zlatá'},";    
            echo "{n_id: 4080, n_title: 'Stříbrná', n_parentid: 1, n_addional: 'Kolekce - Stříbrná'},";    
            echo "{n_id: 4081, n_title: 'Bronzová', n_parentid: 1, n_addional: 'Kolekce - Bronzová'},";    
            echo "{n_id: 4082, n_title: 'Economy', n_parentid: 1, n_addional: 'Kolekce - Economy'},";    
            echo "{n_id: 4083, n_title: 'Ostatní', n_parentid: 1, n_addional: 'Kolekce - Ostatní'},";    
            
            
            /*
            $tags = $this->get_kolekce_tags();
            foreach ($tags as $id => $name) {
                echo "{n_id: " .$id .", n_title: '" .$name ."', n_parentid: 1, n_addional: '" .$name ."'},";    
            }
             * 
             */
        }
        
        
    // not used, finds all tags for kolekce
    private function get_kolekce_tags(){
        
        $args = array(                                                          // Get products with the specified tag
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_tag',
                    'field' => 'slug',
                    'terms' => 'formatovani-odber-jen-to-co-chci-bez-zbytku',
                ),
            ),
        );

        $products_query = new \WP_Query($args);
        $kolekce_tags = [];

        if ($products_query->have_posts()) {
            while ($products_query->have_posts()) {
                $products_query->the_post();
                $product_id = get_the_ID();
                $product_tags = wp_get_post_terms($product_id, 'product_tag');

                if (!empty($product_tags) && !is_wp_error($product_tags)) {
                    foreach ($product_tags as $tag) {
                        if(strpos($tag->name, 'Kolekce -') !== false) $kolekce_tags[$tag->term_id] = $tag->name;
                    }
                }
            }

            wp_reset_postdata();

            $unique_tags = array_unique($kolekce_tags);
            ksort($unique_tags);
            $tags_with_price = $this->set_price($unique_tags);
            return $tags_with_price;
        }        
    }


}

