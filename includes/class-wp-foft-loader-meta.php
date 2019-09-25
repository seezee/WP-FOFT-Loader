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
 * Add links to plugin meta
 */
class WP_FOFT_Loader_Meta {

	/**
	 * The single instance of WP_FOFT_Loader_Meta.
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
	 */
	public function __links() {

		// Filter the plugin meta.
		add_filter( 'plugin_row_meta', array( $this, 'meta_links' ), 10, 2 );
	}

	/**
	 * Custom links.
	 *
	 * @param string $links Custom links.
	 * @param string $file Path to main plugin file.
	 */
	public function meta_links( $links, $file ) {
		// Only for this plugin.
		if ( strpos( $file, 'wp-foft-loader.php' ) !== false ) {

			$supportlink = 'https://wordpress.org/support/plugin/wp-foft-loader';
			$donatelink = 'https://paypal.me/messengerwebdesign?locale.x=en_US';
			$reviewlink = 'https://wordpress.org/support/view/plugin-reviews/wp-foft-loader?rate=5#postform';
			$twitterlink = 'http://twitter.com/czahller';
			$iconstyle = 'style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;"';

			return array_merge( $links, array(
				'<a href="' . $supportlink . '"> <span class="dashicons dashicons-format-chat" ' . $iconstyle . 'title="WP FOFT Loader Support"></span></a>',
				'<a href="' . $twitterlink . '"><span class="dashicons dashicons-twitter" ' . $iconstyle . 'title="Chris J. Zähller on Twitter"></span></a>',
				'<a href="' . $reviewlink . '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="Give a 5 Star Review"></span></a>',
				'<a href="' . $donatelink . '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="Donate"></span></a>',
			) );
		}

		return $links;
	}

	/**
	 * Main WP_FOFT_Loader_Meta Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_Meta is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @param object $parent Object instance.
	 * @return Main WP_FOFT_Loader_Meta instance
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_Meta is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_Meta is forbidden.' ), esc_attr( $this->parent->version ) );
	} // End __wakeup()

}

$meta = new WP_FOFT_Loader_Meta();
$meta -> __links();
