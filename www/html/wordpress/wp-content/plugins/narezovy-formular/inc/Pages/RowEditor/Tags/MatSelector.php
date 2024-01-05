<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\RowEditor\Tags;

class MatSelector {
    
    public function render($product_title, $img_path = '', $id = ''){
        if($product_title == NULL){
            echo '<div class="mat-selector" id="' .$id .'">';
            echo '<span class="dashicons dashicons-plus-alt"></span>';
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
