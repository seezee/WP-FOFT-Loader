<?php

/**
 *
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 */

// If plugin is not being uninstalled, exit (do nothing)
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
// But if it is, delete the options for this plugin from the WP database
} else {
	foreach ( wp_load_alloptions() as $option => $value ) {
		// TO DO: does this work?
		// if ( strpos( $option, $this->base ) === 0 ) {
		if ( strpos( $option, 'wpfl_' ) === 0 ) {
		
			delete_option( $option );
		}
	}
}
