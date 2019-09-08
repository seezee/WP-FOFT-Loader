<?php
/**
 * Settings class file.
 *
 * @package WP FOFT Loader/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
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
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

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

		$this->base = 'wpfl_';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( $this->parent->file ),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of plugin settings page. See readme for implementation.
		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
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
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
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
	private function menu_settings() {
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'WP FOFT Loader Settings', 'wp-foft-loader' ),
				'menu_title'  => __( 'WP FOFT Loader', 'wp-foft-loader' ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->parent->token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
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

		wp_register_script( $this->parent->token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', 'jquery', '1.0.18', true );
		wp_enqueue_script( $this->parent->token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table.
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->token . '_settings">' . __( 'Settings', 'wp-foft-loader' ) . '</a>';
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

		$settings['upload'] = array(
			'title'       => __( 'Upload', 'wp-foft-loader' ),
			'description' => '<p>' . __(
				'
Upload two files for each web font: a WOFF file and a WOFF2 file. We recommend you use',
				'wp-foft-loader'
			) . ' <a href="https://www.fontsquirrel.com/tools/webfont-generator" target="_blank" rel="external noreferrer noopener">Font Squirrel’s ' . __( 'Webfont Generator', 'wp-foft-loader' ) . '</a> ' . __( 'to generate the files. Recommended Font Squirrel settings are:', 'wp-foft-loader' ) . '</p>
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
  <dd>&ldquo;' . __( 'Basic Subsetting', 'wp-foft-loader' ) . '&rdquo;</dd>
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
<p><strong>' . __( 'Filenames must follow the proper naming convention:', 'wp-foft-loader' ) . '</strong> <code>$family</code>SC-<code>$variant</code>-webfont-<code>$filetype</code>.</p>
<dl>
<dt>$family</dt>
<dd>' . __( 'The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but', 'wp-foft-loader' ) . ' <em>' . __( 'no hyphens or spaces', 'wp-foft-loader' ) . '</em>.</dd>
<dt>SC</dt>
<dd>' . __( 'Small caps identifier', 'wp-foft-loader' ) . '. <em>' . __( 'Optional', 'wp-foft-loader' ) . '</em>. ' . __( 'Append to $family only if it is a small caps variant.', 'wp-foft-loader' ) . ' <em>' . __( 'Case-sensitive', 'wp-foft-loader' ) . '</em>.</dd>
<dt>$variant</dt>
<dd>' . __( 'The font variant. Can be weight, style, or a combination of both.', 'wp-foft-loader' ) . ' <em>' . __( 'Case-sensitive', 'wp-foft-loader' ) . '</em>.</dd>
<dt>-webfont-</dt>
<dd>' . __( 'Mandatory suffix. Append to', 'wp-foft-loader' ) . ' $variant.</dd>
<dt>$filetype</dt>
<dd>' . __( 'The file type', 'wp-foft-loader' ) . ', i.e., &ldquo;woff&rdquo; ' . __( 'or', 'wp-foft-loader' ) . ' &ldquo;woff2&rdquo;.</dd>
</dl>
<p><strong>' . __( 'Example', 'wp-foft-loader' ) . '</strong>: ' . __( 'for the bold weight, italic style of', 'wp-foft-loader' ) . ' Times New Roman, ' . __( 'rename the files to', 'wp-foft-loader' ) . ' <code>timenewroman-boldItalic-webfont.woff</code> ' . __( 'and', 'wp-foft-loader' ) . ' <code>timesnewroman-boldItalic-webfont.woff2</code>. ' . __( 'For small caps style families, append', 'wp-foft-loader' ) . ' <code>SC</code> (' . __( 'case-sensitive', 'wp-foft-loader' ) . ') ' . __( 'to the family name,', 'wp-foft-loader' ) . ' e.g., <code>playfairdisplaySC-regular-webfont.woff</code>.</p>
<p>' . __( 'Allowed weights and styles and their CSS mappings are:', 'wp-foft-loader' ) . '</p>
<ul class="col-3">
  <li>thin | hairline (' . __( 'maps to', 'wp-foft-loader' ) . ' 100)</li>
  <li>extraLight | ultraLight (' . __( 'maps to', 'wp-foft-loader' ) . ' 200)</li>
  <li>light (' . __( 'maps to', 'wp-foft-loader' ) . ' 300)</li>
  <li>regular | normal (' . __( 'maps to', 'wp-foft-loader' ) . ' 400)</li>
  <li>medium (' . __( 'maps to', 'wp-foft-loader' ) . ' 500)</li>
  <li>demiBold | semiBold (' . __( 'maps to', 'wp-foft-loader' ) . ' 600)</li>
  <li>bold (' . __( 'maps to', 'wp-foft-loader' ) . ' 700)</li>
  <li>extraBold | ultraBold (' . __( 'maps to', 'wp-foft-loader' ) . ' 800)</li>
  <li>black | heavy (' . __( 'maps to', 'wp-foft-loader' ) . ' 900)</li>
  <li>thinItalic | hairlineItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 100)</li>
  <li>extraLightItalic | ultraLightItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 200)</li>
  <li>lightItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 300)</li>
  <li>italic (' . __( 'maps to', 'wp-foft-loader' ) . ' 400)</li>
  <li>mediumItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 500)</li>
  <li>demiBoldItalic | semiBoldItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 600)</li>
  <li>boldItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 700)</li>
  <li>extraBoldItalic | ultraBoldItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 800)</li>
  <li>blackItalic | heavyItalic (' . __( 'maps to', 'wp-foft-loader' ) . ' 900)</li>
</ul>',
			'fields'      => array(
				array(
					'id'          => 'font',
					'label'       => __( 'Upload Fonts', 'wp-foft-loader' ),
					'description' => __( 'This will upload a font file to your media library and store the attachment ID in the option field.', 'wp-foft-loader' ),
					'type'        => 'font',
					'default'     => '',
					'placeholder' => '',
				),
			),
		);

		$settings['optimize'] = array(
			'title'       => __( 'Optimize', 'wp-foft-loader' ),
			'description' => '<p>' . __( 'Load small subsetted font files before the page fully loads to improve performance.', 'wp-foft-loader' ) . ' <em>' . __( 'This setting works with the Base64 settings in the next tab', 'wp-foft-loader' ) . '.</em> ' . __( 'All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Base64 settings field.', 'wp-foft-loader' ) . '</p>
<p>' . __( 'Enter the names of your Base64 subsetted fonts below. Only the family names are needed, not the styles. Names are case-insensitive. Hyphens and underscores are allowed,', 'wp-foft-loader' ) . ' <em>' . __( 'but spaces are not', 'wp-foft-loader' ) . '</em>.<p>
<dl>
	<dt>' . __( 'Correct', 'wp-foft-loader' ) . ':</dt>
	<dd><strong>playfairdisplay</strong> (' . __( 'all lowercase', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>playfair-display</strong> (' . __( 'hyphens and underscores allowed', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>PlayfairDisplay</strong> (' . __( 'mixed case allowed', 'wp-foft-loader' ) . ')</dd>
	<dt>' . __( 'Incorrect', 'wp-foft-loader' ) . ':</dt>
	<dd><strong>playfairdisplay-bold</strong> (' . __( 'use the family name only; omit the style', 'wp-foft-loader' ) . ', <abbr>i.e.</abbr>, &ldquo;bold&rdquo;)</dd>
	<dd><strong>playfair display</strong> (' . __( 'spaces prohibited', 'wp-foft-loader' ) . ')</dd>
	<dd><strong>Playfair Display</strong> (' . __( 'spaces prohibited', 'wp-foft-loader' ) . ')</dd>
</dl>',
			'fields'      => array(
				array(
					'id'          => 's1-heading',
					'label'       => __( 'Headings', 'wp-foft-loader' ),
					'description' => __( 'Optimize the display font used for high-level headings', 'wp-foft-loader' ) . '(H1, H2, &amp; H3)',
					'type'        => 'alnumdash',
					'default'     => null,
					'placeholder' => 'e.g., playfairdisplay',
				),
				array(
					'id'          => 's1-body',
					'label'       => __( 'Body', 'wp-foft-loader' ),
					'description' => __( 'Optimize body text. This can be a serif or sans-serif font.', 'wp-foft-loader' ),
					'type'        => 'alnumdash',
					'default'     => null,
					'placeholder' => 'e.g., timesnewroman',
				),
				array(
					'id'          => 's1-alt',
					'label'       => __( 'Other elements', 'wp-foft-loader' ),
					'description' => __( 'Optimize non`-body elements', 'wp-foft-loader' ) . ', <abbr>e.g.</abbr>,' . __( 'navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wp-foft-loader' ),
					'type'        => 'alnumdash',
					'default'     => null,
					'placeholder' => 'e.g., latosans',
				),
				array(
					'id'          => 's1-mono',
					'label'       => __( 'Monospaced', 'wp-foft-loader' ),
					'description' => __( 'Optimize monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
					'type'        => 'alnumdash',
					'default'     => null,
					'placeholder' => 'e.g., couriernew',
				),
			),
		);

		$settings['base64'] = array(
			'title'       => __( 'Base64', 'wp-foft-loader' ),
			'description' => '
<p>' . __( 'This setting inlines Base64 encoded font in the document head to improve font loading speeds.', 'wp-foft-loader' ) . ' <em>' . __( 'This setting works with the Optimize settings in the previous tab.', 'wp-foft-loader' ) . '</em>' . __( 'All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Optimize settings field.', 'wp-foft-loader' ) . '</p>
<p>' . __( 'Fonts must be subsetted and encoded to Base64. To subset and encode your fonts, we recommend you use', 'wp-foft-loader' ) . ' <a href="https://www.fontsquirrel.com/tools/webfont-generator" target="_blank" rel="external noreferrer noopener">Font Squirrel’s ' . __( 'Webfont Generator', 'wp-foft-loader' ) . '</a>. ' . __( 'Recommended Font Squirrel settings are:', 'wp-foft-loader' ) . '</p>
<dl class="col-3">
  <dt>' . __( 'Font Formats', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
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
  <dd>&ldquo;' . __( 'Custom Subsetting', 'wp-foft-loader' ) . '&rdquo; ' . __( 'with the Unicode Ranges', 'wp-foft-loader' ) . ' 0030-0039,0041-005A,0061-007A</dd>
  <dd>' . __( 'Leave everything else unchecked or blank', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'OpenType Features', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
  <dt>' . __( 'OpenType Flattening', 'wp-foft-loader' ) . '</dt>
  <dd>' . __( 'None', 'wp-foft-loader' ) . '</dd>
  <dt><abbr>CSS</abbr></dt>
  <dd>&ldquo;' . __( 'Base64 Encode', 'wp-foft-loader' ) . '&rdquo;</dd>
  <dt>' . __( 'Advanced Options', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Font Name Suffix', 'wp-foft-loader' ) . '&rdquo; = (leave blank)</dd>
  <dd>&ldquo;' . __( 'Em Square Value', 'wp-foft-loader' ) . '&rdquo; = 2048</dd>
  <dd>&ldquo;' . __( 'Adjust Glyph Spacing', 'wp-foft-loader' ) . '&rdquo; = 0</dd>
  <dt>' . __( 'Shortcuts', 'wp-foft-loader' ) . '</dt>
  <dd>&ldquo;' . __( 'Remember My Settings', 'wp-foft-loader' ) . '&rdquo;</dd>
</dl>
<hr>
<p>' . __( 'The generator will produce a file that looks something like this:', 'wp-foft-loader' ) . '</p>
<pre><code>@font-face{
  font-family: Merriweather;
  src: url(data:application/font-woff; charset=utf-8; base64, d09GRgABAAAAAB4UABAAAAAAMpAAA…) format("woff");
 }</code></pre>
<p>' . __( 'Copy and paste the part the part between', 'wp-foft-loader' ) . ' <pre><code>src:url (data:application/font-woff; charset=utf-8; base64, </code></pre> ' . __( 'and', 'wp-foft-loader' ) . ' <pre><code>) format("woff");</code></pre> ' . __( 'into the appropriate field below. In this example that would be', 'wp-foft-loader' ) . ' <code>d09GRgABAAAAAB4UABAAAAAAMpAAA…</code>.</p>',
			'wp-foft-loader',
			'fields'      => array(
				array(
					'id'          => 'b64-heading',
					'label'       => __( 'Headings', 'wp-foft-loader' ),
					'description' => __( 'The display font for high-level headings', 'wp-foft-loader' ) . '(H1, H2, &amp; H3)',
					'type'        => 'textarea',
					'default'     => '',
				),
				array(
					'id'          => 'b64-body',
					'label'       => __( 'Body', 'wp-foft-loader' ),
					'description' => __( 'The body text. This can be a serif or sans-serif font.', 'wp-foft-loader' ),
					'type'        => 'textarea',
					'default'     => '',
				),
				array(
					'id'          => 'b64-alt',
					'label'       => __( 'Other elements', 'wp-foft-loader' ),
					'description' => __( 'Non-body elements,', 'wp-foft-loader' ) . '<abbr>e.g.</abbr>, ' . __( 'navigation labels, button labels, <abbr>etc.</abbr> A sans-serif font works best for this.', 'wp-foft-loader' ),
					'type'        => 'textarea',
					'default'     => '',
				),
				array(
					'id'          => 'b64-mono',
					'label'       => __( 'Monospaced', 'wp-foft-loader' ),
					'description' => __( 'Monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
					'type'        => 'textarea',
					'default'     => '',
				),
			),
		);

		$settings['css'] = array(
			'title'       => __( 'CSS', 'wp-foft-loader' ),
			'description' => '<p>' . __( '@import rules are automatically handled by this plugin. You may manually inline your font-related', 'wp-foft-loader' ) . ' <abbr>CSS</abbr> ' . __( 'in the document', 'wp-foft-loader' ) . ' <code>&lt;head&gt;</code> ' . __( 'here. Place rules pertaining only to the', 'wp-foft-loader' ) . ' <code>font-family</code>, <code>font-weight</code>, <code>font-style</code>, ' . __( 'and', 'wp-foft-loader' ) . ' <code>font-variation</code> ' . __( 'properties here.', 'wp-foft-loader' ) . '</p>
<section>
	<h3>' . __( 'Stage 1 <abbr>CSS</abbr>', 'wp-foft-loader' ) . '</h3>
	<p>' . __( 'Declarations placed in this field will load the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=base64">' . __( 'Base64 subset', 'wp-foft-loader' ) . '</a> ' . __( 'as a placeholder while the external fonts load.', 'wp-foftloader' ) . '</p>
	<ul class="wpfl">
		<li>' . __( 'Use only the family name followed by', 'wp-foft-loader' ) . ' <code>Subset</code>' . __( '(<em>case-insensitive</em>)', 'wp-foft-loader' ) . '</li>
		<li>' . __( 'Family names must match the names you input on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=optimize">&ldquo;' . __( 'Optimize', 'wp-foft-loader' ) . '&rdquo; ' . __( 'screen.', 'wp-foft-loader' ) . '</a></li>
		<li>' . __( 'Omit weights and styles from the font name', 'wp-foft-loader' ) . '</li>
		<li>' . __( 'All declarations must start with the', 'wp-foft-loader' ) . '  <code>fonts-stage-1</code> ' . __( 'class', 'wp-foft-loader' ) . '</li>
	</ul>
	<p>' . __( 'See the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=documentation">' . __( 'Documentation screen', 'wp-foft-loader' ) . '</a> ' . __( 'to view the Stage 1 <abbr>CSS</abbr> that this plugin loads by default.', 'wp-foft-loader' ) . '</p>
	<dl class="col-2">
	  <dt>' . __( 'Incorrect:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.nav-primary { <mark>// ' . __( 'Missing class:', 'wp-foft-loader' ) . ' .fonts-stage-1</mark>
  font-family: latoSubset, sans-serif;
}

.fonts-stage-1 #footer-primary {
  font-family: lato-boldSubset, san-serif; <mark>// ' . __( 'Don&rsquo;t include the weight or style', 'wp-foft-loader' ) . '</mark>
}

.fonts-stage-1 #footer-secondary {
  font-family: lato, san-serif; <mark>// ' . __( 'Missing', 'wp-foft-loader' ) . ' &ldquo;' . __( 'Subset', 'wp-foft-loader' ) . '&rdquo; ' . __( 'suffix', 'wp-foftloader' ) . '</mark>
}

.fonts-stage-1 div.callout {
  font-family: latoSubset, san-serif;
  font-size: 1rem; <mark>// ' . __( '&ldquo;font-family,&rdquo; &ldquo;font-weight,&rdquo; &ldquo;font-style,&rdquo;', 'wp-foft-loader' ) . '</mark>
                   <mark>// ' . __( 'and &ldquo;font-variant&rdquo; rules only', 'wp-foft-loader' ) . '</mark>
}</code></pre></dd>
		<dt>' . __( 'Correct:', 'wp-foft-loader' ) . '</dt>
		<dd><pre><code>.fonts-stage-1 .nav-primary {
  font-family: latoSubset, sans-serif;
}

.fonts-stage-1 dl.glossary {
  font-family: latosubset, san-serif; <mark>// ' . __( 'Suffix is case-insensitive', 'wp-foft-loader' ) . '</mark>
}</code></pre>
		</dd>
	</dl>
	<p>
</section>
<section>
	<h3>' . __( 'Stage 2 <abbr>CSS</abbr>', 'wp-foft-loader' ) . '</h3>
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
</section>',
			'fields'      => array(
				array(
					'id'          => 'default_css',
					'label'       => __( 'Plugin CSS', 'wp-foft-loader' ),
					'description' => __( 'The plugin loads some <abbr>CSS</abbr> by default.', 'wp-foft-loader' ),
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
					'description' => __( 'Place <abbr>CSS</abbr> font declarations here.', 'wp-foft-loader' ),
					'type'        => 'textarea',
					'default'     => null,
					'placeholder' => __( 'Example:', 'wp-foft-loader' ) .
					'.fonts-stage-1 body {
  font-family: merriweatherSubset, serif;
}',
				),
				array(
					'id'          => 'stage_2',
					'label'       => __( 'Stage 2 CSS', 'wp-foft-loader' ),
					'description' => __( 'Place <abbr>CSS</abbr> font declarations here.', 'wp-foft-loader' ),
					'type'        => 'textarea_large',
					'default'     => null,
					'placeholder' => __( 'Example:', 'wp-foft-loader' ) .
					'.fonts-stage-2 body {
  font-family: merriweather, "Century Schoolbook L", Georgia, serif;
}
.fonts-stage-2 strong {
  font-weight: 700;
}',
				),
			),
		);

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
					'default'     => '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
					'placeholder' => '',

				),
				array(
					'id'          => 'fstack-mono',
					'label'       => __( 'Monospaced', 'wp-foft-loader' ),
					'description' => __( 'Font stack for monospaced fonts. Used for code examples, preformatted text, and tabular data.', 'wp-foft-loader' ),
					'type'        => 'textarea',
					'default'     => 'Consolas,"Andale Mono WT","Andale Mono","Lucida Console","Lucida Sans Typewriter","DejaVu Sans Mono","Bitstream Vera Sans Mono","Liberation Mono","Nimbus Mono L",Monaco,"Courier New",Courier,monospace',
					'placeholder' => '',

				),
			),
		);

		$settings['documentation'] = array(
			'title'       => __( 'Documentation', 'wp-foft-loader' ),
			'description' => '<section>
	<h3>' . __( 'Fonts Stage 1', 'wp-foft-loader' ) . '</h3>
	<p>' . __( 'This plugin always loads the following Stage 1 styles. The Stage 1 fonts are inlined, subsetted, base64 fonts. &lt;$bodySubset&gt;, &lt;$altSubset&gt;, &lt;$headingSubset&gt;, and &lt;$monoSubset&gt; correspond to the Body, Other Elements, Headings, and Monospaced font-families configured on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=optimize">' . __( 'Optimize', 'wp-foft-loader' ) . '</a> ' . __( 'and', 'wp-foft-loader' ) . '  <a href="?page=wp_foft_loader_settings&tab=base64">' . __( 'Base64', 'wp-foft-loader' ) . '</a> options screens.</p>
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
	<p>' . __( 'This plugin also loads the following Stage 2 styles. You can disable these styles on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=css"><abbr>CSS</abbr> ' . __( 'options screen', 'wp-foft-loader' ) . '</a>. <$body>, <$alt>, <$heading>, ' . __( 'and', 'wp-foft-loader' ) . ' <$mono> ' . __( 'correspond to the Body, Other Elements, Headings, and Monospaced font-families configured on the', 'wp-foft-loader' ) . ' <a href="?page=wp_foft_loader_settings&tab=optimize">' . __( 'Optimize screen', 'wp-foft-loader' ) . '</a>. ' . __( 'You can change the default font fallbacks on the', 'wp-foftloader' ) . ' <a href="?page=wp_foft_loader_settings&tab=fstack">' . __( 'Font Stack settings screen', 'wp-foft-loader' ) . '</a>.</p>
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
  font-weight: 900
}

.fonts-stage-2 em strong h1,
.fonts-stage-2 h1 em strong,
.fonts-stage-2 h1 strong em,
.fonts-stage-2 strong em h1 {
  font-weight: 900;
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
  font-family: $sans, Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace
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
  font-family: $alt, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
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
			'fields'      => array(
				array(
					'id'          => 'documentation',
					'label'       => null,
					'description' => null,
					'type'        => 'hidden',
					'default'     => null,
					'placeholder' => null,
				),
			),
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
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
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
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
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

		$html         = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . chr( 0x0D ) . chr( 0x0A );
		echo $html; // phpcs:ignore

	}

	/**
	 * Load settings page content
	 *
	 * @return void
	 */
	public function settings_page() {

		$html         = '<div class="wrap" id="' . $this->parent->token . '_settings">' . chr( 0x0D ) . chr( 0x0A ) . '<h2><span class="wp-admin-lite-blue"><i class="fa fa-3x fa-font" aria-hidden="true"></i><i class="fa fa-2x fa-font" aria-hidden="true"></i><i class="fa fa-font" aria-hidden="true"></i></span> ' . __( 'WP <abbr>FOFT</abbr> Loader Settings', 'wp-foft-loader' ) . '</h2>' . chr( 0x0D ) . chr( 0x0A ) . '<p>' . __( 'Automates <a href="https://www.zachleat.com/web/comprehensive-webfonts/#critical-foft-data-uri" rel="external noreferrer noopener"><strong>Critical <abbr title="Flash of Faux Text">FOFT</abbr> with Data <abbr title="Uniform Resourse Identifier">URI</abbr></a></strong> to speed up font loading while eliminating Flash of Unstyled Text (<abbr>FOUT</abbr>). Based on the work of <span class="h-card"><a class="p-name u-url" href="https://www.zachleat.com/">Zach Leatherman</a></span>. ' ) . '</p>' . chr( 0x0D ) . chr( 0x0A ) . '<p>' . __( 'Please <strong>save your changes</strong> before navigating to the next tab. ', 'wp-foft-loader' ) . '</p>' . chr( 0x0D ) . chr( 0x0A );

		$tab = '';

		 // phpcs:disable
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}
		// phpcs:enable

			// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . chr( 0x0D ) . chr( 0x0A );

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				// phpcs:disable
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) {
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
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				// phpcs:disable
				if ( isset( $_GET['settings-updated'] ) ) {
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}
				// phpcs:enable

				// Output tab.
				$html .= '<a href="' . esc_attr( $tab_link ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . chr( 0x0D ) . chr( 0x0A );

				++$c;
			}

			$html .= '</h2>' . chr( 0x0D ) . chr( 0x0A );
		}

		// settings_errors();
		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . chr( 0x0D ) . chr( 0x0A );

		// Get settings fields.
		ob_start();
		settings_fields( $this->parent->token . '_settings' );
		do_settings_sections( $this->parent->token . '_settings' );
		$html .= ob_get_clean();

		$html     .= '<p class="submit">' . chr( 0x0D ) . chr( 0x0A );
		$html     .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . chr( 0x0D ) . chr( 0x0A );
		$html     .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'wp-foft-loader' ) ) . '" />' . chr( 0x0D ) . chr( 0x0A );
		$html     .= '</p>' . chr( 0x0D ) . chr( 0x0A );
		$html     .= '</form>' . chr( 0x0D ) . chr( 0x0A );
		$html     .= '</div>' . chr( 0x0D ) . chr( 0x0A );

		echo $html; // phpcs:ignore

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
			self::$instance = new self( $parent );
		}
		return self::$instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of WP_FOFT_Loader_API is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of WP_FOFT_Loader_API is forbidden.', 'wp-foft-loader' ), esc_attr( $this->parent->version ) );
	} // End __wakeup()

}
