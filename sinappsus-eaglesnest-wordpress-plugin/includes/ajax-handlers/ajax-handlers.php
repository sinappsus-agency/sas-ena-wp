<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

function handle_get_available_rooms() {
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    if (!$start_date || !$end_date) {
        wp_send_json_error('Start date and end date are required');
    }

    $widget = new Ena_Sinappsus_Available_Rooms_Widget();
    $available_rooms = $widget->get_available_rooms($start_date, $end_date);

    wp_send_json_success($available_rooms);
}

add_action('wp_ajax_get_available_rooms', 'handle_get_available_rooms');
add_action('wp_ajax_nopriv_get_available_rooms', 'handle_get_available_rooms');