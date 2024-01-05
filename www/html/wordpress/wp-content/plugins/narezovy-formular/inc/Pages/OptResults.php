<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class OptResults {
    
    const DPH = 1.21;
    
    public $form_id;
    public $opt_results;
    
    public function __construct($form_id) {
        $this->form_id = $form_id;
        $this->opt_results = $this->get_opt_results();
    }
    
    public function render_opt_results() {
        $this->render_title();
        if(!empty($this->opt_results)) $this->render_table();
        $this->render_footer();
    }
    
    public function render_table($with_layouts = true){
        $this->render_table_head();
        $this->render_table_content();
        if($with_layouts) $this->render_layouts();
    }
    
    private function render_title(){
    ?>
        <div id="optimized-block" style="margin-top: 40px; <?php if(empty($this->opt_results)) echo 'display: none;'; ?>">
            <hr>
            <h4 class="email-center">Výsledky optimalizace</h4>
            <div id="optimized-results-table">
    <?php                
    }
    
    public function render_table_head(){
    ?>
        <div style="overflow-x: auto;">
            <table id="opt-results-table" class="result-table">
                <thead>
                    <th style="width: 40%">Položka</th>
                    <th style="width: 20%">Cena&nbsp;/&nbsp;MJ</th>
                    <th style="width: 20%">Množství</th>
                    <th style="width: 20%">Celkem</th>
                </thead>
    <?php
    }
    
    public function render_table_content(){
        echo '<tbody>';
        $great_total = 0;
        foreach ($this->opt_results as $row) {
            $row_total = (float)$row->price  * (float)$row->quantity;
            echo '<tr>';
            echo '<td style="width: 40%">' .$row->item_label . '</td>';             
            echo '<td style="width: 20%"><div class="opt-row-upper">' .round($row->price, 2) .'&nbsp;Kč&nbsp;/&nbsp;' .$row->unit_name .'</div><div class="opt-row-lower">' .round($row->price * self::DPH, 2) .'&nbsp;Kč&nbsp;s&nbsp;DPH</div></td>';
            echo '<td style="width: 20%">' .$row->quantity .'&nbsp;' .$row->unit_name  .'</td>';
            echo '<td style="width: 20%"><div class="opt-row-upper">' .round($row_total, 2) .'&nbsp;Kč</div><div class="opt-row-lower">' .round($row_total * self::DPH, 2) .'&nbsp;Kč&nbsp;s&nbsp;DPH</div></td>';
            echo '</tr>';
            $great_total += round($row_total, 2);
        }

        echo '</tbody>';
        ?>
            <tfoot>
                <tr>
                    <td colspan="3" class="opt-total-upper">Celkem</td>
                    <td class="total-cost"><div class="opt-total-upper"><?php echo $great_total; ?>&nbsp;Kč</div><div class="opt-total-lower"><?php echo round($great_total * self::DPH, 2); ?>&nbsp;Kč&nbsp;s&nbsp;DPH</div></td>
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
            $this->opt_results = $wpdb->get_results("SELECT * FROM `" .NF_OPT_RESULTS_TABLE ."` WHERE `form_id` LIKE '" .$this->form_id ."' ORDER BY `id` ASC");
        }
        
        return $this->opt_results;        
    }
    
    private function get_orders_layouts(){
        if(isset($this->opt_results[0]->layouts)){
            return json_decode($this->opt_results[0]->layouts);
        }
    }
}


