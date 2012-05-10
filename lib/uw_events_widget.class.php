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
    $limit = isset($instance['limit']) ? $instance['limit'] : '5';
    echo $this->generateInput('url', 'Url:', $url);
    echo $this->generateInput('limit', 'Limit:', $limit);
    return true;
  }

  /**
   * Update the instance options
   */
  public function update( $new_instance, $old_instance ) {
    // Validations
    if (isset($new_instance['limit']) && (int) $new_instance['limit'] < 1 )
      unset($new_instance['limit']);

    // processes widget options to be saved
    $instance = wp_parse_args( $new_instance, $old_instance );
    return $instance;
  }

  /**
   * Render the widget content
   */
  public function widget( $args, $instance ) {
    $instance['source'] = 'widget';
    $out = '<aside class="widget widget_meta">';
    if ( $parsed = $this->uwe->parse($instance['url'], $instance) ) {
      $out .= $parsed;
    }
    else {
      $out .= "<h2>Error</h2>";
    }
    $out .= '</aside>';
    print $out;
  }

  // Helper to generate single line input structures
  private function generateInput($field, $label, $default='', $type='text') {
    $out = '<label for="' . $this->get_field_id($field) . '">' . $label . '</label> ';
    $out .= '<input type="'.$type.'" class="widefat" id="' . $this->get_field_id($field) . '" name="' .
      $this->get_field_name($field) . '" value="' . esc_attr($default) .'" />';
    return $out;
  }

  // We will possibly need this
  private function generateCheckbox($field, $default='') {
    // checkbox field
  }

  // Early select box renderer
  private function generateSelect($field, $label, $options=array(), $selected='') {
    $selected = esc_attr($selected);
    $out = '<label for="' . $this->get_field_id($field) . '">'. $label . '</label> ';
    $out .= '<select name="' . $this->get_field_name($field) . '" id="' . $this->get_field_id($field) .'">';
    foreach($options as $o) {
      $out .= '<option value="' . $o . '">' . $o . '</option>';
    }
    $out .= "</select>";
    return $out;
  }
}
