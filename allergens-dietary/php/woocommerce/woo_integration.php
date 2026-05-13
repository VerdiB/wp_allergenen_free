<?php

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Allergens_Dietary_Notices')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
}

class Allergens_Dietary_Wc_Integration_Startup
{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init_integration'));
    }

    public function init_integration()
    {
        // Check if the WC_Integration class exists

        if (! class_exists('WC_Integration')) {
            // include_once ALLERGENS_DIETARY_DIRNAME . '/php/wc_integration.php';
            // add_filter('woocommerce_integrations', array($this, 'add_integration'));
            require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
            $level = Allergens_Dietary_Notice_Types::ERROR;
            $message = __('The WooCommerce Integration class was not found. Please make sure WooCommerce is installed correctly', 'allergens-dietary');
            Allergens_Dietary_Notices::getInstance()->error_notice($level, $message);
        }
    }
}
?>