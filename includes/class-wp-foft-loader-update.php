<?php
/*Plugin Name: Upgrader Process Example
Plugin URI: https://pluginrepublic.com/wordpress-plugin-update-hook-upgrader_process_complete/
Description: Just an example of using upgrader_process_complete
Version: 1.0.0
Author: Catapult Themes
Author URI: https://pluginrepublic.com/
Text Domain: wp-upe
Domain Path: /languages*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
 exit;
}

/**
 * This function runs when WordPress completes its upgrade process
 * It iterates through each plugin updated to see if ours is included
 * @param $upgrader_object Array
 * @param $options Array
 */
function wp_upe_upgrade_completed( $upgrader_object, $options ) {
 // The path to our plugin's main file
 $our_plugin = plugin_basename( __FILE__ );
 // If an update has taken place and the updated type is plugins and the plugins element exists
 if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
  // Iterate through the plugins being updated and check if ours is there
  foreach( $options['plugins'] as $plugin ) {
   if( $plugin == $our_plugin ) {
    // Set a transient to record that our plugin has just been updated
    set_transient( 'wp_upe_updated', 1 );
   }
  }
 }
}
add_action( 'upgrader_process_complete', 'wp_upe_upgrade_completed', 10, 2 );

/**
 * Show a notice to anyone who has just updated this plugin
 * This notice shouldn't display to anyone who has just installed the plugin for the first time
 */
function wp_upe_display_update_notice() {
 // Check the transient to see if we've just updated the plugin
 if( get_transient( 'wp_upe_updated' ) ) {
  echo '<div class="notice notice-success">' . __( 'Thanks for updating', 'wp-upe' ) . '</div>';
  delete_transient( 'wp_upe_updated' );
 }
}
add_action( 'admin_notices', 'wp_upe_display_update_notice' );

/**
 * Show a notice to anyone who has just installed the plugin for the first time
 * This notice shouldn't display to anyone who has just updated this plugin
 */
function wp_upe_display_install_notice() {
 // Check the transient to see if we've just activated the plugin
 if( get_transient( 'wp_upe_activated' ) ) {
  echo '<div class="notice notice-success">' . __( 'Thanks for installing', 'wp-upe' ) . '</div>';
  // Delete the transient so we don't keep displaying the activation message
 delete_transient( 'wp_upe_activated' );
 }
}
add_action( 'admin_notices', 'wp_upe_display_install_notice' );

/**
 * Run this on activation
 * Set a transient so that we know we've just activated the plugin
 */
function wp_upe_activate() {
 set_transient( 'wp_upe_activated', 1 );
}
register_activation_hook( __FILE__, 'wp_upe_activate' );