<?php

namespace Inc\Pages;

use Inc\Base\BaseController;
use Inc\Base\User;

    // do actions after order is placed
    class OrderHandler extends BaseController{

        public function __construct() {
            parent::__construct();
        }
        
        public function create_order() {
            $this->render_thankyou_page();
        }
        
        private function render_thankyou_page(){
            echo '<h2>Děkujeme. Váš formulář byl odeslán.</h2>';
            $user = new User();
            if($user->is_registered()) {
                echo '<a href="' .$this->forms_list_page .'"><button class="button" type="button">Zpět na seznam</button></a>';
            } else {
                echo '<a href="/"><button class="button" type="button">Zpět na hlavní stranu</button></a>';
                $user->unset_cookies();
            }
            wp_die();
        }        
    }

