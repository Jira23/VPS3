<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class OptResults {
    
    public function __construct($form_id) {
        $this->form_id = $form_id;
        $this->opt_results = $this->get_opt_results();
    }
    
    public function render_opt_results() {
        $this->render_title();
        if(!empty($this->opt_results)) $this->render_table();
        $this->render_footer();
    }
    
    public function render_table(){
        $this->render_table_head();
        $this->render_table_content();
        $this->render_layouts();
    }
    
    private function render_title(){
    ?>
        <div id="optimized-block" style="margin-top: 40px; <?php if(empty($this->opt_results)) echo 'display: none;'; ?>">
            <hr>
            <h4>Výsledky optimalizace</h4>
            <div id="optimized-results-table">
    <?php                
    }
    
    private function render_table_head(){
    ?>
        <div style="overflow-x: auto;">
            <table class="result-table">
                <thead>
                    <th style="width: 40%">Položka</th>
                    <th style="width: 20%">Cena</th>
                    <th style="width: 20%">Množství</th>
                    <th style="width: 20%">Celkem</th>
                </thead>
    <?php
    }
    
    private function render_table_content(){
        echo '<tbody>';
        $big_total = 0;
        foreach ($this->opt_results as $row) {
            $row_total = (float)$row->price * (float)$row->quantity;
            echo '<tr>';
            echo '<td style="width: 40%">' . (($row->item_id[0] != 'Z') ? get_post($row->item_id)->post_title : $row->item_id ) . '</td>';          // IDs starting with 'Z' are not products
            echo '<td style="width: 20%">' .$row->price .'</td>';            
            echo '<td style="width: 20%">' .$row->quantity .'</td>';
            echo '<td style="width: 20%" class="item-total">' .$row_total .'</td>';
            echo '</tr>';
            $big_total += $row_total;            
        }

        echo '</tbody>';
        ?>
            <tfoot>
                <tr>
                    <td colspan="3">Celkem</td>
                    <td class="total-cost"><?php echo $big_total; ?></td>
                </tr>
            </tfoot>              
            </table>
        </div>
        <?php 
    }
    
    private function render_layouts(){
        $layouts = $this->get_orders_layouts();
    ?>
        <div class="result-gallery">
            <div class="result-thumbnails">
            <?php
                if(!empty($layouts)){
                    foreach ($layouts as $layout_url) {
                        echo '<a href="'.$layout_url .'"><img src="'.$layout_url .'"/></a>';
                    }
                }
            ?>
            </div>
        </div>
    <?php
    }    
    
    
    private function render_footer(){
    ?>
            </div>
        </div>
    <?php
    }
    
    private function get_opt_results() {
        if(!isset($this->opt_results )){                                              // query will be executed only once per object init
            global $wpdb;
            $opt_results = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY `id` ASC");
            $this->opt_results = $opt_results;
        }
        return $this->opt_results;        
    }
    
    private function get_orders_layouts(){
        if(isset($this->opt_results[0]->layouts)){
            return json_decode($this->opt_results[0]->layouts);
        }
    }
}

