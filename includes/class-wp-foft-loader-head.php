<?php
/*-------------------------------------------------*/
// SECURITY
/*-------------------------------------------------*/
	if ( !defined( 'ABSPATH' ) ) {
		die( "Sorry, you are not allowed to access this page directly." );
	}

	/**
	* Enqueue custom fonts

	Place font declaration and script in head -- using inline embed for critical font load with data URI per https://www.zachleat.com/web/comprehensive-webfonts/

	*/

	class WP_FOFT_Loader_Head {

		/**
		 * The single instance of WP_FOFT_Loader_Head.
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

		function fontload() {
		// Locate font files

			$uploads = wp_get_upload_dir();
			$font_path = $uploads['baseurl'] . '/fonts/';
			$font_dir = $uploads['basedir'] . '/fonts/';

			$files = glob($font_dir . '*.woff', GLOB_BRACE);

			// Preload the body font; inline subsets as base64

			$arr = array(); // Use this with wp_kses. Don't allow any HTML.


			$heading   = get_option( 'wpfl_s1-heading' );
			$body      = get_option( 'wpt_s1-body' );
			$alt       = get_option( 'wpt_s1-alt' );
			$mono      = get_option( 'wpt_s1-mono' );

			$heading64 = get_option( 'wpfl_b64-heading' );
			$body64    = get_option( 'wpfl_b64-body' );
			$alt64     = get_option( 'wpfl_b64-alt' );
			$mono64    = get_option( 'wpfl_b64-mono' );

			$fdisplay  = get_option ( 'wpfl_font_display' );

			if( !is_null($body) || !is_null($body64) ) { // Styles start
				echo '<link rel="preload" href="' . wp_kses($font_path, $arr) . wp_kses($body, $arr) . '-regular-webfont.woff2" as="font" type="font/woff2" crossorigin><style type="text/css">@font-face{font-family:' . wp_kses($body, $arr) . 'Subset;src:url(data:application/font-woff;charset=utf-8;base64,' . wp_kses($body64, $arr) . ')format("woff");' . 'font-display:' . wp_kses($fdisplay, $arr) . ';font-weight:400;font-style:normal;unicode-range:U+0030-0039,U+0041-005A,U+0061-007A}';
			};

			if( !is_null($heading) || !is_null($heading64) ) {
				echo '@font-face{font-family:' . wp_kses($heading, $arr) . 'Subset;src:url(data:application/font-woff;charset=utf-8;base64,' . wp_kses($heading64, $arr) . ')format("woff");' . 'font-display:' . wp_kses($fdisplay, $arr) . ';font-weight:400;font-style:normal;unicode-range:u+0026,U+0030-0039,U+0041-005A,U+0061-007A}';

			};

			if( !is_null($alt) || !is_null($alt64) ) {
				echo '@font-face{font-family:' . wp_kses($alt, $arr) . 'Subset;src:url(data:application/font-woff;charset=utf-8;base64,' . wp_kses($alt64, $arr) . ')format("woff");' . 'font-display:' . wp_kses($fdisplay, $arr) . ';font-weight:400;font-style:normal;unicode-range:U+0030-0039,U+0041-005A,U+0061-007A}';

			};

			if( !is_null($mono) || !is_null($mono64) ) {
				echo '@font-face{font-family:' . wp_kses($mono, $arr) . 'Subset;src: url(data:application/font-woff;charset=utf-8;base64,' . wp_kses($mono64, $arr) . ')format("woff");' . 'font-display:' . wp_kses($fdisplay, $arr) . ';font-weight:400;font-style:normal;unicode-range:U+0030-0039,U+0041-005A,U+0061-007A}';
			};

			$suffix = '-webfont';
			$fam = array();
			foreach($files as &$file) {

				$font = basename($file, '.woff'); // remove the file type
				$font = str_replace($suffix, '', $font); // remove the -webfont suffix
				list($family, $style) = explode('-', $font, 2); // explode for 2 parts: family and style

				echo '@font-face{font-family:\'' . wp_kses($family, $arr) . '\';src:url(' . esc_url(( $font_path).basename($file)) . '2)format(\'woff2\'),url(' . esc_url(( $font_path) . basename($file)) . ')format(\'woff\');';
				if (in_array($style, ['normal', 'regular'], true)) {
				// Third parameter enables strict type checking -- see
				//https://php.net/manual/en/function.in-array.php
				  echo 'font-weight:400;font-style:normal;';
				} elseif (in_array($style, ['thinItalic', 'hairlineItalic'], true)) {
				  echo 'font-weight:100;font-style:italic;';
				} elseif (in_array($style, ['extraLightItalic', 'ultraLightItalic'], true)) {
				  echo 'font-weight:200;font-style:italic;';
				} elseif (in_array($style, ['lightItalic'], true)) {
				  echo 'font-weight:300;font-style:italic;';
				} elseif (in_array($style, ['mediumItalic'], true)) {
				  echo 'font-weight:500;font-style:italic;';
				} elseif (in_array($style, ['semiBoldItalic', 'demiBoldItalic'], true)) {
				  echo 'font-weight:600;font-style:italic;';
				} elseif (in_array($style, ['extraBoldItalic', 'ultraBoldItalic'], true)) {
				  echo 'font-weight:800;font-style:italic;';
				} elseif (in_array($style, ['boldItalic'], true)) {
				  echo 'font-weight:700;font-style:italic;';
				} elseif (in_array($style, ['blackItalic', 'heavyItalic'], true)) {
				  echo 'font-weight:900;font-style:italic;';
				} elseif (in_array($style, ['thin', 'hairline'], true)) {
				  echo 'font-weight:100;font-style:normal;';
				} elseif (in_array($style, ['extraLight', 'ultraLight'], true)) {
				  echo 'font-weight:200;font-style:normal;';
				} elseif (in_array($style, ['light'], true)) {
				  echo 'font-weight:300;font-style:normal;';
				} elseif (in_array($style, ['medium'], true)) {
				  echo 'font-weight:500;font-style:normal;';
				} elseif (in_array($style, ['semiBold', 'demiBold'], true)) {
				  echo 'font-weight:600;font-style:normal;';
				} elseif (in_array($style, ['extraBold', 'ultraBold'], true)) {
				  echo 'font-weight:800;font-style:normal;';
				} elseif (in_array($style, ['black', 'heavy'], true)) {
				  echo 'font-weight:900;font-style:normal;';
				} elseif (in_array($style, ['italic'], true)) {
				  echo 'font-weight:400;font-style:italic;';
				} elseif (in_array($style, ['bold'], true)) {
				  echo 'font-weight:700;font-style:normal;';
				} else {
				  echo 'font-weight:400;font-style:normal;';
				}

				// Small caps
				$fam = array($family);
				if ($smallcap = preg_grep('/.+SC$/D', $fam)) { // 1 or more character followed by "SC" at end of string; case-sensitive
				echo 'font-variant:small-caps;';
				}

				echo 'font-display:' . wp_kses($fdisplay, $arr) . ';}';

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

			$fs_heading = get_option ( 'wpfl_fstack-heading' );
			$fs_heading = ',' . $fs_heading;
			$fs_body = get_option ( 'wpfl_fstack-body' );
			$fs_body = ',' . $fs_body;
			$fs_alt = get_option ( 'wpfl_fstack-alt' );
			$fs_alt = ',' . $fs_alt;
			$fs_mono = get_option ( 'wpfl_fstack-mono' );
			$fs_mono = ',' . $fs_mono;

			echo 'body{font-family:serif;font-weight:400;font-style:normal}';
						
			$default_css = get_option ( 'wpfl_default_css' );

			if( !is_null($body) && !is_null($fs_body) ) {
				echo '.fonts-stage-1 body{font-family:' . wp_kses($body, $arr) . 'Subset,serif}';
			}

			if( !is_null($alt) && !is_null($fs_alt) ) {
				echo '.fonts-stage-1 button,.fonts-stage-1 input,.fonts-stage-1 nav,.fonts-stage-1 optgroup,.fonts-stage-1 select,.fonts-stage-1 textarea{font-family:' . wp_kses($alt, $arr) . 'Subset,sans-serif}';
			}

			if( !is_null($heading) && !is_null($fs_heading) ) {
				echo '.fonts-stage-1 h1,.fonts-stage-1 h2,.fonts-stage-1 h3,.fonts-stage-1 h4,.fonts-stage-1 h5,.fonts-stage-1 h6{font-family:' . wp_kses($heading, $arr) . 'Subset,serif}';
			}

			if ( !is_null($mono) && !is_null($fs_mono) ) {
				echo '.fonts-stage-1 code{font-family:' . wp_kses($mono, $arr) . 'Subset,monospace}';
			}

			if (isset($default_css)) {
				if ($default_css == 'on') {
					if( !is_null($body) ) {
						echo '.fonts-stage-2 body,.fonts-stage-2 h4,.fonts-stage-2 h5,.fonts-stage-2 h6{font-family:' . wp_kses($body, $arr) . wp_kses($fs_body, $arr) . '}';
					}

					if( !is_null($heading) ) {
						echo '.fonts-stage-2 h1,.fonts-stage-2 h2,.fonts-stage-2 h3{font-family:' . wp_kses($heading, $arr) . wp_kses($fs_heading, $arr) . ';font-weight:400}';
					}

					
					echo '.fonts-stage-2 code strong,.fonts-stage-2 h4,.fonts-stage-2 h5,.fonts-stage-2 h6,.fonts-stage-2 strong,.fonts-stage-2 strong code{font-weight:700}.fonts-stage-2 h1 strong,.fonts-stage-2 h2 strong,.fonts-stage-2 h3 strong,.fonts-stage-2 strong h1,.fonts-stage-2 strong h2,.fonts-stage-2 strong h3{font-weight:900}.fonts-stage-2 em strong h1,.fonts-stage-2 h1 em strong,.fonts-stage-2 h1 strong em,.fonts-stage-2 strong em h1{font-weight:900;font-style:italic}.fonts-stage-2 abbr{font-weight:700;font-variant:small-caps;padding:0 .13333rem 0 0;letter-spacing:.06667rem;text-transform:lowercase}';

					if( !is_null($mono) ) {
						echo '.fonts-stage-2 code{font-family:' . wp_kses($mono, $arr) . wp_kses($fs_mono, $arr) . '}';
					}

					echo '.fonts-stage-2 cite>em,.fonts-stage-2 cite>q,.fonts-stage-2 em>cite,.fonts-stage-2 em>em,.fonts-stage-2 em>q,.fonts-stage-2 figcaption>cite,.fonts-stage-2 figcaption>em,.fonts-stage-2 q>cite,.fonts-stage-2 q>em{font-style:normal}.fonts-stage-2 code em,.fonts-stage-2 em,.fonts-stage-2 em code,.fonts-stage-2 figcaption,.fonts-stage-2 h2,.fonts-stage-2 h3{font-style:italic}.fonts-stage-2 code em strong,.fonts-stage-2 code strong em,.fonts-stage-2 em code strong,.fonts-stage-2 em strong,.fonts-stage-2 em strong code,.fonts-stage-2 strong code em,.fonts-stage-2 strong em,.fonts-stage-2 strong em code{font-weight:700;font-style:italic}';
					
					if ( !is_null($alt) ) {
						echo '.fonts-stage-2 button,.fonts-stage-2 input,.fonts-stage-2 nav,.fonts-stage-2 optgroup,.fonts-stage-2 select,.fonts-stage-2 textarea{font-family:' . wp_kses($alt, $arr) . wp_kses($fs_alt, $arr) . ';font-weight:400}';
					}
				}
			};

			// User input custom CSS. Sanitize with HTMLPurifier + CSSTidy
			$css_dirty_1 = get_option ( 'wpfl_stage_1' );
			$css_dirty_2 = get_option ( 'wpfl_stage_2' );

			// Create a new configuration object
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Filter.ExtractStyleBlocks', TRUE);

			// Create a new purifier instance
			$purifier = new HTMLPurifier($config);

			// Wrap our CSS in style tags and pass to purifier. 
			// we're not actually interested in the html response though
			$html = $purifier->purify('<style>' . $css_dirty_1 . $css_dirty_2 . '</style>');

			// The "style" blocks are stored seperately
			$clean_css = $purifier->context->get('StyleBlocks');

			// Minification utility
			function compress($string)
			{
				// Remove html comments
				// $string = preg_replace('/<!--.*-->/', '', $string);

				// Merge multiple spaces into one space
				$string = preg_replace('/\s+/', ' ', $string);

				// Remove final semicolon & whitespace
				$string = preg_replace('/;\s+}/', '}', $string);

				// Trim whitespace before opening curly brace
				$string = preg_replace('/\s+{\s+/', '{', $string);

				// Trim whitespaces after commas
				$string = preg_replace('/\,\s+/', ',', $string);

				return  $string;
			}
			
			ob_start('compress'); // Parse DOM Tree.
			// Everything inside gets minified

			// Get the first style block
			echo $clean_css[0] . '</style>'; // Styles end

			ob_end_flush(); // End minification

			echo '<script>(function(){if(sessionStorage.criticalFoftDataUriFontsLoaded ){document.documentElement.className+=" fonts-stage-1 fonts-stage-2";return;}(function(){function e(e,t){document.addEventListener?e.addEventListener("scroll",t,!1):e.attachEvent("scroll",t)}function t(e){document.body?e():document.addEventListener?document.addEventListener("DOMContentLoaded",function t(){document.removeEventListener("DOMContentLoaded",t),e()}):document.attachEvent("onreadystatechange",function n(){if("interactive"==document.readyState||"complete"==document.readyState)document.detachEvent("onreadystatechange",n),e()})}function n(e){this.a=document.createElement("div"),this.a.setAttribute("aria-hidden","true"),this.a.appendChild(document.createTextNode(e)),this.b=document.createElement("span"),this.c=document.createElement("span"),this.h=document.createElement("span"),this.f=document.createElement("span"),this.g=-1,this.b.style.cssText="max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;",this.c.style.cssText="max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;",this.f.style.cssText="max-width:none;display:inline-block;position:absolute;height:100%;width:100%;overflow:scroll;font-size:16px;",this.h.style.cssText="display:inline-block;width:200%;height:200%;font-size:16px;max-width:none;",this.b.appendChild(this.h),this.c.appendChild(this.f),this.a.appendChild(this.b),this.a.appendChild(this.c)}function r(e,t){e.a.style.cssText="max-width:none;min-width:20px;min-height:20px;display:inline-block;overflow:hidden;position:absolute;width:auto;margin:0;padding:0;top:-999px;left:-999px;white-space:nowrap;font:"+t+";"}function i(e){var t=e.a.offsetWidth,n=t+100;return e.f.style.width=n+"px",e.c.scrollLeft=n,e.b.scrollLeft=e.b.scrollWidth+100,e.g!==t?(e.g=t,!0):!1}function s(t,n){function r(){var e=s;i(e)&&null!==e.a.parentNode&&n(e.g)}var s=t;e(t.b,r),e(t.c,r),i(t)}function o(e,t){var n=t||{};this.family=e,this.style=n.style||"normal",this.weight=n.weight||"normal",this.stretch=n.stretch||"normal"}function l(){if(null===a){var e=document.createElement("div");try{e.style.font="condensed 100px sans-serif"}catch(t){}a=""!==e.style.font}return a}function c(e,t){return[e.style,e.weight,l()?e.stretch:"","100px",t].join(" ")}var u=null,a=null,f=null;o.prototype.load=function(e,i){var o=this,a=e||"BESbswy",l=i||3e3,h=(new Date).getTime();return new Promise(function(e,i){null===f&&(f=!!window.FontFace);if(f){var p=new Promise(function(e,t){function n(){(new Date).getTime()-h>=l?t():document.fonts.load(c(o,o.family),a).then(function(t){1<=t.length?e():setTimeout(n,25)},function(){t()})}n()}),d=new Promise(function(e,t){setTimeout(t,l)});Promise.race([d,p]).then(function(){e(o)},function(){i(o)})}else t(function(){function t(){var t;if(t=-1!=m&&-1!=g||-1!=m&&-1!=S||-1!=g&&-1!=S)(t=m!=g&&m!=S&&g!=S)||(null===u&&(t=/AppleWebKit\/([0-9]+)(?:\.([0-9]+))/.exec(window.navigator.userAgent),u=!!t&&(536>parseInt(t[1],10)||536===parseInt(t[1],10)&&11>=parseInt(t[2],10))),t=u&&(m==x&&g==x&&S==x||m==T&&g==T&&S==T||m==N&&g==N&&S==N)),t=!t;t&&(null!==C.parentNode&&C.parentNode.removeChild(C),clearTimeout(L),e(o))}function f(){if((new Date).getTime()-h>=l)null!==C.parentNode&&C.parentNode.removeChild(C),i(o);else{var e=document.hidden;if(!0===e||void 0===e)m=p.a.offsetWidth,g=d.a.offsetWidth,S=v.a.offsetWidth,t();L=setTimeout(f,50)}}var p=new n(a),d=new n(a),v=new n(a),m=-1,g=-1,S=-1,x=-1,T=-1,N=-1,C=document.createElement("div"),L=0;C.dir="ltr",r(p,c(o,"sans-serif")),r(d,c(o,"serif")),r(v,c(o,"monospace")),C.appendChild(p.a),C.appendChild(d.a),C.appendChild(v.a),document.body.appendChild(C),x=p.a.offsetWidth,T=d.a.offsetWidth,N=v.a.offsetWidth,f(),s(p,function(e){m=e,t()}),r(p,c(o,\'"\'+o.family+\'",sans-serif\' )),s(d,function(e){g=e,t()}),r(d,c(o,\'"\'+o.family+\'",serif\' )),s(v,function(e){S=e,t()}),r(v,c(o,\'"\'+o.family+\'",monospace\' ))})})},"undefined"!=typeof module?module.exports=o:(window.FontFaceObserver=o,window.FontFaceObserver.prototype.load=o.prototype.load)})();var fontASubset=new FontFaceObserver(\'';

			$fontoptions = array(
			get_option( 'wpfl_s1-heading' ),
			get_option( 'wpfl_s1-body' ),
			get_option( 'wpfl_s1-alt' ),
			get_option( 'wpfl_s1-mono' )
			);

			$subsets = array_filter($fontoptions);

			ob_start(); // buffer foreach output
			foreach ($subsets as &$subset) {
				$subset .= 'Subset, ';
				echo wp_kses($subset, $arr);
			}

			$subsetstrim = ob_get_clean(); // get buffer
			echo rtrim(wp_kses($subsetstrim,$arr), ', '); // trim final comma & space

			echo '\');Promise.all([fontASubset.load()]).then(function (){document.documentElement.className += " fonts-stage-1";';

			$observer = 'A';
			$observer2 = 'A';

			$fam = array();

			foreach ($files as &$file) {

			$font = basename($file, '.woff'); // remove the file type
			$font = str_replace($suffix, '', $font); // remove the -webfont suffix
			$family = explode("-", $font);
			$fam[] = $family[0]; // First needle

			}

			$results = array_unique($fam);
			$result = array($results[0]);
			foreach ($results as &$result) {
				$obs = $observer++;
				$observed = 'var font'. $obs . '=new FontFaceObserver(\'' . $result . '\');';
				echo wp_kses($observed, $arr);
			}

			echo 'Promise.all([';

			ob_start(); // buffer output of foreach
			foreach ($results as &$result) {
				$obs2 = $observer2++;
				$promise = 'font' . $obs2 . '.load(),';
				echo wp_kses($promise, $arr);
			}

			$output = ob_get_clean(); // get buffer
			echo rtrim(wp_kses($output,$arr), ','); // trim final comma & space

			unset ($observer);
			unset ($observer2);
			unset ($file);

			echo ']).then(function () {document.documentElement.className += " fonts-stage-2";sessionStorage.criticalFoftDataUriFontsLoaded=true;});});})();//</script>';

		}

		function foft_head() {
			$this->fontload();
		}

		/**
		 * Main WP_FOFT_Loader_Head Instance
		 *
		 * Ensures only one instance of WP_FOFT_Loader_Head is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see WP_FOFT_Loader()
		 * @return Main WP_FOFT_Loader_Head instance
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

/**
* Place the @font declaration in the header
*
*/

	$head = new WP_FOFT_Loader_Head;

	add_action( 'wp_head', array( $head, 'foft_head' ) );