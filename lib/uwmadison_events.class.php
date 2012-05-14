<?php

class UwmadisonEvents {
  // The events calendar base API url
  public $api_base;

  // Define the date formats to use
  public $date_formats;

  // Cache expiration in seconds
  public $cache_expiration;

  // Define the plugin name for paths
  public $plugin_name;

  /**
   * Constructor function
   * Set the default instance variables
   */
  public function __construct() {
    // API Base
    $this->api_base = 'http://today.wisc.edu';

    // Plugin name
    $this->plugin_name = 'uwmadison_events';

    // Set the default date formats
    $this->date_formats = array(
      // Used to render the date in each <li> for individual events
      'default' => '<span class="uwmadison_event_date">%D</span>',
      // Used to render the heading for each date group
      'group' => '<span class="uwmadison_event_group_date">%b %e</span>',
    );

    // Default the cache to 30 minutes
    $this->cache_expiration = 60 * 30;
  }

  /**
   * Init our Wordpress stuff
   */
  public function init() {
    // Register the widget class
    add_action( 'widgets_init', create_function( '', 'register_widget( "UwmadisonEventsWidget" );' ) );
    // A short code wrapper for ::parse()
    add_shortcode( 'uwmadison_events', array( &$this, 'shortCode') );
    // Register and enque our stylesheet
    wp_register_style( 'uwmadison_events', plugins_url($this->plugin_name . '/stylesheets/uwmadison_events.css') );
    wp_enqueue_style( 'uwmadison_events' );
  }

  /**
   * Wordpress shortcode responder.
   * Another wrapper for ::parse()
   *
   * @param array $atts
   * @return string Returns the shortcode text
   */
  public function shortCode($atts) {
    $url = $atts['url'];
    $atts['source'] = 'shortcode';
    return $this->parse($url, $atts);
  }

  /**
   * Public parse interface
   * Builds the <ul> for all events
   *
   * @param string $url
   * @param array $opts
   * @return {string} Returns an html <ul> for the events requested
   */
  public function parse($url, $opts=array()) {
    $opts = $this->sanitizeOpts($opts);
    if ( $data = $this->getRemote($url, $opts) ) {
      $title_classes = array('uwmadison_events_title');
      // Add the widget-title class if we're widget instantiated
      if ( $opts['source'] == 'widget' )
        $title_classes[] = 'widget-title';
      // Let our users hook into the title classes
      $title_classes = apply_filters('uwmadison_events_title_classes', $title_classes, $opts);

      // Title and opening UL
      $out = '<div class="uwmadison_events_container"><' . $opts['header_tag'] . ' class="' . implode(' ', $title_classes) . '">' . $opts['title'] . "</{$opts['header_tag']}>\n";
      $out .= '<ul class="uwmadison_events">';

      // Grouped or not grouped
      if ( !$opts['grouped'] ) {
        foreach ( $data->data['ungrouped'] as $event ) {
          $out .= $this->eventHtml($event, $opts);
        }
      }
      else { // Render a grouped list
        foreach ( $data->data['grouped'] as $date => $grouped_data ) {
          // Pull the 'group' formatted date from the first event in the list
          // May be a slightly strange way to handle this, but it should work well
          $group_date = $grouped_data[0]->formatted_dates['group'];

          $out .= "<li>$group_date\n<ul class=\"uwmadison_events_group\">";
          foreach ( $grouped_data as $event ) {
            $out .= $this->eventHtml($event, $opts);
          }
          $out .= "</ul></li>";
        }
      }

      $out .= "</ul></div>"; // Closing UL

      // Filter for the entire output
      $out = apply_filters('uwmadison_events_html', $out, $data, $opts);

      return $out;
    }
    else {
      return '';
    }
  }

  /**
   * HTML for a specific event
   *
   * @param $event {object}
   *
   * @return {string}
   *  Returns the <li> string for the event object
   *
   */
  public function eventHtml($event, $opts=array()) {
    $opts = $this->sanitizeOpts($opts); // sanitize the options

    // Allow others to filter the event link, pass the event object as a second param
    $event_link = $event->link;

    $out = $event->formatted_dates['default'];
    $out .= ' <span class="event-title-and-subtitle"><span class="uwmadison_event_title">' . "<a href=\"$event_link\">" . $event->title . '</a></span>';
    if ( ! empty($event->subtitle) )
      $out .= ' <span class="uwmadison_event_subtitle">' . $event->subtitle . '</span>';
    $out .= '</span>';
    if ( $opts['show_description'] ) {
      if ( ! empty($event->description) )
        $out .= ' <span class="uwmadison_event_description">' . $event->description . '</span>';
    }

    // Let the user apply filters to the <li> for each event
    $out = apply_filters('uwmadison_events_event_html', $out, $event, $opts);

    // Return the output wrapped in an <li>
    return '<li class="uwmadison_event">' . $out . '</li>';
  }

  /**
   * Create the link for an event from an event object
   * This default is filterable
   *
   * @param $event {object}
   * @return {string}
   *  Return the link for a specific event
   */
  public function eventLink($event) {
    $link = $this->api_base . '/events/view/' . $event->id;
    return apply_filters('uwmadison_events_event_link', $link, $event);
  }

  /**
   * Get remote data
   *
   * @param $url {string}
   * @return {array}
   *  Returns an array of data or FALSE
   */
  public function getRemote($url, $opts=array()) {
    $opts = $this->sanitizeOpts($opts); // Sanitize the options

    if ( $parsed_url = $this->parseUrl($url) ) {
      // Build the parsed URL into a url with query params
      $built_url = $this->buildUrl($parsed_url, $opts);

      // Define the cache key
      $cache_key = $this->transientKey($built_url);

      // Pull remote data from the cache or fetch it
      if ( ($remote_cache = get_transient($cache_key)) !== FALSE ) {
        $remote_data = $remote_cache;
      }
      else {
        $get = wp_remote_get($built_url);
        if ( isset($get['response']['code']) && !preg_match('/^(4|5)/', $get['response']['code']) ) {
          $remote_data = json_decode($get['body']);
          set_transient($cache_key, $remote_data, $this->cache_expiration);
        }
        else {
          $remote_data = FALSE;
        }
      }

      if ( $remote_data !== FALSE ) {
        $data = $this->processRemoteData($remote_data);
        $out = (object) array(
          'method' => $parsed_url['method'],
          'id' => $parsed_url['id'],
          'timestamp' => time(),
          'data' => $data,
        );
        return $out;
      }
      else {
        return FALSE;
      }
    }
    else {
      return FALSE;
    }
  }

  /**
   * Process the remote JSON encoded string into a date sorted object
   * Deal with the Wordpress timezone insanity here.
   * All date formatting should be done in here so
   * we only have to worry about timezone switching once.
   *
   * @param $data {string}
   * @return {object}
   *  Return a formatted data object for events
   *
   */
  public function processRemoteData($data) {
    // Init
    $out = array( 'grouped' => array(), 'ungrouped' => array() );

    // Switch to sanity from Wordpress
    $wp_timezone = date_default_timezone_get();
    date_default_timezone_set('America/Chicago');

    foreach ($data as $event) {
      $start_unix = strtotime($event->startDate);
      $end_unix = strtotime($event->endDate);
      $day_stamp = strftime('%d_%m_%Y', $start_unix);

      $e = (object) array(
        'id' => $event->id,
        'title' => $event->title,
        'subtitle' => $event->subtitle,
        'type' => $event->eventtype_id,
        'description' => $event->description,
        'formatted_dates' => $this->parseDateFormats($start_unix),
        'start_timestamp' => $start_unix,
        'end_timestamp' => $end_unix,
        'link' => $this->eventLink($event),
      );

      // Append to grouped and ungrouped output
      $out['ungrouped'][] = $e;
      $out['grouped'][$day_stamp][] = $e;
    }

    // Restore ourselves to Wordpress insanity
    date_default_timezone_set($wp_timezone);

    // Return
    return $out;
  }

  /**
   * Parse an events calendar url
   * @param $url {string}
   * @return {array}
   *  Return an array with a method and id keys i.e. array('id' => 'arts', 'method' => 'tag')
   */
  public function parseUrl($url) {
    // We're only interested in the path
    $parse = parse_url($url);
    $url = $parse['path'];

    $pattern = '~events/([^/]+)/(.*)$~i';
    if ( preg_match($pattern, $url, $matches) ) {
      return array(
        'method' => $matches[1],
        'id' => $this->stripExtension($matches[2]),
      );
    }
    else {
      return FALSE;
    }
  }

  /**
   * Re-build a URL from a parseUrl() parsed url
   * @param $parsed_url {string}
   * @return {string}
   *  Return a full url string
   */
  public function buildUrl($parsed_url, $opts=array()) {
    $opts = $this->sanitizeOpts($opts); // Sanitize the options

    $query = !isset($opts['limit']) ? '' : '?limit=' . (int) $opts['limit'];
    return $this->api_base . '/events/' . $parsed_url['method'] . '/' . $parsed_url['id'] . '.json' . $query;
  }

  /**
   * Helper function to get data for a single event from an id
   * TODO: This needs more response validation for error codes
   *
   * @param $id {string}
   *  The event id
   * @return {object}
   *  Return the event data or FALSE
   */
  public function getEvent($id) {
    $data = FALSE; // Default data to false

    if ( preg_match('/^\d+$/', $id) ) {
      $id = "$id"; // Stringify
      $cache_key = 'uwe_event_' . $id;
      if ( ( $data = get_transient($cache_key) ) === FALSE ) {
        $get = wp_remote_get($this->api_base . '/events/view/' . $id . '.json');
        if ( isset($get['response']['code']) && !preg_match('/^(4|5)/', $get['response']['code']) ) {
          $data = json_decode($get['body']);
          set_transient($cache_key, $data, $this->cache_expiration);
        }
      }
    }

    return $data;
  }

  /**
   * Build a unique cache key for the transient API based on a URL (with query arguments)
   * NOTE: The key has to be less than 40 characters
   * MD5 hex hashes are 32 characters
   *
   * @param $url {string}
   * @return {string}
   *  The unique cache key
   */
  private function transientKey($url) {
    // Needs to be less than 40 characters
    // md5() hex hashes are 32 characters
    return "uwe_r" . md5($url);
  }

  /**
   * Parse a unix timestamp into all of our defined date formats.
   * Date formats are in the instance variable $this->date_formats
   *
   * @param $unix_time {integer}
   *  Unix timestamp
   * @return {array}
   *  Return an array for names with formatted times
   */
  private function parseDateFormats($unix_time) {
    $out = array();
    $date_formats = apply_filters('uwmadison_events_date_formats', $this->date_formats);
    foreach ($date_formats as $name => $format) {
      $out[$name] = strftime($format, $unix_time);
    }
    return $out;
  }

  /**
   * Strip content format extensions from a string
   * @param $s {string}
   * @return {string}
   *  Return the string less any content format extensions, or the original string
   *  if none are found.
   */
  private function stripExtension($s) {
    return preg_replace('~\.(json|html|rss|rss2|xml)$~i', '', $s);
  }

  /**
   * Sanitize the opts array and inject defaults
   *
   * @param $opts {array}
   * @return {array}
   *  Return a sanitized and default injected options array
   */
  private function sanitizeOpts($opts) {
    // Validate the limit
    if ( isset($opts['limit']) && (int) $opts['limit'] < 1 ) {
      unset($opts['limit']);
    }
    else {
      $opts['limit'] = (int) $opts['limit'];
    }

    // Defaults
    $defaults = array(
      'limit' => 5,
      'title' => 'Events',
      'show_description' => FALSE,
      'source' => 'function',
      'grouped' => FALSE,
      'header_tag' => 'h2',
    );

    // Merge in the defaults
    $opts = array_merge($defaults, $opts);

    return $opts;
  }
}
