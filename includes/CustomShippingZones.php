<?php

namespace CustomShippingZones;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

class CustomShippingZones {
    
    public function __construct() {
        add_action( 'before_woocommerce_init', array( $this, 'init' ) );
        add_action( 'load-textdomain', array( $this, 'load_textdomain' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }
    
    public function init(): void {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }
    
    public function load_textdomain(): void {
        load_plugin_textdomain( 'custom-shipping-zones', false, CUSTOM_SHIPPING_ZONES_BASENAME . '/languages' );
    }
    
    public function enqueue_scripts(): void {
        // Let's check if we are on the right page
        $screen = get_current_screen();
        if ( $screen->id !== 'toplevel_page_custom-shipping-zones' ) {
            return;
        }
        wp_enqueue_script( 'custom-shipping-zone-admin', CUSTOM_SHIPPING_ZONES_URL . '/build/index.js', array( 'wp-element' ), CUSTOM_SHIPPING_ZONES_VERSION, true );
    }
    
    public function add_admin_menu(): void {
        add_menu_page(
            __( 'Custom Shipping Zones', 'custom-shipping-zones' ),
            __( 'Custom Shipping Zones', 'custom-shipping-zones' ),
            'manage_options',
            'custom-shipping-zones',
            array( $this, 'render_admin_page' ),
            'dashicons-location-alt',
            99
        );
    }
    
    public function render_admin_page(): void {
        require_once CUSTOM_SHIPPING_ZONES_PATH . 'includes/admin/admin.php';
    }
}
