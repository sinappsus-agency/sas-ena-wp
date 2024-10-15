<?php 

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


function get_funnel_data($funnel_id) {
    $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels/' . $funnel_id);

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}