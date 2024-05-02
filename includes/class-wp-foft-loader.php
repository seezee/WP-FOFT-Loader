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
class WP_FOFT_Loader {
    /**
     * The single instance of WP_FOFT_Loader.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static $instance = null;

    /**
     * Local instance of WP_FOFT_Loader_API
     *
     * @var WP_FOFT_Loader_API|null
     */
    public $admin = null;

    /**
     * Settings class object
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * Constructor function.
     *
     * @param string $file File constructor.
     */
    public function __construct( $file = '' ) {
        $this->token = 'wp-foft-loader';
        // Load plugin environment variables.
        $this->file = $file;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
        $this->script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        // Use minified script.
        register_activation_hook( $this->file, array($this, 'install') );
        // Load admin JS & CSS.
        add_action(
            'admin_enqueue_scripts',
            array($this, 'admin_enqueue_styles'),
            10,
            1
        );
        add_action(
            'admin_enqueue_scripts',
            array($this, 'admin_enqueue_scripts'),
            10,
            1
        );
        add_action(
            'admin_enqueue_scripts',
            array($this, 'admin_enqueue_fa_scripts'),
            10,
            1
        );
        // Load API for generic admin functions.
        if ( is_admin() ) {
            $this->admin = new WP_FOFT_Loader_Admin_API();
        }
        // Handle localisation.
        $this->load_plugin_textdomain();
        add_action( 'init', array($this, 'load_localisation'), 0 );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // Display the admin notification.
            add_action( 'admin_notices', array($this, 'free_activation') );
        }
    }

    // End __construct ()
    /**
     * Displays an activation notice.
     */
    public function free_activation() {
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // $pagenow is a global variable referring to the filename of the
            // current page, such as ‘admin.php’, ‘post-new.php’.
            global $pagenow;
            if ( 'options-general.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
                return;
            }
            $html = '<div id="activated" class="notice notice-info is-dismissible">';
            $html .= '<p>';
            $url1 = '//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/';
            $url2 = '//checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/?trial=free';
            $rel = 'noreferrer noopener';
            $link = sprintf(
                wp_kses( 
                    /* translators: ignore the placeholders in the URL */
                    __( 'Thank you for installing WP FOFT Loader. For small-caps and additional font weights support, please upgrade to <a href="%1$s" rel="%3$s">WP FOFT Loader PRO</a>. Not sure if you need those features? We have a <a href="%2$s" rel="%3$s">FREE 14-day trial</a>.', 'wp-foft-loader' ),
                    array(
                        'a' => array(
                            'href' => array(),
                            'rel'  => array(),
                        ),
                    )
                 ),
                esc_url( $url1 ),
                esc_url( $url2 ),
                $rel
            );
            $html .= $link;
            $html .= '</p>';
            $html .= '</div>';
            echo $html;
            // phpcs:ignore
        }
    }

    //end free_activation()
    /**
     * Admin enqueue style.
     *
     * @param string $hook Hook parameter.
     *
     * @return void
     */
    public function admin_enqueue_styles( $hook ) {
        global $pagenow;
        if ( 'plugins.php' !== $pagenow && 'settings_page_wp-foft-loader' !== $hook || !current_user_can( 'install_plugins' ) ) {
            return;
        }
        wp_register_style(
            $this->token . '-admin',
            esc_url( $this->assets_url ) . 'css/admin' . $this->script_suffix . '.css',
            array(),
            esc_html( WPFL_VERSION )
        );
        wp_enqueue_style( $this->token . '-admin' );
    }

    // End admin_enqueue_styles ()
    /**
     * Load admin Javascript.
     *
     * @access  public
     *
     * @return  void
     * @since   1.0.0
     */
    public function admin_enqueue_scripts() {
        global $pagenow;
        if ( 'options-general.php' !== $pagenow || !current_user_can( 'install_plugins' ) ) {
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
     * @return  bool
     * @since   1.0.0
     */
    public function admin_enqueue_fa_scripts( $hook = '' ) {
        global $pagenow;
        if ( 'plugins.php' === $pagenow || 'options-general.php' === $pagenow ) {
            $protocol = 'https:';
            $url = '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/fontawesome';
            $fallback = esc_url( $this->assets_url ) . 'js/fontawesome';
            $suffix = $this->script_suffix . '.js';
            $link = $protocol . $url . $suffix;
            /**
             * Check whether external files are available.
             *
             * @access public
             *
             * @param string $link Link parameter.
             *
             * @since   1.0.0
             */
            function wpfl_checklink(  $link  ) {
                return (bool) @fopen( $link, 'r' );
                // phpcs:ignore
            }

            // If boolean is TRUE.
            if ( wpfl_checklink( $link ) ) {
                wp_register_script(
                    $this->token . '-fa-main',
                    $url . $this->script_suffix . '.js',
                    array(),
                    WPFL_VERSION,
                    true
                );
                // Otherwise use local copy.
            } else {
                wp_register_script(
                    $this->token . '-fa-main',
                    $fallback . $this->script_suffix . '.js',
                    array(),
                    esc_html( WPFL_VERSION ),
                    true
                );
            }
            wp_enqueue_script( $this->token . '-fa-main' );
            // We're using a specially optimized and renamed version of
            // fa-solid.js to load only the necessary Fontawesome glyphs, i.e.,
            // fa-font. In the event we ever need to add more
            // glyphs, both scripts, i.e., fa-wpfl-solid.js &
            // fa-wpfl-solid.min.js, will need to be updated.
            wp_register_script(
                $this->token . '-fa-solid',
                esc_url( $this->assets_url ) . 'js/fa-wpfl-solid' . $this->script_suffix . '.js',
                array(),
                WPFL_VERSION,
                true
            );
            wp_enqueue_script( $this->token . '-fa-solid' );
        } else {
            return;
        }
    }

    // End admin_enqueue_fa_scripts () */
    /**
     * Hash external javascripts
     *
     * @param string $tag Script HTML tag.
     * @param string $handle WordPress script handle.
     */
    public function hash_js( $tag, $handle ) {
        // add script handles to the array below.
        if ( wpfl_checklink( $link ) ) {
            if ( $this->token . '-fa-main' === $handle ) {
                if ( SCRIPT_DEBUG ) {
                    return str_replace( ' src', ' integrity="sha512-QTB14R2JdqeamILPFRrAgHOWmjlOGmwMg9WB9hrw6IoaX8OdY8J1kiuIAlAFswHCzgeY18PwTqp4g4utWdy6HA==" crossorigin="anonymous" src', $tag );
                } else {
                    return str_replace( ' src', ' integrity="sha512-PoFg70xtc+rAkD9xsjaZwIMkhkgbl1TkoaRrgucfsct7SVy9KvTj5LtECit+ZjQ3ts+7xWzgfHOGzdolfWEgrw==" crossorigin="anonymous" src', $tag );
                }
            }
            return $tag;
        }
    }

    /**
     * Load plugin localisation
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation() {
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
    public function load_plugin_textdomain() {
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
     * @var string WPFL_VERSION Version constant.
     *
     * @return Object WP_FOFT_Loader instance
     * @since 1.0.0
     * @static
     */
    public static function instance( $file = '' ) {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self($file, WPFL_VERSION);
        }
        return self::$instance;
    }

    // End instance ()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Class_WP_FOFT_Loader is forbidden.', 'wp-foft-loader' ), esc_html( WPFL_VERSION ) );
    }

    // End __clone ()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Class_WP_FOFT_Loader is forbidden.', 'wp-foft-loader' ), esc_html( WPFL_VERSION ) );
    }

    // End __wakeup ()
    /**
     * Installation. Runs on activation.
     *
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install() {
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
    private function logversion_number() {
        update_option( $this->token . 'version', esc_html( WPFL_VERSION ) );
    }

    // End logversion_number ()
}
