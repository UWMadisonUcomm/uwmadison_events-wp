## UW-Madison Events Calendar Wordpress Plugin

This plugin currently supplies a widget, and theme function. It should eventually supply a shortcode as well.

### Theme Function

    <?php uwmadison_events('http://today.wisc.edu/events/tag/arts', array('limit' => 3)) ?>
    <?php uwmadison_events('http://today.wisc.edu/events/tag/arts', array('limit' => 3, 'title' => 'Arts Events', 'grouped' => TRUE)) ?>

### Shortcode

Here are my 4 newest events within post content:
    
    [uwmadison_events url=http://today.wisc.edu/events/tag/film limit=4]

Look at 10 of the film events with a feed title of Film Events, showing descriptions:

    [uwmadison_events url=http://today.wisc.edu/events/tag/film limit=10 grouped=1 title="Film Events" show_description=1]

### Lower level helper functions

**uwmadison_events_get_remote($url, *$opts*)**

    <pre>
        <?php print_r(uwmadison_events_get_remote('http://today.wisc.edu/events/tag/arts', array('limit' => 20))) ?>
    </pre>

**uwmadison_events_get_event_data($event_id)**

    <pre>
        <?php print_r(uwmadison_events_get_event_data('123')) ?>
    </pre>
