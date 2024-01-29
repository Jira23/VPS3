<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\Tags;

use Inc\Base\BaseController;

class Alert extends BaseController {
    
    public function render_alert($text, $type = 'warning', $hidden = false, $id = ''){
        
        $types = [
            'error' => ['dashicons-dismiss', '#ff2e2e'],
            'warning'=> ['dashicons-warning', 'orange'],
            'success' => ['dashicons-yes-alt', '#379f2e']
            ];
        
        $style = 'background-color: ' .$types[$type][1] .';';
        if($hidden) $style .= ' display:none;';
        
        ?>
            <div class="NF-alert" style="<?php echo $style; ?>" <?php if($id) echo ' id = "' .$id .'"'; ?> >
              <span class="dashicons <?php echo $types[$type][0]; ?> alert-icon"></span>
              <div class="alert-content">
                <h4><?php echo $text; ?></h4>
              </div>
            </div>
        <?php
    }
}
