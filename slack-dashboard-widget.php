<?php
/**
 * Plugin Name: Slack Dashboard Widget
 * Description:
 * Author: Nikhil Vimal
 * Author URI: http://nik.techvoltz.com
 * Version: 1.0
 * Plugin URI:
 * License: GNU GPLv2+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class Slack_Dashboard_Widget {

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ));

	}

	/**
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'wp_dashboard_setup' action below.
	 */
	public function dashboard_widget() {

		wp_add_dashboard_widget(
			'slack_dashboard_widget',         // Widget slug.
			'Slack Channel Info Dashboard Widget',         // Title.
			array( $this, 'dashboard_widget_function' ) // Display function.
		);
	}


	public function dashboard_widget_function() {
		if (! get_option('slack_dash_widget_name') == "") {

			$jsonurl = get_option( 'slack_dash_widget_name' );
			$json    = wp_remote_get( $jsonurl );

			if ( is_wp_error( $json ) ) {
				return "ERROR";
			} else {
				$body        = wp_remote_retrieve_body( $json );
				$json_output =  json_decode( $body );
				foreach ( $json_output->channels as $channel ) {
					echo "<ul><li>" . esc_html( $channel->name ) . "</li></ul>";
				}
			}
		}


	}
}
new Slack_Dashboard_Widget();


// ------------------------------------------------------------------
// Add all your sections, fields and settings during admin_init
// ------------------------------------------------------------------
//

function slack_dash_widgets_api_init() {
	// Add the section to reading settings so we can add our
	// fields to it
	add_settings_section(
		'slack_dash_widget_section',
		'Example settings section in reading',
		'slack_dash_widget_section_callback_function',
		'general'
	);

	// Add the field with the names and function to use for our new
	// settings, put it in our new section
	add_settings_field(
		'slack_dash_widget_name',
		'Example setting Name',
		'slack_dash_widget_callback_function',
		'general',
		'slack_dash_widget_section'
	);

	// Register our setting so that $_POST handling is done for us and
	// our callback function just has to echo the <input>
	register_setting( 'general', 'slack_dash_widget_name' );
} // slack_dash_widgets_api_init()

add_action( 'admin_init', 'slack_dash_widgets_api_init' );


function slack_dash_widget_section_callback_function() {
	echo '<p>Intro text for our settings section</p>';
}

// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
//
// creates a checkbox true/false option. Other types are surely possible
//

function slack_dash_widget_callback_function() {
	echo '<input type="url" name="slack_dash_widget_name" value="' . esc_url( get_option('slack_dash_widget_name') ) .'" /> Explanation text';
}