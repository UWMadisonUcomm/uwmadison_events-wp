<?php
/**
 * Class for our widget
 */
class UwEventsWidget extends WP_Widget {
  public function __construct() {
    parent::__construct(
        'uw_events_widget', // Base ID
        'UW Events', // Name
        array( 'description' => 'UW-Madison events calendar widget', ) // args
      );

    // Shortcut to acess the isntantiated UwEvents object
    $this->uwe = uw_events_object();
  }

  /**
   * The options form
   */
  public function form( $instance ) {
    $url = isset($instance['url']) ? $instance['url'] : '';
    echo $this->generateInput('url', $url);
    return true;
  }

  /**
   * Update the instance options
   */
  public function update( $new_instance, $old_instance ) {
    // processes widget options to be saved
    $instance = wp_parse_args( $new_instance, $old_instance );
    return $instance;
  }

  /**
   * Render the widget content
   */
  public function widget( $args, $instance ) {
    $out = '<aside class="widget widget_meta">';
    if ( $parsed = $this->uwe->parse($instance['url']) ) {
      $out .= $parsed;
    }
    else {
      $out .= "<h2>Error</h2>";
    }
    $out .= '</aside>';
    print $out;
  }

  // Helper to generate single line input structures
  private function generateInput($field, $default='', $type='text') {
    $out = '<label for="' . $this->get_field_id($field) . '">URL:</label>';
    $out .= '<input type="'.$type.'" class="widefat" id="' . $this->get_field_id($field) . '" name="' .
      $this->get_field_name($field) . '" value="' . esc_attr($default) .'" />';
    return $out;
  }

  // We will possibly need this
  private function generateCheckbox($field, $default='') {
    // checkbox field
  }

  // We will possibly need this as well
  private function generateSelect($field, $options=array(), $selected='') {
    // select field
  }
}

// Register the widget with an ugly php "anonymous" function
add_action( 'widgets_init', create_function( '', 'register_widget( "UwEventsWidget" );' ) );
