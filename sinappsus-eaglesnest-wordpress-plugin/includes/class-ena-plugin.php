<?php
/*
Plugin Name: ENA SINAPPSUS
Description: A plugin for ENA SINAPPSUS that sets up a sidebar link with submenus for CRM and Reservations, and includes an admin page for settings.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('ENA_SINAPPSUS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ENA_SINAPPSUS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ENA_SINAPPSUS_API_URL', 'https://api-ena.sinappsus.us/api');

// Include the config file
require_once ENA_SINAPPSUS_PLUGIN_DIR . 'config.php';

// Add admin menu and submenus
add_action('admin_menu', 'ena_sinappsus_add_admin_menu');
function ena_sinappsus_add_admin_menu() {
    add_menu_page('ENA SYSTEM', 'ENA SYSTEM', 'manage_options', 'ena-system', 'ena_sinappsus_settings_page', 'dashicons-admin-generic');
    add_submenu_page('ena-system', 'CRM', 'CRM', 'manage_options', 'ena-crm', 'ena_sinappsus_crm_page');
    add_submenu_page('ena-system', 'Reservations', 'Reservations', 'manage_options', 'ena-reservations', 'ena_sinappsus_reservations_page');
    add_submenu_page('ena-system', 'Funnel Steps', 'Funnel Steps', 'manage_options', 'funnel-steps', 'funnel_steps_page_html');
}

// Settings page
function ena_sinappsus_settings_page() {
    ?>
    <div class="wrap">
        <h1>ENA SYSTEM Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ena_sinappsus_settings_group');
            do_settings_sections('ena_sinappsus_settings');
            wp_nonce_field('ena_sinappsus_settings_nonce', 'ena_sinappsus_nonce');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// CRM page
function ena_sinappsus_crm_page() {
    ?>
    <div class="wrap">
        <h1>CRM</h1>
        <p>Here you can manage your CRM contacts and leads.</p>
        <!-- Add your CRM management code here -->
    </div>
    <?php
}

// Reservations page
function ena_sinappsus_reservations_page() {
    ?>
    <div class="wrap">
        <h1>Reservations</h1>
        <p>Here you can manage your reservations.</p>
        <!-- Add your reservations management code here -->
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'ena_sinappsus_register_settings');
function ena_sinappsus_register_settings() {
    register_setting('ena_sinappsus_settings_group', 'ena_sinappsus_account_key');
    add_settings_section('ena_sinappsus_settings_section', 'API Settings', null, 'ena_sinappsus_settings');
    add_settings_field('ena_sinappsus_account_key', 'Account Key', 'ena_sinappsus_account_key_callback', 'ena_sinappsus_settings', 'ena_sinappsus_settings_section');
}

function ena_sinappsus_account_key_callback() {
    $account_key = get_option('ena_sinappsus_account_key');
    echo '<input type="text" name="ena_sinappsus_account_key" value="' . esc_attr($account_key) . '" />';
}

// Connect to the API
function ena_sinappsus_connect_to_api($endpoint, $data = array(), $method = 'GET') {
    $account_key = get_option('ena_sinappsus_account_key');
    $url = ENA_SINAPPSUS_API_URL . $endpoint;

    $args = array(
        'method' => $method,
        'headers' => array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $account_key,
        ),
        'body' => json_encode($data),
    );

    $response = wp_remote_request($url, $args);

    if (is_wp_error($response)) {
        return array('error' => $response->get_error_message());
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}

function get_sales_funnels() {
    $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels');

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

// CRM functions
function ena_sinappsus_get_contacts() {
    return ena_sinappsus_connect_to_api('/crm/contacts');
}

function ena_sinappsus_add_lead($lead_data) {
    return ena_sinappsus_connect_to_api('/crm/leads', $lead_data, 'POST');
}


// Shortcode for form
add_shortcode('ena_sinappsus_form', 'ena_sinappsus_form_shortcode');
function ena_sinappsus_form_shortcode($atts) {
    $atts = shortcode_atts(['funnel_id' => ''], $atts);
    ob_start();

    if (empty($atts['funnel_id'])) {
        echo '<p>Please provide a Sales Funnel ID.</p>';
        return ob_get_clean();
    }

    $funnel_data = get_funnel_data($atts['funnel_id']);
    if (empty($funnel_data)) {
        echo '<p>Invalid Sales Funnel ID.</p>';
        return ob_get_clean();
    }

    $crm_tunnel_id = $funnel_data['crm_tunnel_id'];
    $user_persona_id = $funnel_data['user_persona_id'];

    $fields = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'profile_picture' => 'Profile Picture',
        'dob' => 'Date of Birth',
        'address.street_line_01' => 'Street Line 01',
        'address.street_line_02' => 'Street Line 02',
        'address.street_line_03' => 'Street Line 03',
        'address.city' => 'City',
        'address.state' => 'State',
        'address.zip' => 'Zip',
        'address.country' => 'Country',
    ];

    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ena_sinappsus_form_nonce', 'ena_sinappsus_nonce'); ?>
        <input type="hidden" name="funnel_id" value="<?php echo esc_attr($atts['funnel_id']); ?>">
        <input type="hidden" name="crm_tunnel_id" value="<?php echo esc_attr($crm_tunnel_id); ?>">
        <input type="hidden" name="user_persona_id" value="<?php echo esc_attr($user_persona_id); ?>">

        <?php foreach ($fields as $field => $label) : ?>
            <label for="<?php echo esc_attr($field); ?>"><?php echo esc_html($label); ?>:</label>
            <input type="text" id="<?php echo esc_attr($field); ?>" name="<?php echo esc_attr($field); ?>">
        <?php endforeach; ?>

        <input type="submit" name="ena_sinappsus_submit" value="Submit">
    </form>
    <?php

    if (isset($_POST['ena_sinappsus_submit']) && check_admin_referer('ena_sinappsus_form_nonce', 'ena_sinappsus_nonce')) {
        $data = [
            'funnel_id' => sanitize_text_field($_POST['funnel_id']),
            'crm_tunnel_id' => sanitize_text_field($_POST['crm_tunnel_id']),
            'user_persona_id' => sanitize_text_field($_POST['user_persona_id']),
        ];

        foreach ($fields as $field => $label) {
            $data[$field] = sanitize_text_field($_POST[$field]);
        }

        $response = ena_sinappsus_connect_to_api('/contacts', $data, 'POST');
        if ($response && isset($response['id']) && $response['id']) {
            echo '<p>Form submitted successfully!</p>';
        } else {
            echo '<p>Failed to submit form.</p>';
        }
    }

    return ob_get_clean();
}

function get_funnel_data($funnel_id) {
    $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels/' . $funnel_id);

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

// Function to read URL parameters and make API call for the sales funnel page
function handle_sales_funnel_step($sales_funnel_id = null, $step_id = null) {
    // Ensure both parameters are provided
    if (empty($sales_funnel_id) || empty($step_id)) {
        return 'Sales Funnel ID and Step ID are required.';
    }

    // Prepare API URL based on the sales funnel and step IDs
    $api_url = ENA_SINAPPSUS_API_URL . "/salesfunnels/steps/{$sales_funnel_id}/{$step_id}";

    // Make the API call
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return 'API request failed';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Check if the API returns HTML content or a page ID
    if (isset($data['html'])) {
        // Return the HTML content
        return $data['html'];
    } elseif (isset($data['next_page_id'])) {
        // Get the next page ID and redirect
        $next_page = get_permalink($data['next_page_id']);
        wp_redirect($next_page);
        exit;
    }

    return 'No content available for this step.';
}

// Shortcode to load the sales funnel step
function handle_sales_funnel_step_shortcode($atts) {
    $atts = shortcode_atts([
        'sales_funnel' => '',
        'step' => '',
    ], $atts);

    return handle_sales_funnel_step($atts['sales_funnel'], $atts['step']);
}
add_shortcode('load_funnel_step', 'handle_sales_funnel_step_shortcode');

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


// Admin page HTML to link pages and funnel steps
function funnel_steps_page_html() {
    // Fetch all published pages
    $pages = get_pages();

    // Fetch the sales funnel steps (You need to replace this with your actual steps)
    $funnel_steps = array('Step 1', 'Step 2', 'Step 3');

    ?>
    <div class="wrap">
        <h1>Link Funnel Steps to Pages</h1>
        <form method="post" action="options.php">
            <?php settings_fields('funnel_step_options'); ?>
            <?php do_settings_sections('funnel_step_options'); ?>
            <?php wp_nonce_field('funnel_steps_nonce', 'funnel_steps_nonce'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Page</th>
                    <td>
                        <select name="funnel_step_page">
                            <?php foreach ($pages as $page) { ?>
                                <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Select Funnel Step</th>
                    <td>
                        <select name="funnel_step_name">
                            <?php foreach ($funnel_steps as $step) { ?>
                                <option value="<?php echo esc_attr($step); ?>"><?php echo esc_html($step); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings for saving funnel steps to pages
function register_funnel_settings() {
    register_setting('funnel_step_options', 'funnel_step_page');
    register_setting('funnel_step_options', 'funnel_step_name');
}
add_action('admin_init', 'register_funnel_settings');

// Enqueue FontAwesome
function ena_sinappsus_enqueue_fontawesome() {
    // Check if FontAwesome is already enqueued by another plugin or theme
    if (!wp_style_is('ena-sinappsus-fontawesome', 'enqueued')) {
        wp_enqueue_style('ena-sinappsus-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    }
}

// Enqueue on frontend
add_action('wp_enqueue_scripts', 'ena_sinappsus_enqueue_fontawesome');

// Enqueue in Elementor editor
add_action('elementor/editor/after_enqueue_styles', 'ena_sinappsus_enqueue_fontawesome');

// Initialize Elementor Widgets
function ena_sinappsus_register_elementor_widgets() {
    // Check if Elementor is loaded
    if (did_action('elementor/loaded')) {
        // Add custom category
        add_action('elementor/elements/categories_registered', 'ena_sinappsus_add_elementor_widget_categories');

        // Register Widgets
        add_action('elementor/widgets/register', 'ena_sinappsus_register_widgets');
    }
}
add_action('plugins_loaded', 'ena_sinappsus_register_elementor_widgets');

function ena_sinappsus_enqueue_scripts() {
    wp_register_script('ena-sinappsus-widget-script', plugin_dir_url(__FILE__) . 'elementor-widgets/ena-sinappsus-widget-script.js', ['jquery'], '1.0', true);
}
add_action('elementor/editor/after_enqueue_scripts', 'ena_sinappsus_enqueue_scripts');

function ena_sinappsus_add_elementor_widget_categories($elements_manager) {
    $elements_manager->add_category(
        'eagles-nest',
        [
            'title' => __('Eagles Nest', 'ena-sinappsus-plugin'),
            'icon' => 'fa fa-plug',
        ]
    );
}

function ena_sinappsus_register_widgets($widgets_manager) {
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-form-widget.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-funnel-step-widget.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-show-room.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-book-room.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-book-event.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-show-event.php');

    $widgets_manager->register(new \Ena_Sinappsus_Form_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Funnel_Step_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Book_Room_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Book_Event_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Show_Event_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Show_Room_Widget());
}