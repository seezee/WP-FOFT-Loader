<?php
/**
 * Upload class file.
 *
 * @package WP FOFT Loader/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Upload class.
 */
class WP_FOFT_Loader_Upload {

	/**
	 * The single instance of WP_FOFT_Loader_Upload.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $instance = null;

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		// Load & unload the custom upload path.

		add_filter( 'wp_handle_upload_prefilter' . plugin_basename( $this->parent->file ), array( $this, 'pre_upload' ) );

		add_filter( 'wp_handle_upload' . plugin_basename( $this->parent->file ), array( $this, 'post_upload' ) );

	}

	/**
	 * Change upload directory location
	 *
	 * @param object $file A single element of the $_FILES array.
	 */
	public function pre_upload( $file ) {
		add_filter( 'upload_dir', 'custom_upload_dir' );
		return $file;
	}

	/**
	 * Reset upload directory location to default
	 *
	 * @param object $fileinfo File info.
	 */
	public function post_upload( $fileinfo ) {
		remove_filter( 'upload_dir', 'custom_upload_dir' );
		return $fileinfo;
	}

	/**
	 * Main WP_FOFT_Loader_Upload Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_Upload is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @param object $parent Object instance.
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_Upload is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_Upload is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __wakeup()

}
