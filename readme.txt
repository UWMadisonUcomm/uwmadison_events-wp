=== UW-Madison Events Calendar ===
Contributors: bshelton229, jnweaver, sterlinganderson
Tags: uwmadison
Requires at least: 4.0
Tested up to: 4.6.1
Stable tag: 1.2.1

A WordPress plugin to interface with the UW-Madison Events Calendar (http://today.wisc.edu)

== Description ==

This plugin currently supplies a widget, theme functions, and shortcode to
display filtered entries from specific feeds and tags from the UW-Madison events
calendar (http://today.wisc.edu.)

Contribute via GitHub: https://github.com/UWMadisonUcomm/uwmadison_events-wp

== Changelog ==

= 1.2.1 =
* Adds code to test if the plugin's functions and classes already exist, to help avoid errors in instances where the plugin code might be packaged into a theme or another plugin outside of Wordpress's plugin manager. This release otherwise adds no new or changed featues.

= 1.2.0 =
* This is released as a minor update because the widet now properly uses the before_widget and after_widget parameters. In the process, the hard-coded widget_meta CSS class has been dropped. Any styling based on this hook will break unless you use Wordpress's dynamic_sidebar_params filter to add that class yourself.

This version also allows the widget to set the header tag, which default to h2.

= 1.1.9 =
* Cleans up a few minor PHP notices

= 1.1.8 =
* Adds filter for changing the default date string to group events by

= 1.1.7 =
* Adds url data attribute from today.wisc.edu
  
= 1.1.6 =
* Adds handling for wp_remote_get errors; returns FALSE
 
= 1.1.5 =
* Adds uw_map_url field with map.wisc.edu URL for location building if available