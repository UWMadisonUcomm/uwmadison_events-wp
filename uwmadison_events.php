<?php
/**
* @package UwmadisonEvents
* @version 1.2.1
*/
/*
Plugin Name: UW-Madison Events
Description: A wordpress plugin to interface with the UW-Madison events calendar (http://today.wisc.edu)
Author: University Communications and Marketting at the University of Wisconsin-Madison
Version: 1.2.1
*/

// Load the libraries
require_once(dirname(__FILE__) . '/lib/uwmadison_events.class.php');
require_once(dirname(__FILE__) . '/lib/uwmadison_events_widget.class.php');

if ( !function_exists("uwmadison_events_object") ) {
  /**
   * Factory function for the UwEvents class
   *
   * @return {object}
   *  Returns an instantiated UwEvents object, runs ->init() the first time
   *
   */
  function &uwmadison_events_object() {
    static $uwmadison_events_saved;

    if ( $uwmadison_events_saved )
      return $uwmadison_events_saved;

    // Instantiate the main library and init Wordpress
    $uwmadison_events = new UwmadisonEvents();
    $uwmadison_events->init();
    $uwmadison_events_saved = $uwmadison_events;

    return $uwmadison_events_saved;
  }
  uwmadison_events_object(); // Run the factory function
}

if ( !function_exists("uwmadison_events") ) {
  /**
   * Theme helper function for displaying events
   *
   * Prints the events for a URL
   * Calls UwEvents::parse()
   *
   * @param $url {string}
   * @param $opts {array}
   *
   */
  function uwmadison_events($url, $opts=array()) {
    echo uwmadison_events_object()->parse($url, $opts);
  }
}

if ( !function_exists("uwmadison_events_get_remote") ) {
  /**
   * Helper function to pull remote event data
   *
   * @param $url {string}
   * @param $opts {array}
   *
   * @return {object}
   *  Returns an object for the data returned or false
   *
   */
  function uwmadison_events_get_remote($url, $opts=array()) {
    return uwmadison_events_object()->getRemote($url, $opts);
  }
}

if ( !function_exists("uwmadison_events_get_event_data") ) {
  /**
   * Helper function to get data for a single event from an id
   *
   * @param $id {string}
   *  The event id
   * @return {object}
   *  Return the event data or FALSE
   */
  function uwmadison_events_get_event_data($id) {
    return uwmadison_events_object()->getEvent($id);
  }
}
