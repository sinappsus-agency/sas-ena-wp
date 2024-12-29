<?php 

// Enqueue in Elementor editor
add_action('elementor/editor/after_enqueue_styles', 'ena_sinappsus_enqueue_fontawesome');

// Initialize Elementor Widgets
function ena_sinappsus_register_elementor_widgets() {
    // Check if Elementor is loaded
    if (did_action('elementor/loaded')) {
        // Add custom category
        add_action('elementor/elements/categories_registered', 'ena_sinappsus_add_elementor_widget_categories');

        // Register Widgets
        add_action('elementor/widgets/register', 'ena_sinappsus_elementor_register_widgets');


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

function ena_sinappsus_elementor_register_widgets($widgets_manager) {
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-form-widget.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-funnel-step-widget.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-show-room.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-book-room.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-book-event.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-show-event.php');
    require_once(ENA_SINAPPSUS_PLUGIN_DIR . 'elementor-widgets/class-ena-sinappsus-available-rooms.php');

    $widgets_manager->register(new \Ena_Sinappsus_Form_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Funnel_Step_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Book_Room_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Book_Event_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Show_Event_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Show_Room_Widget());
    $widgets_manager->register(new \Ena_Sinappsus_Available_Rooms_Widget());
}
