## UW-Madison Events Calendar Wordpress Plugin ##

This plugin currently supplies a widget, and theme function. It should eventually supply a shortcode as well.

#### Theme function ####

    <?php uw_events('http://today.wisc.edu/events/tag/arts', array('limit' => 3)) ?>

#### Shortcode ####

    Here are my 4 newest events within post content:

    [uw_events url=http://today.wisc.edu/events/tag/film limit=4]
    [uw_events url=http://today.wisc.edu/events/tag/film limit=10 grouped=1 title="Film Events" show_description=1]

### Lower level helper functions ###

#### uw_events_get_remote() ####

    <pre>
    <?php print_r(uw_events_get_remote('http://today.wisc.edu/events/tag/arts', array('limit' => 20))) ?>
    </pre>

#### uw_events_get_event_data($id) ####

    <pre>
    <?php print_r(uw_events_get_event_data('123')) ?>
    </pre>
