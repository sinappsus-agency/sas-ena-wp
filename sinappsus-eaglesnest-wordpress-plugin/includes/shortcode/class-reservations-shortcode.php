<?php 



// Shortcode for Room Booking
add_shortcode('ena_room_booking', 'ena_room_booking_shortcode');
function ena_room_booking_shortcode($atts) {
    $atts = shortcode_atts(['sales_funnel_id' => ''], $atts);
    ob_start();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ena_room_booking_nonce', 'ena_room_booking_nonce'); ?>
        <input type="hidden" name="sales_funnel_id" value="<?php echo esc_attr($atts['sales_funnel_id']); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <input type="submit" name="ena_room_booking_submit" value="Book Room">
    </form>
    <?php
    if (isset($_POST['ena_room_booking_submit']) && check_admin_referer('ena_room_booking_nonce', 'ena_room_booking_nonce')) {
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'sales_funnel_id' => sanitize_text_field($_POST['sales_funnel_id']),
        ];
        $response = ena_sinappsus_connect_to_api('/book-room', $data, 'POST');
        if ($response && isset($response['success']) && $response['success']) {
            echo '<p>Room booked successfully!</p>';
        } else {
            echo '<p>Failed to book room.</p>';
        }
    }
    return ob_get_clean();
}

// Shortcode for Showing All Available Rooms
add_shortcode('ena_show_rooms', 'ena_show_rooms_shortcode');
function ena_show_rooms_shortcode($atts) {
    $atts = shortcode_atts(['sales_funnel_id' => ''], $atts);
    ob_start();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ena_show_rooms_nonce', 'ena_show_rooms_nonce'); ?>
        <input type="hidden" name="sales_funnel_id" value="<?php echo esc_attr($atts['sales_funnel_id']); ?>">
        <input type="submit" name="ena_show_rooms_submit" value="Show Rooms">
    </form>
    <?php
    if (isset($_POST['ena_show_rooms_submit']) && check_admin_referer('ena_show_rooms_nonce', 'ena_show_rooms_nonce')) {
        $data = [
            'sales_funnel_id' => sanitize_text_field($_POST['sales_funnel_id']),
        ];
        $response = ena_sinappsus_connect_to_api('/show-rooms', $data, 'POST');
        if ($response && isset($response['rooms'])) {
            foreach ($response['rooms'] as $room) {
                echo '<p>' . esc_html($room['name']) . '</p>';
            }
        } else {
            echo '<p>No rooms available.</p>';
        }
    }
    return ob_get_clean();
}