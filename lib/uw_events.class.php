<?php

class UwEvents {
  // Api base
  public $api_base = 'http://test.today.wisc.edu';

  /**
   * Initial Function
   */
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
   * Get the remote feed
   */
  public function getRemote($url) {
    return wp_remote_get($this->sanitizeUrl($url));
  }

  /**
   * This will eventually render things
   */
  public function sanitizeUrl($url) {
    return $url;
  }
}
