=== WP FOFT Loader ===
Contributors: Chris J. Zähller
Donate link: https://messengerwebdesign.com/donate
Tags: wordpress, plugin, fonts, performance
Requires at least: 3.9
Tested up to: 5.2.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin implements and automates Zach Leatherman's Cricital FOFT with Data URI (see https://www.zachleat.com/web/comprehensive-webfonts/).

== Installation ==

1. Download the plugin via https://github.com/seezee/WP-FOFT-Loader
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to the 'WP FOFT Loader Settings' page, upload your fonts, and configure the settings. See detailed instructions below.

== Generating and Uploading the Font Files ==

Upload two files for each web font: a WOFF file and a WOFF2 file. We recommend you use Font Squirrel’s Webfont Generator (https://www.fontsquirrel.com/tools/webfont-generator) to generate the files. Mandatory Font Squirrel settings are:

Select "Expert"
Font Formats:			"WOFF"
						"WOFF2"
Advanced Options:		"Font Name Suffix" = -webfont

For detailed recommended settings, see the plugin Upload options screen.

Filenames must follow the proper naming convention: $family-$weight&style-webfont-$filetype, e.g., for the bold weight italic style of Times New Roman, rename the files to timenewroman-boldItalic-webfont.woff and timesnewroman-boldItalic-webfont.woff2. For small caps style families, append SC (case-sensitive) to the family name, e.g., playfairdisplaySC-bold-webfont.woff.

Weights and styles are case-sensitive! Allowed weights and styles and their CSS mappings are:

thin | hairline (maps to 100)
extraLight | ultraLight (maps to 200)
light (maps to 300)
regular | normal (maps to 400)
medium (maps to 500)
demiBold | semiBold (maps to 600)
bold (maps to 700)
extraBold | ultraBold (maps to 800)
black | heavy (maps to 900)
thinItalic (maps to 100)
hairlineItalic | extraLightItalic (maps to 200)
ultraLightItalic | lightItalic (maps to 300)
italic (maps to 400)
mediumItalic (maps to 500)
demiBoldItalic | semiBoldItalic (maps to 600)
boldItalic (maps to 700)
extraBoldItalic | ultraBoldItalic (maps to 800)
blackItalic | heavyItalic (maps to 900)

== Configuration ==

= Optimize =

Load small subsetted font files before the page fully loads to improve performance. This setting works with the Base64 settings in the next tab. All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Base64 settings field.

Enter the names of your Base64 subsetted fonts below. Only the family name is needed, not the style. Names are case-insensitive. Hyphens and underscores are allowed, but spaces are not.

Correct:
playfairdisplay (all lowercase)
playfair-display (hyphens and underscores allowed)
PlayfairDisplay (mixed case allowed)

Incorrect:

playfairdisplay-bold (use the family name only; omit the style, i.e., “bold”)
playfair display (spaces prohibited)
Playfair Display (spaces prohibited)

= Base64 =

This setting inlines Base64 encoded font in the document head to improve font loading speeds. This setting works with the Optimize settings in the previous tab. All of the fields are optional, but if you fill out any of them you should also fill out the corresponding Optimize settings field.

Fonts must be subsetted and encoded to Base64. To subset and encode your fonts, we recommend you use Font Squirrel’s Webfont Generator. Mandatory Font Squirrel settings are:

Select "Expert"
Font Formats:			None
Fix Missing Glyphs:		None
Subsetting:				"Custom Subsetting" with the Unicode Ranges 0030-0039,0041-005A,0061-007A
						Leave everything else unchecked
OpenType Features:		None
OpenType Flattening:	None
CSS:					"Base64 Encode"
Advanced Options:		"Font Name Suffix" = -webfont

For detailed recommended settings, see the plugin Base64 options screen. The generator will produce a file that looks something like this:

@font-face{
  font-family: Merriweather;
  src: url(data:application/font-woff; charset=utf-8; base64, d09GRgABAAAAAB4UABAAAAAAMpAAA…) format("woff");
 }
Copy and paste the part the part between

src:url (data:application/font-woff; charset=utf-8; base64, 
and
) format("woff");
into the appropriate field below. In this example that would be d09GRgABAAAAAB4UABAAAAAAMpAAA….

= CSS =

@import rules are automatically handled by this plugin. You may manually inline your font-related CSS in the document <head> here. Place rules pertaining only to the font-family, font-weight, font-style, and font-variation properties here.

The plugin loads some CSS by default. You may disable it from this screen.

Use only the family name; omit weights and styles from the font name.

All declarations must start with the fonts-stage-2 class.

Incorrect:
p { // Missing class: .fonts-stage-2
  font-family: lato, sans-serif;
  font-weight: 400;
  font-style: normal;
}

strong { // Missing class: .fonts-stage-2
  font-family: lato-bold, serif; // Don’t include style in font name. Better yet, omit declaration altogether.
  font-weight: 700;
}

Correct:
.fonts-stage-2 p {
  font-family: lato, sans-serif;
  font-weight: 400;
  font-style: normal;
}

.fonts-stage-2 strong {
  // No need to redeclare the font-family — all weights map to a single family name
  font-weight: 700; // This will use the lato-bold font
}

For best performance, please minify your CSS (https://cssminifier.com/) before pasting it into the form.

= Font Stacks =

Change the default font fallbacks in case your custom fonts don’t load. Don’t include the names of your default custom fonts here.

## == Screenshots  (Pending) ==

## 1. Description of first screenshot named screenshot-1
## 2. Description of second screenshot named screenshot-2
## 3. Description of third screenshot named screenshot-3

== Frequently Asked Questions ==

= What is the plugin for? =

This plugin template implements and automates Zach Leatherman's Cricital FOFT with Data URI (see https://www.zachleat.com/web/comprehensive-webfonts/).

= How may I help improve this plugin? =

We'd love to hear your feedback. In particular, tell us about your experience configuring the plugin. Are the instructions clear? Do we need to reword them? Did we leave out something crucial? You get the drift.

= I'd like to do more =

We're looking for collaborators to improve the code. If you are an experienced Wordpress programmer, hit us up!

= I'd like to do even more =

Feel free to send a donation to my Paypal account: chris AT messengerwebdesign DOT com. Or buy me a beer if you're in town.

== Changelog ==

= 1.0 =
* 2019-06-13
* Initial release

== Upgrade Notice ==

= 1.0 =
* 2019-06-13
* Initial release
