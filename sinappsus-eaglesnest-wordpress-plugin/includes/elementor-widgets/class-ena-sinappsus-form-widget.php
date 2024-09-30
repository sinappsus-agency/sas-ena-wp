<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Ena_Sinappsus_Form_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ena_sinappsus_form_widget';
    }

    public function get_title() {
        return __( 'ENA Sinappsus Form', 'ena-sinappsus-plugin' );
    }

    public function get_icon() {
        return 'fa fa-address-card'; // You can change this to any FontAwesome icon
    }

    public function get_categories() {
        return [ 'eagles-nest' ];
    }

    protected function _register_controls() {
        // Add controls here if needed
    }

    protected function render() {
        echo do_shortcode( '[ena_sinappsus_form]' );
    }

    protected function _content_template() {}
}
