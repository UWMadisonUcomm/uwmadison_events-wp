## UW-Madison Events Calendar Wordpress Plugin

This plugin currently supplies a widget, and theme function.

### Theme Function

    <?php uwmadison_events('https://today.wisc.edu/events/tag/arts', array('limit' => 3)) ?>
    <?php uwmadison_events('https://today.wisc.edu/events/tag/arts', array('limit' => 3, 'title' => 'Arts Events', 'grouped' => TRUE)) ?>

### Shortcode

Here are my 4 newest events within post content:

    [uwmadison_events url=https://today.wisc.edu/events/tag/film limit=4]

Look at 10 of the film events with a feed title of Film Events, showing descriptions:

    [uwmadison_events url=https://today.wisc.edu/events/tag/film limit=10 grouped=1 title="Film Events" show_description=1]

### Lower level helper functions

**uwmadison_events_get_remote($url, *$opts*)**

    <pre>
        <?php print_r(uwmadison_events_get_remote('https://today.wisc.edu/events/tag/arts', array('limit' => 20))) ?>
    </pre>

**uwmadison_events_get_event_data($event_id)**

    <pre>
        <?php print_r(uwmadison_events_get_event_data('123')) ?>
    </pre>

### Pagination

Note: The today.wisc.edu JSON feed does not return a total count, so you will need to account for this in your logic, e.g. check if the number of events returned is less than per_page or zero.

**Pagination with the theme function:**

    <?php uwmadison_events('https://today.wisc.edu/events/tag/arts', array('per_page' => 5, 'page' => 3)) ?>
    <?php uwmadison_events('https://today.wisc.edu/events/tag/arts', array('per_page' => 5, 'page' => 3, 'title' => 'Arts Events', 'grouped' => TRUE)) ?>

**Pagination with the lower level function:**
    <pre>
        <?php print_r(uwmadison_events_get_remote('https://today.wisc.edu/events/tag/arts', array('per_page' => 5, 'page => 3'))) ?>
    </pre>



### Hooks and Filters

#### uwmadison_events_date_formats filter

There are three date/time strings rendered by default. The default string is rendered for each event in the standard, non-grouped, view. A group item time is rendered for each event in the grouped view. The group header string is used for the date header for the group when rendering the list in group view.

These are all strings that use PHP date() formats which can be overriden with the uwmadison_events_date_formats filter. The filtered object is an associative array with three default keys, 'default', 'group_item', and 'group_header.' You may override these, as well as add your own format keys. Each array item returns a string, and will be made avialable in the event object. If you are going to be overriding the event html manually (covered later), it is still better to modify or add times to this array, which will be made available to you when you filter the event html. This is because this filter will take into account Wordpress's convention of always setting its timezone to UTC during runtime.

Example:

	/**
	 * Filter the UW-Madison Events date formats
	 */
	function my_uw_events_date_formats($date_formats, $unix_time) {
		// Change the default time beside each event
		$date_formats['default'] = date('m/d/y', $unix_time) . ' (day of week: ' . date('l', $unix_time) . ')';

		// Add a custom time format, made available in the $event
		// object passed to other filters
		$date_formats['my_time'] = 'My Time ' . date('Y-m-d', $unix_time);

		// Custom header format for each group of events
		// in the grouped event view
		$date_formats['group_header'] = 'On - '. date('D', $unix_time);

		return $date_formats;
	}
	add_filter('uwmadison_events_date_formats', 'my_uw_events_date_formats', 10, 1);

#### uwmadison_events_event_html filter

The uwmadison_events_html filter allows you to customize the content of each event's li in the ul of events, in both the standard and grouped views.

Example:

		/**
		 * Customise the events html for the <li> or each event
		 */
		function my_uwmadison_events_html($html, $event, $opts) {
			$my_event_html = 'Just the title ' . $event->title;
			return $my_event_html;
		}
		// When registering this filter, we must tell it we're receiving 3 arguments
		// if we want access to the $event object and options
		add_filter('uwmadison_events_event_html', 'my_uwmadison_events_html', 10, 3);

#### uwmadison_events_group_by filter

The *uwmadison_events_group_by* filter allows you to group events by somethign other than the default, which is based on day_month_year.

Example:

    /**
     * Group event by just the month
     */
    function my_uw_events_group_by($group_by) {
        return "F"; // pass a PHP date() format string
    }
    add_filter('uwmadison_events_group_by', 'my_uw_events_group_by');

### Change log

#### 1.5.0 (October 12, 2023)

**Breaking changes**
This version deprecates _strftime()_ calls (deprecated in PHP 8.1; to be removed in PHP 9) with _date()_. 

Implementations that use the _uwmadison_events_group_by_ or _uwmadison_events_date_formats_ filters should update those filters to also use date() formats.

For the _uwmadison_events_group_by_ filter, return a [PHP datetime format](https://www.php.net/manual/en/datetime.format.php) instead of a _strftime()_ format string.

For the *uwmadison_events_date_formats* filter, it now passes an arugment for `$unix_time` which is the Unix time integer representation of an events start date and time. When overriding or adding to the formats array, use `date()` with the passed `$unix_time` value to generate your desired date format string.

#### 1.4.0 (August 12, 2021)

* Replaces short-lived _physical_location_ field from 1.3.0 with _has_hybrid_format_ field which is a new boolean that indicates if an even has both a phycial and virtual location. If an event has only a physical location, the location field will show the location (note that a user can enter the word _Online_ or _Virtual_ in the freeform location field at today.wisc.edu - rare, but possible). If an event has only an online format, the location field will read _Online_ (as prior to 1.3.0). If an event is hybrid, the location will show the physical location. The virtual_url field will have the online stream or website for details. **NOTE:** The default Wordpress tag for showing an event listing does not show any location info. The new _has_hybrid_format_ only applies if you add custom templates in yur child theme.

#### 1.3.0 (August 11, 2021)

* Pulls in two new fields from the today.wisc.edu API: _virtual_url_ and _physical_location_. _virtual_url_ is the URL users enter if their event has an Online option. The legacy _location_ will now also indicate if an event has both a physical location and an online option. The location for a hybrid event will read, e.g. _200 Bascom Hall (also available online)_. The new fields allow you to show both a physical location and a virtual option, each with a link to either the campus map or the URL for the online event. **NOTE:** The default Wordpress tag for showing an event listing does not show any location info. These new fields only apply if you add custom templates in yur child theme.

#### 1.2.5 (March 3, 2021)

* Tested up to Wordpress 5.7

#### 1.2.4 (May 25, 2018)

* Changes the API base URL from http to https


#### 1.2.3 (April 4, 2018)

* Replaces deprecated *create_function* with anonymous function (@kedarjoyner)


#### 1.2.2 (November 28, 2017)

* Includes start and end params for function that builds a URL query (@kedarjoyner)
* Adds sponsor and tags as additional items to pull from the API (@kedarjoyner)
* Ensures limit option gets set - otherwise causes php warning that variable is unset. (@kedarjoyner)

#### 1.2.1 (October 19, 2016)

* Adds code to test if the plugin's functions and classes already exist, to help avoid errors in instances where the plugin code might be packaged into a theme or another plugin outside of Wordpress's plugin manager. This release otherwise adds no new or changed featues.

#### 1.2.0 (March 29, 2016)

* This is released as a minor update because the widet now properly uses the before_widget and after_widget parameters. In the process, the hard-coded widget_meta CSS class has been dropped. Any styling based on this hook will break unless you use Wordpress's dynamic_sidebar_params filter to add that class yourself.

#### 1.1.9 (October 23, 2015)

* Cleans up a few minor PHP notices

#### 1.1.8 (September 25, 2015)

* Adds filter for changing the default date string to group events by

#### 1.1.7 (March 11, 2015)

* Adds url data attribute from today.wisc.edu

#### 1.1.6 (January 27, 2015)

* Adds handling for wp_remote_get errors; returns FALSE (@sterlinganderson)

#### 1.1.5 (June 27, 2014)

* Add uw_map_url field to processd output data. If location references a campus building, a map.wisc.edu URL will be returned, e.g. https://www.map.wisc.edu/?initObj=0485

#### 1.1.4 (March 2, 2014)

* Now accepts per_page and page parameters corresponding to the today.wisc.edu API so that pagination can be designed into your integration of the plugin. E.g. uwmadison_events_get_remote('https://www.today.wisc.edu/events/feed/10', array('per_page' => 10, 'page' => 2)) will return events 11-20 from the feed. (Note: The feed does not return a total count, so you will need to account for this in your logic, e.g. check if the number of events returned is less than per_page or zero). 

This version also allows the widget to set the header tag, which default to h2.

#### 1.1.3 (Dec. 30, 2013)

* Add location field to processd output data

#### 1.1.2 (Sept. 17, 2013)

* Default date formats check for Windows to swap out windows compatible strftime strings.

#### 1.1.1 (Sept. 3, 2013)

* Removes type from output since it is not passed in today.wisc.edu JSON

#### 1.1 (May 16, 2013)

* Adds contact_phone, contact_email, cost and all_day_event to data output
