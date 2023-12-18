<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Base;

use Inc\Base\BaseController;

class Enqueue extends BaseController {
    public function register() {
        if((new CustomPostTypeController())->is_NF_page()){
            add_action( 'wp_enqueue_scripts', array($this, 'enque_scripts_and_styles'));
        }
    }
    
    public function enque_scripts_and_styles() {
        wp_enqueue_style('dashicons-manualy', includes_url('css/dashicons.min.css'));                                   // dashicons are not loaded automatically for unkown reason
        wp_enqueue_style('nf-pluginstyle', $this->plugin_url .'assets/css/narezovy-formular.css');
        wp_enqueue_style('nf-pickletree-pluginstyle', $this->plugin_url .'assets/css/pickletree.css');
        wp_enqueue_style('nf-query-ui-pluginstyle', $this->plugin_url .'assets/css/jquery-ui.css');
        wp_enqueue_style('nf-lightbox-css', $this->plugin_url .'assets/css/simple-lightbox.css');
        wp_enqueue_script('nf-pickletree', $this->plugin_url .'assets/js/pickletree.js');
        wp_enqueue_script('nf-ajax-calls', $this->plugin_url .'assets/js/ajax-calls.js');
        wp_enqueue_script('nf-pickletree-helpers', $this->plugin_url .'assets/js/pickletree-helpers.js');
        wp_enqueue_script('nf-ajax-response-manager', $this->plugin_url .'assets/js/ajax-response-manager.js');                
        wp_enqueue_script('nf-form-helpers', $this->plugin_url .'assets/js/form-helpers.js');
        wp_enqueue_script('nf-form-utils', $this->plugin_url .'assets/js/form-utils.js');
        wp_enqueue_script('nf-form-figures', $this->plugin_url .'assets/js/form-figures.js');
        wp_enqueue_script('nf-formsList-utils', $this->plugin_url .'assets/js/formsList-utils.js');
        wp_enqueue_script('nf-modal-utils', $this->plugin_url . 'assets/js/modal-utils.js');
        wp_enqueue_script('nf-jquery-ui', $this->plugin_url .'assets/js/jquery-ui.js');
        wp_enqueue_script('nf-import', $this->plugin_url . 'assets/js/import.js');
        wp_enqueue_script('nf-lightbox-js', $this->plugin_url .'assets/js/simple-lightbox.jquery.js');        
        wp_enqueue_script('nf-lightbox-js-helpers', $this->plugin_url .'assets/js/simple-lightbox-helpers.js');        
        
    }
}
