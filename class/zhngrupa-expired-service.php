<?php

class Zhngrupa_Expired_Service {

    //Define poles
    private $order_id;

    public function __construct() {
        // Add actions and filters here
        add_action('woocommerce_order_item_add_action_buttons', array($this, 'zhngrupa_send_message_button'));
        add_action('wp_ajax_zhngrupa_check_send_message_button_status', array($this, 'zhngrupa_check_send_message_button_status'));
        add_action('wp_ajax_zhngrupa_send_message_ExpiredService', array($this, 'zhngrupa_send_message_ExpiredService'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Get your order ID
        $this->order_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    }

    // Example method to add custom thank you button
    public function zhngrupa_send_message_button() {
        // Verify that this is the edit order page and that the user is an administrator
        if (
            is_admin()
            && isset($_GET['page'])
            && $_GET['page'] === 'wc-orders'
            && isset($_GET['action'])
            && $_GET['action'] === 'edit'
            && current_user_can('manage_options')
        ) {
            // Sprawdź, czy e-mail został już wysłany
            $email_sent = get_post_meta($this->order_id, '_zhngrupa_expired_service_message_sent', true);

            // If the email has been sent, block the button
            $button_disabled = $email_sent ? 'disabled' : '';

            // Changing the button content depending on the status
            $button_text = $email_sent ? 'Wiadomość o zakończeniu usługi została wysłana' : 'Wyślij wiadomość';

            // Add button in order next to Re-calculate button
            echo '<button id="zhngrupa_send_message_expiry_service" class="button button-primary" ' . $button_disabled . '>' . $button_text . '</button>';
        }
    }

    // Method to check the button status
    public function zhngrupa_check_send_message_button_status() {
        $post_id = $this->order_id;
        $order = wc_get_order($post_id);

        // Check if the user has permission to edit the order
        if (current_user_can('manage_options') && $order) {
            $email_sent = get_post_meta($post_id, '_zhngrupa_expired_service_message_sent', true);

            // We send status information to JavaScript
            wp_send_json_success(array('sent' => $email_sent));
        } else {
            wp_send_json_error('Error getting order or insufficient permissions.');
        }
    }

    // Method for enqueuing scripts
    public function enqueue_scripts() {
        // Pobierz ID zamówienia
        $order_id = $this->order_id;

        // If the order ID is still 0, please complete the process
        if ($order_id === 0) {
            return;
        }

        // Add JavaScript directly in your PHP code
        wp_enqueue_script('zhngrupa-script', plugin_dir_url(__FILE__) . '../js/zhngrupa_send_message_button_script.js', array('jquery'), null, true);

        // Pass data to JavaScript
        wp_localize_script('zhngrupa-script', 'zhngrupaScriptParams', array(
            'nonce'    => wp_create_nonce('zhngrupa_expired_service_nonce'),
            'order_id' => $order_id,
        ));

        error_log( 'Order ID w enylowaniu js: ' . $order_id );
    }

    // Method to send the expired service message
    public function zhngrupa_send_message_ExpiredService() {
        check_ajax_referer('zhngrupa_expired_service_nonce', 'nonce');

        // Recive 'post_id' from Ajax request
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $order = wc_get_order($post_id);

        //error_log( 'Order ID in sending: ' . $order );

        if ($order) {
            $email_sent = get_post_meta($post_id, '_zhngrupa_expired_service_message_sent', true);

            $current_date = date('d-m-Y');

            if (!$email_sent) {
                $customer_email = $order->get_billing_email();
                $customer_name = $order->get_billing_first_name() ? $order->get_billing_first_name() : '';

                $subject = '🎵 Ważne: Zakończenie Usługi Spotify Premium - Odbierz Rabat na Nowy Zakup! 🎉';
                $message = "Cześć $customer_name,<br />Dziękujemy za skorzystanie ze naszych usług! <br />Niestety Twój plan został zakończony <b>$current_date</b> zgodnie z Twoim zamówieniem.<br />Już dziś możesz skorzystać z specjalnej zniżki dla ciebie!";

                $headers[] = 'Content-Type: text/html; charset=UTF-8';

                $email_sent_result = wp_mail($customer_email, $subject, $message, $headers);

                if ($email_sent_result) {
                    update_post_meta($post_id, '_zhngrupa_expired_service_message_sent', true);
                    update_post_meta($post_id, '_zhngrupa_expired_service_button_disabled', true);

                    wp_send_json_success('Email sent successfully.');
                } else {
                    wp_send_json_error('Email sending failed.');
                }
            } else {
                wp_send_json_error('Email already sent.');
            }
        } else {
            wp_send_json_error('Error getting order.');
        }
    }
}
