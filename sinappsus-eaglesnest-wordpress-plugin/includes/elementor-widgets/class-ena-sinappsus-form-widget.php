<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ena_Sinappsus_Form_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ena_sinappsus_form_widget';
    }

    public function get_title() {
        return __('ENA Form', 'ena-sinappsus-plugin');
    }

    public function get_icon() {
        return 'fa fa-wpforms'; // You can change this to any FontAwesome icon
    }

    public function get_categories() {
        return ['eagles-nest'];
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
            'funnel_id',
            [
                'label' => __('Sales Funnel', 'ena-sinappsus-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $funnel_options,
                'default' => '',
            ]
        );

        // Toggle visibility for each form field
        $fields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'profile_picture' => 'Profile Picture',
            'dob' => 'Date of Birth',
            'address.street_line_01' => 'Street Line 01',
            'address.street_line_02' => 'Street Line 02',
            'address.street_line_03' => 'Street Line 03',
            'address.city' => 'City',
            'address.state' => 'State',
            'address.zip' => 'Zip',
            'address.country' => 'Country',
        ];

        foreach ($fields as $field => $label) {
            $this->add_control(
                $field . '_visible',
                [
                    'label' => __('Show ' . $label, 'ena-sinappsus-plugin'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => __('Show', 'ena-sinappsus-plugin'),
                    'label_off' => __('Hide', 'ena-sinappsus-plugin'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                ]
            );
        }

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $funnel_id = $settings['funnel_id'];

        if (empty($funnel_id)) {
            echo __('Please select a Sales Funnel.', 'ena-sinappsus-plugin');
            return;
        }

        $funnel_data = $this->get_funnel_data($funnel_id);
        if (empty($funnel_data)) {
            echo __('Invalid Sales Funnel ID.', 'ena-sinappsus-plugin');
            return;
        }

        $crm_tunnel_id = $funnel_data['crm_tunnel_id'];
        $user_persona_id = $funnel_data['user_persona_id'];

        $fields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'profile_picture' => 'Profile Picture',
            'dob' => 'Date of Birth',
            'address.street_line_01' => 'Street Line 01',
            'address.street_line_02' => 'Street Line 02',
            'address.street_line_03' => 'Street Line 03',
            'address.city' => 'City',
            'address.state' => 'State',
            'address.zip' => 'Zip',
            'address.country' => 'Country',
        ];

        echo '<form method="post" action="">';
        wp_nonce_field('ena_sinappsus_form_nonce', 'ena_sinappsus_nonce');
        echo '<input type="hidden" name="funnel_id" value="' . esc_attr($funnel_id) . '">';
        echo '<input type="hidden" name="crm_tunnel_id" value="' . esc_attr($crm_tunnel_id) . '">';
        echo '<input type="hidden" name="user_persona_id" value="' . esc_attr($user_persona_id) . '">';

        foreach ($fields as $field => $label) {
            if ($settings[$field . '_visible'] === 'yes') {
                echo '<label for="' . esc_attr($field) . '">' . esc_html($label) . ':</label>';
                echo '<input type="text" id="' . esc_attr($field) . '" name="' . esc_attr($field) . '">';
            }
        }

        echo '<input type="submit" name="ena_sinappsus_submit" value="Submit">';
        echo '</form>';

        if (isset($_POST['ena_sinappsus_submit']) && check_admin_referer('ena_sinappsus_form_nonce', 'ena_sinappsus_nonce')) {
            $data = [
                'funnel_id' => sanitize_text_field($_POST['funnel_id']),
                'crm_tunnel_id' => sanitize_text_field($_POST['crm_tunnel_id']),
                'user_persona_id' => sanitize_text_field($_POST['user_persona_id']),
            ];

            foreach ($fields as $field => $label) {
                if ($settings[$field . '_visible'] === 'yes') {
                    $data[$field] = sanitize_text_field($_POST[$field]);
                }
            }

            $response = ena_sinappsus_connect_to_api('/funnel', $data, 'POST');
            if ($response && isset($response['success']) && $response['success']) {
                echo '<p>Form submitted successfully!</p>';
            } else {
                echo '<p>Failed to submit form.</p>';
            }
        }
    }

    private function get_sales_funnels() {
        $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels');

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    private function get_funnel_data($funnel_id) {
        $response = wp_remote_get(ENA_SINAPPSUS_API_URL . '/salesfunnels/' . $funnel_id);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }
}