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

// AJAX handler for adding a user
function handle_add_user() {
    $user_data = array(
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
    );

    $response = ena_sinappsus_add_lead($user_data);

    if (isset($response['id'])) {
        wp_send_json_success($response);
    } else {
        wp_send_json_error('Failed to add user');
    }
}
add_action('wp_ajax_add_user', 'handle_add_user');

// AJAX handler for editing a user
function handle_edit_user() {
    $user_id = intval($_POST['user_id']);
    $user_data = array(
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
    );

    $response = ena_sinappsus_update_user($user_id, $user_data);

    if (isset($response['id'])) {
        wp_send_json_success($response);
    } else {
        wp_send_json_error('Failed to edit user');
    }
}
add_action('wp_ajax_edit_user', 'handle_edit_user');

// AJAX handler for deleting a user
function handle_delete_user() {
    $user_id = intval($_POST['user_id']);

    $response = ena_sinappsus_delete_user($user_id);

    if (isset($response['success']) && $response['success']) {
        wp_send_json_success($response);
    } else {
        wp_send_json_error('Failed to delete user');
    }
}
add_action('wp_ajax_delete_user', 'handle_delete_user');
