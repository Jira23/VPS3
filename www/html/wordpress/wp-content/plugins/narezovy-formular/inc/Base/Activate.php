<?php
/**
 *  @package  narezovy-formular
 */
namespace Inc\Base;

class Activate{
    
    public static function activate() {
        //flush_rewrite_rules();
        
        $activator = new self();
        $activator->insert_posts();                                             // add posts (pages)
        $activator->create_formulare_table();                                   // add tables
        $activator->create_dily_table();
    }

    private function insert_posts() {
        $page_editor_args = array(
            'post_title'   => __('Nářezový formulář editor', NF_PAGE_EDITOR_SLUG),
            'post_content' => '[narezovy-formular-editor]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        );
        
        $editor_page_id = wp_insert_post($page_editor_args);
        add_option(NF_PAGE_EDITOR_OPTION_NAME, $editor_page_id);                // Save page id to the database.        
        
        $page_forms_list_args = array(
            'post_title'   => __('Nářezový formulář seznam', NF_PAGE_FORMS_LIST_SLUG),
            'post_content' => '[narezovy-formular-seznam]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        );        
        $forms_list_page_id = wp_insert_post($page_forms_list_args);
        add_option(NF_PAGE_FORMS_LIST_OPTION_NAME, $forms_list_page_id);
        
        $page_register_user_args = array(
            'post_title'   => __('Nářezový formulář registrace', NF_PAGE_REGISTER_USER_SLUG),
            'post_content' => '[narezovy-formular-register]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        );        
        $forms_register_user_id = wp_insert_post($page_register_user_args);
        add_option(NF_PAGE_REGISTER_USER_OPTION_NAME, $forms_register_user_id);

        $page_import_args = array(
            'post_title'   => __('Nářezový formulář import', NF_PAGE_IMPORT_SLUG),
            'post_content' => '[narezovy-formular-import]',
            'post_status'  => 'publish',
            'post_type'    => 'page'
        );        
        $forms_import_id = wp_insert_post($page_import_args);
        add_option(NF_PAGE_IMPORT_OPTION_NAME, $forms_import_id);        
        
    }
    
    public function create_formulare_table() {
        global $wpdb;
        $table_name = $wpdb->prefix .NF_FORMULARE_TABLE;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userId TINYTEXT,
                nazev TINYTEXT,
                olepeni ENUM('0', '1'),
                stitky ENUM('0', '1'),
                doprava ENUM('0', '1', '2'),
                poznamka TEXT,
                odeslano ENUM('0', '1') NOT NULL DEFAULT '0'
                poptano ENUM('0', '1'),
                datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }  
    
    private function create_dily_table() {
        global $wpdb;
        $table_name = $wpdb->prefix .NF_DILY_TABLE;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                form_id INT(11),
                lamino_id INT(11),
                hrana ENUM('-1', '0', '1'),
                hrana_id INT(11),
                lepidlo TINYTEXT,
                nazev_dilce TINYTEXT,
                ks INT(11),
                delka_dilu INT(11),
                sirka_dilu INT(11),
                orientace ENUM('0', '1'),
                hrana_horni TINYTEXT NULL,
                hrana_leva TINYTEXT NULL,
                hrana_prava TINYTEXT NULL,
                hrana_dolni TINYTEXT NULL,
                tupl ENUM('NE', '30mm', '36mm', '36mm-bila'),
                fig_name TINYTEXT NULL,
                fig_part_code TINYTEXT NULL,
                fig_formula TINYTEXT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }    
    
}