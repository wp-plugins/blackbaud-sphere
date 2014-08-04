<?php

if (!class_exists("ReduxFramework")) {
	return;
}


if (!class_exists("Redux_Framework_Sphere")) {
	class Redux_Framework_Sphere
	{

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct()
		{
			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Create the sections and fields
			$this->setSections();

			if (!isset($this->args['opt_name'])) { // No errors please
				return;
			}

			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
		}


		/**
		 *
		 * This is a test function that will let you see when the compiler hook occurs.
		 * It only runs if a field        set with compiler=>true is changed.
		 **/

		function compiler_action($options, $css)
		{

		}


		/**
		 *
		 * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		 * Simply include this function in the child themes functions.php file.
		 *
		 * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		 * so you must use get_template_directory_uri() if you want to use any of the built in icons
		 **/

		function dynamic_section($sections)
		{
			return $sections;
		}


		/**
		 *
		 * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
		 **/

		function change_arguments($args)
		{
			return $args;
		}


		/**
		 *
		 * Filter hook for filtering the default value of any given field. Very useful in development mode.
		 **/

		function change_defaults($defaults)
		{
			return $defaults;
		}

		public function setSections()
		{
			$this->sections[] = array(
				'title' => __('Event Settings', 'redux-framework-demo'),
				'icon' => 'el-icon-cogs',
				'fields' => array(
					array(
						'id' => 'event_id',
						'type' => 'text',
						'title' => 'Current Event ID'
					),
					array(
						'id' => 'event_goal',
						'type' => 'text',
						'title' => 'Override Event Goal'
					),
					array(
						'id' => 'event_raised',
						'type' => 'text',
						'title' => 'Override Event Raised'
					),
				),
			);

			$this->sections[] = array(
				'title' => __('Sync Settings', 'redux-framework-demo'),
				'heading' => __('Sphere Settings', 'redux-framework-demo'),
				'icon' => 'el-icon-cogs',
				'fields' => array(
					array(
						'id' => 'username',
						'type' => 'text',
						'title' => 'Username'
					),
					array(
						'id' => 'password',
						'type' => 'text',
						'title' => 'Password'
					),
					array(
						'id' => 'section-participant-start',
						'type' => 'section',
						'title' => __('Participant Reports', 'redux-framework-demo'),
						'indent' => false
					),
					array(
						'id' => 'reports',
						'type' => 'reports',
					),
					array(
						'id' => 'section-participant-end',
						'type' => 'section',
						'indent' => false
					),
				),
			);

			$this->sections[] = array(
				'title' => __('Code Settings', 'redux-framework-demo'),
				'heading' => __('Cron Job', 'redux-framework-demo'),
				'icon' => 'el-icon-cogs',
				'fields' => array(
					array(
						'id' => 'cron-job',
						'type' => 'info',
						'raw' => "<p>For reports to stay current you will need to setup a cron job to run the sync script periodically. Enter the following URL in the cron job : </p><p><strong>php " . SPHERE_SYNC_SCRIPT . ' > /dev/null 2>&1</strong></p>',
					),
					array(
						'id' => 'section-shortcode-start',
						'type' => 'section',
						'title' => __('Shortcodes', 'redux-framework-demo'),
						'indent' => true
					),
					array(
						'id' => 'shortcodes',
						'type' => 'info',
						'raw' => "
							<p>Shortcodes allow you to dynamically call specific data from within content through-out the site. </p>
							<p><strong>[sphere_participants amount=XX show_amount_raised=YY]</strong> - Will list top participants with links to their pages. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>[sphere_report_participants report=\"reportName\" amount=XX show_amount_raised=YY]</strong> - Will list only the top participants from a specific report. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace \"reportName\" with the report name in Sync Settings. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>[sphere_participantId id=XXXXX show_amount_raised=YY]</strong> - Replace X's with the supporter id. Will show name & links the participants page. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>[sphere_teams amount=XX show_amount_raised=YY]</strong> - Will list the top teams with links to their pages. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>[sphere_report_teams report=\"reportName\" amount=XX show_amount_raised=YY]</strong> - Will list the top teams with links to their pages. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace \"reportName\" with the report name in Sync Settings. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>[sphere_teamId id=XXXXX show_amount_raised=YY]</strong> - Replace X's with the team id. Will show name & links the participants page. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>[sphere_donate]</strong> - Will create a general donate link.</p>
							<p><strong>[sphere_register]</strong> - Will create a register link.</p>
							<p><strong>[sphere_eventId]</strong> - Will render the event id.</p>
							<p><strong>[sphere_eventName]</strong> - Will render the event name.</p>
							<p><strong>[sphere_eventGoal]</strong> - Will render the event goal amount.</p>
							<p><strong>[sphere_eventRaised]</strong> - Will render the amount raised.</p>
							<p><strong>[sphere_participantsSearch]</strong> - Will link to participants search page in FAF.</p>
							<p><strong>[sphere_teamSearch]</strong> - Will link to team search page in FAF.</p>
						",
					),
					array(
						'id' => 'section-shortcode-end',
						'type' => 'section',
						'indent' => false
					),
					array(
						'id' => 'section-template-start',
						'type' => 'section',
						'title' => __('Template Code', 'redux-framework-demo'),
						'indent' => false
					),
					array(
						'id' => 'template_code',
						'type' => 'info',
						'raw' => "
							<p>Template code allow you to dynamically call specific data from within template files. </p>
							<p><strong>&lt;?php sphere('participants', 'XX', 'YY); ?></strong> - Will list top participants with links to their pages. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>&lt;?php sphere('report_participants', 'reportName', 'XX', 'YY); ?></strong> - Will list only the top participants from a specific report. Replace \"reportName\" with the report name in Sync Settings. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>&lt;?php sphere('participantId', 'XXXXX', 'YY); ?></strong> - Replace X's with the supporter id. Will show name & links the participants page. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>&lt;?php sphere('teams', 'XX', 'YY); ?></strong> - Will list the top teams with links to their pages. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>&lt;?php sphere('report_teams', 'reportName', 'XX', 'YY); ?></strong> - Will list the top teams with links to their pages. Replace X's with the amount you wish to show. Enter 0 for unlimited. Replace \"reportName\" with the report name in Sync Settings. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>&lt;?php sphere('teamId', 'XXXXX', 'YY); ?></strong> - Replace X's with the team id. Will show name & links the participants page. Replace Y' with 0 or 1 to hide or show amount raised.</p>
							<p><strong>&lt;?php sphere('donate'); ?></strong> - Will create a general donate link.</p>
							<p><strong>&lt;?php sphere('register'); ?></strong> - Will create a register link.</p>
							<p><strong>&lt;?php sphere('eventId'); ?></strong> - Will render the event id.</p>
							<p><strong>&lt;?php sphere('eventName'); ?></strong> - Will render the event name.</p>
							<p><strong>&lt;?php sphere('eventGoal'); ?></strong> - Will render the event goal amount.</p>
							<p><strong>&lt;?php sphere('eventRaised'); ?></strong> - Will render the amount raised.</p>
							<p><strong>&lt;?php sphere('participantsSearch'); ?></strong> - Will link to participants search page in FAF.</p>
							<p><strong>&lt;?php sphere('teamSearch'); ?></strong> - Will link to team search page in FAF.</p>
						",
					),
					array(
						'id' => 'section-template-end',
						'type' => 'section',
						'indent' => false
					),
				),
			);
		}

		/**
		 *
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		 **/
		public function setArguments()
		{

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(

				// TYPICAL -> Change these values as you need/desire
				'opt_name' => 'sphere_data', // This is where your data is stored in the database and also becomes your global variable name.
				'display_name' => 'Sphere Options', // Name that appears at the top of your panel
				'display_version' => '1.0', // Version that appears at the top of your panel
				'menu_type' => 'menu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu' => true, // Show the sections below the admin menu item or not
				'menu_title' => __('Sphere Options', 'redux-framework-demo'),
				'page' => __('Sphere API', 'redux-framework-demo'),
				'google_api_key' => '', // Must be defined to add google fonts to the typography module
				'global_variable' => '', // Set a different name for your global variable other than the opt_name
				'dev_mode' => !empty($_REQUEST['dev_mode']) ? true : false, // Show the time the page took to load, etc
				'customizer' => false, // Enable basic customizer support

				// OPTIONAL -> Give you extra features
				'page_priority' => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_parent' => 'themes.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions' => 'manage_options', // Permissions needed to access the options panel.
				'menu_icon' => '', // Specify a custom URL to an icon
				'last_tab' => '', // Force your panel to always open to a specific tab (by id)
				'page_icon' => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
				'page_slug' => '_options', // Page slug used to denote the panel
				'save_defaults' => true, // On load save the defaults to DB before user clicks save or not
				'default_show' => false, // If true, shows the default value next to each field that is not the default value.
				'default_mark' => '', // What to print by the field's title if the value shown is default. Suggested: *


				// CAREFUL -> These options are for advanced use only
				'transient_time' => 60 * MINUTE_IN_SECONDS,
				'output' => false, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag' => false, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				//'domain'             	=> 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
				//'footer_credit'      	=> '', // Disable the footer credit of Redux. Please leave if you can help it.


				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database' => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!


				'show_import_export' => false, // REMOVE
				'system_info' => false, // REMOVE

				'help_tabs' => array(),
				'help_sidebar' => '', // __( '', $this->args['domain'] );
			);
		}
	}

	new Redux_Framework_Sphere();

}