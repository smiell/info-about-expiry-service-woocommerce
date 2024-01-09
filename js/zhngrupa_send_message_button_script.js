jQuery(document).ready(function($) {
    var order_id = zhngrupaScriptParams.order_id || 0;
    var nonce = zhngrupaScriptParams.nonce || '';

    console.log('Order ID: ' + order_id);

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
                disableSendMessageButton();
            }
        }
    });

    // Obsługa kliknięcia przycisku
    $('#zhngrupa_send_message_expiry_service').click(function(event) {
        event.preventDefault(); // Zatrzymaj domyślną akcję przycisku

        // Sprawdź, czy przycisk jest zablokowany
        if (!$(this).prop('disabled')) {
            // Zablokuj przycisk po kliknięciu
            $(this).prop('disabled', true);

            // Wyślij żądanie do serwera, aby obsłużyć kliknięcie przycisku
            $.post(ajaxurl, {
                action: 'zhngrupa_send_message_ExpiredService',
                nonce: nonce,
                post_id: order_id
            }, function(response) {
                console.log(response);
                if (response.success) {
                    // Wiadomość została poprawnie wysłana, zablokuj przycisk i zmień jego treść
                    disableSendMessageButton();
                    alert("Wiadomość została poprawnie wysłana.");
                } else {
                    // Błąd podczas wysyłania wiadomości
                    alert("Błąd: " + response.data);
                }
            });
        }
    });

    function disableSendMessageButton() {
        if (typeof zhngrupaSendMessageExpiredService !== 'undefined') {
            // Sprawdź, czy przycisk istnieje przed dodaniem obsługi zdarzeń
            zhngrupaSendMessageExpiredService.prop('disabled', true);
            zhngrupaSendMessageExpiredService.text('Wiadomość o zakończeniu usługi została wysłana');
        }
    }
});
