<?php

/**
 * Javascript variables class file.
 *
 * @package WP FOFT Loader/Includes
 */
if ( !defined( 'ABSPATH' ) ) {
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
        $arr = array();
        // Use this with wp_kses. Don't allow any HTML.
        // All options prefixed with WPFL_BASE constant; see wp-foft-loader.php.
        $heading = get_option( WPFL_BASE . 's1-heading' );
        $body = get_option( WPFL_BASE . 's1-body' );
        $alt = get_option( WPFL_BASE . 's1-alt' );
        $mono = get_option( WPFL_BASE . 's1-mono' );
        $body_sc = false;
        $heading_sc = false;
        $alt_sc = false;
        $mono_sc = false;
        $bobserver = null;
        $hobserver = null;
        $aobserver = null;
        $mobserver = null;
        $bodyload = null;
        $headingload = null;
        $altload = null;
        $monoload = null;
        if ( isset( $body ) && !empty( $body ) ) {
            $bobserver = 'var fontA = new FontFaceObserver(' . $body;
            if ( true === $body_sc ) {
                $bobserver .= ', {font-variant: \'small-caps\'}';
            }
            $bobserver .= ');var fontB = new FontFaceObserver(' . $body . ', {weight: 700';
            if ( true === $body_sc ) {
                $bobserver .= ', font-variant: \'small-caps\'';
            }
            $bobserver .= '});var fontC = new FontFaceObserver(' . $body . ', {style: \'italic\'';
            if ( true === $body_sc ) {
                $bobserver .= ', font-variant: \'small-caps\'';
            }
            $bobserver .= '});var fontD = new FontFaceObserver(' . $body . ', {weight: 700,style: \'italic\'';
            if ( true === $body_sc ) {
                $bobserver .= ', font-variant: \'small-caps\'';
            }
            $bobserver .= '});';
            $bodyload = 'fontA.load(),fontB.load(),fontC.load(),fontD.load(),';
        }
        if ( isset( $heading ) && !empty( $heading ) ) {
            $hobserver = 'var fontE = new FontFaceObserver(' . $heading;
            if ( true === $heading_sc ) {
                $hobserver .= ', {font-variant: \'small-caps\'}';
            }
            $hobserver .= ');var fontF = new FontFaceObserver(' . $heading . ', {weight: 700';
            if ( true === $heading_sc ) {
                $hobserver .= ', font-variant: \'small-caps\'';
            }
            $hobserver .= '});var fontG = new FontFaceObserver(' . $heading . ', {style: \'italic\'';
            if ( true === $heading_sc ) {
                $hobserver .= ', font-variant: \'small-caps\'';
            }
            $hobserver .= '});var fontH = new FontFaceObserver(' . $heading . ', {weight: 700,style: \'italic\'';
            if ( true === $heading_sc ) {
                $hobserver .= ', font-variant: \'small-caps\'';
            }
            $hobserver .= '});';
            $headingload = 'fontE.load(),fontF.load(),fontG.load(),fontH.load(),';
        }
        if ( isset( $alt ) && !empty( $alt ) ) {
            $aobserver = 'var fontI = new FontFaceObserver(' . $alt;
            if ( true === $alt_sc ) {
                $aobserver .= ', {font-variant: \'small-caps\'}';
            }
            $aobserver .= ');var fontJ = new FontFaceObserver(' . $alt . ', {weight: 700';
            if ( true === $alt_sc ) {
                $aobserver .= ', font-variant: \'small-caps\'';
            }
            $aobserver .= '});var fontK = new FontFaceObserver(' . $alt . ', {style: \'italic\'';
            if ( true === $alt_sc ) {
                $aobserver .= ', font-variant: \'small-caps\'';
            }
            $aobserver .= '});var fontL = new FontFaceObserver(' . $alt . ', {weight: 700,style: \'italic\'';
            if ( true === $alt_sc ) {
                $aobserver .= ', font-variant: \'small-caps\'';
            }
            $aobserver .= '});';
            $altload = 'fontI.load(),fontJ.load(),fontK.load(),fontL.load(),';
        }
        if ( isset( $mono ) && !empty( $mono ) ) {
            $mobserver = 'var fontM = new FontFaceObserver(' . $mono;
            if ( true === $mono_sc ) {
                $mobserver .= ', {font-variant: \'small-caps\'}';
            }
            $mobserver .= ');var fontN = new FontFaceObserver(' . $mono . ', {weight: 700';
            if ( true === $mono_sc ) {
                $mobserver .= ', font-variant: \'small-caps\'';
            }
            $mobserver .= '});var fontO = new FontFaceObserver(' . $mono . ', {style: \'italic\'';
            if ( true === $mono_sc ) {
                $mobserver .= ', font-variant: \'small-caps\'';
            }
            $mobserver .= '});var fontP = new FontFaceObserver(' . $mono . ', {weight: 700,style: \'italic\'';
            if ( true === $mono_sc ) {
                $mobserver .= ', font-variant: \'small-caps\'';
            }
            $mobserver .= '});';
            $monoload = 'fontM.load(),fontN.load(),fontO.load(),fontP.load(),';
        }
        $observers = $bobserver . $hobserver . $aobserver . $mobserver;
        $loaded = $bodyload . $headingload . $altload . $monoload;
        $loaded = rtrim( $loaded, ', ' );
        // Trim trailing comma & space.
        $output = $observers . 'Promise.all([' . $loaded . '])';
        echo '<script type="text/javascript">
	var jsObs  = ' . wp_json_encode( $observers ) . ';
	var jsLoad = ' . wp_json_encode( $loaded ) . ';
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
            self::$instance = new self($parent);
        }
        return self::$instance;
    }

    // End instance()
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
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_JS_Vars is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_JS_Vars is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __wakeup()
}

/**
* Place the @font declaration in the header.
*/
$head = new WP_FOFT_Loader_JS_Vars();
add_action( 'wp_head', array($head, 'foft_inline') );