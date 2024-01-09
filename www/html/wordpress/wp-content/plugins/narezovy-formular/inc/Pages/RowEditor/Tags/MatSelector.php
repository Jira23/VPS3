<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class MatSelector extends \Inc\Base\BaseController{
    
    public function render($product_title, $img_path = '', $id = ''){
        if($product_title == NULL){
            echo '<div class="mat-selector" id="' .$id .'">';
            echo '    <div class="mat-icon">'; 
            echo '        <img src="'.$this->plugin_url .'assets/img/icon_plus.png' .'">';
            echo '    </div>';
            echo '    <div class="mat-title">' .$product_title .'</div>';
            echo '</div>';
        } else {
            echo '<div class="mat-selector" id="' .$id .'">';
            echo '    <div class="mat-icon">'; 
            echo '        <img src="'.$img_path .'">';
            echo '    </div>';
            echo '    <div class="mat-title">' .$product_title .'</div>';
            echo '</div>';
        }
    }
    
}
