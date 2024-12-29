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

        $this->end_controls_section();
    }

    protected function render()
    {
        ?>
        <div class="available-rooms-widget">
            <form id="available-rooms-form">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
                <button type="submit">Check Availability</button>
            </form>
            <div id="available-rooms-results"></div>
        </div>
        <script>
            document.getElementById('available-rooms-form').addEventListener('submit', function(event) {
                event.preventDefault();
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                fetch(`<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=get_available_rooms&start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        const resultsDiv = document.getElementById('available-rooms-results');
                        resultsDiv.innerHTML = '';
                        if (data.success && data.data.length > 0) {
                            data.data.forEach(room => {
                                const roomCard = document.createElement('div');
                                roomCard.classList.add('room-card');
                                roomCard.innerHTML = `<h3>${room.room.name}</h3>`;
                                if (room.bookings && room.bookings.length > 0) {
                                    const bookingsList = document.createElement('ul');
                                    room.bookings.forEach(booking => {
                                        const bookingItem = document.createElement('li');
                                        bookingItem.textContent = `${booking.start_date} to ${booking.end_date}`;
                                        bookingsList.appendChild(bookingItem);
                                    });
                                    roomCard.appendChild(bookingsList);
                                } else {
                                    roomCard.innerHTML += '<p>No bookings during this period.</p>';
                                }
                                resultsDiv.appendChild(roomCard);
                            });
                        } else {
                            resultsDiv.innerHTML = '<p>No available rooms found for the selected dates.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching available rooms:', error);
                    });
            });
        </script>
        <style>
        .available-rooms {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .room-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            width: calc(33.333% - 20px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .room-card h3 {
            margin-top: 0;
        }

        .room-card ul {
            list-style: none;
            padding: 0;
        }

        .room-card ul li {
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-bottom: 8px;
            padding: 8px;
        }
        </style>
        <?php
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