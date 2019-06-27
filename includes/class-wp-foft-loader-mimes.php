<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Allow WOFF & WOFF2 mime-types
 */

class WP_FOFT_Loader_Mimes {

	/**
	 * The single instance of WP_FOFT_Loader_Upload.
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $instance = null;

	/**
	 * The main plugin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = '';

		// Allow WOFF & WOFF2 mime-types

		add_filter( 'wp_check_filetype_and_ext' . plugin_basename( $this->parent->file ), array( $this, 'file_and_ext' ), 10, 4 );

		add_filter( 'wp_upload_mimes' . plugin_basename( $this->parent->file ), array( $this, 'mime_types' ) );

	}

	public function file_and_ext( $types, $file, $filename, $mimes ) {
		if ( false !== strpos( $filename, '.woff' ) ) {
			$types['ext']  = 'woff';
			$types['type'] = 'font/woff|application/font-woff|application/x-font-woff|application/octet-stream';
		}
		if ( false !== strpos( $filename, '.woff2' ) ) {
			$types['ext']  = 'woff2';
			$types['type'] = 'font/woff2|application/octet-stream|font/x-woff2';
		}
		return $types;
	}

	public function mime_types( $existing_mimes ) {
		$existing_mimes['woff']  = 'font/woff|application/font-woff|application/x-font-woff|application/octet-stream';
		$existing_mimes['woff2'] = 'font/woff2|application/octet-stream|font/x-woff2';
		return $existing_mimes;
	}

	/**
	 * Main WP_FOFT_Loader_Upload Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_Upload is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @return Main WP_FOFT_Loader_Upload instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $parent );
		}
		return self::$instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-foft-loader' ), $this->parent->version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-foft-loader' ), $this->parent->version );
	} // End __wakeup()

}
