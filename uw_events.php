<?php
/**
* @package UwEvents
* @version 0.0.1
*/
/*
Plugin Name: UwEvents
Description: A wordpress plugin to interface with the UW-Madison events calendar
Author: University Communications at the University of Wisconsin-Madison
Version: 0.0.1
*/

// Load the main library
require_once(dirname(__FILE__) . '/lib/uw_events.class.php');

/**
 * Factory function for the UwEvents class
 *
 * @return {object}
 *  Returns an instantiated UwEvents object, runs ->init() the first time
 *
 */
function uw_events_object() {
  static $uw_events_saved;

  if ( $uw_events_saved )
    return $uw_events_saved;

  // Instantiate the main library and init Wordpress
  $uw_events = new UwEvents();
  $uw_events->init();
  $uw_events_saved = $uw_events;

  return $uw_events_saved;
}
uw_events_object(); // Run the factory function

// Load our widget class after we've instantiated UwEvents
require_once(dirname(__FILE__) . '/lib/uw_events_widget.class.php');

/**
 * Helper functions for the wordpress theme
 */

/**
 * Return the html for an events calendar url
 *
 * @param $url {string}
 * @param $opts {array}
 *
 * @return {string}
 *  Returns the html for the events url
 *
 */
function uw_events($url, $opts=array()) {
  return uw_events_object()->parse($url, $opts);
}

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
function uw_events_get_remote($url, $opts=array()) {
  return uw_events_object()->getRemote($url, $opts);
}
