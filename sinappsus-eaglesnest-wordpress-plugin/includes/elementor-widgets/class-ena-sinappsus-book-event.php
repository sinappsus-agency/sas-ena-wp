<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Ena_Sinappsus_Book_Event_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ena_sinappsus_book_event_widget';
    }

    public function get_title() {
        return __( 'ENA Book Event', 'ena-sinappsus-plugin' );
    }

    public function get_icon() {
        return 'fa fa-address-card'; // You can change this to any FontAwesome icon
    }

    public function get_categories() {
        return [ 'eagles-nest' ];
    }

    protected function register_controls() {
        $sales_funnels = $this->get_sales_funnels();

        $funnel_options = [];
        if (!empty($sales_funnels)) {
            foreach ($sales_funnels as $funnel) {
                $funnel_options[$funnel['id']] = $funnel['name'];
            }
        }

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'ena-sinappsus-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Sales Funnel Dropdown
        $this->add_control(
            'sales_funnel',
            [
                'label' => __('Sales Funnel', 'ena-sinappsus-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $funnel_options,
                'default' => '',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $sales_funnel_id = $settings['sales_funnel'];

        echo do_shortcode('[ena_book_event_ticket sales_funnel_id="' . esc_attr($sales_funnel_id) . '"]');
    }

    private function get_sales_funnels() {
        $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels');

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    protected function _content_template() {}
}
