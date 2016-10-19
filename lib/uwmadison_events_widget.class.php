<?php
if ( !class_exists("UwmadisonEventsWidget") ) {

  /**
   * Class for our widget
   */
  class UwmadisonEventsWidget extends WP_Widget {
    public function __construct() {
      parent::__construct(
          'uwmadison_events_widget', // Base ID
          'UW-Madison Events', // Name
          array( 'description' => 'UW-Madison events calendar widget', ) // args
        );

      // Shortcut to acess the instantiated UwEvents object
      $this->uwe = uwmadison_events_object();
    }

    /**
     * The options form
     */
    public function form( $instance ) {
      // Defaults
      $url        =     isset($instance['url']) ? $instance['url'] : '';
      $limit      =     isset($instance['limit']) ? $instance['limit'] : '5';
      $title      =     isset($instance['title']) ? $instance['title'] : 'Events';
      $header_tag      =     isset($instance['header_tag']) ? $instance['header_tag'] : 'h2';
      $grouped    =     isset($instance['grouped']) ? $instance['grouped'] : '0';

      // Build the form
      echo $this->generateInput('title', 'Title:', $title);
      echo $this->generateInput('header_tag', 'Header tag:', $header_tag);
      echo $this->generateInput('url', 'Url:', $url);
      echo $this->generateInput('limit', 'Limit:', $limit);
      echo $this->generateCheckbox('grouped', 'Group results:', $grouped);

      return TRUE;
    }

    /**
     * Update the instance options
     */
    public function update( $new_instance, $old_instance ) {
      // Validations
      if (isset($new_instance['limit']) && (int) $new_instance['limit'] < 1 )
        unset($new_instance['limit']);

      // Sanitize grouped
      if ( isset($new_instance['grouped']) && $new_instance['grouped'] == '1' ) {
        $new_instance['grouped'] = '1';
      }
      else {
        $new_instance['grouped'] = '0';
      }

      // sanitize header_tag
      if ( isset($new_instance['header_tag']) ) {
        $new_instance['header_tag'] = sanitize_text_field($new_instance['header_tag']);
      }

      // processes widget options to be saved
      $instance = wp_parse_args( $new_instance, $old_instance );
      return $instance;
    }

    /**
     * Render the widget content
     */
    public function widget( $args, $instance ) {
      $instance['source'] = 'widget';
      $out = $args['before_widget'];
      if ( $parsed = $this->uwe->parse($instance['url'], $instance) ) {
        $out .= $parsed;
      }
      else {
        $out .= "<h2>Error</h2>";
      }
      $out .= $args['after_widget'];
      print $out;
    }

    /**
     * Helper to generate a text input
     */
    private function generateInput($field, $label, $default='', $type='text') {
      $out = '<label for="' . $this->get_field_id($field) . '">' . $label . '</label> ';
      $out .= '<input type="'.$type.'" class="widefat" id="' . $this->get_field_id($field) . '" name="' .
        $this->get_field_name($field) . '" value="' . esc_attr($default) .'" />';
      return $out;
    }

    /**
     * Helper to generate a checkbox
     */
    private function generateCheckbox($field, $label, $default='') {
      $checked = ($default == '1') ? ' CHECKED' : '';
      $out = '<label for="' . $this->get_field_id($field) . '">' . $label . '</label> ';
      $out .= "<input type=\"checkbox\" name=\"{$this->get_field_name($field)}\" value=\"1\" id=\"{$this->get_field_id($field)}\"$checked>";
      return $out;
    }

    // Early select box renderer
    // Not developed fully yet, don't use it
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
}
