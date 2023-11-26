<?php

namespace CustomShippingZones;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use WP_REST_Server;

class CustomShippingZones
{

    public function __construct()
    {
        add_action('before_woocommerce_init', array($this, 'init'));
        add_action('load-textdomain', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_csz_save_states', array($this, 'save_states'));
    }

    public function init(): void
    {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        }
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain('custom-shipping-zones', false, CUSTOM_SHIPPING_ZONES_BASENAME . '/languages');
    }

    public function enqueue_scripts(): void
    {
        // Let's check if we are on the right page
        $screen = get_current_screen();
        if ($screen->id !== 'toplevel_page_custom-shipping-zones') {
            return;
        }
        wp_enqueue_script('custom-shipping-zone-admin', CUSTOM_SHIPPING_ZONES_URL . '/build/index.js', array('wp-element'), CUSTOM_SHIPPING_ZONES_VERSION, true);

        wp_localize_script('custom-shipping-zone-admin', 'cszData', array(
            'states' => $this->get_zones()
        ));

        wp_localize_script('custom-shipping-zone-admin', 'cszAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('csz_nonce')
        ));
    }

    public function add_admin_menu(): void
    {
        add_menu_page(
            __('Custom Shipping Zones', 'custom-shipping-zones'),
            __('Custom Shipping Zones', 'custom-shipping-zones'),
            'manage_options',
            'custom-shipping-zones',
            array($this, 'render_admin_page'),
            'dashicons-location-alt',
            99
        );
    }

    public function render_admin_page(): void
    {
        require_once CUSTOM_SHIPPING_ZONES_PATH . 'includes/admin/admin.php';
    }

    public function get_zones()
    {

        $states = WC()->countries->get_countries();

        return new \WP_REST_Response($states, 200);
    }

    public function save_states()
    {

        if (current_user_can('manage_woocommerce') === false) {
            wp_send_json_error('Not allowed!');
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'csz_nonce')) {
            wp_send_json_error('Nonce verification failed');
        }

        $states = isset($_POST['states']) ? stripslashes($_POST['states']) : array();
        $states = json_decode($states, true);
        $countryCode = isset($_POST['countryCode']) ? sanitize_text_field($_POST['countryCode']) : '';

        $statesFormatted = array();

        foreach ($states as $state) {
            $stateCode = $state['code'];
            $stateName = $state['name'];
            $statesFormatted[$countryCode][$stateCode] = __($stateName, 'woocommerce');
        }

        update_option('custom_shipping_zones', $statesFormatted);
        wp_send_json_success();
    }
}
