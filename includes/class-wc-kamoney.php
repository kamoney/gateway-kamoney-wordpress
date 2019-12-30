<?php
/**
 * WooCommerce Kamoney main class
 *
 * @package WooCommerce_Kamoney
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * WooCommerce bootstrap class.
 */
class WC_Gateway_Kamoney
{

    /**
     * Initialize the plugin public actions.
     */
    public static function init()
    {
        // Checks with WooCommerce is installed.
        if (class_exists('WC_Payment_Gateway')) {
            self::includes();

            add_filter('woocommerce_payment_gateways', array(__CLASS__, 'add_gateway'));
            //add_filter('plugin_action_links_' . plugin_basename(WC_KAMONEY_PLUGIN_FILE), array(__CLASS__, 'plugin_action_links'));
            
        } else {
            add_action('admin_notices', array(__CLASS__, 'woocommerce_missing_notice'));
        }
    }

    /**
     * Action links.
     *
     * @param array $links Action links.
     *
     * @return array
     */
    public static function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=kamoney_payment')) . '">Configurações</a>';

        return array_merge($plugin_links, $links);
    }

    /**
     * Includes.
     */
    private static function includes()
    {
        include_once dirname(__FILE__) . '/class-kamoney.php';
        include_once dirname(__FILE__) . '/class-wc-kamoney-gateway.php';
    }

    /**
     * Add the gateway to WooCommerce.
     *
     * @param  array $methods WooCommerce payment methods.
     *
     * @return array          Payment methods with PagSeguro.
     */
    public static function add_gateway($methods)
    {
        $methods[] = 'WC_Kamoney_Gateway';

        return $methods;
    }

    /**
     * WooCommerce missing notice.
     */
    public static function woocommerce_missing_notice()
    {
        include dirname(__FILE__) . '/admin/views/html-notice-missing-woocommerce.php';
    }
}
