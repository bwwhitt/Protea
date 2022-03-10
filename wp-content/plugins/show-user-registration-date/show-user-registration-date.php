<?php
/**
* Plugin Name: Show User Registration Date
* Plugin URI: https://carlosmr.com/show-user-registration-date
* Description: This plugin shows the registed date field in the table of the Users section in the WordPress dashboard.
* Version: 1.0.0
* Author: Carlos Mart&iacute;nez Romero
* Author URI: https://carlosmr.com
* License: GPL+2
* Text Domain: show-user-registration-date
* Domain Path: /languages
*/
// Starts the plugin
add_action( 'plugins_loaded', 'cmr_suregd_execute' );
function cmr_suregd_execute(){
add_filter('manage_users_columns', 'cmr_suregd_add_upwd_col');
	function cmr_suregd_add_upwd_col($columns) {
	    $columns['user_registered'] = 'Registration date';
	    return $columns;
	}
	 
	add_action('manage_users_custom_column',  'cmr_suregd_show_upwd_col_data', 10, 3);
	function cmr_suregd_show_upwd_col_data($value, $column_name, $user_id) {
	    $user = get_userdata( $user_id );
	    if ( 'user_registered' == $column_name )
	        return $user->user_registered;
	    return $value;
	}
}