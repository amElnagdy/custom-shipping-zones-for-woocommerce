<?php

namespace CustomShippingZones;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

class CustomShippingZones
{

    public function __construct()
    {
        add_action('before_woocommerce_init', array($this, 'init'));
        add_action('load-textdomain', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_csz_save_states', array($this, 'save_states'));
        add_action('wp_ajax_csz_delete_state', array($this, 'delete_state'));
        add_filter('woocommerce_states', array($this, 'modify_woocommerce_states'));
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
            'countries' => $this->get_countries(),
            'current_custom_zones' => $this->get_custom_shipping_zones()
        ));

        wp_localize_script('custom-shipping-zone-admin', 'cszAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('csz_nonce')
        ));

        wp_localize_script('custom-shipping-zone-admin', 'cszStrings', $this->get_strings());
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

    public function get_countries()
    {

        $countries = WC()->countries->get_countries();

        return new \WP_REST_Response($countries, 200);
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
            $statesFormatted[$stateCode] = __($stateName, 'woocommerce');
        }

        $optionName = strtolower($countryCode) . '_custom_shipping_zones';
        $existingStates = get_option($optionName) ?: array();

        // Update the existing states with new states
        $updatedStates = array_merge($existingStates, $statesFormatted);

        // Update the option
        update_option($optionName, $updatedStates);

        wp_send_json_success();
    }

    public function get_custom_shipping_zones()
    {
        $countries = WC()->countries->get_countries();
        $customShippingZones = array();

        foreach ($countries as $countryCode => $countryName) {
            $optionName = strtolower($countryCode) . '_custom_shipping_zones';
            if (get_option($optionName)) {
                $customShippingZones[$countryCode] = get_option($optionName);
            }
        }

        return $customShippingZones;
    }

    public function delete_state()
    {
        if (current_user_can('manage_woocommerce') === false) {
            wp_send_json_error('Not allowed!');
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'csz_nonce')) {
            wp_send_json_error('Nonce verification failed');
        }

        $countryCode = isset($_POST['countryCode']) ? sanitize_text_field($_POST['countryCode']) : '';
        $stateCode = isset($_POST['stateCode']) ? sanitize_text_field($_POST['stateCode']) : '';

        $optionName = strtolower($countryCode) . '_custom_shipping_zones';
        $existingStates = get_option($optionName) ?: array();

        unset($existingStates[$stateCode]);

        update_option($optionName, $existingStates);

        wp_send_json_success();
    }

    public function modify_woocommerce_states($states)
    {
        $customShippingZones = $this->get_custom_shipping_zones();

        foreach ($customShippingZones as $countryCode => $zones) {
            if (!empty($zones)) {
                foreach ($zones as $zoneCode => $zoneName) {
                    $states[$countryCode][$zoneCode] = $zoneName;
                }
            }
        }

        return $states;
    }

    public function get_strings()
    {
        return Strings::strings();
    }
}
