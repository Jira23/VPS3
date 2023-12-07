<?php 
/**
 * @package  narezovy-formular
 */
namespace Inc\Base;

class BaseController {
	public $plugin_path;
	public $plugin_url;
	public $plugin;
        public $editor_page;
        public $forms_list_page;
        public $register_user_page;
        public $import_page;

	public function __construct() {
            $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
            $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
            $this->plugin = plugin_basename( dirname( __FILE__, 3 ) ) . '/narezovy-formular.php';
            $this->editor_page = home_url() .'/' .NF_PAGE_EDITOR_SLUG;
            $this->forms_list_page = home_url() .'/' .NF_PAGE_FORMS_LIST_SLUG;
            $this->register_user_page = home_url() .'/' .NF_PAGE_REGISTER_USER_SLUG;
            $this->import_page = home_url() .'/' .NF_PAGE_IMPORT_SLUG;
	}
}
