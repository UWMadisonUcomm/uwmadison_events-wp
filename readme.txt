=== UW-Madison Events Calendar ===
Contributors: bshelton229, jnweaver, sterlinganderson, kedarjoyner
Tags: uwmadison
Requires at least: 5.0
Tested up to: 6.3
Stable tag: 1.5.0

A WordPress plugin to interface with the UW-Madison Events Calendar (https://today.wisc.edu)

== Description ==

This plugin currently supplies a widget, theme functions, and shortcode to
display filtered entries from specific feeds and tags from the UW-Madison events
calendar (https://today.wisc.edu.)

Contribute via GitHub: https://github.com/UWMadisonUcomm/uwmadison_events-wp

== Changelog ==
= 1.5.0 =
**Deprecated**
This version deprecates _strftime()_ calls (deprecated in PHP 8.1; to be removed in PHP 9) with _date()_. 

Implementations that use the _uwmadison_events_group_by_ or _uwmadison_events_date_formats_ filters should update those filters to also use date() formats.

For the _uwmadison_events_group_by_ filter, return a [PHP datetime format](https://www.php.net/manual/en/datetime.format.php) instead of a _strftime()_ format string.

For the *uwmadison_events_date_formats* filter, it now passes an arugment for `$unix_time` which is the Unix time integer representation of an events start date and time. When overriding or adding to the formats array, use `date()` with the passed `$unix_time` value to generate your desired date format string.

= 1.4.0 =
* Replaces short-lived _physical_location_ field from 1.3.0 with _has_hybrid_format_ field which is a new boolean that indicates if an even has both a phycial and virtual location. If an event has only a physical location, the location field will show the location (note that a user can enter the word _Online_ or _Virtual_ in the freeform location field at today.wisc.edu - rare, but possible). If an event has only an online format, the location field will read _Online_ (as prior to 1.3.0). If an event is hybrid, the location will show the physical location. The virtual_url field will have the online stream or website for details. **NOTE:** The default Wordpress tag for showing an event listing does not show any location info. The new _has_hybrid_format_ only applies if you add custom templates in yur child theme.

= 1.3.0 =
* DON'T USE THIS VERSION. Pulls in two new fields from the today.wisc.edu API: _virtual_url_ and _physical_location_. _virtual_url_ is the URL users enter if their event has an Online option. The legacy _location_ will now also indicate if an event has both a physical location and an online option. The location for a hybrid event will read, e.g. _200 Bascom Hall (also available online)_. The new fields allow you to show both a physical location and a virtual option, each with a link to either the campus map or the URL for the online event. **NOTE:** The default Wordpress tag for showing an event listing does not show any location info. These new fields only apply if you add custom templates in yur child theme.

= 1.2.5 =
* Tested up to Wordpress 5.7

= 1.2.4 =
* Changes the API base URL from http to https

= 1.2.3 =
* Replaces deprecated *create_function* with anonymous function

= 1.2.2 =
* Includes start and end params for function that builds a URL query
* Adds sponsor and tags as additional items to pull from the API
* Ensures limit option gets set - otherwise causes php warning that variable is unset.

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