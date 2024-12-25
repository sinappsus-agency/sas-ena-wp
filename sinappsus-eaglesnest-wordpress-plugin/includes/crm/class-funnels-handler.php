<?php 


function get_sales_funnels() {
    $data = ena_sinappsus_connect_to_api('/sales-funnels');

    if (is_wp_error($data)) {
        return [];
    }

    return $data;
}