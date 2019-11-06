<?php
/**
 * Javascript variables class file.
 *
 * @package WP FOFT Loader/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Sorry, you are not allowed to access this page directly.' );
}

	/**
	 * Enqueue custom fonts.
	 * Place font declaration and script in head -- using inline embed for
	 * critical font load with data URI per
	 * https://www.zachleat.com/web/comprehensive-webfonts/ .
	 */
class WP_FOFT_Loader_JS_Vars {

	/**
	 * The single instance of WP_FOFT_Loader_JS_Vars.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $instance = null;

	/**
	 * Create variable output for /includes/js/fallback.js.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function jsload() {

		$arr = array(); // Use this with wp_kses. Don't allow any HTML.
		// All options prefixed with $base value; see class-wp-foft-loader-settings constructor.
		$heading = get_option( 'wpfl_s1-heading' );
		$body    = get_option( 'wpfl_s1-body' );
		$alt     = get_option( 'wpfl_s1-alt' );
		$mono    = get_option( 'wpfl_s1-mono' );

		if  ( ! is_null( $body ) ) {
			$bobserver = 'var fontA = new FontFaceObserver(' . $body. ');var fontB = new FontFaceObserver(' . $body. ', {weight: 700});var fontC = new FontFaceObserver(' . $body. ', {style: "italic"});var fontD = new FontFaceObserver(' . $body. ', {weight: 700,style: "italic"});';
			$bodyload = 'fontA.load(),fontB.load(),fontC.load(),fontD.load(),';
		}

		if  ( ! is_null( $heading ) ) {
			$hobserver = 'var fontD = new FontFaceObserver(' . $heading. ');var fontE = new FontFaceObserver(' . $heading. ', {weight: 700});var fontF = new FontFaceObserver(' . $heading. ', {style: "italic"});var fontG = new FontFaceObserver(' . $heading. ', {weight: 700,style: "italic"});';
			$headingload = 'fontE.load(),fontF.load(),fontG.load(),fontH.load(),';
		}

		if  ( ! is_null( $alt ) ) {
			$aobserver = 'var fontH = new FontFaceObserver(' . $alt. ');var fontI = new FontFaceObserver(' . $alt. ', {weight: 700});var fontJ = new FontFaceObserver(' . $alt. ', {style: "italic"});var fontK = new FontFaceObserver(' . $alt. ', {weight: 700,style: "italic"});';
			$altload = 'fontI.load(),fontJ.load(),fontK.load(),fontL.load(),';
		}

		if  ( ! is_null( $mono ) ) {
			$mobserver = 'var fontM = new FontFaceObserver(' . $mono. ');var fontN = new FontFaceObserver(' . $mono. ', {weight: 700});var fontO = new FontFaceObserver(' . $mono. ', {style: "italic"});var fontP = new FontFaceObserver(' . $mono. ', {weight: 700,style: "italic"});';
			$monoload = 'fontM.load(),fontN.load(),fontO.load(),fontP.load(),';
		}

		$observers = $bobserver . $hobserver . $aobserver . $mobserver;
		$loaded    = $bodyload . $headingload . $altload . $monoload;
		$loaded    = rtrim($loaded, ", "); // Trim trailing comma & space.

		$output = $observers . 'Promise.all([' . $loaded . '])';

		echo '<script type="text/javascript">
	var jsObs  = `' . $observers . '`;
	var jsLoad = `' . $loaded . '`;
</script>';
	}

	/**
	 * Main WP_FOFT_Loader_JS_Vars Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_JS_Vars is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @param object $parent Object instance.
	 * @return Main WP_FOFT_Loader_JS_Vars instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $parent );
		}
		return self::$instance;
	} // End instance()

	/**
	 * Place the CSS & JS in the head.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function foft_inline() {
		$this->jsload();
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_JS_Vars is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances  of WP_FOFT_Loader_JS_Vars is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __wakeup()

}

/**
* Place the @font declaration in the header.
*/

	$head = new WP_FOFT_Loader_JS_Vars();

	add_action( 'wp_head', array( $head, 'foft_inline' ) );