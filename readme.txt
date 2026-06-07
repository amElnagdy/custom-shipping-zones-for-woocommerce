=== Custom Shipping Zones for WooCommerce ===
Contributors: nagdy
Tags: shipping, woocommerce, custom shipping zones
Requires PHP: 7.4
Requires at least: 6.0
WC requires at least: 9.0
WC tested up to: 9.7
Tested up to: 7.0
Stable tag: 1.0.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add custom states and regions to any country so they become available when you set up WooCommerce shipping zones, taxes, and addresses.

== Description ==

Custom Shipping Zones for WooCommerce lets you add custom states/regions to any country. WooCommerce ships with a fixed list of states for each country — this plugin lets you define your own (for example a district, governorate, county, or any sub-region). Once added, your custom states appear in WooCommerce's own state list, so you can target them when building shipping zones, setting up tax rates, or restricting selling and shipping locations.

This plugin does not replace WooCommerce's shipping zone manager — it feeds it. You keep using WooCommerce → Settings → Shipping exactly as before; you simply have extra states/regions to choose from.

### Features

- Add custom states/regions to any WooCommerce country.
- Manage and delete your custom states/regions from a dedicated WooCommerce settings tab.
- Your custom states appear anywhere WooCommerce uses states — shipping zones, tax rates, and address fields.
- Lightweight: no extra database tables; it uses WooCommerce's official state list.

## Usage

### Adding custom states/regions

1. Go to WooCommerce → Settings → Custom States / Regions.
2. Select a country and add one or more custom states/regions (each gets an auto-generated code).
3. Save. Your new states are now part of WooCommerce's state list for that country.

### Using them in WooCommerce

- Go to WooCommerce → Settings → Shipping and add or edit a zone — your custom states are now selectable there (and in tax and address settings).
- Return to the Custom States / Regions tab anytime to add or delete states/regions.

== Installation ==
1. Upload the `custom-shipping-zones` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Add and manage your custom states/regions via WooCommerce → Settings → Custom States / Regions, then use them under WooCommerce → Settings → Shipping.

== Frequently Asked Questions ==
= What does this plugin actually add? =
It adds custom states/regions to a country. WooCommerce ships with a fixed list of states per country; this plugin lets you add your own (a district, county, governorate, or any sub-region) so they appear in WooCommerce's state list.

= How do I use my custom states in a shipping zone? =
First add your states/regions under WooCommerce → Settings → Custom States / Regions. Then go to WooCommerce → Settings → Shipping and create or edit a zone — your custom states are now selectable, just like WooCommerce's built-in ones.

= Does this replace WooCommerce shipping zones? =
No. It does not manage shipping zones for you. It only adds the states/regions that you then use inside WooCommerce's own shipping zone (and tax/address) settings.

== Screenshots ==
1. Interface for adding a new custom state/region.
2. Managing existing custom states/regions.

== Changelog ==

= 1.0.3 =
* Declared WooCommerce as a required plugin dependency.
* Declared minimum PHP 7.4 and WooCommerce 9.0 in the plugin header.
* WordPress 7.0 compatibility (Tested up to: 7.0).
* Security: enforce nonce-before-capability ordering on admin AJAX handlers.
* Validation: reject unknown country codes and malformed state codes server-side.
* Fixed: the save screen no longer reports success when the server rejects a save.
* Removed stray debug console output on the settings screen.
* Performance: cache custom region lookups for the duration of a request so the
  woocommerce_states filter no longer re-queries the database on every invocation.
  Cache is cleared automatically when a custom region is saved or deleted.
* Repositioned the plugin description and admin labels to accurately reflect what it does: adding custom states/regions to a country for use in WooCommerce shipping zones, taxes, and addresses.
* Removed the "export / import" FAQ entry for a feature that is not yet available.

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
