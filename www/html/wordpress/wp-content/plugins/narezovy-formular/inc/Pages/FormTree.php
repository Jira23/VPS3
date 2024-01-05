<?php

namespace Inc\Pages;

    // generates init script for pickletree (on category tree based selector)
    class FormTree {
        
	public function register(){
            add_action('wp_footer', array( $this, 'initMenu'));
	}        
        
        // inicializuje stromove menu
        public function initMenu(){
            if(!is_page(get_option(NF_PAGE_EDITOR_OPTION_NAME))) return;        // activate on editor page only
            
            echo "
                <script>
                    const tree = new PickleTree({
                        c_target: 'div_tree',
                        c_config: {
                            //start as folded or unfolded
                            foldedStatus: true,
                            //for logging
                            logMode: false,
                            //for switch element
                            switchMode: false,
                            //for automaticly select childs
                            autoChild: false,
                            //for automaticly select parents
                            autoParent: true,
                            //for drag / drop
                            drag: false
                        },
                        c_data:";
            
            echo "[{n_id: " .TOP_CATEGORY_ID .", n_title: 'Deskový materiál', n_parentid: 0},";
            $this->generateMenu(TOP_CATEGORY_ID);                                              // vytvori seznam kategorii podle DOD
            echo "]";
            echo "
                    });
                </script>
            ";
        }
        
        // vykresli menu pro picktree podle menu na DOD
        public function generateMenu($parent_id = 21) {

            $terms = get_terms([
                'taxonomy'    => 'product_cat',
                'hide_empty'  => true,
                'parent'      => $parent_id
            ]);
            
            foreach ( $terms as $term ) {
                $this->generateMenu($term->term_id);
                if(!in_array($term->name, NF_DENIDED_CATEGORIES)){                 // pokud neni kategorie na seznamu zakazanych, vykreslim ji
                    echo "{n_id: " .$term->term_id  .", n_title: '" .$term->name ."', n_parentid: " .$term->parent .", n_addional: '" .$term->slug ."'},";
                }
            }
        }        

}

