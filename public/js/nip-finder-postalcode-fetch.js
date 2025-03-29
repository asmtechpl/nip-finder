jQuery(document).ready(function ($) {
    let typingTimer;
    const doneTypingInterval = 500;
    const $postcodeField = $('#billing_postcode');
    const countryCode = $('#billing_country').val();
    const $cityField = $('#billing_city');

    let citySuggestions = [];

    $postcodeField.on('input', function () {
        clearTimeout(typingTimer);
        const postcode = $(this).val();

        if (/^\d{2}-\d{3}$/.test(postcode) || /^\d{5}$/.test(postcode)) {
            typingTimer = setTimeout(function () {
                $.ajax({
                    url: nip_finder_postalcode_ajax.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'nip_finder_fetch_cities',
                        nonce: nip_finder_postalcode_ajax.nonce,
                        postcode: postcode,
                        countryCode: countryCode
                    },
                    success: function (response) {
                        if (response.success) {
                            citySuggestions = response.data;
                            showCitySuggestions();
                        } else {
                            console.error(response.data.message);
                        }
                    },
                    error: function () {
                        console.error('Błąd przy pobieraniu miejscowości.');
                    }
                });
            }, doneTypingInterval);
        }
    });

    function showCitySuggestions() {
        const $cityDropdown = $('<ul class="city-suggestions"></ul>');
        $cityDropdown.css({
            position: 'absolute',
            background: 'white',
            border: '1px solid #ccc',
            width: $cityField.outerWidth(),
            'z-index': 1000
        });

        citySuggestions.forEach(city => {
            const $item = $('<li></li>').text(city).css({
                padding: '5px',
                cursor: 'pointer'
            });
            $item.on('click', function () {
                $cityField.val($(this).text());
                $cityDropdown.remove();
            });
            $cityDropdown.append($item);
        });

        $('.city-suggestions').remove();
        $cityField.after($cityDropdown);
    }

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.city-suggestions, #billing_city').length) {
            $('.city-suggestions').remove();
        }
    });
});
