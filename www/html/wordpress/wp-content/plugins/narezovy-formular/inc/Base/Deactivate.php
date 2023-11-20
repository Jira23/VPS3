<?php
/**
 *  @package  narezovy-formular
 */
namespace Inc\Base;

class Deactivate{
    public static function deactivate() {
        //flush_rewrite_rules();

        //remove posts (pages)
        $editor_page_id = get_option(NF_PAGE_EDITOR_OPTION_NAME);
        if ($editor_page_id) {
            wp_delete_post($editor_page_id, true);
            delete_option(NF_PAGE_EDITOR_OPTION_NAME);
        }        
        
        $forms_list_page_id = get_option(NF_PAGE_FORMS_LIST_OPTION_NAME);
        if ($forms_list_page_id) {
            wp_delete_post($forms_list_page_id, true);
            delete_option(NF_PAGE_FORMS_LIST_OPTION_NAME);
        }        

        $forms_register_user_id = get_option(NF_PAGE_REGISTER_USER_OPTION_NAME);
        if ($forms_register_user_id) {
            wp_delete_post($forms_register_user_id, true);
            delete_option(NF_PAGE_REGISTER_USER_OPTION_NAME);
        }  

        $forms_import_id = get_option(NF_PAGE_IMPORT_OPTION_NAME);
        if ($forms_import_id) {
            wp_delete_post($forms_import_id, true);
            delete_option(NF_PAGE_IMPORT_OPTION_NAME);
        } 
        
    }
}