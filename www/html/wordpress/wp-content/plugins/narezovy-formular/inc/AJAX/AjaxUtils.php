<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\AJAX;
use Inc\Base\BaseController;

class AjaxUtils extends BaseController {
    
    public function register(){
        add_action( 'wp_ajax_get_desky', array((new Desky()), 'get_desky'));
        add_action( 'wp_ajax_nopriv_get_desky', array((new Desky()), 'get_desky'));
        add_action( 'wp_ajax_get_hrany_list', array((new HranyList()), 'get_hrany_list'));
        add_action( 'wp_ajax_nopriv_get_hrany_list', array((new HranyList()), 'get_hrany_list'));
        add_action( 'wp_ajax_get_hrany_dimensions', array((new HranyDimensions()), 'get_hrany_dimensions'));
        add_action( 'wp_ajax_nopriv_get_hrany_dimensions', array((new HranyDimensions()), 'get_hrany_dimensions'));
        add_action( 'wp_ajax_import_upload', array((new ImportUpload()), 'import_upload'));
        add_action( 'wp_ajax_nopriv_import_upload', array((new ImportUpload()), 'import_upload'));
        add_action( 'wp_ajax_optimize', array((new Optimize()), 'optimize'));
        add_action( 'wp_ajax_nopriv_optimize', array((new Optimize()), 'optimize'));
        
        add_action('wp_footer', array($this, 'inject_global_urls'));
    }
    
    public function inject_global_urls(){                                          // print wp and ajax paths to footer so they can be used by jQuery functions (WP instalation url may vary). 
        
        if((new \Inc\Base\CustomPostTypeController())->is_NF_page()){
            $wp_url = site_url();
            $ajax_url = admin_url('admin-ajax.php');
            echo '
                <script type="text/javascript">
                    var NF_wpUrl = "' .$wp_url .'";
                    var NF_ajaxUrl = "' .$ajax_url .'";
                </script>
            ';
        }
    }
           
    
    public function get_product_categories($product_id, $form = 'id'){
        
        $product_categories = wp_get_post_terms($product_id, 'product_cat');

        if (!empty($product_categories) && !is_wp_error($product_categories)) {
            foreach ($product_categories as $category) {
                $category_id[] = $category->term_id;
                $category_name[] = $category->name;
                $category_slug[] = $category->slug;
            }
        } 
        
        if($form == 'id') return $category_id;
        if($form == 'name') return $category_name;
        if($form == 'slug') return $category_slug;
    }
    
    // upravi nazev hrany tak, ze vrati pole (nazev, rozmer)
    public function shorten_hrana_title($hrana){

        if(!$hrana) return ['decor' => 'JIŽ NENÍ V PRODEJI', 'rozmer' => ''];
        $title = $hrana->get_name();

        $hvezdicka = strpos($title, '*');                                   // najdu si pozici hvezdicky, ktera znaci rozmer napr. 8*20
        $product_decor = substr($title, 0, $hvezdicka);                     // oriznu vse od hvezdicky (vcetne) do prava

        $konec_nazvu = strrpos($product_decor, ' ');                        // najdu si pozici posledni mezery v retezci, abych mohl odriznout cislo pred hvezdickou
        $product_rozmer = trim(substr($title, $konec_nazvu));               // odstranim vsechen text pred rozmerem

        $konec_rozmeru = strpos($product_rozmer, ' ');                                                   // najdu si pozici prvni mezery v retezci, abych mohl odriznout to, co je za rozmerem
        if($konec_rozmeru !== false) $product_rozmer = substr($product_rozmer, 0, $konec_rozmeru);       // pokud neni rozmer az uplne na konci retezce, odstranim vsechen text za rozmerem

        $product_decor = substr($title, 0, $konec_nazvu);                   // oriznu vse od mezery (vcetne) do prava
        $product_decor = trim($product_decor);                              // oriznu pripadne mezery na zacatku a na konci             

        return ['decor' => $product_decor, 'rozmer' => $product_rozmer];
    }    
}