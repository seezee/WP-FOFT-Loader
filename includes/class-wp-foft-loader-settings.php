<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_FOFT_Loader_Settings {

	/**
	 * The single instance of WP_FOFT_Loader_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpfl_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( __( 'WP FOFT Loader Settings', 'wpfoft' ) , __( 'WP FOFT Loader', 'wpfoft' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

    	// We're including the WP media scripts here because they're needed for the image upload field
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'jquery' ), '1.0.1' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'wpfoft' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['upload'] = array(
			'title'					=> __( 'Upload', 'wpfoft' ),
			'description'			=> __( '
<p>Upload two files for each web font: a WOFF file and a WOFF2 file. We recommend you use <a href="https://www.fontsquirrel.com/tools/webfont-generator" target="_blank" rel="external noreferrer noopener">Font Squirrel’s Webfont Generator</a> to generate the files. Recommended Font Squirrel settings are:</p>
<pre>Select "Expert"
Font Formats:		"WOFF"
			"WOFF2"
Truetype Hinting:	"Font Squirrel"
Rendering:		"Fix GASP Table"
Vertical Metrics:	"Auto-Adjust Vertical Metrics"
Fix Missing Glyphs:	"Spaces"
			"Hyphens"
X-height Matching:	"Georgia" (for serif fonts)
			"Verdana" (for sans-serif fonts)
			"Courier" (for monospaced fonts)
Protection:		"WebOnly™"
Subsetting:		"Basic Subsetting"
OpenType Features:	Your choice, but we like "Keep All Features"
OpenType Flattening:	None
CSS:			None
Advanced Options:	"Font Name Suffix" = -webfont
			"Em Square Value"  = 2048
			"Adjust Glyph Spacing"  = 0
Shortcuts:		"Remember My Settings"</pre>
<p><strong>Filenames must follow the proper naming convention:</strong> <code>$family</code>-<code>$weight&amp;style</code>-webfont-<code>$filetype</code>, e.g., for the bold weight italic style of Times New Roman, rename the files to <code>timenewroman-boldItalic-webfont.woff</code> and <code>timesnewroman-boldItalic-webfont.woff2</code>. For small caps style families, append <code>SC</code> (case-sensitive) to the family name, e.g., <code>playfairdisplaySC-bold-webfont.woff</code>.</p>
<p><strong>Weights and styles are case-sensitive!</strong> Allowed weights and styles and their CSS mappings are:</p>
<ul style="columns: 3 15rem; column-gap: 2rem; column-rule: 1px solid;">
  <li>thin | hairline (maps to 100)</li>
  <li>extraLight | ultraLight (maps to 200)</li>
  <li>light (maps to 300)</li>
  <li>regular | normal (maps to 400)</li>
  <li>medium (maps to 500)</li>
  <li>demiBold | semiBold (maps to 600)</li>
  <li>bold (maps to 700)</li>
  <li>extraBold | ultraBold (maps to 800)</li>
  <li>black | heavy (maps to 900)</li>
  <li>thinItalic (maps to 100)</li>
  <li>hairlineItalic | extraLightItalic (maps to 200)</li>
  <li>ultraLightItalic | lightItalic (maps to 300)</li>
  <li>italic (maps to 400)</li>
  <li>mediumItalic (maps to 500)</li>
  <li>demiBoldItalic | semiBoldItalic (maps to 600)</li>
  <li>boldItalic (maps to 700)</li>
  <li>extraBoldItalic | ultraBoldItalic (maps to 800)</li>
  <li>blackItalic | heavyItalic (maps to 900)</li>
</ul>', 'wpfoft' ),
			'fields'				=> array(
				array(
					'id' 			=> 'font',
					'label'			=> __( 'Upload Fonts' , 'wpfoft' ),
					'description'	=> __( 'This will upload a font file to your media library and store the attachment ID in the option field. Once you have uploaded a font the thumbnail will display above these buttons.', 'wpfoft' ),
					'type'			=> 'font',
					'default'		=> '',
					'placeholder'	=> ''
				)
			)
		);

		$settings['optimize'] = array(
			'title'					=> __( 'Optimize', 'wpfoft' ),
			'description'			=> __( '
<p>Load small subsetted font files before the page fully loads to improve performance. <em>This setting works with the Base64 settings in the next tab.</em> All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Base64 settings field.</p>
<p>Enter the names of your Base64 subsetted fonts below. Only the family name is needed, not the style. Names are case-insensitive. Hyphens and underscores are allowed, <em>but spaces are not</em>.<p>
<dl>
	<dt>Correct:</dt>
	<dd><strong>playfairdisplay</strong> (all lowercase)</dd>
	<dd><strong>playfair-display</strong> (hyphens and underscores allowed)</dd>
	<dd><strong>PlayfairDisplay</strong> (mixed case allowed)</dd>
	<dt>Incorrect:</dt>
	<dd><strong>playfairdisplay-bold</strong> (use the family name only; omit the style, <abbr>i.e.</abbr>, &ldquo;bold&rdquo;)</dd>
	<dd><strong>playfair display</strong> (spaces prohibited)</dd>
	<dd><strong>Playfair Display</strong> (spaces prohibited)</dd>
</dl>', 'wpfoft' ),
			'fields'				=> array(
				array(
					'id' 			=> 's1-heading',
					'label'			=> __( 'Headings' , 'wpfoft' ),
					'description'	=> __( 'Optimize the display font used for high-level headings (H1, H2, &amp; H3).', 'wpfoft' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'e.g., playfairdisplay', 'wpfoft' )
				),
				array(
					'id' 			=> 's1-body',
					'label'			=> __( 'Body' , 'wpfoft' ),
					'description'	=> __( 'Optimize body text. This can be a serif or sans-serif font.', 'wpfoft' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'e.g., timesnewroman', 'wpfoft' )
				),
				array(
					'id' 			=> 's1-alt',
					'label'			=> __( 'Other elements' , 'wpfoft' ),
					'description'	=> __( 'Optimize non-body elements, <abbr>e.g.</abbr>, navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wpfoft' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'e.g., latosans', 'wpfoft' )
				),
				array(
					'id' 			=> 's1-mono',
					'label'			=> __( 'Monospaced' , 'wpfoft' ),
					'description'	=> __( 'Optimize monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wpfoft' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'e.g., couriernew', 'wpfoft' )
				)
			)
		);

		$settings['base64'] = array(
			'title'					=> __( 'Base64', 'wpfoft' ),
			'description'			=> __( '
<p>This setting inlines Base64 encoded font in the document head to improve font loading speeds. <em>This setting works with the Optimize settings in the previous tab.</em> All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Optimize settings field.</p>
<p>Fonts must be subsetted and encoded to Base64. To subset and encode your fonts, we recommend you use <a href="https://www.fontsquirrel.com/tools/webfont-generator" target="_blank" rel="external noreferrer noopener">Font Squirrel’s Webfont Generator</a>. Recommended Font Squirrel settings are:</p>
<pre>Select "Expert"
Font Formats:		None
Truetype Hinting:	"Font Squirrel"
Rendering:		"Fix GASP Table"
Vertical Metrics:	"Auto-Adjust Vertical Metrics"
Fix Missing Glyphs:	None
X-height Matching:	"Georgia" (for serif fonts)
			"Verdana" (for sans-serif fonts)
			"Courier" (for monospaced fonts)
Protection:		"WebOnly™"
Subsetting:		"Custom Subsetting" with the Unicode Ranges <code>0030-0039,0041-005A,0061-007A</code>
			Leave everything else unchecked
OpenType Features:	None
OpenType Flattening:	None
CSS:			"Base64 Encode"
Advanced Options:	"Font Name Suffix" = -webfont
			"Em Square Value"  = 2048
			"Adjust Glyph Spacing"  = 0
Shortcuts:		"Remember My Settings"</pre>
<p>The generator will produce a file that looks something like this:</p>
<pre><code>@font-face{
  font-family: Merriweather;
  src: url(data:application/font-woff; charset=utf-8; base64, d09GRgABAAAAAB4UABAAAAAAMpAAA…) format("woff");
 }</code></pre>
<p>Copy and paste the part the part between <pre><code>src:url (data:application/font-woff; charset=utf-8; base64, </code></pre> and <pre><code>) format("woff");</code></pre> into the appropriate field below. In this example that would be <code>d09GRgABAAAAAB4UABAAAAAAMpAAA…</code>.</p>', 'wpfoft' ),
			'fields'				=> array(
				array(
					'id' 			=> 'b64-heading',
					'label'			=> __( 'Headings' , 'wpfoft' ),
					'description'	=> __( 'Display font for high-level headings (H1, H2, &amp; H3).', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> ''
				),
				array(
					'id' 			=> 'b64-body',
					'label'			=> __( 'Body' , 'wpfoft' ),
					'description'	=> __( 'Body text. This can be a serif or sans-serif font.', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> ''
				),
				array(
					'id' 			=> 'b64-alt',
					'label'			=> __( 'Other elements' , 'wpfoft' ),
					'description'	=> __( 'Non-body elements, <abbr>e.g.</abbr>, navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> ''
				),
				array(
					'id' 			=> 'b64-mono',
					'label'			=> __( 'Monospaced' , 'wpfoft' ),
					'description'	=> __( 'Monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> ''
				)
			)
		);

		$settings['css'] = array(
			'title'					=> __( 'CSS', 'wpfoft' ),
			'description'			=> __( '

</style>
<p>@import rules are automatically handled by this plugin. You may manually inline your font-related <abbr>CSS</abbr> in the document <code>&lt;head&gt;</code> here. Place rules pertaining only to the <code>font-family</code>, <code>font-weight</code>, <code>font-style</code>, and <code>font-variation</code> properties here.</p>
<p><em>Use only the family name</em>; <strong>omit weights and styles from the font name</strong>.</p>
<p><strong>All declarations must start with the</strong>  <code>fonts-stage-2</code> <strong>class.</strong></p>
<dl style="columns: 2 15rem; column-gap: 2rem; column-rule: 1px solid">
  <dt>Incorrect:</dt>
    <dd><pre><code>p { // Missing class: .fonts-stage-2
  font-family: lato, sans-serif;
  font-weight: 400;
  font-style: normal;
}

strong { // Missing class: .fonts-stage-2
  font-family: lato-bold, serif; // Don&rsquo;t include style in font name. Better yet, omit declaration altogether.
  font-weight: 700;
}
</code></pre></dd>
<dt>Correct:</dt>
  <dd><pre><code>.fonts-stage-2 p {
  font-family: lato, sans-serif;
  font-weight: 400;
  font-style: normal;
}

.fonts-stage-2 strong {
  // No need to redeclare the font-family &mdash; all weights map to a single family name
  font-weight: 700; // This will use the lato-bold font
}
</code></pre></dd>
</dl><p>For best performance, please <a href="//cssminifier.com" rel="external noreferrer noopener">minify your <abbr>CSS</abbr></a> before pasting it into the form.', 'wpfoft' ),
			'fields'				=> array(
				array(
					'id' 			=> 'default_css',
					'label'			=> __( 'Plugin CSS', 'wpfoft' ),
					'description'	=> __( 'The plugin loads some <abbr>CSS</abbr> by default.', 'wpfoft' ),
					'type'			=> 'radio',
					'options'		=> array(
						'off' => 'Default <abbr>CSS</abbr> Off',
						'on' => 'Default <abbr>CSS</abbr> On'
					),
					'default'       => 'on'
				),
				array(
					'id' 			=> 'custom_css',
					'label'			=> __( 'Custom CSS' , 'wpfoft' ),
					'description'	=> __( 'Place <abbr>CSS</abbr> font declarations here.', 'wpfoft' ),
					'type'			=> 'textarea_large',
					'default'		=> '',
					'placeholder'	=> __( 'Example:
.fonts-stage-2 body {
  font-family: merriweather, "Century Schoolbook L", Georgia, serif;
}
.fonts-stage-2 strong {
  font-weight: 700;
}', 'wpfoft' )
				)
			)
		);

		$settings['fstack'] = array(
			'title'					=> __( 'Font Stack', 'wpfoft' ),
			'description'			=> __( '
<p>Change the default font fallbacks in case your custom fonts don&rsquo;t load. <strong>Don&rsquo;t include the names of your default custom fonts here</strong>.</p>', 'wpfoft' ),
			'fields'				=> array(
				array(
					'id' 			=> 'fstack-heading',
					'label'			=> __( 'Headings' , 'wpfoft' ),
					'description'	=> __( 'Font stack for display font. Applies to high-level headings (H1, H2, &amp; H3).', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> '"Palatino Linotype",Palatino,Palladio,"URW Palladio L","Book Antiqua",Baskerville,"Bookman Old Style","Bitstream Charter","Nimbus Roman No9 L",Garamond,"Apple Garamond","ITC Garamond Narrow","New Century Schoolbook","Century Schoolbook","Century Schoolbook L",Georgia,serif',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'fstack-body',
					'label'			=> __( 'Body' , 'wpfoft' ),
					'description'	=> __( 'Font stack for body text. This can be a serif or sans-serif font.', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> '"Palatino Linotype",Palatino,Palladio,"URW Palladio L","Book Antiqua",Baskerville,"Bookman Old Style","Bitstream Charter","Nimbus Roman No9 L",Garamond,"Apple Garamond","ITC Garamond Narrow","New Century Schoolbook","Century Schoolbook","Century Schoolbook L",Georgia,serif',
					'placeholder'	=> ''

				),
				array(
					'id' 			=> 'fstack-alt',
					'label'			=> __( 'Other elements' , 'wpfoft' ),
					'description'	=> __( 'Font stack for non-body elements, <abbr>e.g.</abbr>, navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
					'placeholder'	=> ''

				),
				array(
					'id' 			=> 'fstack-mono',
					'label'			=> __( 'Monospaced' , 'wpfoft' ),
					'description'	=> __( 'Font stack for monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wpfoft' ),
					'type'			=> 'textarea',
					'default'		=> 'Consolas,"Andale Mono WT","Andale Mono","Lucida Console","Lucida Sans Typewriter","DejaVu Sans Mono","Bitstream Vera Sans Mono","Liberation Mono","Nimbus Mono L",Monaco,"Courier New",Courier,monospace',
					'placeholder'	=> ''

				)
			)
		);


		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2><span style="color:#21759b"><i class="fa fa-3x fa-font" aria-hidden="true"></i><i class="fa fa-2x fa-font" aria-hidden="true"></i><i class="fa fa-font" aria-hidden="true"></i></span> ' . __( 'WP <abbr>FOFT</abbr> Loader Settings' , 'wpfoft' ) . '</h2>' . "\n" . '<p>' . __( 'Automates <a href="https://www.zachleat.com/web/comprehensive-webfonts/#critical-foft-data-uri" rel="external noreferrer noopener"><strong>Critical <abbr title="Flash of Faux Text">FOFT</abbr> with Data <abbr title="Uniform Resourse Identifier">URI</abbr></a></strong> to speed up font loading while eliminating Flash of Unstyled Text <abbr>FOUT</abbr>. Based on the work of <span class="h-card"><a class="p-name u-url" href="https://www.zachleat.com/">Zach Leatherman</a></span>. ' ) . '</p>' . "\n" . '<p>' . __( 'Please <strong>save your changes</strong> before navigating to the next tab. ' ) . '</p>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wpfoft' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main WP_FOFT_Loader_Settings Instance
	 *
	 * Ensures only one instance of WP_FOFT_Loader_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP_FOFT_Loader()
	 * @return Main WP_FOFT_Loader_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}