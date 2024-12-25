<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ena_Sinappsus_Funnel_Step_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ena_sinappsus_funnel_step_widget';
    }

    public function get_title() {
        return __('ENA Funnel Step', 'ena-sinappsus-plugin');
    }

    public function get_icon() {
        return 'fa fa-map-signs'; // You can change this to any FontAwesome icon
    }

    public function get_script_depends() {
        return ['ena-sinappsus-widget-script'];
    }

    public function get_categories() {
        return ['eagles-nest'];
    }

    protected function register_controls() {
        $sales_funnels = $this->get_sales_funnels();

        $funnel_options = [];
        $steps_options = [];

        if (!empty($sales_funnels)) {
            foreach ($sales_funnels as $funnel) {
                $funnel_options[$funnel['id']] = $funnel['name'];

                // Prepare steps for each funnel
                if (!empty($funnel['steps'])) {
                    foreach ($funnel['steps'] as $step) {
                        $steps_options[$funnel['id']][$step['id']] = $step['name'];
                    }
                }
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

        // Step Dropdown (Will be dynamically populated)
        $this->add_control(
            'step',
            [
                'label' => __('Step', 'ena-sinappsus-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [],
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Pass steps options to JavaScript
        $this->register_scripts();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $sales_funnel_id = $settings['sales_funnel'];
        $step_id = $settings['step'];

        // Ensure IDs are set
        if (empty($sales_funnel_id) || empty($step_id)) {
            echo __('Please select a Sales Funnel and Step.', 'ena-sinappsus-plugin');
            return;
        }

        // Pass the selected values to the function
        echo handle_sales_funnel_step($sales_funnel_id, $step_id);
    }

    protected function content_template() {}

    private function register_scripts() {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            // Enqueue the script
            wp_enqueue_script('ena-sinappsus-widget-script', plugin_dir_url(__FILE__) . 'ena-sinappsus-widget-script.js', ['jquery'], '1.0', true);

            // Localize the steps data
            $sales_funnels = $this->get_sales_funnels();

            $steps_data = [];

            if (!empty($sales_funnels)) {
                foreach ($sales_funnels as $funnel) {
                    $funnel_id = $funnel['id'];
                    $steps_data[$funnel_id] = [];

                    if (!empty($funnel['steps'])) {
                        foreach ($funnel['steps'] as $step) {
                            $steps_data[$funnel_id][$step['id']] = $step['name'];
                        }
                    }
                }
            }

            wp_localize_script('ena-sinappsus-widget-script', 'enaSinappsusSteps', $steps_data);
        }
    }

    private function get_sales_funnels() {
        $cached_funnels = get_transient('ena_sinappsus_sales_funnels');

        if ($cached_funnels !== false) {
            return $cached_funnels;
        }

        $data = ena_sinappsus_connect_to_api('/sales-funnels');

        if (is_wp_error($data)) {
            return [];
        }

        set_transient('ena_sinappsus_sales_funnels', $data, 12 * HOUR_IN_SECONDS);
        return $data;

    }
}