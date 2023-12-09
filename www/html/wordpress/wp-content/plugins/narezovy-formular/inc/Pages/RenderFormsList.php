<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

use Inc\Base\User;

class RenderFormsList extends PagesController{
    
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
<style>
/* Style for the alert container */
.custom-alert {
  display: flex;
  align-items: center;
  padding: 10px;
  border-radius: 5px;
  background-color: orange;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: fit-content;
  max-width: 80%;
}

/* Style for the alert icon */
.alert-icon {
  color: white;
  margin-right: 10px;
}

/* Style for the content inside the alert */
.alert-content {
  text-align: center;
}

/* Additional styles for the heading and paragraph */
.alert-content h4 {
  margin: 0;
  font-size: 14px;
  color: white;
  line-height: 1.2;
}
</style>








        <div class="top-buttons">
        <?php
            echo '<a href="' .$this->import_page .'">';
            if(get_user_meta(get_current_user_id(), 'nf_import_select', true))$this->button->render_button('import');                       // add import button if user have import allowed
            echo '</a>';             
        ?>
            <a href="<?php echo $this->editor_page; ?>?form_id=0&part_id=0"><?php $this->button->render_button('nove_zadani');?></a>
            <a href="/navod-formular-narez/"><?php $this->button->render_button('navod');?></a>
        </div>
<div class="custom-alert">
  <span class="dashicons dashicons-warning alert-icon"></span>
  <div class="alert-content">
    <h4>Max. počet současně rozpracovaných zakázek je 5!</h4>
  </div>
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
            <table id="rozepsane" class="NF-table">
                <?php $this->render_rows(); ?>
            </table>
          </div>
          <div id="tabs-2">
            <table id="odeslane" class="NF-table">
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
                echo '<tr class="clickable-row" data-href="' .$this->editor_page .'?form_id=' .$row['id'] .'&part_id=0">';
                echo '<td>' .$row['id'] .'</td>';
                echo '<td>' .$row['nazev'] .'</td>';
                if($this->has_opt_results($row['id'])){                                                                                 // "Poptávka odeslána" for old forms before optimalization functionality added
                    echo '<td><b>Optimalizováno</b></td>';
                } else {
                    echo ($row['poptano'] == 1 && $row['odeslano'] == 0) ? '<td><b>Poptávka odeslána</b></td>' : '<td></td>';
                }
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

    private function has_opt_results($form_id){
        global $wpdb;
        $opt_results = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$form_id ."' LIMIT 1");
        return (!empty($opt_results)) ? true : false;
    }    
}
