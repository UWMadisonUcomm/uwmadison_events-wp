<?php
/**
 * Class for our widget
 */
class UwEventsWidget extends WP_Widget {
  public function __construct() {
    parent::__construct(
        'uw_events_widget', // Base ID
        'UwEventsWidget', // Name
        array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
      );

    // Shortcut to acess the isntantiated UwEvents object
    $this->uwe = uw_events_object();
  }

  public function form( $instance ) {
    // Render the form
    echo '<p>We will have options here</p>';
    return true;
  }

  public function update( $new_instance, $old_instance ) {
    // Process the form
  }

  public function widget( $args, $instance ) {
    // Currently render a test arts feed
    $out = '<aside class="widget widget_meta">';
    $out .= $this->uwe->parse('http://test.today.wisc.edu/events/tag/arts');
    $out .= '</aside>';
    print $out;
  }
}

// Register the widget with an ugly php "anonymous" function
add_action( 'widgets_init', create_function( '', 'register_widget( "UwEventsWidget" );' ) );
