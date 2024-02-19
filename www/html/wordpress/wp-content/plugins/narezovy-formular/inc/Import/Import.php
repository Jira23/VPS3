<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Import;

class Import {
    
    public function init_import(){
        $user_import_type = get_user_meta(get_current_user_id(), 'nf_import_select', true);
        $import_class = 'Inc\Import\Import' .$user_import_type;
        
        $converted = (new $import_class())->import();

        $is_error = in_array(false, array_column($converted["errors"], "status"), true);

        $new_form_id = false;
        if(!$is_error) $new_form_id = $this->create_form($converted['data']);
        
        $to_return = ['errors' => $converted['errors'], 'new_form_id' => $new_form_id];
        
        return $to_return;
    }
    
    private function create_form($data){
        global $wpdb;
        $form_data = [
            'userId' => get_current_user_id(),
            'nazev' => 'Import ' .date("d.m.Y H:i"),
            'olepeni' => 0,
            'stitky' => 0,
            'doprava' => 1
        ];
        
        $wpdb->insert(NF_FORMULARE_TABLE, $form_data);
        $form_id = $wpdb->insert_id;
        
        foreach ($data as $part_data) {
            $wpdb->insert(NF_DILY_TABLE, array_merge(['form_id' => $form_id], $part_data));
        }
        
        return $form_id;
    }
    
}