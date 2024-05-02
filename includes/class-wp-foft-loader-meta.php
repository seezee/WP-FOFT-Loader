<?php

/**
 * Plugin Meta class file.
 *
 * @package WP FOFT Loader/Includes
 */
if ( !defined( 'ABSPATH' ) ) {
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
    public function links() {
        // Filter the plugin meta.
        add_filter(
            'plugin_row_meta',
            array($this, 'meta_links'),
            10,
            2
        );
    }

    /**
     * Custom links.
     *
     * @param string $links Custom links.
     * @param string $file Path to main plugin file.
     */
    public function meta_links( $links, $file ) {
        $plugin = 'wp-foft-loader.php';
        // Only for this plugin.
        if ( strpos( $file, $plugin ) !== false ) {
            $supportlink = 'https://wordpress.org/support/plugin/wp-foft-loader/';
            $support_label = esc_attr_x( 'WP FOFT Loader Support', 'noun', 'fsrs' );
            $donatelink = 'https://paypal.me/messengerwebdesign?locale.x=en_US';
            $reviewlink = 'https://wordpress.org/support/view/plugin-reviews/wp-foft-loader?rate=5#postform';
            $twitterlink = 'https://twitter.com/czahller';
            $coffeelink = 'https://www.buymeacoffee.com/chrisjzahller';
            $iconstyle = 'style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;"';
            return array_merge( $links, array(
                '<a href="' . esc_url( $supportlink ) . '"> <span class="dashicons dashicons-format-chat" ' . $iconstyle . 'title="' . $support_label . '" aria-label="' . $support_label . '"></span></a>',
                /* translators: "Chris J. Zähller" is the plugin author. */
                '<a href="' . esc_url( $twitterlink ) . '"><span class="dashicons dashicons-twitter" ' . $iconstyle . 'title="' . esc_attr__( 'Chris J. Zähller on Twitter', 'fsrs' ) . '" aria-label="' . esc_attr__( 'Chris J. Zähller on Twitter', 'fsrs' ) . '"></span></a>',
                '<a href="' . esc_url( $reviewlink ) . '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="' . esc_attr__( 'Give a 5-Star Review', 'wp-foft-loader' ) . '" aria-label="' . esc_attr__( 'Give a 5-Star Review', 'wp-foft-loader' ) . '"></span></a>',
                '<a href="' . esc_url( $donatelink ) . '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="' . esc_attr__( 'Donate', 'wp-foft-loader' ) . '" aria-label="' . esc_attr__( 'Donate', 'wp-foft-loader' ) . '"></span></a>',
                '<a href="' . esc_url( $coffeelink ) . '"><span class="dashicons dashicons-coffee"' . $iconstyle . 'title="' . esc_attr__( 'Buy the Developer a Coffee', 'wp-foft-loader' ) . '" aria-label="' . esc_attr__( 'Buy the Developer a Coffee', 'wp-foft-loader' ) . '"></span></a>',
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
            self::$instance = new self($parent);
        }
        return self::$instance;
    }

    // End instance()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_Meta is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_Meta is forbidden.' ), esc_attr( WPFL_VERSION ) );
    }

    // End __wakeup()
}

$meta = new WP_FOFT_Loader_Meta();
$meta->links();