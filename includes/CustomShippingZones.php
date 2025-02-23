<?php
namespace ANCSZ\CustomShippingZones;

class CustomShippingZones
{

    public function __construct()
    {
        // Load the text domain
        add_action('load-textdomain', array($this, 'load_textdomain'));

        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // AJAX actions. Check if admin and doing ajax
        if (is_admin() && defined('DOING_AJAX') && DOING_AJAX) {
            add_action('wp_ajax_csz_save_states', array($this, 'save_states'));
            add_action('wp_ajax_csz_delete_state', array($this, 'delete_state'));
        }

        add_filter('woocommerce_states', array($this, 'modify_woocommerce_states'));

        add_filter('plugin_action_links_' . ANCSZ_CUSTOM_SHIPPING_ZONES_BASENAME, array($this, 'settings_link'));

        // Add the settings tab
        add_action('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_settings_tabs_custom_shipping_zones', array($this, 'settings_tab'));
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain('custom-shipping-zones', false, ANCSZ_CUSTOM_SHIPPING_ZONES_BASENAME . '/languages');
    }

    public function enqueue_scripts(): void
    {
        // Check if we are on the WooCommerce settings page for 'custom_shipping_zones'
        if (!isset($_GET['page']) || $_GET['page'] !== 'wc-settings' || !isset($_GET['tab']) || $_GET['tab'] !== 'custom_shipping_zones') {
            return;
        }

        // Enqueue custom admin script for handling shipping zones
        wp_enqueue_script('custom-shipping-zone-admin', ANCSZ_CUSTOM_SHIPPING_ZONES_URL . '/build/index.js', array('wp-element'), ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION, true);

        // Localize script for dynamic data about countries and zones
        wp_localize_script('custom-shipping-zone-admin', 'cszData', array(
            'countries' => $this->get_countries(),
            'current_custom_zones' => $this->get_custom_shipping_zones()
        ));

        // Localize script for AJAX operations
        wp_localize_script('custom-shipping-zone-admin', 'cszAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('csz_nonce')
        ));

        // Localize script for strings used in JavaScript
        wp_localize_script('custom-shipping-zone-admin', 'cszStrings', $this->get_strings());

        // Hide the WooCommerce save button
        wp_register_style('custom-shipping-zone-style', false, [], ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION);
        wp_enqueue_style('custom-shipping-zone-style');
        wp_add_inline_style('custom-shipping-zone-style', '.woocommerce-save-button { display: none !important; }');
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

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'csz_nonce')) {
            wp_send_json_error('Nonce verification failed');
        }

        $states_json = isset($_POST['states']) ? sanitize_text_field(stripslashes($_POST['states'])) : '[]';
        $states = json_decode($states_json, true);
        $countryCode = isset($_POST['countryCode']) ? sanitize_text_field($_POST['countryCode']) : '';

        $statesFormatted = array();

        foreach ($states as $state) {
            $stateCode = $state['code'];
            $stateName = $state['name'];
            $statesFormatted[$stateCode] = $stateName;
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

        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'csz_nonce')) {
            wp_send_json_error('Nonce verification failed');
        }

        $countryCode = isset($_POST['countryCode']) ? sanitize_text_field(wp_unslash($_POST['countryCode'])) : '';
        $stateCode = isset($_POST['stateCode']) ? sanitize_text_field(wp_unslash($_POST['stateCode'])) : '';

        // Check if the state is being used in any shipping zone
        if ($this->is_state_in_use($countryCode, $stateCode)) {
            wp_send_json_error('state_is_in_use');
        }

        $optionName = strtolower($countryCode) . '_custom_shipping_zones';
        $existingStates = get_option($optionName) ?: array();

        unset($existingStates[$stateCode]);

        update_option($optionName, $existingStates);

        wp_send_json_success();
    }

    /**
     * Modifies the WooCommerce states array by adding custom shipping zones.
     *
     * @param array $states The array of WooCommerce states.
     * @return array The modified array of WooCommerce states.
     */
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

    public function add_settings_tab($settings_tabs)
    {
        $settings_tabs['custom_shipping_zones'] = __('Custom Shipping Zones', 'custom-shipping-zones');
        return $settings_tabs;
    }

    public function settings_tab(): void
    {
        require_once ANCSZ_CUSTOM_SHIPPING_ZONES_PATH . 'includes/admin/admin.php';
    }

    private function is_state_in_use($countryCode, $stateCode)
    {
        $shipping_zones = \WC_Shipping_Zones::get_zones();
        foreach ($shipping_zones as $zone) {
            // Check the zone locations
            foreach ($zone['zone_locations'] as $location) {
                $location_parts = explode(':', $location->code);
                if (count($location_parts) > 1) {
                    $location_country_code = $location_parts[0];
                    $location_state_code = $location_parts[1];
                    if ($location_country_code === $countryCode && $location_state_code === $stateCode) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function settings_link($links)
    {
        $donate_link = '<a href="https://ko-fi.com/nagdy" target="_blank no-referrer no-opener" style="color: green;">' . __('Donate', 'custom-shipping-zones') . '</a>';
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=custom_shipping_zones')) . '">' . __('Settings', 'custom-shipping-zones') . '</a>';
        array_unshift($links, $settings_link, $donate_link);
        return $links;
    }
}
