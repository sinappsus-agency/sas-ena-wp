<?php 

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


// Register settings for saving funnel steps to pages
function register_funnel_settings() {
    register_setting('funnel_step_options', 'funnel_step_page');
    register_setting('funnel_step_options', 'funnel_step_name');
}
add_action('admin_init', 'register_funnel_settings');
