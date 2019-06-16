<?php
/*
 * Plugin Name: WP FOFT Loader
 * Version: 1.0
 * Plugin URI: https://github.com/seezee/WP-FOFTLoader/
 * Description: Implements and automates Zach Leatherman's Cricital FOFT with Data URI (see https://www.zachleat.com/web/comprehensive-webfonts/)
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: http://messengerwebdesign.com/
 * Requires at least: 4.0
 * Tested up to: 5.2.1
 *
 * Text Domain: wpfoft
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Chris J. Z
 * @author Chris J. Zähller
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-wp-foft-loader.php' );
require_once( 'includes/class-wp-foft-loader-settings.php' );
require_once( 'includes/class-wp-foft-loader-mimes.php' );
require_once( 'includes/class-wp-foft-loader-upload.php' );
require_once( 'includes/class-wp-foft-loader-head.php' );

// Load plugin library
require_once( 'includes/lib/class-wp-foft-loader-admin-api.php' );

/**
 * Returns the main instance of WP_FOFT_Loader to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WP_FOFT_Loader
 */
function WP_FOFT_Loader () {
	$instance = WP_FOFT_Loader::instance( __FILE__, '1.0.18' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WP_FOFT_Loader_Settings::instance( $instance );
	}

	return $instance;
}

WP_FOFT_Loader();
