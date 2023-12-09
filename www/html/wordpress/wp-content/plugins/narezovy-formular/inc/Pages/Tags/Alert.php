<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags;

use Inc\Base\BaseController;

class Alert extends BaseController {
    
    public function render_alert($text, $type = 'warning'){
        
        $types = [
            'error' => ['dashicons-dismiss', '#ff2e2e'],
            'warning'=> ['dashicons-warning', 'orange'],
            'success' => ['dashicons-yes-alt', '#379f2e']
            ]
        
        ?>
            <div class="NF-alert" style="background-color: <?php echo $types[$type][1]; ?> ;">
              <span class="dashicons <?php echo $types[$type][0]; ?> alert-icon"></span>
              <div class="alert-content">
                <h4><?php echo $text; ?></h4>
              </div>
            </div>
        <?php
    }
}
