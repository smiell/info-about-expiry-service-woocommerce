<?php
/**
 * Plugin Name: Send info about service expiry
 * Description: Send e-mail message to customer to inform about expired service that was bought in order.
 * Version: 1.0
 * Author: ZHNGRUPA
 */

// Dodaj przycisk do strony edycji zamówienia
function zhngrupa_send_message_button() {
    global $post;

    // Sprawdź, czy to strona edycji zamówienia i czy użytkownik jest administratorem
    if (
        is_admin()
        && isset($_GET['page'])
        && $_GET['page'] === 'wc-orders'
        && isset($_GET['action'])
        && $_GET['action'] === 'edit'
        && current_user_can('manage_options')
    ) {
        // Pobierz ID zamówienia
        $order_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

        // Sprawdź, czy e-mail został już wysłany
        $email_sent = get_post_meta($order_id, '_zhngrupa_expired_service_message_sent', true);

        // Jeśli e-mail został wysłany, zablokuj przycisk
        $button_disabled = $email_sent ? 'disabled' : '';

        // Zmiana treści przycisku w zależności od statusu
        $button_text = $email_sent ? 'Wiadomość o zakończeniu usługi została wysłana' : 'Wyślij wiadomość';

        // Dodaj przycisk
        echo '<button id="zhngrupa_send_message_expiry_service" class="button button-primary" ' . $button_disabled . '>' . $button_text . '</button>';
    }
}
add_action('woocommerce_order_item_add_action_buttons', 'zhngrupa_send_message_button', 20);

// Sprawdź uprawnienia do akcji AJAX
function zhngrupa_check_send_message_button_status() {
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $order = wc_get_order($post_id);

    // Sprawdź, czy użytkownik ma uprawnienia do edycji zamówienia
    if (current_user_can('manage_options') && $order) {
        $email_sent = get_post_meta($post_id, '_zhngrupa_expired_service_message_sent', true);

        // Przesyłamy informację o statusie do JavaScript
        wp_send_json_success(array('sent' => $email_sent));
    } else {
        wp_send_json_error('Error getting order or insufficient permissions.');
    }
}

add_action('wp_ajax_zhngrupa_check_send_message_button_status', 'zhngrupa_check_send_message_button_status');

function zhngrupa_send_message_button_script() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            var zhngrupaSendMessageExpiredService = $('#zhngrupa_send_message_expiry_service');
            var nonce = '<?php echo wp_create_nonce('zhngrupa_expired_service_nonce'); ?>';
            var order_id = $('#post_ID').val();

            // Sprawdź, czy wiadomość została już wysłana
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'zhngrupa_check_send_message_button_status',
                    nonce: nonce,
                    post_id: order_id
                },
                success: function(response) {
                    if (response.success && response.data.sent) {
                        // Wiadomość została wysłana wcześniej, zablokuj przycisk
                        zhngrupaSendMessageExpiredService.prop('disabled', true);
                        zhngrupaSendMessageExpiredService.text('Wiadomość o zakończeniu usługi została wysłana');
                    }
                }
            });

            // Obsługa kliknięcia przycisku
            zhngrupaSendMessageExpiredService.click(function() {
                // Sprawdź, czy przycisk jest zablokowany
                if (!zhngrupaSendMessageExpiredService.prop('disabled')) {
                    // Zablokuj przycisk po kliknięciu
                    zhngrupaSendMessageExpiredService.prop('disabled', true);

                    // Wyślij żądanie do serwera, aby obsłużyć kliknięcie przycisku
                    $.post(ajaxurl, {
                        action: 'zhngrupa_send_message_ExpiredService',
                        nonce: nonce,
                        post_id: order_id
                    }, function(response) {
                        console.log(response);
                        if (response.success) {
                            // Wiadomość została poprawnie wysłana, zablokuj przycisk i zmień jego treść
                            zhngrupaSendMessageExpiredService.prop('disabled', true);
                            zhngrupaSendMessageExpiredService.text('Wiadomość o zakończeniu usługi została wysłana');
                            alert("Wiadomość została poprawnie wysłana.");
                        } else {
                            // Błąd podczas wysyłania wiadomości
                            alert("Błąd: " + response.data);
                        }
                    });
                }
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'zhngrupa_send_message_button_script');

function zhngrupa_send_message_ExpiredService() {
    check_ajax_referer('zhngrupa_expired_service_nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $order = wc_get_order($post_id);

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
add_action('wp_ajax_zhngrupa_send_message_ExpiredService', 'zhngrupa_send_message_ExpiredService');
