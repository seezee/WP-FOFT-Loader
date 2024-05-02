<?php

/**
 * Settings class file.
 *
 * @package WP FOFT Loader/Includes
 */
if ( !defined( 'ABSPATH' ) ) {
    'Sorry, you are not allowed to access this page directly.';
}
/**
 * Enqueue custom fonts.
 * Place font declaration and script in head -- using inline embed for
 * critical font load with data URI per
 * https://www.zachleat.com/web/comprehensive-webfonts/ .
 */
class WP_FOFT_Loader_Head {
    /**
     * The single instance of WP_FOFT_Loader_Head.
     *
     * @var    object
     * @access private
     * @since  1.0.0
     */
    private static $instance = null;

    /**
     * The main plugin object.
     *
     * @var    object
     * @access public
     * @since  1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     *
     * @var    string
     * @access public
     * @since  1.0.0
     */
    public $base = '';

    /**
     * Suffix for Javascripts.
     *
     * @var    string
     * @access public
     * @since  1.0.0
     */
    public $script_suffix;

    /**
     * Constructor function.
     */
    public function __construct() {
        $this->script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
        // Use minified script.
    }

    /**
     * Generate CSS & Javascript to be loaded in <head>.
     *
     * @access public
     * @since  1.0.0
     */
    public function fontload() {
        $plugin_url = plugin_dir_url( __DIR__ );
        // Locate font files.
        $uploads = wp_get_upload_dir();
        $font_path = $uploads['baseurl'] . '/fonts/';
        if ( null !== $uploads['baseurl'] && is_ssl() ) {
            // Rewrite to HTTPS if needed.
            $font_path = str_replace( 'http://', 'https://', $font_path );
        }
        $font_dir = $uploads['basedir'] . '/fonts/';
        $files = glob( $font_dir . '*.woff', GLOB_BRACE );
        // Preload the body font; load subsets.
        $arr = array();
        // Use this with wp_kses. Don't allow any HTML.
        // All options prefixed with WPFL_BASE value; see wp-foft-loader.php constants.
        if ( get_option( WPFL_BASE . 's1-heading' ) !== false ) {
            $heading = get_option( WPFL_BASE . 's1-heading' );
        } else {
            $heading = null;
        }
        if ( get_option( WPFL_BASE . 's1-body' ) !== false ) {
            $body = get_option( WPFL_BASE . 's1-body' );
        } else {
            $body = null;
        }
        if ( get_option( WPFL_BASE . 's1-alt' ) !== false ) {
            $alt = get_option( WPFL_BASE . 's1-alt' );
        } else {
            $alt = null;
        }
        if ( get_option( WPFL_BASE . 's1-mono' ) !== false ) {
            $mono = get_option( WPFL_BASE . 's1-mono' );
        } else {
            $mono = null;
        }
        $fdisplay = get_option( WPFL_BASE . 'font_display' );
        if ( !is_null( $body ) ) {
            echo '<link rel="preload" href="' . esc_url( $font_path . $body . '-optimized.woff2' ) . '" as="font" type="font/woff2" crossorigin>';
        }
        if ( !is_null( $heading ) ) {
            echo '<link rel="preload" href="' . esc_url( $font_path . $heading . '-optimized.woff2' ) . '" as="font" type="font/woff2" crossorigin>';
        }
        if ( !is_null( $alt ) ) {
            echo '<link rel="preload" href="' . esc_url( $font_path . $alt . '-optimized.woff2' ) . '" as="font" type="font/woff2" crossorigin>';
        }
        if ( !is_null( $mono ) ) {
            echo '<link rel="preload" href="' . esc_url( $font_path . $mono . '-optimized.woff2' ) . '" as="font" type="font/woff2" crossorigin>';
        }
        echo '<style type="text/css">';
        // Styles start.
        if ( !is_null( $body ) ) {
            // Body subset @font-face.
            echo 
                '@font-face{font-family:',
                wp_kses( $body, $arr ),
                'Subset;src:url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $body, $arr ),
                '-optimized.woff2)format("woff2"),url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $body, $arr ),
                '-optimized.woff)format("woff");unicode-range:U+41-5A, U+61-7A;font-display:' . wp_kses( $fdisplay, $arr ) . '}'
            ;
        }
        if ( !is_null( $heading ) ) {
            // Heading & display subset @font-face.
            echo 
                '@font-face{font-family:',
                wp_kses( $heading, $arr ),
                'Subset;src:url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $heading, $arr ),
                '-optimized.woff2)format("woff2"),url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $heading, $arr ),
                '-optimized.woff)format("woff");unicode-range:U+41-5A, U+61-7A;font-display:' . wp_kses( $fdisplay, $arr ) . '}'
            ;
        }
        if ( !is_null( $alt ) ) {
            // UI elements subset @font-face.
            echo 
                '@font-face{font-family:',
                wp_kses( $alt, $arr ),
                'Subset;src:url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $alt, $arr ),
                '-optimized.woff2)format("woff2"),url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $alt, $arr ),
                '-optimized.woff)format("woff");unicode-range:U+41-5A, U+61-7A;font-display:' . wp_kses( $fdisplay, $arr ) . '}'
            ;
        }
        if ( !is_null( $mono ) ) {
            // Monospace subset @font-face.
            echo 
                '@font-face{font-family:',
                wp_kses( $mono, $arr ),
                'Subset;src:url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $mono, $arr ),
                '-optimized.woff2)format("woff2"),url(',
                wp_kses( $font_path, $arr ),
                wp_kses( $mono, $arr ),
                '-optimized.woff)format("woff");unicode-range:U+41-5A, U+61-7A;font-display:' . wp_kses( $fdisplay, $arr ) . '}'
            ;
        }
        $suffix = '-webfont';
        $fam = array();
        foreach ( $files as &$file ) {
            if ( !fnmatch( '*optimized*', $file ) ) {
                $font = basename( $file, '.woff' );
                // remove the file type.
                $font = str_replace( $suffix, '', $font );
                // remove the -webfont suffix.
                list( $family, $style ) = explode( '-', $font, 2 );
                // explode for 2 parts: family and style.
                echo '@font-face{font-family:\'' . wp_kses( $family, $arr ) . '\';src:url(' . esc_url( $font_path . basename( $file ) ) . '2)format(\'woff2\'),url(' . esc_url( $font_path . basename( $file ) ) . ')format(\'woff\');';
                $fontstyle = 'normal';
                if ( !wpfl_fs()->can_use_premium_code() ) {
                    if ( in_array( $style, array('italic', 'boldItalic'), true ) ) {
                        // Third parameter enables strict type checking -- see
                        // https://php.net/manual/en/function.in-array.php.
                        $fontstyle = 'italic';
                    }
                }
                $fontweight = '400';
                if ( in_array( $style, array('bold', 'boldItalic'), true ) ) {
                    $fontweight = '700';
                }
                echo 'font-weight:' . wp_kses( $fontweight, $arr ) . ';font-style:' . wp_kses( $fontstyle, $arr ) . ';';
                echo 'font-display:' . wp_kses( $fdisplay, $arr ) . ';}';
                /*
                				Name                      Weight
                				Thin, Hairline            100
                				Extra Light, Ultra Light  200
                				Light                     300
                				Normal, Regular           400
                				Medium                    500
                				Semi Bold, Demi Bold      600
                				Bold                      700
                				Extra Bold, Ultra Bold    800
                				Black, Heavy              900
                */
            }
        }
        $fs_heading = get_option( WPFL_BASE . 'fstack-heading' );
        $fs_heading = ',' . $fs_heading;
        $fs_body = get_option( WPFL_BASE . 'fstack-body' );
        $fs_body = ',' . $fs_body;
        $fs_alt = get_option( WPFL_BASE . 'fstack-alt' );
        $fs_alt = ',' . $fs_alt;
        $fs_mono = get_option( WPFL_BASE . 'fstack-mono' );
        $fs_mono = ',' . $fs_mono;
        echo 'body{font-family:serif;font-weight:400;font-style:normal}';
        $default_css = get_option( WPFL_BASE . 'default_css' );
        if ( !is_null( $body ) && !is_null( $fs_body ) ) {
            echo '.fonts-stage-1 body{font-family:' . wp_kses( $body, $arr ) . 'Subset,';
            if ( !wpfl_fs()->can_use_premium_code() ) {
                echo 'serif';
            }
            echo '}';
        }
        if ( !is_null( $heading ) && !is_null( $fs_heading ) ) {
            echo '.fonts-stage-1 h1,.fonts-stage-1 h2,.fonts-stage-1 h3,.fonts-stage-1 h4,.fonts-stage-1 h5,.fonts-stage-1 h6{font-family:' . wp_kses( $heading, $arr ) . 'Subset,';
            if ( !wpfl_fs()->can_use_premium_code() ) {
                echo 'serif';
            }
            echo '}';
        }
        if ( !is_null( $alt ) && !is_null( $fs_alt ) ) {
            echo '.fonts-stage-1 button,.fonts-stage-1 input,.fonts-stage-1 nav,.fonts-stage-1 optgroup,.fonts-stage-1 select,.fonts-stage-1 textarea{font-family:' . wp_kses( $alt, $arr ) . 'Subset,sans-serif}';
        }
        if ( !is_null( $mono ) && !is_null( $fs_mono ) ) {
            echo '.fonts-stage-1 code,.fonts-stage-1 kbd,.fonts-stage-1 samp{font-family:' . wp_kses( $mono, $arr ) . 'Subset,monospace}';
        }
        if ( isset( $default_css ) ) {
            if ( 'on' === $default_css ) {
                if ( !is_null( $body ) ) {
                    echo '.fonts-stage-2 body,.fonts-stage-2 h4,.fonts-stage-2 h5,.fonts-stage-2 h6{font-family:' . wp_kses( $body, $arr ) . wp_kses( $fs_body, $arr ) . '}';
                }
                if ( !is_null( $heading ) ) {
                    echo '.fonts-stage-2 h1,.fonts-stage-2 h2,.fonts-stage-2 h3{font-family:' . wp_kses( $heading, $arr ) . wp_kses( $fs_heading, $arr ) . ';font-weight:400}';
                }
                echo '.fonts-stage-2 code strong,.fonts-stage-2 h4,.fonts-stage-2 h5,.fonts-stage-2 h6,.fonts-stage-2 strong,.fonts-stage-2 strong code{font-weight:700}.fonts-stage-2 h1 strong,.fonts-stage-2 h2 strong,.fonts-stage-2 h3 strong,.fonts-stage-2 strong h1,.fonts-stage-2 strong h2,.fonts-stage-2 strong h3{font-weight:900}.fonts-stage-2 em strong h1,.fonts-stage-2 h1 em strong,.fonts-stage-2 h1 strong em,.fonts-stage-2 strong em h1{font-weight:900;font-style:italic}.fonts-stage-2 abbr{font-weight:700;font-variant:small-caps;padding:0 .13333rem 0 0;letter-spacing:.06667rem;text-transform:lowercase}';
                if ( !is_null( $mono ) ) {
                    echo '.fonts-stage-2 code,.fonts-stage-2 kbd,.fonts-stage-2 samp{font-family:' . wp_kses( $mono, $arr ) . wp_kses( $fs_mono, $arr ) . '}';
                }
                echo '.fonts-stage-2 cite>em,.fonts-stage-2 cite>q,.fonts-stage-2 em>cite,.fonts-stage-2 em>em,.fonts-stage-2 em>q,.fonts-stage-2 figcaption>cite,.fonts-stage-2 figcaption>em,.fonts-stage-2 q>cite,.fonts-stage-2 q>em{font-style:normal}.fonts-stage-2 code em,.fonts-stage-2 em,.fonts-stage-2 em code,.fonts-stage-2 figcaption,.fonts-stage-2 h2,.fonts-stage-2 h3{font-style:italic}.fonts-stage-2 code em strong,.fonts-stage-2 code strong em,.fonts-stage-2 em code strong,.fonts-stage-2 em strong,.fonts-stage-2 em strong code,.fonts-stage-2 strong code em,.fonts-stage-2 strong em,.fonts-stage-2 strong em code{font-weight:700;font-style:italic}';
                if ( !is_null( $alt ) ) {
                    echo '.fonts-stage-2 button,.fonts-stage-2 input,.fonts-stage-2 nav,.fonts-stage-2 optgroup,.fonts-stage-2 select,.fonts-stage-2 textarea{font-family:' . wp_kses( $alt, $arr ) . wp_kses( $fs_alt, $arr ) . ';font-weight:400}';
                }
            }
        }
        // User input custom CSS. Sanitize with HTMLPurifier + CSSTidy.
        $css_dirty_1 = get_option( WPFL_BASE . 'stage_1' );
        $css_dirty_2 = get_option( WPFL_BASE . 'stage_2' );
        // Create a new configuration object.
        $config = HTMLPurifier_Config::createDefault();
        $config->set( 'CSS.Proprietary', true );
        $config->set( 'Filter.ExtractStyleBlocks', true );
        // Create a new purifier instance.
        $purifier = new HTMLPurifier($config);
        // Wrap our CSS in style tags and pass to purifier.
        // We're not actually interested in the html response though.
        $html = $purifier->purify( '<style type="text/css">' . $css_dirty_1 . $css_dirty_2 . '</style>' );
        // The "style" blocks are stored seperately.
        $clean_css = $purifier->context->get( 'StyleBlocks' );
        /**
         * Minification utility.
         *
         * @param string $string Input to be minified.
         * @since 1.0.0
         */
        function compress(  $string  ) {
            // Convert '>' back to utf-8. 4 backslashes needed; see
            // "https://stackoverflow.com/questions/4025482/cant-escape-the-backslash-with-regex".
            $string = preg_replace( '/(\\\\3E)/', '>', $string );
            // Merge multiple spaces into one space.
            $string = preg_replace( '/\\s+/', ' ', $string );
            // Remove final semicolon & whitespace.
            $string = preg_replace( '/;\\s+}/', '}', $string );
            // Trim whitespace before opening curly brace.
            $string = preg_replace( '/\\s+{\\s+/', '{', $string );
            // Trim whitespaces after commas.
            $string = preg_replace( '/\\,\\s+/', ',', $string );
            return $string;
        }

        ob_start( 'compress' );
        // Parse DOM Tree.
        // Everything inside gets minified.
        // Get the first style block.
        echo wp_kses( $clean_css[0], $arr ) . '</style>';
        // Styles end.
        ob_end_flush();
        // End minification.
        $bodyload = null;
        $headingload = null;
        $altload = null;
        $monoload = null;
        if ( !is_null( $body ) ) {
            $bodyload = '"1em ' . wp_kses( $body, $arr ) . '", ';
        }
        if ( !is_null( $heading ) && (!is_null( $body ) && $body !== $heading) ) {
            // Output only if it doesn't duplicate the body font.
            $headingload = '"1em ' . wp_kses( $heading, $arr ) . '", ';
        }
        if ( !is_null( $alt ) && (!is_null( $body ) && $body !== $alt && $heading !== $alt) ) {
            // Output only if it doesn't duplicate the body or heading fonts.
            $altload = '"1em ' . wp_kses( $alt, $arr ) . '", ';
        }
        if ( !is_null( $mono ) && (!is_null( $body ) && $body !== $mono) ) {
            // Output only if it doesn't duplicate the body font.
            $monoload = '"1em ' . wp_kses( $mono, $arr ) . '", ';
        }
        $fontsloaded = $bodyload . $headingload . $altload . $monoload;
        $fontsloaded = rtrim( $fontsloaded, ', ' );
        // Trim trailing comma & space.
        if ( !is_null( $bodyload ) || !is_null( $headingload ) || !is_null( $altload ) || !is_null( $monoload ) ) {
            echo '<script>
(function() {
	"use strict";

	if ( sessionStorage.fontsLoadedCriticalFoftPreloadFallback ) {
		document.documentElement.className += " fonts-stage-2";
		return;
	} else if ( "fonts" in document ) {
		document.fonts.load(' . wp_kses( $fontsloaded, $arr ) . ').then(function () {
			document.documentElement.className += " fonts-stage-1";

			Promise.all([';
            $promise1 = null;
            $promise2 = null;
            $promise3 = null;
            $promise4 = null;
            $promises = null;
            if ( !is_null( $body ) ) {
                $promise1 .= '
				document.fonts.load("400 1em ' . wp_kses( $body, $arr ) . '"),
				document.fonts.load("700 1em ' . wp_kses( $body, $arr ) . '"),
				document.fonts.load("italic 1em ' . wp_kses( $body, $arr ) . '"),
				document.fonts.load("italic 700 1em ' . wp_kses( $body, $arr ) . '"),';
            }
            if ( !is_null( $heading ) && (!is_null( $body ) && $body !== $heading) ) {
                // Output only if it doesn't duplicate the body font.
                $promise2 .= '
				document.fonts.load("400 1em ' . wp_kses( $heading, $arr ) . '"),
				document.fonts.load("700 1em ' . wp_kses( $heading, $arr ) . '"),
				document.fonts.load("italic 1em ' . wp_kses( $heading, $arr ) . '"),
				document.fonts.load("italic 700 1em ' . wp_kses( $heading, $arr ) . '"),';
            }
            if ( !is_null( $alt ) && (!is_null( $body ) && $alt !== $heading) ) {
                // Output only if it doesn't duplicate the body font.
                $promise3 .= '
				document.fonts.load("400 1em ' . wp_kses( $alt, $arr ) . '"),
				document.fonts.load("700 1em ' . wp_kses( $alt, $arr ) . '"),
				document.fonts.load("italic 1em ' . wp_kses( $alt, $arr ) . '"),
				document.fonts.load("italic 700 1em ' . wp_kses( $alt, $arr ) . '"),';
            }
            if ( !is_null( $mono ) && (!is_null( $body ) && $body !== $mono) ) {
                // Output only if it doesn't duplicate the body font.
                $promise4 .= '
				document.fonts.load("400 1em ' . wp_kses( $mono, $arr ) . '"),
				document.fonts.load("700 1em ' . wp_kses( $mono, $arr ) . '"),
				document.fonts.load("italic 1em ' . wp_kses( $mono, $arr ) . '"),
				document.fonts.load("italic 700 1em ' . wp_kses( $mono, $arr ) . '"),';
            }
            if ( !is_null( $promise1 ) ) {
                $promises = $promise1;
            }
            if ( !is_null( $promise2 ) ) {
                $promises .= $promise2;
            }
            if ( !is_null( $promise3 ) ) {
                $promises .= $promise3;
            }
            if ( !is_null( $promise4 ) ) {
                $promises .= $promise4;
            }
            echo wp_kses( rtrim( $promises, ',' ), $arr );
            // Trim trailing comma.
            echo '
			]).then(function () {
				document.documentElement.className += " fonts-stage-2";

				// Optimization for Repeat Views
				sessionStorage.fontsLoadedCriticalFoftPreloadFallback = true;
			}).catch(console.log.bind(console));
		});
	} else {
		// use fallback
		var ref = document.getElementsByTagName( "script" )[ 0 ];
		var script = document.createElement( "script" );
		script.src = "' . wp_kses( $plugin_url, $arr ) . 'assets/js/fallback' . wp_kses( $this->script_suffix, $arr ) . '.js?' . wp_kses( WPFL_VERSION, $arr ) . '";
		script.async = true;
		ref.parentNode.insertBefore( script, ref );

	}
})();
</script>';
        }
    }

    /**
     * Place the CSS & JS in the head.
     *
     * @access public
     * @since  1.0.0
     */
    public function foft_head() {
        $this->fontload();
    }

    /**
     * Main WP_FOFT_Loader_Head Instance
     *
     * Ensures only one instance of WP_FOFT_Loader_Head is loaded or can be loaded.
     *
     * @since  1.0.0
     * @static
     * @see    WP_FOFT_Loader()
     * @param  object $parent Object instance.
     * @return Main WP_FOFT_Loader_Head instance
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
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_Head is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_Head is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __wakeup()
}

/**
* Place the @font declaration in the header.
*/
$head = new WP_FOFT_Loader_Head();
add_action( 'wp_head', array($head, 'foft_head') );