<?php
/*
Plugin Name: 	Infusionsoft Contact Checker
Plugin URI: 	http://www.first10.co.uk
Description: 	This plugin is based on the basic infusionsoft api sample wp plugin and purely checks user details on login and updates WP User if different from Infusionsoft data
Version: 		0.5
Author: 		Dan Stapleton / First 10
Author URI: 	http://www.first10.co.uk

*/


global $infusionsoft;

require_once plugin_dir_path( __FILE__ ) . 'infusionsoft.php';
require_once plugin_dir_path( __FILE__ ) . 'infusionsoft-contact-check.php';
require_once plugin_dir_path( __FILE__ ) . 'infusionsoft-settings.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

// Load main Infusionsoft API
$settings = (array) get_option( 'infusionsoft_settings' );
if ( isset( $settings['subdomain'] ) && isset( $settings['api_key'] ) && isset( $settings['gf_integration'] ) ) {
	$infusionsoft = new Infusionsoft( $settings['subdomain'], $settings['api_key'] );

	// Make sure Infusionsoft connected
	if ( is_wp_error( $infusionsoft->error ) ) {
		$error = $infusionsoft->error->get_error_message();
		add_action( 'admin_notices', create_function( '$error', 'echo "<div class=\"error\"><p><strong>Infusionsoft Error:</strong> ' . $error . '</p></div>";' ) );
	}

} else {
  function infusionsoft_error_notice() {
    $class = "error";
    $message = "The InfusionSoft contacts plugin is not configured correctly!
                Users will not be loaded on login until you have entered
                the details in the <a href=".
                admin_url('options-general.php?page=infusionsoft')
                .">settings page</a>.";
          echo"<div class=\"$class\"> <p>$message</p></div>";
  }
  add_action( 'admin_notices', 'infusionsoft_error_notice' );
}

class Infusionsoft_WP {
	/**
	 * Calls all actions and hooks used by the plugin
	 */
	public function __construct() {
		$settings = (array) get_option( 'infusionsoft_settings' );

		// Load Gravity Forms integration if enabled
		if ( isset( $settings['gf_integration'] ) && $settings['gf_integration'] && ! is_plugin_active( 'infusionsoft/infusionsoft.php' ) ) {
			$infusionsoft_gravityforms = new Infusionsoft_GravityForms;
		}
	}
}

// Start the plugin
$infusionsoft_wp = new Infusionsoft_WP;
