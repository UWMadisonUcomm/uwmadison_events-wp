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
function uw_events() {
  static $uw_events_saved;

  if ( $uw_events_saved )
    return $uw_events_saved;

  // Instantiate the main library and init Wordpress
  $uw_events = new UwEvents();
  $uw_events->init();
  $uw_events_saved = $uw_events;

  return $uw_events_saved;
}
uw_events(); // Run the factory function
