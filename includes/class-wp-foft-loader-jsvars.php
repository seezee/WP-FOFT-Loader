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
class WP_FOFT_Loader_JS_Vars
{
    /**
     * The single instance of WP_FOFT_Loader_JS_Vars.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static  $instance = null ;
    /**
     * Create variable output for /includes/js/fallback.js.
     *
     * @access  public
     * @since   1.0.0
     */
    public function jsload()
    {
        $arr = array();
        // Use this with wp_kses. Don't allow any HTML.
        // All options prefixed with _BASE_ constant; see wp-foft-loader.php.
        $heading = get_option( _BASE_ . 's1-heading' );
        $body = get_option( _BASE_ . 's1-body' );
        $alt = get_option( _BASE_ . 's1-alt' );
        $mono = get_option( _BASE_ . 's1-mono' );
        $body_sc = false;
        $heading_sc = false;
        $alt_sc = false;
        $mono_sc = false;
        $bobserver = NULL;
        
        if ( !is_null( $body ) ) {
            $bobserver = 'var fontA = new FontFaceObserver(' . $body;
            if ( $body_sc === true ) {
                $bobserver .= ', {font-variant: \'small-caps\'}';
            }
            $bobserver .= ');var fontB = new FontFaceObserver(' . $body . ', {weight: 700';
            if ( $body_sc === true ) {
                $bobserver .= ', font-variant: \'small-caps\'';
            }
            $bobserver .= '});var fontC = new FontFaceObserver(' . $body . ', {style: \'italic\'';
            if ( $body_sc === true ) {
                $bobserver .= ', font-variant: \'small-caps\'';
            }
            $bobserver .= '});var fontD = new FontFaceObserver(' . $body . ', {weight: 700,style: \'italic\'';
            if ( $body_sc === true ) {
                $bobserver .= ', font-variant: \'small-caps\'';
            }
            $bobserver .= '});';
            $bodyload = 'fontA.load(),fontB.load(),fontC.load(),fontD.load(),';
        }
        
        $hobserver = NULL;
        
        if ( !is_null( $heading ) ) {
            $hobserver = 'var fontE = new FontFaceObserver(' . $heading;
            if ( $heading_sc === true ) {
                $hobserver .= ', {font-variant: \'small-caps\'}';
            }
            $hobserver .= ');var fontF = new FontFaceObserver(' . $heading . ', {weight: 700';
            if ( $heading_sc === true ) {
                $hobserver .= ', font-variant: \'small-caps\'';
            }
            $hobserver .= '});var fontG = new FontFaceObserver(' . $heading . ', {style: \'italic\'';
            if ( $heading_sc === true ) {
                $hobserver .= ', font-variant: \'small-caps\'';
            }
            $hobserver .= '});var fontH = new FontFaceObserver(' . $heading . ', {weight: 700,style: \'italic\'';
            if ( $heading_sc === true ) {
                $hobserver .= ', font-variant: \'small-caps\'';
            }
            $hobserver .= '});';
            $headingload = 'fontE.load(),fontF.load(),fontG.load(),fontH.load(),';
        }
        
        $aobserver = NULL;
        
        if ( !is_null( $alt ) ) {
            $aobserver = 'var fontI = new FontFaceObserver(' . $alt;
            if ( $alt_sc === true ) {
                $aobserver .= ', {font-variant: \'small-caps\'}';
            }
            $aobserver .= ');var fontJ = new FontFaceObserver(' . $alt . ', {weight: 700';
            if ( $alt_sc === true ) {
                $aobserver .= ', font-variant: \'small-caps\'';
            }
            $aobserver .= '});var fontK = new FontFaceObserver(' . $alt . ', {style: \'italic\'';
            if ( $alt_sc === true ) {
                $aobserver .= ', font-variant: \'small-caps\'';
            }
            $aobserver .= '});var fontL = new FontFaceObserver(' . $alt . ', {weight: 700,style: \'italic\'';
            if ( $alt_sc === true ) {
                $aobserver .= ', font-variant: \'small-caps\'';
            }
            $aobserver .= '});';
            $altload = 'fontI.load(),fontJ.load(),fontK.load(),fontL.load(),';
        }
        
        $mobservers = NULL;
        
        if ( !is_null( $mono ) ) {
            $mobserver = 'var fontM = new FontFaceObserver(' . $mono;
            if ( $mono_sc === true ) {
                $mobserver .= ', {font-variant: \'small-caps\'}';
            }
            $mobserver .= ');var fontN = new FontFaceObserver(' . $mono . ', {weight: 700';
            if ( $mono_sc === true ) {
                $mobserver .= ', font-variant: \'small-caps\'';
            }
            $mobserver .= '});var fontO = new FontFaceObserver(' . $mono . ', {style: \'italic\'';
            if ( $mono_sc === true ) {
                $mobserver .= ', font-variant: \'small-caps\'';
            }
            $mobserver .= '});var fontP = new FontFaceObserver(' . $mono . ', {weight: 700,style: \'italic\'';
            if ( $mono_sc === true ) {
                $mobserver .= ', font-variant: \'small-caps\'';
            }
            $mobserver .= '});';
            $monoload = 'fontM.load(),fontN.load(),fontO.load(),fontP.load(),';
        }
        
        $observers = $bobserver . $hobserver . $aobserver . $mobserver;
        $loaded = $bodyload . $headingload . $altload . $monoload;
        $loaded = rtrim( $loaded, ", " );
        // Trim trailing comma & space.
        $output = $observers . 'Promise.all([' . $loaded . '])';
        echo  '<script type="text/javascript">
	var jsObs  = ' . wp_json_encode( $observers ) . ';
	var jsLoad = ' . wp_json_encode( $loaded ) . ';
</script>' ;
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
    public static function instance( $parent )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $parent );
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
    public function foft_inline()
    {
        $this->jsload();
    }
    
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_JS_Vars is forbidden.', 'wp-foft-loader' ), esc_attr( _VERSION_ ) );
    }
    
    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances  of WP_FOFT_Loader_JS_Vars is forbidden.', 'wp-foft-loader' ), esc_attr( _VERSION_ ) );
    }

}
/**
* Place the @font declaration in the header.
*/
$head = new WP_FOFT_Loader_JS_Vars();
add_action( 'wp_head', array( $head, 'foft_inline' ) );