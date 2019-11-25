<?php

/**
 * Main plugin class file.
 *
 * @package WP FOFT Loader/Includes
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Main plugin class.
 */
class WP_FOFT_Loader
{
    /**
     * The single instance of WP_FOFT_Loader.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static  $instance = null ;
    /**
     * Local instance of WP_FOFT_Loader_API
     *
     * @var WP_FOFT_Loader_API|null
     */
    public  $admin = null ;
    /**
     * Settings class object
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public  $settings = null ;
    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $token ;
    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $file ;
    /**
     * The main plugin directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $dir ;
    /**
     * The plugin assets directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $assets_dir ;
    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $assets_url ;
    /**
     * Suffix for Javascripts.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public  $script_suffix ;
    /**
     * Constructor function.
     *
     * @param string $file File constructor.
     */
    public function __construct( $file = '' )
    {
        $this->token = 'wp_foft_loader';
        // Load plugin environment variables.
        $this->file = $file;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
        $this->script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        // Use minified script.
        register_activation_hook( $this->file, array( $this, 'install' ) );
        // Load admin JS & CSS.
        add_action(
            'admin_enqueue_scripts',
            array( $this, 'admin_enqueue_styles' ),
            10,
            1
        );
        add_action(
            'admin_enqueue_scripts',
            array( $this, 'admin_enqueue_scripts' ),
            10,
            1
        );
        add_action(
            'admin_enqueue_scripts',
            array( $this, 'admin_enqueue_fa_scripts' ),
            10,
            1
        );
        // Load API for generic admin functions.
        if ( is_admin() ) {
            $this->admin = new WP_FOFT_Loader_Admin_API();
        }
        // Handle localisation.
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // Display the admin notification
            add_action( 'admin_notices', array( $this, 'free_activation' ) );
        }
    }
    
    // End __construct ()
    /**
     * Displays an activation notice.
     */
    public function free_activation()
    {
        
        if ( !wpfl_fs()->can_use_premium_code() ) {
            $html = '<div id="activated" class="notice notice-info is-dismissible">';
            $html .= '<p>';
            $html .= __( '<span class="dashicons dashicons-info"></span> Thank you for installing WP FOFT Loader. For small-caps and additional font weights support, please upgrade to <a href="//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/" rel="noopener noreferrer">WP FOFT Loader PRO</a>. Not sure if you need those features? We have a <a href="//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/?trial=free" rel="noopener noreferrer">FREE 14-day trial.</a>', 'wp-foft-loader' );
            $html .= '</p>';
            $html .= '</div>';
            echo  $html ;
        }
    
    }
    
    // end plugin_activation
    /**
     * Admin enqueue style.
     *
     * @param string $hook Hook parameter.
     *
     * @return void
     */
    public function admin_enqueue_styles( $hook = '' )
    {
        wp_register_style(
            $this->token . '-admin',
            esc_url( $this->assets_url ) . 'css/admin.css',
            array(),
            esc_html( _VERSION_ )
        );
        wp_enqueue_style( $this->token . '-admin' );
    }
    
    // End admin_enqueue_styles ()
    /**
     * Load admin Javascript.
     *
     * @access  public
     *
     * @param string $hook Hook parameter.
     *
     * @return  void
     * @since   1.0.0
     */
    public function admin_enqueue_scripts( $hook = '' )
    {
        // $pagenow is a global variable referring to the filename of the
        // current page, such as ‘admin.php’, ‘post-new.php’.
        global  $pagenow ;
        if ( $pagenow != 'options-general.php' ) {
            return;
        }
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-form' );
    }
    
    // End admin_enqueue_scripts () */
    /**
     * Load admin meta Javascript.
     *
     * @access  public
     *
     * @param string $hook Hook parameter.
     *
     * @return  void
     * @since   1.0.0
     */
    public function admin_enqueue_fa_scripts( $hook = '' )
    {
        global  $pagenow ;
        
        if ( ($pagenow = 'plugins.php') || ($pagenow = 'general-options.php') ) {
            wp_register_script(
                $this->token . '-fa-main',
                esc_url( $this->assets_url ) . 'js/fontawesome' . $this->script_suffix . '.js',
                array(),
                _VERSION_,
                true
            );
            wp_enqueue_script( $this->token . '-fa-main' );
            // We're using a specially optimized version of fa-solid.js to
            // load only the necessary Fontawesome glyphs, i.e. fa-coffee
            // & fa-font. In the event we ever need to add more glyphs, both
            // scripts, i.e., fa-solid.js & fa-solid.min.js, will need to be
            // updated.
            wp_register_script(
                $this->token . '-fa-solid',
                esc_url( $this->assets_url ) . 'js/fa-solid' . $this->script_suffix . '.js',
                array(),
                _VERSION_,
                true
            );
            wp_enqueue_script( $this->token . '-fa-solid' );
        } else {
            return;
        }
    
    }
    
    // End admin_enqueue_fa_scripts () */
    /**
     * Load plugin localisation
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation()
    {
        load_plugin_textdomain( 'wp-foft-loader', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    }
    
    // End load_localisation ()
    /**
     * Load plugin textdomain
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain()
    {
        $domain = 'wp-foft-loader';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    }
    
    // End load_plugin_textdomain ()
    /**
     * Main WP_FOFT_Loader Instance
     *
     * Ensures only one instance of WP_FOFT_Loader is loaded or can be loaded.
     *
     * @param string $file File instance.
     * @param string _VERSION_ Version parameter.
     *
     * @return Object WP_FOFT_Loader instance
     * @since 1.0.0
     * @static
     */
    public static function instance( $file = '' )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $file, _VERSION_ );
        }
        return self::$instance;
    }
    
    // End instance ()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Class_WP_FOFT_Loader is forbidden.', 'wp-foft-loader' ), esc_html( _VERSION_ ) );
    }
    
    // End __clone ()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Class_WP_FOFT_Loader is forbidden.', 'wp-foft-loader' ), esc_html( _VERSION_ ) );
    }
    
    // End __wakeup ()
    /**
     * Installation. Runs on activation.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->logversion_number();
    }
    
    // End install ()
    /**
     * Log the plugin version number.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function logversion_number()
    {
        update_option( $this->token . 'version', esc_html( _VERSION_ ) );
    }

}