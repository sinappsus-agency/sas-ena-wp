<?php 

// Enqueue FontAwesome
function ena_sinappsus_enqueue_fontawesome() {
    // Check if FontAwesome is already enqueued by another plugin or theme
    if (!wp_style_is('ena-sinappsus-fontawesome', 'enqueued')) {
        wp_enqueue_style('ena-sinappsus-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    }
}

// Enqueue on frontend
add_action('wp_enqueue_scripts', 'ena_sinappsus_enqueue_fontawesome');

// Enqueue the new crm.js script in the admin dashboard
function ena_sinappsus_enqueue_crm_script($hook) {
    if ($hook !== 'toplevel_page_ena-system') {
        return;
    }

    wp_enqueue_script('crm-script', plugin_dir_url(__FILE__) . '../../includes/scripts/crm.js', array('jquery'), null, true);

    wp_localize_script('crm-script', 'crmData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('admin_enqueue_scripts', 'ena_sinappsus_enqueue_crm_script');
