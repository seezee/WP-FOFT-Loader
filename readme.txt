=== WP FOFT Loader ===  
Contributors: seezee  , freemius
Donate link: https://messengerwebdesign.com/donate  
Author URI: https://github.com/seezee  
Plugin URI: https://wordpress.org/plugins/wp-foft-loader/  
Tags: wordpress, plugin, fonts, webfonts, performance, UX  
Requires at least: 3.9  
Tested up to: 5.3  
Requires PHP: 7.0  
Stable tag: 2.0.23  
License: GNUv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html  

[//]: # (*********************************************************************          **********Short description: 150 characters or fewer; no markup! *****          *********************************************************************)
Optimize and speed up webfont loading and improve UX by minimizing Flash of Invisible Text, Flash of Unstyled Text, and DOM Reflow.

== Description ==

This plugin implements and automates Zach Leatherman’s Critical FOFT with preload, with a polyfill fallback emulating font-display: optional to optimize and speed up webfont loading and improve UX by minimizing Flash of Invisible Text, Flash of Unstyled Text, and DOM Reflow. See [https://github.com/zachleat/web-font-loading-recipes#the-compromise-critical-foft-with-preload-with-a-polyfill-fallback-emulating-font-display-optional](https://github.com/zachleat/web-font-loading-recipes#the-compromise-critical-foft-with-preload-with-a-polyfill-fallback-emulating-font-display-optional).

[![RIPS CodeRisk](https://coderisk.com/wp/plugin/wp-foft-loader/badge "RIPS CodeRisk")](https://coderisk.com/wp/plugin/wp-foft-loader)[![WP compatibility](https://plugintests.com/plugins/wp-foft-loader/wp-badge.svg)](https://plugintests.com/plugins/wp-foft-loader/latest)[![PHP compatibility](https://plugintests.com/plugins/wp-foft-loader/php-badge.svg)](https://plugintests.com/plugins/wp-foft-loader/latest)

== Acknowledgement ==

This plugin is based on [Hugh Lashbrooke’s Starter Plugin](https://github.com/hlashbrooke/WordPress-Plugin-Template), a robust and GPL-licensed code template for creating a standards-compliant WordPress plugin.

== PRO only features ==

* Support for 7 additional font-weights
* Small-caps support
* Finer-grained control of default CSS
* Default options reset
* Warn user of unsaved changes when navigating plugin tabs

== Installation ==

### USING THE WORDPRESS DASHBOARD
1. Navigate to “Add New” in the plugins dashboard
2. Search for “WP FOFT Loader”
3. Click “Install Now”
4. Activate the plugin on the Plugin dashboard
5. Go to Settings -> WP FOFT Loader, upload your fonts, and configure the settings.


### UPLOADING IN WORDPRESS DASHBOARD
1. Click the download button on this and save “wp-foft-loader.zip” to your computer
2. Navigate to “Add New” in the plugins dashboard
3. Navigate to the “Upload” area
4. Select “wp-foft-loader.zip” from your computer
5. Click “Install Now”
6. Activate the plugin in the Plugin dashboard
7. Go to Settings -> WP FOFT Loader, upload your fonts, and configure the settings.

### USING FTP
1. Download the WP FOFT Loader ZIP file
2. Extract the WP FOFT Loader ZIP file to your computer
3. Upload the “wp-foft-loader” directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard
5. Go to Settings -> WP FOFT Loader, upload your fonts, and configure the settings.

### UPGRADING TO WP FOFT LOADER PRO
1. Go to Settings -> WP FOFT Loader -> Upgrade
2. Fill out the payment form and submit
3. Your license key will automatically be entered

### DOWNLOAD FROM GITHUB
1. Download the plugin via https://github.com/seezee/WP-FOFT-Loader
2. Follow the directions for using <abbr>FTP</abbr>

== Configuration ==

= Video Tutorials =

[Episode 1. Intro and Background](https://youtu.be/0C0lDJ3T12o)  

[Episode 2. Font Squirrel Generator (WOFF & WOFF2)](https://youtu.be/-StFYcOSDCU)  

= Generating and Uploading the Font Files =

Upload two files for each web font: a WOFF file and a WOFF2 file. We recommend you use [Font Squirrel’s Webfont Generator](https://www.fontsquirrel.com/tools/webfont-generator) to generate the files. Mandatory Font Squirrel settings are:

	Select “Expert”
	Font Formats:		“WOFF”
						“WOFF2”
	Advanced Options:	“Font Name Suffix” = -webfont

For detailed recommended settings, see the plugin Upload options screen.



**Filenames must follow the proper naming convention:** `$family`-`$variant`-webfont.`$filetype`.

**$family**
: The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but no hyphens or spaces.



**$variant**
: The font style. Can be weight, style, or a combination of both. *Case-sensitive*.

**-webfont**
: Mandatory suffix. Append to $variant.

**$filetype**
: The file type, i.e., “woff” or “woff2”.



**Example**: for the bold weight italic style of Times New Roman, rename the files to timesnewroman-boldItalic-webfont.woff and timesnewroman-boldItalic-webfont.woff2.

Allowed weights and styles and their CSS mappings are:


regular | normal (maps to 400)  

italic (maps to 400)  

boldItalic (maps to 700)  


__For small-caps support and extended font-weight support, please upgrade to__ [__WP FOFT Loader PRO__](https://checkout.freemius.com/mode/dialog/plugin/4955/plan/7984/licenses/1/).

This plugin supports 1 – 4 font families. For each font family you upload, specify its name in the appropriate setting below the uploader.

**Correct:**

`playfairdisplay` (all lowercase)  
`playfair_display` (hyphens allowed)  
`PlayfairDisplay` (mixed case allowed)  

**Incorrect:**

`playfairdisplay-bold` (hyphens not allowed; use the family name only; omit the style, i.e., “bold”)  
`playfair-display` (hyphens not allowed)  
`playfair display` (spaces prohibited)  
`Playfair Display` (spaces prohibited)  

= Subset =

Upload up to 4 small, subsetted fonts. For each font, upload a WOFF & WOFF2 file (for a total of up to 8 files). Each font will act as a placeholder until the full fonts load.

**Filenames must follow the proper naming convention:** `$family`-optimized.`$filetype`.

**$family**
: The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but no hyphens or spaces. Each $family base name should match the name used for the matching font uploaded on the previous upload screen.

**-optimized**
: Mandatory suffix. Append to $family.

**$filetype**
: The file type, i.e., “woff” or “woff2”.

**Example**: If you uploaded timesnewroman-regular-webfont.woff and timesnewroman-regular-webfont.woff2 as your body font on the previous screen, name the subsetted versions timesnewroman-optimized.woff and timesnewroman-optimized.woff2 respectively.

To subset and encode your fonts, we recommend you use Font Squirrel’s Webfont Generator. Mandatory Font Squirrel settings are:

	Select “Expert”
	Font Formats:			“WOFF”
							“WOFF2”
	Fix Missing Glyphs:		None
	Subsetting:				“Custom Subsetting” with the Unicode Ranges 0065-0041-005A,0061-007A
							Leave everything else unchecked  
	OpenType Features:		None
	OpenType Flattening:	None
	CSS:					Leave unchecked
	Advanced Options:		“Font Name Suffix” = -optimized

For detailed recommended settings, see the plugin Subset options screen.

= CSS =

@import rules are automatically handled by this plugin. You may manually inline your font-related CSS in the document `<head>` here. Place rules pertaining only to the font-family, font-weight, font-style, and font-variation properties here.

#### Plugin CSS

The plugin loads some CSS by default. You may disable it from this screen.

#### Font Display

The plugin uses `font-display: swap` by default. You can override the [`font-display`](https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display) property here.

#### CSS Stage 1

Declarations placed in this field will load subsetted fonts as placeholders while the full fonts load.

* Use only the family name followed by Subset (case-sensitive)
* Family names must match the names you input on the “Subset” screen.
* All declarations must start with the fonts-stage-1 class

See the Documentation screen to view the Stage 1 CSS that this plugin loads by default.

Incorrect:

	.nav-primary { // Missing class: .fonts-stage-1
	  font-family: latoSubset, sans-serif;
	}

	.fonts-stage-1 #footer-secondary {
	  font-family: lato, san-serif; // Missing “Subset” suffix
	}

	.fonts-stage-1 div.callout {
	  font-family: latoSubset, san-serif;
	  font-size: 1rem; // “font-family,” “font-weight,” “font-style,”
					   // and “font-variant” rules only
	}

	.fonts-stage-1 div.callout {
	  font-family: latosubset, san-serif; // “Subset” suffix is case-sensitive
	}

Correct:

	.fonts-stage-1 .nav-primary {
	  font-family: latoSubset, sans-serif;
	}

#### CSS Stage 2

* Use only the family name
* Family names must match the file names for the fonts you uploaded on the “Upload” screen.
* Omit weights and styles from the font name
* All declarations must start with the fonts-stage-2 class
* For best performance, please minify your CSS before pasting it into the form.

See the Documentation screen to view the Stage 2 CSS that this plugin loads by default.

Incorrect:

	tbody { // Missing class: .fonts-stage-2
	  font-family: lato, Corbel, "Lucida Grande", sans-serif;
	  font-weight: 400;
	  font-style: normal;
	}

	.fonts-stage-2 span.bolder {
	  font-family: lato-bold, Corbel, "Lucida Grande", sans-serif; // Don’t include style in font name.
	  // Better yet, omit declaration altogether.
	  font-weight: 700;
	}

	.fonts-stage-2 div.callout {
	  font-family: lato-regular, Corbel, "Lucida Grande", san-serif;
	  font-size: 1rem; // “font-family,” “font-weight,” “font-style,”
					   // and “font-variant” rules only
	}

Correct:

	.fonts-stage-2 div.callout {
	  font-family: lato, Corbel, "Lucida Grande", sans-serif;
	  font-weight: 400;
	  font-style: normal;
	}

	.fonts-stage-2 div.callout {
	  // No need to redeclare the font-family — all weights map to a single family name
	  font-weight: 700; // This will use the lato-bold font
	}

For best performance, please [minify your CSS](https://cssminifier.com/) before pasting it into the form.

= Font Stacks =

Change the default font fallbacks in case your custom fonts don’t load. Don’t include the names of your default custom fonts here.

= Further Documentation =

See the Documentation screen to view the CSS this plugin loads by default and to view video tutorials.

== Screenshots ==

1. Uploads screen: upload your custom web fonts here
2. Optimize screen: tells fontobserver.js which fonts to load for stage 1
3. Base64 screen: inlines Base64 data URI for subsetted stage 1 fonts
4. CSS screen: all font-related CSS goes here so it can be inlined.
5. Font Stacks screen: sets the default font stacks
6. Documentation (1): Information about the CSS that the plugin loads by default
7. Documentation (2): Video tutorials

== Frequently Asked Questions ==

### What is the plugin for?

This plugin implements and automates [Zach Leatherman’s Critical FOFT with preload, with a polyfill fallback emulating font-display: optional](https://github.com/zachleat/web-font-loading-recipes#the-compromise-critical-foft-with-preload-with-a-polyfill-fallback-emulating-font-display-optional). According to [a tweet from Mr. Leatherman](https://twitter.com/zachleat/status/1187810081175474176), this technique is the best compromise between font speed loading and a positive user experience.

### How may I help improve this plugin?

I’d love to hear your feedback. In particular, tell me about your experience configuring the plugin. Are the instructions clear? Do I need to reword them? Did I leave out something crucial? You get the drift.

### I’d like to do more

I’m looking for collaborators to improve the code. If you are an experienced Wordpress programmer, hit me up!

### I’d like to do even more

Feel free to send a donation to my [Paypal account](https://paypal.me/messengerwebdesign?locale.x=en_US). Or buy me a beer if you’re in town.

== Translations ==

* English: Default language, always included

Would you like to help translate WP FOFT Loader into your own language? [You can do that here!](https://translate.wordpress.org/projects/wp-plugins/wp-foft-loader)

== Dependencies ==

This plugin includes these third-party libraries in its package.

* [HTMLPurifier](https://github.com/ezyang/htmlpurifier): v1.7.1
* [CSSTidy](https://github.com/Cerdic/CSSTidy): v4.12.0

== Changelog ==

= 2.0.23 =
* 2019-12-02
* BUGFIX: fix broken path for fallback.min.js

= 2.0.22 =
* 2019-12-02
* BUGFIX: fix improper Freemius filter in wp-foft-loader.php
* BUGFIX: fix path error that prevents one of the optimized fonts from loading
* Add font-display property to subsetted font declaration

= 2.0.21 =
* 2019-11-29
* BUGFIX: Fix update success notice showing if not updated

= 2.0.20 =
* 2019-11-29
* Fixed missing spaces to upgrade success notice v2.0.17 – v2.0.19

= 2.0.19 =
* 2019-11-29
* BUGFIX: moved wpfl_activation() below wpfl_check_version() so they fire in correct order
* Added get_option() check for FALSE in wpfl_check_version()

= 2.0.19 =
* 2019-11-29
* BUGFIX: corrected version check error

= 2.0.17 =
* 2019-11-29
* Add aria-label to meta links
* Improved URL sanitization with esc_url()
* Some internationalization fixes
* Regenerate .POT file
* Tweaks to ratings microcopy

= 2.0.16 =
* 2019-11-29
* Change settings page slug to wp-foft-loader
* Add class-wp-foft-loader-ratings.php
* Additional capability & pagenow() checks

= 2.0.15 =
* 2019-11-25
* BUGFIX: proper check for whether options are set in class-wp-foft-loader-jsvars.php
* BUGFIX: prevent loading of inline Font Face Observer JS until user uploads at least one font & sets at least one option on main screen

= 2.0.14 =
* 2019-11-25
* BUGFIX: fix undefined variable $promises in class-wp-foft-loader-head.php

= 2.0.13 =
* 2019-11-25
* BUGFIX: change default options from empty to NULL to avoid missing font in Javascript font preload
* Minor admin CSS changes
* Minor revision to descriptive text on settings page
* POT file updated

= 2.0.12 =
* 2019-11-25
* Fix missing fontawesome glyphs in plugins settings page main heading

= 2.0.11 =
* 2019-11-24
* BUGFIX: Fix mixed content error in file upload path

= 2.0.10 =
* 2019-11-24
* BUGFIX: use array_pad() to finally fix undefined offset in class-wp-foft-loader-settings.php

= 2.0.9 =
* 2019-11-24
* BUGFIX: fix more undefined offsets
* BUGFIX: change "$" to "jQuery" in "ays-beforeunload-shim.min.js"

= 2.0.8 =
* 2019-11-23
* Sanitize variables jsObs & jsLoad with wp_json_encode()
* BUGFIX: replace all instances of undefined variable $version with constant _VERSION_
* BUGFIX: fix more undefined variables & offsets
* BUGFIX: fix constant referring to wrong directory path

= 2.0.7 =
* 2019-11-22
* BUGFIXES: Check for existence of variables and arrays & fix undefined offsets

= 2.0.6 =
* 2019-11-19
* Move HTMLPurifier & CSSTidy to vendor directory
* Fix "Buy the Developer a Coffee" link in plugin meta
* Add author & plugin URIs to readme

= 2.0.5 =
* 2019-11-15
* Tested up to WordPress 5.3

= 2.0.4 =
* 2019-11-12
* BUGFIX: fixed improper Freemius "if" statements
* BUGFIX: plugin no longer fails to uninstall
* BUGFIX: plugin now deletes options properly on uninstall
* Added new Admin messages for new installs & updates
* Added _BASE_ & _VERSION_ constants & replaced limited-scope variables
* New PRO feature: finer-grained control over default CSS output
* New PRO feature: ability to restore plugin defaults

= 2.0.3 =
* 2019-11-07
* Improved support for small-caps in PRO version
* Warn on change without saving PRO version only
* Added admin messages after plugin activation or update
* Auto-disactivate FREE plugin when activating PRO version
* Fix README typos & formatting
* Remove out-of-date README info (translators)
* Add version check in wp_options table

= 2.0.2 =
* 2019-11-05
* Moved support for extended font weights and small-caps to PRO plugin
* Integrated FREEMIUS code for PRO plugin
* Eliminated redundant output if user uploads fewer than 4 fonts
* Changed font declarations to auto-populated select
* Moved font declarations to plugin settings tab
* Use template literals in class-wp-foft-loader-jsvars.php
* Ajaxify admin messages
* Prepend random exclamations to admin messages
* Update HTMLPurifier to v4.12.0
* Update CSSTidy to v1.7.1
* Removed out-of-date translation files

= 2.0.1 =
* 2019-10-30
* **IMPORTANT** Versions >= 2.0.0 and up introduce breaking changes from versions <= 1.0.47
* Users upgrading from v1.x.x will need to visit the “Subset” screen and configure subsetted fonts
* Fixed error in class-wp-foft-loader-jsvars

= 2.0.0 =
* 2019-10-30
* **IMPORTANT** This is a major update with breaking changes
* Users upgrading from v1.x.x will need to visit the “Subset” screen and configure subsetted fonts
* Move from “Critical FOFT with Data URI” to “Critical FOFT with preload, with a polyfill fallback emulating font-display”

= 1.0.47 =
* 2019-10-16
* Tested up to WordPress 1.0.46

= 1.0.46 =
* 2019-09-26
* Fix undefined offset `0`
* Fix undefined index `placeholder`

= 1.0.45 =
* 2019-09-26
* Remove undefined variable $parent from class mimes
* Add variable $plugin to class meta

= 1.0.44 =
* 2019-09-25
* Add links to plugin meta

= 1.0.43 =
* 2019-09-25
* Update plugin description in main file

= 1.0.42 =
* 2019-09-25
* Add `samp` tag to default CSS

= 1.0.41 =
* 2019-09-25
* Add `kbd` tag to default CSS

= 1.0.40 =
* 2019-09-12
* Remove unused dev code from HTMLPurifier & CSSTidy

= 1.0.39 =
* 2019-09-08
* Security update: Fix XSS double-quoted attribute flaw in class-wp-foft-loader-settings.php

= 1.0.38 =
* 2019-09-05
* Remove admin script enqueuing since we’re not using it and it throws a 404

= 1.0.37 =
* 2012-08-19
* Added package.json
* Minor updates to readme.txt & readme.md

= 1.0.36 =
* 2019-08.13
* Fixed uploader bug. WOFF & WOFF2 files now permitted & upload to correct folder

= 1.0.35 =
* 2019-07-31
* Update CSSTidy library to v1.7.0

= 1.0.34 =
* 2019-07-29
* Modifed HTMLTidy config in class-wp-foft-loader-head.php

= 1.0.33 =
* 2019-07-07
* Updated .pot file
* Replaced incorrect Dutch .po & .mo

= 1.0.32 =
* 2019-07-05
* Bugfix in class-wp-foft-loader-settings.php
* Better installation instructions

= 1.0.31 =
* 2019-07-04
* More sanitization
* Pass PHPCS/WPCS checks
* Remove unused metabox code

= 1.0.30 =
* 2019-06-27
* Fixed incorrect Dutch .po filenames

= 1.0.29 =
* 2019-06-28
* More sanitizing
* Changes to README content
* Corrected some errors in Dutch translation

= 1.0.28 =
* 2019-06-27
* Create correct icon sizes for WP Plugin Repository
* Fix readme.txt formatting
* Changes to README content

= 1.0.27 =
* 2019-06-27
* Code formatting now meets Wordpress standards
* Strict type checking on comparisons
* Use Yoda case where appropriate
* Convert unnecessary concatenations to single strings
* Added Dutch translation

= 1.0.26 =
* 2019-06-25
* Minor bugfix (fixed typo in code)

= 1.0.25 =
* 2019-06-21
* Additional NULL value checks before outputting styles
* Other code improvements
* Numerous documentation improvements
* Added first two video tutorials (more to come)

= 1.0.24 =
* 2019-06-19
* Bugfixes: fixed typos in output CSS
* Separated Stage 1 and Stage 2 CSS in CSS screen
* Added Documentation screen
* Major edits to body copy
* Removed internationalization where it’s not needed

= 1.0.23 =
* 2019-06-18
* Use HTMLPurifier & CSSTidy to sanitize user input custom CSS

= 1.0.22 =
* 2019-06-17
* Improve introductory microcopy on Settings page
* Bugfix: fixed incorrect textdomain

= 1.0.21 =
* 2019-06-17
* Update custom CSS output in /includes/class-wp-foft-loader-head.php (convert `&lt;` back to `>` child selector after sanitizing)
* Update default CSS output in /includes/class-wp-foft-loader-head.php (don’t escape `>`)

= 1.0.20 =
* 2019-06-16
* Add font-display option to CSS Settings screen
* Update .pot, .po, & .mo language files

= 1.0.19 =
* 2019-06-16
* Improve file-naming convention documentation

= 1.0.18 =
* 2019-06-16
* Improve file-naming convention documentation
* Use definition list for Font Squirrel suggested settings
* Open media uploader in Upload view instead of Library view
* Trim unused code from class-wp-foft-loader-admin-api.php
* Update .pot, .po, & .mo language files

= 1.0.17 =
* 2019-06-15
* Reverted recommended Em Square Value setting

= 1.0.16 =
* 2019-06-15
* Corrected font weight mapping in README & upload instructions
* Updated recommended Font Squirrel Generator settings

= 1.0.15 =
* 2019-06-14
* Added missing change to changelog & re-assigned version numbers

= 1.0.14 =
* 2019-06-14
* Added plugin banner & icon

= 1.0.13 =
* 2019-06-14
* Corrected GPL License info

= 1.0.12 =
* 2019-06-14
* Corrected version numbering error
* Fixed typo in README description

= 1.0.11 =
* 2019-06-14
* Corrected license URI

= 1.0.10 =
* 2019-06-14
* Upgraded GNU Public License from v2 to v3

= 1.0.9 =
* 2019-06-14
* Corrected Contributers username

= 1.0.8 =
* 2019-06-14
* Add Requires PHP to README

= 1.0.7 =
* 2019-06-14
* Add screenshots

= 1.0.6 =
* 2019-06-14
* Create readme.txt
* Add comments to readme.md

= 1.0.5 =
* 2019-06-14
* Upgrade license from GNU GPL 2 to GNU GPL 3
* Fix Undefined index: placeholder in class-wp-foft-loader-admin-api.php
* Add acknowledgement to README

= 1.0.4 =
* 2019-06-13
* README formatting fix

= 1.0.3 =
* 2019-06-13
* Further README formatting improvements

= 1.0.2 =
* 2019-06-13
* Fixed README formatting & added Pros & Cons to FAQ

= 1.0.1 =
* 2019-06-13
* Removed trailing comma if font stack is not set

= 1.0 =
* 2019-06-13
* Initial release

== Upgrade Notice ==

[//]: # (*********************************************************************          **********Update version tag in main file at lines _4_ and _45_!*****          *********************************************************************)

[//]: # (*********************************************************************          **********Update version numbering in .po and .pot files!************          *********************************************************************)

= 2.0.23 =
* 2019-12-02
* BUGFIX: fix broken path for fallback.min.js

[//]: # (REMEMBER to update the Stable tag and copy all changes to readme.txt!)
