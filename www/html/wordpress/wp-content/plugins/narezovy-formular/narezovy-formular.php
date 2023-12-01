<?php
/**
 * @package  narezovy-formular
 */
/*
Plugin Name: Narezovy formular
Plugin URI: 
Description: Narezovy formular pro Drevoobchod Dolezal
Version: 1.0.1
Author: RSS
Author URI:
License: GPLv2 or later
Text Domain: Narezovy formular
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

-Copyright 2005-2015 Automattic, Inc.
*/


if ( ! defined( 'ABSPATH' ) ) return;

global $wpdb;

// page constants
define('NF_PAGE_EDITOR_SLUG', 'narezovy-formular-editor');
define('NF_PAGE_FORMS_LIST_SLUG', 'narezovy-formular-seznam');
define('NF_PAGE_REGISTER_USER_SLUG', 'narezovy-formular-registrace');
define('NF_PAGE_IMPORT_SLUG', 'narezovy-formular-import');
define('NF_PAGE_EDITOR_OPTION_NAME', 'narezovy_fromular_page_id');
define('NF_PAGE_FORMS_LIST_OPTION_NAME', 'narezovy_fromular_page_forms_list_id');
define('NF_PAGE_REGISTER_USER_OPTION_NAME', 'narezovy_fromular_page_register_user_id');
define('NF_PAGE_IMPORT_OPTION_NAME', 'narezovy_fromular_page_import_id');

// tables
define('NF_FORMULARE_TABLE', $wpdb->prefix .'nf_formulare');
define('NF_DILY_TABLE', $wpdb->prefix .'nf_dily');
define('NF_OPT_RESULTS_TABLE', $wpdb->prefix .'nf_opt_results');

define( 'DENIDED_CATEGORIES',  array(                                           // kategories, not be visible on deska selection
    'Spárovky - přířezy', 
    'Spárovky smrk - přířezy',
    'Hrany + ABS',
    'Koncovky, rohy k lištám PD',
    'Lišty k PD EGGER',
    'Lišty k PD KRONOSPAN',
    'Lišty k PD ostatní'
));

// for testing outside DOD server.
$_SERVER['SERVER_ADDR'] == '194.182.64.183' ? define('TOP_CATEGORY_ID', 21) : define('TOP_CATEGORY_ID', 2255);
$_SERVER['SERVER_ADDR'] == '194.182.64.183' ? define('PDK_CATEGORY_ID', 44) : define('PDK_CATEGORY_ID', 2276);
$_SERVER['SERVER_ADDR'] == '194.182.64.183' ? define('MDF_LAKOVANE_CATEGORY_ID', 154) : define('MDF_LAKOVANE_CATEGORY_ID', 2405);

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

function activate_narezovy_formular_plugin() {
    Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'activate_narezovy_formular_plugin' );

function deactivate_narezovy_formular_plugin() {
    Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_narezovy_formular_plugin' );

add_action('plugins_loaded', function(){
    if ( class_exists( 'Inc\\Init' ) ) {
        Inc\Init::register_services();
    }
});