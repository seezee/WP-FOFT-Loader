<?php

/**
 * Plugin Name: WP FOFT Loader
 * Version: 2.0.15
 * Author URI: https://github.com/seezee
 * Plugin URI: https://wordpress.org/plugins/wp-foft-loader/
 * GitHub Plugin URI: seezee/WP-FOFT-Loader  
 * Description: Optimize and speed up webfont loading and improve UX by minimizing Flash of Invisible Text, Flash of Unstyled Text, and DOM Reflow.
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.0
 * Tested up to: 5.2.1
 * PHP Version 7.0
 * Text Domain: wp-foft-loader
 * Domain Path: /lang/
 *
 *
 * @package WordPress
 * @author  Chris J. Zähller <chris@messengerwebdesign.com>
 * @since   1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Freemius; handles activating PRO features for licensed users and stripping
 * PRO features from free plugin version.
 *
 * @since  1.0.0
 *
 */

if ( !function_exists( 'wpfl_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wpfl_fs()
    {
        global  $wpfl_fs ;
        
        if ( !isset( $wpfl_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wpfl_fs = fs_dynamic_init( array(
                'id'              => '4955',
                'slug'            => 'wp-foft-loader',
                'premium_slug'    => 'wp-foft-loader-pro',
                'type'            => 'plugin',
                'public_key'      => 'pk_687d46aecb0d682d1bb34aa19e066',
                'is_premium'      => false,
                'premium_suffix'  => 'PRO',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'trial'           => array(
                'days'               => 14,
                'is_require_payment' => false,
            ),
                'has_affiliation' => 'all',
                'menu'            => array(
                'slug'   => 'wp_foft_loader_settings',
                'parent' => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'         => true,
            ) );
        }
        
        return $wpfl_fs;
    }
    
    // Init Freemius.
    wpfl_fs();
    // Signal that SDK was initiated.
    do_action( 'wpfl_fs_loaded' );
}

// Plugin constants.
const  _BASE_ = 'wpfl_' ;
const  _VERSION_ = '2.0.15' ;
// Load plugin class files.
require_once 'includes/class-wp-foft-loader.php';
require_once 'includes/class-wp-foft-loader-jsvars.php';
// Must run before next file.
require_once 'includes/class-wp-foft-loader-head.php';
require_once 'includes/class-wp-foft-loader-meta.php';
require_once 'includes/class-wp-foft-loader-mimes.php';
require_once 'includes/class-wp-foft-loader-settings.php';
require_once 'includes/class-wp-foft-loader-upload.php';
require_once 'includes/vendor/htmlpurifier/library/HTMLPurifier.auto.php';
require_once 'includes/vendor/csstidy/class.csstidy.php';
// Load plugin library.
require_once 'includes/lib/class-wp-foft-loader-admin-api.php';
/**
 * Returns the main instance of wp_foft_loader to prevent the need to use
 * globals.
 *
 * @since  1.0.0
 * @return object wp_foft_loader
 */
function wp_foft_loader()
{
    $instance = wp_foft_loader::instance( __FILE__, _VERSION_ );
    if ( is_null( $instance->settings ) ) {
        $instance->settings = WP_FOFT_Loader_Settings::instance( $instance );
    }
    return $instance;
}

wp_foft_loader();
// Activation / upgrade
function wpfl_activation()
{
    update_option( 'wpfl_version', _BASE_ );
}

register_activation_hook( __FILE__, 'wpfl_activation' );
// Checks the version number. Run wpfl_activation only if numbers mismatch.
function wpfl_check_version()
{
    
    if ( _BASE_ !== get_option( 'wpfl_version' ) ) {
        wpfl_activation();
        // Notice for FREE users.
        $html = '<div id="updated" class="notice notice-success is-dismissible">';
        $html .= '<p>';
        $html .= __( '<span class="dashicons dashicons-yes-alt"></span> WP FOFT Loader updated successfully. For small-caps and additional font weights support, please upgrade to <a href="//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/" rel="noopener noreferrer">WP FOFT Loader PRO</a>. Not sure if you need those features? We have a <a href="//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/?trial=free" rel="noopener noreferrer">FREE 14-day trial.</a>', 'wp-foft-loader' );
        $html .= '</p>';
        $html .= '</div>';
        echo  $html ;
    }

}

add_action( 'plugins_loaded', 'wpfl_check_version' );
/**
 * Runs only if plugin is uninstalled.
 *
 * @since  1.0.0
 * @return object WP_UNINSTALL_PLUGIN
 */
function wpfl_fs_uninstall_cleanup()
{
    foreach ( wp_load_alloptions() as $option => $value ) {
        if ( strpos( $option, _BASE_ ) === 0 ) {
            delete_option( $option );
        }
    }
}

// Uninstall hook.
wpfl_fs()->add_action( 'after_uninstall', 'wpfl_fs_uninstall_cleanup' );