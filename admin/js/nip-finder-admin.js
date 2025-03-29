(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(document).ready(function () {
        $('#btn-nip-finder-check-status').on('click', function () {
            $.ajax({
                type: 'POST',
                url: nip_finder.ajax_url,
                data: {
                    action: 'nip_finder_check_status',
                    nonce: nip_finder.nonce
                },
                beforeSend: function () {
                    $('#btn-nip-finder-check-status').prop('disabled', true);
                },
                success: function (response) {
                    if (response.success && response.data.content.accessGus) {
                        alert("Połączenie z serwisem jest poprawne.");
                    } else {
                        alert("Błąd podczas komunikacji z serwisem.")
                    }
                    $('#btn-nip-finder-check-status').prop('disabled', false);

                },
                error: function () {
                    alert('Wystąpił błąd połączenia.');
                }
            });
        });

        $('#nip-finder-generate-api-key').on('click', function () {
            $.ajax({
                type: 'POST',
                url: nip_finder.ajax_url,
                data: {
                    action: 'nip_finder_register',
                    nonce: nip_finder.nonce,
                },
                success: function (response) {
                    window.location.href = response.data.url;
                }
            });
        });

        $('#btn-nip-finder-manage-licence').on('click', function () {
            window.location.href = $(this).attr('data-url');
        });

        $('#btn-nip-finder-register-subscription').on('click', function () {
            $.ajax({
                type: 'POST',
                url: nip_finder.ajax_url,
                data: {
                    action: 'nip_finder_register_subscription',
                    nonce: nip_finder.nonce
                },
                success: function (response) {
                    if (response.success) {
                        window.location.reload();
                    }
                }
            });
        });
    });

})(jQuery);
