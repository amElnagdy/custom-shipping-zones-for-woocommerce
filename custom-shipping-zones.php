<?php

/**
 * Plugin Name: Custom Shipping Zones for WooCommerce
 * Description: Lightweight, yet powerful WooCommerce extension that allows you to add custom shipping zones to WooCommerce
 * Version: 1.0.0
 * Author: Nagdy
 * Author URI: https://nagdy.me
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: custom-shipping-zones
 * Domain Path: /languages
 * WC tested up to: 8.3
 */

use CustomShippingZones\CustomShippingZones;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
const CUSTOM_SHIPPING_ZONES_VERSION = '1.0.0';
define('CUSTOM_SHIPPING_ZONES_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_SHIPPING_ZONES_URL', plugin_dir_url(__FILE__));
define('CUSTOM_SHIPPING_ZONES_BASENAME', plugin_basename(__FILE__));

require_once 'vendor/autoload.php';


new CustomShippingZones();

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});
