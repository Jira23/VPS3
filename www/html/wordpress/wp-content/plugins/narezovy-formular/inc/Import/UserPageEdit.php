<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Import;

// add selectbox to user edit page
class UserPageEdit {
    
    public function register(){
        add_action('edit_user_profile', array($this, 'custom_user_profile_fields'));
        add_action('edit_user_profile_update', array($this, 'save_custom_user_profile_fields'));
    }
    
    public function custom_user_profile_fields($user) {
        if (current_user_can('edit_users', $user->ID)) {
            ?>
            <h3>Nářezový formulář - Import</h3>
            <table class="form-table">
                <tr>
                    <th><label for="nf_import_select">Způsob importu</label></th>
                    <td>
                        <select name="nf_import_select" id="nf_import_select">
                            <option value="0" <?php selected(get_the_author_meta('nf_import_select', $user->ID), '0'); ?>>Žádný</option>
                            <option value="Ceska" <?php selected(get_the_author_meta('nf_import_select', $user->ID), 'Ceska'); ?>>Češka</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php
        }
    }

    public function save_custom_user_profile_fields($user_id) {
        if (current_user_can('edit_users', $user_id)) {
            update_user_meta($user_id, 'nf_import_select', $_POST['nf_import_select']);
        }
    }
    
    
}
