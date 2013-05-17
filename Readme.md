## UW-Madison Events Calendar Wordpress Plugin

This plugin currently supplies a widget, and theme function. It should eventually supply a shortcode as well.

### Theme Function

    <?php uwmadison_events('http://today.wisc.edu/events/tag/arts', array('limit' => 3)) ?>
    <?php uwmadison_events('http://today.wisc.edu/events/tag/arts', array('limit' => 3, 'title' => 'Arts Events', 'grouped' => TRUE)) ?>

### Shortcode

Here are my 4 newest events within post content:

    [uwmadison_events url=http://today.wisc.edu/events/tag/film limit=4]

Look at 10 of the film events with a feed title of Film Events, showing descriptions:

    [uwmadison_events url=http://today.wisc.edu/events/tag/film limit=10 grouped=1 title="Film Events" show_description=1]

### Lower level helper functions

**uwmadison_events_get_remote($url, *$opts*)**

    <pre>
        <?php print_r(uwmadison_events_get_remote('http://today.wisc.edu/events/tag/arts', array('limit' => 20))) ?>
    </pre>

**uwmadison_events_get_event_data($event_id)**

    <pre>
        <?php print_r(uwmadison_events_get_event_data('123')) ?>
    </pre>

### Hooks and Filters

#### uwmadison_events_date_formats filter

There are three date/time strings rendered by default. The default string is rendered for each event in the standard, non-grouped, view. A group item time is rendered for each event in the grouped view. The group header string is used for the date header for the group when rendering the list in group view.

These are all strftime formatted strings which can be overriden with the uwmadison_events_date_formats filter. The filtered object is an associative array with three default keys, 'default', 'group_item', and 'group_header.' You may override these, as well as add your own strftime keys. Each item will be parsed with strftime with each event's startDate, and will be made avialable in the event object. If you are going to be overriding the event html manually (covered later), it is still better to modify or add times to this array, which will be made available to you when you filter the event html. This is because this filter will take into account Wordpress's convention of always setting its timezone to UTC during runtime.

Example:

	/**
	 * Filter the UW-Madison Events date formats
	 */
	function my_uw_events_date_formats($date_formats) {
		// Change the default time beside each event
		$date_formats['default'] = '%D (day of week: %a)';

		// Add a custom time format, made available in the $event
		// object passed to other filters
		$date_formats['my_time'] = 'My Time %F';

		// Custom header format for each group of events
		// in the grouped event view
		$date_formats['group_header'] = 'On - %a';

		return $date_formats;
	}
	add_filter('uwmadison_events_date_formats', 'my_uw_events_date_formats');

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

### Change log

#### 1.1

* Adds contact_phone, contact_email, cost and all_day_event to data output
