<?php

namespace ANCSZ\CustomShippingZones;

class Strings
{
    static public function strings(): array
    {
        return [
            'these_states_will_be_added' => __('These states will be added to ', 'custom-shipping-zones'),
            'delete' => __('Delete', 'custom-shipping-zones'),
            'states_saved_successfully' => __('States saved successfully', 'custom-shipping-zones'),
            'reload_page' => __('Reload page and add new zones', 'custom-shipping-zones'),
            'save_states' => __('Save states', 'custom-shipping-zones'),
            'select_country' => __('Select country', 'custom-shipping-zones'),
            'state_deleted' => __('State deleted successfully', 'custom-shipping-zones'),
            'failed_to_delete_state' => __('Failed to delete state', 'custom-shipping-zones'),
            'an_error_occurred' => __('An error occurred while deleting the state', 'custom-shipping-zones'),
            'country' => __('Country', 'custom-shipping-zones'),
            'state' => __('State', 'custom-shipping-zones'),
            'code' => __('Code', 'custom-shipping-zones'),
            'action' => __('Action', 'custom-shipping-zones'),
            'current_custom_shipping_zones' => __('Your Existing Custom Shipping Zones', 'custom-shipping-zones'),
            'please_enter_state_name' => __('Please enter a state name', 'custom-shipping-zones'),
            'state_name' => __('State name', 'custom-shipping-zones'),
            'please_enter_state_code' => __('Please enter a state code', 'custom-shipping-zones'),
            'state_codes_are_auto_generated' => __('State codes are automatically generated.', 'custom-shipping-zones'),
            'state_code' => __('State code', 'custom-shipping-zones'),
            'add_state' => __('Add state', 'custom-shipping-zones'),
            'navigate_to_woocommerce_settings' => __('Now you can go to WooCommerce → Settings → Shipping to use the newly added custom zones.', 'custom-shipping-zones'),
            'state_is_in_use' => __('This state is currently being used in a shipping zone. Please remove it from the shipping zones before deleting.', 'custom-shipping-zones'),
            'no_custom_zones_to_export' => __('There are no custom zones to export.', 'custom-shipping-zones'),
            'custom_zones_exported' => __('Custom zones exported successfully.', 'custom-shipping-zones'),
            'export_custom_zones' => __('Export custom zones', 'custom-shipping-zones'),
            'export_import_divider' => __('Export / Import', 'custom-shipping-zones'),
            'search_text' => __('Search by country, state or code', 'custom-shipping-zones'),
            'export_description' => __('Export your custom shipping zones to a file that can be exported on a different website.', 'custom-shipping-zones'),
            'custom_zones_imported' => __('Custom zones imported successfully.', 'custom-shipping-zones'),
            'failed_to_import_custom_zones' => __('Failed to import custom zones.', 'custom-shipping-zones'),
            'or' => __('or', 'custom-shipping-zones'),
            'import_custom_zones' => __('Import custom zones', 'custom-shipping-zones'),
            'import_description' => __('Import your custom shipping zones from a file that was exported from a different website.', 'custom-shipping-zones'),
            'import_warning' => __('Warning: Importing will overwrite all existing custom shipping zones.', 'custom-shipping-zones'),
            'faq_whats_code' => __('What is the code?', 'custom-shipping-zones'),
            'faq_code_description' => __('The code is a unique identifier for the state. It is automatically generated when you add a new state.', 'custom-shipping-zones'),
            'faq_cant_delete' => __('Why can\'t I delete a custom shipping zone?', 'custom-shipping-zones'),
            'faq_cant_delete_description' => __('You cannot delete a custom shipping zone if it is being used in a shipping zone. Remove it from the shipping zone first.', 'custom-shipping-zones'),
            'faq_export_import' => __('How do I export / import custom shipping zones?', 'custom-shipping-zones'),
            'faq_export_import_description' => __('Export/import functionality will be supported in a future update, allowing you to easily transfer settings between stores. For the time being, you\'ll need to create them on the other website.', 'custom-shipping-zones'),
            'faq_woocommerce_settings' => __('How do I use the custom shipping zones in WooCommerce?', 'custom-shipping-zones'),
            'faq_woocommerce_settings_description' => __('After adding custom shipping zones, go to WooCommerce → Settings → Shipping to use the newly added zones.', 'custom-shipping-zones'),
            'faq_rate_plugin' => __('How do I rate this plugin?', 'custom-shipping-zones'),
            /* translators: %s: A link to the plugin review page */
            'faq_rate_plugin_description' => sprintf(__('If you like this plugin, please <a href="%s" target="_blank">leave a 5-star review on WordPress.org</a>. It helps us a lot!', 'custom-shipping-zones'), 'https://wordpress.org/support/plugin/custom-shipping-zones-for-woocommerce/reviews/#new-post'),
            'faq_donate' => __('How do I buy the developer a coffee?', 'custom-shipping-zones'),
            'faq_donate_description' => __('If you like this plugin, please <a href="https://ko-fi.com/nagdy" target="_blank no-referrer no-opener" style="color: green;">buy me a coffee</a>. It helps me a lot!', 'custom-shipping-zones'),
            'do_you_have_other_plugins' => __('Do you have other plugins?', 'custom-shipping-zones'),
            'do_you_have_other_plugins_description' => __('Yes, I have other plugins. You can find them <a href="https://profiles.wordpress.org/nagdy/#content-plugins" target="_blank no-referrer no-opener" style="color: green;">here</a>.', 'custom-shipping-zones'),
        ];
    }
}
