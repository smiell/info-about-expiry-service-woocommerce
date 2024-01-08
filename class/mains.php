<?php
/**
 * Class NotiferAboutExpiredServiceMain
 *
 * Configure the plugin global settings
 */

//  class NotiferAboutExpiredServiceMain {
//     function __construct() {
//         add_action('woocommerce_admin_order_data_after_billing_address', 'add_custom_thank_you_button');
//     }

//     function add_custom_thank_you_button() {
//         global $post;
    
//         // Sprawdź, czy to strona edycji zamówienia i czy użytkownik jest administratorem
//         if (is_admin() && get_post_type($post) == 'shop_order' && current_user_can('manage_options')) {
//             echo '<button id="custom_thank_you_button" class="button">Wyślij wiadomość</button>';
//         }
//     }

//  }