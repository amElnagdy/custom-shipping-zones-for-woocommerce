=== Custom Shipping Zones for WooCommerce ===
Contributors: nagdy
Tags: shipping, woocommerce, custom shipping zones
Requires PHP: 7.4
Requires at least: 6.0
WC requires at least: 9.0
WC tested up to: 9.7
Tested up to: 7.0
Stable tag: 1.0.5
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A powerful tool for WooCommerce store owners to create custom shipping zones beyond the default zones provided by WooCommerce.

== Description ==

Custom Shipping Zones for WooCommerce allows store owners to extend the flexibility of WooCommerce shipping zones. With this plugin, you can create detailed shipping zones based on specific criteria that are not supported out of the box by WooCommerce.

### Features

- Create custom shipping zones tailored to specific needs.
- Easily manage and edit custom zones from the WooCommerce settings panel.
- Integrate seamlessly with existing WooCommerce shipping methods.

## Usage

### Adding Custom Shipping Zones

1. Navigate to WooCommerce → Settings → Custom Shipping Zones.
2. Use the intuitive interface to define new shipping zones based on your specific requirements.
3. Assign shipping methods and rates to your custom zones.

### Managing Zones

- Visit the Custom Shipping Zones tab to edit or delete existing zones.
- Adjust zone settings and shipping methods as needed to adapt to changing business needs.

== Installation ==
1. Upload the `custom-shipping-zones` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Configure and manage your shipping zones via WooCommerce → Settings → Custom Shipping Zones tab.

== Frequently Asked Questions ==
= What is a custom shipping zone? =
A custom shipping zone is a geographical area where specific shipping methods and rates apply. This plugin allows you to define these areas based on unique criteria not typically handled by default WooCommerce settings.

= How do I set up a new shipping zone? =
Go to WooCommerce → Settings → Custom Shipping Zones and define your zones with custom criteria and shipping methods.

== Screenshots ==
1. Interface for adding a new custom shipping zone.
2. Managing existing custom zones.

== Changelog ==

= 1.0.5 =
* Performance: cache custom region lookups for the duration of a request so the
  woocommerce_states filter no longer re-queries the database on every invocation.
* Cache is cleared automatically when a custom region is saved or deleted.

= 1.0.4 =
* Security: enforce nonce-before-capability ordering on admin AJAX handlers.
* Validation: reject unknown country codes and malformed state codes server-side.
* Fixed: the save screen no longer reports success when the server rejects a save.
* Removed stray debug console output on the settings screen.

= 1.0.3 =
* Declared WooCommerce as a required plugin dependency.
* Declared minimum PHP 7.4 and WooCommerce 9.0 in the plugin header.
* WordPress 7.0 compatibility (Tested up to: 7.0).

= 1.0.2 =
* WordPress 6.9 compatibility.
* WooCommerce 9.7 compatibility.

= 1.0.1 =
* WordPress 6.7 compatibility.
* Added new FAQ sections to the plugin settings page.

= 1.0.0 =
* Initial Release
* Added the ability to create and manage custom shipping zones.
* Integrated with WooCommerce settings for easy access.
