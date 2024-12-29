<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Ena_Sinappsus_Available_Rooms_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'ena_sinappsus_available_rooms_widget';
    }

    public function get_title()
    {
        return __('ENA Available Rooms', 'ena-sinappsus-plugin');
    }

    public function get_icon()
    {
        return 'fa fa-bed'; // You can change this to any FontAwesome icon
    }

    public function get_categories()
    {
        return ['eagles-nest'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'ena-sinappsus-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'start_date',
            [
                'label' => __('Start Date', 'ena-sinappsus-plugin'),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                    'dateFormat' => 'Y-m-d',
                ],
            ]
        );

        $this->add_control(
            'end_date',
            [
                'label' => __('End Date', 'ena-sinappsus-plugin'),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                    'dateFormat' => 'Y-m-d',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $start_date = $settings['start_date'];
        $end_date = $settings['end_date'];

        if ($start_date && $end_date) {
            $available_rooms = $this->get_available_rooms($start_date, $end_date);

            if (!empty($available_rooms)) {
                echo '<div class="available-rooms">';
                foreach ($available_rooms as $room) {
                    echo '<div class="room-card">';
                    echo '<h3>' . esc_html($room['room']['name']) . '</h3>';
                    if (!empty($room['bookings'])) {
                        echo '<ul>';
                        foreach ($room['bookings'] as $booking) {
                            echo '<li>' . esc_html($booking['start_date']) . ' to ' . esc_html($booking['end_date']) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No bookings during this period.</p>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No available rooms found for the selected dates.</p>';
            }
        } else {
            echo '<p>Please select a start and end date.</p>';
        }
    }

    private function get_available_rooms($start_date, $end_date)
    {
        $data = ena_sinappsus_connect_to_api('/bookings/available-rooms', [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);

        if (empty($data)) {
            return [];
        }

        return $data;
    }

    protected function _content_template() {}
}