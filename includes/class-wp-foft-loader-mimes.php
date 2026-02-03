<?php
/**
 * Mimes Allowed class file.
 *
 * @package WP FOFT Loader/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow WOFF & WOFF2 mime-types
 */
class WP_FOFT_Loader_Mimes {

	/**
	 * The single instance of WP_FOFT_Loader_Mimes.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $instance = null;

	/**
	 * Constructor function.
	 */
	public function allow_woff() {

		add_filter( 'wp_check_filetype_and_ext', array( $this, 'file_and_ext' ), 10, 4 );

		add_filter( 'upload_mimes', array( $this, 'mime_types' ) );
	}

	/**
	 * Extensions and types allowed.
	 *
	 * @param string $types File type.
	 * @param string $file Full file path.
	 * @param string $filename File name.
	 * @param string $mimes Mimes to add.
	 */
	public function file_and_ext( $types, $file, $filename, $mimes ) {
		$ext                = pathinfo( $filename, PATHINFO_EXTENSION );
		$allowed_ext        = array(
			'woff',
			'woff2',
		);
		$mime_type          = mime_content_type( $file );
		$allowed_mime_types = array(
			'font/woff',
			'font/woff2',
			'application/font-woff',
			'application/font-woff2',
		);
		if ( ( in_array( $mime_type, $allowed_mime_types, true ) ) && ( in_array( $ext, $allowed_ext, true ) ) ) {
			$types['ext']  = $ext;
			if ( 'woff' === $ext ) {
				$types['type'] = 'font/woff|application/font-woff';
			} else if ( 'woff2' === $ext ) {
				$types['type'] = 'font/woff2|application/font-woff2';
			};
		};
		return $types;
	}

	/**
	 * Add mime types.
	 *
	 * @param array $existing_mimes Array of existing mimes to be modified.
	 */
	public function mime_types( $existing_mimes ) {
		$existing_mimes['woff']  = 'font/woff|application/font-woff';
		$existing_mimes['woff2'] = 'font/woff2|application/font-woff2';
		return $existing_mimes;
	}

	/**
	 * Main WP_FOFT_Loader_Mimes Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_Mimes is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @param object $parent Object instance.
	 * @return Main WP_FOFT_Loader_Mimes instance
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_Mimes is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_Mimes is forbidden.' ), esc_attr( WPFL_VERSION ) );
	} // End __wakeup()

}

$mimes = new WP_FOFT_Loader_Mimes();
$mimes->allow_woff();
