<?php
/**
 * Plugin Name: Send info about service expiry
 * Description: Send e-mail message to customer to inform about expired service that was bought in order.
 * Version: 1.0
 * Author: ZHNGRUPA
 */

// Include the main class file
require_once plugin_dir_path(__FILE__) . 'class/zhngrupa-expired-service.php';

// Instantiate the main class
$zhngrupa_expired_service = new Zhngrupa_Expired_Service();