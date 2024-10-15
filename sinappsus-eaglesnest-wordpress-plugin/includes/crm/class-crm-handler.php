<?php 


// CRM functions
function ena_sinappsus_get_contacts() {
    return ena_sinappsus_connect_to_api('/crm/contacts');
}

function ena_sinappsus_add_lead($lead_data) {
    return ena_sinappsus_connect_to_api('/crm/leads', $lead_data, 'POST');
}