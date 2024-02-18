<?php
/**
 * Plugin Name: Expired service manager
 * Description: Send e-mail message to customer to inform about expired service that was bought in order.
 * Version: 1.0.1
 * Author: ZHNGRUPA
 */

// Include the main classes files
require_once plugin_dir_path(__FILE__) . 'class/zhngrupa-expired-service-main.php';
require_once plugin_dir_path(__FILE__) . 'class/zhngrupa-expired-service-options.php';
require_once plugin_dir_path(__FILE__) . 'class/zhngrupa-expired-service-order-form.php';


// Instantiate the main class
$zhngrupa_expired_service = new Zhngrupa_Expired_Service();
$zhngrupa_expired_service_options = new ZhnGrupa_Expired_Service_Options();
$zhngrupa_expired_service_order_form = new Zhngrupa_Membership_MetaBox();