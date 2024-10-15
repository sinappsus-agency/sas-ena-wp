<?php 


function get_sales_funnels() {
    $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels');

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}