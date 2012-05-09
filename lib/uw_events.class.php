<?php

class UwEvents {
  // The events calendar base API url
  public $api_base = 'http://test.today.wisc.edu';

  // Constructor
  public function __construct() {
    // Constructor code
  }

  /**
   * Init our wordpress stuff
   */
  public function init() {
    // Wordpress init code
  }

  /**
   * Public parse interface
   * Builds the <ul> for all events
   *
   * @param $url {string}
   * @param $opts {array}
   *
   * @return {string}
   *  Returns an html <ul> for the events requested
   *
   */
  public function parse($url, $opts=array()) {
    // Merge the defaults and the user supplied options
    $defaults = array(
      'limit' => 10
    );
    $opts = array_merge($defaults, $opts);

    if ( $data = $this->getRemote($url, $opts) ) {
      $out = '<ul class="uw_events">';
      foreach ( $data->data->events as $event ) {
        $out .= $this->eventHtml($event);
      }
      $out .= '</ul>';
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
  public function eventHtml($event) {
    return '<li class="uw_event"><span class="uw_event_title">' . $event->title . '</span></li>';
  }

  /**
   * Get remote data
   *
   * @param $url {string}
   * @return {array}
   *  Returns an array of data or FALSE
   */
  public function getRemote($url, $opts=array()) {
    if ( $parsed_url = $this->parseUrl($url) ) {
      $get = wp_remote_get($this->buildUrl($parsed_url, $opts));
      if ( isset($get['body']) && !empty($get['body']) ) {
        $data = json_decode($get['body']);
        return (object) array(
          'method' => $parsed_url['method'],
          'id' => $parsed_url['id'],
          'data' => $data,
        );
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
   * Parse an events calendar url
   * @param $url {string}
   * @return {array}
   *  Return an array with a method and id keys i.e. array('id' => 'arts', 'method' => 'tag')
   */
  public function parseUrl($url) {
    // We're only interested in the path
    $parse = parse_url($url);
    $url = $parse['path'];

    $pattern = '~/events/([^/]+)/(.*)$~i';
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
    $query = empty($opts) ? '' : '?' . http_build_query($opts);
    return $this->api_base . '/events/' . $parsed_url['method'] . '/' . $parsed_url['id'] . '.json' . $query;
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
}
