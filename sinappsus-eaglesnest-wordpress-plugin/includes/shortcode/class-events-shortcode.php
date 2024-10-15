<?php 


// Shortcode for Showing All Events
add_shortcode('ena_show_events', 'ena_show_events_shortcode');
function ena_show_events_shortcode($atts) {
    $atts = shortcode_atts(['sales_funnel_id' => ''], $atts);
    ob_start();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ena_show_events_nonce', 'ena_show_events_nonce'); ?>
        <input type="hidden" name="sales_funnel_id" value="<?php echo esc_attr($atts['sales_funnel_id']); ?>">
        <input type="submit" name="ena_show_events_submit" value="Show Events">
    </form>
    <?php
    if (isset($_POST['ena_show_events_submit']) && check_admin_referer('ena_show_events_nonce', 'ena_show_events_nonce')) {
        $data = [
            'sales_funnel_id' => sanitize_text_field($_POST['sales_funnel_id']),
        ];
        $response = ena_sinappsus_connect_to_api('/show-events', $data, 'POST');
        if ($response && isset($response['events'])) {
            foreach ($response['events'] as $event) {
                echo '<p>' . esc_html($event['name']) . '</p>';
            }
        } else {
            echo '<p>No events available.</p>';
        }
    }
    return ob_get_clean();
}

// Shortcode for Booking Event Ticket
add_shortcode('ena_book_event_ticket', 'ena_book_event_ticket_shortcode');
function ena_book_event_ticket_shortcode($atts) {
    $atts = shortcode_atts(['sales_funnel_id' => ''], $atts);
    ob_start();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ena_book_event_ticket_nonce', 'ena_book_event_ticket_nonce'); ?>
        <input type="hidden" name="sales_funnel_id" value="<?php echo esc_attr($atts['sales_funnel_id']); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <input type="submit" name="ena_book_event_ticket_submit" value="Book Ticket">
    </form>
    <?php
    if (isset($_POST['ena_book_event_ticket_submit']) && check_admin_referer('ena_book_event_ticket_nonce', 'ena_book_event_ticket_nonce')) {
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'sales_funnel_id' => sanitize_text_field($_POST['sales_funnel_id']),
        ];
        $response = ena_sinappsus_connect_to_api('/book-event-ticket', $data, 'POST');
        if ($response && isset($response['success']) && $response['success']) {
            echo '<p>Ticket booked successfully!</p>';
        } else {
            echo '<p>Failed to book ticket.</p>';
        }
    }
    return ob_get_clean();
}
