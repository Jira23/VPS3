<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

use Inc\Base\User;

class RenderFormsList extends PagesContoller{
    
    function __construct() {
        parent::__construct();
    }

    public function register(){
        add_action('wp_footer', array($this, 'init_tabs'));
    }      
    
    public function init_tabs(){
        if(!is_page(get_option(NF_PAGE_FORMS_LIST_OPTION_NAME))) return;        // activate on forms list page only
        
        echo '  <script>
                    jQuery(document).ready(function($) { 
                        $( "#tabs" ).tabs();
                    } );
                </script>';
    }
    
    public function render_forms_list(){
        if(!(new User())->get_id()) $this->jQuery_redirect($this->register_user_page);                          // unregistered users are redirected to registration page
        $this->render_header();
        $this->render_list();
    }
    
    private function render_header(){
        ?>
        <div style="text-align: left;">
        <?php
            echo '<a href="' .$this->import_page .'">';
            if(get_user_meta(get_current_user_id(), 'nf_import_select', true))$this->button->render_button('import');                       // add import button if user have import allowed
            echo '</a>';             
        ?>
            <a href="<?php echo $this->editor_page; ?>?form_id=0&part_id=0"><?php $this->button->render_button('nove_zadani');?></a>
            <a href="/navod-formular-narez/"><?php $this->button->render_button('navod');?></a>
        </div>
        <?php
    }

    private function render_list(){
        ?>            
        <div id="tabs" style="overflow-x: auto; overflow-y: auto; white-space: nowrap; margin-top: 30px;">
          <ul>
            <li><a href="#tabs-1">Rozepsané</a></li>
            <li><a href="#tabs-2">Odeslané</a></li>
          </ul>
          <div id="tabs-1">
            <table id="rozepsane" class="shop_table cart wishlist_table wishlist_view traditional">
                <?php $this->render_rows(); ?>
            </table>
          </div>
          <div id="tabs-2">
            <table id="odeslane" class="shop_table cart wishlist_table wishlist_view traditional">
                <?php $this->render_rows(1); ?>
            </table>
          </div>
        </div>
        <?php    
    }
    
    private function get_forms_list($odeslano = 0){
        global $wpdb;
        $user_id = (new User())->get_id();

        $prepared_statement = $wpdb->prepare("SELECT * FROM " .NF_FORMULARE_TABLE ." WHERE userId LIKE %d AND odeslano LIKE %d ORDER BY id DESC", $user_id, $odeslano);
        $results = $wpdb->get_results($prepared_statement, ARRAY_A);
        return($results);
    }
    
    private function render_rows($odeslano = 0){
        $rows = $this->get_forms_list($odeslano);
        if(!$rows){
            $this->render_empty_list();
            return;
        }
        ?>
        <thead>
            <th style="width: 10%;"> Číslo formuláře </th>
            <th style="width: 55%;"> Název formuláře </th>
            <th style="width: 15%;"></th>
            <th style="width: 15%;"> Datum </th>
            <th style="width: 5%;">Úpravy</th>
        </thead>
        <tbody>
        <?php
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td><a href="' .$this->editor_page .'?form_id=' .$row['id'] .'&part_id=0">' .$row['id'] .'</a></td>';
                echo '<td><a href="' .$this->editor_page .'?form_id=' .$row['id'].'&part_id=0">' .$row['nazev'] .'</a></td>';
                echo ($row['poptano'] == 1 && $row['odeslano'] == 0) ? '<td><b>Poptávka odeslána</b></td>' : '<td></td>';
                echo '<td>' .date('j.n.Y', strtotime($row['datum'])) .'</td>';
                echo '<td><form id="parts-list-form" method="post">';
                $this->button->render_button('smazat_formular', null, ['value' => $row['id']]); 
                $this->button->render_button('duplikovat_formular', null, ['value' => $row['id']]); 
                echo '</form></td>';
                echo '</tr>';
            }
            echo '</tbody>';        
    }
    
    private function render_empty_list(){
        echo '<h2>Zatím zde nejsou žádné formuláře.</h2>';
    }    
}
