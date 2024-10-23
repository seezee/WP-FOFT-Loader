<?php

/**
 * Plugin Name: WP FOFT Loader
 * Version: 2.1.36
 * Author URI: https://github.com/seezee
 * Plugin URI: https://wordpress.org/plugins/wp-foft-loader/
 * GitHub Plugin URI: seezee/WP-FOFT-Loader
 * Description: Optimize and speed up web font loading and improve UX by minimizing Flash of Invisible Text, Flash of Unstyled Text, and DOM Reflow.
 * Author: Chris J. Zähller / Messenger Web Design
 * Author URI: https://messengerwebdesign.com/
 * Requires at least: 4.6.0
 * Tested up to: 6.6.2
 * PHP Version 7.0
 * Text Domain: wp-foft-loader
 * Domain Path: /lang/
 *
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
    function wpfl_fs() {
        global $wpfl_fs;
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
$arr = array(
    'abbr' => array(),
);
// Plugin constants.
if ( !defined( 'WPFL_BASE' ) ) {
    define( 'WPFL_BASE', 'wpfl_' );
} else {
    /* translators: don't translate “WPFL_BASE”. */
    echo '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ' . wp_kses( __( 'WP <abb>FOFT</abbr> Loader ERROR! The <abbr>PHP</abbr> constant “WPFL_BASE”; has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'wp-foft-loader' ), $arr ) . '</div>';
}
if ( !defined( 'WPFL_VERSION' ) ) {
    define( 'WPFL_VERSION', '2.1.36' );
} else {
    /* translators: don't translate “WPFL_VERSION”. */
    echo '<div id="updated" class="notice notice-error is-dismissible"><span class="dashicons dashicons-no"></span> ' . wp_kses( __( 'WP <abb>FOFT</abbr> Loader ERROR! The <abbr>PHP</abbr> constant “WPFL_VERSION” has already been defined. This could be due to a conflict with another plugin or theme. Please check your logs to debug.', 'wp-foft-loader' ), $arr ) . '</div>';
}
// Load plugin class files.
require_once 'includes/class-wp-foft-loader.php';
require_once 'includes/class-wp-foft-loader-js-vars.php';
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
function wp_foft_loader() {
    $instance = wp_foft_loader::instance( __FILE__, WPFL_VERSION );
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
function wpfl_check_version() {
    if ( WPFL_VERSION !== get_option( WPFL_BASE . 'version' ) || get_option( WPFL_BASE . 'version' ) === false ) {
        // Runs if version mismatch or doesn't exist.
        // $pagenow is a global variable referring to the filename of the
        // current page, such as ‘admin.php’, ‘post-new.php’.
        global $pagenow;
        if ( 'options-general.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
            // Show only on settings pages.
            return;
        }
        $arr = array(
            'a' => array(
                'href' => array(),
                'rel'  => array(),
            ),
        );
        // Notice for FREE users.
        $html = '<div id="updated" class="notice notice-success is-dismissible">';
        $html .= '<p>';
        $html .= '<span class="dashicons dashicons-yes-alt"></span> ';
        $url1 = '//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/';
        $url2 = '//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/?trial=free';
        $rel = 'noreferrer noopener';
        $link = sprintf(
            wp_kses( 
                /* translators: ignore the placeholders in the URL */
                __( 'WP FOFT Loader updated successfully. For small-caps and additional font weights support, please upgrade to <a href="%1$s" rel="%3$s">WP FOFT Loader PRO</a>. Not sure if you need those features? We have a <a href="%2$s" rel="%3$s">FREE 14-day trial</a>.', 'wp-foft-loader' ),
                $arr
             ),
            esc_url( $url1 ),
            esc_url( $url2 ),
            $rel
        );
        $html .= $link;
        $html .= '</p>';
        $html .= '</div>';
        echo $html;
        //phpcs:ignore
        update_option( WPFL_BASE . 'version', WPFL_VERSION );
    }
}

add_action( 'plugins_loaded', 'wpfl_check_version' );
/**
 * Delete options on uninstall.
 */
function wpfl_fs_uninstall_cleanup() {
    if ( get_option( WPFL_BASE . 'uninstall' ) !== null ) {
        $uninstall = get_option( WPFL_BASE . 'uninstall' );
    } else {
        $uninstall = null;
    }
    if ( null !== $uninstall && 'delete' !== $uninstall ) {
        return;
    } else {
        foreach ( wp_load_alloptions() as $option => $value ) {
            if ( strpos( $option, WPFL_BASE ) === 0 ) {
                delete_option( $option );
            }
        }
    }
}

// Uninstall hook.
wpfl_fs()->add_action( 'after_uninstall', 'wpfl_fs_uninstall_cleanup' );