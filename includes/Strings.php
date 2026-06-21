<?php

namespace ANCSZ\CustomShippingZones;

class Strings
{
    static public function strings(): array
    {
        return [
            'these_states_will_be_added' => __('These states will be added to ', 'custom-shipping-zones-for-woocommerce'),
            'delete' => __('Delete', 'custom-shipping-zones-for-woocommerce'),
            'states_saved_successfully' => __('States saved successfully', 'custom-shipping-zones-for-woocommerce'),
            'reload_page' => __('Reload page and add new states/regions', 'custom-shipping-zones-for-woocommerce'),
            'save_states' => __('Save states', 'custom-shipping-zones-for-woocommerce'),
            'select_country' => __('Select country', 'custom-shipping-zones-for-woocommerce'),
            'state_deleted' => __('State deleted successfully', 'custom-shipping-zones-for-woocommerce'),
            'failed_to_delete_state' => __('Failed to delete state', 'custom-shipping-zones-for-woocommerce'),
            'an_error_occurred' => __('An error occurred while deleting the state', 'custom-shipping-zones-for-woocommerce'),
            'invalid_country'       => __('That country is not recognized. Nothing was saved.', 'custom-shipping-zones-for-woocommerce'),
            'invalid_state_code'    => __('State codes may use only letters, numbers and hyphens (max 10 characters).', 'custom-shipping-zones-for-woocommerce'),
            'failed_to_save_states' => __('Failed to save states. Please check your entries and try again.', 'custom-shipping-zones-for-woocommerce'),
            'country' => __('Country', 'custom-shipping-zones-for-woocommerce'),
            'state' => __('State', 'custom-shipping-zones-for-woocommerce'),
            'code' => __('Code', 'custom-shipping-zones-for-woocommerce'),
            'action' => __('Action', 'custom-shipping-zones-for-woocommerce'),
            'current_custom_shipping_zones' => __('Your Existing Custom States / Regions', 'custom-shipping-zones-for-woocommerce'),
            'please_enter_state_name' => __('Please enter a state name', 'custom-shipping-zones-for-woocommerce'),
            'state_name' => __('State name', 'custom-shipping-zones-for-woocommerce'),
            'please_enter_state_code' => __('Please enter a state code', 'custom-shipping-zones-for-woocommerce'),
            'state_codes_are_auto_generated' => __('State codes are automatically generated.', 'custom-shipping-zones-for-woocommerce'),
            'state_code' => __('State code', 'custom-shipping-zones-for-woocommerce'),
            'add_state' => __('Add state', 'custom-shipping-zones-for-woocommerce'),
            'navigate_to_woocommerce_settings' => __('Now you can go to WooCommerce → Settings → Shipping to use the newly added custom states/regions.', 'custom-shipping-zones-for-woocommerce'),
            'state_is_in_use' => __('This state is currently being used in a shipping zone. Please remove it from the shipping zones before deleting.', 'custom-shipping-zones-for-woocommerce'),
            'no_custom_zones_to_export' => __('There are no custom zones to export.', 'custom-shipping-zones-for-woocommerce'),
            'custom_zones_exported' => __('Custom zones exported successfully.', 'custom-shipping-zones-for-woocommerce'),
            'export_custom_zones' => __('Export custom zones', 'custom-shipping-zones-for-woocommerce'),
            'export_import_divider' => __('Export / Import', 'custom-shipping-zones-for-woocommerce'),
            'search_text' => __('Search by country, state or code', 'custom-shipping-zones-for-woocommerce'),
            'export_description' => __('Export your custom shipping zones to a file that can be exported on a different website.', 'custom-shipping-zones-for-woocommerce'),
            'custom_zones_imported' => __('Custom zones imported successfully.', 'custom-shipping-zones-for-woocommerce'),
            'failed_to_import_custom_zones' => __('Failed to import custom zones.', 'custom-shipping-zones-for-woocommerce'),
            'or' => __('or', 'custom-shipping-zones-for-woocommerce'),
            'import_custom_zones' => __('Import custom zones', 'custom-shipping-zones-for-woocommerce'),
            'import_description' => __('Import your custom shipping zones from a file that was exported from a different website.', 'custom-shipping-zones-for-woocommerce'),
            'import_warning' => __('Warning: Importing will overwrite all existing custom shipping zones.', 'custom-shipping-zones-for-woocommerce'),
            'faq_whats_code' => __('What is the code?', 'custom-shipping-zones-for-woocommerce'),
            'faq_code_description' => __('The code is a unique identifier for the state. It is automatically generated when you add a new state.', 'custom-shipping-zones-for-woocommerce'),
            'faq_cant_delete' => __('Why can\'t I delete a custom state/region?', 'custom-shipping-zones-for-woocommerce'),
            'faq_cant_delete_description' => __('You cannot delete a custom state/region while it is being used in a WooCommerce shipping zone. Remove it from the shipping zone first, then delete it here.', 'custom-shipping-zones-for-woocommerce'),

            'faq_woocommerce_settings' => __('How do I use my custom states/regions in WooCommerce?', 'custom-shipping-zones-for-woocommerce'),
            'faq_woocommerce_settings_description' => __('After adding your custom states/regions, go to WooCommerce → Settings → Shipping to use them in a shipping zone (they also appear in tax and address settings).', 'custom-shipping-zones-for-woocommerce'),
            'faq_rate_plugin' => __('How do I rate this plugin?', 'custom-shipping-zones-for-woocommerce'),
            /* translators: %s: A link to the plugin review page */
            'faq_rate_plugin_description' => sprintf(__('If you like this plugin, please <a href="%s" target="_blank">leave a 5-star review on WordPress.org</a>. It helps us a lot!', 'custom-shipping-zones-for-woocommerce'), 'https://wordpress.org/support/plugin/custom-shipping-zones-for-woocommerce/reviews/#new-post'),
            'faq_donate' => __('How do I buy the developer a coffee?', 'custom-shipping-zones-for-woocommerce'),
            'faq_donate_description' => __('If you like this plugin, please <a href="https://ko-fi.com/nagdy" target="_blank no-referrer no-opener" style="color: green;">buy me a coffee</a>. It helps me a lot!', 'custom-shipping-zones-for-woocommerce'),
            'do_you_have_other_plugins' => __('Do you have other plugins?', 'custom-shipping-zones-for-woocommerce'),
            'do_you_have_other_plugins_description' => __('Yes, I have other plugins. You can find them <a href="https://profiles.wordpress.org/nagdy/#content-plugins" target="_blank no-referrer no-opener" style="color: green;">here</a>.', 'custom-shipping-zones-for-woocommerce'),
        ];
    }
}
