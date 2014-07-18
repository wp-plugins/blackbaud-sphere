<?php

/*
Plugin Name: Sphere
Description: Integrates Blackbaud Sphere Friends Asking Friends with WordPress by pulling participant reports and returning the values in WordPress for custom leaderboards using both template tags and shortcodes.
Author: CMS Code, Inc.
Version: 1.1.5
Author URI: http://www.cmscode.com
*/



define('SPHERE_PATH', plugin_dir_path(__FILE__));

define('SPHERE_SYNC_PATH', SPHERE_PATH . 'sync/');

define('SPHERE_SYNC_SCRIPT', SPHERE_SYNC_PATH . 'sync_participants.php');



define('SPHERE_VERSION', '6');



register_activation_hook(__FILE__, 'sphere_install');

register_deactivation_hook(__FILE__, 'sphere_uninstall');

function sphere_install() {

 

	global $wpdb;



	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

       	

	if (!is_multisite()) {

		$data_table_name = $wpdb->prefix . "supporters";

		$sql = "CREATE TABLE {$data_table_name} (

			`id` int(11) NOT NULL AUTO_INCREMENT,

			`type` varchar(64) NOT NULL,

			`supporter_id` int(11) NOT NULL,

			`lname` char(100) NOT NULL,

			`fname` char(100) NOT NULL,

			`event_id` int(11) NOT NULL,

			`event_name` char(100) NOT NULL,

			`amount_raised` decimal(10,2) NOT NULL,

			`donations_raised` decimal(10,2) NOT NULL,

			`team_id` char(100) NOT NULL,

			`team_name` char(100) NOT NULL,

			`team_leader` tinyint(1) NOT NULL,

			`date_created` int(11) NOT NULL,

			`last_modified` int(11) NOT NULL,

			UNIQUE KEY `id` (`id`),

			KEY `supporter_id` (`supporter_id`),

			KEY `event_id` (`event_id`)

		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";

		dbDelta($sql);

	} else {

		// get blog lists

		$blog_lists = $wpdb->get_results("SELECT * FROM $wpdb->blogs");



		// create table for each blog

		foreach ($blog_lists as $blog) {

			switch_to_blog($blog->blog_id);



			$data_table_name = $wpdb->prefix . "supporters";

			$sql = "CREATE TABLE {$data_table_name} (

				`id` int(11) NOT NULL AUTO_INCREMENT,

				`type` varchar(64) NOT NULL,

				`supporter_id` int(11) NOT NULL,

				`lname` char(100) NOT NULL,

				`fname` char(100) NOT NULL,

				`event_id` int(11) NOT NULL,

				`event_name` char(100) NOT NULL,

				`amount_raised` decimal(10,2) NOT NULL,

				`donations_raised` decimal(10,2) NOT NULL,

				`team_id` char(100) NOT NULL,

				`team_name` char(100) NOT NULL,

				`team_leader` tinyint(1) NOT NULL,

				`date_created` int(11) NOT NULL,

				`last_modified` int(11) NOT NULL,

				UNIQUE KEY `id` (`id`),

				KEY `supporter_id` (`supporter_id`),

				KEY `event_id` (`event_id`)

			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";

			dbDelta($sql);



			restore_current_blog();

		}

	}

   update_site_option('sphere_version', SPHERE_VERSION);

}

function sphere_uninstall() {

      delete_option( 'sphere_access_activation_key');

	  delete_option( 'sphere_access_admin_url');		

      delete_option( 'sphere_access_admin_username');		



}





add_action('plugins_loaded', 'sphere_update_db_check');

function sphere_update_db_check() {

	if (SPHERE_VERSION != get_site_option('sphere_version')) {

		sphere_install();

	}

  }



require_once(dirname(__FILE__) . '/redux/core/framework.php');

require_once(dirname(__FILE__) . '/redux/config/sphere.php');

require_once(dirname(__FILE__) . '/functions/common.php');

require_once(dirname(__FILE__) . '/functions/shortcodes.php');

require_once(dirname(__FILE__) . '/functions/template_codes.php');



//add_action('activated_plugin','save_error');

//function save_error(){

//	file_put_contents(ABSPATH. 'wp-content/error_activation.html', ob_get_contents());

//}