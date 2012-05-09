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
   * Get remote data
   *
   * @param $url {string}
   * @return {array}
   *  Returns an array of data or FALSE
   */
  public function getRemote($url) {
    if ( $parsed_url = $this->parseUrl($url) ) {
      $get = wp_remote_get($this->buildUrl($parsed_url));
      if ( isset($get['body']) && !empty($get['body']) ) {
        $data = json_decode($get['body']);
        return array(
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
  public function buildUrl($parsed_url) {
    return $this->api_base . '/events/' . $parsed_url['method'] . '/' . $parsed_url['id'] . '.json';
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
