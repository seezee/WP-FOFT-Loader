<?php
/**
 * errors class file.
 *
 * @package WP FOFT Loader/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Sorry, you are not allowed to access this page directly.' );
}

/**
 * Admin errors.
 *
 */
class WP_FOFT_Loader_Errors {

	/**
	 * The single instance of WP_FOFT_Loader_Errors.
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
	 * Admin errors.
	 *
	 * @return void
	 */
	public function settings_errors() {
		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				'wp_foft_loader_errors',
				esc_attr('settings_updated'),
				__('Good job! Your settings were saved!', 'wp-foft-loader'),
				'updated'
			);
		}
	}

	/**
	* Add admin errors to settings page.
	*/
	public function wp_foft_loader_notices() {
		$this->settings_errors( 'wp_foft_loader_errors' );
	}

	/**
	 * Main WP_FOFT_Loader_Errors Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_Errors is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @param object $parent Object instance.
	 * @return Main WP_FOFT_Loader_Errors instance
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_Errors is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances  of WP_FOFT_Loader_Errors is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __wakeup()

}

/**
* Display admin notices.
*/

$errors = new WP_FOFT_Loader_Errors();

add_action( 'admin_notices', array( $errors, 'wp_foft_loader_notices' ) );
