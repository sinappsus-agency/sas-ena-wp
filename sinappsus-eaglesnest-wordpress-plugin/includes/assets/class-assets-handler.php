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
