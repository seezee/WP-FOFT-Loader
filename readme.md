=== WP FOFT Loader ===  
Contributors: seezee  
Translators: augusgils, nilovelez  
Donate link: https://messengerwebdesign.com/donate  
Tags: wordpress, plugin, fonts, webfonts, performance, UX  
Requires at least: 3.9  
Tested up to: 5.2.2  
Requires PHP: 7.0  
Stable tag: 1.0.43  
License: GNUv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html
GitHub Plugin URI: seezee/WP-FOFT-Loader  

== Description ==

This plugin implements and automates [Zach Leatherman’s Critical FOFT with Data URI](https://www.zachleat.com/web/comprehensive-webfonts/) to optimize and speed up webfont loading and improve UX by minimizing Flash of Invisible Text, Flash of Unstyled Text, and DOM Reflow.

[![RIPS CodeRisk](https://coderisk.com/wp/plugin/wp-foft-loader/badge "RIPS CodeRisk")](https://coderisk.com/wp/plugin/wp-foft-loader)

== Acknowledgement ==

This plugin is based on [Hugh Lashbrooke’s Starter Plugin](https://github.com/hlashbrooke/WordPress-Plugin-Template), a robust and GNU-licensed code template for creating a standards-compliant WordPress plugin.

== Installation ==

### USING THE WORDPRESS DASHBOARD
1. Navigate to “Add New” in the plugins dashboard
2. Search for “WP FOFT Loader”
3. Click “Install Now”
4. Activate the plugin on the Plugin dashboard
5. Go to Settings -> WP FOFT Loader, upload your fonts, and configure the settings.

### UPLOADING IN WORDPRESS DASHBOARD
1. Navigate to “Add New” in the plugins dashboard
2. Navigate to the “Upload” area
3. Select wp-foft-loader.zip from your computer
4. Click “Install Now”
5. Activate the plugin in the Plugin dashboard
6. Go to Settings -> WP FOFT Loader, upload your fonts, and configure the settings.

### USING FTP
1. Download the WP FOFT Loader ZIP file
2. Extract the WP FOFT Loader ZIP file to your computer
3. Upload the “wp-foft-loader” directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin’s dashboard
5. Go to Settings -> WP FOFT Loader, upload your fonts, and configure the settings.

### DOWNLOAD FROM GITHUB
1. Download the plugin via https://github.com/seezee/WP-FOFT-Loader
2. Follow the directions for using FTP

== Generating and Uploading the Font Files ==

Upload two files for each web font: a WOFF file and a WOFF2 file. We recommend you use [Font Squirrel’s Webfont Generator](https://www.fontsquirrel.com/tools/webfont-generator) to generate the files. Mandatory Font Squirrel settings are:

	Select “Expert”
	Font Formats:			“WOFF”
					“WOFF2”
	Advanced Options:		“Font Name Suffix” = -webfont

For detailed recommended settings, see the plugin Upload options screen.

**Filenames must follow the proper naming convention:** `$family`SC-`$variant`-webfont-`$filetype`.

**$family**
: The font family base name without style. Case-insensitive. May contain letters, numerals, and underscores but no hyphens or spaces.

**SC**
: Small caps identifier. *Optional*. Append to $family only if it is a small caps variant. *Case-sensitive*.

**$variant**
: The font style. Can be weight, style, or a combination of both. *Case-sensitive*.

**-webfont-**
: Mandatory suffix. Append to $variant.

**$filetype**
: The file type, i.e., “woff” or “woff2”.

**Example**: for the bold weight italic style of Times New Roman, rename the files to timenewroman-boldItalic-webfont.woff and timesnewroman-boldItalic-webfont.woff2. For small caps style families, append SC (case-sensitive) to the family name, e.g., playfairdisplaySC-bold-webfont.woff.

Allowed weights and styles and their CSS mappings are:

thin | hairline (maps to 100)  
extraLight | ultraLight (maps to 200)  
light (maps to 300)  
regular | normal (maps to 400)  
medium (maps to 500)  
demiBold | semiBold (maps to 600)  
bold (maps to 700)  
extraBold | ultraBold (maps to 800)  
black | heavy (maps to 900)  
thinItalic | hairlineItalic(maps to 100)  
extraLightItalic | ultraLightItalic (maps to 200)  
lightItalic (maps to 300)  
italic (maps to 400)  
mediumItalic (maps to 500)  
demiBoldItalic | semiBoldItalic (maps to 600)  
boldItalic (maps to 700)  
extraBoldItalic | ultraBoldItalic (maps to 800)  
blackItalic | heavyItalic (maps to 900)  

== Configuration ==

### Video Tutorials

[Episode 1. Intro and Background](https://youtu.be/0C0lDJ3T12o)  

[Episode 2. Font Squirrel Generator (WOFF & WOFF2)](https://youtu.be/-StFYcOSDCU)  

### Optimize

Load small subsetted font files before the page fully loads to improve performance. This setting works with the Base64 settings in the next tab. All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Base64 settings field.

Enter the names of your Base64 subsetted fonts below. **Only the family name is needed, not the style.** Names are case-insensitive. Hyphens and underscores are allowed, but spaces are not.

**Correct:**

`playfairdisplay` (all lowercase)  
`playfair-display` (hyphens and underscores allowed)  
`PlayfairDisplay` (mixed case allowed)  

**Incorrect:**

`playfairdisplay-bold` (use the family name only; omit the style, i.e., “bold”)  
`playfair display` (spaces prohibited)  
`Playfair Display` (spaces prohibited)  

### Base64

This setting inlines Base64 encoded font in the document head to improve font loading speeds. This setting works with the Optimize settings in the previous tab. All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Optimize settings field.

Fonts must be subsetted and encoded to Base64. To subset and encode your fonts, we recommend you use Font Squirrel’s Webfont Generator. Mandatory Font Squirrel settings are:

	Select “Expert”
	Font Formats:			None
	Fix Missing Glyphs:		None
	Subsetting:			“Custom Subsetting” with the Unicode Ranges 0030-0039,0041-005A,0061-007A
					Leave everything else unchecked  
	OpenType Features:		None
	OpenType Flattening:		None
	CSS:				“Base64 Encode”

For detailed recommended settings, see the plugin Base64 options screen. The generator will produce a file that looks something like this:

	@font-face{  
	  font-family: Merriweather;  
	  src: url(data:application/font-woff; charset=utf-8; base64,   d09GRgABAAAAAB4UABAAAAAAMpAAA…) format(“woff”);
	 }
 
Copy and paste the part the part between ‘`src:url (data:application/font-woff; charset=utf-8; base64,`’ and ‘`) format(“woff”);`’ into the appropriate field below. In this example that would be ‘`d09GRgABAAAAAB4UABAAAAAAMpAAA…`’.

### CSS

@import rules are automatically handled by this plugin. You may manually inline your font-related CSS in the document `<head>` here. Place rules pertaining only to the font-family, font-weight, font-style, and font-variation properties here.

#### Plugin CSS

The plugin loads some CSS by default. You may disable it from this screen.

#### Font Display

The plugin uses `font-display: swap` by default. You can override the [`font-display`](https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display) property here.

#### CSS Stage 1

Declarations placed in this field will load the Base64 subset as a placeholder while the external fonts load.

* Use only the family name followed by Subset (case-insensitive)
* Family names must match the names you input on the “Optimize” screen.
* Omit weights and styles from the font name
* All declarations must start with the fonts-stage-1 class
* See the Documentation screen to view the Stage 1 CSS that this plugin loads by default.

Incorrect:

	.nav-primary { // Missing prefix: .fonts-stage-1
	  font-family: latoSubset, sans-serif;
	}

	.fonts-stage-1 #footer-primary {
	  font-family: lato-boldSubset, san-serif; // Don’t include the weight or style
	}

	.fonts-stage-1 #footer-secondary {
	  font-family: lato, san-serif; // Missing “Subset” suffix
	}

	.fonts-stage-1 div.callout {
	  font-family: lato-Subset, san-serif;
	  font-size: 1rem; // “font-family,” “font-weight,” “font-style,”
					   // and “font-variant” rules only
	}
	
Correct:

	.fonts-stage-1 .nav-primary {
	  font-family: latoSubset, sans-serif;
	}

	.fonts-stage-1 dl.glossary {
	  font-family: latosubset, san-serif; // Suffix is case-insensitive
	}

#### CSS Stage 2

* Use only the family name
* Family names must match the file names for the fonts you uploaded on the “Upload” screen.
* Omit weights and styles from the font name
* All declarations must start with the fonts-stage-2 class
* For best performance, please minify your CSS before pasting it into the form.
* See the Documentation screen to view the Stage 2 CSS that this plugin loads by default.

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

### Font Stacks

Change the default font fallbacks in case your custom fonts don’t load. Don’t include the names of your default custom fonts here.

### Further Documentation

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

This plugin implements and automates [Zach Leatherman’s Critical Flash of Faux Text (FOFT) with Data URI](https://www.zachleat.com/web/comprehensive-webfonts/). This technique is the best compromise between font speed loading and a positive user experience.

**PROS**

* All the existing Pros of the [Critical FOFT approach](https://www.zachleat.com/web/comprehensive-webfonts/#critical-foft).
* Eliminates Flash of Invisible Text (FOIT) and greatly reduces Flash of Unstyled Text (FOUT) for the roman font. A small reflow will occur for additional characters loaded in the second stage and when the other weights and styles are loaded, but it will have a much smaller impact.

**CONS**

* All the existing Cons of the [Critical FOFT approach](https://www.zachleat.com/web/comprehensive-webfonts/#critical-foft).
* The small inlined Data URI will marginally block initial render. We’re trading this for highly reduced FOUT.
* Self hosting: Required.

### How may I help improve this plugin?

I’d love to hear your feedback. In particular, tell me about your experience configuring the plugin. Are the instructions clear? Do I need to reword them? Did I leave out something crucial? You get the drift.

### I’d like to do more

I’m looking for collaborators to improve the code. If you are an experienced Wordpress programmer, hit me up!

### I’d like to do even more

Feel free to send a donation to my [Paypal account](https://paypal.me/messengerwebdesign?locale.x=en_US). Or buy me a beer if you’re in town.

== Translations ==

* English: Default language, always included
* Dutch, Netherlands (nl_NL) by Augus van Gils @augusgils

Forthcoming translations:

* Spanish (es) by Daniel de Lira
* Spanish (es_MX) by Nilo Vélez @nilovelez

== Dependencies ==

This plugin includes these third-party libraries in its package.

* [HTMLPurifier](https://github.com/ezyang/htmlpurifier): v1.7.0
* [CSSTidy](https://github.com/Cerdic/CSSTidy): v4.11.0

== Changelog ==

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
* Corrected GNU License info

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
* Upgrade license from GNU GNU 2 to GNU GNU 3
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

[//]: # (*********************************************************************          ***Do not copy/paste to readme.txt! You'll mess up the formatting!***          *********************************************************************)

= 1.0.43 =
* 2019-09-25
* Update plugin description in main file

[//]: # (REMEMBER to update the Stable tag and copy all changes to readme.txt!)

