<?php
/**
 * Plugin Name: WP FOFT Loader
 * Version: 1.0.30
 * Plugin URI: https://github.com/seezee/WP-FOFTLoader/
 * Description: Implements and automates Zach Leatherman's
 *              Critical FOFT with Data URI (see
 *              https://www.zachleat.com/web/comprehensive-webfonts/)
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.0
 * Tested up to: 5.2.1
 * PHP Version 7.0
 * Text Domain: wp-foft-loader
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author  Chris J. Zähller <chris@messengerwebdesign.com>
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files
require_once 'includes/class-wp-foft-loader.php';
require_once 'includes/class-wp-foft-loader-head.php';
require_once 'includes/class-wp-foft-loader-mimes.php';
require_once 'includes/class-wp-foft-loader-settings.php';
require_once 'includes/class-wp-foft-loader-upload.php';
require_once 'includes/htmlpurifier/library/HTMLPurifier.auto.php';
require_once 'includes/csstidy/class.csstidy.php';

// Load plugin library
require_once 'includes/lib/class-wp-foft-loader-admin-api.php';

/**
 * Returns the main instance of wp_foft_loader to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object wp_foft_loader
 */
function wp_foft_loader() {
	$instance = wp_foft_loader::instance( __FILE__, '1.0.30' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WP_FOFT_Loader_Settings::instance( $instance );
	}

	return $instance;
}

wp_foft_loader();
