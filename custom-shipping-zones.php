<?php

/**
 * Plugin Name: Custom Shipping Zones for WooCommerce
 * Description: Lightweight WooCommerce extension that lets you add custom states/regions to any country for use in WooCommerce shipping zones, taxes, and address fields.
 * Version: 1.0.3
 * Author: Nagdy
 * Author URI: https://nagdy.me
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: custom-shipping-zones
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Requires Plugins: woocommerce
 * WC requires at least: 9.0
 * WC tested up to: 9.7
 */

use ANCSZ\CustomShippingZones\CustomShippingZones;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
const ANCSZ_CUSTOM_SHIPPING_ZONES_VERSION = '1.0.3';
define('ANCSZ_CUSTOM_SHIPPING_ZONES_PATH', plugin_dir_path(__FILE__));
define('ANCSZ_CUSTOM_SHIPPING_ZONES_URL', plugin_dir_url(__FILE__));
define('ANCSZ_CUSTOM_SHIPPING_ZONES_BASENAME', plugin_basename(__FILE__));

require_once 'vendor/autoload.php';


new CustomShippingZones();

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});
