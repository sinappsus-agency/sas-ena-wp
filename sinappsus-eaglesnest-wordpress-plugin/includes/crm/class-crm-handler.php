<?php 

// CRM functions
function ena_sinappsus_get_contacts() {
    return ena_sinappsus_connect_to_api('/crm/contacts');
}

function ena_sinappsus_add_lead($lead_data) {
    return ena_sinappsus_connect_to_api('/crm/leads', $lead_data, 'POST');
}

// Function to update users
function ena_sinappsus_update_user($user_id, $user_data) {
    return ena_sinappsus_connect_to_api('/crm/users/' . $user_id, $user_data, 'PUT');
}

// Function to delete users
function ena_sinappsus_delete_user($user_id) {
    return ena_sinappsus_connect_to_api('/crm/users/' . $user_id, array(), 'DELETE');
}
