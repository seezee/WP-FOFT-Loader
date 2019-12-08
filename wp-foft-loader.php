<?php

/**
 * Plugin Name: WP FOFT Loader
 * Version: 2.0.27
 * Author URI: https://github.com/seezee
 * Plugin URI: https://wordpress.org/plugins/wp-foft-loader/
 * GitHub Plugin URI: seezee/WP-FOFT-Loader  
 * Description: Optimize and speed up webfont loading and improve UX by minimizing Flash of Invisible Text, Flash of Unstyled Text, and DOM Reflow.
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.0
 * Tested up to: 5.3
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
                'slug'   => 'wp-foft-loader',
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

if ( !defined( '_WPFL_BASE_' ) ) {
    define( '_WPFL_BASE_', 'wpfl_' );
} else {
    echo  '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ' . __( 'WP <abb>FOFT</abbr> Loader ERROR! The <abbr>PHP</abbr> constant', 'wp-foft-loader' ) . ' &ldquo;_WPFL_BASE_&rdquo; ' . __( 'has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'wp-foft-loader' ) . '</div>' ;
}


if ( !defined( '_WPFL_VERSION_' ) ) {
    define( '_WPFL_VERSION_', '2.0.27' );
} else {
    echo  '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ' . __( 'WP <abb>FOFT</abbr> Loader ERROR! The <abbr>PHP</abbr> constant', 'wp-foft-loader' ) . ' &ldquo;_WPFL_VERSION_&rdquo; ' . __( 'has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'wp-foft-loader' ) . '</div>' ;
}

// Load plugin class files.
require_once 'includes/class-wp-foft-loader.php';
require_once 'includes/class-wp-foft-loader-jsvars.php';
// Must run before next file.
require_once 'includes/class-wp-foft-loader-head.php';
require_once 'includes/class-wp-foft-loader-meta.php';
require_once 'includes/class-wp-foft-loader-mimes.php';
require_once 'includes/class-wp-foft-loader-ratings.php';
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
    $instance = wp_foft_loader::instance( __FILE__, _WPFL_VERSION_ );
    if ( is_null( $instance->settings ) ) {
        $instance->settings = WP_FOFT_Loader_Settings::instance( $instance );
    }
    return $instance;
}

wp_foft_loader();
/**
 * Checks the version number in the DB. If they don't match we just upgraded, * so show a notice and update the DB.
 *
 * @since  1.0.0
 */
function wpfl_check_version()
{
    
    if ( _WPFL_VERSION_ !== get_option( _WPFL_BASE_ . 'version' ) || get_option( _WPFL_BASE_ . 'version' ) == FALSE ) {
        // Runs if version mismatch or doesn't exist.
        // $pagenow is a global variable referring to the filename of the
        // current page, such as ‘admin.php’, ‘post-new.php’.
        global  $pagenow ;
        if ( $pagenow != 'options-general.php' || !current_user_can( 'install_plugins' ) ) {
            // Show only on settings pages.
            return;
        }
        
        if ( wpfl_fs()->is__premium_only() && wpfl_fs()->can_use_premium_code() ) {
            // Notice for PRO users.
            $html = '<div id="updated" class="notice notice-success is-dismissible">';
            $html .= '<p>';
            $html .= __( '<span class="dashicons dashicons-yes-alt"></span> WP FOFT Loader PRO updated successfully!', 'wp-foft-loader' );
            $html .= '</p>';
            $html .= '</div>';
            echo  $html ;
        } elseif ( wpfl_fs()->is__premium_only() && !wpfl_fs()->can_use_premium_code() ) {
            // Notice for PRO users who have not activated their licenses.
            $html = '<div id="updated" class="notice notice-success is-dismissible">';
            $html .= '<p>';
            $html .= __( '<span class="dashicons dashicons-yes-alt"></span> WP FOFT Loader PRO updated successfully! <a href="' . esc_url( 'options-general.php?page=' . $this->parent->token ) . '-account">' . __( 'Please activate your license', 'wp-foft-loader' ) . '</a> to enable PRO features.', 'wp-foft-loader' );
            $html .= '</p>';
            $html .= '</div>';
            echo  $html ;
        } else {
            // Notice for FREE users.
            $html = '<div id="updated" class="notice notice-success is-dismissible">';
            $html .= '<p>';
            $html .= '<span class="dashicons dashicons-yes-alt"></span> ' . __( 'WP FOFT Loader updated successfully. For small-caps and additional font weights support, please upgrade to', 'wp-foft-loader' ) . ' <a href="' . esc_url( '//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/' ) . '" rel="noopener noreferrer">WP FOFT Loader PRO</a>. ' . __( 'Not sure if you need those features? We have a', 'wp-foft-loader' ) . ' <a href="' . esc_url( '//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/?trial=free" rel="noopener noreferrer' ) . '">' . __( 'FREE 14-day trial.', 'wp-foft-loader' ) . '</a>';
            $html .= '</p>';
            $html .= '</div>';
            echo  $html ;
        }
        
        update_option( _WPFL_BASE_ . 'version', _WPFL_VERSION_ );
    }

}

add_action( 'plugins_loaded', 'wpfl_check_version' );
function wpfl_fs_uninstall_cleanup()
{
    foreach ( wp_load_alloptions() as $option => $value ) {
        if ( strpos( $option, _WPFL_BASE_ ) === 0 ) {
            delete_option( $option );
        }
    }
}

// Uninstall hook.
wpfl_fs()->add_action( 'after_uninstall', 'wpfl_fs_uninstall_cleanup' );