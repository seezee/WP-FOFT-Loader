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
class WP_FOFT_Loader_Settings
{
    /**
     * The single instance of WP_FOFT_Loader_Settings.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static  $instance = null ;
    /**
     * The main plugin object.
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public  $parent = null ;
    /**
     * Available settings for plugin.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public  $settings = array() ;
    /**
     * Constructor function.
     *
     * @param object $parent Parent object.
     */
    public function __construct( $parent )
    {
        $this->parent = $parent;
        // Initialise settings.
        add_action( 'init', array( $this, 'init_settings' ), 11 );
        // Register plugin settings.
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        // Add settings page to menu.
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
        // Add settings link to plugins page.
        add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array( $this, 'add_settings_link' ) );
        // Configure placement of plugin settings page. See readme for implementation.
        add_filter( _BASE_ . 'menu_settings', array( $this, 'configure_settings' ) );
    }
    
    /**
     * Initialise settings
     *
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }
    
    /**
     * Add settings page to admin menu
     *
     * @return void
     */
    public function add_menu_item()
    {
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
            add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
        }
    
    }
    
    /**
     * Prepare default settings page arguments
     *
     * @return mixed|void
     */
    private function menu_settings()
    {
        return apply_filters( _BASE_ . 'menu_settings', array(
            'location'    => 'options',
            'parent_slug' => 'options-general.php',
            'page_title'  => __( 'WP FOFT Loader Settings', 'wp-foft-loader' ),
            'menu_title'  => __( 'WP FOFT Loader', 'wp-foft-loader' ),
            'capability'  => 'manage_options',
            'menu_slug'   => $this->parent->token . '_settings',
            'function'    => array( $this, 'settings_page' ),
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
    public function configure_settings( $settings = array() )
    {
        return $settings;
    }
    
    /**
     * Load settings JS & CSS
     *
     * @return void
     */
    public function settings_assets()
    {
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
    public function add_settings_link( $links )
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->token . '_settings">' . __( 'Settings', 'wp-foft-loader' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }
    
    /**
     * Open media uploader on Upload tab instead of Library view
     */
    public function upload_media_manager_by_default()
    {
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
    private function settings_fields()
    {
        // Locate font files so we can display a list later.
        $uploads = wp_get_upload_dir();
        $font_path = $uploads['baseurl'] . '/fonts/';
        $font_dir = $uploads['basedir'] . '/fonts/';
        $files = glob( $font_dir . '*.woff', GLOB_BRACE );
        
        if ( $files ) {
            $uploadmessage = '<h3>' . __( 'You have uploaded the following fonts:', 'wp-foft-loader' ) . '</h3>';
        } else {
            $uploadmessage = '<h3>' . __( 'You have not uploaded any fonts.', 'wp-foft-loader' ) . '</h3>';
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
            
            if ( !fnmatch( "*optimized*", $file ) ) {
                // Non-subsetted files
                $font = basename( $file, '.woff' );
                // Remove the file type.
                list( $family, $style, $type ) = explode( '-', $font, 3 );
                // Explode for 3 parts: family, style & type (-webfont).
                echo  '<li>' . $font . '</li>' ;
            }
        
        }
        $fontlist = ob_get_clean();
        // Get buffer & display list of uploaded fonts.
        ob_start();
        foreach ( $files as &$file ) {
            
            if ( !fnmatch( "*optimized*", $file ) ) {
                $font = basename( $file, '.woff' );
                $fonts = explode( '-', $font, 3 );
                // Explode for 1 part: family.
                echo  $fonts[0] . ',' ;
            }
        
        }
        $choices = ob_get_clean();
        $choices = wp_kses( $choices, $fam );
        // Sanitize the input.
        $c_str = implode( ',', array_unique( explode( ',', $choices ) ) );
        // Remove duplicate font-families & convert the array to a string
        $c_str = rtrim( $c_str, ',' );
        // Trim trailing comma & space.
        $c_arr = explode( ',', $c_str );
        // Split at comma & make an array
        
        if ( empty($c_arr) ) {
            $c_arr = NULL;
        } else {
            list( $c1, $c2, $c3, $c4, $c5 ) = $c_arr;
            // Assign variables to the array values. Used below to assign $heading, $body, $alt, & $mono.
        }
        
        ob_start();
        foreach ( $files as &$file ) {
            
            if ( fnmatch( "*optimized*", $file ) ) {
                // Subsetted files only.
                $font = basename( $file, '.woff' );
                list( $family, $style, $type ) = explode( '-', $font, 3 );
                // explode for 3 parts: family, style & type (-optimized).
                echo  '<li>' . $font . '</li>' ;
            }
        
        }
        $ofontlist = ob_get_clean();
        if ( !wpfl_fs()->can_use_premium_code() ) {
            // Autoremoved from the PRO version.
            $settings['upload'] = array(
                'title'       => __( 'Upload', 'wp-foft-loader' ),
                'description' => '<p>Upload two files for each web font: a WOFF file and a WOFF2 file. In most cases you will upload regular, italic, bold, and bold italic versions of each font.</p><details><summary class="wp-admin-lite-blue">' . __( 'Preparing the Files', 'wp-foft-loader' ) . '</summary><p>' . __( 'We recommend you use', 'wp-foft-loader' ) . ' <a href="https://www.fontsquirrel.com/tools/webfont-generator" target="_blank" rel="external noreferrer noopener">Font Squirrel’s ' . __( 'Webfont Generator', 'wp-foft-loader' ) . '</a> ' . __( 'to generate the files. Recommended Font Squirrel settings are:', 'wp-foft-loader' ) . '</p>
<dl class="col-3">
  <dt>' . __( 'Font Formats', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;<abbr>WOFF</abbr>&rdquo;</dd>
  <dd>&ldquo;<abbr>WOFF2</abbr>&rdquo;</dd>
  <dt>' . __( 'Truetype Hinting', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Keep Existing', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Rendering', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'Vertical Metrics', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'No Adjustment', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Fix Missing Glyphs', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'X-height Matching', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'None', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Protection', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Select', 'wp-foft-loader' ) . ' &ldquo;WebOnly™&rdquo; ' . __( 'if you are using a commercially licensed font', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'Subsetting', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Basic Subsetting', 'wp-foft-loader' ) . '&rdquo; or &ldquo;' . __( 'No Subsetting', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'OpenType Features', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Your choice, but we like', 'wp-foft-loader' ) . ' &ldquo;' . __( 'Keep All Features', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'OpenType Flattening', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
  <dt><abbr>CSS</abbr></dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'Advanced Options', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Font Name Suffix', 'wp-foft-loader' ) . '&rdquo; = -webfont</dd>
  <dd>&ldquo;' . __( 'Em Square Value', 'wp-foft-loader' ) . '&rdquo; = 2048</dd>
  <dd>&ldquo;' . __( 'Adjust Glyph Spacing', 'wp-foft-loader' ) . '&rdquo; = 0</dd>
  <dt>' . __( 'Shortcuts', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Remember My Settings', 'wp-foft-loader' ) . '&rdquo;</dd>
</dl>
<hr>
<p></details><details><summary class="wp-admin-lite-blue">' . __( 'Naming the Files', 'wp-foft-loader' ) . '</summary><strong>' . __( 'Filenames must follow the proper naming convention:', 'wp-foft-loader' ) . '</strong> <code>$family</code>-<code>$variant</code>-webfont.<code>$filetype</code>.</p>
<dl>
<dt>$family</dt>
<dd>' . __( 'The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but', 'wp-foft-loader' ) . ' <em>' . __( 'no hyphens or spaces', 'wp-foft-loader' ) . '</em>.</dd>
<dt>$variant</dt>
<dd>' . __( 'The font variant. Can be weight, style, or a combination of both.', 'wp-foft-loader' ) . ' <em>' . __( 'Case-sensitive', 'wp-foft-loader' ) . '</em>.</dd>
<dt>-webfont</dt>
<dd>' . __( 'Mandatory suffix. Append to', 'wp-foft-loader' ) . ' $variant.</dd>
<dt>$filetype</dt>
<dd>' . __( 'The file type,', 'wp-foft-loader' ) . ' i.e., &ldquo;woff&rdquo; or &ldquo;woff2&rdquo;.</dd>
</dl>
<p><strong>' . __( 'Example', 'wp-foft-loader' ) . '</strong>: ' . __( 'for the bold weight, italic style of', 'wp-foft-loader' ) . ' Times New Roman, ' . __( 'rename the files to', 'wp-foft-loader' ) . ' <code>timesnewroman-boldItalic-webfont.woff</code> ' . __( 'and', 'wp-foft-loader' ) . ' <code>timesnewroman-boldItalic-webfont.woff2</code>. ' . __( 'For small caps style families, append', 'wp-foft-loader' ) . ' <code>SC</code> (' . __( 'case-sensitive', 'wp-foft-loader' ) . ') ' . __( 'to the family name,', 'wp-foft-loader' ) . ' e.g., <code>playfairdisplaySC-regular-webfont.woff</code>.</p><h3>' . __( '$family Examples', 'wp-foft-loader' ) . '</h3><dl>
	<dt>' . __( 'Correct', 'wp-foft-loader' ) . ':</dt>
	<dd><strong>playfairdisplay</strong> (' . __( 'all lowercase', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>playfair_display</strong> (' . __( 'underscores allowed', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>PlayfairDisplay</strong> (' . __( 'mixed case allowed', 'wp-foft-loader' ) . ')</dd>
	<dt>' . __( 'Incorrect', 'wp-foft-loader' ) . ':</dt>
	<dd><strong>playfair-display</strong> (' . __( 'hyphens not allowed', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>playfair display</strong> (' . __( 'spaces prohibited', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>Playfair Display</strong> (' . __( 'spaces prohibited', 'wp-foft-loader' ) . ')</dd>
</dl>
</details><details><summary class="wp-admin-lite-blue">' . __( 'Allowed Weights &amp; Mappings', 'wp-foft-loader' ) . '</summary>' . __( 'Allowed weights and styles and their CSS mappings are:', 'wp-foft-loader' ) . '
<ul class="col-3">
	<li>regular | normal (' . __( 'maps to', 'wp-foft-loader' ) . ' 400)</li>
	<li>bold (' . __( 'maps to', 'wp-foft-loader' ) . ' 700)</li>
	<li>italic (' . __( 'maps to', 'wp-foft-loader' ) . ' 400)</li>
	<li>mediumItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 500)</li>
	<li>boldItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 700)</li>
</ul></details><details><summary class="wp-admin-lite-blue">' . __( 'Your Fonts', 'wp-foft-loader' ) . '</summary>' . $uploadmessage . '<ul class="col-3">' . wp_kses( $fontlist, $allowed_html ) . '</ul></details><p>' . __( 'This plugin supports 1&thinsp;&ndash;&thinsp;4 font families. After uploading your fonts, assign them as needed below.', 'wp-foft-loader' ) . '</p>',
                'fields'      => array(
                array(
                'id'          => 'font',
                'label'       => __( 'Upload Fonts', 'wp-foft-loader' ),
                'description' => __( 'Upload font files to your media library and store the attachment ID in the option field.', 'wp-foft-loader' ),
                'type'        => 'font',
                'default'     => '',
                'placeholder' => '',
            ),
                array(
                'id'          => 's1-heading',
                'label'       => __( 'Headings', 'wp-foft-loader' ),
                'description' => __( 'Specify the display font used for high-level headings', 'wp-foft-loader' ) . '(H1, H2, &amp; H3)',
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
                'label'       => __( 'Body', 'wp-foft-loader' ),
                'description' => __( 'Specify the body text. This can be a serif or sans-serif font.', 'wp-foft-loader' ),
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
                'label'       => __( 'Other elements', 'wp-foft-loader' ),
                'description' => __( 'Specify non-body elements', 'wp-foft-loader' ) . ', <abbr>e.g.</abbr>,' . __( 'navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wp-foft-loader' ),
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
                'label'       => __( 'Monospaced', 'wp-foft-loader' ),
                'description' => __( 'Specify monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
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
        $settings['subset'] = array(
            'title'       => __( 'Subset', 'wp-foft-loader' ),
            'description' => '<p>' . __( 'Upload up to 4 small, subsetted fonts. For each font, upload a WOFF &amp; WOFF2 file (for a total of up to 8 files). Each font will act as a placeholder until the full fonts load.', 'wp-foft-loader' ) . '<p><details><summary class="wp-admin-lite-blue">' . __( 'Preparing the Files', 'wp-foft-loader' ) . '</summary><p>' . __( 'We recommend you use', 'wp-foft-loader' ) . ' <a href="https://www.fontsquirrel.com/tools/webfont-generator" target="_blank" rel="external noreferrer noopener">Font Squirrel’s ' . __( 'Webfont Generator', 'wp-foft-loader' ) . '</a> ' . __( 'to generate the files. Recommended Font Squirrel settings are:', 'wp-foft-loader' ) . '</p>
<dl class="col-3">
  <dt>' . __( 'Font Formats', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;<abbr>WOFF</abbr>&rdquo;</dd>
  <dd>&ldquo;<abbr>WOFF2</abbr>&rdquo;</dd>
  <dt>' . __( 'Truetype Hinting', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Keep Existing', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Rendering', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'Vertical Metrics', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'No Adjustment', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Fix Missing Glyphs', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Default options (leave unchecked)', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'X-height Matching', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'None', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Protection', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Select', 'wp-foft-loader' ) . ' &ldquo;WebOnly™&rdquo; ' . __( 'if you are using a commercially licensed font', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'Subsetting', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Custom Subsetting', 'wp-foft-loader' ) . '&rdquo; ' . __( 'with the Unicode Ranges', 'wp-foft-loader' ) . ' 0041-005A,0061-007A</dd>
  <dd>' . __( 'Leave everything else unchecked or blank', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'OpenType Features', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'OpenType Flattening', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
  <dt><abbr>CSS</abbr></dt>
  <dd>' . __( 'Leave unchecked', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'Advanced Options', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Font Name Suffix', 'wp-foft-loader' ) . '&rdquo; = -optimized</dd>
  <dd>&ldquo;' . __( 'Em Square Value', 'wp-foft-loader' ) . '&rdquo; = 2048</dd>
  <dd>&ldquo;' . __( 'Adjust Glyph Spacing', 'wp-foft-loader' ) . '&rdquo; = 0</dd>
  <dt>' . __( 'Shortcuts', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'Leave unchecked', 'wp-foft-loader' ) . '</dd>
</dl></details><details><summary class="wp-admin-lite-blue">' . __( 'Naming the Files', 'wp-foft-loader' ) . '</summary>
<p><strong>' . __( 'Filenames must follow the proper naming convention:', 'wp-foft-loader' ) . '</strong> <code>$family</code>-optimized.<code>$filetype</code>.</p>
<dl>
<dt>$family</dt>
<dd>' . __( 'The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but', 'wp-foft-loader' ) . ' <em>' . __( 'no hyphens or spaces.', 'wp-foft-loader' ) . '</em> <strong>' . __( 'Each', 'wp-foft-loader' ) . ' <code>$family</code> ' . __( 'base name should match the name used for the matching font uploaded on the previous upload screen.', 'wp-foft-loader' ) . '</strong></dd>
<dt>-optimized</dt>
<dd>' . __( 'Mandatory suffix. Append to', 'wp-foft-loader' ) . ' $family.</dd>
<dt>$filetype</dt>
<dd>' . __( 'The file type,', 'wp-foft-loader' ) . ' i.e., &ldquo;woff&rdquo; or &ldquo;woff2&rdquo;.</dd>
</dl><p><strong>' . __( 'Example', 'wp-foft-loader' ) . '</strong>: ' . __( 'If you uploaded', 'wp-foft-loader' ) . ' <code>timesnewroman-regular-webfont.woff</code> ' . __( 'and', 'wp-foft-loader' ) . ' <code>timesnewroman-regular-webfont.woff2</code> ' . __( 'as your body font on the previous screen, name the subsetted versions ', 'wp-foft-loader' ) . ' <code>timesnewroman-optimized.woff</code> ' . __( 'and', 'wp-foft-loader' ) . '  <code>timesnewroman-optimized.woff2</code> ' . __( 'respectively.', 'wp-foft-loader' ) . '</p></details><details><summary class="wp-admin-lite-blue">' . __( 'Your Fonts', 'wp-foft-loader' ) . '</summary>' . $uploadmessage . '<ul class="col-3">' . wp_kses( $ofontlist, $allowed_html ) . '</ul></details>',
            'fields'      => array( array(
            'id'          => 'font',
            'label'       => __( 'Upload Fonts', 'wp-foft-loader' ),
            'description' => __( 'Upload font files to your media library and store the attachment ID in the option field.', 'wp-foft-loader' ),
            'type'        => 'font',
            'default'     => '',
            'placeholder' => '',
        ) ),
        );
        $settings['css'] = array(
            'title'       => __( 'CSS', 'wp-foft-loader' ),
            'description' => '<p>' . __( '@import rules are automatically handled by this plugin. You may manually inline your font-related', 'wp-foft-loader' ) . ' <abbr>CSS</abbr> ' . __( 'in the document', 'wp-foft-loader' ) . ' <code>&lt;head&gt;</code> ' . __( 'here. Place rules pertaining only to the', 'wp-foft-loader' ) . ' <code>font-family</code>, <code>font-weight</code>, <code>font-style</code>, ' . __( 'and', 'wp-foft-loader' ) . ' <code>font-variation</code> ' . __( 'properties here.', 'wp-foft-loader' ) . '</p>
<details>
	<summary>' . __( 'Stage 1 <abbr>CSS</abbr>', 'wp-foft-loader' ) . '</summary>
	<p>' . __( 'Declarations placed in this field will load the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=subset">' . __( 'optimized subset', 'wp-foft-loader' ) . '</a> ' . __( 'as a placeholder while the non-subsetted fonts load.', 'wp-foftloader' ) . '</p>
	<ul class="wpfl">
		<li>' . __( 'Use only the family name followed by', 'wp-foft-loader' ) . ' <code>Subset</code> ' . __( '(case-sensitive)', 'wp-foft-loader' ) . '</li>
		<li>' . __( 'Family names must match the names you input on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=optimize">&ldquo;' . __( 'Optimize', 'wp-foft-loader' ) . '&rdquo; ' . __( 'screen.', 'wp-foft-loader' ) . '</a></li>
		<li>' . __( 'All declarations must start with the', 'wp-foft-loader' ) . '  <code>fonts-stage-1</code> ' . __( 'class', 'wp-foft-loader' ) . '</li>
	</ul>
	<p>' . __( 'See the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=documentation">' . __( 'Documentation screen', 'wp-foft-loader' ) . '</a> ' . __( 'to view the Stage 1 <abbr>CSS</abbr> that this plugin loads by default.', 'wp-foft-loader' ) . '</p>
	<dl class="col-2">
	  <dt>' . __( 'Incorrect:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.nav-primary { <mark>// ' . __( 'Missing class:', 'wp-foft-loader' ) . ' .fonts-stage-1</mark>
  font-family: latoSubset, sans-serif;
}

.fonts-stage-1 #footer-secondary {
  font-family: lato, san-serif; <mark>// ' . __( 'Missing', 'wp-foft-loader' ) . ' &ldquo;' . __( 'Subset', 'wp-foft-loader' ) . '&rdquo; ' . __( 'suffix', 'wp-foftloader' ) . '</mark>
}

.fonts-stage-1 div.callout {
  font-family: latoSubset, san-serif;
  font-size: 1rem; <mark>// ' . __( '&ldquo;font-family,&rdquo; &ldquo;font-weight,&rdquo; &ldquo;font-style,&rdquo;', 'wp-foft-loader' ) . '</mark>
                   <mark>// ' . __( 'and &ldquo;font-variant&rdquo; rules only', 'wp-foft-loader' ) . '</mark>
}

.fonts-stage-1 div.callout {
  font-family: latosubset, san-serif; <mark>// &ldquo;' . __( 'Subset', 'wp-foft-loader' ) . '&rdquo; ' . __( 'suffix is case-sensitive', 'wp-foft-loader' ) . '</mark>
}</code></pre>
	</dd>
		<dt>' . __( 'Correct:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.fonts-stage-1 .nav-primary {
  font-family: latoSubset, sans-serif;
}
</code></pre>
		</dd>
	</dl>
	<p>
</details>
<details>
	<summary>' . __( 'Stage 2 <abbr>CSS</abbr>', 'wp-foft-loader' ) . '</summary>
	<p>' . __( 'Declarations placed in this field will load after non-subsetted fonts load.', 'wp-foftloader' ) . '</p>
	<ul class="wpfl">
		<li>' . __( 'Use only the family name', 'wp-foft-loader' ) . '</li>
		<li>' . __( 'Family names must match the file names for the fonts you uploaded on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=upload">&ldquo;' . __( 'Upload', 'wp-foft-loader' ) . '&rdquo; ' . __( 'screen.', 'wp-foft-loader' ) . '</a></li>
		<li>' . __( 'Omit weights and styles from the font name', 'wp-foft-loader' ) . '</li>
		<li>' . __( 'All declarations must start with the', 'wp-foft-loader' ) . '  <code>fonts-stage-2</code> ' . __( 'class', 'wp-foft-loader' ) . '</li>
		<li>' . __( 'For best performance, please', 'wp-foft-loader' ) . ' <a href="//cssminifier.com" rel="external noreferrer noopener">' . __( 'minify your <abbr>CSS</abbr></a> before pasting it into the form.', 'wp-foft-loader' ) . '</li>
	</ul>
	<p>' . __( 'See the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=documentation">' . __( 'Documentation screen', 'wp-foft-loader' ) . '</a> ' . __( 'to view the Stage 2 <abbr>CSS</abbr> that this plugin loads by default.', 'wp-foft-loader' ) . '</p>
	<dl class="col-2">
		<dt>' . __( 'Incorrect:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>tbody { <mark>// ' . __( 'Missing class:', 'wp-foft-loader' ) . ' .fonts-stage-2</mark>
  font-family: lato, Corbel, "Lucida Grande", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.fonts-stage-2 span.bolder {
  font-family: lato-bold, Corbel, "Lucida Grande", sans-serif; <mark>// ' . __( 'Don&rsquo;t include style in font name.', 'wp-foft-loader' ) . '</mark>
  <mark>// ' . __( 'Better yet, omit declaration altogether.', 'wp-foft-loader' ) . '</mark>
  font-weight: 700;
}

.fonts-stage-2 div.callout {
  font-family: lato-regular, Corbel, "Lucida Grande", san-serif;
  font-size: 1rem; <mark>// ' . __( '&ldquo;font-family,&rdquo; &ldquo;font-weight,&rdquo; &ldquo;font-style,&rdquo;', 'wp-foft-loader' ) . '</mark>
                   <mark>// ' . __( 'and &ldquo;font-variant&rdquo; rules only', 'wp-foft-loader' ) . '</mark>
}</code></pre>
		</dd>
		<dt>' . __( 'Correct:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.fonts-stage-2 div.callout {
  font-family: lato, Corbel, "Lucida Grande", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.fonts-stage-2 div.callout {
  <mark>// ' . __( 'No need to redeclare the font-family &mdash; all weights map to a single family name', 'wp-foft-loader' ) . '</mark>
  font-weight: 700; <mark>// ' . __( 'This will use the lato-bold font' ) . '</mark>
}</code></pre>
		</dd>
	</dl>
	<p>
</details>',
            'fields'      => array(
            array(
            'id'          => 'default_css',
            'label'       => __( 'Plugin CSS', 'wp-foft-loader' ),
            'description' => __( 'The plugin loads some <abbr>CSS</abbr> by default. See', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=documentation">' . __( 'documentation', 'wp-foft-loader' ) . '</a>.',
            'type'        => 'radio',
            'options'     => array(
            'off' => __( 'Default <abbr>CSS</abbr> Off', 'wp-foft-loader' ),
            'on'  => __( 'Default <abbr>CSS</abbr> On', 'wp-foft-loader' ),
        ),
            'default'     => 'on',
        ),
            array(
            'id'          => 'font_display',
            'label'       => __( 'Font Display', 'wp-foft-loader' ),
            'description' => __( 'Override the', 'wp-foft-loader' ) . '<code><a href="//developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display" rel="external noopener noreferrer">font-display</a></code>' . __( ' property here. The plugin uses', 'wp-foft-loader' ) . ' <code>font-display: swap</code>' . __( ' by default.', 'wp-foft-loader' ),
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
            'label'       => __( 'Stage 1 CSS', 'wp-foft-loader' ),
            'description' => __( 'Place <abbr>CSS</abbr> font declarations for subsetted fonts here.', 'wp-foft-loader' ),
            'type'        => 'textarea',
            'default'     => null,
            'placeholder' => __( 'Example:', 'wp-foft-loader' ) . '.fonts-stage-1 body {
  font-family: merriweatherSubset, serif;
}',
        ),
            array(
            'id'          => 'stage_2',
            'label'       => __( 'Stage 2 CSS', 'wp-foft-loader' ),
            'description' => __( 'Place <abbr>CSS</abbr> font declarations for non-subsetted fonts here.', 'wp-foft-loader' ),
            'type'        => 'textarea_large',
            'default'     => null,
            'placeholder' => __( 'Example:', 'wp-foft-loader' ) . '.fonts-stage-2 body {
  font-family: merriweather, "Century Schoolbook L", Georgia, serif;
}
.fonts-stage-2 strong {
  font-weight: 700;
}',
        )
        ),
        );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            $settings['fstack'] = array(
                'title'       => __( 'Font Stack', 'wp-foft-loader' ),
                'description' => '<p>' . __( 'Change the default font fallbacks in case your custom fonts don&rsquo;t load.', 'wp-foft-loader' ) . ' <strong>' . __( 'Don&rsquo;t include the names of your default custom fonts here.', 'wp-foft-loader' ) . '</strong></p>',
                'fields'      => array(
                array(
                'id'          => 'fstack-heading',
                'label'       => __( 'Headings', 'wp-foft-loader' ),
                'description' => __( 'Font stack for display font. Applies to high-level headings', 'wp-foft-loader' ) . '(H1, H2, &amp; H3).',
                'type'        => 'textarea',
                'default'     => '"Palatino Linotype",Palatino,Palladio,"URW Palladio L","Book Antiqua",Baskerville,"Bookman Old Style","Bitstream Charter","Nimbus Roman No9 L",Garamond,"Apple Garamond","ITC Garamond Narrow","New Century Schoolbook","Century Schoolbook","Century Schoolbook L",Georgia,serif',
                'placeholder' => '',
            ),
                array(
                'id'          => 'fstack-body',
                'label'       => __( 'Body', 'wp-foft-loader' ),
                'description' => __( 'Font stack for body text. This can be a serif or sans-serif font.', 'wp-foft-loader' ),
                'type'        => 'textarea',
                'default'     => '"Palatino Linotype",Palatino,Palladio,"URW Palladio L","Book Antiqua",Baskerville,"Bookman Old Style","Bitstream Charter","Nimbus Roman No9 L",Garamond,"Apple Garamond","ITC Garamond Narrow","New Century Schoolbook","Century Schoolbook","Century Schoolbook L",Georgia,serif',
                'placeholder' => '',
            ),
                array(
                'id'          => 'fstack-alt',
                'label'       => __( 'Other elements', 'wp-foft-loader' ),
                'description' => __( 'Font stack for non-body elements,', 'wp-foft-loader' ) . '<abbr>e.g.</abbr>,' . __( 'navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wp-foft-loader' ),
                'type'        => 'textarea',
                'default'     => '-apple-system,BlinkMacSystemFont,"Segoe UI",Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
                'placeholder' => '',
            ),
                array(
                'id'          => 'fstack-mono',
                'label'       => __( 'Monospaced', 'wp-foft-loader' ),
                'description' => __( 'Font stack for monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
                'type'        => 'textarea',
                'default'     => 'Consolas,"Andale Mono WT","Andale Mono","Lucida Console","Lucida Sans Typewriter","DejaVu Sans Mono","Bitstream Vera Sans Mono","Liberation Mono","Nimbus Mono L",Monaco,monospace',
                'placeholder' => '',
            )
            ),
            );
        }
        $settings['documentation'] = array(
            'title'       => __( 'Documentation', 'wp-foft-loader' ),
            'description' => '<section>
	<h3>' . __( 'Fonts Stage 1', 'wp-foft-loader' ) . '</h3>
	<p>' . __( 'This plugin always loads the following Stage 1 styles. The Stage 1 fonts are subsetted fonts, acting as placeholders until the full Stage 2 fonts load. &lt;$bodySubset&gt;, &lt;$altSubset&gt;, &lt;$headingSubset&gt;, and &lt;$monoSubset&gt; correspond to the Body, Other Elements, Headings, and Monospaced font-families configured on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=optimize">' . __( 'Subset', 'wp-foft-loader' ) . '</a> options screen.</p>
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
	<p>' . __( 'This plugin also loads the following Stage 2 styles. You can disable these styles on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=css"><abbr>CSS</abbr> ' . __( 'options screen', 'wp-foft-loader' ) . '</a>. <$body>, <$alt>, <$heading>, ' . __( 'and', 'wp-foft-loader' ) . ' <$mono> ' . __( 'correspond to the Body, Other Elements, Headings, and Monospaced font-families configured on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=subset">' . __( 'Subset screen', 'wp-foft-loader' ) . '</a>. ' . __( 'You can change the default font fallbacks on the', 'wp-foftloader' ) . ' <a href="?page=wp_foft_loader_settings&tab=fstack">' . __( 'Font Stack settings screen', 'wp-foft-loader' ) . '</a>.</p>
<pre class="col-3"><code>.fonts-stage-2 body,
.fonts-stage-2 h4,
.fonts-stage-2 h5,
.fonts-stage-2 h6 {
  font-family: $body, "Palatino Linotype", Palatino, Palladio, "URW Palladio L", "Book Antiqua", Baskerville, "Bookman Old Style", "Bitstream Charter", "Nimbus Roman No9 L", Garamond, "Apple Garamond", "ITC Garamond Narrow", "New Century Schoolbook", "Century Schoolbook", "Century Schoolbook L", Georgia, serif
}

.fonts-stage-2 h1,
.fonts-stage-2 h2,
.fonts-stage-2 h3 {
  font-family: $heading, "Palatino Linotype", Palatino, Palladio, "URW Palladio L", "Book Antiqua", Baskerville, "Bookman Old Style", "Bitstream Charter", "Nimbus Roman No9 L", Garamond, "Apple Garamond", "ITC Garamond Narrow", "New Century Schoolbook", "Century Schoolbook", "Century Schoolbook L", Georgia, serif;
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
  font-family: $sans, Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, monospace
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
  font-family: $alt, -apple-system, BlinkMacSystemFont, "Segoe UI", Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-weight: 400
}</code></pre>
</section>
<section>
	<h3>' . __( 'Video Tutorials', 'wp-foft-loader' ) . '</h3>
    <div class="col-2">
      <div class="video-responsive"><iframe width="560" height="315" src="https://www.youtube.com/embed/0C0lDJ3T12o?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
      <div class="video-responsive"><iframe width="560" height="315" src="https://www.youtube.com/embed/-StFYcOSDCU?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
    </div>
</section>',
            'fields'      => array( array(
            'id'          => 'documentation',
            'label'       => null,
            'description' => null,
            'type'        => 'hidden',
            'default'     => null,
            'placeholder' => null,
        ) ),
        );
        $settings = apply_filters( $this->parent->token . '_settings_fields', $settings );
        return $settings;
    }
    
    /**
     * Register plugin settings
     *
     * @return void
     */
    public function register_settings()
    {
        
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
                    array( $this, 'settings_section' ),
                    $this->parent->token . '_settings'
                );
                foreach ( $data['fields'] as $field ) {
                    // Validation callback for field.
                    $validation = '';
                    if ( isset( $field['callback'] ) ) {
                        $validation = $field['callback'];
                    }
                    // Register field.
                    $option_name = _BASE_ . $field['id'];
                    register_setting( $this->parent->token . '_settings', $option_name, $validation );
                    // Add field to page.
                    add_settings_field(
                        $field['id'],
                        $field['label'],
                        array( $this->parent->admin, 'display_field' ),
                        $this->parent->token . '_settings',
                        $section,
                        array(
                        'field'  => $field,
                        'prefix' => _BASE_,
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
    public function settings_section( $section )
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . chr( 0xd ) . chr( 0xa );
        echo  $html ;
        // phpcs:ignore
    }
    
    /**
     * Load settings page content
     *
     * @return void
     */
    public function settings_page()
    {
        $html = '<div class="wrap" id="' . $this->parent->token . '_settings">' . chr( 0xd ) . chr( 0xa ) . '<h2><span class="wp-admin-lite-blue"><i class="fa fa-3x fa-font" aria-hidden="true"></i><i class="fa fa-2x fa-font" aria-hidden="true"></i><i class="fa fa-font" aria-hidden="true"></i></span> ' . __( 'WP <abbr>FOFT</abbr> Loader Settings', 'wp-foft-loader' ) . '</h2>' . chr( 0xd ) . chr( 0xa ) . '<p>' . __( 'Automates', 'wp-foft-loader' ) . ' &ldquo;<a href="https://github.com/zachleat/web-font-loading-recipes#the-compromise-critical-foft-with-preload-with-a-polyfill-fallback-emulating-font-display-optional" rel="external noreferrer noopener"><strong>' . __( 'Critical', 'wp-foft-loader' ) . ' <abbr title="Flash of Faux Text">FOFT</abbr> ' . __( 'with preload, with a polyfill fallback emulating font-display: optional', 'wp-foft-loader' ) . '</strong></a>&rdquo;' . __( ' to speed up font loading while eliminating Flash of Unstyled Text (', 'wp-foft-loader' ) . '<abbr>FOUT</abbr>). ' . __( 'Based on the work of', 'wp-foft-loader' ) . ' <span class="h-card"><a class="p-name u-url" href="https://www.zachleat.com/">Zach Leatherman</a></span>.</p>' . chr( 0xd ) . chr( 0xa );
        if ( !wpfl_fs()->can_use_premium_code() ) {
            $html .= '<p><strong>' . __( 'Upgrade to', 'wp-foft-loader' ) . ' <abbr>WP</abbr> <abbr>FOFT</abbr> Loader PRO ' . __( 'for additional font weights and small-caps support', 'wp-foft-loader.' ) . '.</strong></p>' . chr( 0xd ) . chr( 0xa );
        }
        $html .= '<p>' . __( 'Please', 'wp-foft-loader' ) . ' <strong>' . __( 'save your changes', 'wp-foft-loader' ) . '</strong> ' . __( 'before navigating to the next tab. ', 'wp-foft-loader' ) . '</p>' . chr( 0xd ) . chr( 0xa );
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
                $html .= '<a href="' . esc_attr( $tab_link ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . chr( 0xd ) . chr( 0xa );
                ++$c;
            }
            $html .= '</h2>' . chr( 0xd ) . chr( 0xa );
        }
        
        $html .= '<form method="post" action="options.php" name="wpflSettings" id="wpflSettings" enctype="multipart/form-data">' . chr( 0xd ) . chr( 0xa );
        // Get settings fields.
        ob_start();
        settings_fields( $this->parent->token . '_settings' );
        do_settings_sections( $this->parent->token . '_settings' );
        $html .= ob_get_clean();
        global  $pagenow ;
        // Run certain logic ONLY if we are on the correct settings page.
        $html .= '<p class="submit">' . chr( 0xd ) . chr( 0xa );
        $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . chr( 0xd ) . chr( 0xa );
        echo  $html ;
        // phpcs:ignore
        submit_button(
            __( 'Save Settings', 'wp-foft-loader' ),
            'primary',
            'save_wpfl_options',
            false
        );
        $html2 = '</p>' . chr( 0xd ) . chr( 0xa );
        $html2 .= '</form>' . chr( 0xd ) . chr( 0xa );
        $html2 .= '</div>' . chr( 0xd ) . chr( 0xa );
        $success1 = __( 'Yeehaw!', 'wp-foft-loader' );
        $success2 = __( 'Good Job!', 'wp-foft-loader' );
        $success3 = __( 'Hooray!', 'wp-foft-loader' );
        $success4 = __( 'Yay!', 'wp-foft-loader' );
        $success5 = __( 'Huzzah!', 'wp-foft-loader' );
        $success6 = __( 'Bada bing bada boom!', 'wp-foft-loader' );
        $message1 = array(
            $success1,
            $success2,
            $success3,
            $success4,
            $success5,
            $success6
        );
        $message1 = $message1[array_rand( $message1 )];
        $error1 = __( 'Dangit!', 'wp-foft-loader' );
        $error2 = __( 'Aw heck!', 'wp-foft-loader' );
        $error3 = __( 'Egads!', 'wp-foft-loader' );
        $error4 = __( 'D&rsquo;oh!', 'wp-foft-loader' );
        $error5 = __( 'Drat!', 'wp-foft-loader' );
        $error6 = __( 'Dagnabit!', 'wp-foft-loader' );
        $message2 = array(
            $error1,
            $error2,
            $error3,
            $error4,
            $error5,
            $error6
        );
        $message2 = $message2[array_rand( $message2 )];
        if ( 'options-general.php' === $pagenow && 'wp_foft_loader_settings' === $_GET['page'] ) {
            // Ajaxify the form. Timeout should be >= 5000 or you'll get errors.
            $html2 .= '<div id="saveResult"></div>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	   jQuery("#wpflSettings").submit(function() { 
		  jQuery(this).ajaxSubmit({
			 success: function(){
				jQuery("#saveResult").html(`<div id="saveMessage" class="notice notice-success is-dismissible"></div>`);
				jQuery("#saveMessage").append(`<p><span class="dashicons dashicons-yes-alt"></span> ' . $message1 . __( ' Your settings were saved!', 'wp-foft-loader' ) . '</p>`).show();
			 },
			 error: function(){
				jQuery("#saveResult").html(`<div id="saveMessage" class="notice notice-error is-dismissible"></div>`);
				jQuery("#saveMessage").append(`<p><span class="dashicons dashicons-no"></span> ' . $message2 . __( ' There was an error saving your settings. Please open a support ticket if the problem persists!', 'wp-foft-loader' ) . '</p>`).show();
			 },
			 timeout: 10000
		  }); 
		  setTimeout(`jQuery("#saveMessage").hide("slow");`, 7500);
		  return false; 
	   });
	});
	</script>';
        }
        echo  $html2 ;
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
    public static function instance( $parent )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $parent );
        }
        return self::$instance;
    }
    
    // End instance()
    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_API is forbidden.', 'wp-foft-loader' ), esc_attr( _VERSION_ ) );
    }
    
    // End __clone()
    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_API is forbidden.', 'wp-foft-loader' ), esc_attr( _VERSION_ ) );
    }

}