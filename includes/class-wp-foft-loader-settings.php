<?php

/**
 * Settings class file.
 *
 * @package WP FOFT Loader/Settings
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Settings class.
 */
class WP_FOFT_Loader_Settings {
    /**
     * The single instance of WP_FOFT_Loader_Settings.
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
     * Available settings for plugin.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    /**
     * Constructor function.
     *
     * @param object $parent Parent object.
     */
    public function __construct( $parent ) {
        $this->parent = $parent;
        // Initialise settings.
        add_action( 'init', array($this, 'init_settings'), 11 );
        // Register plugin settings.
        add_action( 'admin_init', array($this, 'register_settings') );
        // Add settings page to menu.
        add_action( 'admin_menu', array($this, 'add_menu_item') );
        // Add settings link to plugins page.
        add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array($this, 'add_settings_link') );
        // Configure placement of plugin settings page. See readme for implementation.
        add_filter( WPFL_BASE . 'menu_settings', array($this, 'configure_settings') );
    }

    /**
     * Initialise settings
     *
     * @return void
     */
    public function init_settings() {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     *
     * @return void
     */
    public function add_menu_item() {
        $args = $this->menu_settings();
        // Do nothing if wrong location key is set.
        if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
            switch ( $args['location'] ) {
                case 'options':
                case 'submenu':
                    $page = add_submenu_page(
                        $args['parent_slug'],
                        $args['page_title'],
                        $args['menu_title'],
                        $args['capability'],
                        $args['menu_slug'],
                        $args['function']
                    );
                    break;
                case 'menu':
                    $page = add_menu_page(
                        $args['page_title'],
                        $args['menu_title'],
                        $args['capability'],
                        $args['menu_slug'],
                        $args['function'],
                        $args['icon_url'],
                        $args['position']
                    );
                    break;
                default:
                    return;
            }
            add_action( 'admin_print_styles-' . $page, array($this, 'settings_assets') );
        }
    }

    /**
     * Prepare default settings page arguments
     *
     * @return mixed|void
     */
    private function menu_settings() {
        return apply_filters( WPFL_BASE . 'menu_settings', array(
            'location'    => 'options',
            'parent_slug' => 'options-general.php',
            'page_title'  => esc_html__( 'WP FOFT Loader Settings', 'wp-foft-loader' ),
            'menu_title'  => esc_html__( 'WP FOFT Loader', 'wp-foft-loader' ),
            'capability'  => 'manage_options',
            'menu_slug'   => $this->parent->token,
            'function'    => array($this, 'settings_page'),
            'icon_url'    => '',
            'position'    => null,
        ) );
    }

    /**
     * Container for settings page arguments
     *
     * @param array $settings Settings array.
     *
     * @return array
     */
    public function configure_settings( $settings = array() ) {
        return $settings;
    }

    /**
     * Load settings JS & CSS
     *
     * @return void
     */
    public function settings_assets() {
        // We're including the WP media scripts here because they're needed for the image upload field.
        wp_enqueue_media();
        wp_register_script(
            $this->parent->token . '-settings-js',
            $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js',
            'jquery',
            '1.0.18',
            true
        );
        wp_enqueue_script( $this->parent->token . '-settings-js' );
    }

    /**
     * Add settings link to plugin list table.
     *
     * @param  array $links Existing links.
     * @return array        Modified links
     */
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . esc_url( 'options-general.php?page=' . $this->parent->token ) . '">' . esc_html__( 'Settings', 'wp-foft-loader' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    /**
     * Open media uploader on Upload tab instead of Library view
     */
    public function upload_media_manager_by_default() {
        if ( did_action( 'wp_enqueue_media' ) ) {
            ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
  wp.media.controller.Library.prototype.defaults.contentUserSetting = false;
  wp.media.controller.FeaturedImage.prototype.defaults.contentUserSetting = false;
});
</script>
			<?php 
        }
    }

    /**
     * Build settings fields
     *
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields() {
        // Locate font files so we can display a list later.
        $uploads = wp_get_upload_dir();
        $font_path = $uploads['baseurl'] . '/fonts/';
        $font_dir = $uploads['basedir'] . '/fonts/';
        $files = glob( $font_dir . '*.woff', GLOB_BRACE );
        $optfiles = glob( $font_dir . '*optimized*', GLOB_BRACE );
        $optcount = count( $optfiles ) * 0.5;
        $filecount = count( $files ) - $optcount;
        if ( $files ) {
            if ( 1 === $filecount ) {
                $uploadmessage = '<h3>' . esc_html__( 'You have uploaded the following font:', 'wp-foft-loader' ) . '</h3> ';
            } else {
                $uploadmessage = '<h3>' . esc_html__( 'You have uploaded the following fonts:', 'wp-foft-loader' ) . '</h3> ';
            }
            if ( 1 === $optcount ) {
                $uploadmessage2 = '<h3>' . esc_html__( 'You have uploaded the following font:', 'wp-foft-loader' ) . '</h3> ';
            } else {
                $uploadmessage2 = '<h3>' . esc_html__( 'You have uploaded the following fonts:', 'wp-foft-loader' ) . '</h3> ';
            }
        } else {
            $uploadmessage = '<h3>' . esc_html__( 'You have not uploaded any fonts.', 'wp-foft-loader' ) . '</h3>';
            $uploadmessage2 = $uploadmessage;
        }
        $suffix = '-webfont';
        $fam = array();
        // For wp_kses.
        $allowed_html = array(
            'li' => array(
                'class' => array(),
                'id'    => array(),
            ),
        );
        ob_start();
        // Buffer foreach output.
        foreach ( $files as &$file ) {
            if ( !fnmatch( '*optimized*', $file ) ) {
                // Non-subsetted files.
                $font = basename( $file, '.woff' );
                // Remove the file type.
                list( $family, $style, $type ) = explode( '-', $font, 3 );
                // Explode for 3 parts: family, style & type (-webfont).
                echo '<li>' . wp_kses( $font, $fam ) . '</li>';
            }
        }
        $fontlist = ob_get_clean();
        // Get buffer & display list of uploaded fonts.
        ob_start();
        foreach ( $files as &$file ) {
            if ( !fnmatch( '*optimized*', $file ) ) {
                $font = basename( $file, '.woff' );
                $fonts = explode( '-', $font, 3 );
                // Explode for 1 part: family.
                echo wp_kses( $fonts[0], $fam ) . ',';
            }
        }
        $choices = ob_get_clean();
        $choices = wp_kses( $choices, $fam );
        // Sanitize the input.
        $c_str = implode( ',', array_unique( explode( ',', $choices ) ) );
        // Remove duplicate font-families & convert the array to a string.
        $c_str = rtrim( $c_str, ',' );
        // Trim trailing comma & space.
        $c_arr = explode( ',', $c_str );
        // Split at comma & make an array.
        if ( empty( $c_arr ) ) {
            $c_arr = null;
        } else {
            list( $c1, $c2, $c3, $c4, $c5 ) = array_pad( $c_arr, 5, null );
            // Assign variables to the array values. Used below to assign $heading,
            // $body, $alt, & $mono. Use array_pad() to avoid undefined offset. See
            // https://stackoverflow.com/questions/24401788/php-undefined-offset-
            // from-list.
        }
        ob_start();
        foreach ( $files as &$file ) {
            if ( fnmatch( '*optimized*', $file ) ) {
                // Subsetted files only.
                $font = basename( $file, '.woff' );
                list( $family, $type ) = explode( '-', $font, 2 );
                // explode for 2 parts: family & type (-optimized).
                echo '<li>' . wp_kses( $font, $fam ) . '</li>';
            }
        }
        $ofontlist = ob_get_clean();
        // Get buffer & display list of subsetted fonts.
        $sq_url = '//www.fontsquirrel.com/tools/webfont-generator';
        $moz_url = '//developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display';
        $subs_url = '?page=' . $this->parent->token . '&tab=subset';
        $docs_url = '?page=' . $this->parent->token . '&tab=documentation';
        $upload_url = '?page=' . $this->parent->token . '&tab=upload';
        $css_url = '?page=' . $this->parent->token . '&tab=css';
        $fstack_url = '?page=' . $this->parent->token . '&tab=fstack';
        $rel = 'external noreferrer noopener';
        $target = '_blank';
        $arr = array(
            'a'      => array(
                'href'   => array(),
                'rel'    => array(),
                'target' => array(),
                'class'  => array(),
            ),
            'em'     => array(),
            'strong' => array(),
            'abbr'   => array(),
            'code'   => array(),
            'mark'   => array(),
            'span'   => array(
                'class' => array(),
            ),
        );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // FREE version & PRO users without a valid license.
            $settings['upload'] = array(
                'title'       => esc_html__( 'Upload', 'wp-foft-loader' ),
                'description' => '<p>' . esc_html__( 'Upload two files for each web font: a WOFF file and a WOFF2 file. In most cases you will upload regular, italic, bold, and bold italic versions of each font.', 'wp-foft-loader' ) . '</p>
<details>
  <summary>' . esc_html__( 'Preparing the Files', 'wp-foft-loader' ) . '
  </summary>
	<p>' . sprintf(
                    wp_kses( 
                        /* translators: ignore the placeholders in the URL */
                        __( 'We recommend you use <a href="%1$s" rel="%2$s" target="%3$s">Font Squirrel’s Webfont Generator</a> to generate the files. Recommended Font Squirrel settings are:', 'wp-foft-loader' ),
                        $arr
                     ),
                    esc_url( $sq_url ),
                    $rel,
                    $target
                ) . '</p>
  <dl class="col-3">
    <dt>Font Formats</dt>
    <dd>&ldquo;<abbr>WOFF</abbr>&rdquo;</dd>
    <dd>&ldquo;<abbr>WOFF2</abbr>&rdquo;</dd>
    <dt>Truetype Hinting</dt>
    <dd>&ldquo;Keep Existing&rdquo;</dd>
    <dt>Rendering</dt>
    <dd>' . esc_html__( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
    <dt>Vertical Metrics</dt>
    <dd>&ldquo;No Adjustment&rdquo;</dd>
    <dt>Fix Missing Glyphs</dt>
    <dd>' . esc_html__( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
    <dt>X-height Matching</dt>
    <dd>&ldquo;None&rdquo;</dd>
    <dt>Protection</dt>
    <dd>' . esc_html__( 'Select “WebOnly™” if you are using a commercially licensed font', 'wp-foft-loader' ) . '</dd>
    <dt>Subsetting</dt>
    <dd>' . esc_html__( '“Basic Subsetting” or “No Subsetting”', 'wp-foft-loader' ) . '</dd>
    <dt>OpenType Features</dt>
    <dd>' . esc_html__( 'Your choice, but we like “Keep All Features”', 'wp-foft-loader' ) . '</dd>
    <dt>OpenType Flattening</dt>
    <dd>None</dd>
    <dt><abbr>CSS</abbr></dt>
    <dd>' . esc_html__( 'Default options (leave all unchecked)', 'wp-foft-loader' ) . '</dd>
    <dt>Advanced Options</dt>
    <dd>&ldquo;Font Name Suffix&rdquo; = -webfont</dd>
    <dd>&ldquo;Em Square Value&rdquo; = 2048</dd>
    <dd>&ldquo;Adjust Glyph Spacing&rdquo; = 0</dd>
    <dt>Shortcuts</dt>
    <dd>&ldquo;Remember My Settings&rdquo;</dd>
  </dl>
</details>
<details>
  <summary>' . esc_html__( 'Naming the Files', 'wp-foft-loader' ) . '</summary>
  <p>' . wp_kses( __( '<strong>Filenames must follow the proper naming convention:</strong> <code>$family</code>SC-<code>$variant</code>-webfont.<code>$filetype</code>.', 'wp-foft-loader' ), $arr ) . '</p>
  <dl>
    <dt>' . esc_html__( '$family', 'wp-foft-loader' ) . '</dt>
    <dd>' . wp_kses( __( 'The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but <em>no hyphens or spaces</em>', 'wp-foft-loader.' ), $arr ) . '</dd>
	<dt>SC</dt>
	<dd>' . wp_kses( __( 'Small caps identifier (PRO only). <em>Optional</em>. Append to $family only if it is a small caps variant. <em>Case-sensitive</em>.', 'wp-foft-loader' ), $arr ) . '</dd>
    <dt>' . esc_html__( '$variant', 'wp-foft-loader' ) . '</dt>
    <dd>' . wp_kses( __( 'The font variant. Can be weight, style, or a combination of both. See “Allowed Weights & Mappings”. <em>Case-sensitive</em>.', 'wp-foft-loader' ), $arr ) . '</dd>
    <dt>-webfont</dt>
    <dd>' . esc_html__( 'Mandatory suffix. Append to $variant.', 'wp-foft-loader' ) . '</dd>
    <dt>' . esc_html__( '$filetype', 'wp-foft-loader' ) . '</dt>
    <dd>' . wp_kses( __( 'The file type, <abbr>i.e.</abbr>, “woff” or “woff2”.', 'wp-foft-loader' ), $arr ) . '</dd>
  </dl>
    <p>' . wp_kses( __( '<strong>Example</strong>: for the bold weight, italic style of Times New Roman, rename the files to <code>timesnewroman-boldItalic-webfont.woff</code> and <code>timesnewroman-boldItalic-webfont.woff2</code>. <strong>PRO only feature</strong>: for small caps style families, append <code>SC</code> (case-sensitive) to the family name, <abbr>e.g.</abbr>, <code>playfairdisplaySC-regular-webfont.woff</code>.', 'wp-foft-loader' ), $arr ) . '</p>
    <h3>' . esc_html__( '$family Examples', 'wp-foft-loader' ) . '</h3>
    <dl>
      <dt>' . esc_html_x( 'Correct', 'adjective', 'wp-foft-loader' ) . ':</dt>
      <dd><strong>playfairdisplay</strong> (' . esc_html__( 'all lowercase', 'wp-foft-loader' ) . ')</dd>
      <dd><strong>playfair_display</strong> (' . esc_html__( 'underscores allowed', 'wp-foft-loader' ) . ')</dd>
      <dd><strong>PlayfairDisplay</strong> (' . esc_html__( 'mixed case allowed', 'wp-foft-loader' ) . ')</dd>
    <dd><strong>playfairdisplaySC</strong> (' . esc_html__( '(PRO only) small-caps identifier', 'wp-foft-loader' ) . ')</dd>
    <dt>' . __( 'Incorrect', 'wp-foft-loader' ) . ':</dt>
    <dd><strong>playfair-display</strong> (' . esc_html__( 'hyphens not allowed', 'wp-foft-loader' ) . ')</dd>
    <dd><strong>playfair display</strong> (' . esc_html__( 'spaces prohibited', 'wp-foft-loader' ) . ')</dd>
    <dd><strong>Playfair Display</strong> (' . esc_html__( 'spaces prohibited', 'wp-foft-loader' ) . ')</dd>
    <dd><strong>playfairdisplaysc</strong> (' . esc_html__( '(PRO only) small-caps identifier is case-sensitive', 'wp-foft-loader' ) . ')</dd>
  </dl>
</details>
<details>
  <summary>' . esc_html__( 'Allowed Weights & Mappings', 'wp-foft-loader' ) . '</summary>' . esc_html__( 'Allowed weights and styles and their CSS mappings are:', 'wp-foft-loader' ) . '
  <ul class="col-3">
    <li>thin | hairline (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 100)</li>
    <li>extraLight | ultraLight (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 200)</li>
    <li>light (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 300)</li>
    <li>regular | normal (' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 400)</li>
    <li>medium (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 500)</li>
    <li>demiBold | semiBold (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 600)</li>
    <li>bold (' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 700)</li>
    <li>extraBold | ultraBold (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 800)</li>
    <li>black | heavy (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 900)</li>
    <li>thinItalic | hairlineItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 100)</li>
    <li>extraLightItalic | ultraLightItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 200)</li>
    <li>lightItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 300)</li>
    <li>italic (' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 400)</li>
    <li>mediumItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 500)</li>
    <li>demiBoldItalic | semiBoldItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 600)</li>
    <li>boldItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 700)</li>
    <li>extraBoldItalic | ultraBoldItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 800)</li>
    <li>blackItalic | heavyItalic (<strong>' . esc_html__( 'PRO only', 'wp-foft-loader' ) . '</strong>; ' . esc_html__( 'maps to', 'wp-foft-loader' ) . ' 900)</li>
</ul>
</details>
<details>
  <summary>' . esc_html__( 'Your Fonts', 'wp-foft-loader' ) . '</summary>' . $uploadmessage . '
  <ul class="col-3">' . wp_kses( $fontlist, $allowed_html ) . '</ul>
</details>
<p>' . esc_html__( 'This plugin supports 1&thinsp;&ndash;&thinsp;4 font families. After uploading your fonts, assign them as needed below.', 'wp-foft-loader' ) . '</p>',
                'fields'      => array(
                    array(
                        'id'          => 'font',
                        'label'       => esc_html_x( 'Upload Fonts', 'verb', 'wp-foft-loader' ),
                        'description' => wp_kses( __( 'Upload font files to your media library and store the attachment <abbr>ID</abbr> in the option field.', 'wp-foft-loader' ), $arr ),
                        'type'        => 'font',
                        'default'     => '',
                        'placeholder' => '',
                    ),
                    array(
                        'id'          => 's1-heading',
                        'label'       => esc_html__( 'Headings', 'wp-foft-loader' ),
                        'description' => esc_html__( 'Specify the display font used for high-level headings', 'wp-foft-loader' ) . '(H1, H2, & H3)',
                        'type'        => 'select',
                        'options'     => array(
                            0   => null,
                            $c1 => $c1,
                            $c2 => $c2,
                            $c3 => $c3,
                            $c4 => $c4,
                            $c5 => $c5,
                        ),
                        'default'     => null,
                    ),
                    array(
                        'id'          => 's1-body',
                        'label'       => esc_html_x( 'Body', 'e.g., Body Text', 'wp-foft-loader' ),
                        'description' => esc_html__( 'Specify the body text. This can be a serif or sans-serif font.', 'wp-foft-loader' ),
                        'type'        => 'select',
                        'options'     => array(
                            0   => null,
                            $c1 => $c1,
                            $c2 => $c2,
                            $c3 => $c3,
                            $c4 => $c4,
                            $c5 => $c5,
                        ),
                        'default'     => null,
                    ),
                    array(
                        'id'          => 's1-alt',
                        'label'       => esc_html__( 'Functional text', 'wp-foft-loader' ),
                        'description' => wp_kses( __( 'Specify non-body, functional text elements, <abbr>e.g.</abbr>, navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wp-foft-loader' ), $arr ),
                        'type'        => 'select',
                        'options'     => array(
                            0   => null,
                            $c1 => $c1,
                            $c2 => $c2,
                            $c3 => $c3,
                            $c4 => $c4,
                            $c5 => $c5,
                        ),
                        'default'     => null,
                    ),
                    array(
                        'id'          => 's1-mono',
                        'label'       => esc_html__( 'Monospaced', 'wp-foft-loader' ),
                        'description' => esc_html__( 'Specify monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
                        'type'        => 'select',
                        'options'     => array(
                            0   => null,
                            $c1 => $c1,
                            $c2 => $c2,
                            $c3 => $c3,
                            $c4 => $c4,
                            $c5 => $c5,
                        ),
                        'default'     => null,
                    )
                ),
            );
        }
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // FREE version & PRO users without a valid license.
            $settings['subset'] = array(
                'title'       => esc_html__( 'Subset', 'wp-foft-loader' ),
                'description' => '<p>' . esc_html__( 'Upload up to 4 subsetted fonts. For each font, upload a WOFF & WOFF2 file (for a total of up to 8 files). Each font will act as a placeholder until the full fonts load.', 'wp-foft-loader' ) . '</p><details><summary>' . esc_html__( 'Preparing the Files', 'wp-foft-loader' ) . '</summary><p>' . sprintf(
                    wp_kses( 
                        /* translators: ignore the placeholders in the URL */
                        __( 'We recommend you use <a href="%1$s" rel="%2$s" target="%3$s">Font Squirrel’s Webfont Generator</a> to generate the files. Recommended Font Squirrel settings are:', 'wp-foft-loader' ),
                        $arr
                     ),
                    esc_url( $sq_url ),
                    $rel,
                    $target
                ) . '</p>
    <dl class="col-3">
    <dt>Font Formats</dt>
    <dd>&ldquo;<abbr>WOFF</abbr>&rdquo;</dd>
    <dd>&ldquo;<abbr>WOFF2</abbr>&rdquo;</dd>
    <dt>Truetype Hinting</dt>
    <dd>&ldquo;Keep Existing&rdquo;</dd>
    <dt>Rendering</dt>
    <dd>' . esc_html__( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
    <dt>Vertical Metrics</dt>
    <dd>&ldquo;No Adjustment&rdquo;</dd>
    <dt>Fix Missing Glyphs</dt>
    <dd>' . esc_html__( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
    <dt>X-height Matching</dt>
    <dd>&ldquo;None&rdquo;</dd>
    <dt>Protection</dt>
    <dd>' . esc_html__( 'Select “WebOnly™” if you are using a commercially licensed font', 'wp-foft-loader' ) . '</dd>
  <dt>Subsetting</dt>
  <dd>' . esc_html__( '“Custom Subsetting” with the Unicode Ranges 0041-005A,0061-007A', 'wp-foft-loader' ) . '</dd>
  <dd>' . esc_html__( 'Leave everything else unchecked or blank', 'wp-foft-loader' ) . '</dd>
  <dt>OpenType Features</dt>
  <dd>None</dd>
  <dt>OpenType Flattening</dt>
  <dd>None</dd>
  <dt><abbr>CSS</abbr></dt>
  <dd>' . esc_html__( 'Default options (leave all unchecked)', 'wp-foft-loader' ) . '</dd>
    <dt>Advanced Options</dt>
    <dd>&ldquo;Font Name Suffix&rdquo; = -optimized</dd>
    <dd>&ldquo;Em Square Value&rdquo; = 2048</dd>
    <dd>&ldquo;Adjust Glyph Spacing&rdquo; = 0</dd>
    <dt>Shortcuts</dt>
    <dd>&ldquo;Remember My Settings&rdquo;</dd>
    </dl>
  </details>
  <details>
    <summary>' . esc_html__( 'Naming the Files', 'wp-foft-loader' ) . '</summary>
    <p>' . wp_kses( __( '<strong>Filenames must follow the proper naming convention:</strong> <code>$family</code>SC-optimized.<code>$filetype</code>.', 'wp-foft-loader' ), $arr ) . '</p>
    <dl>
      <dt>' . esc_html__( '$family', 'wp-foft-loader' ) . '</dt>
      <dd>' . wp_kses( __( 'The font family base name <em>without style</em> Case-insensitive. May contain letters, numerals, and underscores but <em>no hyphens or spaces</em>. <strong>Each <code>$family</code> base name should match the name used for the matching font uploaded on the previous upload screen.</strong>', 'wp-foft-loader' ), $arr ) . '</dd>
      <dt>SC</dt>
      <dd>' . wp_kses( __( 'Small caps identifier (PRO only). <em>Optional</em>. Append to $family only if it is a small caps variant. <em>Case-sensitive</em>.', 'wp-foft-loader' ), $arr ) . '</dd>
      <dt>-optimized</dt>
      <dd>' . esc_html__( 'Mandatory suffix. Append to $family.', 'wp-foft-loader' ) . '</dd>
      <dt>' . esc_html__( '$filetype', 'wp-foft-loader' ) . '</dt>
      <dd>' . wp_kses( __( 'The file type, <abbr>i.e.</abbr>, “woff” or “woff2”.', 'wp-foft-loader' ), $arr ) . '</dd>
    </dl>
    <p>' . wp_kses( __( '<strong>Example</strong>: If you uploaded <code>timesnewroman-regular-webfont.woff</code> and <code>timesnewroman-regular-webfont.woff2</code> as your body font on the previous screen, name the subsetted versions  <code>timesnewroman-optimized.woff</code> and  <code>timesnewroman-optimized.woff2</code> respectively.', 'wp-foft-loader' ), $arr ) . '</p>
  </details>
  <details>
    <summary>' . esc_html__( 'Your Fonts', 'wp-foft-loader' ) . '</summary>' . $uploadmessage2 . '
    <ul class="col-3">' . wp_kses( $ofontlist, $allowed_html ) . '</ul>
  </details>',
                'fields'      => array(array(
                    'id'          => 'font',
                    'label'       => esc_html_x( 'Upload Fonts', 'verb', 'wp-foft-loader' ),
                    'description' => wp_kses( __( 'Upload font files to your media library and store the attachment <abbr>ID</abbr> in the option field.', 'wp-foft-loader' ), $arr ),
                    'type'        => 'font',
                    'default'     => '',
                    'placeholder' => '',
                )),
            );
        }
        $settings['css'] = array(
            'title'       => esc_html_x( 'CSS', 'abbreviation for “Cascading Style Sheets”', 'wp-foft-loader' ),
            'description' => '<p>' . wp_kses( __( '@import rules are automatically handled by this plugin. You may manually inline your font-related <abbr>CSS</abbr> in the document <code>&lt;head&gt;</code> here. Place rules pertaining only to the <code>font-family</code>, <code>font-weight</code>, <code>font-style</code>, and <code>font-variation</code> properties here.', 'wp-foft-loader' ), $arr ) . '</p>
<details>
	<summary>' . __( 'Stage 1 <abbr>CSS</abbr>', 'wp-foft-loader' ) . '</summary>
	<p>' . sprintf( wp_kses( 
                /* translators: ignore the placeholders in the URL */
                __( 'Declarations placed in this field will load the <a href="%s">optimized subset</a> as a placeholder while the non-subsetted fonts load.', 'wp-foft-loader' ),
                $arr
             ), esc_url( $subs_url ) ) . '</p>
	<ul class="wpfl">
		<li>' . wp_kses( __( 'Use only the family name followed by <code>Subset</code> (case-sensitive)', 'wp-foft-loader' ), $arr ) . '</li>
		<li>' . sprintf( wp_kses( 
                /* translators: ignore the placeholders in the URL */
                __( 'Family names must match the names you input on the <a href="%s">Subset screen</a>.', 'wp-foft-loader' ),
                $arr
             ), esc_url( $subs_url ) ) . '</li>
		<li>' . wp_kses( __( 'All declarations must start with the <code>fonts-stage-1</code> class', 'wp-foft-loader' ), $arr ) . '</li>
	</ul>
	<p>' . sprintf( wp_kses( 
                /* translators: ignore the placeholders in the URL */
                __( 'See the <a href="%s">Documentation screen</a> to view the Stage 1 <abbr>CSS</abbr> that this plugin loads by default.', 'wp-foft-loader' ),
                $arr
             ), esc_url( $docs_url ) ) . '</p>
	<dl class="col-2">
	  <dt>' . esc_html__( 'Incorrect:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.nav-primary { <mark>// ' . esc_html__( 'Missing class: .fonts-stage-1', 'wp-foft-loader' ) . '</mark>
  font-family: latoSubset, sans-serif;
}

.fonts-stage-1 #footer-secondary {
  font-family: lato, san-serif; <mark>// ' . esc_html__( 'Missing “Subset” suffix', 'wp-foft-loader' ) . '</mark>
}

.fonts-stage-1 div.callout {
  font-family: latoSubset, san-serif;
	font-size: 1rem; ' . wp_kses( __( '<mark>// “font-family,” “font-weight,” “font-style,”</mark>
                   <mark>// and “font-variant” rules only</mark>', 'wp-foft-loader' ), $arr ) . '
}

.fonts-stage-1 div.callout {
  font-family: latosubset, san-serif; <mark>// ' . esc_html__( '“Subset” suffix is case-sensitive', 'wp-foft-loader' ) . '</mark>
}</code></pre>
	</dd>
		<dt>' . esc_html_x( 'Correct:', 'adjective', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.fonts-stage-1 .nav-primary {
  font-family: latoSubset, sans-serif;
  font-weight: 700;
  font-style: normal;
  font-variant: initial;
}
</code></pre>
		</dd>
	</dl>
	<p>
</details>
<details>
	<summary>' . wp_kses( __( 'Stage 2 <abbr>CSS</abbr>', 'wp-foft-loader' ), $arr ) . '</summary>
	<p>' . esc_html__( 'Declarations placed in this field will load after non-subsetted fonts load.', 'wp-foftloader' ) . '</p>
	<ul class="wpfl">
		<li>' . esc_html__( 'Use only the family name', 'wp-foft-loader' ) . '</li>
		<li>' . sprintf( wp_kses( 
                /* translators: ignore the placeholders in the URL */
                __( 'Family names must match the file names for the fonts you uploaded on the <a href="%s">Upload screen</a>.', 'wp-foft-loader' ),
                $arr
             ), esc_url( $upload_url ) ) . '</li>
		<li>' . esc_html__( 'Omit weights and styles from the font name', 'wp-foft-loader' ) . '</li>
		<li>' . wp_kses( __( 'All declarations must start with the <code>fonts-stage-2</code> class', 'wp-foft-loader' ), $arr ) . '</li>
	</ul>
	<p>' . sprintf( wp_kses( 
                /* translators: ignore the placeholders in the URL */
                __( 'See the <a href="%s">Documentation screen</a> to view the Stage 2 <abbr>CSS</abbr> that this plugin loads by default.', 'wp-foft-loader' ),
                $arr
             ), esc_url( $docs_url ) ) . '</p>
	<dl class="col-2">
		<dt>' . esc_html__( 'Incorrect:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>tbody { <mark>// ' . esc_html__( 'Missing class: .fonts-stage-2', 'wp-foft-loader' ) . '</mark>
  font-family: lato, Corbel, "Lucida Grande", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.fonts-stage-2 span.bolder {
  font-family: lato-bold, Corbel, "Lucida Grande", sans-serif;
  <mark>// ' . esc_html__( 'Don’t include style in font name', 'wp-foft-loader' ) . '</mark>
  <mark>// ' . esc_html__( 'Better yet, omit the declaration', 'wp-foft-loader' ) . '</mark>
  font-weight: 700;
}

.fonts-stage-2 div.callout {
  font-family: lato, Corbel, "Lucida Grande", san-serif;
	font-size: 1rem; ' . wp_kses( __( '<mark>// “font-family,” “font-weight,” “font-style,”</mark>
                   <mark>// and “font-variant” rules only</mark>', 'wp-foft-loader' ), $arr ) . '
}</code></pre>
		</dd>
		<dt>' . esc_html_x( 'Correct:', 'adjective', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.fonts-stage-2 div.callout {
  font-family: lato, Corbel, "Lucida Grande", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.fonts-stage-2 div.callout:focus {
  <mark>// ' . esc_html__( 'No need to redeclare the font-family — all weights map to a single family name', 'wp-foft-loader' ) . '</mark>
  font-weight: 700; <mark>// ' . esc_html__( 'This will use the lato-bold font' ) . '</mark>
}</code></pre>
		</dd>
	</dl>
	<p>
</details>',
            'fields'      => array(
                array(
                    'id'          => 'default_css',
                    'label'       => wp_kses( __( 'Plugin <abbr>CSS</abbr>', 'wp-foft-loader' ), $arr ),
                    'description' => sprintf( wp_kses( 
                        /* translators: ignore the placeholders in the URL */
                        __( 'The plugin loads some <abbr>CSS</abbr> by default. See <a href="%s">the documentation</a>.', 'wp-foft-loader' ),
                        $arr
                     ), esc_url( $docs_url ) ),
                    'type'        => 'radio',
                    'options'     => array(
                        'off' => wp_kses( __( 'Default <abbr>CSS</abbr> Off', 'wp-foft-loader' ), $arr ),
                        'on'  => wp_kses( __( 'Default <abbr>CSS</abbr> On', 'wp-foft-loader' ), $arr ),
                    ),
                    'default'     => 'on',
                ),
                array(
                    'id'          => 'font_display',
                    'label'       => esc_html__( 'Font Display', 'wp-foft-loader' ),
                    'description' => sprintf(
                        wp_kses( 
                            /* translators: ignore the placeholders in the URL & don't translate CSS between <code></code> tags */
                            __( 'Override the <code><a href="%1$s" rel="%2$s" target="%3$s">font-display</a></code> property here. The plugin uses <code>font-display: swap</code> by default', 'wp-foft-loader' ),
                            $arr
                         ),
                        esc_url( $moz_url ),
                        $rel,
                        $target
                    ),
                    'type'        => 'radio',
                    'options'     => array(
                        'swap'     => 'Swap',
                        'auto'     => 'Auto',
                        'block'    => 'Block',
                        'fallback' => 'Fallback',
                        'optional' => 'Optional',
                    ),
                    'default'     => 'swap',
                ),
                array(
                    'id'          => 'stage_1',
                    'label'       => esc_html__( 'Stage 1 CSS', 'wp-foft-loader' ),
                    'description' => wp_kses( __( 'Place <abbr>CSS</abbr> font declarations for subsetted fonts here.', 'wp-foft-loader' ), $arr ),
                    'type'        => 'textarea',
                    'default'     => null,
                    'placeholder' => '// ' . esc_html__( 'Example:', 'wp-foft-loader' ) . '
.fonts-stage-1 body {
  font-family: merriweatherSubset, serif;
}',
                ),
                array(
                    'id'          => 'stage_2',
                    'label'       => wp_kses( __( 'Stage 2 <abbr>CSS</abbr>', 'wp-foft-loader' ), $arr ),
                    'description' => wp_kses( __( 'Place <abbr>CSS</abbr> font declarations for non-subsetted fonts here.', 'wp-foft-loader' ), $arr ),
                    'type'        => 'textarea_large',
                    'default'     => null,
                    'placeholder' => '// ' . esc_html__( 'Example:', 'wp-foft-loader' ) . '
.fonts-stage-2 body {
  font-family: merriweather, "Century Schoolbook L", Georgia, serif;
}
.fonts-stage-2 strong {
  font-weight: 700;
}',
                )
            ),
        );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // FREE version & PRO users without a valid license.
            $settings['fstack'] = array(
                'title'       => esc_html__( 'Font Stack', 'wp-foft-loader' ),
                'description' => '<p>' . esc_html__( 'Change the default font fallbacks in case your custom fonts don’t load.', 'wp-foft-loader' ) . ' <strong>' . esc_html__( 'Don’t include the names of your default custom fonts here.', 'wp-foft-loader' ) . '</strong></p>',
                'fields'      => array(
                    array(
                        'id'          => 'fstack-heading',
                        'label'       => esc_html__( 'Headings', 'wp-foft-loader' ),
                        'description' => esc_html__( 'Font stack for display font. Applies to high-level headings', 'wp-foft-loader' ) . '(H1, H2, & H3).',
                        'type'        => 'textarea',
                        'default'     => '"Palatino Linotype",Palatino,Palladio,"URW Palladio L","Book Antiqua",Baskerville,"Bookman Old Style","Bitstream Charter","Nimbus Roman No9 L",Garamond,"Apple Garamond","ITC Garamond Narrow","New Century Schoolbook","Century Schoolbook","Century Schoolbook L",Georgia,serif',
                        'placeholder' => '',
                    ),
                    array(
                        'id'          => 'fstack-body',
                        'label'       => esc_html_x( 'Body', 'e.g., Body Text', 'wp-foft-loader' ),
                        'description' => esc_html__( 'Font stack for body text. This can be a serif or sans-serif font.', 'wp-foft-loader' ),
                        'type'        => 'textarea',
                        'default'     => '"Palatino Linotype",Palatino,Palladio,"URW Palladio L","Book Antiqua",Baskerville,"Bookman Old Style","Bitstream Charter","Nimbus Roman No9 L",Garamond,"Apple Garamond","ITC Garamond Narrow","New Century Schoolbook","Century Schoolbook","Century Schoolbook L",Georgia,serif',
                        'placeholder' => '',
                    ),
                    array(
                        'id'          => 'fstack-alt',
                        'label'       => esc_html__( 'Functional text', 'wp-foft-loader' ),
                        'description' => wp_kses( __( 'Font stack for non-body, functional text elements, <abbr>e.g.</abbr>, navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wp-foft-loader' ), $arr ),
                        'type'        => 'textarea',
                        'default'     => '-apple-system,BlinkMacSystemFont,"Segoe UI",Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
                        'placeholder' => '',
                    ),
                    array(
                        'id'          => 'fstack-mono',
                        'label'       => esc_html__( 'Monospaced', 'wp-foft-loader' ),
                        'description' => esc_html__( 'Font stack for monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
                        'type'        => 'textarea',
                        'default'     => 'Consolas,"Andale Mono WT","Andale Mono","Lucida Console","Lucida Sans Typewriter","DejaVu Sans Mono","Bitstream Vera Sans Mono","Liberation Mono","Nimbus Mono L",Monaco,monospace',
                        'placeholder' => '',
                    )
                ),
            );
        }
        $settings['advanced'] = array(
            'title'       => esc_html__( 'Advanced Settings', 'wp-foft-loader' ),
            'description' => wp_kses( __( 'Uninstalling this plugin automatically deletes its options from the database. To leave the options intact, (<abbr>e.g.</abbr>, when upgrading from the FREE version to the PRO version) change this setting.', 'wp-foft-loader' ), $arr ),
            'fields'      => array(array(
                'id'          => 'uninstall',
                'label'       => __( 'Uninstall Options', 'wp-foft-loader' ),
                'description' => '',
                'type'        => 'radio',
                'options'     => array(
                    'delete' => wp_kses( __( 'Delete all <abbr>WP</abbr> <abbr>FOFT</abbr> Loader options from the database when the plugin is uninstalled.', 'wp-foft-loader' ), $arr ) . '<br />',
                    'retain' => wp_kses( __( 'Leave all <abbr>WP</abbr> <abbr>FOFT</abbr> Loader options in the database when the plugin is uninstalled.', 'wp-foft-loader' ), $arr ),
                ),
                'default'     => 'delete',
            )),
        );
        $settings['documentation'] = array(
            'title'       => esc_html__( 'Documentation', 'wp-foft-loader' ),
            'description' => '<section>
	<h3>' . esc_html__( 'Fonts Stage 1', 'wp-foft-loader' ) . '</h3>
	<p>' . sprintf( wp_kses( 
                /* translators: ignore the placeholders in the URL and don't translate text between <code></code> tags */
                __( 'This plugin always loads the following Stage 1 styles. The Stage 1 fonts are subsetted fonts, acting as placeholders until the full Stage 2 fonts load. <code>&lt;$bodySubset&gt;</code>, <code>&lt;$altSubset&gt;</code>, <code>&lt;$headingSubset&gt;</code>, and <code>&lt;$monoSubset&gt;</code> correspond to the Body, Other Elements, Headings, and Monospaced font-families configured on the <a href="%s">Subset screen</a>.', 'wp-foft-loader' ),
                $arr
             ), esc_url( $subs_url ) ) . '</p>
<pre class="col-3"><code>body {
  font-family: serif;
  font-weight: 400;
  font-style: normal
}

.fonts-stage-1 body {
  font-family: <$bodySubset>, serif
}

.fonts-stage-1 button,
.fonts-stage-1 input,
.fonts-stage-1 nav,
.fonts-stage-1 optgroup,
.fonts-stage-1 select,
.fonts-stage-1 textarea {
  font-family: <$altSubset>, sans-serif
}

.fonts-stage-1 h1,
.fonts-stage-1 h2,
.fonts-stage-1 h3,
.fonts-stage-1 h4,
.fonts-stage-1 h5,
.fonts-stage-1 h6 {
  font-family: <$headingSubset>, serif
}

.fonts-stage-1 code {
  font-family: <$monoSubset>, monospace
}</code></pre>
</section>
<section>
	<h3>' . __( 'Fonts Stage 2', 'wp-foft-loader' ) . '</h3>
	<p>' . sprintf(
                wp_kses( 
                    /* translators: ignore the placeholders in the URL and don't translate text between <code></code> tags */
                    __( 'This plugin also loads the following Stage 2 styles. You can disable these styles on the <a href="%1$s"><abbr>CSS</abbr> screen</a>. <code>&lt;$body&gt;</code>, <code>&lt;$alt&gt;</code>, <code>&lt;$heading&gt;</code>, and <code>&lt;$mono&gt;</code> correspond to the Body, Other Elements, Headings, and Monospaced font-families configured on the <a href="%2$s">Subset screen</a>. You can change the default font fallbacks on the <a href="%3$s">Font Stack screen</a>', 'wp-foft-loader' ),
                    $arr
                 ),
                esc_url( $css_url ),
                esc_url( $subs_url ),
                esc_url( $fstack_url )
            ) . '</p>
<pre class="col-3"><code>.fonts-stage-2 body,
.fonts-stage-2 h4,
.fonts-stage-2 h5,
.fonts-stage-2 h6 {
  font-family: &lt;$body&gt;, "Palatino Linotype", Palatino, Palladio, "URW Palladio L", "Book Antiqua", Baskerville, "Bookman Old Style", "Bitstream Charter", "Nimbus Roman No9 L", Garamond, "Apple Garamond", "ITC Garamond Narrow", "New Century Schoolbook", "Century Schoolbook", "Century Schoolbook L", Georgia, serif
}

.fonts-stage-2 h1,
.fonts-stage-2 h2,
.fonts-stage-2 h3 {
  font-family: &lt;$heading&gt;, "Palatino Linotype", Palatino, Palladio, "URW Palladio L", "Book Antiqua", Baskerville, "Bookman Old Style", "Bitstream Charter", "Nimbus Roman No9 L", Garamond, "Apple Garamond", "ITC Garamond Narrow", "New Century Schoolbook", "Century Schoolbook", "Century Schoolbook L", Georgia, serif;
  font-weight: 400
}

.fonts-stage-2 code strong,
.fonts-stage-2 h4,
.fonts-stage-2 h5,
.fonts-stage-2 h6,
.fonts-stage-2 strong,
.fonts-stage-2 strong code {
  font-weight: 700
}

.fonts-stage-2 h1 strong,
.fonts-stage-2 h2 strong,
.fonts-stage-2 h3 strong,
.fonts-stage-2 strong h1,
.fonts-stage-2 strong h2,
.fonts-stage-2 strong h3 {
  font-weight: 700
}

.fonts-stage-2 em strong h1,
.fonts-stage-2 h1 em strong,
.fonts-stage-2 h1 strong em,
.fonts-stage-2 strong em h1 {
  font-weight: 700;
  font-style: italic
}

.fonts-stage-2 abbr {
  font-weight: 700;
  font-variant: small-caps;
  padding: 0 .13333rem 0 0;
  letter-spacing: .06667rem;
  text-transform: lowercase
}

.fonts-stage-2 code {
  font-family: &lt;$sans&gt;, Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, monospace
}

.fonts-stage-2 cite > em,
.fonts-stage-2 cite > q,
.fonts-stage-2 em > cite,
.fonts-stage-2 em > em,
.fonts-stage-2 em > q,
.fonts-stage-2 figcaption > cite,
.fonts-stage-2 figcaption > em,
.fonts-stage-2 q > cite,
.fonts-stage-2 q > em {
  font-style: normal
}

.fonts-stage-2 code em,
.fonts-stage-2 em,
.fonts-stage-2 em code,
.fonts-stage-2 figcaption,
.fonts-stage-2 h2,
.fonts-stage-2 h3 {
  font-style: italic
}

.fonts-stage-2 code em strong,
.fonts-stage-2 code strong em,
.fonts-stage-2 em code strong,
.fonts-stage-2 em strong,
.fonts-stage-2 em strong code,
.fonts-stage-2 strong code em,
.fonts-stage-2 strong em,
.fonts-stage-2 strong em code {
  font-weight: 700;
  font-style: italic
}

,
.fonts-stage-2 button,
.fonts-stage-2 input,
.fonts-stage-2 nav,
.fonts-stage-2 optgroup,
.fonts-stage-2 select,
.fonts-stage-2 textarea {
  font-family: &lt;$alt&gt;, -apple-system, BlinkMacSystemFont, "Segoe UI", Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-weight: 400
}</code></pre>
</section>
<section>
	<h3>' . esc_html__( 'Video Tutorials', 'wp-foft-loader' ) . '</h3>
    <div class="col-2">
      <div class="video-responsive"><iframe width="560" height="315" src="https://www.youtube.com/embed/0C0lDJ3T12o?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
      <div class="video-responsive"><iframe width="560" height="315" src="https://www.youtube.com/embed/-StFYcOSDCU?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
    </div>
</section>',
            'fields'      => array(array(
                'id'          => 'documentation',
                'label'       => null,
                'description' => null,
                'type'        => 'hidden',
                'default'     => null,
                'placeholder' => null,
            )),
        );
        $settings = apply_filters( $this->parent->token . '_settings_fields', $settings );
        return $settings;
    }

    /**
     * Register plugin settings
     *
     * @return void
     */
    public function register_settings() {
        if ( is_array( $this->settings ) ) {
            // Check posted/selected tab.
            $current_section = '';
            // phpcs:disable
            if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
                $current_section = sanitize_text_field( wp_unslash( $_POST['tab'] ) );
            } else {
                if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
                    $current_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
                }
            }
            // phpcs:enable
            foreach ( $this->settings as $section => $data ) {
                if ( $current_section && $current_section !== $section ) {
                    continue;
                }
                // Add section to page.
                add_settings_section(
                    $section,
                    $data['title'],
                    array($this, 'settings_section'),
                    $this->parent->token
                );
                foreach ( $data['fields'] as $field ) {
                    // Validation callback for field.
                    $validation = '';
                    if ( isset( $field['callback'] ) ) {
                        $validation = $field['callback'];
                    }
                    // Register field.
                    $option_name = WPFL_BASE . $field['id'];
                    register_setting( $this->parent->token, $option_name, $validation );
                    // Add field to page.
                    add_settings_field(
                        $field['id'],
                        $field['label'],
                        array($this->parent->admin, 'display_field'),
                        $this->parent->token,
                        $section,
                        array(
                            'field'  => $field,
                            'prefix' => WPFL_BASE,
                        )
                    );
                }
                if ( !$current_section ) {
                    break;
                }
            }
        }
    }

    /**
     * Settings section.
     *
     * @param array $section Array of section ids.
     * @return void
     */
    public function settings_section( $section ) {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . chr( 0xd ) . chr( 0xa );
        echo $html;
        // phpcs:ignore
    }

    /**
     * Load settings page content
     *
     * @return void
     */
    public function settings_page() {
        $zl1_url = '//github.com/zachleat/web-font-loading-recipes#the-compromise-critical-foft-with-preload-with-a-polyfill-fallback-emulating-font-display-optional';
        $zl2_url = '//www.zachleat.com/';
        $rel = 'external noreferrer noopener';
        $target = '_blank';
        $class_hcard = 'h-card';
        $class_pname = 'p-name u-url';
        $arr = array(
            'a'      => array(
                'href'   => array(),
                'rel'    => array(),
                'target' => array(),
                'class'  => array(),
            ),
            'abbr'   => array(),
            'strong' => array(),
            'span'   => array(
                'class' => array(),
            ),
        );
        $html = '<div class="wrap" id="' . $this->parent->token . '">' . chr( 0xd ) . chr( 0xa ) . '<h2><span class="wp-admin-lite-blue"><i class="wpfl-fas fa-3x fa-font" aria-hidden="true"></i> <span class="wp-admin-red"><i class="wpfl-fas fa-2x fa-font" aria-hidden="true"></i></span> <i class="wpfl-fas fa-font" aria-hidden="true"></i></span> ' . wp_kses( __( 'WP <abbr>FOFT</abbr> Loader Settings', 'wp-foft-loader' ), $arr ) . '</h2>' . chr( 0xd ) . chr( 0xa ) . '<p>' . sprintf(
            wp_kses( 
                /* translators: ignore the placeholders */
                __( 'Automates “<a href="%3$s" rel="%5$s" target="%6$s">Critical <abbr>FOFT</abbr> with preload, with a polyfill fallback emulating font-display: optional</a>” to speed up web font loading while eliminating Flash of Unstyled Text (<abbr>FOUT</abbr>) and Flash of Invisible Text (<abbr>FOIT</abbr>). Based on the work of <span class="%1$s"><a class="%2$s" href="%4$s" rel="%5$s" target="%6$s">Zach Leatherman</a></span>.', 'wp-foft-loader' ),
                $arr
             ),
            $class_hcard,
            $class_pname,
            esc_url( $zl1_url ),
            esc_url( $zl2_url ),
            $rel,
            $target
        ) . '</p>' . chr( 0xd ) . chr( 0xa );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // FREE version & PRO users without a valid license.
            $html .= '<p><strong>' . wp_kses( __( 'Upgrade to <abbr>WP</abbr> <abbr>FOFT</abbr> Loader PRO for additional font weights and small-caps support', 'wp-foft-loader.' ), $arr ) . '.</strong></p>' . chr( 0xd ) . chr( 0xa );
        }
        $html .= '<p>' . wp_kses( __( 'Please <strong>save your changes</strong> before navigating to the next tab. ', 'wp-foft-loader' ), $arr ) . '</p>' . chr( 0xd ) . chr( 0xa );
        $tab = '';
        // phpcs:disable
        if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
            $tab .= sanitize_text_field( wp_unslash( $_GET['tab'] ) );
        }
        // phpcs:enable
        // Show page tabs.
        if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {
            $html .= '<h2 class="nav-tab-wrapper">' . chr( 0xd ) . chr( 0xa );
            $c = 0;
            foreach ( $this->settings as $section => $data ) {
                // Set tab class.
                // phpcs:disable
                $class = 'nav-tab';
                if ( !isset( $_GET['tab'] ) ) {
                    if ( 0 === $c ) {
                        $class .= ' nav-tab-active';
                    }
                } else {
                    if ( isset( $_GET['tab'] ) && $section === $_GET['tab'] ) {
                        $class .= ' nav-tab-active';
                    }
                }
                // phpcs:enable
                // Set tab link.
                $tab_link = add_query_arg( array(
                    'tab' => $section,
                ) );
                // phpcs:disable
                if ( isset( $_GET['settings-updated'] ) ) {
                    $tab_link = remove_query_arg( 'settings-updated', $tab_link );
                }
                // phpcs:enable
                // Output tab.
                $html .= '<a href="' . esc_url( $tab_link ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . chr( 0xd ) . chr( 0xa );
                ++$c;
            }
            $html .= '</h2>' . chr( 0xd ) . chr( 0xa );
        }
        $html .= '<form method="post" action="options.php" name="wpflSettings" id="wpflSettings" enctype="multipart/form-data">' . chr( 0xd ) . chr( 0xa );
        // Get settings fields.
        ob_start();
        settings_fields( $this->parent->token );
        do_settings_sections( $this->parent->token );
        $html .= ob_get_clean();
        global $pagenow;
        // Run certain logic ONLY if we are on the correct settings page.
        $html .= '<p class="submit">' . chr( 0xd ) . chr( 0xa );
        $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . chr( 0xd ) . chr( 0xa );
        echo $html;
        // phpcs:ignore
        submit_button(
            esc_html__( 'Save Settings', 'wp-foft-loader' ),
            'primary',
            'save_wpfl_options',
            false
        );
        $html2 = '</p>' . chr( 0xd ) . chr( 0xa );
        $html2 .= '</form>' . chr( 0xd ) . chr( 0xa );
        $html2 .= '</div>' . chr( 0xd ) . chr( 0xa );
        $success1 = esc_html__( 'Yeehaw!', 'wp-foft-loader' );
        $success2 = esc_html__( 'Good Job!', 'wp-foft-loader' );
        $success3 = esc_html__( 'Hooray!', 'wp-foft-loader' );
        $success4 = esc_html__( 'Yay!', 'wp-foft-loader' );
        $success5 = esc_html__( 'Huzzah!', 'wp-foft-loader' );
        $success6 = esc_html__( 'Bada bing bada boom!', 'wp-foft-loader' );
        $message1 = array(
            $success1,
            $success2,
            $success3,
            $success4,
            $success5,
            $success6
        );
        $message1 = $message1[array_rand( $message1 )];
        $error1 = esc_html__( 'Dangit!', 'wp-foft-loader' );
        $error2 = esc_html__( 'Aw heck!', 'wp-foft-loader' );
        $error3 = esc_html__( 'Egads!', 'wp-foft-loader' );
        $error4 = esc_html__( 'D’oh!', 'wp-foft-loader' );
        $error5 = esc_html__( 'Drat!', 'wp-foft-loader' );
        $error6 = esc_html__( 'Dagnabit!', 'wp-foft-loader' );
        $message2 = array(
            $error1,
            $error2,
            $error3,
            $error4,
            $error5,
            $error6
        );
        $message2 = $message2[array_rand( $message2 )];
        if ( 'options-general.php' === $pagenow && isset( $_GET['page'] ) && 'wp-foft-loader' === $_GET['page'] ) {
            // phpcs:ignore
            // Ajaxify the form. Timeout should be >= 5000 or you'll get errors.
            $html2 .= '<div id="saveResult"></div>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	   jQuery("#wpflSettings").submit(function() {
		  jQuery(this).ajaxSubmit({
			 success: function(){
				jQuery("#saveResult").html(`<div id="saveMessage" class="notice notice-success is-dismissible"></div>`);
				jQuery("#saveMessage").append(`<p><span class="dashicons dashicons-yes-alt"></span> ' . $message1 . ' ' . esc_html__( 'Your settings were saved!', 'wp-foft-loader' ) . '</p>`).show();
			 },
			 error: function(){
				jQuery("#saveResult").html(`<div id="saveMessage" class="notice notice-error is-dismissible"></div>`);
				jQuery("#saveMessage").append(`<p><span class="dashicons dashicons-no"></span> ' . $message2 . ' ' . esc_html__( 'There was an error saving your settings. Please open a support ticket if the problem persists!', 'wp-foft-loader' ) . '</p>`).show();
			 },
			 timeout: 10000
		  });
		  setTimeout(`jQuery("#saveMessage").hide("slow");`, 7500);
		  return false;
	   });
	});
	</script>';
        }
        echo $html2;
    }

    /**
     * Main WP_FOFT_Loader_Settings Instance
     *
     * Ensures only one instance of WP_FOFT_Loader_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WP_FOFT_Loader()
     * @param object $parent Object instance.
     * @return Main WP_FOFT_Loader_Settings instance
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
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_API is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_API is forbidden.', 'wp-foft-loader' ), esc_attr( WPFL_VERSION ) );
    }

    // End __wakeup()
}
