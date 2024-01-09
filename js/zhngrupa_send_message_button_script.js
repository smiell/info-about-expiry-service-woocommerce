jQuery(document).ready(function($) {
    var order_id = zhngrupaScriptParams.order_id || 0;
    var nonce = zhngrupaScriptParams.nonce || '';

    //console.log('Order ID: ' + order_id);

    // Check if the message has already been sent
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
                // The message has been sent previously, block the button
                disableSendMessageButton();
            }
        }
    });

    // Button click handle
    $('#zhngrupa_send_message_expiry_service').click(function(event) {
        event.preventDefault(); // Stop the button's default action

        // Check if the button is locked
        if (!$(this).prop('disabled')) {
            // Lock the button when clicked
            $(this).prop('disabled', true);

            // Send a request to the server to handle the button click
            $.post(ajaxurl, {
                action: 'zhngrupa_send_message_ExpiredService',
                nonce: nonce,
                post_id: order_id
            }, function(response) {
                console.log(response);
                if (response.success) {
                    // The message has been successfully sent, lock the button and change its content
                    disableSendMessageButton();
                    alert("Wiadomość została poprawnie wysłana.");
                } else {
                    // Error sending message
                    alert("Błąd: " + response.data);
                }
            });
        }
    });

    function disableSendMessageButton() {
        if (typeof zhngrupaSendMessageExpiredService !== 'undefined') {
            // Check if the button exists before adding event handlers
            zhngrupaSendMessageExpiredService.prop('disabled', true);
            zhngrupaSendMessageExpiredService.text('Wiadomość o zakończeniu usługi została wysłana');
        }
    }
});
